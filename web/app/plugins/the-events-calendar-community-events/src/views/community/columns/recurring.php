<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Recurring Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/recurring.php
 *
 * @since  4.5
 * @version 4.5
 */

if ( ! function_exists( 'tribe_is_recurring_event' ) ) {
	return;
}

if ( tribe_is_recurring_event( $event->ID ) ) {
	esc_html_e( 'Yes', 'tribe-events-community' );
} else {
	esc_html_e( 'No', 'tribe-events-community' );
}