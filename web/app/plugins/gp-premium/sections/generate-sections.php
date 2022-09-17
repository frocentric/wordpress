<?php
/**
 * The Sections module.
 *
 * @since 1.0.0
 * @deprecated 2.0.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'GENERATE_SECTIONS_VERSION' ) ) {
	define( 'GENERATE_SECTIONS_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/generate-sections.php';
