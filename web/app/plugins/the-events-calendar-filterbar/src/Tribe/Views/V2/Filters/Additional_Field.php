<?php
/**
 * An implementation of the Category filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Class Additional_Field
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Additional_Field extends \Tribe__Events__Filterbar__Filters__Additional_Field {
	use Context_Filter;

	public function set_meta_key( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	/**
	 * Parses the raw value from the context to a set of additional field values.
	 *
	 * @since 4.9.0
	 *
	 * @param array|string $raw_value Unused.
	 *
	 * @return array An array of parsed additional field values.
	 */
	protected function parse_value( $raw_value ) {
		$additional_fields = $this->context->get( 'filterbar_additional_fields', [] );
		$additional_fields = array_combine(
			array_map( static function ( $key ) {
				return preg_replace( '/^tribe_/', '', $key );
			}, array_keys( $additional_fields ) ),
			$additional_fields
		);

		if ( ! isset( $additional_fields[ $this->slug ] ) ) {
			return null;
		}

		return is_array( $additional_fields[ $this->slug ] )
			? $additional_fields[ $this->slug ]
			: explode( ',', $additional_fields[ $this->slug ] );
	}

	/**
	 * Builds the value that should be set in the query argument for the Additional Fields filter.
	 *
	 * @since 4.9.0
	 *
	 * @param array $query_args An array of query arguments to modify.
	 * @param string|array $value       The value, as received from the context.
	 * @param string       $context_key The key used to fetch the `$value` from the Context.
	 * @param Context      $context     The context instance.
	 *
	 * @return array The modified query arguments.
	 */
	public static function fill_query_args( array $query_args, $value, $context_key, Context $context ) {
		$value = (array) $value;

		if ( empty( $value ) ) {
			return $query_args;
		}

		$add = array_combine(
			array_keys( $value ),
			array_map( [ Arr::class, 'list_to_array' ], $value )
		);

		return array_merge( $query_args, $add );
	}
}
