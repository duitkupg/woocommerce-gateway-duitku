<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku OVO
 *
 * This gateway is used for processing online payment using OVO.
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_OVO
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_OVO extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_ovo';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku OVO';
	    $this->payment_method = 'OV';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/duitku_ovo.png', dirname(__FILE__) );
		
		//Load settings
		$this->init_form_fields();
		$this->init_settings();
	}

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
				'default' => __('Pembayaran Duitku OVO', 'wc-duitku'),
				'desc_tip'      => true,
			  ),
			  'description' => array(
				'title' => __('Description', 'wc-duitku'),
				'type' => 'textarea',
				'description' => __('', 'wc-duitku'),
				'default' => 'Sistem pembayaran menggunakan Duitku.'
			  ),
			);
	}
	
	
 }
 //$obj = new WC_Gateway_Duitku_OVO;

?>
