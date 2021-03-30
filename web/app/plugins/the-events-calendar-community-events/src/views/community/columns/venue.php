<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Venue Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/venue.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

if ( tribe_has_venue( $event->ID ) ) {
	$venue_id = tribe_get_venue_id( $event->ID );
	if ( current_user_can( 'edit_post', $venue_id ) ) {
		echo '<a href="' . esc_url( tribe( 'community.main' )->getUrl( 'edit', $venue_id, null, Tribe__Events__Main::VENUE_POST_TYPE ) ) . '">' . esc_html( tribe_get_venue( $event->ID ) ) . '</a>';
	} else {
		echo esc_html( tribe_get_venue( $event->ID ) );
	}
} else {
	echo '—';
}