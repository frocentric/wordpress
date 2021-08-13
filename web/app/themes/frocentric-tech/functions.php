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

add_action( 'init', 'register_user_menu' );

if ( ! function_exists( 'register_user_menu' ) ) {
	function register_user_menu() {
		register_nav_menu( 'user-menu', __( 'User Menu' ) );
	}
}

add_action( 'generate_after_primary_menu', 'generate_after_primary_menu_callback' );

if ( ! function_exists( 'generate_after_primary_menu_callback' ) ) {
	function generate_after_primary_menu_callback() {
		wp_nav_menu(
			[
				'theme_location' => 'user-menu',
				'container' => 'div',
				'container_class' => 'main-nav user-nav',
				'container_id' => 'user-menu',
				'menu_class' => '',
				'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', generate_get_element_classes( 'menu' ) ) . '">%3$s</ul>',
			]
		);
	}
}

// Add custom image size used on homepage.
add_image_size( 'frocentric-halfwidth', 580, 0 );

// Disable page titles
add_filter( 'generate_show_title', '__return_false' );
