<?php
/*
Add-on Name: Generate Menu Plus
Author: Thomas Usborne
Author URI: http://edge22.com
*/

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the version
if ( ! defined( 'GENERATE_MENU_PLUS_VERSION' ) ) {
	define( 'GENERATE_MENU_PLUS_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone add-on and GP Premium
require plugin_dir_path( __FILE__ ) . 'functions/generate-menu-plus.php';

// Set up language files
if ( ! function_exists( 'generate_menu_plus_init' ) ) {
	add_action( 'plugins_loaded', 'generate_menu_plus_init' );

	function generate_menu_plus_init() {
		load_plugin_textdomain( 'menu-plus', false, 'gp-premium/langs/menu-plus/' );
	}
}
