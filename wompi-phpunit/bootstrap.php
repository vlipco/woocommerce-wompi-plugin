<?php

/**
 * Wompi Unit Tests Bootstrap
 * PHPUnit 7.5.19
 */

define( 'WOMPI_UNIT_TESTS_DIR', dirname( __FILE__ ) );

class Wompi_Unit_Tests_Bootstrap {

	/**
     * Instance
     */
	protected static $instance = null;

	/**
     * Directory where wordpress-tests-lib
     */
	public $wp_tests_dir;

	/**
     * Testing directory
     */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;
	public $plugins_dir;

	/**
	 * Setup the unit testing environment
	 */
	public function __construct() {

		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );

        $this->plugins_dir   = dirname( WOMPI_UNIT_TESTS_DIR );
		$this->tests_dir    = WOMPI_UNIT_TESTS_DIR . '/tests';
		$this->wp_tests_dir = WOMPI_UNIT_TESTS_DIR . '/wp';

        // Load the WP testing environment
        require_once $this->wp_tests_dir . '/includes/bootstrap.php';

        /**
         * Load external plugins
         */
        $this->external_load();

        /**
         * Load Wompi testing classes
         */
        require_once $this->tests_dir . '/includes/class-test-wompi-data.php';
        require_once $this->tests_dir . '/includes/test-class-wc-wompi-webhook-handler.php';
	}

	/**
	 * Load external plugins
	 */
	public function external_load() {
		define( 'WC_USE_TRANSACTIONS', false );
        require_once $this->plugins_dir . '/woocommerce/woocommerce.php';
        require_once $this->plugins_dir . '/woocommerce-gateway-wompi/woocommerce-gateway-wompi.php';
        require_once WC_WOMPI_PLUGIN_PATH . '/includes/class-wc-wompi.php';
        WC_Wompi::instance();
	}

	/**
	 * Get the single class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Wompi_Unit_Tests_Bootstrap::instance();
