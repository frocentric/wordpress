<?php
/**
 * Handles the construction and set up of filters for a context.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2_1\Filters;

use Tribe\Events\Filterbar\Views\V2\Filters\Context_Filter;
use Tribe\Events\Filterbar\Views\V2\Filters\Factory as Compatibility_Factory;
use Tribe\Events\Filterbar\Views\V2_1\Filters;
use Tribe\Events\Views\V2\View;
use Tribe__Context as Context;

/**
 * Class Factory
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Filters
 */
class Factory extends Compatibility_Factory {

	/**
	 * Adds filter bar HTML classes applied to the View top-level container.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string> $html_classes Array of classes used for this view.
	 * @param string        $view_slug    The current view slug.
	 * @param View          $instance     The current View object.
	 */
	public function for_html_classes( array $html_classes, $view_slug, View $instance ) {
		$layout = tribe( Filters::class )->get_layout_setting();

		$html_classes[] = 'tribe-events--has-filter-bar';
		$html_classes[] = "tribe-events--filter-bar-$layout";

		return $html_classes;
	}

	/**
	 * Get the Active Filters to add to the Template Vars.
	 *
	 * @since 5.0.0
	 *
	 * @param Context $context            The View context instance.
	 * @param string  $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
	 *
	 * @return array
	 */
	public function get_active_filters_for_template_vars( Context $context = null, $breakpoint_pointer = '' ) {
		$context = $context ?: tribe_context();
		$filters = $this->build_for_context( $context, static function ( $key ) use ( $context ) {
			if ( 'filterbar_geofence_distance' !== $key ) {
				// By default do not skip the filters.
				return false;
			}

			// Skip the Distance filter if there is no location search.
			$geoloc_search = $context->get( 'geoloc_search', false );

			return empty( $geoloc_search );
		} );

		usort( $filters, 'tribe_sort_by_priority' );

		$template_var_filters = [];

		$layout = tribe( Filters::class )->get_layout_setting();

		// Get available filters from active filters.
		/** @var Context_Filter|\Tribe__Events__Filterbar__Filter $filter */
		$available_filters = array_filter( $filters, static function( $filter ) {
			// Add the values to the values property to make them accessible.
			$filter->set_values();

			// Skip any filter with not values to filter by.
			return ! empty( $filter->values );
		} );

		// Format the Filters for the Template Vars.
		/** @var Context_Filter|\Tribe__Events__Filterbar__Filter $filter */
		foreach ( $available_filters as $filter ) {
			$filter->set_data_visitor( [ Data::class, 'visit_filter_field_data' ] );
			$filter->set_display_value_visitor( [ Data::class, 'visit_display_value' ] );

			$current_value = '';
			if ( 1 <= count( (array) $filter->currentValue ) ) {
				$current_value = count( (array) $filter->currentValue );
			}

			// Try to initialize the filter open/closed state from the context.
			$is_open = Filters::is_open_in_context( $context, $available_filters, $filter );

			/**
			 * Allows filtering of if the filters should be closed by default for the current layout.
			 *
			 * @since 5.0.0
			 *
			 * @param boolean               $is_open Whether the filter should be open or closed (default false).
			 * @param array<array>          $filters An array of filters passed to the template.
			 * @param array<string,mixed>   $context An array of parameters passed to the template.
			 */
			$is_open = apply_filters( "tribe_events_filter_bar_views_v2_1_{$layout}_filter_open", $is_open, $filters, $context);

			/**
			 * Allows filtering of if specific individual filters should be closed by default for the current layout.
			 *
			 * @since 5.0.0
			 *
			 * @param boolean             $is_open Whether the filter should be open or closed (default false).
			 * @param Context_Filter|\Tribe__Events__Filterbar__Filter $filter The specific filter instance.
			 * @param array<string,mixed> $context An array of parameters passed to the template.
			 */
			$is_open = apply_filters( "tribe_events_filter_bar_views_v2_1_{$layout}_{$filter->slug}_filter_open", $is_open, $filter, $context );

			$filter_data = [
				'label'            => $filter->name,
				'selections_count' => $current_value,
				'selections'       => $filter->get_current_value_for_display(),
				'toggle_id'        => "$filter->slug-toggle-$breakpoint_pointer",
				'container_id'     => "$filter->slug-container-$breakpoint_pointer",
				'pill_toggle_id'   => "$filter->slug-pill-toggle-$breakpoint_pointer",
				'is_open'          => $is_open,
				'name'             => $filter->get_name_field(),
				'fields'           => $filter->get_fields_data_by_type(),
				'type'             => 'select' === $filter->type ? 'dropdown' : $filter->type,
			];

			$template_var_filters[] = $filter_data;
		}

		return $template_var_filters;
	}
}
