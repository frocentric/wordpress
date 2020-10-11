<?php
/**
 * Froware custom theme
 *
 * @package Froware theme
 */

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

/**
 * Enqueues parent theme styles
 */
function enqueue_parent_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', null, wp_get_theme()->get( 'Version' ) );
}

/**
 * Prints HTML with meta information for the categories, tags.
 *
 * @since 1.2.5
 */
function generate_entry_meta() {
	$items = apply_filters( 'generate_footer_entry_meta_items', array(
		'author',
		'tags',
		'comments-link',
	) );

	foreach ( $items as $item ) {
		generate_do_post_meta_item( $item );
	}
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since 0.1
 */
function generate_posted_on() {
	$items = apply_filters( 'generate_header_entry_meta_items', array(
		'categories',
		'date',
	) );

	foreach ( $items as $item ) {
		generate_do_post_meta_item( $item );
	}
}
