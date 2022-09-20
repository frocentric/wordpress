<?php
/**
 * Frocentric: Tech custom theme
 *
 * @package Frocentric: Tech theme
 */

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

if ( ! function_exists( 'enqueue_parent_styles' ) ) {
	/**
	 * Enqueues parent theme styles
	 */
	function enqueue_parent_styles() {
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', null, wp_get_theme()->get( 'Version' ) );
		wp_enqueue_style( 'theme-variables', get_theme_file_uri( 'css/variables.css' ), [ 'generate-child' ], filemtime( get_theme_file_path( 'css/variables.css' ) ), 'all' );
		wp_enqueue_style( 'theme-fonts', get_theme_file_uri( 'css/fonts.css' ), [ 'theme-variables' ], filemtime( get_theme_file_path( 'css/fonts.css' ) ), 'all' );
		wp_enqueue_style( 'theme-elements', get_theme_file_uri( 'css/elements.css' ), [ 'theme-fonts' ], filemtime( get_theme_file_path( 'css/elements.css' ) ), 'all' );
		wp_enqueue_style( 'theme-elementor', get_theme_file_uri( 'css/elementor.css' ), [ 'theme-elements' ], filemtime( get_theme_file_path( 'css/elementor.css' ) ), 'all' );
		wp_enqueue_style( 'theme-forms', get_theme_file_uri( 'css/ninja-forms.css' ), [ 'theme-elements' ], filemtime( get_theme_file_path( 'css/ninja-forms.css' ) ), 'all' );
		wp_enqueue_style( 'theme-tech', get_theme_file_uri( 'css/tech.css' ), [ 'theme-forms' ], filemtime( get_theme_file_path( 'css/tech.css' ) ), 'all' );
	}
}

if ( ! function_exists( 'generate_entry_meta' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * @since 1.2.5
	 */
	function generate_entry_meta() {
		$items = apply_filters(
			'generate_footer_entry_meta_items',
			[
				'author',
				'tags',
				'comments-link',
			]
		);

		foreach ( $items as $item ) {
			generate_do_post_meta_item( $item );
		}
	}
}

if ( ! function_exists( 'generate_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 0.1
	 */
	function generate_posted_on() {
		$items = apply_filters(
			'generate_header_entry_meta_items',
			[
				'categories',
				'date',
			]
		);

		foreach ( $items as $item ) {
			generate_do_post_meta_item( $item );
		}
	}
}

add_action( 'generate_before_entry_title', 'generate_post_meta' );

if ( ! function_exists( 'generate_post_meta' ) ) {
	/**
	 * Build the post meta.
	 *
	 * @since 1.3.29
	 */
	function generate_post_meta() {
		$post_types = apply_filters(
			'generate_entry_meta_post_types',
			[
				'post',
			]
		);

		if ( in_array( get_post_type(), $post_types, true ) ) : ?>
			<div class="entry-meta">
				<?php generate_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php
		endif;
	}
}

// Add custom image size used on homepage.
add_image_size( 'frocentric-halfwidth', 580, 0 );

// Disable page titles
add_filter( 'generate_show_title', '__return_false' );
