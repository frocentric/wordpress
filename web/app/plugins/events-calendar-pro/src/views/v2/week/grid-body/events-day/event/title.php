<?php
/**
 * View: Week View - Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
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
?>
<h3 class="tribe-events-pro-week-grid__event-title tribe-common-h8 tribe-common-h--alt">
	<?php $this->template( 'week/grid-body/events-day/event/title/featured' ); ?>
	<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
</h3>
