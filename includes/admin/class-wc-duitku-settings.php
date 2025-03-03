<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Duitku Admin Global Settings Payment
 *
 * This file is used for creating the Duitku global configuration
 *
 * Copyright (c) Duitku
 *
 * This script is only free to the use for merchants of Duitku. If
 * you have found this script useful a small recommendation as well as a
 * comment on merchant form would be greatly appreciated.
 *
 * @class       Duitku_Settings
 * @package     Duitku/Classes
 * @category    Class
 * @author      Duitku
 * @located at  /includes/admin/
 */

class Duitku_Settings {

	public static $tab_name = 'duitku_settings';
	public static $option_prefix = 'duitku';
	public static function init() {
		$request = $_REQUEST;
		add_filter('woocommerce_settings_tabs_array', array(__CLASS__, 'add_duitku_settings_tab'), 50);
		add_action('woocommerce_settings_tabs_duitku_settings', array(__CLASS__, 'duitku_settings_page'));
		add_action('woocommerce_update_options_duitku_settings', array(__CLASS__, 'update_duitku_settings'));
		//      add_action( 'admin_enqueue_scripts', array(__CLASS__ , 'enqueue_scripts' ) );
	}

	/**
	 * Validate the data for Duitku global configuration
	 *
	 * $param array $request
	 * @return mixed
	 */
	public static function validate_configuration($request) {
		foreach ($request as $k => $v) {
			$key = str_replace('duitku_', '', $k);
			$options[$key] = $v;
		}
		//if ( wc_duitku_global_validation( $options ) )
		//   return __( 'Please fill in all the mandatory fields', 'wc-duitku' );
		return '';
	}

	/**
	 * Adds Duitku Tab to WooCommerce
	 * Calls from the hook "woocommerce_settings_tabs_array"
	 *
	 * @param array $woocommerce_tab
	 * @return array $woocommerce_tab
	 */
	public static function add_duitku_settings_tab($woocommerce_tab) {
		$woocommerce_tab[self::$tab_name] = 'Duitku ' . __('Global Configuration', 'wc-duitku');
		return $woocommerce_tab;
	}

	/**
	 * Adds setting fields for Duitku global configuration

	 * @param none
	 * @return void
	 */
	public static function duitku_settings_fields() {
		global $duitku_payments;
		//add_action( 'admin_footer', 'wc_duitku_custom_admin_redirect' );
		//$admin_url = admin_url( 'admin.php?page=wc-duitku-admin' );
		//$logpath = ((WOOCOMMERCE_VERSION > '2.2.0' ) ? wc_get_log_file_path( 'duitkupayments' ) : "woocommerce/logs/novalnetpayments-".sanitize_file_name( wp_hash( 'novalnetpayments' )));
		$settings = apply_filters('woocommerce_' . self::$tab_name, array(
			array(
				'title' => 'Duitku ' . __('Global Configuration', 'wc-duitku'),
				'id' => self::$option_prefix . '_global_settings',
				'desc' => __('Selamat datang di pengaturan global duitku. Untuk dapat menggunakan duitku payment channel, mohon mengisi form di bawah ini.
<br \>  untuk mendapatkan api dan merchant code mohon kontak  <a href="mailto:admin@duitku.com">admin@duitku.com</a>', 'wc-duitku'),
				'type' => 'title',
				'default' => '',
			),
			array(
				'title' => __('Merchant Code', 'wc_duitku'),
				'desc' => '<br />' . __('masukkan kode merchant anda.', 'wc-duitku'),
				'id' => self::$option_prefix . '_merchant_code',
				'type' => 'text',
				'default' => '',
			),
			array(
				'title' => __('API Key', 'wc_duitku'),
				'desc' => '<br />' . __(' Dapatkan API Key <a href=https://duitku.com>disini</a></small>.', 'wc-duitku'),
				'id' => self::$option_prefix . '_api_key',
				'type' => 'text',
				'css' => 'width:25em;',
				'default' => '',
			),
			array(
				'title' => __('Duitku Endpoint', 'wc_duitku'),
				'desc' => '<br />' . __('Duitku endpoint API. Mohon isi merchant code dan api key sebelum mengakses endpoint.', 'wc-duitku'),
				'id' => self::$option_prefix . '_endpoint',
				'type' => 'text',
				'css' => 'width:25em;',
				'default' => '',
			),
			array(
				'title' => __('Duitku Debug', 'wc_duitku'),
				'desc' => '<br />' . sprintf(__('Duitku Log dapat digunakan untuk melihat event, seperti notifikasi pembayaran.
                 <code>%s</code> ', 'woothemes'), wc_get_log_file_path('duitku')),
				'id' => self::$option_prefix . '_debug',
				'type' => 'checkbox',
				'default' => 'no',
			),
		));
		return apply_filters('woocommerce_' . self::$tab_name, $settings);
	}

	/**
	 * Adds settings fields to the individual sections
	 * Calls from the hook "woocommerce_settings_tabs_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function duitku_settings_page() {
		woocommerce_admin_fields(self::duitku_settings_fields());
	}

	/**
	 * Updates settings fields from individual sections
	 * Calls from the hook "woocommerce_update_options_" {tab_name}
	 *
	 * @param none
	 * @return void
	 */
	public static function update_duitku_settings() {
		woocommerce_update_options(self::duitku_settings_fields());
	}

}

Duitku_Settings::init();
