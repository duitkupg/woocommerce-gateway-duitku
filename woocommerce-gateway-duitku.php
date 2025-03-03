<?php
/*
Plugin Name: Duitku Payment Gateway
Description: Duitku Payment Gateway API V2: 2.11.11
Version: 2.11.11

Author: Duitku
Author URI: https://www.duitku.com/
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
- Add new Gudang Voucher QRIS

improvement 2.11.5 to 2.11.6
- Add new Payment Jenius Pay

improvement 2.11.6 to 2.11.7
-improvement for signature validation in callback
-fix failing status from check transaction

improvement 2.11.7 to 2.11.8
-fix process fees in signature validation

improvement 2.11.8 to 2.11.9
-Re-add Sampoerna Bank
-Add new Payment Danamon VA

improvement 2.11.9 to 2.11.10
-Add new payment BSI VA
-Adjustment for order total exclude fee to include fee

improvement 2.11.10 to 2.11.11
- Woocommerce Duitku Payment Gateway API V2 Support Blocks Checkout
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Duitku_Payments{
	public static function init(){
		add_action('plugins_loaded', array(__CLASS__, 'woocommerce_duitku_init'), 0);
		add_action('woocommerce_blocks_loaded', array(__CLASS__, 'woocommerce_gateway_duitku_woocommerce_block_support'));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'duitku_dom_manipulate_init'));
	}

	public static function duitku_dom_manipulate_init(){
		wp_enqueue_script( 'duitku-dom-manipulate-js', plugins_url('/includes/assets/js/duitku_dom_manipulate.js', __FILE__ ));
	}

	public static function woocommerce_duitku_init(){
		if(!class_exists('WC_Payment_Gateway')){
			return;
		}
		
		include_once dirname(__FILE__) . '/includes/admin/class-wc-duitku-settings.php';
		include_once dirname(__FILE__) . '/includes/duitku/wc-gateway-duitku-sanitized.php';
		include_once dirname(__FILE__) . '/includes/duitku/wc-gateway-duitku-validation.php';
		if(!class_exists('Duitku_Payment_Gateway')){
			include dirname(__FILE__) . '/includes/class-wc-gateway-duitku.php';
		}

		add_filter('woocommerce_payment_gateways', 'add_duitku_gateway');

		function add_duitku_gateway($methods){
			$methods[] = 'WC_Gateway_Duitku_VA_Permata';
			$methods[] = 'WC_Gateway_Duitku_VA_BNI';
			$methods[] = 'WC_Gateway_Duitku_OVO';
			$methods[] = 'WC_Gateway_Duitku_CC';
			$methods[] = 'WC_Gateway_Duitku_CC_MIGS';
			$methods[] = 'WC_Gateway_Duitku_BCA';
			$methods[] = 'WC_Gateway_Duitku_VA_ATM_Bersama';
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
			$methods[] = 'WC_Gateway_Duitku_VA_SAMPOERNA';
			$methods[] = 'WC_Gateway_Duitku_LINKAJA_QRIS';
			$methods[] = 'WC_Gateway_Duitku_INDOMARET';
			$methods[] = 'WC_Gateway_Duitku_POS';
			$methods[] = 'WC_Gateway_Duitku_BRIVA';
			$methods[] = 'WC_Gateway_Duitku_BNC';
			$methods[] = 'WC_Gateway_Duitku_NOBU_Qris';
			$methods[] = 'WC_Gateway_Duitku_ATOME';
			$methods[] = 'WC_Gateway_Duitku_JENIUS_PAY';
			$methods[] = 'WC_Gateway_Duitku_GUDANG_VOUCHER_QRIS';
			$methods[] = 'WC_Gateway_Duitku_VA_DANAMON_H2H';
			$methods[] = 'WC_Gateway_Duitku_VA_BSI';
			return $methods;
		}

		foreach (glob(dirname(__FILE__) . '/includes/gateways/*.php') as $filename) {
		include_once $filename;
	}

	}

	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	public static function plugin_abspath() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/*
	* function to declare compatibility with cart checkout blocks
	*/

	public static function woocommerce_gateway_duitku_woocommerce_block_support() {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			require_once 'includes/blocks/class-wc-duitku-payments-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-permata-blocks.php';
            require_once 'includes/blocks/class-wc-gateway-duitku-va-bni-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-ovo-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-cc-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-cc-migs-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-bca-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-atm-bersama-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-bca-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-mandiri-h2h-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-cimb-niaga-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-maybank-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-ritel-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-shopee-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-indodana-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-shopeepay-applink-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-linkaja-applink-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-dana-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-artha-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-sampoerna-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-linkaja-qris-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-indomaret-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-pos-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-briva-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-bnc-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-nobu-qris-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-atome-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-jenius-pay-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-gudang-voucher-qris-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-danamon-h2h-blocks.php';
			require_once 'includes/blocks/class-wc-gateway-duitku-va-bsi-blocks.php';
			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_Permata_Blocks() );
                    $payment_method_registry->register( new WC_Gateway_Duitku_VA_BNI_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_OVO_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_CC_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_CC_MIGS_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_BCA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_ATM_Bersama_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_BCA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_MANDIRI_H2H_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_CIMB_Niaga_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_Maybank_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_Ritel_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_SHOPEE_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_INDODANA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_SHOPEEPAY_APPLINK_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_LINKAJA_APPLINK_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_DANA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_ARTHA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_SAMPOERNA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_LINKAJA_QRIS_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_INDOMARET_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_POS_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_BRIVA_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_BNC_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_NOBU_Qris_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_ATOME_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_JENIUS_PAY_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_GUDANG_VOUCHER_QRIS_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_DANAMON_H2H_Blocks() );
					$payment_method_registry->register( new WC_Gateway_Duitku_VA_BSI_Blocks() );
				}
			);
		}
	}
}

WC_Duitku_Payments::init();
?>