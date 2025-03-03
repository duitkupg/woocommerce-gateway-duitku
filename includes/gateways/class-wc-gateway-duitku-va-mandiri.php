<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku VA MANDIRI
 *
 * This gateway is used for processing online payment using VA MANDIRI
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_VA_MANDIRI
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_VA_MANDIRI extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_va_mandiri';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku VA MANDIRI';
	    $this->payment_method = 'M1';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/mandiri.png', dirname(__FILE__) );
	}
 }

?>
