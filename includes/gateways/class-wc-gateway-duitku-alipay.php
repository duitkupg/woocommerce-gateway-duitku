<?php

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
 }

/**
 * Duitku Alipay
 *
 * This gateway is used for processing Alipay online payment
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       WC_Gateway_Duitku_alipay
 * @extends     Duitku_Payment_Gateway
 * @package     Duitku/Classes/Payment
 * @author      Duitku
 * @located at  /includes/gateways
 */

 class WC_Gateway_Duitku_Alipay extends Duitku_Payment_Gateway {
    var $sub_id = 'duitku_alipay';
        public function __construct() {
	    parent::__construct();
            $this->method_title = 'Duitku Alipay';
	    $this->payment_method = 'V1';
	    //payment gateway logo
	    $this->icon = plugins_url('/assets/alipay.png', dirname(__FILE__) );
	}
 }
 //$obj = new WC_Gateway_Duitku_Mandiri;

?>
