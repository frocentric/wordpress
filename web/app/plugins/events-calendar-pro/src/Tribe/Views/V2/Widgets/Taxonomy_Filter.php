<?php
/**
 * Utility class that provides methods for Widgets that include taxonomy filters.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Views\Widgets\Widget_View;
use Tribe\Events\Views\V2\Widgets\Widget_Abstract;
use Tribe__Events__Main as TEC;

/**
 * Class Taxonomy_Filter
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Taxonomy_Filter {
	/**
	 * Match any operand.
	 *
	 * @since 5.3.0
	 *
	 * @var string
	 */
	const OPERAND_OR = 'OR';

	/**
	 * Match all operand.
	 *
	 * @since 5.3.0
	 *
	 * @var string
	 */
	const OPERAND_AND = 'AND';

	/**
	 * Default operand for taxonomy filters.
	 *
	 * @since 5.3.0
	 *
	 * @var string
	 */
	const DEFAULT_OPERAND = self::OPERAND_OR;

	/**
	 * Get the admin structure for a widget taxonomy filter.
	 *
	 * @since 5.2.0
	 *
	 * @return array<string,mixed> The additional structure
	 */
	public function get_taxonomy_admin_section() {
		return [
			'taxonomy_section' => [
				'type'     => 'section',
				'classes'  => [ 'calendar-widget-filters-container' ],
				'label'    => _x( 'Filters:', 'The title for the selected taxonomy section of the List Widget.', 'tribe-events-calendar-pro' ),
				'children' => [
					'filters' => [
						'type' => 'taxonomy-filters',
						'name' => 'filters',
					],
					'operand' => [
						'type'     => 'fieldset',
						'classes'  => 'tribe-common-form-control-checkbox-radio-group',
						'label'    => _x( 'Operand:', 'The label for the taxonomy and/or option in the List Widget.', 'tribe-events-calendar-pro' ),
						'children' => [
							[
								'type'         => 'radio',
								'label'        => 'Match all',
								'button_value' => static::OPERAND_AND,
							],
							[
								'type'         => 'radio',
								'label'        => 'Match any',
								'button_value' => static::OPERAND_OR,
							],
						],
					],
				],
			],
			'taxonomy'         => [
				'type'        => 'taxonomy',
				'classes'     => 'calendar-widget-add-filter',
				'label'       => _x( 'Add a filter:', 'The label for the option to filter the List Widget events via a taxonomy.', 'tribe-events-calendar-pro' ),
				'placeholder' => _x( 'Select a Taxonomy Term', 'Placeholder label for taxonomy filter dropdown.', 'tribe-events-calendar-pro' ),
			],
		];
	}

	/**
	 * Decodes and sets the taxonomy args in a format that WP can use.
	 *
	 * @since 5.2.0
	 * @since 5.3.0    Add $operand to handle Matching all.
	 *
	 * @param string|array<string,mixed> $filters The current 'filter' arguments.
	 * @param string                     $operand The current Operand that we will use to determine how to build the classes.
	 *
	 * @return array<string,mixed> $filters The clean and ready filters argument.
	 */
	public function set_taxonomy_args( $filters, $operand = self::DEFAULT_OPERAND ) {
		$filters = maybe_unserialize( $filters );

		if ( is_string( $filters ) ) {
			$filters = json_decode( $filters, true );
		}

		// Remove empty elements from each sub-array, then from the top-level one.
		$filters = array_filter( array_map( 'array_filter', (array) $filters ) );

		if ( static::OPERAND_OR === strtoupper( $operand ) ) {
			return $filters;
		}

		return $filters;
	}

	/**
	 * Removes all filters that contains empty strings as before was creating data structures such as:
	 * {"tribe_events_cat":[]}, instead of just empty string. Return the properly formatted taxonomy
	 * filters.
	 *
	 * @since 4.4.21
	 * @since 5.2.0 carried to new widget.
	 *
	 * @param mixed $filters The filter taxonomies to be analyzed.
	 *
	 * @return string A JSON string representation of the clean and properly formatted filters.
	 */
	public function format_taxonomy_filters( $filters ) {
		$filters = maybe_unserialize( $filters );

		if ( is_string( $filters ) ) {
			$filters = json_decode( $filters, true );
		}

		// Remove empty elements from each sub-array, then from the top-level one.
		$filters = array_filter( array_map( 'array_filter', (array) $filters ) );

		return empty( $filters ) ? '' : (string) wp_json_encode( $filters );
	}

	/**
	 * Parse and format the data for select2 fields.
	 *
	 * @since 5.3.0
	 *
	 * @param Widget_Abstract     $widget_obj The widget object.
	 *
	 * @return string  Which terms are disabled for this widget.
	 */
	public function get_disabled_terms_on_widget( $widget_obj ) {
		$disabled            = [];

		if ( ! ( isset( $widget_obj->number, $widget_obj->option_name ) && is_numeric( $widget_obj->number ) ) ) {
			// Trust no one.
			return '';
		}

		// Hunt down the widget options.
		$widgets_options = get_option( $widget_obj->option_name );

		if ( ! isset( $widgets_options[ $widget_obj->number ]['filters'] ) ) {
			return '';
		}

		$tax_filters = json_decode( $widgets_options[ $widget_obj->number ]['filters'], true );

		// Avoids warnings around array_values.
		if ( ! is_array( $tax_filters ) ) {
			return '';
		}

		// Populate the disables terms IDs.
		$disabled = array_filter( array_merge( $disabled, ...array_values( $tax_filters ) ) );

		return wp_json_encode( $disabled );
	}

	/**
	 * Modify the data for the taxonomy filter.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $data The data for the field we're rendering.
	 * @param string              $field_name The slug for the field.
	 * @param Widget_Abstract     $widget_obj The widget object.
	 *
	 * @return array<string,mixed> The modified field data.
	 */
	public function add_taxonomy_filters_field_data( $data, $field_name, $widget_obj ) {
		if ( 'filters' !== $field_name ) {
			return $data;
		}

		$data['id']         = $widget_obj->get_field_id( 'filters' );
		$data['name']       = $widget_obj->get_field_name( 'filters' );
		$data['list_items'] = $this->format_tax_value_for_list( $data['value'] );

		return $data;
	}

	/**
	 * Generates a formatted array of taxonomy items for the template.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,string> $value The input values to iterate through and display.
	 *
	 * @return array<string,string> $list_items The array of taxonomy items.
	 */
	public function format_tax_value_for_list( $value ) {
		if ( empty( $value ) ) {
			return [];
		}

		$value = json_decode( $value, true );

		$list_items = [];
		foreach ( $value as $tax_name => $terms ) {
			if ( empty( $terms ) ) {
				continue;
			}

			$tax_obj = get_taxonomy( $tax_name );

			$list_items[ $tax_name ] = [
				'name'  => $tax_obj->labels->name,
				'terms' => [],
			];

			foreach ( $terms as $term_name ) {
				if ( empty( $term_name ) ) {
					continue;
				}

				$term_obj = get_term( $term_name, $tax_name );

				if ( empty( $term_obj ) || is_wp_error( $term_obj ) ) {
					continue;
				}

				$list_items[ $tax_name ]['terms'][] = [
					'name' => $term_obj->name,
					'id'   => $term_obj->term_id,
				];
			}
		}

		return $list_items;
	}

	/**
	 * Add args before hading them off to the repository.
	 *
	 * @since 5.1.1
	 * @since 5.3.0 Include $widget_view param.
	 *
	 * @param array<string,mixed> $args        The arguments to be set on the View repository instance.
	 * @param \Tribe__Context     $context     The context to use to setup the args.
	 * @param Widget_View         $widget_view Widget View being filtered.
	 *
	 * @return array<string,mixed> $args The arguments, ready to be set on the View repository instance.
	 */
	public function add_taxonomy_filters_repository_args( $args, $context, $widget_view ) {
		/**
		 * @todo remove dependency on Context, this variable should come from $args instead of context.
		 */
		$operand = $context->get( 'operand', static::DEFAULT_OPERAND );
		if ( ! empty( $context->get( 'post_tag' ) ) ) {
			$args['post_tag'] = $context->get( 'post_tag' );
		}

		$operation = static::OPERAND_AND === $operand ? 'term_and' : 'term_in';
		foreach( [ 'post_tag', TEC::TAXONOMY ] as $taxonomy ) {
			if ( empty( $args[ $taxonomy ] ) ) {
				continue;
			}
			$widget_view->get_repository()->by( $operation, $taxonomy, $args[ $taxonomy ] );

			unset( $args[ $taxonomy ] );
		}

		// Makes sure tax query exists.
		if ( empty( $args['tax_query'] ) ) {
			$args['tax_query'] = [];
		}

		$args['tax_query']['relation'] = $operand;

		return $args;
	}
}
