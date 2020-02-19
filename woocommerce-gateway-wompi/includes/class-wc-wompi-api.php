<?php
defined( 'ABSPATH' ) || exit;

/**
 * Communicates with Wompi API
 */
class WC_Wompi_API {

    /**
     * Define API constants
     */
    const ENDPOINT = 'https://production.wompi.co/v1';
    const ENDPOINT_TEST = 'https://sandbox.wompi.co/v1';
    const EVENT_TRANSACTION_UPDATED = 'transaction.updated';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_VOIDED = 'VOIDED';
    const PAYMENT_TYPE_CARD = 'CARD';

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
            $this->endpoint = self::ENDPOINT_TEST;
            $this->public_key = $options['test_public_key'];
            $this->private_key = $options['test_private_key'];
        } else {
            $this->endpoint = self::ENDPOINT;
            $this->public_key = $options['public_key'];
            $this->private_key = $options['private_key'];
        }
    }

    /**
     * Getter
     */
    public function __get( $name ) {
        if ( property_exists( $this, $name ) ) {
            return $this->$name;
        }
    }

	/**
	 * Generates the headers to pass to API request
	 */
    private function get_headers( $use_secret ) {
        $headers = array();

        if ( $use_secret ) {
            $headers['Authorization'] = 'Bearer ' . $this->private_key;
        }

		return $headers;
	}

	/**
	 * Send the request to Wompi's API
	 */
	public function request( $method, $request, $data = null, $use_secret = false ) {
		WC_Wompi_Logger::log( "==== REQUEST ============================== Start Log ==== \n REQUEST URL: " . $method . ' ' . $this->endpoint . $request . "\n", false );
		if ( ! is_null( $data ) ) {
            WC_Wompi_Logger::log( 'REQUEST DATA: ' . print_r( $data, true ), false );
        }

        $headers = $this->get_headers( $use_secret );

		$params = array(
            'method'  => $method,
            'headers' => $headers,
            'body'    => $data,
        );

        // Exclude private key from logs
        if ( 'yes' === WC_Wompi::$settings['logging'] && ! empty( $headers ) ) {
            $strlen = strlen( $this->private_key );
            $headers['Authorization'] = 'Bearer ' . ( ! empty( $strlen ) ? str_repeat( 'X', $strlen ) : '' );
            WC_Wompi_Logger::log( 'REQUEST HEADERS: ' . print_r( $headers, true ), false );
        }

		$response = wp_safe_remote_post( $this->endpoint . $request, $params );
        WC_Wompi_Logger::log( 'REQUEST RESPONSE: ' . print_r( $response, true ), false );

		if ( is_wp_error( $response ) ) {
			return false;
		}

        return json_decode( $response['body'] );
	}

    /**
     * Transaction void
     */
	public function transaction_void( $transaction_id ) {
        $response = $this->request( 'POST', '/transactions/' . $transaction_id . '/void', null, true );
        return $response->data->transaction->status == self::STATUS_APPROVED ? true : false;
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
