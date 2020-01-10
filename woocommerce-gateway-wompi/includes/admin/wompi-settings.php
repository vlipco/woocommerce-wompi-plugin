<?php
defined( 'ABSPATH' ) || exit;

return apply_filters(
	'wc_wompi_settings',
	array(
		'enabled' => array(
			'title'       => __( 'Enable/Disable', 'woocommerce-gateway-wompi' ),
			'label'       => __( 'Enable Wompi', 'woocommerce-gateway-wompi' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no',
		),
		'title' => array(
			'title'       => __( 'Title', 'woocommerce-gateway-wompi' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-wompi' ),
			'default'     => 'Wompi',
			'desc_tip'    => true,
		),
		'description' => array(
			'title'       => __( 'Description', 'woocommerce-gateway-wompi' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-wompi' ),
			'default'     => __( 'Pay via Wompi gateway.', 'woocommerce-gateway-wompi' ),
			'desc_tip'    => true,
		),
		'webhook' => array(
			'title'       => __( 'Webhook Endpoints', 'woocommerce-gateway-wompi' ),
			'type'        => 'title',
			'description' => sprintf( __( 'You must add the following webhook endpoint <strong class="wc_wompi-webhook-link">&nbsp;%s&nbsp;</strong> to your <a href="https://comercios.wompi.co/my-account" target="_blank">Wompi account settings</a>.', 'woocommerce-gateway-wompi' ), add_query_arg( 'wc-api', 'wc_wompi', trailingslashit( get_home_url() ) ) ),
		),
		'testmode' => array(
			'title'       => __( 'Test mode', 'woocommerce-gateway-wompi' ),
			'label'       => __( 'Enable Test Mode', 'woocommerce-gateway-wompi' ),
			'type'        => 'checkbox',
			'description' => __( 'Place the payment gateway in test mode using test API keys.', 'woocommerce-gateway-wompi' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		),
		'test_public_key' => array(
			'title'       => __( 'Test Public Key', 'woocommerce-gateway-wompi' ),
			'type'        => 'text',
			'description' => __( 'Get your API keys from your Wompi account.', 'woocommerce-gateway-wompi' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'test_private_key' => array(
			'title'       => __( 'Test Private Key', 'woocommerce-gateway-wompi' ),
			'type'        => 'password',
			'description' => __( 'Get your API keys from your Wompi account.', 'woocommerce-gateway-wompi' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'public_key' => array(
			'title'       => __( 'Live Public Key', 'woocommerce-gateway-wompi' ),
			'type'        => 'text',
			'description' => __( 'Get your API keys from your Wompi account.', 'woocommerce-gateway-wompi' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'private_key' => array(
			'title'       => __( 'Live Private Key', 'woocommerce-gateway-wompi' ),
			'type'        => 'password',
			'description' => __( 'Get your API keys from your Wompi account.', 'woocommerce-gateway-wompi' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'logging' => array(
			'title'       => __( 'Logging', 'woocommerce-gateway-wompi' ),
			'label'       => __( 'Log debug messages', 'woocommerce-gateway-wompi' ),
			'type'        => 'checkbox',
			'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-gateway-wompi' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
	)
);
