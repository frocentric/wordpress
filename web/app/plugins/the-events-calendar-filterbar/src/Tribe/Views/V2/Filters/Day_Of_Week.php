<?php
/**
 * An implementation of the Day of Week filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Day_Of_Week
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Day_Of_Week extends \Tribe__Events__Filterbar__Filters__Day_Of_Week {
	use Context_Filter;

	/**
	 * Parses the raw value from the context to a set it in the format used by the filter.
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
	 * Adds the JOIN required to have the date meta JOIN clause to the query the filter is filtering.
	 *
	 * @since 4.9.0
	 */
	protected function setup_join_clause() {
		$this->add_date_meta_to_query( $this->query );
		parent::setup_join_clause();
	}

	/**
	 * Builds the value that should be set in the query argument for the Day of Week filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array An array of day numbers.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::list_to_array( $value, ',' );
	}
}
