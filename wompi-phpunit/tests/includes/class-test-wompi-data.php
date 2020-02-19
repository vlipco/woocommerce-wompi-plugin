<?php
/**
 * Emulate Testing Data
 */
class Test_Wompi_Data {

    /**
     * Emulate response
     */
    public static function response() {

        $response = file_get_contents( WOMPI_UNIT_TESTS_DIR . '/tests/responses/response.txt' );
        return json_decode( $response );

    }
}