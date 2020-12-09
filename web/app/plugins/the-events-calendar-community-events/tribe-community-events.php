<?php
/*
Plugin Name: The Events Calendar: Community Events
Plugin URI:  http://m.tri.be/1acd
Description: Community Events is an add-on providing additional functionality to the open source plugin The Events Calendar. Empower users to submit and manage their events on your website. <a href="http://tri.be/shop/wordpress-community-events/?utm_campaign=in-app&utm_source=docblock&utm_medium=plugin-community">Check out the full feature list</a>. Need more features? Peruse our selection of <a href="http://tri.be/products/?utm_campaign=in-app&utm_source=docblock&utm_medium=plugin-community" target="_blank">plugins</a>.
Version:     4.7.1.1
Author:      Modern Tribe, Inc.
Author URI:  http://m.tri.be/21
Text Domain: tribe-events-community
Domain Path: /lang/
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
Copyright 2011-2012 by Modern Tribe Inc and the contributors

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

define( 'EVENTS_COMMUNITY_DIR', dirname( __FILE__ ) );
define( 'EVENTS_COMMUNITY_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( EVENTS_COMMUNITY_FILE ) . '/src/functions/php-min-version.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'tribe-events-community', EVENTS_COMMUNITY_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.6
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_events_community_not_php_version_plugin_name( $names ) {
		$names['tribe-events-community'] = esc_html__( 'Community Events', 'tribe-events-community' );

		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_events_community_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}

	return false;
}

/**
 * Attempt to Register Plugin
 *
 * @since 4.6
 */
function tribe_register_community_events() {
	//remove action if we run this hook through common
	remove_action( 'plugins_loaded', 'tribe_register_community_events', 50 );

	// if we do not have a dependency checker then shut down
	if ( ! class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {
		add_action( 'admin_notices', 'tribe_events_community_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_events_community_show_fail_message' );

		//prevent loading of PRO
		remove_action( 'tribe_common_loaded', 'tribe_community_events_init' );

		return;
	}

	tribe_community_events_autoloading();

	new Tribe__Events__Community__Plugin_Register();

}
add_action( 'tribe_common_loaded', 'tribe_register_community_events', 5 );
// add action if Event Tickets or the Events Calendar is not active
add_action( 'plugins_loaded', 'tribe_register_community_events', 50 );


/**
 * Instantiate class and set up WordPress actions on Common Loaded
 *
 * @since 4.6
 */
add_action( 'tribe_common_loaded', 'tribe_community_events_init' );
function tribe_community_events_init() {
	$classes_exist = class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Community__Main' );
	$plugins_check = function_exists( 'tribe_check_plugin' ) ?
		tribe_check_plugin( 'Tribe__Events__Community__Main' )
		: false;
	$version_ok    = $classes_exist && $plugins_check;

	if ( class_exists( 'Tribe__Main' ) && ! is_admin() && ! file_exists( __DIR__ . '/src/Tribe/PUE/Helper.php' ) ) {
		tribe_main_pue_helper();
	}

	if ( ! $version_ok ) {
		// if we have the plugin register the dependency check will handle the messages
		if ( class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {
			new Tribe__Events__Community__PUE( __FILE__ );

			return;
		}

		add_action( 'admin_notices', 'tribe_events_community_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_events_community_show_fail_message' );

		return;
	}

	require_once( 'src/functions/template-tags.php' );

	new Tribe__Events__Community__PUE( EVENTS_COMMUNITY_FILE );

	tribe_singleton( 'community.main', new Tribe__Events__Community__Main() );
	tribe_singleton( 'community.templates', new Tribe__Events__Community__Templates() );

	add_action( 'admin_init', [ 'Tribe__Events__Community__Schema', 'init' ] );
}

/**
 * Autoloading of Tribe Events Community
 *
 * @since 3.10
 */
function tribe_community_events_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix( 'Tribe__Events__Community__', EVENTS_COMMUNITY_DIR . '/src/Tribe', 'events-community' );

	// deprecated classes are registered in a class to path fashion
	foreach ( glob( EVENTS_COMMUNITY_DIR . '/src/deprecated/*.php' ) as $file ) {
		$class_name = str_replace( '.php', '', basename( $file ) );
		$autoloader->register_class( $class_name, $file );
	}

	$autoloader->register_autoloader();
}

/**
 * Shows message if the plugin can't load due to TEC not being installed.
 *
 * @deprecated 4.6.3 Use the new properly namespaced function tribe_events_community_show_fail_message instead.
 *
 * @since  1.0
 */
function tribe_ce_show_fail_message() {
	_deprecated_function(
		__FUNCTION__,
		'4.6.3',
		'tribe_events_community_show_fail_message'
	);
	tribe_events_community_show_fail_message();
}

/**
 * Shows notice of missing requirements if Common is unavailable due to TEC not being active.
 *
 * @since 4.6.3
 * @since 4.6.5 Added messaging for the other plugins besides just TEC.
 */
function tribe_events_community_show_fail_message() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$text_domain = 'tribe-events-community';

	// If we don't have Common classes load the old fashioned way
	if ( ! class_exists( 'Tribe__Main' ) ) {
		load_plugin_textdomain( $text_domain, false, $mopath );
	} else {
		// This will load `wp-content/languages/plugins` files first
		Tribe__Main::instance()->load_text_domain( $text_domain, $mopath );
	}

	// Make sure Thickbox is available and consistent appearance regardless of which admin page we're on
	wp_enqueue_style( 'plugin-install' );
	wp_enqueue_script( 'plugin-install' );
	add_thickbox();

	echo '<div class="error"><p>'
	. sprintf(
		'%1s <a href="%2s" class="thickbox" title="%3s">%4s</a>.',
		esc_html__( 'To begin using The Events Calendar: Community Events, please install the latest version of', 'tribe-events-community' ),
		esc_url( 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true' ),
		esc_html__( 'The Events Calendar', 'tribe-events-community' ),
		esc_html__( 'The Events Calendar', 'tribe-events-community' )
		) .
	'</p></div>';
}

register_activation_hook( EVENTS_COMMUNITY_FILE, 'tribe_ce_activate' );

function tribe_ce_activate() {
	tribe_community_events_autoloading();
	if ( ! class_exists( 'Tribe__Events__Community__Main' ) ) {
		return;
	}
	Tribe__Events__Community__Main::activateFlushRewrite();
}

/**
 * Instantiate class and get the party started!
 *
 * @deprecated 4.6
 *
 * @since 1.0
 */
function Tribe_CE_Load() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	return;
}
