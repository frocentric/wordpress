<?php
/**
 * An implementation of the Tags filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Tag
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Tag extends \Tribe__Events__Filterbar__Filters__Tag {
	use Context_Filter;

	/**
	 * Parses the raw value from the context to the format used by the filter.
	 *
	 * @since 4.9.0
	 *
	 * @param array|string $raw_value A tag term ID, or an array of tag term IDs.
	 *
	 * @return array An array of time of tag term ids.
	 */
	protected function parse_value( $raw_value ) {
		return array_filter( (array) $raw_value );
	}

	/**
	 * Builds the value that should be set in the query argument for the Tag filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array An array of term IDs.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::list_to_array( $value, ',' );
	}
}
