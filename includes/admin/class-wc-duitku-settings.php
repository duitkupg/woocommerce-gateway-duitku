<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Duitku Admin Global Settings Payment
 *
 * This file is used for creating the Duitku global configuration
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       Duitku_Settings
 * @package     Duitku/Classes
 * @category    Class
 * @author      Duitku
 * @located at  /includes/admin/
 */

class Duitku_Settings {

	public static $tab_name = 'duitku_settings';
	public static $option_prefix = 'duitku';
	public static function init() {
		add_filter('woocommerce_settings_tabs_array', array(__CLASS__, 'add_duitku_settings_tab'), 50);
		add_action('woocommerce_settings_tabs_duitku_settings', array(__CLASS__, 'duitku_settings_page'));
		add_action('woocommerce_update_options_duitku_settings', array(__CLASS__, 'update_duitku_settings'));
		add_action( 'woocommerce_cart_calculate_fees', array(__CLASS__, 'wp_add_checkout_fees') );
		add_action( 'woocommerce_review_order_before_payment', array(__CLASS__, 'wp_refresh_checkout_on_payment_methods_change') );
		add_action( 'woocommerce_view_order', array(__CLASS__, 'view_order_and_thankyou_page' ), 1, 1);
		add_action('woocommerce_thankyou', array(__CLASS__, 'view_order_and_thankyou_page' ), 1, 1);

		//load fee API before page checkout
		add_action('woocommerce_review_order_before_payment', array(__CLASS__, 'fee' ), 1, 1);
	}

	/**
	* Hook function that will be called on thank you page
	* Output HTML for payment/instruction URL
	* @param  [String] $order_id generated by WC
	* @return [String] HTML
	*/
	public static function view_order_and_thankyou_page( $order_id ) {
		if ( !isset($order_id) ) return;

		$order = new WC_Order( $order_id );
		$reference = get_post_meta($order->get_id(), '_duitku_pg_reference', true);

		if ( isset($reference) ) {
			echo "<h2>Payment Info</h2>
			  <table class='woocommerce-table shop_table'>
				  <tbody>
					<tr>
						<th scope='row'>Reference:</th>
						<td>$reference</td>
					</tr>
				</tbody>
			</table>";
			
		}
	}

	// add fee
	public static function wp_add_checkout_fees(  $order_id ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		self::fee();

		$chosen_gateway = WC()->session->get( 'chosen_payment_method' );


		if ( $chosen_gateway == 'duitku_ovo' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('OV') );
		} else
		if ( $chosen_gateway == 'duitku_credit_card' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('VC') );
		} else
		if ( $chosen_gateway == 'duitku_bca' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('BK') );
		} else
		if ( $chosen_gateway == 'duitku_va_permata' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('BT') );
		} else
		if ( $chosen_gateway == 'duitku_va_atm_bersama' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('A1') );
		} else
		if ( $chosen_gateway == 'duitku_va_bni' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('I1') );
		} else
		if ( $chosen_gateway == 'duitku_va_mandiri_h2h' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('M2') );
		} else
		if ( $chosen_gateway == 'duitku_va_cimb_niaga' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('B1') );
		} else
		if ( $chosen_gateway == 'duitku_va_maybank' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('VA') );
		} else
		if ( $chosen_gateway == 'duitku_va_ritel' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('FT') );
		}else
		if ( $chosen_gateway == 'duitku_shopee' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('SP') );
		}else
		if ( $chosen_gateway == 'duitku_indodana' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('DN') );
		}else
		if ( $chosen_gateway == 'duitku_shopeepay_applink' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('SA') );
		}else
		if ( $chosen_gateway == 'duitku_linkaja_applink' ) {
			$option_setting = (array)get_option( 'woocommerce_' . $chosen_gateway . '_settings' );
			
			if ( isset($option_setting['tipe']) ){
				WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee($option_setting['tipe']) );
			} else {
				WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('LA') );
			}
		}else
		if ( $chosen_gateway == 'duitku_nobu_qris' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('NQ') );
		}else
		if ( $chosen_gateway == 'duitku_va_bca' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('BC') );
		}else
		if ( $chosen_gateway == 'duitku_credit_card_migs' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('MG') );
		}else
		if ( $chosen_gateway == 'duitku_dana' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('DA') );
		}else
		if ( $chosen_gateway == 'duitku_indomaret' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('IR') );
		}else
		if ( $chosen_gateway == 'duitku_pospay' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('A2') );
		}else
		if ( $chosen_gateway == 'duitku_briva' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('BR') );
		}else
		if ( $chosen_gateway == 'duitku_bnc' ) {
			WC()->cart->add_fee( __('Surcharge', 'wc-duitku'), self::get_fee('NC') );
		}
	}

	// assign fee
	public static function fee() {
		WC()->session->set('paymentFee', 0);

		$feeAmount = 0;
		if ( sizeof( WC()->cart->get_fees() ) > 0 ) {
		  $fees = WC()->cart->get_fees();
		  $i = 0;
		  foreach( $fees as $item ) {

			if ( $item->name == __('Surcharge', 'wc-duitku') ) {
			  continue;
			}

			$feeAmount = ceil($item->amount);
		  }
		}

		$endpoint	= rtrim(get_option('duitku_endpoint'), '/');
		$amount		= CEIL( WC()->cart->cart_contents_total + WC()->cart->shipping_total - WC()->cart->tax_total + $feeAmount );
		$datetime	= date('Y-m-d H:i:s', time());

		//endpoint for checkfee
		$url = $endpoint . '/api/merchant/paymentmethod/getpaymentmethod';

		//generate Signature
		$signature = hash('sha256', get_option('duitku_merchant_code') . $amount . $datetime . get_option('duitku_api_key'));
		// Prepare Parameters
		$params = array(
			'merchantCode'	=> get_option('duitku_merchant_code'),
			'amount' 		=> $amount,
			'datetime' 		=> $datetime,
			'signature'		=> $signature
		);

		$headers = array('Content-Type' => 'application/json');


		$response = wp_remote_post($url, array(
			'method' => 'POST', 'body' => json_encode($params), 'timeout' => 90, 'sslverify' => false, 'headers' => $headers,
		));

		// Retrieve the body's resopnse if no errors found
		$response_body = wp_remote_retrieve_body($response);
		$response_code = wp_remote_retrieve_response_code($response);


		if ($response_code == '200') {
			// Parse the response into something we can read
			$resp = json_decode($response_body);

			if ($resp->responseCode == '00') {
				WC()->session->set( 'paymentFee', $resp->paymentFee );
			}

		} else {
			return false;
		}

		return false;
	}

	// GET fee session
	private static function get_fee($paymentMethod){
		$json = WC()->session->get('paymentFee');

		if ( empty($json) ) {
			return;
		}
		foreach($json as $data){
			if ($data->paymentMethod != $paymentMethod) continue;

			$fee =  $data->totalFee;
		}

		return $fee;
	}

	// reload checkout on payment gateway change
	public static function wp_refresh_checkout_on_payment_methods_change(){
		?>
		<script type="text/javascript">
			(function($){
				$( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
					$('body').trigger('update_checkout');
					// alert(p); //uncomment for testing
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Validate the data for Duitku global configuration
	 *
	 * $param array $request
	 * @return mixed
	 */
	public static function validate_configuration($request) {
		foreach ($request as $k => $v) {
			$key = str_replace('duitku_', '', $k);
			$options[$key] = $v;
		}
		//if ( wc_duitku_global_validation( $options ) )
		//   return __( 'Please fill in all the mandatory fields', 'wc-duitku' );
		return '';
	}

	/**
	 * Adds Duitku Tab to WooCommerce
	 * Calls from the hook "woocommerce_settings_tabs_array"
	 *
	 * @param array $woocommerce_tab
	 * @return array $woocommerce_tab
	 */
	public static function add_duitku_settings_tab($woocommerce_tab) {
		$woocommerce_tab[self::$tab_name] = 'Duitku ' . __('Global Configuration', 'wc-duitku');
		return $woocommerce_tab;
	}

	/**
	 * Adds setting fields for Duitku global configuration

	 * @param none
	 * @return void
	 */
	public static function duitku_settings_fields() {
		global $duitku_payments;
		//add_action( 'admin_footer', 'wc_duitku_custom_admin_redirect' );
		//$admin_url = admin_url( 'admin.php?page=wc-duitku-admin' );
		//$logpath = ((WOOCOMMERCE_VERSION > '2.2.0' ) ? wc_get_log_file_path( 'duitkupayments' ) : "woocommerce/logs/novalnetpayments-".sanitize_file_name( wp_hash( 'novalnetpayments' )));
		$settings = apply_filters('woocommerce_' . self::$tab_name, array(
			array(
				'title' => 'Duitku ' . esc_html('Global Configuration', 'wc-duitku'),
				'id' => self::$option_prefix . '_global_settings',
				'desc' => __('Selamat datang di pengaturan global duitku. Untuk dapat menggunakan duitku payment channel, mohon mengisi form di bawah ini.
					<br \>  untuk mendapatkan api dan merchant code mohon kontak  <a href="mailto:admin@duitku.com">admin@duitku.com</a>', 'wc-duitku'),
				'type' => 'title',
				'default' => '',
			),
			array(
				'title' => esc_html('Merchant Code', 'wc_duitku'),
				'desc' => '<br />' . esc_html('masukkan kode merchant anda.', 'wc-duitku'),
				'id' => self::$option_prefix . '_merchant_code',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => esc_html('API Key', 'wc_duitku'),
				'desc' => '<br />' . __(' Dapatkan API Key <a href=https://duitku.com>disini</a></small>.', 'wc-duitku'),
				'id' => self::$option_prefix . '_api_key',
				'type' => 'text',
				'css' => 'width:25em;',
				'default' => '',
			),
			array(
				'title' => esc_html('Duitku Endpoint', 'wc_duitku'),
				'desc' => '<br />' . __('Duitku endpoint API. Mohon isi merchant code dan api key sebelum mengakses endpoint.', 'wc-duitku'),
				'id' => self::$option_prefix . '_endpoint',
				'type' => 'text',
				'css' => 'width:25em;',
				'default' => '',
			),
			array(
				'title' => esc_html('Duitku Prefix', 'wc_duitku'),
				'desc' => '<br />' . __('Prefix order id. Dapat digunakan untuk custom order id', 'wc-duitku'),
				'id' => self::$option_prefix . '_prefix',
				'type' => 'text',
				'css' => 'width:25em;',
				'default' => '',
				'maxlength' => 2,
			),
			array(
				'title' => esc_html('Credential Code', 'wc_duitku'),
				'desc' => '<br />' . esc_html('Masukkan kode kredensial anda. Kode ini hanya digunakan untuk payment method Credit Card MIGS.', 'wc-duitku'),
				'id' => self::$option_prefix . '_credential_code',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => esc_html('Duitku Debug', 'wc_duitku'),
				'desc' => '<br />' . sprintf(__('Duitku Log dapat digunakan untuk melihat event, seperti notifikasi pembayaran.
                	<code>%s</code> ', 'woothemes'), wc_get_log_file_path('duitku')),
				'id' => self::$option_prefix . '_debug',
				'type' => 'checkbox',
				'default' => 'no',
			),
		));
		return apply_filters('woocommerce_' . self::$tab_name, $settings);
	}

	/**
	 * Adds settings fields to the individual sections
	 * Calls from the hook "woocommerce_settings_tabs_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function duitku_settings_page() {
		woocommerce_admin_fields(self::duitku_settings_fields());
	}

	/**
	 * Updates settings fields from individual sections
	 * Calls from the hook "woocommerce_update_options_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function update_duitku_settings() {
		woocommerce_update_options(self::duitku_settings_fields());
	}

}

Duitku_Settings::init();
