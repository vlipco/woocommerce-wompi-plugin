<?php
/*
Plugin Name: Plugin WooCommerce de Wompi
Plugin URI: https://docs.wompi.co/
Description: Plugin WooCommerce para la pasarela de pagos Wompi.
Version: 1.0.0
Author: Vlipco SAS
Author URI: https://wompi.co/
Domain Path: /languages
Text Domain: woocommerce-gateway-wompi
WC requires at least: 3.5.0
WC tested up to: 4.3.0
*/

defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
define( 'WC_WOMPI_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'WC_WOMPI_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WC_WOMPI_MIN_WC_VER', '3.5.0' );

/**
 * Notice if WooCommerce not activated
 */
function woocommerce_gateway_wompi_wc_missing_notice() {
    echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'WooCommerce Wompi Gateway requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-gateway-wompi' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Notice if WooCommerce not supported
 */
function woocommerce_gateway_wompi_wc_not_supported_notice() {
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'WooCommerce Wompi Gateway requires WooCommerce %1$s or greater.', 'woocommerce-gateway-wompi' ), WC_WOMPI_MIN_WC_VER, WC_VERSION ) . '</strong></p></div>';
}

/**
 * Hook on plugins loaded
 */
add_action( 'plugins_loaded', 'woocommerce_gateway_wompi_init', 0 );
function woocommerce_gateway_wompi_init() {
    /**
     * Load languages
     */
    load_plugin_textdomain( 'woocommerce-gateway-wompi', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

    /**
     * Check if WooCommerce is activated
     */
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'woocommerce_gateway_wompi_wc_missing_notice' );
        return;
    }
		
		/**
     * Check if WooCommerce is supported
     */
    if ( version_compare( WC_VERSION, WC_WOMPI_MIN_WC_VER, '<' ) ) {
        add_action( 'admin_notices', 'woocommerce_gateway_wompi_wc_not_supported_notice' );
        return;
    }

    /**
     * Returns the main instance of WC_Wompi
     */
    require_once WC_WOMPI_PLUGIN_PATH . '/includes/class-wc-wompi.php';
    WC_Wompi::instance();

    /**
     * Add plugin action links
     */
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'WC_Wompi', 'plugin_action_links' ) );
}
