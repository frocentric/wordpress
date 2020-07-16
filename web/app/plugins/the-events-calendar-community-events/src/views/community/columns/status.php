<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * My Events Column for Status Display
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/columns/status.php
 *
 * @since  4.5
 * @version 4.5
 */

$post_status = $event->post_status;
$status_label = get_post_status_object( $post_status );

if ( ! empty( $status_label ) ) {
	$status_label = $status_label->label;
	printf(
		'<div class="event-status"><span class="hover">%s</span>%s</div>',
		esc_html( $status_label ),
		tribe( 'community.main' )->getEventStatusIcon( $post_status )
	);
}