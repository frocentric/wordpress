<?php
/**
 * A Geo Location handler that handles geocode resolution by comparing the request location to a fixed set of location
 * and coordinates.
 *
 * This handler should be used in testing, or when the list of location users can search is known, and resolved, before
 * hand.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc;

use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Geo_Loc_Data;
use Tribe__Context as Context;
use Tribe__Events__Pro__Geo_Loc as Fencer;

/**
 * Class Static_Handler
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */
class Static_Handler extends Base_Handler implements Handler_Interface {
	/**
	 * A static array of locations the object should use to statically resolve a location to a set of coordinates.
	 *
	 * @since 4.7.9
	 *
	 * @var array
	 */
	protected $locations = [];

	/**
	 * The levenshtein distance that should be used when trying to compare locations.
	 *
	 * @since 4.7.9
	 *
	 * @var int
	 */
	protected $match_by_levenshtein_distance = 3;

	/**
	 * Whether to search by sub string when looking for matching locations in the set, or not.
	 *
	 * @since 4.7.9
	 *
	 * @var bool
	 */
	protected $match_by_substr = true;

	/**
	 * Static_Handler constructor.
	 *
	 * @since 4.7.9
	 *
	 * @param Fencer $fencer    An instance of the object used to fence Venues.
	 * @param array  $locations An array of locations the handler should use.
	 */
	public function __construct( Fencer $fencer, $locations = [] ) {
		parent::__construct( $fencer );
		$this->locations = $locations;
	}

	/**
	 * Sets the array of locations the handler should use to try and attempt the resolutions.
	 *
	 * @since 4.7.9
	 *
	 * @param array $locations An array of locations to use for the resolutions.
	 */
	public function set_locations( array $locations = [] ) {
		$this->locations = $locations;
	}

	/**
	 * {@inheritDoc}
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

		$geo_loc_data = $this->search_locations( $location );

		/** @var \WP_Error $geo_loc_data */
		if ( $geo_loc_data instanceof \WP_Error ) {
			do_action( 'tribe_log', 'error', 'Geocoding_Handler', [
				'action'  => 'static_location_resolution_failure',
				'code'    => $geo_loc_data->get_error_code(),
				'message' => $geo_loc_data->get_error_message(),
				'data'    => $geo_loc_data->get_error_data(),
			] );

			return $repository_args;
		}

		do_action( 'tribe_log', 'debug', 'Geocoding_Handler', [
			'action' => 'static_location_resolution_success',
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

	/**
	 * Searches the locations set for a match.
	 *
	 * @since 4.7.9
	 *
	 * @param string $location The location to try and match.
	 *
	 * @return Geo_Loc_Data|\WP_Error Either the matched location data, or an error to indicate the location did not
	 *                                match any in the set.
	 */
	protected function search_locations( $location ) {
		$key = false;

		if ( ! empty( $this->locations ) ) {
			$key = $this->find_location( $location );
		}

		if ( false === $key ) {
			return new \WP_Error(
				'location-not-found',
				__( 'Location' ),
				[ 'location' => $location ]
			);
		}

		return Geo_Loc_Data::from_array( $this->locations[ $key ] );
	}

	/**
	 * Finds the location in the location set, trying to match it to the location keys in different ways.
	 *
	 * @since 4.7.9
	 *
	 * @param string $location The location to search for.
	 *
	 * @return string|false Either the matching location key, or `false` on failure.
	 */
	protected function find_location( $location ) {
		$location = strtolower( trim( $location ) );

		if ( array_key_exists( $location, $this->locations ) ) {
			return $location;
		}

		if ( $this->match_by_levenshtein_distance > 0 ) {
			$key = $this->search_by_levenshtein_distance( $location );
			if ( false !== $key ) {
				return $key;
			}
		}

		if ( true === $this->match_by_substr ) {
			$key = $this->search_by_substr( $location );
			if ( false !== $key ) {
				return $key;
			}
		}

		return false;
	}

	/**
	 * Tries to match the location using the Levenshtein distance.
	 *
	 * @since 4.7.9
	 *
	 * @param string $location The location to match.
	 *
	 * @return string|false Either the matched location, or `false` if not found.
	 */
	protected function search_by_levenshtein_distance( $location ) {
		$similar_locations = array_reduce( array_keys( $this->locations ), function ( array $acc, $candidate ) use ( $location ) {
			$distance = levenshtein( $candidate, $location );

			if ( $distance > $this->match_by_levenshtein_distance ) {
				return $acc;
			}

			$acc[ $candidate ] = $distance;

			return $acc;
		}, [] );

		if ( count( $similar_locations ) ) {
			asort( $similar_locations );

			return array_keys( $similar_locations )[0];
		}

		return false;
	}

	/**
	 * Tries to match the location as substring of a set location.
	 *
	 * @since 4.7.9
	 *
	 * @param string $location The location to match.
	 *
	 * @return string|false Either the matched location, or `false` if not found.
	 */
	protected function search_by_substr( $location ) {
		foreach ( $this->locations as $key => $candidate ) {
			if ( false !== strpos( $key, $location ) ) {
				return $key;
			}
		}

		return false;
	}

	/**
	 * Sets the Levenshtein distance that should be used to try and find matching locations.
	 *
	 * @since 4.7.9
	 *
	 * @param int $levenshtein_distance The Levenshtein distance that should be used to try and find matching locations.
	 *                                  If set to 0, then only exact, case-insensitive, matches will do.
	 */
	public function match_by_levenshtein_distance( $match_by_levenshtein_distance ) {
		$this->match_by_levenshtein_distance = $match_by_levenshtein_distance;
	}

	/**
	 * Sets whether to try and match locations by substring or not.
	 *
	 * @since 4.7.9
	 *
	 * @param bool $match_by_substr Whether to try and match locations by substring or not.
	 */
	public function match_by_substr( $match_by_substr ) {
		$this->match_by_substr = $match_by_substr;
	}
}
