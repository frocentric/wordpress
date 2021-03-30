<?php
/**
 * View: Week View - Mobile Event Date
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/event/date.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @var WP_Post $event The event post object, decorated with additional properties by the `tribe_get_event` function.
 *
 * @see tribe_get_event() for the additional properties added to the event post object.
 *
 * @version 5.1.1
 */

?>
<div class="tribe-events-pro-week-mobile-events__event-datetime-wrapper tribe-common-b2">
	<?php $this->template( 'week/mobile-events/day/event/date/featured', [ 'event' => $event ] ); ?>
	<time
		class="tribe-events-pro-week-mobile-events__event-datetime"
		datetime="<?php echo esc_attr( $event->dates->start_display->format( 'c' ) ); ?>"
	>
		<?php echo $event->schedule_details->escaped(); // Already escaped. ?>
	</time>
	<?php $this->template( 'week/mobile-events/day/event/date/recurring', [ 'event' => $event ] ); ?>
</div>
