<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Organizer Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/organizer.php
 *
 * @since  4.5
 * @version 4.5
 */

if ( tribe_has_organizer( $event->ID ) ) {
	$organizer_id = tribe_get_organizer_id( $event->ID );

	if ( current_user_can( 'edit_post', $organizer_id ) ) {
		echo '<a href="' . esc_url( tribe( 'community.main' )->getUrl( 'edit', $organizer_id, null, Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) . '">' . esc_html( tribe_get_organizer( $event->ID ) ) . '</a>';
	} else {
		echo esc_html( tribe_get_organizer( $event->ID ) );
	}
} else {
	echo '—';
}