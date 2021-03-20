<?php
/**
 * View: Map View - Single Event Actions - Details
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/actions/details.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.3
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
?>
<a
	href="<?php echo esc_url( $event->permalink ); ?>"
	title="<?php the_title_attribute( $event->ID ); ?>"
	rel="bookmark"
	class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt"
	data-js="tribe-events-pro-map-event-actions-link-details"
>
	<?php
	echo esc_html(
		sprintf(
			/* translators: %s: Event (singular) */
			__( '%s Details', 'tribe-events-calendar-pro' ),
			tribe_get_event_label_singular()
		)
	);
	?>
</a>
