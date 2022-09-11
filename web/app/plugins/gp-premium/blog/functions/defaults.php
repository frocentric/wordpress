<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'generate_blog_get_defaults' ) ) {
	/**
	 * Set our defaults
	 *
	 * @since 0.1
	 */
	function generate_blog_get_defaults() {
		$generate_blog_defaults = array(
			'excerpt_length' => '55',
			'read_more' => __( 'Read more', 'gp-premium' ),
			'read_more_button' => false,
			'masonry' => false,
			'masonry_load_more' => __( '+ More', 'gp-premium' ),
			'masonry_loading' => __( 'Loading...', 'gp-premium' ),
			'infinite_scroll' => false,
			'infinite_scroll_button' => false,
			'post_image' => true,
			'post_image_position' => '',
			'post_image_alignment' => 'post-image-aligned-center',
			'post_image_size' => 'full',
			'post_image_width' => '',
			'post_image_height' => '',
			'post_image_padding' => true,
			'single_post_image' => true,
			'single_post_image_position' => 'inside-content',
			'single_post_image_alignment' => 'center',
			'single_post_image_size' => 'full',
			'single_post_image_width' => '',
			'single_post_image_height' => '',
			'single_post_image_padding' => true,
			'page_post_image' => true,
			'page_post_image_position' => 'above-content',
			'page_post_image_alignment' => 'center',
			'page_post_image_size' => 'full',
			'page_post_image_width' => '',
			'page_post_image_height' => '',
			'page_post_image_padding' => true,
			'date' => true,
			'author' => true,
			'categories' => true,
			'tags' => true,
			'comments' => true,
			'single_date' => true,
			'single_author' => true,
			'single_categories' => true,
			'single_tags' => true,
			'single_post_navigation' => true,
			'column_layout' => false,
			'columns' => '50',
			'featured_column' => false,
		);

		return apply_filters( 'generate_blog_option_defaults', $generate_blog_defaults );
	}
}
