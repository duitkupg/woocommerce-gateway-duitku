<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku Credit Card Via Veritrans
 *
 * This gateway is used for processing online payment using Veritrans Credit Card
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_CC
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_CC extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_credit_card';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku Credit Card';
	    $this->payment_method = 'VC';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/credit-card.png', dirname(__FILE__) );
	}
 }
 //$obj = new WC_Gateway_Duitku_Mandiri;

?>
