<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku VA Maybank
 *
 * This gateway is used for processing online payment using VA Maybank
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_Maybank
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_VA_Maybank extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_va_maybank';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku VA Maybank';
	    $this->payment_method = 'VA';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/maybank.png', dirname(__FILE__) );
	}
 }

?>
