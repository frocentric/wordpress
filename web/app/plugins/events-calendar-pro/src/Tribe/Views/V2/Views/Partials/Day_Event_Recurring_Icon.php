<?php
/**
 * Manages the day event recurring icon the Views V2 implementation.
 */

namespace Tribe\Events\Pro\Views\V2\Views\Partials;

use Tribe\Events\Views\V2\Interfaces\View_Partial_Interface;

/**
 * Class Day_Event_Recurring_Icon
 *
 * @since   4.7.8
 * @package Tribe\Events\Pro\Views\V2\Views\Partials
 */
class Day_Event_Recurring_Icon implements View_Partial_Interface {
	/**
	 * Renders the recurring icon in the View.
	 *
	 * @since  4.7.8
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 *
	 * @return string
	 */
	public function render( \Tribe__Template $template ) {
		return $template->template( 'day/event/recurring', $template->get_values() );
	}
}
