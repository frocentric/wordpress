<?php
/**
 * This file handles column-related functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'generate_blog_get_columns' ) ) {
	/**
	 * Initiate columns.
	 *
	 * @since 0.1
	 */
	function generate_blog_get_columns() {
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		// If columns are enabled, set to true.
		$columns = ( $generate_blog_settings['column_layout'] ) ? true : false;

		// If we're not dealing with posts, set it to false.
		// Check for is_home() to prevent bug in Yoast that throws off the post type check.
		$columns = ( 'post' === get_post_type() || is_search() || is_home() ) ? $columns : false;

		// If masonry is enabled via filter, enable columns.
		// phpcs:ignore -- Non-strict comparison allowed.
		$columns = ( 'true' == apply_filters( 'generate_blog_masonry', 'false' ) ) ? true : $columns;

		// If we're on a singular post or page, disable.
		$columns = ( is_singular() ) ? false : $columns;

		// Turn off columns if we're on a WooCommerce search page.
		if ( function_exists( 'is_woocommerce' ) ) {
			$columns = ( is_woocommerce() && is_search() ) ? false : $columns;
		}

		// Bail if there's no search results.
		if ( is_search() ) {
			global $wp_query;

			// phpcs:ignore -- non-strict comparison allowed.
			if ( 0 == $wp_query->post_count ) {
				$columns = false;
			}
		}

		// Return the result.
		return apply_filters( 'generate_blog_columns', $columns );
	}
}

if ( ! function_exists( 'generate_blog_get_masonry' ) ) {
	/**
	 * Check if masonry is enabled.
	 * This function is a mess with strings as bools etc.. Will re-write in a big upate to get lots of testing.
	 */
	function generate_blog_get_masonry() {
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		// If masonry is enabled via option or filter, enable it.
		// phpcs:ignore -- non-strict comparison allowed.
		if ( $generate_blog_settings['masonry'] || 'true' == apply_filters( 'generate_blog_masonry', 'false' ) ) {
			$masonry = 'true';
		} else {
			$masonry = 'false';
		}

		// Allow masonry to be turned off using a boolean.
		if ( false === apply_filters( 'generate_blog_masonry', 'false' ) ) {
			$masonry = 'false';
		}

		return $masonry;
	}
}

if ( ! function_exists( 'generate_blog_add_columns_container' ) ) {
	add_action( 'generate_before_main_content', 'generate_blog_add_columns_container' );
	/**
	 * Add columns container
	 *
	 * @since 1.0
	 */
	function generate_blog_add_columns_container() {
		if ( ! generate_blog_get_columns() ) {
			return;
		}

		$columns = generate_blog_get_column_count();

		printf(
			'<div class="generate-columns-container %1$s">%2$s',
			'false' !== generate_blog_get_masonry() ? 'masonry-container are-images-unloaded' : '',
			'false' !== generate_blog_get_masonry() ? '<div class="grid-sizer grid-' . esc_attr( $columns ) . ' tablet-grid-50 mobile-grid-100"></div>' : '' // phpcs:ignore -- no escaping needed.
		);
	}
}

if ( ! function_exists( 'generate_blog_add_ending_columns_container' ) ) {
	add_action( 'generate_after_main_content', 'generate_blog_add_ending_columns_container' );
	/**
	 * Add closing columns container
	 *
	 * @since 1.0
	 */
	function generate_blog_add_ending_columns_container() {
		if ( ! generate_blog_get_columns() ) {
			return;
		}

		echo '</div><!-- .generate-columns-contaier -->';
	}
}

if ( ! function_exists( 'generate_blog_columns_css' ) ) {
	/**
	 * Add inline CSS
	 */
	function generate_blog_columns_css() {
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( function_exists( 'generate_spacing_get_defaults' ) ) {
			$spacing_settings = wp_parse_args(
				get_option( 'generate_spacing_settings', array() ),
				generate_spacing_get_defaults()
			);
		}

		$separator = ( function_exists( 'generate_spacing_get_defaults' ) ) ? absint( $spacing_settings['separator'] ) : 20;

		$return = '';
		if ( generate_blog_get_columns() ) {
			$return .= '.generate-columns {margin-bottom: ' . $separator . 'px;padding-left: ' . $separator . 'px;}';
			$return .= '.generate-columns-container {margin-left: -' . $separator . 'px;}';
			$return .= '.page-header {margin-bottom: ' . $separator . 'px;margin-left: ' . $separator . 'px}';
			$return .= '.generate-columns-container > .paging-navigation {margin-left: ' . $separator . 'px;}';
		}

		return $return;
	}
}

if ( ! function_exists( 'generate_blog_get_column_count' ) ) {
	/**
	 * Get our column grid class
	 */
	function generate_blog_get_column_count() {
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		$count = $generate_blog_settings['columns'];

		return apply_filters( 'generate_blog_get_column_count', $count );
	}
}
