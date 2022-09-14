<?php
/**
 * The legacy hooks module.
 *
 * @since 1.0.0
 * @deprecated 1.7.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'GENERATE_HOOKS_VERSION' ) ) {
	define( 'GENERATE_HOOKS_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
