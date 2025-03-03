<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku BCA Klikpay
 *
 * This gateway is used for processing BCA Klikpay online payment
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_bca
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_BCA extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_bca';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku BCA Klikpay';
	    $this->payment_method = 'BK';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/duitku_bca.png', dirname(__FILE__) );
		
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
					'default' => __('Pembayaran Duitku BCA Klikpay', 'wc-duitku'),
					'desc_tip'      => true,
				  ),
				  'description' => array(
					'title' => __('Description', 'wc-duitku'),
					'type' => 'textarea',
					'description' => __('', 'wc-duitku'),
					'default' => 'Sistem pembayaran menggunakan Duitku.'
				  )

				);
	}
 }
 //$obj = new WC_Gateway_Duitku_Mandiri;

?>
