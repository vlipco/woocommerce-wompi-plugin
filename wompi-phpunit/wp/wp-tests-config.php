<?php

$abspath = str_replace( 'wp-content\plugins\wompi-phpunit\wp', '', dirname( __FILE__ ));
define( 'ABSPATH', $abspath );

define( 'WP_DEFAULT_THEME', 'default' );
define( 'WP_DEBUG', true );

// ** MySQL settings ** //
// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.
// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.
define( 'DB_NAME', 'phpunit' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );