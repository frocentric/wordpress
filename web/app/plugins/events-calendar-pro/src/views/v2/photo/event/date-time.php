<?php
/**
 * View: Photo View - Single Event Date Time
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/date-time.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @since 5.0.0
 * @since 5.1.1 Moved icons out to separate templates.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var obj     $date_formats Object containing the date formats.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

$time_format = tribe_get_time_format();
$display_end_date = $event->dates->start_display->format( 'H:i' ) !== $event->dates->end_display->format( 'H:i' );
?>
<div class="tribe-events-pro-photo__event-datetime tribe-common-b2">
	<?php $this->template( 'photo/event/date-time/featured' ); ?>
	<?php if ( $event->all_day ) : ?>
		<time datetime="<?php echo esc_attr( $event->dates->start_display->format( 'Y-m-d' ) ) ?>">
			<?php esc_attr_e( 'All day', 'tribe-events-calendar-pro' ); ?>
		</time>
	<?php elseif ( $event->multiday ) : ?>
		<?php echo $event->schedule_details->value(); ?>
	<?php else : ?>
		<time datetime="<?php echo esc_attr( $event->dates->start_display->format( 'H:i' ) ) ?>">
			<?php echo esc_html( $event->dates->start_display->format( $time_format ) ) ?>
		</time>
		<?php if ( $display_end_date ) : ?>
			<span class="tribe-events-events-pro-photo__event-datetime-separator"><?php echo esc_html( $date_formats->time_range_separator ); ?></span>
			<time datetime="<?php echo esc_attr( $event->dates->end_display->format( 'H:i' ) ) ?>">
				<?php echo esc_html( $event->dates->end_display->format( $time_format ) ) ?>
			</time>
		<?php endif; ?>
	<?php endif; ?>
	<?php $this->template( 'photo/event/date-time/recurring', [ 'event' => $event ] ); ?>
</div>
