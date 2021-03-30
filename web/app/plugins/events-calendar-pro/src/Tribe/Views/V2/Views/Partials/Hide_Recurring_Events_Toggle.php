<?php
/**
 * Manages the toggle to hide recurring events for the Views V2 implementation.
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2\Views\Partials;
 */

namespace Tribe\Events\Pro\Views\V2\Views\Partials;

use Tribe\Events\Views\V2\Interfaces\View_Partial_Interface;
use Tribe\Events\Views\V2\Views\Month_View;
use Tribe\Events\Pro\Views\V2\Views\All_View;
use Tribe\Events\Pro\Views\V2\Views\Week_View;

/**
 * Class Hide_Recurring_Events_Toggle
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2\Views\Partials;
 */
class Hide_Recurring_Events_Toggle implements View_Partial_Interface {
	/**
	 * Renders the "Hide recurring events" toggle in the View.
	 *
	 * @since  4.7.5
	 *
	 * @param \Tribe__Template $template Current instance of the `Tribe__Template` that's being rendered.
	 *
	 * @return string The rendered partial HTML code.
	 */
	public function render( \Tribe__Template $template ) {
		if ( Month_View::class === $template->get( 'view_class' ) ) {
			return '';
		}

		if ( Week_View::class === $template->get( 'view_class' ) ) {
			return '';
		}
		if ( All_View::class === $template->get( 'view_class' ) ) {
			return '';
		}

		if ( ! $template->get( 'display_recurring_toggle' ) ) {
			return '';
		}

		return $template->template( 'recurrence/hide-recurring', $template->get_values() );
	}
}
