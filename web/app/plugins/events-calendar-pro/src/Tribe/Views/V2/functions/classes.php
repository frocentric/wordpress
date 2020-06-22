<?php
/**
 * Calendar Class Functions.
 *
 * @since 5.1.1
 */

namespace Tribe\Events\Pro\Views\V2;

// Don't load directly!
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
	return;
}

/**
 * Used in the multiday week loop.
 * Outputs classes for the multiday event (article).
 *
 * @since 5.1.1
 *
 * @param WP_Post $event            An event post object with event-specific properties added from the the `tribe_get_event`
 *                                  function.
 * @param string  $day         The `Y-m-d` date of the day currently being displayed.
 * @param bool    $week_start_date Whether the current grid day being rendered is the first day of the week or not.
 * @param string  $today_date       Today's date in the `Y-m-d` format.
 *
 * @return array<string> $classes   The classes to add to the multiday event.
 */
function week_view_multiday_classes( $event, $day, $week_start_date, $today_date ) {
	$should_display = in_array( $day, $event->displays_on, true )
				  || ( ! $event->starts_this_week && $week_start_date === $day );

	$classes = [ 'tribe-events-pro-week-grid__multiday-event' ];

	if ( ! empty( $event->featured ) ) {
		$classes[] = 'tribe-events-pro-week-grid__multiday-event--featured';
	}

	// An event is considered "past" when it ends before the start of today.
	if ( $event->dates->end_display->format( 'Y-m-d' ) < $today_date ) {
		$classes[] = 'tribe-events-pro-week-grid__multiday-event--past';
	}

	if ( ! $should_display ) {
		$classes = get_post_class( $classes, $event->ID );

		return apply_filters( 'tribe_events_pro_views_v2_month_multiday_classes', $classes, $event, $day, $week_start_date, $today_date );
	}

	$classes[] = 'tribe-events-pro-week-grid__multiday-event--width-' . $event->this_week_duration;
	$classes[] = 'tribe-events-pro-week-grid__multiday-event--display';

	if ( $event->starts_this_week ) {
		$classes[] = 'tribe-events-pro-week-grid__multiday-event--start';
	}

	if ( $event->ends_this_week ) {
		$classes[] = 'tribe-events-pro-week-grid__multiday-event--end';
	}

	$classes = get_post_class( $classes, $event->ID );

	return apply_filters( 'tribe_events_pro_views_v2_month_multiday_classes', $classes, $event, $day, $week_start_date, $today_date );
}
