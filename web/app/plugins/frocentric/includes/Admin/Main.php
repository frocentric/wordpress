<?php
/**
 * Handle admin hooks.
 *
 * @class       Admin
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Frocentric\Utils;

/**
 * Admin main class
 */
final class Main {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {

		Assets::hooks();
		ContentClassifierAdmin::hooks();

		add_action( 'current_screen', array( __CLASS__, 'conditional_includes' ) );
		add_action( 'init', array( __CLASS__, 'customise_error_reporting' ), 1 );
	}

	/**
	 * Include admin files conditionally.
	 *
	 * @return void
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	public static function conditional_includes() {

		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		switch ( $screen->id ) {
			case 'dashboard':
			case 'options-permalink':
			case 'users':
			case 'user':
			case 'profile':
			case 'user-edit':
		}
	}

	public static function customise_error_reporting() {
		if ( ! defined( 'ADMIN_EXCLUDED_ERROR_LEVELS' ) || ! is_array( ADMIN_EXCLUDED_ERROR_LEVELS ) ) {
			return;
		}

		Utils::customise_error_reporting( ADMIN_EXCLUDED_ERROR_LEVELS );
	}
}
