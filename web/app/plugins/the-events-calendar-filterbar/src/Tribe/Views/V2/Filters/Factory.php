<?php
/**
 * Handles the construction and set up of filters for a context.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe\Events\Views\V2\View;
use Tribe\Events\Filterbar\Views\V2\Filters;
use Tribe__Context as Context;
use Tribe__Events__Filterbar__Filter as Filter;
use Tribe__Utils__Array as Arr;
use WP_REST_Request as Request;

/**
 * Class Factory
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Factory {
	/**
	 * Builds and sets up the filters required by the repository arguments.
	 *
	 * Filters are built only if needed and attached to the specific context to avoid side-effects.
	 *
	 * @since 4.9.0
	 *
	 * @param array   $args    The repository arguments to filter.
	 * @param Context $context The context to build the filters for.
	 *
	 * @return array The filtered repository arguments.
	 */
	public function for_repository_args( array $args, Context $context ) {
		$this->build_for_context( $context, true );

		/*
		 * When the context is destroyed, the hash could be re-used.
		 * The chance of collision here is really slim and further avoided as in filters we check for has AND type.
		 */
		$args['context_hash'] = spl_object_hash( $context );

		return $args;
	}

	/**
	 * Modifies the rest Params to reflect required modifications to make filter work as expected.
	 *
	 * @since 4.9.0
	 *
	 * @param array   $params   The Rest params to filter.
	 * @param Request $request  WP Rest Request used.
	 *
	 * @return array The filtered params.
	 */
	public function for_rest_params( array $params, Request $request ) {
		$params_map = static::context_to_filters_map();
		$is_form_submit = tribe_is_truthy( Arr::get( $params, [ 'view_data', 'form_submit' ], false ) );

		foreach ( $params_map as $key => $filter_class ) {
			$tribe_key = 'tribe_' . $key;
			$context_key = tribe_context()->get_read_key_for( $key, Context::QUERY_VAR );

			// Prevent resetting when a link was clicked.
			if ( ! $is_form_submit ) {
				if ( ! empty( $params[ $context_key ] ) ) {
					$params[ $key ] = $params[ $context_key ];
					$params[ $tribe_key ] = $params[ $context_key ];
				}
				continue;
			}

			// If it's empty we need to reset in all places.
			if ( empty( $params['view_data'][ $tribe_key ] ) ) {
				// Now reset all 3 points of interest for the filterbar values.
				$params['view_data'][ $tribe_key ] = null;
				$params[ $tribe_key ] = null;
				$params[ $context_key ] = null;
				continue;
			} else {
				// When not empty we replace that value on the other two places.
				$params[ $tribe_key ] = $params['view_data'][ $tribe_key ];
				$params[ $context_key ] = $params['view_data'][ $tribe_key ];
			}
		}

		return $params;
	}

	/**
	 * Returns a map relating the filters context keys with their class.
	 *
	 * @since 4.9.0
	 *
	 * @return array A map relating the filters context keys with their class.
	 */
	public static function context_to_filters_map() {
		$map = [
			'filterbar_category'       => Category::class,
			'filterbar_cost'           => Cost::class,
			'filterbar_tag'            => Tag::class,
			'filterbar_venue'          => Venue::class,
			'filterbar_organizer'      => Organizer::class,
			'filterbar_day_of_week'    => Day_Of_Week::class,
			'filterbar_time_of_day'    => Time_Of_Day::class,
			'filterbar_country'        => Country::class,
			'filterbar_city'           => City::class,
			'filterbar_state_province' => State::class,
			'filterbar_featured'       => Featured_Events::class,
		];

		if ( class_exists( 'Tribe__Events__Pro__Geo_Loc_Filter' ) ) {
			$map['filterbar_geofence_distance'] = Distance::class;
		}

		if ( class_exists( 'Tribe__Events__Pro__Custom_Meta' ) ) {
			$map['filterbar_additional_fields'] = Additional_Field::class;
		}

		/**
		 * Filters the map relating Context keys to Filter Bar filters.
		 *
		 * We build the filters depending on the value associated with each Context key.
		 * Removing a key will, in fact prevent that filter from being built.
		 *
		 * @since 4.9.0
		 *
		 * @param array<string,string> $map The map relating Context keys to Filter Bar filters. It has shape
		 *                                  `[ <context_key> => <filter_class> ]`.
		 */
		$map = apply_filters( 'tribe_events_filter_bar_context_to_filter_map', $map );

		return $map;
	}

	/**
	 * Builds each filter according to the current context to display it.
	 *
	 * @since 4.9.0
	 *
	 * @param Context|null $context The context of the display; defaults to the global current one if not set.
	 */
	public function for_display( Context $context = null ) {
		$context = $context ?: tribe_context();
		$filters = $this->build_for_context( $context, static function ( $key ) use ( $context ) {
			if ( 'filterbar_geofence_distance' !== $key ) {
				// By default do not skip the filters.
				return false;
			}

			// Skip the Distance filter if there is no location search.
			$geoloc_search = $context->get( 'geoloc_search', false );

			return empty( $geoloc_search );
		} );

		usort( $filters, static function ( Filter $filter_a, Filter $filter_b ) {
			if ( $filter_a->priority == $filter_b->priority ) {
				return 0;
			}

			return ( $filter_a->priority < $filter_b->priority ) ? - 1 : 1;
		} );

		/** @var \Tribe__Events__Filterbar__Filter $filter */
		foreach ( $filters as $filter ) {
			$filter->displayFilter();
		}
	}

	/**
	 * Builds an instance of each filter, attaching it to the specified context.
	 *
	 * @since 4.9.0
	 *
	 * @param Context $context     The context to attach each instance to.
	 * @param bool|callable $skip_filter Whether to build the filter or not; if this value is `true` then the
	 *                                   filter will be not be built if the value the filter works on is empty. If this
	 *                                   parameter is a callable then it will be called for each filter and passed the
	 *                                   filter `$context_key` as first argument and the controlled `$value` as second
	 *                                   argument, the callback should return a boolean value.
	 *
	 * @return array An array of the built filters in the shape `[ <filter_context_key> => <filter_instance> ]`.
	 */
	public function build_for_context( Context $context, $skip_filter = true ) {
		$filter_context_keys = static::context_to_filters_map();
		$built_filters       = [];

		foreach ( $filter_context_keys as $filter_context_key => $filter_class ) {
			$value = $context->get( $filter_context_key, false );

			$skip_this_filter = is_callable( $skip_filter )
				? $skip_filter( $filter_context_key, $value )
				: empty( $value ) && $skip_filter;

			if ( $skip_this_filter ) {
				continue;
			}

			if ( $filter_context_key === 'filterbar_additional_fields' ) {
				if ( ! $skip_this_filter ) {
					$active_additional_fields = Context_Filter::get_available_additional_fields();

					$value = array_combine(
						$active_additional_fields,
						array_map( static function ( $key ) use ( $context ) {
							return $context->get( $key, $context->get( 'tribe_' . $key, null ) );
						}, $active_additional_fields )
					);
				}

				// We need to add a filter instance for each value.
				foreach ( $value as $k => $v ) {
					if ( ! Context_Filter::is_available( $filter_class, $k ) ) {
						// Never build a filter if the filter is not available at all.
						continue;
					}

					/** @var Additional_Field $filter */
					$filter = call_user_func( [ $filter_class, 'build_for_context' ], $context, $k );
					$filter->set_meta_key( preg_replace( '/^tribe_/', '', $k ) );
					add_action( 'tribe_repository_events_query', [ $filter, 'filter_query' ] );
					$built_filters[ $k ] = $filter;
				}
			} else {
				if ( ! Context_Filter::is_available( $filter_class, $filter_context_key ) ) {
					// Never build a filter if the filter is not available at all.
					continue;
				}

				$filter = call_user_func( [ $filter_class, 'build_for_context' ], $context, $filter_context_key );
				add_action( 'tribe_repository_events_query', [ $filter, 'filter_query' ] );
				$built_filters[ $filter_context_key ] = $filter;
			}
		}

		return $built_filters;
	}
}
