<?php
/**
 * View: Venue meta - Map
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/map/map.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $venue The venue post object.
 * @var object $map_provider Object with data of map provider.
 *
 */

$url = '';
// Verifies if that event has a venue.
if ( ! empty( $venue->geolocation->address ) ) {
	$url = add_query_arg(
		[
			'key' => $map_provider->api_key,
			'q'   => urlencode( $venue->geolocation->address ),
		],
		$map_provider->iframe_url
	);
}

?>
<iframe
	class="tribe-events-pro-venue__meta-data-google-maps-default"
	src="<?php echo esc_url( $url ); ?>"
>
</iframe>
