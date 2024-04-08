<?php
/**
 * Handle front hooks.
 *
 * @class       Front
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Frocentric\Utils;

/**
 * Front main class
 */
final class Main {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		Assets::hooks();
		add_action( 'init', array( __CLASS__, 'customise_error_reporting' ), 1 );
	}

	public static function customise_error_reporting() {
		if ( ! defined( 'FRONTEND_EXCLUDED_ERROR_LEVELS' ) || ! is_array( FRONTEND_EXCLUDED_ERROR_LEVELS ) ) {
			return;
		}

		Utils::customise_error_reporting( FRONTEND_EXCLUDED_ERROR_LEVELS );
	}
}
