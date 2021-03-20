<?php
/**
 * Handles the filtering of the Context to add Filter Bar specific locations.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Service_Providers
 */

namespace Tribe\Events\Filterbar\Service_Providers;

/**
 * Class Context
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Service_Providers
 */
class Context extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9.0
	 */
	public function register() {
		add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );
	}

	/**
	 * Filters the context locations to add the ones used by The Events Calendar Filter Bar.
	 *
	 * @since 4.9.0
	 *
	 * @param array $locations The array of context locations.
	 *
	 * @return array The modified context locations.
	 */
	public function filter_context_locations( array $locations = [] ) {
		$get_fb_val_from_view_data = static function ( $key ) {
			return static function ( $view_data ) use ( $key ) {
				return ! empty( $view_data[ 'tribe_filterbar_' . $key ] ) ? $view_data[ 'tribe_filterbar_' . $key ] : null;
			};
		};

		$get_fb_additional_fields_from_data = static function ( $data_source ) {
			if ( empty( $data_source ) ) {
				return [];
			}

			// As we filter, we also sanitize and update the value.
			$is_ecp_custom_field = static function ( &$value, $key ) {
				return 0 === strpos( $key, 'tribe__ecp_custom_' )
					? (bool) tribe_sanitize_deep( $value )
					: false;
			};

			$values = array_filter( $data_source, $is_ecp_custom_field, ARRAY_FILTER_USE_BOTH );

			// Add a fallback and look into the URL.
			if ( ! empty( $data_source['url'] ) && empty( $values ) ) {
				$query_string = parse_url( $data_source['url'], PHP_URL_QUERY );
				parse_str( (string) $query_string, $query_args );
				$values = array_filter( (array) $query_args, $is_ecp_custom_field, ARRAY_FILTER_USE_BOTH );
			}

			return empty( $values ) ? \Tribe__Context::NOT_FOUND : $values;
		};

		$locations = array_merge( $locations, [
			'filterbar_cost'              => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_cost' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_cost' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'cost' ) ],
				],
			],
			'filterbar_venue'             => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_venues' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_venues' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'venue' ) ],
				],
			],
			'filterbar_day_of_week'       => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_dayofweek' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_dayofweek' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'day_of_week' ) ],
				],
			],
			'filterbar_time_of_day'       => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_timeofday' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_timeofday' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'time_of_day' ) ],
				],
			],
			'filterbar_country'           => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_country' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_country' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'country' ) ],
				],
			],
			'filterbar_city'              => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_city' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_city' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'city' ) ],
				],
			],
			'filterbar_featured'          => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_featuredevent' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_featuredevent' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'featured' ) ],
				],
			],
			'filterbar_category'          => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_eventcategory' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_eventcategory' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'category' ) ],
				],
			],
			'filterbar_tag'               => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_tags' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_tags' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'tag' ) ],
				],
			],
			'filterbar_organizer'         => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_organizers' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_organizers' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'organizer' ) ],
				],
			],
			'filterbar_state_province'    => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_state' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_state' ],
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_val_from_view_data( 'state_province' ) ],
				],
			],
			'filterbar_geofence_distance' => [
				'read' => [
					\Tribe__Context::QUERY_VAR     => [ 'tribe_geofence' ],
					\Tribe__Context::REQUEST_VAR   => [ 'tribe_geofence' ],
					\Tribe__Context::LOCATION_FUNC => [
						'view_data',
						$get_fb_val_from_view_data( 'geofence_distance' )
					],
				],
			],
			'filterbar_additional_fields' => [
				'read' => [
					\Tribe__Context::FUNC          => static function () use ( $get_fb_additional_fields_from_data ) {
						return $get_fb_additional_fields_from_data( $_REQUEST );
					},
					\Tribe__Context::LOCATION_FUNC => [ 'view_data', $get_fb_additional_fields_from_data ],
				],
			],
		] );

		return $locations;
	}
}
