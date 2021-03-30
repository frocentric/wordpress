<?php
/**
 * View: Week View - Event Tooltip Description
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event/tooltip/description.php
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
if ( empty( (string) $event->excerpt ) ) {
	return;
}
?>
<div class="tribe-events-pro-week-grid__event-tooltip-description tribe-common-b3">
	<?php echo (string) $event->excerpt; ?>
</div>
