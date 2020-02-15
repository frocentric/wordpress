<?php
defined( 'WPINC' ) or die;

add_action( 'admin_init', 'generate_blog_update_visibility_settings' );
/**
 * Migrates our old Blog settings so we can use checkboxes instead.
 *
 * @since 1.5
 */
function generate_blog_update_visibility_settings() {
	// Get our migration settings
	$settings = get_option( 'generate_migration_settings', array() );

	// If we've already ran this function, bail
	if ( isset( $settings[ 'blog_visibility_updated' ] ) && 'true' == $settings[ 'blog_visibility_updated' ] ) {
		return;
	}

	// A lot of the defaults changed, so lets put the old defaults here
	$defaults = array(
		'masonry' => 'false',
		'masonry_width' => 'width2',
		'masonry_most_recent_width' => 'width4',
		'post_image' => 'true',
		'date' => 'true',
		'author' => 'true',
		'categories' => 'true',
		'tags' => 'true',
		'comments' => 'true',
	);

	// Get our spacing settings
	$blog_settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		$defaults
	);

	$new_settings = array();

	// These options use to be a select input with false + true values
	// This will make the false values empty so the options can be checkboxes
	$keys = array( 'date', 'author', 'categories', 'tags', 'comments', 'masonry', 'post_image' );
	foreach ( $keys as $key ) {
		if ( is_string( $blog_settings[ $key ] ) ) {
			if ( 'false' == $blog_settings[ $key ] ) {
				$new_settings[ $key ] = false;
			} elseif ( 'true' == $blog_settings[ $key ] ) {
				$new_settings[ $key ] = true;
			}
		}
	}

	// Set the single post meta options to whatever the blog options are
	$new_settings[ 'single_date' ] = isset( $new_settings[ 'date' ] ) ? $new_settings[ 'date' ] : true;
	$new_settings[ 'single_author' ] = isset( $new_settings[ 'author' ] ) ? $new_settings[ 'author' ] : true;
	$new_settings[ 'single_categories' ] = isset( $new_settings[ 'categories' ] ) ? $new_settings[ 'categories' ] : true;
	$new_settings[ 'single_tags' ] = isset( $new_settings[ 'tags' ] ) ? $new_settings[ 'tags' ] : true;

	if ( isset( $new_settings[ 'masonry' ] ) && $new_settings[ 'masonry' ] ) {
		$new_settings[ 'column_layout' ] = true;
		$new_settings[ 'infinite_scroll' ] = true;
		$new_settings[ 'infinite_scroll_button' ] = true;

		if ( 'width2' == $blog_settings['masonry_width'] ) {
			$new_settings[ 'columns' ] = '33';
		}

		if ( 'width4' == $blog_settings['masonry_width'] ) {
			$new_settings[ 'columns' ] = '50';
		}

		if ( 'width6' == $blog_settings['masonry_width'] ) {
			$new_settings[ 'columns' ] = '100';
		}

		if ( 'width2' == $blog_settings[ 'masonry_width' ] ) {
			if ( 'width2' !== $blog_settings[ 'masonry_most_recent_width' ] ) {
				$new_settings[ 'featured_column' ] = true;
			} else {
				$new_settings[ 'featured_column' ] = false;
			}
		}

		if ( 'width4' == $blog_settings[ 'masonry_width' ] ) {
			if ( 'width6' == $blog_settings[ 'masonry_most_recent_width' ] ) {
				$new_settings[ 'featured_column' ] = true;
			} else {
				$new_settings[ 'featured_column' ] = false;
			}
		}

		if ( 'width6' == $blog_settings[ 'masonry_width' ] ) {
			$new_settings[ 'featured_column' ] = false;
		}
	}

	if ( function_exists( 'generate_page_header_get_defaults' ) ) {
		$page_header_settings = wp_parse_args(
			get_option( 'generate_page_header_settings', array() ),
			generate_page_header_get_defaults()
		);

		if ( 'hide' == $page_header_settings[ 'post_header_position' ] ) {
			$new_settings[ 'single_post_image' ] = false;
		} else {
			$new_settings[ 'single_post_image_position' ] = $page_header_settings[ 'post_header_position' ];
		}

		$new_settings[ 'page_post_image_position' ] = $page_header_settings[ 'page_header_position' ];
	}

	unset( $blog_settings['masonry_width'] );
	unset( $blog_settings['masonry_most_recent_width'] );

	$update_settings = wp_parse_args( $new_settings, $blog_settings );
	update_option( 'generate_blog_settings', $update_settings );

	// Update our migration option so we don't need to run this again
	$updated[ 'blog_visibility_updated' ] = 'true';
	$migration_settings = wp_parse_args( $updated, $settings );
	update_option( 'generate_migration_settings', $migration_settings );
}
