<?php
namespace Tribe\Events\Filterbar\Views\V2;

/**
 * Class managing Filters loading for the Views V2.
 *
 * @package Tribe\Events\Filterbar\Views\V2
 * @since   4.9.0
 */
class Filters {

	/**
	 * @var string Term for vertical layout.
	 *
	 * @since 5.0.0
	 */
	const LAYOUT_VERTICAL = 'vertical';

	/**
	 * @var string Term for horizontal layout.
	 *
	 * @since 5.0.0
	 */
	const LAYOUT_HORIZONTAL = 'horizontal';
	/**
	 * Which layout setting the user picker for their filters.
	 *
	 * @since  4.9.0
	 *
	 * @return string Only `vertical` or `horizontal` values are allowed here.
	 */
	public function get_layout_setting() {
		$default = self::LAYOUT_VERTICAL;
		$allowed = [ self::LAYOUT_HORIZONTAL, $default ];
		$value   = (string) tribe_get_option( 'events_filters_layout', $default );

		return esc_attr( in_array( $value, $allowed ) ? $value : $default );
	}

	/**
	 * Get whether filters should display or not.
	 *
	 * @since 4.9.1
	 *
	 * @param \Tribe\Events\Views\V2\View_Interface $view The View currently rendering.
	 *
	 * @return bool
	 */
	public function should_display_filters( $view ) {
		/**
		 * Allows filtering of whether filters should display.
		 *
		 * @since 4.9.1
		 *
		 * @param bool                                  $should_display_filters Boolean on whether to display filters or not.
		 * @param \Tribe\Events\Views\V2\View_Interface $view                   The View currently rendering.
		 */
		return apply_filters( 'tribe_events_filter_bar_views_v2_should_display_filters', true, $view );
	}
}
