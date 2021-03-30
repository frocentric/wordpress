<?php
/**
 * Updates and modifies the filter data as required by the v2.1 implementation.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2_1\Filters;

use Tribe\Events\Filterbar\Views\V2\Filters\Context_Filter;
use Tribe__Events__Filterbar__Filter as Filter;

/**
 * Class Data
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Filters
 */
class Data {
	/**
	 * Updates a filter field data entry to format it according to v2.1 requirements.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed> $field_data The filter field data entry; modified by reference.
	 */
	public static function visit_filter_field_data( &$field_data ) {
		if ( ! is_array( $field_data ) ) {
			return;
		}

		// Add an trailing `[]` to the field name if the type requires it.
		$types_req_brackets = [ 'checkbox', 'dropdown', 'select', 'multiselect' ];
		if (
			in_array( $field_data['type'], $types_req_brackets, true )
			&& ! preg_match( '#\\[]$#', $field_data['name'] )
		) {
			$field_data['name'] .= '[]';
		}
	}

	/**
	 * Updates a filter display value to stick with the format required by v2.1.
	 *
	 * @since 5.0.0
	 *
	 * @param string                $display_value The input display value, as produced by the Filter.
	 * @param Filter|Context_Filter $filter        The currently visited filter.
	 *
	 * @return string The updated Filter display value.
	 */
	public static function visit_display_value( $display_value, $filter ) {
		if ( empty( $display_value ) || ! $filter instanceof Filter ) {
			return $display_value;
		}

		$more = count( (array) $filter->currentValue ) - 1;
		if ( $more ) {
			$display_value .= " +{$more}";
		}

		return $display_value;
	}
}
