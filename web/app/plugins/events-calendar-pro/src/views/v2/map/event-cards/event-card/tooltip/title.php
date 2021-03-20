<?php
/**
 * View: Map View - Tooltip Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/tooltip/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
?>
<h3 class="tribe-events-pro-map__event-tooltip-title tribe-common-h7">
	<a
		href="<?php echo esc_url( $event->permalink ) ?>"
		title="<?php echo esc_attr( get_the_title( $event->ID ) ); ?>"
		rel="bookmark"
		class="tribe-events-pro-map__event-tooltip-title-link tribe-common-anchor-thin"
	>
		<?php echo esc_html( get_the_title( $event->ID ) ); ?>
	</a>
</h3>
