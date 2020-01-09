<?php
defined( 'ABSPATH' ) || exit;

/**
 * Webhook Handler Class
 */
class WC_Wompi_Webhook_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_api_wc_wompi', array( $this, 'check_for_webhook' ) );
	}

	/**
	 * Check incoming requests for Wompi Webhook data and process them
	 */
	public function check_for_webhook() {

		if ( ! WC_Wompi_Helper::is_webhook(true) ) {
			return false;
		}

        $response = json_decode( file_get_contents('php://input') );
        if ( is_object( $response ) ) {
            WC_Wompi_Logger::log( 'Webhook response: ' . print_r( $response, true ) );
            $this->process_webhook( $response );
        } else {
            WC_Wompi_Logger::log( 'Response ERROR' );
            status_header( 400 );
        }
	}

	/**
	 * Processes the incoming webhook
	 */
	public function process_webhook( $response ) {
        // Check transaction event
		switch ( $response->event ) {
			case 'transaction.updated':
				$this->process_webhook_payment( $response );
				break;
            default :
                WC_Wompi_Logger::log( 'TRANSACTION Event Not Found' );
                status_header( 400 );
		}
	}

    /**
     * Process the payment
     */
	public function process_webhook_payment( $response ) {
        $data = $response->data;
        // Validate transaction response
        if ( isset( $data->transaction ) ) {
            $transaction = $data->transaction;
            $order = new WC_Order( $transaction->reference );
            if( $this->is_payment_valid( $order, $transaction ) ) {
                // Update order data
                $this->update_order_data( $order, $transaction );
                $this->apply_status( $order, $transaction );
                status_header( 200 );
            } else {
                $this->update_transaction_status( $order, __('Wompi payment validation is invalid. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'failed' );
                status_header( 400 );
            }
        } else {
            WC_Wompi_Logger::log( 'TRANSACTION Response Not Found' );
            status_header( 400 );
        }
    }

    /**
     * Validate transaction response
     */
    protected function is_payment_valid( $order, $transaction ) {
        if ( $order === false ) {
            WC_Wompi_Logger::log( 'Order Not Found' . ' TRANSACTION ID: ' . $transaction->id );
            return false;
        }
        if ( $order->get_payment_method() != 'wompi' ) {
            WC_Wompi_Logger::log( 'Payment method incorrect' . ' TRANSACTION ID: ' . $transaction->id . ' ORDER ID: ' . $order->get_id() . ' PAYMENT METHOD: ' . $order->get_payment_method() );
            return false;
        }
        $amount = WC_Wompi_Helper::get_amount_in_cents( $order->get_total() );
        if ( $transaction->amount_in_cents != $amount ) {
            WC_Wompi_Logger::log( 'Amount incorrect' . ' TRANSACTION ID: ' . $transaction->id . ' ORDER ID: ' . $order->get_id() . ' AMOUNT: ' . $amount );
            return false;
        }

        return true;
    }

    /**
     * Apply transaction status
     */
    public function apply_status( $order, $transaction ) {
        switch( $transaction->status ) {
            case 'APPROVED' :
                $order->payment_complete( $transaction->id );
                $this->update_transaction_status( $order, __('Wompi payment APPROVED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'completed' );
                break;
            case 'VOIDED' :
                $this->update_transaction_status( $order, __('Wompi payment VOIDED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'voided' );
                break;
            case 'DECLINED' :
                $this->update_transaction_status( $order, __('Wompi payment DECLINED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'cancelled' );
                break;
            default : // ERROR
                $this->update_transaction_status( $order, __('Wompi payment ERROR. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'failed' );
        }
    }

    /**
     * Update order data
     */
    public function update_order_data( $order, $transaction ) {
        // Check if order data was set
        if ( ! $order->get_transaction_id() ) {
            // Set transaction id
            $order->update_meta_data( '_transaction_id', $transaction->id );
            // Set customer email
            $order->update_meta_data( '_billing_email', $transaction->customer_email );
            // Parse full name
            $full_name = WC_Wompi_Helper::split_fullname( $transaction->customer_data->full_name );
            // Set first name
            $order->update_meta_data( '_billing_first_name', $full_name[0] );
            // Set last name
            $order->update_meta_data( '_billing_last_name', $full_name[1] );
            // Set phone number
            $order->update_meta_data( '_billing_phone', $transaction->customer_data->phone_number );
        }
    }

    /**
     * Update transaction status
     */
    public function update_transaction_status( $order, $note, $status ) {
        $order->add_order_note( $note );
        $order->update_status( $status );
    }
}

new WC_Wompi_Webhook_Handler();
