<?php
/**
 * An implementation of the Cost filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Cost
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Cost extends \Tribe__Events__Filterbar__Filters__Cost {
	use Context_Filter;

	/**
	 * Parses the value from the context and builds it the way the base filter.
	 *
	 * @since 4.9.0
	 *
	 * @param mixed $raw_value The raw value.
	 *
	 * @return array The parsed cost range value.
	 */
	public function parse_value( $raw_value ) {
		$value = (array) $raw_value;

		if ( isset( $value['min'] ) && isset( $value['max'] ) ) {
			return array( $value );
		} else {
			foreach ( $value as &$v ) {
				$range = explode( '-', $v );
				if ( ! preg_match( '/[0-9]+\-[0-9]+/', $v ) ) {
					continue;
				}
				$v = array( 'min' => $range[0], 'max' => $range[1] );
			}

			return $value;
		}

		return [];
	}

	/**
	 * Builds the value that should be set in the query argument for the Cost filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return string A comma-separated list of price ranges, e.g. `0-9` or `0-9,12-23`.
	 */
	public static function build_query_arg_value( $value, $context_key, Context $context ) {
		return Arr::to_list( $value, ',' );
	}
}
