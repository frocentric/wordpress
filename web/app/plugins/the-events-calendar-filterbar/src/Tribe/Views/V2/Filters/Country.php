<?php
/**
 * An implementation of the Country filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Country
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Country extends \Tribe__Events__Filterbar__Filters__Country {
	use Context_Filter;
	use Concatenated_Value_Handling;

	/**
	 * Builds the value that should be set in the query argument for the Country filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array<int|string> An array of Venue IDs, e.g. [ '23' ] or [ 23, 89 ].
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::list_to_array( $value, ',' );
	}
}
