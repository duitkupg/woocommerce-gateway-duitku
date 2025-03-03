<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Class to support "Duitku V2" payment integration with WooCommerce Blocks.
 * 
 * This class extends AbstractPaymentMethodType and allows the "Duitku V2" payment method
 * to be used within WooCommerce Blocks.
 */
abstract class WC_Gateway_Duitku_Blocks_Support extends AbstractPaymentMethodType {

	private $gateway;
	protected $name;

	/**
	 * Initializes the class and loads the settings from WooCommerce.
	 * 
	 * This function is run when the class is initialized. It fetches the gateway settings from WooCommerce options
	 */
	public function initialize() {
		$this->settings = get_option( "woocommerce_{$this->name}_settings", []);
		$gateways       = WC()->payment_gateways->payment_gateways();
		$this->gateway  = $gateways[ $this->name ];
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Returns an array of scripts/handles to be registered for Duitku V2 payment method.
	 *
	 * The scripts returned are for frontend interaction in WooCommerce Blocks.
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/assets/js/frontend/blocks.js';
		$script_asset_path = WC_Duitku_Payments::plugin_abspath() . 'assets/js/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => '2.11.11'
			);
		$script_url        = WC_Duitku_Payments::plugin_url() . $script_path;

		wp_register_script(
			'wc-duitku-payments-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		return [ 'wc-duitku-payments-blocks' ];
	}

	/**
	 * Returns the payment method data required for WooCommerce Blocks.
	 * 
	 * This data is used on the frontend to display information about the payment method to the user.
	 * @return array - Payment method data, including title, description, and supported features.
	 */
	public function get_payment_method_data() {
		$gateway = $this->gateway;
		return [
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			'supports'    => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] )
		];
	}
}
