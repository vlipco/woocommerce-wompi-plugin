<?php

use PHPUnit\Framework\TestCase;
/**
 * Test Webhook Handler Class
 */
class Test_WC_Wompi_Webhook_Handler extends WC_Wompi_Webhook_Handler {

    public $response;

    /**
     * Check incoming requests for Wompi Webhook data and process them
     */
    public function check_for_webhook() {

        if ( ! WC_Wompi_Helper::is_webhook(true) ) {
            return false;
        }

        $this->response = Test_Wompi_Data::response();
        TestCase::assertIsObject( $this->response );

        $this->process_webhook( $this->response );
    }

    /**
     * Process the payment
     */
    public function process_webhook_payment( $response ) {
        $data = $response->data;
        // Validate transaction response
        if ( isset( $data->transaction ) ) {
            $transaction = $data->transaction;
            $order = new WC_Order();
            if( $this->is_payment_valid( $order, $transaction ) ) {
                // Apply transaction status
                $this->apply_status( $order, $transaction );

                // Check order status
                $status = $order->get_status();
                if( $status == 'completed' ) {

                    // Fix for setting order id
                    $order->set_id( $transaction->reference );

                    // Parse full name
                    $full_name = WC_Wompi_Helper::split_fullname( $transaction->customer_data->full_name );
                    // Check transaction id
                    TestCase::assertEquals( $transaction->id, $order->get_transaction_id() );
                    // Check customer email
                    TestCase::assertEquals( $transaction->customer_email, $order->get_meta( '_billing_email' ) );
                    // Check first name
                    TestCase::assertEquals( $full_name[0], $order->get_meta( '_billing_first_name' ) );
                    // Check last name
                    TestCase::assertEquals( $full_name[1], $order->get_meta( '_billing_last_name' ) );
                    // Check phone number
                    TestCase::assertEquals( $transaction->customer_data->phone_number, $order->get_meta( '_billing_phone' ) );
                } else {
                    TestCase::assertTrue( false, 'Order status: ' . $status );
                }
            } else {
                TestCase::assertTrue( false, 'Wompi payment validation is invalid' );
            }
        } else {
            TestCase::assertTrue( false, 'TRANSACTION Response Not Found' );
        }
    }

    /**
     * Validate transaction response
     */
    protected function is_payment_valid( $order, $transaction ) {

        /**
         * Set test values
         */
        $order->set_payment_method( 'wompi' );
        $order->set_total( $transaction->amount_in_cents/1000 );

        if ( $order === false ) {
            return false;
        }
        if ( $order->get_payment_method() != 'wompi' ) {
            return false;
        }
        $amount = $order->get_total() * 1000;
        if ( $transaction->amount_in_cents != $amount ) {
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
                $this->update_order_data( $order, $transaction );
                // Fix for payment_complete
                $this->payment_complete( $order, $transaction );
                $this->update_transaction_status( $order, __('Wompi payment APPROVED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'completed' );
                break;
            case 'VOIDED' :
                // Fix for update status
                $order->set_id( $transaction->reference );
                $this->update_transaction_status( $order, __('Wompi payment VOIDED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'voided' );
                break;
            case 'DECLINED' :
                // Fix for update status
                $order->set_id( $transaction->reference );
                $this->update_transaction_status( $order, __('Wompi payment DECLINED. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'cancelled' );
                break;
            default : // ERROR
                // Fix for update status
                $order->set_id( $transaction->reference );
                $this->update_transaction_status( $order, __('Wompi payment ERROR. TRANSACTION ID: ', 'woocommerce-gateway-wompi') . ' (' . $transaction->id . ')', 'failed' );
        }
    }

    /**
     * Payment complete
     */
    public function payment_complete( $order, $transaction ) {
        $order->set_transaction_id( $transaction->id );
        $order->set_date_paid( current_time( 'timestamp', true ) );
        $order->set_status( apply_filters( 'woocommerce_payment_complete_order_status', $order->needs_processing() ? 'processing' : 'completed', $order->get_id(), $order ) );
    }
}

new Test_WC_Wompi_Webhook_Handler();