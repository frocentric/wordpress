<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Recurring Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/recurring.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

if ( ! function_exists( 'tribe_is_recurring_event' ) ) {
	return;
}

if ( tribe_is_recurring_event( $event->ID ) ) {
	esc_html_e( 'Yes', 'tribe-events-community' );
} else {
	esc_html_e( 'No', 'tribe-events-community' );
}