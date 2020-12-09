<?php
/**
 * View: Map View - Event Card
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.3
 *
 * @var WP_Post $event        The event post object with properties added by the `tribe_get_event` function.
 * @var object  $map_provider Object with data of map provider.
 * @var int     $index        The index of the event card, starting from 0.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */

$wrapper_classes = [ 'tribe-events-pro-map__event-card-wrapper' ];

$wrapper_classes['tribe-events-pro-map__event-card-wrapper--featured']      = $event->featured;
$wrapper_classes['tribe-events-pro-map__event-card-wrapper--has-thumbnail'] = $event->thumbnail->exists;

$article_classes = get_post_class( [ 'tribe-events-pro-map__event-card' ], $event->ID );

$data_src_attr = '';

if ( empty( $map_provider->is_premium ) ) {
	$wrapper_classes['tribe-events-pro-map__event-card-wrapper--active'] = 0 === $index;

	if ( $event->venues->count() ) {
		$venue = $event->venues->first();
		if ( is_numeric( $venue->geolocation->latitude ) && is_numeric( $venue->geolocation->longitude ) ) {
			/*
			 * We have to make sure the Venue has a valid latitude and longitude or its address will never resolve.
			 * Latitude and longitude 0,0 are valid coordinates, so we look for empty string.
			 */
			$iframe_url = add_query_arg( [
				'key' => $map_provider->api_key,
				'q'   => urlencode( $venue->geolocation->address ),
			], $map_provider->iframe_url );

			$data_src_attr = 'data-src="' . esc_url( $iframe_url ) . '"';
		}
	}
}

$aria_selected = $aria_expanded = ! $map_provider->is_premium && ( 0 === $index ) ? 'true' : 'false';
$aria_controls = 'tribe-events-pro-map-event-actions-' . $event->ID;

/* The calculations below are to get the required data for the aria label */
/*
 * If the request date is after the event start date, show the request date to avoid users from seeing dates "in the
 * past" in relation to the date they requested (or today's date).
 */
$display_date = empty( $is_past ) && ! empty( $request_date )
	? max( $event->dates->start_display, $request_date )
	: $event->dates->start_display;

/* Event date tag is used in the aria label */
$event_date_tag = $display_date->format( 'M j.' );

$time_format = tribe_get_time_format();
$display_end_time = $event->dates->start_display->format( 'H:i' ) !== $event->dates->end_display->format( 'H:i' );

if ( $event->multiday ) {
	$start_date = $event->dates->start_display->format( 'F j' );
	$end_date   = $event->dates->end_display->format( 'F j' );

	if ( $event->all_day ) {
		/* Event date time is used in the aria label */
		$event_date_time = sprintf( __( '%1$s to %2$s.', 'tribe-events-calendar-pro' ), $start_date, $end_date );
	} else {
		$start_time = $event->dates->start_display->format( $time_format );
		$end_time   = $event->dates->end_display->format( $time_format );

		/* Event date time is used in the aria label */
		$event_date_time = sprintf( __( '%1$s at %2$s to %3$s at %4$s.', 'tribe-events-calendar-pro' ), $start_date, $start_time, $end_date, $end_time );
	}
} elseif ( $event->all_day ) {
	/* Event date time is used in the aria label */
	$event_date_time = __( 'All Day.', 'tribe-events-calendar-pro' );
} else {
	$start_time = $event->dates->start_display->format( $time_format );

	if ( $display_end_time ) {
		$end_time = $event->dates->end_display->format( $time_format );

		/* Event date time is used in the aria label */
		$event_date_time = sprintf( __( '%1$s to %2$s.', 'tribe-events-calendar-pro' ), $start_time, $end_time );
	} else {
		/* Event date time is used in the aria label */
		$event_date_time = sprintf( __( '%1$s.', 'tribe-events-calendar-pro' ), $start_time );
	}
}

/* Event title is used in the aria label */
$event_title = wp_kses_post( get_the_title( $event->ID ) );

$aria_label = sprintf(
	/* translators: %1$s: event date tag, in M j. format, %2$s: event date time, %3$s: event title, %4$s: event (singular) */
	__( '%1$s %2$s %3$s. Click to select %4$s.', 'tribe-events-calendar-pro' ),
	$event_date_tag,
	$event_date_time,
	$event_title,
	tribe_get_event_label_singular_lowercase()
);
?>
<div
	<?php tribe_classes( $wrapper_classes ) ?>
	<?php echo $data_src_attr; ?>
	data-js="tribe-events-pro-map-event-card-wrapper"
	data-event-id="<?php echo esc_attr( $event->ID ); ?>"
>

	<button
		class="tribe-events-pro-map__event-card-button"
		data-js="tribe-events-pro-map-event-card-button"
		aria-selected="<?php echo esc_attr( $aria_selected ); ?>"
		aria-controls="<?php echo esc_attr( $aria_controls ); ?>"
		aria-expanded="<?php echo esc_attr( $aria_expanded ); ?>"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
	>
		<article <?php tribe_classes( $article_classes ); ?>>
			<div class="tribe-common-g-row tribe-events-pro-map__event-row">

				<?php $this->template( 'map/event-cards/event-card/date-tag', [ 'event' => $event ] ); ?>

				<?php $this->template( 'map/event-cards/event-card/event', [ 'event' => $event, 'index' => $index ] ); ?>

			</div>
		</article>
	</button>

	<div class="tribe-events-pro-map__event-card-spacer">
		<div class="tribe-common-g-row tribe-events-pro-map__event-row-spacer">
			<div class="tribe-common-g-col tribe-events-pro-map__event-wrapper-spacer">
				<div class="tribe-common-g-row tribe-events-pro-map__event-spacer">
					<div class="tribe-common-g-col tribe-events-pro-map__event-details-spacer">
						<?php $this->template( 'map/event-cards/event-card/actions', [ 'event' => $event, 'index' => $index, 'linked' => true ] ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php $this->template( 'map/event-cards/event-card/tooltip', [ 'event' => $event ] ); ?>

</div>
