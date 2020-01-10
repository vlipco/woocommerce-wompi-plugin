<?php
defined( 'ABSPATH' ) || exit;

/**
 * Extend Payment Gateway class
 */
class WC_Gateway_Wompi_Custom extends WC_Payment_Gateway {

    /**
     * Vars
     */
    const MINIMUM_ORDER_AMOUNT = 150000;
    public $testmode;
    public $public_key;
    public $private_key;

    /**
     * Init hooks
     */
    public function init_hooks() {
        add_action( 'woocommerce_receipt_wompi', array( $this, 'generate_wompi_widget' ) );
    }

    /**
     * Returns all supported currencies for this payment method
     */
    public function get_supported_currency() {
        return apply_filters( 'wc_wompi_supported_currencies', WC_Wompi_API::instance()->supported_currency );
    }

    /**
     * Generate Wompi widget on "Pay for order" page
     */
    public function generate_wompi_widget( $order_id ) {
        $order = new WC_Order( $order_id );

        $out = '';
        $out .= '<div class="wompi-button-holder">';
        $out .= '
            <script
                src="https://checkout.wompi.co/widget.js"
                data-render="button"
                data-public-key="'.( $this->get_option( 'testmode' ) ? $this->get_option( 'test_public_key' ) : $this->get_option( 'public_key' ) ).'"
                data-currency="'.get_woocommerce_currency().'"
                data-amount-in-cents="'.WC_Wompi_Helper::get_amount_in_cents( $order->get_total() ).'"
                data-reference="'.$order_id.'"
                data-redirect-url="'.$order->get_checkout_order_received_url().'"
                >
            </script>
        ';
        $out .= '</div>';

        echo $out;
    }

    /**
     * Billing details fields on the checkout page
     */
    public static function billing_fields() {
        return array(); // return empty, means hide
    }

    /**
     * Before checkout billing form
     */
    public static function before_checkout_billing_form() {
        echo '<p>' . __('Billing details will need to be entered in the Wompi widget', 'woocommerce-gateway-wompi') . '</p>';
    }

    /**
     * Generate order key on thank you page
     */
    public static function thankyou_order_key( $order_key ) {
        if ( empty( $_GET['key'] ) ) {
            global $wp;
            $order = wc_get_order( $wp->query_vars['order-received'] );
            $order_key = $order->get_order_key();
        }

        return $order_key;
    }

    /**
     * Inform user if status of received order is failed on the thank you page
     */
    public static function thankyou_order_received_text( $text ) {
        global $wp;
        $order = wc_get_order( $wp->query_vars['order-received'] );
        $status = $order->get_status();
        if ( in_array( $status, array( 'cancelled', 'failed', 'refunded', 'voided' ) ) ) {
            return '<div class="woocommerce-error">' . sprintf( __( 'This order changed to status &ldquo;%s&rdquo;. Please contact us if you need assistance.', 'woocommerce-gateway-wompi' ), $status ) . '</div>';
        } else {
            return $text;
        }
    }

    /**
     * Validation on checkout page
     */
    public static function checkout_validation( $fields, $errors ){
        $amount = floatval( WC()->cart->total );
        if ( ! self::validate_minimum_order_amount( $amount ) ) {
            $errors->add( 'validation', sprintf( __( 'Sorry, the minimum allowed order total is %1$s to use this payment method.', 'woocommerce-gateway-wompi' ), self::MINIMUM_ORDER_AMOUNT ) );
        }
    }

    /**
     * Validates that the order meets the minimum order amount
     */
    public static function validate_minimum_order_amount( $amount ) {
        if ( WC_Wompi_Helper::get_amount_in_cents( $amount ) < self::MINIMUM_ORDER_AMOUNT ) {
            return false;
        } else {
            return true;
        }
    }
}