<?php
/**
 * Widget: Featured Venue Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/events-list/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
?>
<h4 class="tribe-common-h7 tribe-events-widget-featured-venue__event-title">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php echo esc_attr( $event->title ); ?>"
		rel="bookmark"
		class="tribe-common-anchor-thin tribe-events-widget-featured-venue__event-title-link"
	>
		<?php
		// phpcs:ignore
		echo $event->title;
		?>
	</a>
</h4>
