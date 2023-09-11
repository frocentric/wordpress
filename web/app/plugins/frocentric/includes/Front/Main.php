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
		add_action( 'init', array( __CLASS__, 'customise_error_reporting' ) );
	}

	public static function customise_error_reporting() {
		if ( ! defined( 'EXCLUDED_ERROR_LEVELS' ) || ! is_array( EXCLUDED_ERROR_LEVELS ) ) {
			return;
		}

		$error_mask = E_ALL;
		foreach ( EXCLUDED_ERROR_LEVELS as $level_name ) {
			if ( defined( $level_name ) ) {
				$level = constant( $level_name );

				if ( is_numeric( $level ) ) {
					$error_mask &= ~$level;
				}
			}
		}
		// error_reporting(E_ALL);
		// error_reporting(E_NOTICE);
		// error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
		error_reporting( $error_mask );
	}
}
