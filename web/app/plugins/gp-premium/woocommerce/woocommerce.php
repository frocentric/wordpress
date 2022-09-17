<?php
/**
 * The WooCommerce module.
 *
 * @since 1.3.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
define( 'GENERATE_WOOCOMMERCE_VERSION', GP_PREMIUM_VERSION );

// Include functions identical between standalone addon and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
require plugin_dir_path( __FILE__ ) . 'fields/woocommerce-colors.php';
