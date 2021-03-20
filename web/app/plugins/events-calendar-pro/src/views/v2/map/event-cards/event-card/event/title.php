<?php
/**
 * View: Map View - Single Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
?>
<h3 class="tribe-events-pro-map__event-title tribe-common-h8 tribe-common-h7--min-medium">
	<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
</h3>
