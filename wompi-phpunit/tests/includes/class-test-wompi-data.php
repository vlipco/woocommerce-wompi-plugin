<?php
/**
 * Emulate Testing Data
 */
class Test_Wompi_Data {

    /**
     * Emulate response
     */
    public static function response() {

        $response = file_get_contents(WOMPI_UNIT_TESTS_DIR . '/tests/responses/response.txt');
        return json_decode( $response );

        /*return self::convert_response( array(
            'event' => 'transaction.updated',
            'data' => array(
                'transaction' => array(
                    'id' => '11128-1576800783-87365',
                    'created_at' => '2019-12-20T00:13:03.703Z',
                    'amount_in_cents' => 4950000,
                    'reference' => 'xxxxzzzz2',
                    'customer_email' => 'test@gmail.com',
                    'currency' => 'COP',
                    'payment_method_type' => 'CARD',
                    'payment_method' => array(
                        'type' => 'CARD',
                        'extra' => array(
                            'bin' => 424242,
                            'name' => 'VISA-4242',
                            'brand' => 'VISA',
                            'exp_year' => 20,
                            'exp_month' => 10,
                            'last_four' => 4242,
                            'external_identifier' => 'rWjjUppCQJ'
                        ),
                        'token' => 'tok_test_1128_D536949b1e0ce67A74d3Fe85191B4324',
                        'installments' => 1
                    ),
                    'status' => 'APPROVED',
                    'status_message' => '',
                    'shipping_address' => '',
                    'redirect_url' => '',
                    'payment_source_id' => '',
                    'payment_link_id' => '',
                    'customer_data' => array(
                        'full_name' => 'Name Surname',
                        'phone_number' => '+5711111'
                    ),
                    'bill_id' => ''
                )
            ),
            'sent_at' => '2019-12-20T00:13:04Z'
        ) );*/
    }

    /**
     * Convert response
     */
    /*public static function convert_response( $response ) {
        return json_decode( json_encode( $response ) );
    }*/
}