<?php
/**
 * Main class.
 *
 * @package  Frocentric
 * @version  1.0.0
 */

namespace Frocentric;

use Frocentric\Constants;
use Frocentric\Admin\Main as Admin;
use Frocentric\Front\Main as Front;

/**
 * Base Plugin class holding generic functionality
 */
final class Main {

	/**
	 * Constructor
	 */
	public static function bootstrap() {

		register_activation_hook( PLUGIN_FILE, array( Install::class, 'install' ) );

		add_action( 'plugins_loaded', array( __CLASS__, 'load' ) );

		add_action( 'init', array( __CLASS__, 'init' ) );

		// Perform other actions when plugin is loaded.
		do_action( 'frocentric_loaded' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'frocentric' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'frocentric' ), '1.0.0' );
	}

	/**
	 * Include plugins files and hook into actions and filters.
	 *
	 * @since  1.0.0
	 */
	public static function load() {

		if ( ! self::check_plugin_requirements() ) {
			return;
		}

		if ( Utils::is_request( Constants::ADMIN_REQUEST ) ) {
			Admin::hooks();
		}

		if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
			Front::hooks();
		}

		// Common includes.
		Block::hooks();

		Customizations\Discourse::hooks();
		Customizations\EAddons::hooks();
		Customizations\Elementor::hooks();
		Customizations\Feedzy::hooks();
		Customizations\GeneratePress::hooks();
		Customizations\NinjaForms::hooks();
		Customizations\Tribe::hooks();

		// Set up localisation.
		self::load_plugin_textdomain();

		// Init action.
		do_action( 'frocentric_loaded' );
	}

	/**
	 * Method called by init hook
	 *
	 * @return void
	 */
	public static function init() {

		// Before init action.
		do_action( 'before_frocentric_init' );

		// Add needed hooks here.
		// After init action.
		do_action( 'frocentric_init' );
	}

	/**
	 * Checks all plugin requirements. If run in admin context also adds a notice.
	 *
	 * @return boolean
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	private static function check_plugin_requirements() {

		$errors = array();
		global $wp_version;

		if ( ! version_compare( PHP_VERSION, Constants::PLUGIN_REQUIREMENTS['php_version'], '>=' ) ) {
			/* Translators: The minimum PHP version */
			$errors[] = sprintf( esc_html__( 'Frocentric Platform requires a minimum PHP version of %s.', 'frocentric' ), self::PLUGIN_REQUIREMENTS['php_version'] );
		}

		if ( ! version_compare( $wp_version, Constants::PLUGIN_REQUIREMENTS['wp_version'], '>=' ) ) {
			/* Translators: The minimum WP version */
			$errors[] = sprintf( esc_html__( 'Frocentric Platform requires a minimum WordPress version of %s.', 'frocentric' ), self::PLUGIN_REQUIREMENTS['wp_version'] );
		}

		if ( empty( $errors ) ) {
			return true;
		}

		if ( Utils::is_request( Constants::ADMIN_REQUEST ) ) {

			add_action(
				'admin_notices',
				function () use ( $errors ) {
					?>
					<div class="notice notice-error">
						<?php
						foreach ( $errors as $error ) {
							echo '<p>' . esc_html( $error ) . '</p>';
						}
						?>
					</div>
					<?php
				}
			);

			return;
		}

		return false;
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/frocentric/frocentric-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/frocentric-LOCALE.mo
	 */
	private static function load_plugin_textdomain() {

		// Add plugin's locale.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'frocentric' );

		load_textdomain( 'frocentric', WP_LANG_DIR . '/frocentric/frocentric-' . $locale . '.mo' );

		load_plugin_textdomain( 'frocentric', false, plugin_basename( __DIR__ ) . '/i18n/languages' );
	}
}
