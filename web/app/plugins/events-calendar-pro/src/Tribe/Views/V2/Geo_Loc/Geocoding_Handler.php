<?php
/**
 * A Geo Location handler that handles the fencing expecting to find the geo location search string in the context to,
 * then, resolve it to a set of latitude and longitude coordinates using a service, if available.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc;

use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Service_Interface as Service;
use Tribe__Context as Context;
use Tribe__Events__Pro__Geo_Loc as Fencer;

/**
 * Class Geocoding_Handler
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */
class Geocoding_Handler extends Base_Handler implements Handler_Interface {
	/**
	 * An instance of the API service.
	 *
	 * @since 4.7.9
	 *
	 * @var Service
	 */
	protected $service;

	/**
	 * Geocoding_Handler constructor.
	 *
	 * @param Fencer  $fencer  An instance of the Fencer, used to filter out venues not matching.
	 * @param Service $service An instance of the API service.
	 */
	public function __construct( Fencer $fencer, Service $service ) {
		parent::__construct( $fencer );
		$this->service = $service;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.7.9
	 */
	public function filter_repository_args( array $repository_args = [], Context $context = null ) {
		$context = $context ?: tribe_context();

		$location = $context->get( 'geoloc_search', false );

		if ( false === $location ) {
			return $repository_args;
		}

		// If there are no venues to start with, then do not even bother making a request and do not fence at all.
		if ( ! tribe_venues()->found() ) {
			return $repository_args;
		}

		$geo_loc_data = $this->service->resolve_to_coords( $location );

		/** @var \WP_Error $geo_loc_data */
		if ( $geo_loc_data instanceof \WP_Error ) {
			do_action( 'tribe_log', 'error', 'Geocoding_Handler', [
				'action' => 'geocode_resolution_failure',
				'code'    => $geo_loc_data->get_error_code(),
				'message' => $geo_loc_data->get_error_message(),
				'data'    => $geo_loc_data->get_error_data(),
			] );

			$repository_args['void_query'] = true;

			return $repository_args;
		}

		do_action( 'tribe_log', 'debug', 'Geocoding_Handler', [
			'action' => 'geocode_resolution_success',
			'data'   => $geo_loc_data->to_array(),
		] );

		$lat = $geo_loc_data->get_lat();
		$lng = $geo_loc_data->get_lng();

		$context->safe_set( [
			'geoloc_lat' => $lat,
			'geoloc_lng' => $lng,
		] );

		$venues = $this->fencer->get_venues_in_geofence( $lat, $lng );

		if ( $venues ) {
			$repository_args['venue'] = (array) $venues;
		} else {
			$repository_args['void_query'] = true;
		}

		return $repository_args;
	}
}
