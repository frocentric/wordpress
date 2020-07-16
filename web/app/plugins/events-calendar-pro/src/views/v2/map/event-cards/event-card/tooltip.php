<?php
/**
 * View: Map View - Tooltip
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/tooltip.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var array $events_by_venue A array with events grouped by Venue, it uses the Venue ID as index.
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var array $events An array of the events, in sequence.
 *
 * @see tribe_get_event() For the format of the event object.
 */
$selected_venue_obj = null;
foreach( $events_by_venue as $venue_obj ) {
	if ( in_array( $event->ID, $venue_obj->event_ids ) ) {
		$selected_venue_obj = $venue_obj;
		break;
	}
}

if ( empty( $selected_venue_obj ) ) {
	return;
}

$has_multiple_events = count( $selected_venue_obj->event_ids ) > 1;
$classes = [ 'tribe-events-pro-map__event-tooltip' ];
$classes['tribe-events-pro-map__event-tooltip--has-slider'] = $has_multiple_events;
?>
<script
	type="text/template"
	class="tribe-events-pro-map__event-tooltip-template"
	data-js="tribe-events-pro-map-event-tooltip-template"
>
	<div <?php tribe_classes( $classes ); ?>>
		<?php if ( $has_multiple_events ) : ?>
			<div
				class="tribe-events-pro-map__event-tooltip-slider-container swiper-container"
				data-js="tribe-events-pro-map-event-tooltip-slider"
			>
				<div class="tribe-events-pro-map__event-tooltip-slider-wrapper swiper-wrapper tribe-common-g-row">

					<?php $slide_index = 0; ?>
					<?php foreach( $events as $tooltip_event ) : ?>
						<?php
						// Skip any events not in this particular venue.
						if ( ! in_array( $tooltip_event->ID, $selected_venue_obj->event_ids ) ) {
							continue;
						}
						?>
						<div
							class="tribe-events-pro-map__event-tooltip-slide swiper-slide tribe-common-g-col"
							data-js="tribe-events-pro-map-event-tooltip-slide"
							data-event-id="<?php echo esc_attr( $tooltip_event->ID ); ?>"
							data-slide-index="<?php echo esc_attr( $slide_index ); ?>"
						>
							<?php $this->template( 'map/event-cards/event-card/tooltip/date-time', [ 'event' => (object) $tooltip_event ] ); ?>
							<?php $this->template( 'map/event-cards/event-card/tooltip/title', [ 'event' => (object) $tooltip_event ] ); ?>
							<?php $this->template( 'map/event-cards/event-card/tooltip/venue', [ 'event' => (object) $tooltip_event ] ); ?>
							<?php $this->template( 'map/event-cards/event-card/tooltip/cost', [ 'event' => (object) $tooltip_event ] ); ?>
						</div>
						<?php $slide_index += 1; ?>
					<?php endforeach; ?>

				</div>

				<?php $this->template( 'map/event-cards/event-card/tooltip/navigation' ); ?>
			</div>
		<?php else : ?>
			<?php $this->template( 'map/event-cards/event-card/tooltip/date-time', [ 'event' => $event ] ); ?>
			<?php $this->template( 'map/event-cards/event-card/tooltip/title', [ 'event' => $event ] ); ?>
			<?php $this->template( 'map/event-cards/event-card/tooltip/venue', [ 'event' => $event ] ); ?>
			<?php $this->template( 'map/event-cards/event-card/tooltip/cost', [ 'event' => $event ] ); ?>
		<?php endif; ?>
	</div>
</script>
