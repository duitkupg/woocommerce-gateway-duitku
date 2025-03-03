<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Duitku_Payment_gateway class
 *
 * @author   Duitku
 * @extends  WC_Payment_Gateway
 * @version  2.11.11
 */

class Duitku_Payment_gateway extends WC_Payment_Gateway {

	/** @var bool whether or not logging is enabled */
			public static $log_enabled = false;

			/** @var WC_Logger Logger instance */
			public static $log = false;

			/** you can control it with Sanitized (default: true) */
			public static $sanitized = true;
			public static $validation = true;


			public function __construct() {
				//payment gateway logo
				//plugin id
				$this->id = $this->sub_id;

				//payment method will be set in each child class (e.g. Duitku Wallet = WW or Mandiri = M2)
				$this->payment_method = '';

				//true only in case of direct payment method, false in our case
				$this->has_fields = true;

				//set duitku global configuration
				//redirect URL
				$this->redirect_url = WC()->api_request_url('WC_Gateway_' . $this->id);

				//Load settings
				//$this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				$this->title = (isset($this->settings['title'])) ? $this->settings['title'] : "Pembayaran Duitku";
				$this->enabled = (isset($this->settings['enabled'])) ? $this->settings['enabled'] : false;
				$this->description = (isset($this->settings['description'])) ? $this->settings['description'] : "";
				$this->tipe = (isset($this->settings['tipe'])) ? $this->settings['tipe'] : null;

				// set  variables from global configuration
				$this->apiKey = get_option('duitku_api_key');
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

			
			public function process_fees($order, $item_details) {
				
				$item_details = $item_details;
				$totalAmount = intval($order->order_total); //includes fee

				if (sizeof($order->get_fees()) > 0) {
					$fees = $order->get_fees();
					foreach ($fees as $item) {

						// exclude surcharge
						if ($item['name'] == __('Surcharge', 'wc-duitku')) {
							$totalAmount -= round($item['line_total']);
							continue;
						}

						$item_details[] = array(
							'name' => $item['name'],
							'price' => round($item['line_total']),
							'quantity' => 1
						);

					}
				}

				return array(
					'item_details' => $item_details,
					'total_amount' => $totalAmount
				);
			}
			
			/**
			 * @param $order_id
			 * @return null
			 */
			function process_payment($order_id) {
				//global $woocommerce;
				$order = new WC_Order($order_id);

				//Total Amount Include Fee
				$totalAmount = intval($order->order_total); //Include

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
				
				$fees_data = $this->process_fees($order, $item_details);
				$item_details = $fees_data['item_details'];
				$totalAmount = $fees_data['total_amount'];
				

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
				//$this->log("api key process payment" . $this->apiKey);

				//generate Signature
				$signature = md5($this->merchantCode . $this->prefix . $order_id . $totalAmount . $this->apiKey);
				
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
				$this->log('response body: ' . $response_body . 'for order id: ' . $order_id);
				$this->log('response HTTP code: ' . $response_code);
				$this->log($url);
				
				// means the transaction was a success
				if ($response_code == '200') {

					//save reference Code from duitku
					$this->log('Inquiry Success for order Id ' . $order->get_order_number() . ' with reference number ' . $resp->reference);					

					// store Url as $Order metadata
					$order->update_meta_data('_duitku_pg_reference', $resp->reference);
					  //$order->update_meta_data($order_id, '_duitku_pg_reference',$resp->reference);
					  //$order->save();

					// Redirect to thank you page
					return array(
						'result' => 'success', 'redirect' => $resp->paymentUrl,
					);
				} else {
					$this->log('Inquiry failed for order Id ' . $order->get_order_number());
					// Transaction was not succesful Add notice to the cart

					if ($response_code = "400") {
						$order->add_order_note( 'Error:' .  $resp->Message);
						throw new Exception($resp->Message);
					}
					else
					{
						$order->add_order_note( 'Error: error processing payment.');
						throw new Exception("Error processing payment."); 
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
				$result_Code = wc_clean(stripslashes($params['resultCode']));
				$reference = wc_clean(stripslashes($params['reference']));
				
				$params['signature']= isset($_REQUEST['signature'])? sanitize_text_field($_REQUEST['signature']): null;
				$reqSignature = wc_clean(stripslashes($params['signature']));

				$order = new WC_Order($order_id);
				
				$item_details = [];
				
				$fees_data = $this->process_fees($order, $item_details);
				$item_details = $fees_data['item_details'];
				$amount = $fees_data['total_amount'];
				
				
				//signature validation
				$signature = md5($this->merchantCode . $amount . $this->prefix . $order_id . $this->apiKey);
				if($reqSignature == $signature){
					$this->log("Signature valid");
				}else{
					$this->log("Invalid signature!");
					exit;
				}

				if($result_Code == "00"){
					$respon = json_decode($this->validate_transaction($this->prefix . $order_id));
					if($respon->statusCode == "00"){
						$order->payment_complete();
						$order->add_order_note(__("Pembayaran telah dilakukan melalui Duitku dengan ID " . $this->prefix . $order_id . ' dan No Reference ' . $reference, 'woocommerce'));
						$this->log("Callback diterima. Pembayaran dengan order ID " . $order_id . " telah berhasil.");
					}else if($respon->statusCode == "01"){
						$order->add_order_note( "Pembayaran menggunakan Duitku dengan order ID " . $this->prefix . $order_id . "reference " . $reference . " tertunda.");
						$this->log("Callback diterima. Pembayaran dengan order ID " . $order_id . " tertunda.");
					}else{
						$order->add_order_note("Callback diterima dengan result code " . $result_Code . " untuk order ID " . $this->prefix . $order_id . " dan hasil validasi cek transaksi status code " . $respon->statusCode);
						$this->log("Callback diterima dengan result code " . $result_Code . " untuk Order ID " . $order_id . " dan hasil validasi cek transaksi status code " . $respon->statusCode);
					}
				}else if($result_Code == "01"){
					$respon = json_decode($this->validate_transaction($this->prefix . $order_id));
					if($respon->statusCode == "02"){
						$order->update_status("failed");
						$order->add_order_note("Pembayaran menggunakan Duitku dengan order ID " . $this->prefix . $order_id . "gagal");
						$this->log("Callback diterima. Pembayaran dengan order ID " . $order_id . "gagal");
					}else if($respon->statusCode == "01"){
						$order->add_order_note("Pembayaran menggunakan Duitku dengan order ID " . $this->prefix . $order_id . "reference " . $reference. "tertunda");
						$this->log("Callback diterima. Pembayaran dengan order ID " . $order_id . " tertunda.");
					}else{
						$order->add_order_note("Callback diterima dengan result code " . $result_Code . " untuk order ID " . $this->prefix . $order_id . " dan hasil validasi cek transaksi status code " . $respon->statusCode);
						$this->log("Callback diterima dengan result code " . $result_Code . " untuk Order ID " . $order_id . " dan hasil validasi cek transaksi status code " . $respon->statusCode);
					}
				}else{
						$order->add_order_note("Callback diterima dengan result code " . $result_Code . " untuk Order ID " . $this->prefix . $order_id);
						$this->log("Callback diterima dengan result code " . $result_Code . " untuk Order ID " . $order_id);
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
						$order->add_order_note("Transaksi untuk order ID " . $order_id . " telah diproses, menunggu verifikasi hasil pembayaran");
						$this->log('Notify Response. Transaksi untuk order ID ' .$order_id . " result code " .$params['resultCode']);
						WC()->cart->empty_cart();
            			return wp_redirect($order->get_checkout_order_received_url());
				}else if ($params['resultCode'] == '01') {
						$order->add_order_note("Transaksi untuk order ID " . $order_id . " sedang diproses");
						$this->log('Notify Response. Transaksi untuk order ID ' .$order_id . " result code " .$params['resultCode']);

						wc_add_notice('Melakukan pembatalan pembayaran untuk order ID ' . $order_id);

						WC()->cart->empty_cart();
						return wp_redirect(home_url('/my-account/orders/'));
				} else {
						$order->add_order_note("Pembayaran dengan Duitku untuk order ID" . $order_id . " tidak berhasil, dengan result code " .$params['resultCode']);
						$this->log('Notify Response. Transaksi untuk order ID ' .$order_id . " result code " .$params['resultCode']);
						$this->log('back to checkout page');
						WC()->cart->empty_cart();
						return wp_redirect(home_url('/my-account/orders/'));          			
				}
			}

			/**
			 * @param $order_id
			 * @param $reference
			 */
			protected function validate_transaction($order_id) {

				$order = new WC_Order($order_id);

				//endpoint for transactionStatus
				$url = esc_url_raw($this->endpoint) . '/api/merchant/transactionStatus';

				//generate Signature
				$signature = md5($this->merchantCode . $order_id . $this->apiKey);

				// Prepare Parameters
				$params = array(
					'merchantCode' => $this->merchantCode, // API Key Merchant /
					'merchantOrderId' => $order_id,
					'signature' => $signature
				);

				$headers = array('Content-Type' => 'application/json');

				// show request for inquiry
				$this->log("validate transaction:");
				$this->log(var_export(json_encode($params), true));
				$this->log("validate url: " . $url);

				$response = wp_remote_post($url, array(
					'method' => 'POST', 
					'body' => json_encode($params), 
					'timeout' => 90, 
					'sslverify' => false, 
					'headers' => $headers,
				));

				// Retrieve the body's resopnse if no errors found
				$response_body = wp_remote_retrieve_body($response);
				$response_code = wp_remote_retrieve_response_code($response);
				$resp = json_decode($response_body);

				$this->log("response Body validate transaction: " . $response_body);
				$this->log("receive response HTTP Code: " . $response_code . " with status code check transaction: " . $resp->statusCode);

				if ($response_code == '200') {
					return $response_body;
				} else {
					$this->log($response_body);
				}

				exit;
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


?>