<?php
/**
 * Manages the location search field the Views V2 implementation.
 */

namespace Tribe\Events\Pro\Views\V2\Views\Partials;

use Tribe\Events\Views\V2\Interfaces\View_Partial_Interface;

/**
 * Class Location_Search_Field
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2\Views\Partials
 */
class Location_Search_Field implements View_Partial_Interface {
	/**
	 * Renders the "Location" search field in the View.
	 *
	 * @since  4.7.5
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 *
	 * @return string
	 */
	public function render( \Tribe__Template $template ) {
		$disable_tribe_bar    = tribe_is_truthy( tribe_get_option( 'tribeDisableTribeBar', false ) );
		$hide_location_search = tribe_is_truthy( tribe_get_option( 'hideLocationSearch', false ) );

		if ( $disable_tribe_bar || $hide_location_search || tribe_is_using_basic_gmaps_api() ) {
			return '';
		}

		return $template->template( 'location/form-field', $template->get_values() );
	}
}
