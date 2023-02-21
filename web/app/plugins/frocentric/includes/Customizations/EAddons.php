<?php
/**
 * e-addons Hooks
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
 * EAddons Class.
 */
class EAddons {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
			add_filter( 'e_addons/dynamic', [ __CLASS__, 'e_addons_dynamic' ], 10, 3 );
		}
	}

	/**
	 * Parses API fields from the e-addons link field
	 * Tokens are in the format {{field_name}}
	 */
	public static function e_addons_dynamic( $value, $fields = [], $urlencode = false ) {
		if ( ! array_key_exists( 'block', $fields ) ) {
			return $value;
		}

		$api_fields = $fields['block'];
		$value = preg_replace_callback(
			'/(\{\{\s*(\w+)\s*\}\})/',
			function ( $matches ) use ( $urlencode, $api_fields ) {
				$value = '';

				if ( isset( $api_fields[ $matches[2] ] ) ) {
					$value = $api_fields[ $matches[2] ];
				}

				if ( $urlencode ) {
					$value = urlencode( $value );
				}

				return $value;
			}, $value
		);

		return $value;
	}
}
