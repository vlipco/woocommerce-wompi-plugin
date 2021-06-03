<?php
/**
 * Emulate Testing Data
 */
class Test_Wompi_Data {

    /**
     * Emulate response
     */
    public static function response() {
	    $tests_dir  = dirname( __FILE__ );
	    $plugin_dir = dirname( dirname( $tests_dir ) );

	    $response = file_get_contents( $plugin_dir . '/unit-tests/responses/response.txt' );
        return json_decode( $response );

    }

	/**
	 * Emulate response without customer_data key
	 */
	public static function noCustomerDataResponse() {
		$tests_dir  = dirname( __FILE__ );
		$plugin_dir = dirname( dirname( $tests_dir ) );

		$response = file_get_contents( $plugin_dir . '/unit-tests/responses/response_no_customer_data.txt' );
		return json_decode( $response );

	}

	/**
	 * Emulate response with a invalid checksum
	 */
	public static function invalidChecksumResponse() {
		$tests_dir  = dirname( __FILE__ );
		$plugin_dir = dirname( dirname( $tests_dir ) );

		$response = file_get_contents( $plugin_dir . '/unit-tests/responses/response_invalid_checksum.txt' );
		return json_decode( $response );

	}
}
