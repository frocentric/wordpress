<?php
/**
 * View: Week View - Event Featured Image
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event/featured-image.php
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
 */
// Only display the featured image on events that last more than 2:30h
$should_display = $event->duration > 2.5 * HOUR_IN_SECONDS;

if ( ! $should_display || empty( $event->featured ) || ! $event->thumbnail->exists ) {
	return;
}
?>
<div class="tribe-events-pro-week-grid__event-featured-image-wrapper">
	<div class="tribe-events-pro-week-grid__event-featured-image tribe-common-c-image tribe-common-c-image--bg">
		<div
			class="tribe-common-c-image__bg"
			style="background-image: url('<?php echo $event->thumbnail->full->url; ?>');"
			role="img"
			aria-label="<?php echo esc_attr( get_the_title( $event->ID ) ); ?>"
		>
		</div>
	</div>
</div>
