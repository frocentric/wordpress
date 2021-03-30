<?php
/**
 * An implementation of the Venue filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Venue
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Venue extends \Tribe__Events__Filterbar__Filters__Venue {
	use Context_Filter;

	/**
	 * Parses the raw value from the context to a set of Venue IDs.
	 *
	 * @since 4.9.0
	 *
	 * @param array|string $raw_value Either an array of Venue IDs, or a comma-separated list of Venue IDs.
	 *
	 * @return array An array of Venue IDs.
	 */
	protected function parse_value( $raw_value ) {
		$values = is_array( $raw_value ) ? $raw_value : explode( ',', $raw_value );
		$values = array_map( 'absint', $values );

		return array_filter( $values );
	}

	/**
	 * Builds the value that should be set in the query argument for the Venue filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array<string> An array of Venue IDs, e.g. `23` or `23-89`.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::list_to_array( $value, ',' );
	}
}
