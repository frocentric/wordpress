<?php
/**
 * Provides methods for supporting (or not) Filter Bar on/with shortcodes.
 *
 * @since   5.0.2
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */

namespace Tribe\Events\Filterbar\Views\V2;

/**
 * Trait With_Shortcode_Support
 *
 * @since   5.0.2
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
trait With_Shortcode_Support {
	/**
	 * Attempt to detect reliably that we're using a shortcode.
	 *
	 * @since 5.0.2
	 *
	 * @param Tribe__Template $template Current instance of a template.

	 * @return boolean
	 */
	public static function is_using_shortcode( $template ) {
		// Try the easy ways first.
		if (  tribe_doing_shortcode() ) {
			return true;
		}

		if ( $template->get_context()->is( 'shortcode' ) ) {
			return true;
		}

		// Now the hard way.
		$values = $template->get_values();

		// Sanity check. This should never happen.
		if ( ! isset( $values['url'] ) ) {
			return false;
		}

		$query_string = wp_parse_url( $values['url'], PHP_URL_QUERY );

		parse_str( $query_string, $params );

		if ( ! empty( $params['shortcode'] ) ) {
			return true;
		}

		return false;
	}
}
