<?php
/**
 * PHPUnit bootstrap file
 *
 * @package woocommerce-gateway-wompi
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	//default_option_woocommerce_wompi_settings
	add_filter( 'default_option_woocommerce_wompi_settings', function (){
		return [
			"enabled" => "yes",
			"title" => "Wompi",
			"description" => "Pay via Wompi gateway.",
			"webhook" => "",
			"testmode" => "yes",
			"test_public_key" => "pub_test_XXXXXXXXXXXX",
			"test_private_key" => "prv_test_XXXXXXXXXXXX",
			"test_event_secret_key" => "test_events_XXXXXXXXXXXX",
			"public_key" => "pub_prod_XXXXXXXXXXXX",
			"private_key" => "prv_prod_XXXXXXXXXXXX",
			"event_secret_key" => "prod_events_XXXXXXXXXXXX",
			"logging" => "yes",
		];
	});
    //load woocommerce-gateway-wompi
	require dirname( dirname( __FILE__ ) ) . '/woocommerce-gateway-wompi.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Find the path to the WooCommerce core test bootstrap file.
$_bootstrap = dirname( __DIR__ ) . '/vendor/woocommerce/woocommerce/tests/legacy/bootstrap.php';

// Verify that Composer dependencies have been installed.
if ( ! file_exists( $_bootstrap ) ) {
	echo "Unable to find the WooCommerce test bootstrap file. Have you run `composer install`?";
	exit( 1 );

}
require_once $_bootstrap;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
