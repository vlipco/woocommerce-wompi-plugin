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
     * Plugins directory
     */
    public $plugins_dir;

    /**
     * Testing directory
     */
    public $tests_dir;

	/**
     * Directory where wordpress-tests-lib
     */
	public $wp_tests_dir;

	/**
	 * Setup the unit testing environment
	 */
	public function __construct() {

		ini_set( 'display_errors', 'on' );
		error_reporting( E_ALL );

        $_SERVER['SERVER_NAME'] = 'localhost';

        $this->plugins_dir  = dirname( WOMPI_UNIT_TESTS_DIR );
		$this->tests_dir    = WOMPI_UNIT_TESTS_DIR . '/tests';
		$this->wp_tests_dir = WOMPI_UNIT_TESTS_DIR . '/wp';

        // Load test function so tests_add_filter() is available
        require_once $this->wp_tests_dir . '/includes/functions.php';

        // Load plugins
        tests_add_filter( 'muplugins_loaded', array( $this, 'plugins_loaded' ) );

        // Install WC
        tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );

        // Load the WP testing environment
        require_once $this->wp_tests_dir . '/includes/bootstrap.php';

        /**
         * Woocommerce Helpers
         */
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-product.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-coupon.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-fee.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-shipping.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-customer.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-order.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-shipping-zones.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-payment-token.php';
        require_once WOMPI_UNIT_TESTS_DIR . '/wc-framework/helpers/class-wc-helper-settings.php';

        /**
         * Load Wompi testing classes
         */
        require_once $this->tests_dir . '/includes/class-test-wompi-data.php';
        require_once $this->tests_dir . '/includes/test-class-wc-wompi-webhook-handler.php';
	}

	/**
	 * Load plugins
	 */
	public function plugins_loaded() {

	    // WooCommerce
		define( 'WC_USE_TRANSACTIONS', false );
        require_once $this->plugins_dir . '/woocommerce/woocommerce.php';

        // WooCommerce Wompi Gateway
        require_once $this->plugins_dir . '/woocommerce-gateway-wompi/woocommerce-gateway-wompi.php';
        require_once WC_WOMPI_PLUGIN_PATH . '/includes/class-wc-wompi.php';
        WC_Wompi::instance();
	}

    /**
     * Install WooCommerce after the test environment and WC have been loaded.
     */
    public function install_wc() {

        // Clean existing install first.
        define( 'WP_UNINSTALL_PLUGIN', true );
        define( 'WC_REMOVE_ALL_DATA', true );
        include $this->plugins_dir . '/woocommerce/uninstall.php';

        echo 'Installing WooCommerce database tables...' . PHP_EOL;
        WC_Install::install();

        // Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
        if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
            $GLOBALS['wp_roles']->reinit();
        } else {
            $GLOBALS['wp_roles'] = null;
            wp_roles();
        }
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