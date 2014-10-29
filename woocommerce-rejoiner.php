<?php
/*
Plugin Name: WooCommerce Rejoiner
Plugin URI: http://jacksonwhelan.com/woocommerce-rejoiner/
Description: Integrates Rejoiner with WooCommerce.
Author: Jackson Whelan
Author URI: http://www.jacksonwhelan.com
Version: 1.1
*/

// Add the integration to WooCommerce

function wc_rejoiner( $integrations ) {
	global $woocommerce;

	if ( is_object( $woocommerce ) && version_compare( $woocommerce->version, '2.1-beta-1', '>=' ) ) {
		include_once( 'includes/class-wc-rejoiner.php' );
		$integrations[] = 'WC_Rejoiner';
	}

	return $integrations;
}

add_filter( 'woocommerce_integrations', 'wc_rejoiner', 10 );