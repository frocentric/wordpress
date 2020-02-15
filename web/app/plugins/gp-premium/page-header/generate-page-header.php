<?php
/*
Addon Name: Generate Page Header
Author: Thomas Usborne
Author URI: http://edge22.com
*/

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the version
if ( ! defined( 'GENERATE_PAGE_HEADER_VERSION' ) ) {
	define( 'GENERATE_PAGE_HEADER_VERSION', GP_PREMIUM_VERSION );
}

if ( ! function_exists( 'generate_page_header_init' ) ) {
	add_action( 'plugins_loaded', 'generate_page_header_init' );

	function generate_page_header_init() {
		load_plugin_textdomain( 'page-header', false, 'gp-premium/langs/page-header/' );
	}
}

// Include assets unique to this addon
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
