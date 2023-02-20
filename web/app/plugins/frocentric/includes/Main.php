<?php
/**
 * Main class.
 *
 * @package  Frocentric
 * @version  1.0.0
 */

namespace Frocentric;

use Frocentric\Admin\Main as Admin;
use Frocentric\Front\Main as Front;

/**
 * Base Plugin class holding generic functionality
 */
final class Main {

	/**
	 * Set the minimum required versions for the plugin.
	 */
	const PLUGIN_REQUIREMENTS = [
		'php_version' => '8.0',
		'wp_version'  => '6.0',
	];

	/**
	 * Constructor
	 */
	public static function bootstrap() {

		register_activation_hook( PLUGIN_FILE, [ Install::class, 'install' ] );

		add_action( 'plugins_loaded', [ __CLASS__, 'load' ] );

		add_action( 'init', [ __CLASS__, 'init' ] );

		// Perform other actions when plugin is loaded.
		do_action( 'plugin_name_loaded' );
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

		if ( Utils::is_request( 'admin' ) ) {
			Admin::hooks();
		}

		if ( Utils::is_request( 'frontend' ) ) {
			Front::hooks();
		}

		// Common includes.
		Block::hooks();

		Customizations\Elementor::hooks();

		// Set up localisation.
		self::load_plugin_textdomain();

		// Init action.
		do_action( 'plugin_name_loaded' );
	}

	/**
	 * Method called by init hook
	 *
	 * @return void
	 */
	public static function init() {

		// Before init action.
		do_action( 'before_plugin_name_init' );

		// Add needed hooks here.

		// After init action.
		do_action( 'plugin_name_init' );
	}

	/**
	 * Checks all plugin requirements. If run in admin context also adds a notice.
	 *
	 * @return boolean
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	private static function check_plugin_requirements() {

		$errors = [];
		global $wp_version;

		if ( ! version_compare( PHP_VERSION, self::PLUGIN_REQUIREMENTS['php_version'], '>=' ) ) {
			/* Translators: The minimum PHP version */
			$errors[] = sprintf( esc_html__( 'Frocentric Platform requires a minimum PHP version of %s.', 'frocentric' ), self::PLUGIN_REQUIREMENTS['php_version'] );
		}

		if ( ! version_compare( $wp_version, self::PLUGIN_REQUIREMENTS['wp_version'], '>=' ) ) {
			/* Translators: The minimum WP version */
			$errors[] = sprintf( esc_html__( 'Frocentric Platform requires a minimum WordPress version of %s.', 'frocentric' ), self::PLUGIN_REQUIREMENTS['wp_version'] );
		}

		if ( empty( $errors ) ) {
			return true;
		}

		if ( Utils::is_request( 'admin' ) ) {

			add_action(
				'admin_notices',
				function() use ( $errors ) {
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

		load_plugin_textdomain( 'frocentric', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}
}
