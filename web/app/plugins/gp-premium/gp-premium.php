<?php
/**
 * Plugin Name: GP Premium
 * Plugin URI: https://generatepress.com
 * Description: The entire collection of GeneratePress premium modules.
 * Version: 2.3.2
 * Requires at least: 5.2
 * Requires PHP: 5.6
 * Author: Tom Usborne
 * Author URI: https://generatepress.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gp-premium
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'GP_PREMIUM_VERSION', '2.3.2' );
define( 'GP_PREMIUM_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GP_PREMIUM_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'GP_LIBRARY_DIRECTORY', plugin_dir_path( __FILE__ ) . 'library/' );
define( 'GP_LIBRARY_DIRECTORY_URL', plugin_dir_url( __FILE__ ) . 'library/' );

require_once GP_PREMIUM_DIR_PATH . 'inc/class-rest.php';

if ( ! function_exists( 'generatepress_is_module_active' ) ) {
	/**
	 * Checks if a module is active.
	 *
	 * @param string $module The option name to check.
	 * @param string $constant The constant to check for.
	 **/
	function generatepress_is_module_active( $module, $constant ) {
		// If we don't have the module or constant, bail.
		if ( ! $module && ! $constant ) {
			return false;
		}

		// If our module is active, return true.
		if ( 'activated' === get_option( $module ) || defined( $constant ) ) {
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

if ( generatepress_is_module_active( 'generate_package_backgrounds', 'GENERATE_BACKGROUNDS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'backgrounds/generate-backgrounds.php';
}

if ( generatepress_is_module_active( 'generate_package_blog', 'GENERATE_BLOG' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'blog/generate-blog.php';
}

if ( generatepress_is_module_active( 'generate_package_copyright', 'GENERATE_COPYRIGHT' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'copyright/generate-copyright.php';
}

if ( generatepress_is_module_active( 'generate_package_disable_elements', 'GENERATE_DISABLE_ELEMENTS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'disable-elements/generate-disable-elements.php';
}

if ( generatepress_is_module_active( 'generate_package_elements', 'GENERATE_ELEMENTS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'elements/elements.php';
}

if ( generatepress_is_module_active( 'generate_package_secondary_nav', 'GENERATE_SECONDARY_NAV' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'secondary-nav/generate-secondary-nav.php';
}

if ( generatepress_is_module_active( 'generate_package_spacing', 'GENERATE_SPACING' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'spacing/generate-spacing.php';
}

if ( generatepress_is_module_active( 'generate_package_menu_plus', 'GENERATE_MENU_PLUS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'menu-plus/generate-menu-plus.php';
}

if ( generatepress_is_module_active( 'generate_package_woocommerce', 'GENERATE_WOOCOMMERCE' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		require_once GP_PREMIUM_DIR_PATH . 'woocommerce/woocommerce.php';
	}
}

// Deprecated modules.
if ( generatepress_is_module_active( 'generate_package_hooks', 'GENERATE_HOOKS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'hooks/generate-hooks.php';
}

if ( generatepress_is_module_active( 'generate_package_page_header', 'GENERATE_PAGE_HEADER' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'page-header/generate-page-header.php';
}

if ( generatepress_is_module_active( 'generate_package_sections', 'GENERATE_SECTIONS' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'sections/generate-sections.php';
}

add_action( 'after_setup_theme', 'generate_premium_load_modules' );
/**
 * Load our modules after the theme has initiated.
 *
 * @since 2.1.0
 */
function generate_premium_load_modules() {
	$is_using_dynamic_typography = function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography();

	if ( ! $is_using_dynamic_typography && generatepress_is_module_active( 'generate_package_typography', 'GENERATE_TYPOGRAPHY' ) ) {
		require_once GP_PREMIUM_DIR_PATH . 'typography/generate-fonts.php';
	}

	if ( version_compare( generate_premium_get_theme_version(), '3.1.0-alpha.1', '<' ) && generatepress_is_module_active( 'generate_package_colors', 'GENERATE_COLORS' ) ) {
		require_once GP_PREMIUM_DIR_PATH . 'colors/generate-colors.php';
	}
}

// General functionality.
require_once GP_PREMIUM_DIR_PATH . 'inc/functions.php';
require_once GP_PREMIUM_DIR_PATH . 'general/class-external-file-css.php';
require_once GP_PREMIUM_DIR_PATH . 'general/smooth-scroll.php';
require_once GP_PREMIUM_DIR_PATH . 'general/icons.php';
require_once GP_PREMIUM_DIR_PATH . 'general/enqueue-scripts.php';
require_once GP_PREMIUM_DIR_PATH . 'inc/deprecated.php';

// Load our Dashboard functions once the theme has loaded.
require_once GP_PREMIUM_DIR_PATH . 'inc/class-dashboard.php';

if ( generatepress_is_module_active( 'generate_package_site_library', 'GENERATE_SITE_LIBRARY' ) && version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
	require_once GP_PREMIUM_DIR_PATH . 'site-library/class-site-library-rest.php';
	require_once GP_PREMIUM_DIR_PATH . 'site-library/class-site-library-helper.php';
}

if ( is_admin() ) {
	require_once GP_PREMIUM_DIR_PATH . 'inc/deprecated-admin.php';

	if ( generatepress_is_module_active( 'generate_package_site_library', 'GENERATE_SITE_LIBRARY' ) && version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
		require_once GP_PREMIUM_DIR_PATH . 'site-library/class-site-library.php';
	}
}

if ( ! function_exists( 'generate_premium_updater' ) ) {
	add_action( 'admin_init', 'generate_premium_updater', 0 );
	/**
	 * Set up the updater
	 **/
	function generate_premium_updater() {
		if ( ! class_exists( 'GeneratePress_Premium_Plugin_Updater' ) ) {
			include GP_PREMIUM_DIR_PATH . 'library/class-plugin-updater.php';
		}

		$license_key = get_option( 'gen_premium_license_key' );

		$edd_updater = new GeneratePress_Premium_Plugin_Updater(
			'https://generatepress.com',
			__FILE__,
			array(
				'version'   => GP_PREMIUM_VERSION,
				'license'   => trim( $license_key ),
				'item_name' => 'GP Premium',
				'author'    => 'Tom Usborne',
				'url'       => home_url(),
				'beta'      => apply_filters( 'generate_premium_beta_tester', false ),
			)
		);
	}
}

add_filter( 'edd_sl_plugin_updater_api_params', 'generate_premium_set_updater_api_params', 10, 3 );
/**
 * Add the GeneratePress version to our updater params.
 *
 * @param array  $api_params  The array of data sent in the request.
 * @param array  $api_data    The array of data set up in the class constructor.
 * @param string $plugin_file The full path and filename of the file.
 */
function generate_premium_set_updater_api_params( $api_params, $api_data, $plugin_file ) {
	/*
	 * Make sure $plugin_file matches your plugin's file path. You should have a constant for this
	 * or can use __FILE__ if this code goes in your plugin's main file.
	 */
	if ( __FILE__ === $plugin_file ) {
		// Dynamically retrieve the current version number.
		$api_params['generatepress_version'] = defined( 'GENERATE_VERSION' ) ? GENERATE_VERSION : '';
	}

	return $api_params;
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
		$theme = wp_get_theme();

		if ( 'GeneratePress' === $theme->name || 'generatepress' === $theme->template ) {

			// Get our information on updates.
			// @see https://developer.wordpress.org/reference/functions/wp_prepare_themes_for_js/.
			$updates = array();
			if ( current_user_can( 'update_themes' ) ) {
				$updates_transient = get_site_transient( 'update_themes' );
				if ( isset( $updates_transient->response ) ) {
					$updates = $updates_transient->response;
				}
			}

			$screen = get_current_screen();

			// If a GeneratePress update exists, and we're not on the themes page.
			// No need to tell people an update exists on the themes page, WP does that for us.
			if ( isset( $updates['generatepress'] ) && 'themes' !== $screen->base ) {
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
					<p>%1$s <a href="%3$s">%2$s</a></p>
				</div>',
				esc_html__( 'GP Premium requires GeneratePress to be your active theme.', 'gp-premium' ),
				esc_html__( 'Install now.', 'gp-premium' ),
				esc_url( admin_url( 'theme-install.php?theme=generatepress' ) )
			);
		}

	}
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'generate_add_configure_action_link' );
/**
 * Show a "Configure" link in the plugin action links.
 *
 * @since 1.3
 * @param array $links The existing plugin row links.
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
		'generate-typography/generate-fonts.php',
	);

	deactivate_plugins( $addons );
}
