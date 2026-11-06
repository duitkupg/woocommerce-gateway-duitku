<?php
    class WC_Gateway_Duitku_LINKAJA_APPLINK_Blocks extends WC_Gateway_Duitku_Blocks_Support {
        protected $name = 'duitku_linkaja_applink';

    public function initialize() {
        $this->settings = get_option( 'woocommerce_duitku_linkaja_applink_settings', array() );
    }

    public function is_active() {
        return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
    }

    public function get_payment_method_script_handles() {
        wp_register_script(
            'duitku-linkaja-applink-blocks',
            plugins_url( '../assets/js/blocks/linkaja-applink.js', __FILE__ ), // adjust path
            array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities' ),
            '1.0.0',
            true
        );
        return array( 'duitku-linkaja-applink-blocks' );
    }

    public function get_payment_method_data() {
        return array(
            'title'       => ! empty( $this->settings['title'] ) ? $this->settings['title'] : 'Linkaja Applink',
            'description' => ! empty( $this->settings['description'] ) ? $this->settings['description'] : '',
            'icon'        => plugins_url( '../assets/duitku_linkaja_applink.png', __FILE__ ),
            'supports'    => array( 'products' ),
        );
    }
}