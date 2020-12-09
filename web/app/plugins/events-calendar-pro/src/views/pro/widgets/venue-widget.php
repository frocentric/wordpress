<?php
/**
 * Events Pro Venue Widget
 * This is the template for the output of the venue widget.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/widgets/venue-widget.php
 *
 * @version 4.4
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_plural = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

?>

<div class="tribe-venue-widget-wrapper">
	<div class="tribe-venue-widget-venue">
		<div class="tribe-venue-widget-venue-name">
			<?php echo tribe_get_venue_link( $venue_ID ); ?>
		</div>
		<?php if ( has_post_thumbnail( $venue_ID ) ) { ?>
			<div class="tribe-venue-widget-thumbnail">
				<?php echo get_the_post_thumbnail( $venue_ID, 'related-event-thumbnail' ); ?>
			</div>
		<?php } ?>
		<div class="tribe-venue-widget-address">
			<?php echo tribe_get_full_address( $venue_ID, true ) ?>
		</div>
	</div>
	<?php if ( 0 === $events->post_count ): ?>
		<?php printf( __( 'No upcoming %s.', 'tribe-events-calendar-pro' ), $events_label_plural_lowercase ); ?>
	<?php else: ?>
		<?php do_action( 'tribe_events_venue_widget_before_the_list' ); ?>
		<ul class="tribe-venue-widget-list">
			<?php while ( $events->have_posts() ): ?>
				<?php $events->the_post(); ?>
				<li class="<?php tribe_events_event_classes() ?>">
					<?php
					if (
						tribe( 'tec.featured_events' )->is_featured( get_the_ID() )
						&& get_post_thumbnail_id( get_the_ID() )
					) {
						/**
						 * Fire an action before the venue widget featured image
						 */
						do_action( 'tribe_events_list_venue_before_the_event_image' );

						/**
						 * Allow the default post thumbnail size to be filtered
						 *
						 * @param $size
						 */
						$thumbnail_size = apply_filters( 'tribe_events_venue_widget_thumbnail_size', 'post-thumbnail' );
						?>
						<div class="tribe-event-image">
							<?php the_post_thumbnail( $thumbnail_size ); ?>
						</div>
						<?php

						/**
						 * Fire an action after the venue widget featured image
						 */
						do_action( 'tribe_events_venue_widget_after_the_event_image' );
					}
					?>
					<h4 class="tribe-event-title">
						<a href="<?php echo esc_url( tribe_get_event_link() ); ?>"><?php echo get_the_title( get_the_ID() ) ?></a>
					</h4>
					<?php echo tribe_events_event_schedule_details() ?>
					<?php if ( tribe_get_cost( get_the_ID() ) != '' ): ?>
						<span class="tribe-events-divider">|</span>
						<span class="tribe-events-event-cost">
						<?php echo tribe_get_cost( get_the_ID(), true ); ?>
					</span>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php do_action( 'tribe_events_venue_widget_after_the_list' ); ?>
	<?php endif; ?>

	<a href="<?php echo esc_url( tribe_get_venue_link( $venue_ID, false ) ); ?>"><?php printf( __( 'View all %1$s at this %2$s', 'tribe-events-calendar-pro' ), $events_label_plural, tribe_get_venue_label_singular() ); ?></a>
</div>
