<?php
/**
 * Unit tests for webhook
 */
use PHPUnit\Framework\TestCase;
class Tests_Gateway extends TestCase {

    /**
     * Set up
     */
    public function setUp() {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['wc-api'] = 'wc_wompi';
    }

    /**
     * Test for Check incoming requests for Wompi Webhook data and process them
     */
    public function test_check_for_webhook() {
        $webhook_handler = new Test_WC_Wompi_Webhook_Handler();
        $result = $webhook_handler->check_for_webhook();
        $this->assertNull( $result );
    }
}

