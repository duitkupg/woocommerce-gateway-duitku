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
	    $this->icon = plugins_url('/assets/bca-klikpay.png', dirname(__FILE__) );
	}
 }
 //$obj = new WC_Gateway_Duitku_Mandiri;

?>
