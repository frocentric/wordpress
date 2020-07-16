<?php
/**
 * View: Week View - Grid Body
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var array $multiday_events     An array of each day multi-day events and more event count, if any, in the shape
 *                                 `[ <Y-m-d> => [ 'events' => [ ...$multiday_events], 'more_events' => <int> ] ]`.
 * @var bool  $has_multiday_events Boolean whether the week has multiday events or not.
 * @var array $events              An array of each day non multi-day events, if any, in the shape `[ <Y-m-d> => [ ...$events ] ]`.
 */
?>
<div class="tribe-events-pro-week-grid__body" role="rowgroup">

	<?php if ( count( $multiday_events ) && $has_multiday_events ) : ?>

		<div class="tribe-events-pro-week-grid__multiday-events-row-outer-wrapper">
			<div class="tribe-events-pro-week-grid__multiday-events-row-wrapper">
				<div
					class="tribe-events-pro-week-grid__multiday-events-row"
					data-js="tribe-events-pro-week-multiday-events-row"
					role="row"
				>

					<?php $this->template( 'week/grid-body/multiday-events-row-header' ); ?>

					<?php foreach ( $multiday_events as $day => list( $day_multiday_events, $more_events ) ) : ?>
						<?php $this->template(
							'week/grid-body/multiday-events-day',
							[ 'day' => $day, 'events' => $day_multiday_events, 'more_events' => $more_events ]
						); ?>
					<?php endforeach; ?>

				</div>
			</div>
		</div>

	<?php endif; ?>

	<div class="tribe-events-pro-week-grid__events-scroll-wrapper">
		<div class="tribe-events-pro-week-grid__events-row-outer-wrapper" data-js="tribe-events-pro-week-grid-events-row-outer-wrapper">
			<div class="tribe-events-pro-week-grid__events-row-wrapper" data-js="tribe-events-pro-week-grid-events-row-wrapper">
				<div class="tribe-events-pro-week-grid__events-row" role="row">

					<?php $this->template( 'week/grid-body/events-row-header' ); ?>

					<?php foreach ( $events as $day => $day_events ) : ?>
						<?php $this->template( 'week/grid-body/events-day', [ 'events' => $day_events ] ); ?>
					<?php endforeach; ?>

				</div>
			</div>
		</div>
	</div>

</div>
