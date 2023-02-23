<?php
/**
 * Ninja Forms Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants as Constants;
use Frocentric\Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NinjaForms Class.
 */
class NinjaForms {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
			add_filter( 'ninja_forms_post_run_action_type_redirect', array( __CLASS__, 'ninja_forms_post_run_action_type_redirect' ), 10, 3 );
		}
	}

	/**
	 * Hooks onto ninja_forms_post_run_action_type_redirect to correctly decode redirection URL argument
	 * Fixes URL provided when logging in from Discourse
	 */
	public static function ninja_forms_post_run_action_type_redirect( $data ) {
		if ( array_key_exists( 'actions', $data ) && array_key_exists( 'redirect', $data['actions'] ) ) {
			$data['actions']['redirect'] = htmlspecialchars_decode( $data['actions']['redirect'] );
		}

		return $data;
	}
}
