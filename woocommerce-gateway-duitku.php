<?php

/*
Plugin Name: Duitku Payment Gateway
Description: Duitku Payment Gateway Version: 2.11.6
Author: Duitku
Author URI: https://www.duitku.com/
Version: 2.11.6
URI: http://www.duitku.com

improvement 1.3 to 1.4:
- add ATM Bersama, BNI, CIMB Niaga and Maybank Virtual Account support.

improvement 1.4 to 1.5:
- add OVO Payment.

improvement 1.5 to 1.6:
- add Mandiri Virtual Account.

improvement 1.6 to 1.7:
- add Credit Card Fasilitator.
- add fitur fee

improvement 1.7 to 2.0:
- upgrade API v2
- add ShopeePay
- add Indodana

improvement 2.0 to 2.1:
- Improve Expired Period

improvement 2.1 to 2.2:
- Add ShopeePay Applink & LinkAja Applink
- Add observer & mutation for detect device

improvement 2.2 to 2.3:
- Add BCA Virtual Account

improvement 2.3 to 2.4
- Change Logo Indodana

improvement 2.4 to 2.5
- Change Mandiri Virtual Account become Deprecated
- Remove Credit Card SO
- Add Mandiri Direct Virtual Account
- Add Credit Card Facilitator

improvement 2.5 to 2.6
- Add DANA Payment.
- Add LinkAja Payment.
- Add Sanitized & Validation Email and Phone Number feature

improvement 2.6 to 2.7
- Add LinkAja QRIS.

improvement 2.7 to 2.8
- Add Indomaret.

improvement 2.8 to 2.9
- Add Pos Indonesia.

improvement 2.9 to 2.10
- Add Bank Neo Commerce.
- Remove Deprecated Mandiri

improvement 2.10 to 2.11
- Add BRI Virtual Account.
- Add QRIS by Nobu.

improvement 2.11 to 2.11.1
- Improvement for input Phone Number Parameter

improvement 2.11.1 to 2.11.2
- Change Logo Bank Neo Commerce

improvement 2.11.2 to 2.11.3
- Add new Payment ATOME

removing feature 2.11.3 to 2.11.4
- Remove Sampoerna VA 

improvement 2.11.4 to 2.11.5
- Add new Payment Gudang Voucher QRIS

improvement 2.11.5 to 2.11.6
- Add new Payment Jenius Pay
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', 'woocommerce_duitku_init', 0);

add_action('wp_enqueue_scripts','duitku_dom_manipulate_init');

//function to check if the customer open from mobile or desktop
function duitku_dom_manipulate_init() {
	wp_enqueue_script( 'duitku-dom-manipulate-js', plugins_url('/includes/assets/js/duitku_dom_manipulate.js', __FILE__ ));
}

function woocommerce_duitku_init() {
	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}

	//include global configuration file

	include_once dirname(__FILE__) . '/includes/admin/class-wc-duitku-settings.php';
	include_once dirname(__FILE__) . '/includes/duitku/wc-gateway-duitku-sanitized.php';
	include_once dirname(__FILE__) . '/includes/duitku/wc-gateway-duitku-validation.php';
	if (!class_exists('Duitku_Payment_Gateway')) {

		/**
		 * duitku abstract class
		 * parent class for other payment gateways (e.g. CIMB, Mandiri)
		 */
		Abstract class Duitku_Payment_Gateway extends WC_Payment_Gateway {

			/** @var bool whether or not logging is enabled */
			public static $log_enabled = false;

			/** @var WC_Logger Logger instance */
			public static $log = false;

			/** you can control it with Sanitized (default: true) */
			public static $sanitized = true;
			public static $validation = true;


			public function __construct() {

				//plugin id
				$this->id = $this->sub_id;

				//payment method will be set in each child class (e.g. Duitku Wallet = WW or Mandiri = M2)
				$this->payment_method = '';

				//true only in case of direct payment method, false in our case
				$this->has_fields = false;

				//set duitku global configuration
				//redirect URL
				$this->redirect_url = WC()->api_request_url('WC_Gateway_' . $this->id);

				//Load settings
				// $this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				$this->title = (isset($this->settings['title'])) ? $this->settings['title'] : "Pembayaran Duitku";
				$this->enabled = (isset($this->settings['enabled'])) ? $this->settings['enabled'] : false;
				$this->description = (isset($this->settings['description'])) ? $this->settings['description'] : "";
				$this->tipe = (isset($this->settings['tipe'])) ? $this->settings['tipe'] : null;

				// set  variables from global configuration
				$this->apikey = get_option('duitku_api_key');
				$this->merchantCode = get_option('duitku_merchant_code');
				$this->prefix = get_option('duitku_prefix');
				$this->expiryPeriod = (isset($this->settings['duitku_expiry_period'])) ? $this->settings['duitku_expiry_period'] : 1440;
				$this->credCode = get_option('duitku_credential_code');
				self::$log_enabled = get_option('duitku_debug');

				// remove trailing slah and add one for our need.
				$this->endpoint = rtrim(get_option('duitku_endpoint'), '/');

				self::$log_enabled = get_option('duitku_debug') == 'yes' ? true : false;

				// Actions
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));

				// Payment listener/API hook
				add_action('woocommerce_api_wc_gateway_' . $this->id, array($this, 'check_duitku_response'));

			}

			public function admin_options() {
				echo '<table class="form-table">';
				$this->generate_settings_html();
				echo '</table>';
			}

			/**
			 * @param $order_id
			 * @return null
			 */
			function process_payment($order_id) {
				$order = new WC_Order($order_id);
				$this->log("Cek tanggal" . $order->order_date);


				//Total Amount Exclude Fee
				$totalAmount = intval($order->order_total); //Exclude

				$this->log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->redirect_url);

				//endpoint for inquiry
				$url = $this->endpoint . '/api/merchant/v2/inquiry';

				//merchant user info taken from billing name
				$current_user = $order->billing_first_name . " " . $order->billing_last_name;				

				$item_details = [];

				foreach ($order->get_items() as $item_key => $item ) {
				  $item_name    = $item->get_name();
				  $quantity     = $item->get_quantity();
				  $product_price  = $item->get_subtotal();				  

				  $item_details[] = array(
					'name' => $item_name,
					'price' => round($product_price),
					'quantity' => $quantity
				  );
				}
								

				// Shipping fee as item_details
				if( $order->get_total_shipping() > 0 ) {
				  $item_details[] = array(
					'name' => 'Shipping Fee',
					'price' => round($order->get_total_shipping()),
					'quantity' => 1
				  );
				}

				// Tax as item_details
				if( $order->get_total_tax() > 0 ) {
				  $item_details[] = array(
					'name' => 'Tax',
					'price' => round($order->get_total_tax()),
					'quantity' => 1
				  );
				}

				// Discount as item_details
				if ( $order->get_total_discount() > 0) {
				  $item_details[] = array(
					'name' => 'Total Discount',
					'price' => round($order->get_total_discount())  * -1,
					'quantity' => 1
				  );
				}

				// Fees as item_details
				if ( sizeof( $order->get_fees() ) > 0 ) {
				  $fees = $order->get_fees();
				  $i = 0;
				  foreach( $fees as $item ) {

					if ( $item['name'] == __('Surcharge', 'wc-duitku') ) {
					  $totalAmount = $totalAmount - round($item['line_total']);
					  continue;
					}

					$item_details[] = array(
					  'name' => $item['name'],
					  'price' => round($item['line_total']),
					  'quantity' => 1
					);
					$i++;
				  }
				}

				$billing_address = array(
				  'firstName' => $order->billing_first_name,
				  'lastName' => $order->billing_last_name,
				  'address' => $order->billing_address_1 . " " . $order->billing_address_2,
				  'city' => $order->billing_city,
				  'postalCode' => $order->billing_postcode,
				  'phone' => $order->billing_phone,
				  'countryCode' => $order->billing_country
				);

				$customerDetails = array(
					'firstName' => $order->billing_first_name,
					'lastName' => $order->billing_last_name,
					'email' => $order->billing_email,
					'phoneNumber' => $order->billing_phone,
					'billingAddress' => $billing_address,
					'shippingAddress' => $billing_address
				);

				//generate Signature
	
				$signature = md5($this->merchantCode . $this->prefix . $order_id . $totalAmount . $this->apikey);
				
				if ( isset($this->tipe) ) {
					$payment_method = $this->tipe;
				} else {
					$payment_method = $this->payment_method;
				}

				// Prepare Parameters
				$params = array(
					'merchantCode' => $this->merchantCode, // API Key Merchant /
					'paymentAmount' => $totalAmount,
					'paymentMethod' => $payment_method,
					'merchantOrderId' => $this->prefix . $order_id,
					'productDetails' => get_bloginfo() . ' Order : #' . $order_id,
					'additionalParam' => '',
					'merchantUserInfo' => $current_user,
					'customerVaName' => $current_user,
					'email' => $order->billing_email,
					'phoneNumber' => $order->billing_phone,
					'signature' => $signature,
					'expiryPeriod' => $this->expiryPeriod,
					'returnUrl' => esc_url_raw($this->redirect_url) . '?status=notify',
					'callbackUrl' => esc_url_raw($this->redirect_url),
					'customerDetail' => $customerDetails,
					'itemDetails' => $item_details
				);
				
				if ($this->payment_method == "MG") {
					$url = $this->endpoint . '/api/merchant/creditcard/inquiry';
					$params['credCode'] = $this->credCode;
				}

				$headers = array('Content-Type' => 'application/json');
				
				if (self::$sanitized) {
					WC_Gateway_Duitku_Sanitized::duitkuRequest($params);
				}
				
				if (self::$validation) {
				  WC_Gateway_Duitku_Validation::duitkuRequest($params);
				}
				
				//check cache payment url
				// session_start();
				// if(isset($_SESSION[$params['merchantOrderId']])){
				// 	$paymentMethod = $_SESSION[$params['merchantOrderId']]['paymentMethod'];
				// }
				// if($paymentMethod == $payment_method){
				// 	$paymentUrl = $_SESSION[$params['merchantOrderId']]['paymentUrl'];
				// 	return array(
				// 		'result' => 'success', 'redirect' => $paymentUrl,
				// 	);
				// }

				// show request for inquiry
				$this->log("create a request for inquiry");
				$this->log(json_encode($params, true));

				// Send this payload to Duitku.com for processing
				$response = wp_remote_post($url, array(
					'method' => 'POST', 'body' => json_encode($params), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,
				));

				// Retrieve the body's resopnse if no errors found
				$response_body = wp_remote_retrieve_body($response);
				$response_code = wp_remote_retrieve_response_code($response);

				if (is_wp_error($response)) {
					throw new Exception(__('We are currently experiencing problems
								trying to connect to this payment gateway. Sorry for the
								inconvenience.', 'duitku'));
				}

				if (empty($response_body)) {
					throw new Exception(__('Duitku\'s Response was empty.',
						'duitku'));
				}

				// Parse the response into something we can read
				$resp = json_decode($response_body);

				//log response from server
				$this->log('response body: ' . $response_body);
				$this->log('response code: ' . $response_code);
				$this->log($url);
				
				// means the transaction was a success
				if ($response_code == '200') {

					//save reference Code from duitku
					$this->log('Inquiry Success for order Id ' . $order->get_order_number() . ' with reference number ' . $resp->reference);					

					// store Url as $Order metadata
					  $order->update_meta_data('_duitku_pg_reference',$resp->reference);
					  $order->save();

					// save payment url to cache
					// session_start();
					// $_SESSION[$params['merchantOrderId']] = array("paymentUrl" => $resp->paymentUrl, "paymentMethod" => $payment_method);
					// $this->log("Cek masuk cache " . $_SESSION[$params['merchantOrderId']]);

					// Redirect to thank you page
					return array(
						'result' => 'success', 'redirect' => $resp->paymentUrl,
					);
				} else {
					$this->log('Inquiry failed for order Id ' . $order->get_order_number());
					// Transaction was not succesful Add notice to the cart

					if ($response_code = "400") {
						wc_add_notice($resp->Message, 'error');
						// Add note to the order for your reference
						$order->add_order_note( 'Error:' .  $resp->Message);
					}
					else
					{
						wc_add_notice("error processing payment", 'error');
						// Add note to the order for your reference
						$order->add_order_note( 'Error: error processing payment.');
					}
					return;
				}
			}

			/**
			 * @return null
			 */
			function check_duitku_response() {

				$params = [];
				$params['resultCode'] = isset($_REQUEST['resultCode'])? sanitize_text_field($_REQUEST['resultCode']): null;
				$params['merchantOrderId'] = isset($_REQUEST['merchantOrderId'])? sanitize_text_field($_REQUEST['merchantOrderId']): null;
				$params['reference'] = isset($_REQUEST['reference'])? sanitize_text_field($_REQUEST['reference']): null;
				$params['status'] = isset($_REQUEST['status'])? sanitize_text_field($_REQUEST['status']): null;

				$params['merchantOrderId'] = str_replace($this->prefix,'',$params['merchantOrderId']);
				if (empty($params['resultCode']) || empty($params['merchantOrderId']) || empty($params['reference'])) {
					throw new Exception(__('wrong query string please contact admin.',
						'duitku'));
					return;
				}

				//if notification only redirect to notification page
				if (!empty($params['status']) && $params['status'] == 'notify') {
					$this->notify_response($params);
					exit;
				}
				
				//if callback request proceed to payment

				$order_id = wc_clean(stripslashes($params['merchantOrderId']));
				$status = wc_clean(stripslashes($params['resultCode']));
				$reference = wc_clean(stripslashes($params['reference']));

				$order = new WC_Order($order_id);

				if ($status == '00' && $this->validate_transaction($this->prefix . $order_id, $reference)) {
					$order->payment_complete();
					$order->add_order_note(__('Pembayaran telah dilakukan melalui duitku dengan id ' . $this->prefix . $order_id . ' Dan No Reference ' . $reference, 'woocommerce'));
					$this->log("Pembayaran dengan order ID " . $order_id . " telah berhasil.");
				}else {
					$order->update_status('failed');
					$order->add_order_note('Pembayaran dengan duitku tidak berhasil');
					$this->log("Pembayaran dengan order ID " . $order_id . " gagal.");					
				}

				exit;
			}

			function notify_response($params) {				
			
				// log request from Duitku server
				$this->log(var_export($params, true));
				
				if (empty($params['resultCode']) || empty($params['merchantOrderId'])) {
					throw new Exception(__('wrong query string please contact admin.', 'duitku'));
						return false;
				}	

				$order_id = wc_clean(stripslashes($params['merchantOrderId']));
				$order_id = str_replace($this->prefix,'',$order_id);
				$order = new WC_Order($order_id);
											
				if ($params['resultCode'] == '00') {
						WC()->cart->empty_cart();
					 	wc_add_notice('pembayaran dengan duitku telah berhasil.');
            			return wp_redirect($order->get_checkout_order_received_url());
				}else if ($params['resultCode'] == '01') {
						WC()->cart->empty_cart();														
						wc_add_notice('pembayaran dengan duitku sedang diproses.');
						return wp_redirect($order->get_view_order_url());
				} else {
						WC()->cart->empty_cart();
						wc_add_notice('pembayaran dengan duitku gagal.', 'error');
            			return wp_redirect($order->get_cancel_order_url());
            			throw new Exception(_('Pembayaran Gagal.', 'duitku'));
            			
				}
			}

			/**
			 * @param $order_id
			 * @param $reference
			 */
			protected function validate_transaction($order_id, $reference) {

				$order = new WC_Order($order_id);

				//endpoint for transactionStatus
				$url = esc_url_raw($this->endpoint) . '/api/merchant/transactionStatus';

				//generate Signature
				$signature = md5($this->merchantCode . $order_id . $this->apikey);

				// Prepare Parameters
				$params = array(
					'merchantCode' => $this->merchantCode, // API Key Merchant /
					'merchantOrderId' => $order_id,
					'signature' => $signature,
					'reference' => $reference,
				);

				$headers = array('Content-Type' => 'application/json');

				// show request for inquiry
				$this->log("validate transaction:");
				$this->log(var_export($params, true));

				$response = wp_remote_post($url, array(
					'method' => 'POST', 'body' => json_encode($params), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,
				));

				// Retrieve the body's resopnse if no errors found
				$response_body = wp_remote_retrieve_body($response);
				$response_code = wp_remote_retrieve_response_code($response);

				$this->log("response Body: " . $response_body);
				$this->log("response Code: " . $response_code);

				if ($response_code == '200') {
					// Parse the response into something we can read
					$resp = json_decode($response_body);
					if ($resp->statusCode == '00') {
						return true;
					}

				} else {
					$this->log($response_body);
				}

				return false;
			}

			/**
			 * function to generate log for debugging
			 * to activate loggin please set debug to true in admin configuration
			 * @param type $message
			 * @return type
			 */
			public static function log($message) {
				if (self::$log_enabled) {
					if (empty(self::$log)) {
						self::$log = new WC_Logger();
					}
					self::$log->add('duitku', $message);
				}
			}

		}

	}

	/**
	 *
	 * @param type $methods
	 * set duitku gateway that uses Duitku Payment Gateway
	 * @return type
	 */
	function add_duitku_gateway($methods) {
		$methods[] = 'WC_Gateway_Duitku_OVO';
		$methods[] = 'WC_Gateway_Duitku_CC';
		$methods[] = 'WC_Gateway_Duitku_CC_MIGS';
		$methods[] = 'WC_Gateway_Duitku_BCA';
		$methods[] = 'WC_Gateway_Duitku_VA_Permata';
		$methods[] = 'WC_Gateway_Duitku_VA_ATM_Bersama';
		$methods[] = 'WC_Gateway_Duitku_VA_BNI';
		$methods[] = 'WC_Gateway_Duitku_VA_BCA';
		$methods[] = 'WC_Gateway_Duitku_VA_MANDIRI_H2H';
		$methods[] = 'WC_Gateway_Duitku_VA_CIMB_Niaga';
		$methods[] = 'WC_Gateway_Duitku_VA_Maybank';
		$methods[] = 'WC_Gateway_Duitku_VA_Ritel';
		$methods[] = 'WC_Gateway_Duitku_SHOPEE';
		$methods[] = 'WC_Gateway_Duitku_INDODANA';
		$methods[] = 'WC_Gateway_Duitku_SHOPEEPAY_APPLINK';
		$methods[] = 'WC_Gateway_Duitku_LINKAJA_APPLINK';
		$methods[] = 'WC_Gateway_Duitku_DANA';
		$methods[] = 'WC_Gateway_Duitku_VA_ARTHA';
		$methods[] = 'WC_Gateway_Duitku_LINKAJA_QRIS';
		$methods[] = 'WC_Gateway_Duitku_INDOMARET';
		$methods[] = 'WC_Gateway_Duitku_POS';
		$methods[] = 'WC_Gateway_Duitku_BRIVA';
		$methods[] = 'WC_Gateway_Duitku_BNC';
		$methods[] = 'WC_Gateway_Duitku_NOBU_Qris';
		$methods[] = 'WC_Gateway_Duitku_ATOME';
		$methods[] = 'WC_Gateway_Duitku_JENIUS_PAY';
		$methods[] = 'WC_Gateway_Duitku_GUDANG_VOUCHER_QRIS';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'add_duitku_gateway');

	foreach (glob(dirname(__FILE__) . '/includes/gateways/*.php') as $filename) {
		include_once $filename;
	}

}
