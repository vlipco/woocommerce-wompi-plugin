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

define( 'DISABLE_WP_CRON', true );
define( 'WP_MEMORY_LIMIT', -1 );
define( 'WP_MAX_MEMORY_LIMIT', -1 );

// Load WordPress
require_once ABSPATH . '/wp-settings.php';


