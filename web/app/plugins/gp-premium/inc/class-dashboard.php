<?php
/**
 * Build our admin dashboard.
 *
 * @package GeneratePress Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This class adds premium sections to our Dashboard.
 */
class GeneratePress_Pro_Dashboard {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @var $instance Class instance.
	 */
	private static $instance;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get started.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
	}

	/**
	 * Add our actions and require old Dashboard files if we need them.
	 */
	public function setup() {
		// Load our old dashboard if we're using an old version of GeneratePress.
		if ( ! class_exists( 'GeneratePress_Dashboard' ) ) {
			if ( is_admin() ) {
				require_once GP_PREMIUM_DIR_PATH . 'inc/legacy/dashboard.php';
				require_once GP_PREMIUM_DIR_PATH . 'inc/legacy/import-export.php';
				require_once GP_PREMIUM_DIR_PATH . 'inc/legacy/reset.php';
				require_once GP_PREMIUM_DIR_PATH . 'inc/legacy/activation.php';
			}

			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'generate_admin_dashboard', array( $this, 'module_list' ), 8 );
		add_action( 'generate_admin_dashboard', array( $this, 'license_key' ), 5 );
		add_action( 'generate_admin_dashboard', array( $this, 'import_export' ), 50 );
		add_action( 'generate_admin_dashboard', array( $this, 'reset' ), 100 );
		add_filter( 'generate_premium_beta_tester', array( $this, 'set_beta_tester' ) );
	}

	/**
	 * Get data for all of our pro modules.
	 */
	public static function get_modules() {
		$modules = array(
			'Backgrounds' => array(
				'title' => __( 'Backgrounds', 'gp-premium' ),
				'description' => __( 'Set background images for various HTML elements.', 'gp-premium' ),
				'key' => 'generate_package_backgrounds',
				'settings' => 'generate_background_settings',
				'isActive' => 'activated' === get_option( 'generate_package_backgrounds', false ),
				'exportable' => true,
			),
			'Blog' => array(
				'title' => __( 'Blog', 'gp-premium' ),
				'description' => __( 'Set blog options like infinite scroll, masonry layouts and more.', 'gp-premium' ),
				'key' => 'generate_package_blog',
				'settings' => 'generate_blog_settings',
				'isActive' => 'activated' === get_option( 'generate_package_blog', false ),
				'exportable' => true,
			),
			'Colors' => array(
				'title' => __( 'Colors', 'gp-premium' ),
				'key' => 'generate_package_colors',
				'isActive' => 'activated' === get_option( 'generate_package_colors', false ),
			),
			'Copyright' => array(
				'title' => __( 'Copyright', 'gp-premium' ),
				'description' => __( 'Set a custom copyright message in your footer.', 'gp-premium' ),
				'key' => 'generate_package_copyright',
				'settings' => 'copyright',
				'isActive' => 'activated' === get_option( 'generate_package_copyright', false ),
				'exportable' => true,
			),
			'Disable Elements' => array(
				'title' => __( 'Disable Elements', 'gp-premium' ),
				'description' => __( 'Disable default theme elements on specific pages or inside a Layout Element.', 'gp-premium' ),
				'key' => 'generate_package_disable_elements',
				'isActive' => 'activated' === get_option( 'generate_package_disable_elements', false ),
			),
			'Elements' => array(
				'title' => __( 'Elements', 'gp-premium' ),
				'description' => __( 'Use our block editor theme builder, build advanced HTML hooks, and gain more Layout control.', 'gp-premium' ),
				'key' => 'generate_package_elements',
				'isActive' => 'activated' === get_option( 'generate_package_elements', false ),
			),
			'Hooks' => array(
				'title' => __( 'Hooks', 'gp-premium' ),
				'description' => __( 'This module has been deprecated. Please use Elements instead.', 'gp-premium' ),
				'key' => 'generate_package_hooks',
				'settings' => 'generate_hooks',
				'isActive' => 'activated' === get_option( 'generate_package_hooks', false ),
				'exportable' => true,
			),
			'Menu Plus' => array(
				'title' => __( 'Menu Plus', 'gp-premium' ),
				'description' => __( 'Set up a mobile header, sticky navigation or off-canvas panel.', 'gp-premium' ),
				'key' => 'generate_package_menu_plus',
				'settings' => 'generate_menu_plus_settings',
				'isActive' => 'activated' === get_option( 'generate_package_menu_plus', false ),
				'exportable' => true,
			),
			'Page Header' => array(
				'title' => __( 'Page Header', 'gp-premium' ),
				'description' => __( 'This module has been deprecated. Please use Elements instead.', 'gp-premium' ),
				'key' => 'generate_package_page_header',
				'settings' => 'generate_page_header_settings',
				'isActive' => 'activated' === get_option( 'generate_package_page_header', false ),
				'exportable' => true,
			),
			'Secondary Nav' => array(
				'title' => __( 'Secondary Nav', 'gp-premium' ),
				'description' => __( 'Add a fully-featured secondary navigation to your site.', 'gp-premium' ),
				'key' => 'generate_package_secondary_nav',
				'settings' => 'generate_secondary_nav_settings',
				'isActive' => 'activated' === get_option( 'generate_package_secondary_nav', false ),
				'exportable' => true,
			),
			'Sections' => array(
				'title' => __( 'Sections', 'gp-premium' ),
				'description' => __( 'This module has been deprecated. Please consider using our GenerateBlocks plugin instead.', 'gp-premium' ),
				'key' => 'generate_package_sections',
				'isActive' => 'activated' === get_option( 'generate_package_sections', false ),
			),
			'Spacing' => array(
				'title' => __( 'Spacing', 'gp-premium' ),
				'description' => __( 'Set the padding and overall spacing of your theme elements.', 'gp-premium' ),
				'key' => 'generate_package_spacing',
				'settings' => 'generate_spacing_settings',
				'isActive' => 'activated' === get_option( 'generate_package_spacing', false ),
				'exportable' => true,
			),
			'Typography' => array(
				'title' => __( 'Typography', 'gp-premium' ),
				'description' => __( 'This module has been deprecated. Switch to our dynamic typography system in Customize > General instead.', 'gp-premium' ),
				'key' => 'generate_package_typography',
				'isActive' => 'activated' === get_option( 'generate_package_typography', false ),
			),
			'WooCommerce' => array(
				'title' => __( 'WooCommerce', 'gp-premium' ),
				'description' => __( 'Add colors, typography, and layout options to your WooCommerce store.', 'gp-premium' ),
				'key' => 'generate_package_woocommerce',
				'settings' => 'generate_woocommerce_settings',
				'isActive' => 'activated' === get_option( 'generate_package_woocommerce', false ),
				'exportable' => true,
			),
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'GENERATE_DISABLE_SITE_LIBRARY' ) ) {
			$modules['Site Library'] = array(
				'title' => __( 'Site Library', 'gp-premium' ),
				'description' => __( 'Choose from an extensive library of professionally designed starter sites.', 'gp-premium' ),
				'key' => 'generate_package_site_library',
				'isActive' => 'activated' === get_option( 'generate_package_site_library', false ),
			);
		}

		if ( function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography() ) {
			unset( $modules['Typography'] );
		}

		if ( version_compare( generate_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) ) {
			unset( $modules['Colors'] );
		}

		$deprecated_modules = apply_filters(
			'generate_premium_deprecated_modules',
			array(
				'Page Header',
				'Hooks',
				'Sections',
			)
		);

		foreach ( $deprecated_modules as $deprecated_module ) {
			if ( isset( $modules[ $deprecated_module ] ) ) {
				$modules[ $deprecated_module ]['deprecated'] = true;
			}
		}

		ksort( $modules );

		return $modules;
	}

	/**
	 * Get modules that can have their settings exported and imported.
	 */
	public static function get_exportable_modules() {
		$modules = array(
			'Core' => array(
				'settings' => 'generate_settings',
				'title' => __( 'Core', 'gp-premium' ),
				'isActive' => true,
			),
		);

		foreach ( self::get_modules() as $key => $data ) {
			if ( ! empty( $data['exportable'] ) && $data['isActive'] ) {
				$modules[ $key ] = $data;
			}
		}

		return $modules;
	}

	/**
	 * Get options using theme_mods.
	 */
	public static function get_theme_mods() {
		$theme_mods = array(
			'font_body_variants',
			'font_body_category',
			'font_site_title_variants',
			'font_site_title_category',
			'font_site_tagline_variants',
			'font_site_tagline_category',
			'font_navigation_variants',
			'font_navigation_category',
			'font_secondary_navigation_variants',
			'font_secondary_navigation_category',
			'font_buttons_variants',
			'font_buttons_category',
			'font_heading_1_variants',
			'font_heading_1_category',
			'font_heading_2_variants',
			'font_heading_2_category',
			'font_heading_3_variants',
			'font_heading_3_category',
			'font_heading_4_variants',
			'font_heading_4_category',
			'font_heading_5_variants',
			'font_heading_5_category',
			'font_heading_6_variants',
			'font_heading_6_category',
			'font_widget_title_variants',
			'font_widget_title_category',
			'font_footer_variants',
			'font_footer_category',
			'generate_copyright',
		);

		if ( function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography() ) {
			$theme_mods = array(
				'generate_copyright',
			);
		}

		return $theme_mods;
	}

	/**
	 * Get our setting keys.
	 */
	public static function get_setting_keys() {
		return array(
			'generate_settings',
			'generate_background_settings',
			'generate_blog_settings',
			'generate_hooks',
			'generate_page_header_settings',
			'generate_secondary_nav_settings',
			'generate_spacing_settings',
			'generate_menu_plus_settings',
			'generate_woocommerce_settings',
		);
	}

	/**
	 * Returns safely the license key.
	 */
	public static function get_license_key() {
		$license_key = get_option( 'gen_premium_license_key', '' );

		if ( $license_key && strlen( $license_key ) > 4 ) {
			$hidden_length = strlen( $license_key ) - 4;
			$safe_part = substr( $license_key, 0, 4 );
			$hidden_part = implode('', array_fill( 0, $hidden_length, '*' ) );

			return $safe_part . $hidden_part;
		}

		return $license_key;
	}

	/**
	 * Add our scripts to the page.
	 */
	public function enqueue_scripts() {
		if ( ! class_exists( 'GeneratePress_Dashboard' ) ) {
			return;
		}

		$dashboard_pages = GeneratePress_Dashboard::get_pages();
		$current_screen = get_current_screen();

		if ( in_array( $current_screen->id, $dashboard_pages ) ) {
			wp_enqueue_style(
				'generate-pro-dashboard',
				GP_PREMIUM_DIR_URL . 'dist/style-dashboard.css',
				array( 'wp-components' ),
				GP_PREMIUM_VERSION
			);

			if ( 'appearance_page_generate-options' === $current_screen->id ) {
				wp_enqueue_script(
					'generate-pro-dashboard',
					GP_PREMIUM_DIR_URL . 'dist/dashboard.js',
					array(),
					GP_PREMIUM_VERSION,
					true
				);

				wp_set_script_translations( 'generate-pro-dashboard', 'gp-premium', GP_PREMIUM_DIR_PATH . 'langs' );

				wp_localize_script(
					'generate-pro-dashboard',
					'generateProDashboard',
					array(
						'modules' => self::get_modules(),
						'exportableModules' => self::get_exportable_modules(),
						'siteLibraryUrl' => admin_url( 'themes.php?page=generatepress-library' ),
						'elementsUrl' => admin_url( 'edit.php?post_type=gp_elements' ),
						'hasWooCommerce' => class_exists( 'WooCommerce' ),
						'licenseKey' => self::get_license_key(),
						'licenseKeyStatus' => get_option( 'gen_premium_license_key_status', 'deactivated' ),
						'betaTester' => get_option( 'gp_premium_beta_testing', false ),
					)
				);
			}
		}
	}

	/**
	 * Enable beta testing if our option is set.
	 *
	 * @since 2.1.0
	 * @param boolean $value Whether beta testing is on or not.
	 */
	public function set_beta_tester( $value ) {
		if ( get_option( 'gp_premium_beta_testing', false ) ) {
			return true;
		}

		return $value;
	}

	/**
	 * Add the container for our start customizing app.
	 */
	public function module_list() {
		echo '<div id="generatepress-module-list"></div>';
	}

	/**
	 * Add the container for our start customizing app.
	 */
	public function license_key() {
		echo '<div id="generatepress-license-key"></div>';
	}

	/**
	 * Add the container for our start customizing app.
	 */
	public function import_export() {
		echo '<div id="generatepress-import-export-pro"></div>';
	}

	/**
	 * Add the container for our reset app.
	 */
	public function reset() {
		echo '<div id="generatepress-reset-pro"></div>';
	}
}

GeneratePress_Pro_Dashboard::get_instance();
