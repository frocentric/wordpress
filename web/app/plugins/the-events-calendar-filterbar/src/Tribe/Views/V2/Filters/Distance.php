<?php
/**
 * An implementation of the Distance filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Distance
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Distance extends \Tribe__Events__Pro__Geo_Loc_Filter {
	use Context_Filter;

	/**
	 * Parses the raw value from the context to an integer.
	 *
	 * @since 4.9.0
	 *
	 * @param mixed $raw_value The raw filter value.
	 *
	 * @return array<string,integer> The distance value.
	 */
	protected function parse_value( $raw_value ) {
		return array_filter( (array) $raw_value );
	}

	/**
	 * Overrides the base method to always hook.
	 *
	 * @since 4.9.0
	 */
	protected function setup_query_filters() {
		add_filter( 'tribe_geoloc_geofence', [ $this, 'setup_geofence_in_query' ] );
	}

	/**
	 * Builds the value that should be set in the query argument for the Distance filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return string A comma-separated list of Venue IDs, e.g. `23` or `23,89`.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::to_list( $value, ',' );
	}
}
