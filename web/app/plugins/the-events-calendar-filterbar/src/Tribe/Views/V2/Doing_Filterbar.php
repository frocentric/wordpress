<?php
/**
 * Provides methods to model and take care of the "doing Filter Bar" context.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */

namespace Tribe\Events\Filterbar\Views\V2;

/**
 * Trait Doing_Filterbar
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */
trait Doing_Filterbar {

	/**
	 * Runs a function "doing Filter Bar".
	 *
	 * The "doing Filter Bar" function means collecting and taking care of a curated context and filter setup that
	 * allows the Filter Bar components to render correctly.
	 *
	 * @since 5.0.0
	 *
	 * @param callable $do The callback to execute in the curated context.
	 *
	 * @return mixed The callback result, if any.
	 */
	protected function doing_filterbar( callable $do ) {
		// The Latest Past View will filter out a number of components not in its safelist, take care of that.
		add_filter( 'tribe_events_latest_past_view_display_template', '__return_true', 100 );

		$result = $do();

		// Restore the Latest Past Events View filter.
		remove_filter( 'tribe_events_latest_past_view_display_template', '__return_true', 100 );

		return $result;
	}
}
