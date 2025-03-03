<?php

/*
Plugin Name: Duitku Payment Gateway
Description: Duitku Payment Gateway Version: 1.7
Author: PRIYANA ANGGIYAWAN
Version: 1.7
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

 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', 'woocommerce_duitku_init', 0);

function woocommerce_duitku_init() {
	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}

	//include global configuration file

	include_once dirname(__FILE__) . '/includes/admin/class-wc-duitku-settings.php';
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
			
			public function __construct() {

				//plugin id
				$this->id = $this->sub_id;

				//payment method will be set in each child class (e.g. Duitku Wallet = WW or Mandiri = MY)
				$this->payment_method = '';

				//true only in case of direct payment method, false in our case
				$this->has_fields = false;

				//set duitku global configuration
				//redirect URL
				$this->redirect_url = WC()->api_request_url('WC_Gateway_' . $this->id);

				//Load settings
				$this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				$this->title = $this->settings['title'];
				$this->enabled = $this->settings['enabled'];
				$this->description = $this->settings['description'];

				// set  variables from global configuration
				$this->apikey = get_option('duitku_api_key');
				$this->merchantCode = get_option('duitku_merchant_code');
				self::$log_enabled = get_option('duitku_debug');

				// remove trailing slah and add one for our need.
				$this->endpoint = rtrim(get_option('duitku_endpoint'), '/');

				self::$log_enabled = get_option('duitku_debug') == 'yes' ? true : false;

				// Actions
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));

				// Payment listener/API hook
				add_action('woocommerce_api_wc_gateway_' . $this->id, array($this, 'check_duitku_response'));		
				
			}
			
			
	  
			/**
			 * set field for each payment gateway
			 * @return void
			 */
			function init_form_fields() {

				$this->form_fields = array(
					'enabled' => array(
						'title' => __('Enable/Disable', 'woothemes'),
						'label' => __('Enable Duitku', 'woothemes'),
						'type' => 'checkbox', 'description' => '',
						'default' => 'no',
					),
					'title' => array(
						'title' => __('Title', 'woothemes'), 'type' =>
						'text', 'description' => __('', 'woothemes'),
						'default' => __('Pembayaran Duitku', 'woothemes'),
					),
					'description' => array(
						'title' => __('Description', 'woothemes'),
						'type' => 'textarea', 'description' => __('',
							'woothemes'), 'default' => 'Sistem pembayaran menggunakan Duitku.',
					),
				);
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

				//Total Amount Exclude Fee
				$totalAmount = intval($order->subtotal); //Exclude
				
				$this->log('Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->redirect_url);

				//endpoint for inquiry
				$url = $this->endpoint . '/api/merchant/inquiry';

				//generate Signature
				$signature = md5($this->merchantCode . $order_id . $totalAmount . $this->apikey);

				//merchant user info taken from billing name
				$current_user = $order->billing_first_name . " " . $order->billing_last_name;

				//record current user who made a transaction for merchant info
				/*
				if (is_user_logged_in()) {
					$current_user = wp_get_current_user()->user_login;
				} else {
					$current_user = "GUEST";
				}
				*/				

				// Prepare Parameters
				$params = array(
					'merchantCode' => $this->merchantCode, // API Key Merchant /
					'paymentAmount' => $totalAmount,
					'paymentMethod' => $this->payment_method,
					'merchantOrderId' => $order_id,
					'productDetails' => get_bloginfo() . ' Order : #' . $order_id,
					'additionalParam' => '',
					'merchantUserInfo' => $current_user,
					'email' => $order->billing_email,
					'phoneNumber' => $order->billing_phone,
					'signature' => $signature,
					//'returnUrl' => esc_url_raw( add_query_arg( 'utm_nooverride', '1', $this->get_return_url( $order ) ) ),
					'returnUrl' => $this->redirect_url . '?status=notify',
					'callbackUrl' => $this->redirect_url,
				);

				$headers = array('Content-Type' => 'application/json');

				// show request for inquiry
				$this->log("create a request for inquiry");
				$this->log(var_export($params, true));

				// Send this payload to Authorize.net for processing
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

				// Test the code to know if the transaction went through or not. 1 or 4
				// means the transaction was a success
				if ($response_code == '200') {

					//save reference Code from duitku
					$this->log('Inquiry Success for order Id ' . $order->get_order_number() . ' with reference number ' . $resp->reference);
					if($this->payment_method == 'BT')
					{
						WC()->cart->empty_cart();
					}

					// store Url as $Order metadata
					  $order->update_meta_data('_duitku_payment_reference',$resp->reference);
					  $order->save();
					  
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

				$this->log("masuk ke check duitku response.");

				if (empty($_REQUEST['resultCode']) || empty($_REQUEST['merchantOrderId']) || empty($_REQUEST['reference'])) {
					throw new Exception(__('wrong query string please contact admin.',
						'duitku'));
					return;
				}

				if (!empty($_REQUEST['status']) && $_REQUEST['status'] == 'notify') {
					$this->notify_response();
					exit;
				}

				$order_id = wc_clean(stripslashes($_REQUEST['merchantOrderId']));
				$status = wc_clean(stripslashes($_REQUEST['resultCode']));
				$reference = wc_clean(stripslashes($_REQUEST['reference']));

				$order = new WC_Order($order_id);

				if ($status == '00' && $this->validate_transaction($order_id, $reference)) {
					$order->payment_complete();
					$order->add_order_note(__('Pembayaran telah dilakukan melalui duitku dengan id ' . $order_id . ' Dan No Reference ' . $reference, 'woocommerce'));
					$this->log("Pembayaran dengan order ID " . $order_id . " telah berhasil.");
				}else {
					$order->add_order_note('Pembayaran dengan duitku tidak berhasil');
					$this->log("Pembayaran dengan order ID " . $order_id . " gagal.");
					//$order->update_status( 'on-hold', __( 'pembayaran gagal mohon contact administrator ', 'woocommerce'));
					//$order->reduce_order_stock();
					//WC()->cart->empty_cart();
				}

				exit;
			}

			function notify_response() {
				
				// log request from server
				$this->log(var_export($_REQUEST, true));

				if (empty($_REQUEST['resultCode']) || empty($_REQUEST['merchantOrderId'])) {
					throw new Exception(__('wrong query string please contact admin.',
						'duitku'));
					return false;
				}

				$order_id = wc_clean(stripslashes($_REQUEST['merchantOrderId']));
				$order = new WC_Order($order_id);

				if ($_REQUEST['resultCode'] == '00') {
					wc_add_notice('pembayaran dengan duitku telah berhasil.');
            				return wp_redirect($order->get_checkout_order_received_url());
				}else if ($_REQUEST['resultCode'] == '01') {
					wc_add_notice('pembayaran dengan duitku sedang diproses.');
							
            				//return wp_redirect($order->get_checkout_order_received_url());
							// WC()->cart->empty_cart();
							wp_redirect( get_permalink( woocommerce_get_page_id( 'shop' ) ) );
				} else {
					wc_add_notice('pembayaran dengan duitku gagal.', 'error');
            				return wp_redirect($order->get_checkout_payment_url(false));
				}
				//return wp_redirect($this->get_return_url(new WC_Order($order_id)));
			}

			/**
			 * @param $order_id
			 * @param $reference
			 */
			protected function validate_transaction($order_id, $reference) {

				$order = new WC_Order($order_id);

				//endpoint for transactionStatus
				$url = $this->endpoint . '/api/merchant/transactionStatus';

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
		$methods[] = 'WC_Gateway_Duitku_CC_SO';
		$methods[] = 'WC_Gateway_Duitku_BCA';
		$methods[] = 'WC_Gateway_Duitku_VA_Permata';
		$methods[] = 'WC_Gateway_Duitku_VA_ATM_Bersama';
		$methods[] = 'WC_Gateway_Duitku_VA_BNI';
		$methods[] = 'WC_Gateway_Duitku_VA_MANDIRI';
		$methods[] = 'WC_Gateway_Duitku_VA_CIMB_Niaga';
		$methods[] = 'WC_Gateway_Duitku_VA_Maybank';
		$methods[] = 'WC_Gateway_Duitku_VA_Ritel';
		return $methods;
	}

	add_filter('woocommerce_payment_gateways', 'add_duitku_gateway');

	foreach (glob(dirname(__FILE__) . '/includes/gateways/*.php') as $filename) {
		include_once $filename;
	}

}
