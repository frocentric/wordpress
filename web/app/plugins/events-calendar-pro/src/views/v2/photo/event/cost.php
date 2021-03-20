<?php
/**
 * View: Photo View - Single Event Cost
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/cost.php
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

if ( empty( $event->cost ) ) {
	return;
}
?>
<div class="tribe-events-c-small-cta tribe-common-b3 tribe-events-pro-photo__event-cost">
	<span class="tribe-events-c-small-cta__price">
		<?php echo esc_html( $event->cost ); ?>
	</span>
</div>
