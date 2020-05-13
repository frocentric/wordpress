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
