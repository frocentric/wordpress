<?php
/*
Plugin Name: WP Show Posts Pro
Plugin URI: https://wpshowposts.com
Description: WP Show Posts Pro extends the awesome WP Show Posts plugin. It adds new features like Masonry, AJAX pagination, styling and much more!
Version: 1.0-beta.1
Author: Tom Usborne
Author URI: https://tomusborne.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-show-posts-pro
*/

define( 'WPSP_PRO_VERSION', '1.0-beta.1' );

// Require WP Show Posts free version
require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'wpsp_register_required_plugins' );
/**
 * Set up TGMPA
 *
 * @since 0.1
 */
function wpsp_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => 'WP Show Posts',
			'slug'      => 'wp-show-posts',
			'required'  => true,
		)
	);

	$config = array(
		'id'           => 'wp-show-posts-pro',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'parent_slug'  => 'plugins.php',
		'capability'   => 'manage_options',
		'has_notices'  => true,
		'dismissable'  => false,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	tgmpa( $plugins, $config );
}

// Add necessary files
require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/admin.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/defaults.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/compat.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'inc/sanitize.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/ajax-pagination/ajax-pagination.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/styling/styling.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/columns/columns.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/social-sharing/social-sharing.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/image-gallery/image-gallery.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/carousel/carousel.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/general/general.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'modules/cards/cards.php';

add_action( 'plugins_loaded', 'wpsp_pro_languages' );
/*
 * Make WPSP Pro translatable
 * @since 0.4
 */
function wpsp_pro_languages() {
	load_plugin_textdomain( 'wp-show-posts-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'wp_enqueue_scripts', 'wpsp_pro_enqueue_scripts' );
/*
 * Enqueue our CSS to the front end
 * @since 0.1
 */
function wpsp_pro_enqueue_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
	wp_enqueue_style( 'wp-show-posts-pro', plugins_url( "css/wp-show-posts{$suffix}.css", __FILE__ ), array( 'wp-show-posts' ), WPSP_PRO_VERSION );
}


add_action( 'admin_init', 'wpsp_pro_updater', 0 );
/*
 * Set up our updater.
 *
 * @since 0.1
 */
function wpsp_pro_updater() {
	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater
		include( dirname( __FILE__ ) . '/inc/EDD_SL_Plugin_Updater.php' );
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'wp_show_posts_license' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( 'https://wpshowposts.com', __FILE__, array(
			'version' 	=> WPSP_PRO_VERSION,
			'license' 	=> $license_key,
			'item_name' => 'WP Show Posts Pro',
			'author' 	=> 'Tom Usborne',
			'url'       => home_url(),
			'beta'		=> apply_filters( 'wpsp_pro_beta_tester', false ),
		)
	);
}
