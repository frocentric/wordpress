<?php
// don't load directly 
if ( !defined('ABSPATH') || !defined('WP_ADMIN') ) {
	return;
}
add_filter( 'wpematico_plugins_updater_args', 'plugin_updater_wpematico_pro', 10, 1);
function plugin_updater_wpematico_pro($args) {
	if (empty($args['pro_licenser'])) {
		$args['pro_licenser'] = array();
		$args['pro_licenser']['api_url'] = 'https://etruel.com';
		$args['pro_licenser']['plugin_file'] = WPeMaticoPRO::$dir.'wpematicopro.php';
		$args['pro_licenser']['api_data'] = array(
			'version' 	=> WPEMATICOPRO_VERSION, 				// current version number
			'item_name' => 'WPeMatico Professional', 	// name of this plugin
			'author' 	=> 'Esteban Truelsegaard'  // author of this plugin
		);
					
	}
	return $args;
}


?>
