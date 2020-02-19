<?php

$config_file_path = dirname( dirname( __FILE__ ) ) . '/wp-tests-config.php';

/*
 * Globalize some WordPress variables, because PHPUnit loads this file inside a function.
 * See: https://github.com/sebastianbergmann/phpunit/issues/325
 */
global $wpdb, $current_site, $current_blog, $wp_rewrite, $shortcode_tags, $wp, $phpmailer, $wp_theme_directories;

if ( ! is_readable( $config_file_path ) ) {
	echo "ERROR: wp-tests-config.php is missing!\n";
	exit( 1 );
}
require_once $config_file_path;

if ( version_compare( tests_get_phpunit_version(), '8.0', '>=' ) ) {
    printf(
        "ERROR: Looks like you're using PHPUnit %s. WordPress is currently only compatible with PHPUnit up to 7.x.\n",
        tests_get_phpunit_version()
    );
    echo "Please use the latest PHPUnit version from the 7.x branch.\n";
    exit( 1 );
}

tests_reset__SERVER();

define( 'DISABLE_WP_CRON', true );
define( 'WP_MEMORY_LIMIT', -1 );
define( 'WP_MAX_MEMORY_LIMIT', -1 );

$PHP_SELF            = '/index.php';
$GLOBALS['PHP_SELF'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Should we run in multisite mode?
$multisite = ( '1' === getenv( 'WP_MULTISITE' ) );
$multisite = $multisite || ( defined( 'WP_TESTS_MULTISITE' ) && WP_TESTS_MULTISITE );
$multisite = $multisite || ( defined( 'MULTISITE' ) && MULTISITE );

if ( '1' !== getenv( 'WP_TESTS_SKIP_INSTALL' ) ) {
    system( WP_PHP_BINARY . ' ' . escapeshellarg( dirname( __FILE__ ) . '/install.php' ) . ' ' . escapeshellarg( $config_file_path ) . ' ' . $multisite, $retval );
    if ( 0 !== $retval ) {
        exit( $retval );
    }
}

$GLOBALS['_wp_die_disabled'] = false;

// Load WordPress
require_once ABSPATH . '/wp-settings.php';

// Delete any default posts & related data
//_delete_all_posts();
_delete_all_data();
