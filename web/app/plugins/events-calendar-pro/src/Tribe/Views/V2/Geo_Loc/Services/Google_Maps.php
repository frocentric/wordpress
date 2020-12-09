<?php
/**
 * ${CARET}
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Views\V2\Geo_Loc\Services
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc\Services;

use Tribe__Events__Google__Maps_API_Key as API_Key;

class Google_Maps implements Service_Interface {
	/**
	 * {@inheritDoc}
	 *
	 * @since 4.7.9
	 */
	public function resolve_to_coords( $address ) {
		if ( tribe_is_using_basic_gmaps_api() ) {
			return new \WP_Error(
				'using-basic-api-key',
				__(
					'Google Maps API geocode resolution is only supported with a custom API key.',
					'tribe-events-calendar-pro'
				)
			);
		}

		$api_key = (string) tribe_get_option( API_Key::$api_key_option_name );

		$url = sprintf(
			'https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s',
			urlencode( $address ),
			urlencode( $api_key )
		);

		$response = wp_remote_get( $url );

		if ( $response instanceof \WP_Error ) {
			return $response;
		}

		$code    = wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );
		$body    = wp_remote_retrieve_body( $response );

		if ( 200 !== $code ) {
			return new \WP_Error(
				'google-maps-api-status-error',
				sprintf(
					__( 'Google Maps API replied with an error status to the request: %s', 'tribe-events-calendar-pro' ),
					$code
				),
				[
					'address'  => $address,
					'code'     => $code,
					'message'  => $message,
					'response' => $response,
				]
			);
		}

		$decoded = json_decode( $body, true );

		if ( false === $decoded ) {
			return new \WP_Error(
				'malformed-response',
				__( 'Google Maps API returned a malformed response.', 'tribe-events-calendar-pro' ),
				[
					'address'  => $address,
					'code'     => $code,
					'message'  => $message,
					'response' => $response,
					'body'     => $body,
				]
			);
		}

		$ok = array_sum( [
			isset( $decoded['results'][0]['geometry']['location']['lat'] ),
			isset( $decoded['results'][0]['geometry']['location']['lng'] ),
			isset( $decoded['results'][0]['formatted_address'] ),
			isset( $decoded['results'][0]['address_components'] ),
		] );

		if ( 4 !== $ok ) {
			return new \WP_Error(
				'google-maps-api-missing-data',
				__( 'Google Maps API response is missing some data.', 'tribe-events-calendar-pro' ),
				[
					'address'  => $address,
					'code'     => $code,
					'message'  => $message,
					'response' => $response,
					'body'     => $body,
				]
			);
		}

		$lat               = $decoded['results'][0]['geometry']['location']['lat'];
		$lng               = $decoded['results'][0]['geometry']['location']['lng'];
		$formatted_address = $decoded['results'][0]['formatted_address'];
		$address_components = $decoded['results'][0]['address_components'];

		return new Geo_Loc_Data( $address, $lat, $lng, $formatted_address, $address_components );
	}
}
