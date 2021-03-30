<?php
/**
 * View: Map View - Single Event Recurring Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/event/date-time/recurring.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.2.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( empty( $event->recurring ) ) {
	return;
}
?>
<em
	class="tribe-events-pro-map__event-datetime-recurring-icon"
	aria-label="<?php esc_attr_e( 'Recurring', 'tribe-events-calendar-pro' ) ?>"
	title="<?php esc_attr_e( 'Recurring', 'tribe-events-calendar-pro' ) ?>"
>
	<?php $this->template( 'components/icons/recurring', [ 'classes' => [ 'tribe-events-pro-map__event-datetime-recurring-icon-svg' ] ] ); ?>
</em>
