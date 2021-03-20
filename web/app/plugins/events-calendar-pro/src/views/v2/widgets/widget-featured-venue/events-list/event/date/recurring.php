<?php
/**
 * Widget: Featured Venue Event Recurring Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/events-list/event/date/recurring.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.3.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.2.0
 */

if ( empty( $event->recurring ) ) {
	return;
}
$recurring_label = _x(
	'Recurring',
	'Recurring label for event in featured venue widget.',
	'tribe-events-calendar-pro'
);

?>
<a
	href="<?php echo esc_url( $event->permalink_all ); ?>"
	class="tribe-events-widget-featured-venue__event-datetime-recurring-link"
>
	<em
		class="tribe-events-widget-featured-venue__event-datetime-recurring-icon"
		aria-label="<?php echo esc_attr( $recurring_label ); ?>"
		title="<?php echo esc_attr( $recurring_label ); ?>"
	>
		<?php $this->template( 'components/icons/recurring', [ 'classes' => [ 'tribe-events-widget-featured-venue__event-datetime-recurring-icon-svg' ] ] ); ?>
	</em>
</a>
