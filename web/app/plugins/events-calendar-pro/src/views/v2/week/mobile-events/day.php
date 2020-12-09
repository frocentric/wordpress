<?php
/**
 * View: Week View Mobile Events Day
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 *
 * @version 5.0.0
 *
 * @var string $day_date The day date, in the `Y-m-d` format.
 * @var array  $day The data for the day.
 * @var bool   $is_current_week boolean containing if the selected week is the current week.
 * @var string $today_date      Today's date in `Y-m-d`.
 * @var string $week_start_date The week start date, in `Y-m-d` format.
 */

$hidden      = 'true';
$day_classes = [ 'tribe-events-pro-week-mobile-events__day' ];

// If in the current week, and today's is the day.
// Or for the rest of the weeks, if it's the first day of the week.
if (
	( $is_current_week && $today_date === $day_date )
	|| ( ! $is_current_week && $day_date === $week_start_date )
) {
	$hidden        = 'false';
	$day_classes[] = 'tribe-events-pro-week-mobile-events__day--active';
}
?>

<div
	<?php tribe_classes( $day_classes ) ?>
	id="tribe-events-pro-week-mobile-events-day-<?php echo esc_attr( $day_date ); ?>"
	aria-hidden="<?php echo esc_attr( $hidden ); ?>"
>
	<?php if ( 0 === $day['found_events'] && ! empty( $day['message_mobile'] ) ) : ?>
		<?php $this->template(
			'components/messages',
			[
				'messages' => [
					'notice' => [
						$day['message_mobile']
					]
				]
			]
		); ?>
	<?php endif; ?>

	<?php foreach ( $day['event_times'] as $event_time => $time_content ) {

		if ( ! empty( $time_content['time'] ) ) {
			$this->template(
				'week/mobile-events/day/time-separator',
				[ 'time' => $time_content['time'], 'datetime' => $time_content['datetime'] ]
			);
		} else {
			$this->template(
				'week/mobile-events/day/type-separator',
				[ 'type' => $event_time ]
			);
		}

		foreach ( $time_content[ 'events' ] as $event ) {
			$this->setup_postdata( $event );
	 		$this->template( 'week/mobile-events/day/event', [ 'event' => $event ] );
	 	}
	 }
	?>

</div>
