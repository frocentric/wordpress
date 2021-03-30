<?php
/**
 * View: Map View - Single Event Actions - Details Spacer
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/actions/details-spacer.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.3
 *
 */
?>
<span class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt">
	<?php
	echo esc_html(
		sprintf(
			/* translators: %s: Event (singular) */
			__( '%s Details', 'tribe-events-calendar-pro' ),
			tribe_get_event_label_singular()
		)
	);
	?>
</span>
