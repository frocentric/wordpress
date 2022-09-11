<?php
/**
 * The Page Header module.
 *
 * @since 1.1.0
 * @deprecated 1.7.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version.
if ( ! defined( 'GENERATE_PAGE_HEADER_VERSION' ) ) {
	define( 'GENERATE_PAGE_HEADER_VERSION', GP_PREMIUM_VERSION );
}

// Include assets unique to this addon.
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
