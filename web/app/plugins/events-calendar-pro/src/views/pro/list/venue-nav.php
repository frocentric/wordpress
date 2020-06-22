<?php
/**
 * Single Venue View Nav Template
 * This file loads the single Venue view navigation.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/venue-nav.php
 *
 * @package TribeEventsCalendar
 * @version 4.7.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_plural = tribe_get_event_label_plural();

global $wp_query;

$page     = $wp_query->get( 'paged', 1 );
$venue_id = Tribe__Utils__Array::get( $wp_query->get( 'meta_query' ), [ '_eventvenueid_in', 'value' ], 0 );
?>

<nav class="tribe-events-nav-pagination" aria-label="<?php echo esc_html( sprintf( esc_html__( '%s List Navigation', 'tribe-events-calendar-pro' ), $events_label_plural ) ); ?>">
	<ul class="tribe-events-sub-nav">
		<!-- Left Navigation -->

		<?php if ( tribe_has_previous_event() ) : ?>
			<li class="<?php echo esc_attr( tribe_left_navigation_classes() ); ?>" aria-label="previous events link">
				<a href="<?php echo esc_url( tribe_venue_previous_events_link( $page, $venue_id ) ); ?>" rel="prev"><?php printf( '<span>&laquo;</span> ' . esc_html__( 'Previous %s', 'tribe-events-calendar-pro' ), $events_label_plural ); ?></a>

			</li><!-- .tribe-events-nav-left -->
		<?php endif; ?>

		<!-- Right Navigation -->
		<?php if ( tribe_venue_has_next_events( $page, $venue_id ) ) : ?>
			<li class="<?php echo esc_attr( tribe_right_navigation_classes() ); ?>" aria-label="next events link">
				<a href="<?php echo esc_url( tribe_venue_next_events_link( $page, $venue_id ) ); ?>" rel="next"><?php printf( esc_html__( 'Next %s', 'tribe-events-calendar-pro' ), $events_label_plural . ' <span>&raquo;</span>' ); ?></a>
			</li><!-- .tribe-events-nav-right -->
		<?php endif; ?>
	</ul>
</nav>