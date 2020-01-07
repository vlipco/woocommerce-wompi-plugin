<?php
defined( 'ABSPATH' ) || exit;

/**
 * Communicates with Wompi API.
 */
class WC_Wompi_API {

    /**
     * Define API endpoints
     */
    const API_ENDPOINT = '';
    const API_ENDPOINT_TEST = 'https://sandbox.wompi.co/v1';

    /**
     * API endpoint
     */
    private static $endpoint = '';

	/**
	 * Private API Key.
	 */
	private static $private_key = '';

	/**
	 * Set private API Key.
	 */
	public static function set_private_key( $private_key ) {
		self::$private_key = $private_key;
	}

	/**
	 * Get private key.
	 */
	public static function get_private_key() {
		if ( ! self::$private_key ) {
			$options = get_option( 'woocommerce_wompi_settings' );

			if ( isset( $options['testmode'], $options['private_key'], $options['test_private_key'] ) ) {
			    if ( 'yes' === $options['testmode'] ) {
                    self::set_private_key( $options['test_private_key'] );
                    self::set_endpoint( self::API_ENDPOINT_TEST );
                } else {
                    self::set_private_key( $options['private_key'] );
                    self::set_endpoint( self::API_ENDPOINT );
                }
			}
		}
		return self::$private_key;
	}

    /**
     * Set API endpoint
     */
    public static function set_endpoint( $endpoint ) {
        self::$endpoint = $endpoint;
    }

	/**
	 * Get API endpoint
	 */
	public static function get_endpoint() {
		return self::$endpoint;
	}

	/**
	 * Generates the headers to pass to API request.
	 */
	public static function get_headers() {
		return array(
            "Content-type" => "application/json;charset=UTF-8",
            'Authorization' => 'Bearer ' . self::get_private_key(),
        );
	}

	/**
	 * Send the request to Wompi's API
	 */
	public static function request( $request, $data = '', $method = 'POST' ) {
		WC_Wompi_Logger::log( 'Request: ' . self::$endpoint . $request . ' Request data: ' . print_r( $data, true ) );

		$headers         = self::get_headers();
		WC_Wompi_Logger::log( 'Headers: ' . print_r( $headers, true ) );

		$response = wp_safe_remote_post(
			self::$endpoint . $request,
			array(
				'method'  => $method,
				'headers' => $headers,
				'body'    => $data,
			)
		);

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			WC_Wompi_Logger::log( 'ERROR Response: ' . print_r( $response, true ) );

			return false;
		}

		return json_decode( $response['body'] );
	}

    /**
     * Transaction void
     */
	public static function transaction_void( $transaction_id ) {
        return self::request( '/transactions/' . $transaction_id . '/void' );
    }
}
