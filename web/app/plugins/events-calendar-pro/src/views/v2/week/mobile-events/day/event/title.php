<?php
/**
 * View: Week View - Mobile Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 */
$event_id = $event->ID;
?>
<h3 class="tribe-events-pro-week-mobile-events__event-title tribe-common-h6 tribe-common-h5--min-medium">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php the_title_attribute( $event_id ); ?>"
		rel="bookmark"
		class="tribe-events-pro-week-mobile-events__event-title-link tribe-common-anchor-thin"
	>
		<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
	</a>
</h3>
