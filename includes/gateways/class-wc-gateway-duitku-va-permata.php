<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku VA Permata
 *
 * This gateway is used for processing online payment using VA Permata
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_VA_Permata
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_VA_Permata extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_va_permata';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku VA Permata';
	    $this->payment_method = 'BT';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/va_permata.png', dirname(__FILE__) );
	}
 }

?>
