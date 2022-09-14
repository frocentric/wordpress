<?php
/**
 * Blog module.
 *
 * @since 1.1.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Define the version. This used to be a standalone plugin, so we need to keep this constant.
if ( ! defined( 'GENERATE_BLOG_VERSION' ) ) {
	define( 'GENERATE_BLOG_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium.
require plugin_dir_path( __FILE__ ) . 'functions/generate-blog.php';
