<?php
/**
 * Unit tests for webhook
 */
use PHPUnit\Framework\TestCase;
class WC_Wompi_Tests extends TestCase {

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
        self::$test_order = $order;

        // Generate response
        self::$response = Test_Wompi_Data::response();
        TestCase::assertIsObject( self::$response );
    }

    /**
     * Test for Check incoming requests for Wompi Webhook data and process them
     */
    public function test_check_for_webhook() {
        $webhook_handler = new Test_WC_Wompi_Webhook_Handler();
        $result = $webhook_handler->check_for_webhook();
        $this->assertNull( $result );

        $webhook_handler->check_order_data();
    }
}

