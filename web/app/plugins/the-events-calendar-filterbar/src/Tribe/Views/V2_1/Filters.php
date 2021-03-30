<?php
namespace Tribe\Events\Filterbar\Views\V2_1;

use Tribe__Context as Context;
use Tribe__Events__Filterbar__Filter as Filter;
use Tribe__Events__Filterbar__Settings as Filter_Bar_Settings;

/**
 * Class managing Filters loading for the Views V2_1.
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 * @since   5.0.0
 */
class Filters {
	/**
	 * Filters the View template variables before the HTML is generated to add the ones related to this plugin filters.
	 *
	 * @since 5.0.0
	 *
	 * @param array   $template_vars The View template variables.
	 * @param Context $context       The View current context.
	 *
	 * @return array<string,mixed> The filtered template variables.
	 */
	public function filter_template_vars( array $template_vars, Context $context = null ) {
		$context = null !== $context ? $context : tribe_context();

		if ( $context->get( 'shortcode' ) ) {
			return $template_vars;
		}

		$template_vars['layout']                       = $this->get_layout_setting();
		$template_vars['filterbar_state']              = $this->get_open_closed_state( $context );
		$template_vars['filters']                      = $this->get_filters( $context, $template_vars['breakpoint_pointer'] );
		$template_vars['selected_filters']             = $this->get_selected_filters( $template_vars['filters'], $context, $template_vars['breakpoint_pointer'] );
		$template_vars['mobile_initial_state_control'] = $this->get_mobile_initial_state_control( $template_vars['layout'], $context );

		return $template_vars;
	}

	/**
	 * Which layout setting the user picked for their filters.
	 *
	 * @since  5.0.0
	 *
	 * @return string The display of the filter bar as either vertical or horizontal.
	 */
	public function get_layout_setting() {
		$default = 'vertical';
		$allowed = [ 'horizontal', $default ];
		$value   = (string) tribe_get_option( 'events_filters_layout', $default );

		return esc_attr( in_array( $value, $allowed ) ? $value : $default );
	}

	/**
	 * Returns the whole Filter Bar open or closed state.
	 *
	 * On initial PHP requests, the state is the one set by the administrator; on following requests, the state is the
	 * one provided by the front-end and read from the context.
	 *
	 * @since  5.0.0
	 *
	 * @param Context|null $context The current request context, or `null` to ignore the context.
	 *
	 * @return string The state of the filter bar on initial load as either `open` or `closed`.
	 */
	public function get_open_closed_state( Context $context = null ) {
		$default = 'open';
		$allowed = [ 'closed', $default ];

		/*
		 * PHP state, the one set the by the site administrator by means of the Settings; this state will be used if
		 * the front-end is not providing one explicitly.
		 */
		$initial_state = (string) tribe_get_option( 'events_filters_default_state', $default );

		// On Vertical, though, the initial state is always open.
		if ( 'vertical' === $this->get_layout_setting() ) {
			$initial_state = 'open';
		}

		// If the context provides the open/closed state, then use it; else default to the initial PHP one.
		$state = null !== $context ? $context->get( 'fbar_state', $initial_state ) : $initial_state;

		// We're not filtering this here as the value is filterable together with the rest of them template vars.
		return esc_attr( in_array( $state, $allowed, true ) ? $state : $default );
	}

	/**
	 * Get the active filters formatted for use with the V2_1 templates.
	 *
	 * @since  5.0.0
	 *
	 * @param Context $context            The View current context.
	 * @param string  $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
	 *
	 * @return array<string,array> An array of active filters formatted for the V2_1 template_vars.
	 */
	public function get_filters( $context, $breakpoint_pointer ) {
		/**
		 * Filter the Filter bar V2_1 active filters data that is formatted for use in the templates.
		 *
		 * @since 5.0.0
		 *
		 * @param array   $filters            An array of active filters formatted for the V2_1 template_vars.
		 * @param Context $context            The View current context.
		 * @param string  $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
		 */
		return apply_filters( 'tribe_events_filter_bar_views_v2_1_template_vars_filters', [], $context, $breakpoint_pointer );
	}

	/**
	 * Get the selected filters for the Filter Bar V2_1.
	 *
	 * @since  5.0.0
	 *
	 * @param array   $filters An array of active filters formatted for the V2_1 template_vars.
	 * @param Context $context The View current context.
	 *
	 * @return array An array of selected filters formatted for V2_1 template_vars.
	 */
	public function get_selected_filters( $filters, $context ) {
		/**
		 * Filter the selected filters for the Filter Bar V2_1.
		 *
		 * @since 5.0.0
		 *
		 * @param array               $selected_filters An array of selected filters formatted for V2_1 template_vars.
		 * @param array<string,array> $filters          An array of active filters formatted for the V2_1 template_vars.
		 * @param Context             $context          The View current context.
		 */
		return apply_filters( 'tribe_events_filter_bar_views_v2_1_template_vars_selected_filters', [], $filters, $context );
	}

	/**
	 * Get whether mobile initial state should be controlled by JS or not.
	 *
	 * @since 5.0.0.1
	 *
	 * @param string  $layout  The display of the filter bar as either vertical or horizontal.
	 * @param Context $context The View current context.
	 *
	 * @return bool Whether mobile initial state should be controlled by JS or not.
	 */
	public function get_mobile_initial_state_control( $layout, $context ) {
		/**
		 * Allows filtering of whether to control mobile initial state or not.
		 *
		 * @since 5.0.0.1
		 *
		 * @param bool    $mobile_initial_state_control Boolean on whether to control mobile initial state or not.
		 * @param Context $context                      The View current context.
		 */
		$mobile_initial_state_control = apply_filters( 'tribe_events_filter_bar_views_v2_1_mobile_initial_state_control', true, $context );

		/**
		 * Allows filtering of whether to control mobile initial state or not for a specific filter bar layout.
		 *
		 * @since 5.0.0.1
		 *
		 * @param bool    $mobile_initial_state_control Boolean on whether to control mobile initial state or not.
		 * @param Context $context                      The View current context.
		 */
		return apply_filters( "tribe_events_filter_bar_views_v2_1_{$layout}_mobile_initial_state_control", $mobile_initial_state_control, $context );
	}

	/**
	 * Get whether filters should display or not.
	 *
	 * @since 5.0.0
	 *
	 * @param \Tribe\Events\Views\V2\View_Interface $view The View currently rendering.
	 *
	 * @return bool
	 */
	public function should_display_filters( $view ) {
		/**
		 * Allows filtering of whether filters should display.
		 *
		 * @since 5.0.0
		 *
		 * @param bool                                  $should_display_filters Boolean on whether to display filters or not.
		 * @param \Tribe\Events\Views\V2\View_Interface $view                   The View currently rendering.
		 */
		return apply_filters( 'tribe_events_filter_bar_views_v2_1_should_display_filters', true, $view );
	}

	/**
	 * Returns whether a Filter Bar Filter state is open or not in a specific Context.
	 *
	 * "Open" indicates the state where a user has interacted with the filter and has opened it to input a choice.
	 * The open/closed state is parsed from a bitmask number provided in the request body; when no information about
	 * a Filter is found, the filter is assumed closed.
	 *
	 * @since 5.0.0
	 *
	 * @param Context       $context           The request Context to try and infer the Filter state from.
	 * @param array<Filter> $available_filters The filters available to template vars.
	 * @param Filter        $filter            The filter to return the open/closed state for.
	 *
	 * @return bool Whether a Filter is open or not.
	 */
	public static function is_open_in_context( Context $context, array $available_filters, Filter $filter ) {
		$current_value = $filter->currentValue;

		$filters_state = $context->get( 'fbar_filters_state', 0 );

		// Prune negative values.
		$filters_state = absint( $filters_state ) === (int) $filters_state ? (int) $filters_state : 0;

		// The `decbin` function is little-endian, but our state is big-endian: let's reverse the output string.
		$bitmask = strrev( decbin( $filters_state ) );

		$active_filter_titles = array_column( $available_filters, 'title' );

		$filter_position = array_search( $filter->get_title(), $active_filter_titles, true );

		$is_open = false !== $filter_position && ! empty( $bitmask[ $filter_position ] );

		return (bool) $is_open;
	}
}
