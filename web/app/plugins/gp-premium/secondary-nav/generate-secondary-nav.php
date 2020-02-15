<?php
/*
Addon Name: Generate Secondary Nav
Author: Thomas Usborne
Author URI: http://edge22.com
*/

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the version
if ( ! defined( 'GENERATE_SECONDARY_NAV_VERSION' ) ) {
	define( 'GENERATE_SECONDARY_NAV_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
