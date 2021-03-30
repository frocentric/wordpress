<?php
/**
 * View: Map View - Single Event Date/Time
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/event/date-time.php
 *
 * See more documentation about our views templating system.
 *
 * @since 5.0.1
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @link https://evnt.is/1aiy
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var obj     $date_formats Object containing the date formats.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

use Tribe__Date_Utils as Dates;

$time_format = tribe_get_time_format();
$display_end_time = $event->dates->start_display->format( 'H:i' ) !== $event->dates->end_display->format( 'H:i' );

if ( $event->multiday ) {
	$start      = $event->schedule_details->value();
	$start_attr = $event->dates->start_display->format( Dates::DBDATEFORMAT );
} elseif ( $event->all_day ) {
	$start      = __( 'All Day', 'tribe-events-calendar-pro' );
	$start_attr = $event->dates->start_display->format( Dates::DBDATEFORMAT );
} else {
	$start      = $event->dates->start_display->format( $time_format );
	$start_attr = $event->dates->start_display->format( Dates::DBTIMEFORMAT );
	$end        = $event->dates->end_display->format( $time_format );
	$end_attr   = $event->dates->end_display->format( Dates::DBTIMEFORMAT );
}
?>
<div class="tribe-events-pro-map__event-datetime-wrapper tribe-common-b2 tribe-common-b3--min-medium">
	<?php $this->template( 'map/event-cards/event-card/event/date-time/featured' ); ?>
	<time
		class="tribe-events-pro-map__event-start-datetime"
		datetime="<?php echo esc_attr( $start_attr ); ?>"
	>
		<?php if ( $event->multiday ) : ?>
			<?php echo $start; ?>
		<?php else : ?>
			<?php echo esc_html( $start ); ?>
		<?php endif; ?>
	</time>
	<?php if ( ( ! $event->all_day && ! $event->multiday ) && $display_end_time ) : ?>
		<span class="tribe-events-pro-map__event-datetime-separator"><?php echo esc_html( $date_formats->time_range_separator ); ?></span>
		<time
			class="tribe-events-pro-map__event-end-datetime"
			datetime="<?php echo esc_attr( $end_attr ); ?>"
		>
			<?php echo esc_html( $end ); ?>
		</time>
	<?php endif; ?>
	<?php $this->template( 'map/event-cards/event-card/event/date-time/recurring', [ 'event' => $event ] ); ?>
</div>
