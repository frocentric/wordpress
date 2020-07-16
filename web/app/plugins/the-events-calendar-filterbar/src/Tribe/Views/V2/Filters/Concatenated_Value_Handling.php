<?php
/**
 * Provides methods to support handling concatenated values
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

/**
 * Trait Context_Filter
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
trait Concatenated_Value_Handling {

	/**
	 * Parses the raw value from the context to a set of Venue IDs.
	 *
	 * @since 4.9.0
	 *
	 * @param array|string $raw_value Either an array of "-" concatenated Venue IDs, or a comma-separated list of "-" concatenated Venue IDs.
	 *
	 * @return array An array of Venue IDs.
	 */
	protected function parse_value( $raw_value ) {
		$values = is_array( $raw_value ) ? $raw_value : explode( ',', $raw_value );

		foreach ( $values as &$value ) {
			$value = str_replace( ',', '-', $value );
		}

		return array_filter( $values );
	}
}