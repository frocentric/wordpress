<?php
/**
 * View: Week View - Event Tooltip Date
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event/tooltip/date.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

use Tribe__Date_Utils as Dates;
$event_date_attr = $event->dates->start->format( Dates::DBDATEFORMAT );

?>
<div class="tribe-events-pro-week-grid__event-tooltip-datetime">
	<?php $this->template( 'week/grid-body/events-day/event/tooltip/date/featured' ); ?>
	<time datetime="<?php echo esc_attr( $event_date_attr ); ?>">
		<?php echo $event->schedule_details->value(); ?>
	</time>
	<?php $this->template( 'week/grid-body/events-day/event/tooltip/date/recurring', [ 'event' => $event ] ); ?>
</div>
