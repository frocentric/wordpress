<?php
/*
Plugin Name: The Events Calendar PRO
Description: The Events Calendar PRO, a premium add-on to the open source The Events Calendar plugin (required), enables recurring events, custom attributes, venue pages, new widgets and a host of other premium features.
Version: 5.1.2
Author: Modern Tribe, Inc.
Author URI: http://m.tri.be/20
Text Domain: tribe-events-calendar-pro
License: GPLv2 or later
*/

/*
Copyright 2010-2012 by Modern Tribe Inc and the contributors

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


define( 'EVENTS_CALENDAR_PRO_DIR', dirname( __FILE__ ) );
define( 'EVENTS_CALENDAR_PRO_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( EVENTS_CALENDAR_PRO_FILE ) . '/src/functions/php-min-version.php';

// Load Composer autoload file only if we've not included this file already.
require_once EVENTS_CALENDAR_PRO_DIR . '/vendor/autoload.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'tribe-events-calendar-pro', EVENTS_CALENDAR_PRO_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.6
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_events_pro_not_php_version_plugin_name( $names ) {
		$names['tribe-events-calendar-pro'] = esc_html__( 'Events Calendar Pro', 'tribe-events-calendar-pro' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_events_pro_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}
	return false;
}

// Register Plugin
add_action( 'tribe_common_loaded', 'tribe_register_pro', 5 );

/**
 * Attempt to Register Plugin
 *
 * @since 4.6
 *
 */
function tribe_register_pro() {

	//remove action if we run this hook through common
	remove_action( 'plugins_loaded', 'tribe_register_pro', 50 );

	// if we do not have a dependency checker then shut down
	if ( ! class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

		add_action( 'admin_notices', 'tribe_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_show_fail_message' );

		//prevent loading of PRO
		remove_action( 'tribe_common_loaded', 'tribe_events_calendar_pro_init' );

		return;
	}

	tribe_init_events_pro_autoloading();

	new Tribe__Events__Pro__Plugin_Register();

}
add_action( 'tribe_common_loaded', 'tribe_register_pro', 5 );
// add action if Event Tickets or the Events Calendar is not active
add_action( 'plugins_loaded', 'tribe_register_pro', 50 );

/**
 * Instantiate class and set up WordPress actions on Common Loaded
 *
 * @since 4.6
 *
 */
add_action( 'tribe_common_loaded', 'tribe_events_calendar_pro_init' );
function tribe_events_calendar_pro_init() {

	$classes_exist = class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Pro__Main' );
	$plugins_check = function_exists( 'tribe_check_plugin' ) ?
		tribe_check_plugin( 'Tribe__Events__Pro__Main' )
		: false;
	$version_ok    = $classes_exist && $plugins_check;

	if ( class_exists( 'Tribe__Main' ) && ! is_admin() && ! file_exists( __DIR__ . '/src/Tribe/PUE/Helper.php' ) ) {
		tribe_main_pue_helper();
	}

	if ( apply_filters( 'tribe_ecp_to_run_or_not_to_run', $version_ok ) ) {
		new Tribe__Events__Pro__PUE( __FILE__ );
		Tribe__Events__Pro__Main::instance();
	} else {
		/**
		 * Dummy function to avoid fatal error in edge upgrade case
		 *
		 * @return bool
		 **/
		function tribe_is_recurring_event() {
			return false;
		}
	}
	if ( ! $version_ok ) {

		// if we have the plugin register the dependency check will handle the messages
		if ( class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

			new Tribe__Events__Pro__PUE( __FILE__ );

			return;
		}

		add_action( 'admin_notices', 'tribe_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_show_fail_message' );
	}
}


/**
 * Shows message if the plugin can't load due to TEC not being available.
 */
function tribe_show_fail_message() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$domain = 'tribe-events-calendar-pro';

	// If we don't have Common classes load the old fashioned way
	if ( ! class_exists( 'Tribe__Main' ) ) {
		load_plugin_textdomain( $domain, false, $mopath );
	} else {
		// This will load `wp-content/languages/plugins` files first
		Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

	$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';

	echo '<div class="error"><p>'
	. sprintf(
		'%1s <a href="%2s" class="thickbox" title="%3s">%4s</a>.',
		esc_html__( 'To begin using Events Calendar PRO, please install the latest version of', 'tribe-events-calendar-pro' ),
		esc_url( $url ),
		esc_html__( 'The Events Calendar', 'tribe-events-calendar-pro' ),
		esc_html__( 'The Events Calendar', 'tribe-events-calendar-pro' )
		) .
	'</p></div>';
}

/**
 * Requires the autoloader class from the main plugin class and sets up
 * autoloading.
 */
function tribe_init_events_pro_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}
	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix( 'Tribe__Events__Pro__', dirname( __FILE__ ) . '/src/Tribe', 'events-calendar-pro' );

	// deprecated classes are registered in a class to path fashion
	foreach ( glob( dirname( __FILE__ ) . '/src/deprecated/*.php' ) as $file ) {
		$class_name = str_replace( '.php', '', basename( $file ) );
		$autoloader->register_class( $class_name, $file );
	}
	$autoloader->register_autoloader();
}

/**
 * Register Deactivation
 */
register_deactivation_hook( __FILE__, 'tribe_events_pro_deactivation' );
function tribe_events_pro_deactivation( $network_deactivating ) {

	if ( ! class_exists( 'Tribe__Abstract_Deactivation' ) ) {
		return; // can't do anything since core isn't around
	}

	require_once dirname( __FILE__ ) . '/src/Tribe/Main.php';
	require_once dirname( __FILE__ ) . '/src/Tribe/Deactivation.php';
	Tribe__Events__Pro__Main::deactivate( $network_deactivating );
}
/**
 * Register Activation
 */
register_activation_hook( __FILE__, 'tribe_events_pro_activation' );
function tribe_events_pro_activation() {
	if ( ! is_network_admin()  ) {
		// We set with a string to avoid having to include a file here.
		set_transient( '_tribe_events_delayed_flush_rewrite_rules', 'yes', 0 );
	}
}

/**
 * Instantiate class and set up WordPress actions.
 *
 * @deprecated 4.6
 *
 */
function Tribe_ECP_Load() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	return;
}

/**
 * Add Events PRO to the list of add-ons to check required version.
 *
 * @deprecated 4.6
 *
 * @since 4.6
 *
 * @return array $plugins the required info
 */
function tribe_init_ecp_addon( $plugins ) {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	$plugins['Tribe__Events__Pro__Main'] = array(
		'plugin_name' => 'Events Calendar PRO',
		'required_version' => Tribe__Events__Pro__Main::REQUIRED_TEC_VERSION,
		'current_version' => Tribe__Events__Pro__Main::VERSION,
		'plugin_dir_file' => basename( dirname( __FILE__ ) ) . '/events-calendar-pro.php',
	);

	return $plugins;
}
