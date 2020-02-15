<?php
/*
Addon Name: Generate Copyright
Author: Thomas Usborne
Author URI: http://edge22.com
*/

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the version
if ( ! defined( 'GENERATE_COPYRIGHT_VERSION' ) ) {
	define( 'GENERATE_COPYRIGHT_VERSION', GP_PREMIUM_VERSION );
}

// Include functions identical between standalone addon and GP Premium
require plugin_dir_path( __FILE__ ) . 'functions/functions.php';
