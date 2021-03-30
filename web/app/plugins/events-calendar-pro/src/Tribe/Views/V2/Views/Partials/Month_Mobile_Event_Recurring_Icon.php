<?php
/**
 * Manages the month mobile event recurring icon the Views V2 implementation.
 */

namespace Tribe\Events\Pro\Views\V2\Views\Partials;

use Tribe\Events\Views\V2\Interfaces\View_Partial_Interface;

/**
 * Class Month_Mobile_Event_Recurring_Icon
 *
 * @since   4.7.8
 * @package Tribe\Events\Pro\Views\V2\Views\Partials
 */
class Month_Mobile_Event_Recurring_Icon implements View_Partial_Interface {
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
		return $template->template( 'month/mobile-event/recurring', $template->get_values() );
	}
}
