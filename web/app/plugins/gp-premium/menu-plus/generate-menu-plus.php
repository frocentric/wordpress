<?php
/**
 * The Menu Plus module.
 *
 * @since 1.0.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'GENERATE_MENU_PLUS_VERSION' ) ) {
	define( 'GENERATE_MENU_PLUS_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone add-on and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/generate-menu-plus.php';
require plugin_dir_path( __FILE__ ) . 'fields/slideout-nav-colors.php';
