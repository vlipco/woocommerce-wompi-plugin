<?php
defined( 'ABSPATH' ) || exit;

/**
 * Add custom order statuses
 */
class WC_Wompi_Order_Statuses {

    /**
     * Vars
     */
    const VOIDED_EXPIRY = 3600; // 1 hour

    /**
     * Init
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_voided_post_status' ), 10 );
        add_filter( 'wc_order_statuses', array( $this, 'order_statuses' ) );
        add_action( 'woocommerce_process_shop_order_meta', array( $this, 'process_shop_order_meta' ) );
    }

    /**
     * Add custom status to order list
     */
    public function register_voided_post_status() {
        register_post_status( 'wc-voided', array(
            'label'                     => _x( 'Voided', 'Order status', 'woocommerce-gateway-wompi' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Voided <span class="count">(%s)</span>', 'Voided <span class="count">(%s)</span>', 'woocommerce-gateway-wompi' )
        ) );

    }

    /**
     * Add custom status to order page drop down
     */
    public function order_statuses( $order_statuses ) {
        $add_status = false;
        if ( WC_Wompi_Helper::is_webhook() ) {
            $add_status = true;
        } else {
            global $pagenow, $post;
            if ( $pagenow == 'edit.php' && $_GET['post_type'] == 'shop_order' ) {
                $add_status = true;
            } elseif ( $pagenow == 'post.php' && is_object( $post ) && $post->post_type == 'shop_order' ) {
                $order = new WC_Order( $post->ID );
                $status = $order->get_status();
                if ( $status == 'voided' || self::check_voided_access( $order, $status ) ) {
                    $add_status = true;
                }
            }
        }
        if ( $add_status ) {
            $order_statuses['wc-voided'] = _x( 'Voided', 'Order status', 'woocommerce-gateway-wompi' );
        }

        return $order_statuses;
    }

    /**
     * Check allowing to change order to Voided status
     */
    public function check_voided_access( $order, $status ) {

        if ( $status != 'completed') {
            return false;
        }

        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        if ( get_post_meta( $order_id, WC_Wompi::FIELD_PAYMENT_METHOD_TYPE, true ) != WC_Wompi_API::PAYMENT_TYPE_CARD ) {
            return false;
        }

        $time_diff = current_time('timestamp') - $order->get_date_completed()->getOffsetTimestamp();
        if ( $time_diff > self::VOIDED_EXPIRY ) {
            return false;
        }

        return true;
    }

    /**
     * On order update
     */
    public function process_shop_order_meta( $order_id ) {

        // Change order status to Voided
        if ( $_POST['order_status'] == 'wc-voided' ) {
            $order = new WC_Order( $order_id );
            $status = $order->get_status();

            if ( $status != 'completed' ||
                ! self::check_voided_access( $order, $status ) ||
                ! $this->order_void( $order )
            ) {
                $order->add_order_note( __( 'Unable to change status to Voided.', 'woocommerce-gateway-wompi' ) );
                WC_Wompi_Logger::log( 'Unable to change status to Voided.' );
                $_POST['order_status'] = 'wc-' . $status;
            }
        }
    }

    /**
     * On order void
     */
    public function order_void( $order ) {

        // API Void
        if ( WC_Wompi_API::instance()->transaction_void( $order->get_transaction_id() ) ) {

            // Gateway process
            WC_Gateway_Wompi::process_void( $order );

            return true;
        }

        return false;
    }
}

new WC_Wompi_Order_Statuses();