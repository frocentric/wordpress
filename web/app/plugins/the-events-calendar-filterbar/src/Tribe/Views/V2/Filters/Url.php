<?php
/**
 * Handles the manipulation of a View URL in the context of the applied filters.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe\Events\Views\V2\View_Interface;
use Tribe__Context as Context;

/**
 * Class Url
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Url {

	/**
	 * Filters the View URL query arguments to add the ones handled by Filterbar filters.
	 *
	 * @since 4.9.0
	 *
	 * @param array          $query_args The current View URL query arguments.
	 * @param View_Interface $view       The View whose URL arguments are being filtered.
	 *
	 * @return array The filtered View URL query arguments.
	 */
	public function filter_view_query_args( array $query_args, View_Interface $view ) {
		$context = $view->get_context();

		foreach ( Factory::context_to_filters_map() as $context_key => $filter_class ) {
			$value = $context->get( $context_key, '__not_found__' );

			if ( '__not_found__' === $value ) {
				continue;
			}

			$filter_request_key = $context->get_read_key_for( $context_key, Context::REQUEST_VAR );

			if ( method_exists( $filter_class, 'fill_query_args' ) ) {
				$query_args = $filter_class::fill_query_args( $query_args, $value, $context_key, $context );
			} elseif ( method_exists( $filter_class, 'build_query_arg_value' ) ) {
				$value = $filter_class::build_query_arg_value( $value, $context_key, $context );
				$query_args[ $filter_request_key ] = $value;
			} else {
				$query_args[ $filter_request_key ] = $value;
			}
		}

		return $query_args;
	}
}
