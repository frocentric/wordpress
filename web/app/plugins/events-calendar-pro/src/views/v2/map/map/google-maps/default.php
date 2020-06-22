<?php
/**
 * View: Map View - Google Maps Default
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/map/google-maps/default.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var array  $events       An array of the week events, in sequence.
 * @var object $map_provider Object with data of map provider.
 */

// Gets the first event.
$event = reset( $events );

$url = '';
// Verifies if that event has a venue.
if ( isset( $event->venues ) && $event->venues->count() ) {
	$venue = $event->venues->first();
	if ( is_numeric( $venue->geolocation->latitude ) && is_numeric( $venue->geolocation->longitude ) ) {
		/*
		 * We have to make sure the Venue has a valid latitude and longitude or its address will never resolve.
		 * Latitude and longitude 0,0 are valid coordinates, so we look for empty string.
		 */
		$url = add_query_arg(
			[
				'key' => $map_provider->api_key,
				'q'   => urlencode( $venue->geolocation->address ),
			],
			$map_provider->iframe_url
		);
	}
}
?>
<iframe
	class="tribe-events-pro-map__google-maps-default"
	data-js="tribe-events-pro-map-google-maps-default"
	src="<?php echo esc_url( $url ); ?>"
>
</iframe>
