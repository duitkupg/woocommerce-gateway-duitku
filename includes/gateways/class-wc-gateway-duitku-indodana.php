<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku INDODANA
 *
 * This gateway is used for processing online payment using INDODANA.
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_INDODANA
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_INDODANA extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_indodana';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku INDODANA';
	    $this->payment_method = 'DN';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/indodana.png', dirname(__FILE__) );
		
		//Load settings
		$this->init_form_fields();
		$this->init_settings();
	}
	
	/**
	 * set field for each payment gateway
	 * @return void
	 */
	function init_form_fields() {
			
		$this->form_fields = array(
			'enabled' => array(
				'title' => __('Enable/Disable', 'wc-duitku'),
				'label' => __('Enable Duitku', 'wc-duitku'),
				'type' => 'checkbox', 'description' => '',
				'default' => 'no',
			),
			'title' => array(
				'title' => __('Title', 'wc-duitku'), 
				'type' => 'text', 
				'description' => __('', 'wc-duitku'),
				'default' => __('Pembayaran Duitku', 'wc-duitku'),
			),
			'description' => array(
				'title' => __('Description', 'wc-duitku'),
				'type' => 'textarea', 
				'description' => __('', 'wc-duitku'), 
				'default' => 'Sistem pembayaran menggunakan Duitku.',
			),
		);
	}
	
 }
 //$obj = new WC_Gateway_Duitku_OVO;

?>
