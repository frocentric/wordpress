<?php
/**
 * The Secondary Nav module.
 *
 * @since 1.0.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'GENERATE_SECONDARY_NAV_VERSION' ) ) {
	define( 'GENERATE_SECONDARY_NAV_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';

// Include secondary navigation color fields.
require plugin_dir_path( __FILE__ ) . 'fields/secondary-navigation.php';
