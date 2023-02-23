<?php
/**
 * Handle plugin's install actions.
 *
 * @class       Install
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install class
 */
final class Install {

	/**
	 * Install action.
	 */
	public static function install() {

		// Perform install actions here.
		// Trigger action.
		do_action( 'frocentric_installed' );
	}
}
