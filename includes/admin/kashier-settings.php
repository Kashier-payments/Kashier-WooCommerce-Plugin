<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters(
	'wc_kashier_settings',
	array(
		'enabled'                       => array(
			'title'       => __( 'Enable/Disable', 'woocommerce-gateway-kashier' ),
			'label'       => __( 'Enable Kashier', 'woocommerce-gateway-kashier' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no',
		),
		'title'                         => array(
			'title'       => __( 'Title', 'woocommerce-gateway-kashier' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-kashier' ),
			'default'     => __( 'Credit Card (Kashier)', 'woocommerce-gateway-kashier' ),
			'desc_tip'    => true,
		),
		'description'                   => array(
			'title'       => __( 'Description', 'woocommerce-gateway-kashier' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-kashier' ),
			'default'     => __( 'Pay with your credit card via Kashier.', 'woocommerce-gateway-kashier' ),
			'desc_tip'    => true,
		),
		'testmode'                      => array(
			'title'       => __( 'Test mode', 'woocommerce-gateway-kashier' ),
			'label'       => __( 'Enable Test Mode', 'woocommerce-gateway-kashier' ),
			'type'        => 'checkbox',
			'description' => __( 'Place the payment gateway in test mode using test API keys.', 'woocommerce-gateway-kashier' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		),
        'merchant_id'                         => array(
            'title'       => __( 'Merchant ID', 'woocommerce-gateway-kashier' ),
            'type'        => 'text',
            'description' => __( 'Get your Merchant ID from your kashier account.', 'woocommerce-gateway-kashier' ),
            'default'     => '',
            'desc_tip'    => true,
        ),
        'test_api_key'          => array(
			'title'       => __( 'Test API Key', 'woocommerce-gateway-kashier' ),
			'type'        => 'password',
			'description' => __( 'Get your API keys from your kashier account.', 'woocommerce-gateway-kashier' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'api_key'               => array(
			'title'       => __( 'Live API Key', 'woocommerce-gateway-kashier' ),
			'type'        => 'password',
			'description' => __( 'Get your API keys from your kashier account.', 'woocommerce-gateway-kashier' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'saved_cards'                   => array(
			'title'       => __( 'Saved Cards', 'woocommerce-gateway-kashier' ),
			'label'       => __( 'Enable Payment via Saved Cards', 'woocommerce-gateway-kashier' ),
			'type'        => 'checkbox',
			'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Kashier servers, not on your store.', 'woocommerce-gateway-kashier' ),
			'default'     => 'yes',
			'desc_tip'    => true,
		),
		'logging'                       => array(
			'title'       => __( 'Logging', 'woocommerce-gateway-kashier' ),
			'label'       => __( 'Log debug messages', 'woocommerce-gateway-kashier' ),
			'type'        => 'checkbox',
			'description' => __( 'Save debug messages to the WooCommerce System Status log.', 'woocommerce-gateway-kashier' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
	)
);
