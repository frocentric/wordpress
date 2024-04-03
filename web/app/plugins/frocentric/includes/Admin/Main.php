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
}
