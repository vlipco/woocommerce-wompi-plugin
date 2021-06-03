<?php

use PHPUnit\Framework\TestCase;
/**
 * Test Webhook Handler Class
 */
class Test_WC_Wompi_Webhook_Handler extends WC_Wompi_Webhook_Handler {

    /**
     * Check incoming requests for Wompi Webhook data and process them
     */
    public function check_for_webhook() {

        if ( ! WC_Wompi_Helper::is_webhook(true) ) {
            TestCase::assertTrue( false, 'is_webhook invalid' );
            return false;
        }

        // Emulate response reference
        WC_Wompi_Tests::$response->data->transaction->reference = WC_Wompi_Tests::$test_order->get_id();

        $this->process_webhook( WC_Wompi_Tests::$response );
    }

    /**
     * Check order data
     */
    public function check_order_data() {
        $transaction = WC_Wompi_Tests::$response->data->transaction;
        $order_id = WC_Wompi_Tests::$test_order->get_id();
        $order = new WC_Order( $order_id );

        // Check order status
        TestCase::assertTrue( $order->is_paid() );
        // Check transaction id
        TestCase::assertEquals( $transaction->id, $order->get_transaction_id() );
        // Check customer email
        TestCase::assertEquals( $transaction->customer_email, get_post_meta($order_id, '_billing_email', true) );
        // Check first name
        if ( property_exists($transaction, 'customer_data') && property_exists($transaction->customer_data, 'full_name')) {
            TestCase::assertEquals($transaction->customer_data->full_name, get_post_meta($order_id, '_billing_first_name', true));
        }
        // Check last name
        TestCase::assertEquals( '', get_post_meta($order_id, '_billing_last_name', true) );
        // Check phone number
        if ( property_exists($transaction, 'customer_data') && property_exists($transaction->customer_data, 'phone_number')) {
            TestCase::assertEquals($transaction->customer_data->phone_number, get_post_meta($order_id, '_billing_phone', true));
        }
    }

    /**
     * Check no changes in order data
     */
    public function check_no_change_order_data() {
        $transaction = WC_Wompi_Tests::$response->data->transaction;
        $order_id = WC_Wompi_Tests::$test_order->get_id();
        $order = new WC_Order( $order_id );

		// Check transaction id
		TestCase::assertNotEquals( $transaction->id, $order->get_transaction_id() );
		// Check transaction status
		TestCase::assertFalse( $order->is_paid() );
    }
}
