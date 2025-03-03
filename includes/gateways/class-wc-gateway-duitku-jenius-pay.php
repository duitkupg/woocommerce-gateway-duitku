<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku JENIUS PAY
 *
 * This gateway is used for processing online payment using JENIUS PAY.
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_JENIUS PAY
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_JENIUS_PAY extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_jenius_pay';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku JENIUS PAY';
	    $this->payment_method = 'JP';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/duitku_jenius_pay.png', dirname(__FILE__) );
		
		//Load settings
		$this->init_form_fields();
		$this->init_settings();
	}
	
	/**
	 * set field for each payment gateway
	 * @return void
	 */
	function init_form_fields()
	{

				$this->form_fields = array(
				  'enabled' => array(
					'title' => __('Enable/Disable', 'wc-duitku'),
					'type' => 'checkbox',
					'label' => __('Enable Duitku Payment', 'wc-duitku'),
					'default' => 'no'
				  ),
				  'title' => array(
					'title' => __('Title', 'wc-duitku'),
					'type' => 'text',
					'description' => __('', 'wc-duitku'),
					'default' => __('Pembayaran Duitku Jenius Pay', 'wc-duitku'),
					'desc_tip'      => true,
				  ),
				  'description' => array(
					'title' => __('Description', 'wc-duitku'),
					'type' => 'textarea',
					'description' => __('', 'wc-duitku'),
					'default' => 'Sistem pembayaran menggunakan Duitku.'
				  ),
				  'duitku_expiry_period' => array(
					'title' => __('Expired Period', 'wc-duitku'),
					'type' => 'number',
					'text', 'description' => __('', 'wc-duitku'),
					'description' => __('Masa berlaku transaksi sebelum kedaluwarsa. example <code>1 - 1440 ( menit )</code>', 'wc-duitku'),
					'default' => __('1440', 'wc-duitku'),
					'custom_attributes' => array(
						'min'       =>  1,
						'max'       =>  1440,
					),
				  ),
				);
	}
	
 }
 //$obj = new WC_Gateway_Duitku_JENIUS PAY;

?>
