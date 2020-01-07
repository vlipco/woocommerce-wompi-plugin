<?php
defined( 'ABSPATH' ) || exit;

/**
 * Provides static methods as helpers
 */
class WC_Wompi_Helper {

    /**
     * Split customer fullname
     */
    public static function split_fullname( $name ) {
        $name = trim( $name );
        $last_name = ( strpos($name, ' ') === false ) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name );
        $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );

        return array( $first_name, $last_name );
    }

    /**
     * Check if current request is webhook
     */
    public static function is_webhook( $log = false) {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_GET['wc-api'] ) && $_GET['wc-api'] === 'wc_wompi' ) {
            return true;
        } else {
            if ( $log ) {
                WC_Wompi_Logger::log( 'Webhook checking error' );
            }
            return false;
        }
    }
}
