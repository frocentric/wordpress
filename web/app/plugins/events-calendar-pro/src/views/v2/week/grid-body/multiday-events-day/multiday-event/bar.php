<?php
/**
 * View: Week View - Single Multiday Event Bar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day/multiday-event/bar.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.1.1
 *
 * @var string  $day             The current date being rendered, in `Y-m-d` format.
 * @var string  $week_start_date The week start date, in `Y-m-d` format.
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

?>
<div class="tribe-events-pro-week-grid__multiday-event-bar">
	<div class="tribe-events-pro-week-grid__multiday-event-bar-inner">
		<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/bar/featured', [ 'event' => $event ] ); ?>
		<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/bar/title', [ 'event' => $event ] ); ?>
		<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/bar/recurring', [ 'event' => $event ] ); ?>
	</div>
</div>
