<?php

class WC_Wompi_Tests extends WC_Unit_Test_Case {

    /**
     * Order object
     */
    public static $test_order;

    /**
     * Response
     */
    public static $response;

    /**
     * Set up
     */
    public function setUp() {

        // Emulate environment
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['wc-api'] = 'wc_wompi';

        // Create order
        $order = WC_Helper_Order::create_order();
	    $payment_gateways = WC()->payment_gateways->payment_gateways();
	    $order->set_payment_method( $payment_gateways['wompi'] );
	    $order->set_billing_email('');
	    $order->set_billing_first_name('');
	    $order->set_billing_last_name('');
	    $order->set_billing_phone('');
	    $order->save();
        self::$test_order = $order;
    }

    /**
     * Test for Check incoming requests for Wompi Webhook data and process them
     */
    public function test_check_for_webhook() {
		// Generate response
		self::$response = Test_Wompi_Data::response();
		$this->assertIsObject( self::$response );
		//Process response
        $webhook_handler = new Test_WC_Wompi_Webhook_Handler();
        $result = $webhook_handler->check_for_webhook();
        $this->assertNull( $result );
		//CheckResponse
        $webhook_handler->check_order_data();
    }

    /**
     * Test for Check incoming requests without customer_data key
     */
    public function test_check_for_webhook_no_customer_data_key() {
		// Generate response
		self::$response = Test_Wompi_Data::noCustomerDataResponse();
		$this->assertIsObject( self::$response );

        $webhook_handler = new Test_WC_Wompi_Webhook_Handler();
        $result = $webhook_handler->check_for_webhook();
        $this->assertNull( $result );

		//CheckResponse
		$webhook_handler->check_order_data();
    }

    /**
     * Test for Check incoming requests with no valid checksum
     */
    public function test_check_for_webhook_invalid_checksum() {
		// Generate response
		self::$response = Test_Wompi_Data::invalidChecksumResponse();
		$this->assertIsObject( self::$response );

        $webhook_handler = new Test_WC_Wompi_Webhook_Handler();
        $result = $webhook_handler->check_for_webhook();
        $this->assertNull( $result );

		//CheckResponse
		$webhook_handler->check_no_change_order_data();
    }
}

