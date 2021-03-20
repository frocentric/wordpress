<?php
/**
 * View: Week View - Event Date
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event/date.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 * @since 5.2.0 Updated the template to use and support the time format set in WordPress General Settings.
 *
 * @var WP_Post $event        The event post object with properties added by the `tribe_get_event` function.
 * @var obj     $date_formats Object containing the date formats.
 * @var string  $time_format  The format used to display the time.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

$display_end_date = $event->dates->start_display->format( 'H:i' ) !== $event->dates->end_display->format( 'H:i' );
?>

<div class="tribe-events-pro-week-grid__event-datetime">
	<?php $this->template( 'week/grid-body/events-day/event/date/featured' ); ?>
	<time datetime="<?php echo $event->dates->start_display->format( 'H:i' ); ?>">
		<?php echo esc_html( $event->dates->start_display->format( $time_format ) ); ?>
	</time>
	<?php if ( $display_end_date ) : ?>
		<span class="tribe-events-pro-week-grid__event-datetime-separator"><?php echo esc_html( $date_formats->time_range_separator ); ?></span>
		<time datetime="<?php echo $event->dates->end_display->format( 'H:i' ) ?>">
			<?php echo esc_html( $event->dates->end_display->format( $time_format ) ); ?>
		</time>
	<?php endif; ?>
	<?php $this->template( 'week/grid-body/events-day/event/date/recurring', [ 'event' => $event ] ); ?>
</div>
