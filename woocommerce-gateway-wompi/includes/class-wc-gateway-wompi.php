<?php
defined( 'ABSPATH' ) || exit;

/**
 * Payment Gateway class
 */
class WC_Gateway_Wompi extends WC_Gateway_Wompi_Custom {

    /**
     * Constructor
     */
    public function __construct() {

        $options = WC_Wompi::$settings;

        $this->id = 'wompi';
        $this->method_title = 'WOMPI';
        $this->method_description = sprintf( __( 'Wompi works via Widget Checkout. <a href="%1$s" target="_blank">Sign up</a> for a Wompi account, and <a href="%2$s" target="_blank">get your Wompi account keys</a>.', 'woocommerce-gateway-wompi' ), 'https://comercios.wompi.co/', 'https://comercios.wompi.co/my-account' );
        $this->has_fields = false;
        $this->init_form_fields();
        $this->init_settings();
        $this->enabled = $options['enabled'];
        $this->icon = WC_WOMPI_PLUGIN_URL . '/assets/img/wompi-logo.png';
        $this->title = $options['title'];
        $this->description = $options['description'];
        $this->testmode = $options['testmode'];
        $this->supports = array(
            'products'
        );
        $this->public_key  = $this->testmode ? $options['test_public_key'] : $options['public_key'];
        $this->private_key = $this->testmode ? $options['test_private_key'] : $options['private_key'];

        // Hooks
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        if ( 'yes' === $this->enabled ) {
            $this->init_hooks();
        }
    }

    /**
     * Checks to see if all criteria is met before showing payment method
     */
    public function is_available() {
        if ( 'yes' === $this->enabled ) {
            if ( ! $this->private_key ||
                 ! $this->public_key ||
                 ! in_array( get_woocommerce_currency(), self::get_supported_currency() )
            ) {
                return false;
            }

            return true;
        }

        return parent::is_available();
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = require( dirname( __FILE__ ) . '/admin/wompi-settings.php' );
    }

    /**
     * Gets the transaction URL linked to Wompi dashboard
     */
    public function get_transaction_url( $order ) {
        $this->view_transaction_url = 'https://comercios.wompi.co/transactions/%s';

        return parent::get_transaction_url( $order );
    }

    /**
     * Process the payment (after place order)
     */
    public function process_payment( $order_id ) {
        $order = new WC_Order( $order_id );
        if ( version_compare(WOOCOMMERCE_VERSION, '2.1.0', '>=') ) {
            /* >= 2.1.0 */
            $checkout_payment_url = $order->get_checkout_payment_url(true);
        } else {
            /* < 2.1.0 */
            $checkout_payment_url = get_permalink( get_option('woocommerce_pay_page_id') );
        }

        // Clear cart
        WC()->cart->empty_cart();

        return array(
            'result' => 'success',
            'redirect' => add_query_arg( 'order_pay', $order_id, $checkout_payment_url )
        );
    }

    /**
     * Process the payment to void
     */
    public static function process_void( $order ) {

        // Restore stock
        wc_maybe_increase_stock_levels( $order );
    }
}