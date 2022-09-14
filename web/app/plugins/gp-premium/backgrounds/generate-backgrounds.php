<?php
/**
 * Backgrounds module.
 *
 * @since 1.1.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version. This used to be a standalone plugin, so we need to keep this constant.
if ( ! defined( 'GENERATE_BACKGROUNDS_VERSION' ) ) {
	define( 'GENERATE_BACKGROUNDS_VERSION', GP_PREMIUM_VERSION );
}

require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
