<?php
/*
Plugin Name: GP Premium
Plugin URI: https://generatepress.com
Description: The entire collection of GeneratePress premium modules.
Version: 1.9.1
Author: Tom Usborne
Author URI: https://generatepress.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gp-premium
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set our version
define( 'GP_PREMIUM_VERSION', '1.9.1' );

// Set our library directory
define( 'GP_LIBRARY_DIRECTORY', plugin_dir_path( __FILE__ ) . 'library/' );
define( 'GP_LIBRARY_DIRECTORY_URL', plugin_dir_url( __FILE__ ) . 'library/' );

if ( ! function_exists( 'generatepress_is_module_active' ) ) {
	/**
	 * Check to see if an add-ons is active
	 * module: Check the database entry
	 * definition: Check to see if defined in wp-config.php
	 **/
	function generatepress_is_module_active( $module, $definition ) {
		// If we don't have the module or definition, bail.
		if ( ! $module && ! $definition ) {
			return false;
		}

		// If our module is active, return true.
		if ( 'activated' == get_option( $module ) || defined( $definition ) ) {
			return true;
		}

		// Not active? Return false.
		return false;
	}
}

if ( ! function_exists( 'generate_package_setup' ) ) {
	add_action( 'plugins_loaded', 'generate_package_setup' );
	/**
	 * Set up our translations
	 **/
	function generate_package_setup() {
		load_plugin_textdomain( 'gp-premium', false, 'gp-premium/langs/' );
	}
}

// Backgrounds
if ( generatepress_is_module_active( 'generate_package_backgrounds', 'GENERATE_BACKGROUNDS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'backgrounds/generate-backgrounds.php';
}

// Blog
if ( generatepress_is_module_active( 'generate_package_blog', 'GENERATE_BLOG' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'blog/generate-blog.php';
}

// Colors
if ( generatepress_is_module_active( 'generate_package_colors', 'GENERATE_COLORS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'colors/generate-colors.php';
}

// Copyright
if ( generatepress_is_module_active( 'generate_package_copyright', 'GENERATE_COPYRIGHT' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'copyright/generate-copyright.php';
}

// Disable Elements
if ( generatepress_is_module_active( 'generate_package_disable_elements', 'GENERATE_DISABLE_ELEMENTS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'disable-elements/generate-disable-elements.php';
}

// Elements
if ( generatepress_is_module_active( 'generate_package_elements', 'GENERATE_ELEMENTS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'elements/elements.php';
}

// Hooks
if ( generatepress_is_module_active( 'generate_package_hooks', 'GENERATE_HOOKS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'hooks/generate-hooks.php';
}

// Page Header
if ( generatepress_is_module_active( 'generate_package_page_header', 'GENERATE_PAGE_HEADER' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'page-header/generate-page-header.php';
}

// Secondary Navigation
if ( generatepress_is_module_active( 'generate_package_secondary_nav', 'GENERATE_SECONDARY_NAV' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'secondary-nav/generate-secondary-nav.php';
}

// Spacing
if ( generatepress_is_module_active( 'generate_package_spacing', 'GENERATE_SPACING' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'spacing/generate-spacing.php';
}

// Typography
if ( generatepress_is_module_active( 'generate_package_typography', 'GENERATE_TYPOGRAPHY' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'typography/generate-fonts.php';
}

// Sections
if ( generatepress_is_module_active( 'generate_package_sections', 'GENERATE_SECTIONS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'sections/generate-sections.php';
}

// Menu Plus
if ( generatepress_is_module_active( 'generate_package_menu_plus', 'GENERATE_MENU_PLUS' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'menu-plus/generate-menu-plus.php';
}

// WooCommerce
if ( generatepress_is_module_active( 'generate_package_woocommerce', 'GENERATE_WOOCOMMERCE' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'woocommerce/woocommerce.php';
	}
}

// General
require_once plugin_dir_path( __FILE__ ) . 'inc/functions.php';
require_once plugin_dir_path( __FILE__ ) . 'general/smooth-scroll.php';
require_once plugin_dir_path( __FILE__ ) . 'general/icons.php';

// Deprecated functions.
require_once plugin_dir_path( __FILE__ ) . 'inc/deprecated.php';

// Load admin-only files.
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/reset.php';
	require_once plugin_dir_path( __FILE__ ) . 'import-export/generate-ie.php';

	if ( generatepress_is_module_active( 'generate_package_site_library', 'GENERATE_SITE_LIBRARY' ) && version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'sites/sites.php';
	}

	require_once plugin_dir_path( __FILE__ ) . 'inc/activation.php';
	require_once plugin_dir_path( __FILE__ ) . 'inc/dashboard.php';
}

if ( ! function_exists( 'generate_premium_updater' ) ) {
	add_action( 'admin_init', 'generate_premium_updater', 0 );
	/**
	 * Set up the updater
	 **/
	function generate_premium_updater() {
		// Load EDD SL Plugin Updater
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/library/EDD_SL_Plugin_Updater.php' );
		}

		// retrieve our license key from the DB
		$license_key = get_option( 'gen_premium_license_key' );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( 'https://generatepress.com', __FILE__, array(
				'version' 	=> GP_PREMIUM_VERSION,
				'license' 	=> trim( $license_key ),
				'item_name' => 'GP Premium',
				'author' 	=> 'Tom Usborne',
				'url'       => home_url(),
				'beta'		=> apply_filters( 'generate_premium_beta_tester', false ),
			)
		);
	}
}

if ( ! function_exists( 'generate_premium_setup' ) ) {
	add_action( 'after_setup_theme', 'generate_premium_setup' );
	/**
	 * Add useful functions to GP Premium
	 **/
	function generate_premium_setup() {
		// This used to be in the theme but the WP.org review team asked for it to be removed.
		// Not wanting people to have broken shortcodes in their widgets on update, I added it into premium.
		add_filter( 'widget_text', 'do_shortcode' );
	}
}

if ( ! function_exists( 'generate_premium_theme_information' ) ) {
	add_action( 'admin_notices', 'generate_premium_theme_information' );
	/**
	 * Checks whether there's a theme update available and lets you know.
	 * Also checks to see if GeneratePress is the active theme. If not, tell them.
	 *
	 * @since 1.2.95
	 **/
	function generate_premium_theme_information() {

		// Get our theme data
		$theme = wp_get_theme();

		// If we're using GeneratePress
		if ( 'GeneratePress' == $theme->name || 'generatepress' == $theme->template ) {

			// Get our information on updates
			// Taken from https://developer.wordpress.org/reference/functions/wp_prepare_themes_for_js/
			$updates = array();
			if ( current_user_can( 'update_themes' ) ) {
				$updates_transient = get_site_transient( 'update_themes' );
				if ( isset( $updates_transient->response ) ) {
					$updates = $updates_transient->response;
				}
			}

			// Check what admin page we're on
			$screen = get_current_screen();

			// If a GeneratePress update exists, and we're not on the themes page.
			// No need to tell people an update exists on the themes page, WP does that for us.
			if ( isset( $updates[ 'generatepress' ] ) && 'themes' !== $screen->base ) {

				printf(
					'<div class="notice is-dismissible notice-info">
						<p>%1$s <a href="%2$s">%3$s</a></p>
					</div>',
					esc_html__( 'GeneratePress has an update available.', 'gp-premium' ),
					esc_url( admin_url( 'themes.php' ) ),
					esc_html__( 'Update now.', 'gp-premium' )
				);

			}

		} else {

			// GeneratePress isn't the active theme, let them know GP Premium won't work.
			printf(
				'<div class="notice is-dismissible notice-warning">
					<p>%1$s <a href="https://generatepress.com/install-generatepress" target="_blank">%2$s</a></p>
				</div>',
				esc_html__( 'GP Premium requires GeneratePress to be your active theme.', 'gp-premium' ),
				esc_html__( 'Install now.', 'gp-premium' )
			);

		}

	}
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'generate_add_configure_action_link' );
/**
 * Show a "Configure" link in the plugin action links.
 *
 * @since 1.3
 */
function generate_add_configure_action_link( $links ) {
	$configuration_link = '<a href="' . admin_url( 'themes.php?page=generate-options' ) . '">' . __( 'Configure', 'gp-premium' ) . '</a>';
	return array_merge( $links, array( $configuration_link ) );
}

add_action( 'admin_init', 'generatepress_deactivate_standalone_addons' );
/**
 * Deactivate any standalone add-ons if they're active.
 *
 * @since 1.3.1
 */
function generatepress_deactivate_standalone_addons() {
	$addons = array(
		'generate-backgrounds/generate-backgrounds.php',
		'generate-blog/generate-blog.php',
		'generate-colors/generate-colors.php',
		'generate-copyright/generate-copyright.php',
		'generate-disable-elements/generate-disable-elements.php',
		'generate-hooks/generate-hooks.php',
		'generate-ie/generate-ie.php',
		'generate-menu-plus/generate-menu-plus.php',
		'generate-page-header/generate-page-header.php',
		'generate-secondary-nav/generate-secondary-nav.php',
		'generate-sections/generate-sections.php',
		'generate-spacing/generate-spacing.php',
		'generate-typography/generate-fonts.php'
	);

	deactivate_plugins( $addons );
}
