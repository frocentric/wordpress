<?php
/**
 * View: Map View - Single Event Actions - Directions Spacer
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/actions/directions-spacer.php
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

if ( ! $event->venues->count() ) {
	return;
}
?>
<span class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt">
	<?php esc_html_e( 'Get Directions', 'tribe-events-calendar-pro' ); ?>
</span>
