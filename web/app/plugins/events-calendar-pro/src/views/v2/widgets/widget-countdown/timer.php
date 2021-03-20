<?php
/**
 * Widget: Countdown Timer
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-countdown/timer.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var boolean $event_done     A boolean of whether the event has already started.
 * @var boolean $show_seconds   Whether the widget should display seconds in the countdown.
 * @var string  $count_to_date  A date string of the event start date in ISO 8601 date format.
 * @var string  $count_to_stamp Timestamp of the event start date.
 */

if ( $event_done ) {
	return;
}
?>
<time
	class="tribe-events-widget-countdown__time"
	datetime="<?php echo esc_attr( $count_to_date ); ?>"
	data-seconds="<?php echo esc_attr( $count_to_stamp ); ?>"
	data-js="tribe-events-widget-countdown-time"
	role="timer"
>
	<div class="tribe-events-widget-countdown__number">
		<span class="tribe-events-widget-countdown__number--days" data-js="tribe-events-widget-countdown-days">DD</span>
		<span class="tribe-events-widget-countdown__under tribe-events-widget-countdown__under--days">
			<?php echo esc_html_x( 'days', 'The days duration label', 'tribe-events-calendar-pro' ); ?>
		</span>
	</div>
	<div class="tribe-events-widget-countdown__number">
		<span class="tribe-events-widget-countdown__number--hours" data-js="tribe-events-widget-countdown-hours">HH</span>
		<span class="tribe-events-widget-countdown__under tribe-events-widget-countdown__under--hours">
			<?php echo esc_html_x( 'hours', 'The hours duration label', 'tribe-events-calendar-pro' ); ?>
		</span>
	</div>
	<div class="tribe-events-widget-countdown__number">
		<span class="tribe-events-widget-countdown__number--minutes" data-js="tribe-events-widget-countdown-minutes">MM</span>
		<span class="tribe-events-widget-countdown__under tribe-events-widget-countdown__under--minutes">
			<?php echo esc_html_x( 'mins', 'The abbreviated minutes duration label', 'tribe-events-calendar-pro' ); ?>
		</span>
	</div>
	<?php if ( $show_seconds ) : ?>
		<div class="tribe-events-widget-countdown__number">
			<span class="tribe-events-widget-countdown__number--seconds" data-js="tribe-events-widget-countdown-seconds">SS</span>
			<span class="tribe-events-widget-countdown__under tribe-events-widget-countdown__under--seconds">
				<?php echo esc_html_x( 'secs', 'The abbreviated seconds duration label', 'tribe-events-calendar-pro' ); ?>
			</span>
		</div>
	<?php endif; ?>
</time>
