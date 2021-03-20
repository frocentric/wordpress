<?php
/**
 * View: Week View - Multiday Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day/multiday-event.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @var WP_Post $event           The current event post object.
 * @var string  $day             The current date being rendered, in `Y-m-d` format.
 * @var string  $week_start_date The week start date, in `Y-m-d` format.
 * @var string  $today_date      Today's date, in the `Y-m-d` format.
 *
 * @see tribe_get_event() for the additional properties added to the event post object.
 *
 * @version 5.1.1
 */

$classes = \Tribe\Events\Pro\Views\V2\week_view_multiday_classes( $event, $day, $week_start_date, $today_date );

$should_display = in_array( $day, $event->displays_on, true ) || ( ! $event->starts_this_week && $week_start_date === $day );
?>
<div class="tribe-events-pro-week-grid__multiday-event-wrapper">
	<article <?php tribe_classes( $classes ) ?> data-event-id="<?php echo esc_attr( $event->ID ); ?>">
		<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/hidden', [ 'event' => $event ] ); ?>
		<?php
		// Either the event starts today or it starts earlier and this day is the first day of the week.
		if ( $should_display ) {
			$this->template(
				'week/grid-body/multiday-events-day/multiday-event/bar',
				[
					'day'             => $day,
					'event'           => $event,
					'week_start_date' => $week_start_date,
				]
			);
			$this->template( 'week/grid-body/events-day/event/tooltip', [ 'event' => $event ] );
		}
		?>
	</article>
</div>
