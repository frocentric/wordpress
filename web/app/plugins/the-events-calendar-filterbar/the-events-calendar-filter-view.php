<?php
/*
Plugin Name: The Events Calendar: Filter Bar
Description: Creates an advanced filter panel on the frontend of your events list views.
Version: 4.10.0
Author: Modern Tribe, Inc.
Author URI: http://m.tri.be/25
Text Domain: tribe-events-filter-view
License: GPLv2
*/

/*
Copyright 2012 Modern Tribe Inc. and the Collaborators

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

define( 'TRIBE_EVENTS_FILTERBAR_DIR', dirname( __FILE__ ) );
define( 'TRIBE_EVENTS_FILTERBAR_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( TRIBE_EVENTS_FILTERBAR_FILE ) . '/src/functions/php-min-version.php';

// Load Composer autoload file only if we've not included this file already.
require_once TRIBE_EVENTS_FILTERBAR_DIR . '/vendor/autoload.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'tribe-events-filter-view', TRIBE_EVENTS_FILTERBAR_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.6
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_events_filterbar_not_php_version_plugin_name( $names ) {
		$names['tribe-events-filter-view'] = esc_html__( 'Events Filter Bar', 'tribe-events-filter-view' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_events_filterbar_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}
	return false;
}

/**
 * Attempt to Register Plugin
 *
 * @since 4.6
 *
 */
function tribe_register_filterbar() {

	//remove action if we run this hook through common
	remove_action( 'plugins_loaded', 'tribe_register_filterbar', 50 );

	// if we do not have a dependency checker then shut down
	if ( ! class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

		add_action( 'admin_notices', 'tribe_events_filter_view_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_events_filter_view_show_fail_message' );

		//prevent loading of Filter Bar
		remove_action( 'tribe_common_loaded', 'tribe_events_filterbar_init' );

		return;
	}

	tribe_init_filterbar_autoloading();

	new Tribe__Events__Filterbar__Plugin_Register();

}
add_action( 'tribe_common_loaded', 'tribe_register_filterbar', 5 );
// add action if Event Tickets or the Events Calendar is not active
add_action( 'plugins_loaded', 'tribe_register_filterbar', 50 );


/**
 * Function used to load the the Filters View addon.
 *
 * @since 4.6
 *
 */
add_action( 'tribe_common_loaded', 'tribe_events_filterbar_init' );
function tribe_events_filterbar_init() {

	tribe_init_filterbar_autoloading();

	$classes_exist = class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Filterbar__View' );
	$plugins_check = function_exists( 'tribe_check_plugin' ) ?
		tribe_check_plugin( 'Tribe__Events__Filterbar__View' )
		: false;
	$version_ok    = $classes_exist && $plugins_check;

	if ( class_exists( 'Tribe__Main' ) && ! is_admin() && ! file_exists( __DIR__ . '/src/Tribe/PUE/Helper.php' ) ) {
		tribe_main_pue_helper();
	}

	if ( ! $version_ok ) {

		$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
		$domain = 'tribe-events-filter-view';

		// If we don't have Common classes load the old fashioned way
		if ( ! class_exists( 'Tribe__Main' ) ) {
			load_plugin_textdomain( $domain, false, $mopath );
		} else {
			// This will load `wp-content/languages/plugins` files first
			Tribe__Main::instance()->load_text_domain( $domain, $mopath );
    }

		// if we have the plugin register the dependency check will handle the messages
		if ( class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {
			new Tribe__Events__Filterbar__PUE( TRIBE_EVENTS_FILTERBAR_FILE );

			return;
		}

		add_action( 'admin_notices', 'tribe_events_filter_view_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_events_filter_view_show_fail_message' );

		return;
	}

	Tribe__Events__Filterbar__View::init();
	new Tribe__Events__Filterbar__PUE( TRIBE_EVENTS_FILTERBAR_FILE );
}

/**
 * Shows message if the plugin can't load due to TEC not being installed.
 *
 * @since 3.4
 * @author PaulHughes01
 *
 * @return void
 */
function tribe_events_filter_view_show_fail_message() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$domain = 'tribe-events-filter-view';

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
		esc_html__( 'To begin using The Events Calendar: Filter Bar, please install the latest version of', 'tribe-events-filter-view' ),
		esc_url( $url ),
		esc_html__( 'The Events Calendar', 'tribe-events-filter-view' ),
		esc_html__( 'The Events Calendar', 'tribe-events-filter-view' )
		) .
	'</p></div>';
}

/**
 * Requires the autoloader class from the main plugin class and sets up
 * autoloading.
 */
function tribe_init_filterbar_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix( 'Tribe__Events__Filterbar__', TRIBE_EVENTS_FILTERBAR_DIR . '/src/Tribe', 'tribe-filterbar' );

	// deprecated classes are registered in a class to path fashion
	foreach ( glob( TRIBE_EVENTS_FILTERBAR_DIR . '/src/deprecated/*.php' ) as $file ) {
		$class_name = str_replace( '.php', '', basename( $file ) );
		$autoloader->register_class( $class_name, $file );
	}
	$autoloader->register_autoloader();
}

/**
 * Function used to load the the Filters View addon.
 *
 * @deprecated 4.6
 *
 * @since 3.4
 *
 * @author PaulHughes01
 * @return void
 */
 function TribeEventsFilterViewsLoad() {
	 _deprecated_function( __FUNCTION__, '4.6', '' );

    return;
 }
