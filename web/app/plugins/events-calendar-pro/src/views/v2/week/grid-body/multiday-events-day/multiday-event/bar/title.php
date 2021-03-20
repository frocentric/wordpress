<?php
/**
 * View: Week View - Single Multiday Event Bar Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day/multiday-event/bar/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.1.1
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

?>
<h3 class="tribe-events-pro-week-grid__multiday-event-bar-title tribe-common-h8 tribe-common-h--alt">
	<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
</h3>
