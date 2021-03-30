<?php
/**
 * View: Week View - Multiday Events Day
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array  $events              An array of events in this day stack.
 * @var string $day                 The current date being rendered, in `Y-m-d` format.
 * @var int    $multiday_min_toggle The number we should be displaying the multiday toggle on.
 * @var int    $more_events         The number of events not showing in the stack due to toggle settings.
 */

$should_display_more_link = count( $events ) > $multiday_min_toggle;

// This will be the toggle id for this day.
$mutiday_day_toggle_id = 'tribe-events-pro-multiday-toggle-day-' . $day;
?>
<div class="tribe-events-pro-week-grid__multiday-events-day" role="gridcell">

	<?php foreach ( $events as $key => $event ) : ?>

		<?php if ( $should_display_more_link && ( $multiday_min_toggle === $key ) ) : ?>
			<?php /* If this is the third event and the toggle is 3,then show this. */ ?>
			<div
				id="<?php echo esc_attr( $mutiday_day_toggle_id ); ?>"
				data-js="tribe-events-pro-week-multiday-accordion"
				class="tribe-events-pro-week-grid__multiday-overflow-events"
			>
		<?php endif; ?>

		<?php
		if ( false === $event ) {
			$this->template( 'week/grid-body/multiday-events-day/multiday-event-spacer' );
			continue;
		}

		$this->setup_postdata( $event );

		$this->template( 'week/grid-body/multiday-events-day/multiday-event', [ 'event' => $event, 'day' => $day ] );
		?>
	<?php endforeach; ?>

	<?php if ( $should_display_more_link ) : ?>
		<?php /* This closes the `tribe-events-pro-week-grid__multiday-overflow-events` element. */ ?>
		</div>

		<?php $this->template( 'week/grid-body/multiday-events-day/more-events', [ 'more_events' => $more_events ] ); ?>
	<?php endif; ?>

</div>
