<?php
/**
 * Widget: Countdown Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-countdown/event-title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
?>
<h3 class="tribe-common-h6 tribe-events-widget-countdown__event-title">
	<a
		class="tribe-common-anchor-thin tribe-events-widget-countdown__event-title-link"
		href="<?php echo esc_url( $event->permalink ); ?>"
	>
		<?php echo esc_html( $event->title ); ?>
	</a>
</h3>
