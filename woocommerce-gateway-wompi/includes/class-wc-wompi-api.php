<?php
defined( 'ABSPATH' ) || exit;

/**
 * Communicates with Wompi API
 */
class WC_Wompi_API {

    /**
     * Define API endpoints
     */
    const API_ENDPOINT = 'https://production.wompi.co/v1';
    const API_ENDPOINT_TEST = 'https://sandbox.wompi.co/v1';

    /**
     * The single instance of the class
     */
    protected static $_instance = null;

    /**
     * API endpoint
     */
    private $endpoint = '';

    /**
     * Public API Key
     */
    private $public_key = '';

	/**
	 * Private API Key
	 */
	private $private_key = '';

    /**
     * Supported currency
     */
    private $supported_currency = array();

    /**
     * Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {

        $options = WC_Wompi::$settings;

        if ( 'yes' === $options['testmode'] ) {
            $this->endpoint = self::API_ENDPOINT_TEST;
            $this->public_key = $options['test_public_key'];
            $this->private_key = $options['test_private_key'];
        } else {
            $this->endpoint = self::API_ENDPOINT;
            $this->public_key = $options['public_key'];
            $this->private_key = $options['private_key'];
        }

        // Get supported currency
        $this->supported_currency = $this->get_merchant_data('accepted_currencies');
    }

    /**
     * Get API endpoint
     */
    public function get_endpoint() {
        return $this->endpoint;
    }

    /**
     * Get public key
     */
    public function get_public_key() {
        return $this->public_key;
    }

	/**
	 * Get private key
	 */
    public function get_private_key() {
        return $this->private_key;
	}

    /**
     * Get supported currency
     */
    public function get_supported_currency() {
        return $this->supported_currency;
    }

	/**
	 * Generates the headers to pass to API request
	 */
    private function get_headers() {
		return array(
            'Authorization' => 'Bearer ' . $this->private_key,
        );
	}

	/**
	 * Send the request to Wompi's API
	 */
	public function request( $method, $request, $data = null, $use_headers = false ) {
		WC_Wompi_Logger::log( 'Request: ' . $this->endpoint . $request . ' Request data: ' . print_r( $data, true ) );

		$params = array(
            'method'  => $method,
            'body'    => $data,
        );

		if ( $use_headers ) {
            $headers         = $this->get_headers();
            $params['headers'] = $headers;
            WC_Wompi_Logger::log( 'Headers: ' . print_r( $headers, true ) );
        }

		$response = wp_safe_remote_post( $this->endpoint . $request, $params );

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			WC_Wompi_Logger::log( 'ERROR Response: ' . print_r( $response, true ) );

			return false;
		}

		return json_decode( $response['body'] );
	}

    /**
     * Transaction void
     */
	public function transaction_void( $transaction_id ) {
        return $this->request( 'POST', '/transactions/' . $transaction_id . '/void', null, true );
    }

    /**
     * Get merchant data
     */
    public function get_merchant_data( $type ) {
        $response = $this->request( 'GET', '/merchants/' . $this->public_key  );
        if ( isset( $response->data ) && is_object( $response->data ) ) {
            $data = $response->data;
            switch ( $type ) {
                case 'accepted_currencies':
                    return ( isset( $data->accepted_currencies ) && is_array( $data->accepted_currencies ) ) ? $data->accepted_currencies : array();
                default:
                    return $data;
            }
        } else {
            return array();
        }
    }
}

WC_Wompi_API::instance();
