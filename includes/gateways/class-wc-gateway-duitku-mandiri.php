<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku Mandiri KlikPay
 *
 * This gateway is used for processing online payment using mandiri clickpay.
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_Mandiri
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_Mandiri extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_mandiri';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku Mandiri';
	    $this->payment_method = 'MY';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/mandiricp.png', dirname(__FILE__) );
	}
 }
 //$obj = new WC_Gateway_Duitku_Mandiri;

?>
