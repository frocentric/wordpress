<?php
defined( 'WPINC' ) or die;

require plugin_dir_path( __FILE__ ) . 'post-type.php';
require plugin_dir_path( __FILE__ ) . 'global-locations.php';
require plugin_dir_path( __FILE__ ) . 'metabox.php';
require plugin_dir_path( __FILE__ ) . 'page-header.php';
require plugin_dir_path( __FILE__ ) . 'post-image.php';

add_action( 'wp', 'generate_page_header_do_setup' );
/**
 * Adds our page headers in their correct places, and sets any necessary filters.
 *
 * @since 1.4
 */
function generate_page_header_do_setup() {
	if ( is_admin() ) {
		return;
	}

	$options = generate_page_header_get_options();

	if ( ! $options ) {
		return;
	}

	$global_locations = wp_parse_args( get_option( 'generate_page_header_global_locations', array() ), '' );

	// Remove elements if they're being added as a template tag
	if ( '' !== $options[ 'content' ] ) {
		if ( strpos( $options[ 'content' ], '{{post_title}}' ) !== false ) {
			add_filter( 'generate_show_title', '__return_false' );
			remove_action( 'generate_archive_title', 'generate_archive_title' );
			add_filter( 'post_class', 'generate_page_header_remove_hentry' );
		}

		if ( strpos( $options[ 'content' ], '{{post_date}}' ) !== false ) {
			add_filter( 'generate_post_date', '__return_false' );
			add_filter( 'post_class', 'generate_page_header_remove_hentry' );
		}

		if ( strpos( $options[ 'content' ], '{{post_author}}' ) !== false ) {
			add_filter( 'generate_post_author', '__return_false' );
			add_filter( 'post_class', 'generate_page_header_remove_hentry' );
		}

		if ( strpos( $options[ 'content' ], '{{post_terms.category}}' ) !== false ) {
			add_filter( 'generate_show_categories', '__return_false' );
		}

		if ( strpos( $options[ 'content' ], '{{post_terms.post_tag}}' ) !== false ) {
			add_filter( 'generate_show_tags', '__return_false' );
		}
	}

	// Replace our logos if set
	if ( generate_page_header_logo_exists() && $options[ 'logo_url' ] ) {
		add_filter( 'generate_logo', 'generate_page_header_replace_logo' );
	}

	if ( generate_page_header_navigation_logo_exists() && $options[ 'navigation_logo_url' ] ) {
		add_filter( 'generate_navigation_logo', 'generate_page_header_replace_navigation_logo' );
	}

	// Single posts
	if ( is_singular() ) {
		if ( 'inside-content' == generate_get_page_header_location() ) {
			add_action( 'generate_before_content','generate_page_header' );
		}

		if ( 'below-title' == generate_get_page_header_location() ) {
			add_action( 'generate_after_entry_header','generate_page_header' );
		}

		if ( 'above-content' == generate_get_page_header_location() ) {
			add_action( 'generate_after_header','generate_page_header' );
		}
	}

	/**
	 * Need to check all 3
	 * @see https://core.trac.wordpress.org/ticket/18636
	 */
	if ( is_tax() || is_category() || is_tag() ) {
		add_action( 'generate_after_header','generate_page_header' );
	}

	// Blog page header
	if ( generate_get_blog_page_header() ) {
		add_action( 'generate_after_header','generate_page_header' );
	}

	// Custom post types (excluding single posts)
	if ( isset( $global_locations[ get_post_type( get_the_ID() ) ] ) && '' !== $global_locations[ get_post_type( get_the_ID() ) ] && ! is_singular() ) {
		add_action( 'generate_after_header','generate_page_header' );
	}

	// Search results
	if ( is_search() ) {
		add_action( 'generate_after_header','generate_page_header' );
	}

	// 404 page
	if ( is_404() ) {
		add_action( 'generate_after_header','generate_page_header' );
	}
}

/**
 * Gets our post meta if it exists.
 * If it doesn't, return an empty string.
 *
 * @since 1.4
 *
 * @return string|bool
 */
function generate_page_header_get_post_meta( $post_id, $key = '', $single = false ) {
	return null !== get_post_meta( $post_id, $key, $single ) ? get_post_meta( $post_id, $key, $single ) : '';
}

/**
 * Put all of our meta box settings into an array we can use
 *
 * We set the post ID based on various settings in here so we can use the same
 * settings no matter the location.
 *
 * @since 1.4
 *
 * @param int $id The ID of our page header to return.
 * @return array All our meta box settings.
 */
function generate_page_header_get_options( $id = false ) {
	$global_locations = wp_parse_args( get_option( 'generate_page_header_global_locations', array() ), '' );
	$post_type = get_post_type( get_the_ID() );

	// Get our term meta if we're on a taxonomy
	// Need to check all 3
	// @see https://core.trac.wordpress.org/ticket/18636
	if ( is_tax() || is_category() || is_tag() && ! $id && ! is_singular() ) {
		$queried_object = get_queried_object();

		if ( is_object( $queried_object ) ) {
			if ( isset( $global_locations[ $queried_object->taxonomy ] ) && '' !== $global_locations[ $queried_object->taxonomy ] && ! $id ) {
				$id = $global_locations[ $queried_object->taxonomy ];

				if ( 'publish' !== get_post_status( $id ) ) {
					$id = false;
				}
			}

			$tax_post_id = get_term_meta( $queried_object->term_id, '_generate-select-page-header', true );
			if ( '' !== $tax_post_id && 'publish' == get_post_status( $tax_post_id ) ) {
				$id = $tax_post_id;
			}
		}
	}

	// Set our blog page ID if we're on the blog
	if ( isset( $global_locations[ 'blog' ] ) && generate_get_blog_page_header() && ! $id ) {
		$id = $global_locations[ 'blog' ];
	}

	// Archive post types
	if ( isset( $global_locations[ $post_type . '_archives' ] ) && '' !== $global_locations[ $post_type . '_archives' ] && ! $id && is_post_type_archive( $post_type ) ) {
		$id = $global_locations[ $post_type . '_archives' ];
	}

	// Search results
	if ( isset( $global_locations[ 'search_results' ] ) && '' !== $global_locations[ 'search_results' ] && ! $id && is_search() ) {
		$id = $global_locations[ 'search_results' ];
	}

	// 404
	if ( isset( $global_locations[ '404' ] ) && '' !== $global_locations[ '404' ] && ! $id && is_404() ) {
		$id = $global_locations[ '404' ];
	}

	if ( is_singular() ) {
		// Single post types
		if ( isset( $global_locations[ $post_type ] ) && '' !== $global_locations[ $post_type ] && ! $id ) {
			$id = $global_locations[ $post_type ];

			if ( 'publish' !== get_post_status( $id ) ) {
				$id = false;
			}
		}

		// Use our Page Header CPT
		// If it doesn't exist, use our on-page meta box
		$cpt_post_id = get_post_meta( get_the_ID(), '_generate-select-page-header', true );
		if ( '' !== $cpt_post_id && 'publish' == get_post_status( $cpt_post_id ) ) {
			$id = get_post_meta( get_the_ID(), '_generate-select-page-header', true );
		} elseif ( get_post_meta( get_the_ID(), '_meta-generate-page-header-image', true ) || get_post_meta( get_the_ID(), '_meta-generate-page-header-content', true ) ) {
			// Get the page ID if we have a featured image, custom image or page header content
			$id = get_the_ID();
		}

		// If we still don't have an ID, check if we have a featured image to show
		if ( ! $id && has_post_thumbnail() ) {
			$id = get_the_ID();
		}
	}

	$id = apply_filters( 'generate_page_header_id', $id );

	// Bail if we don't have an ID
	if ( ! $id ) {
		return;
	}

	// Figure out our image ID
	$image_id = null;
	if ( get_post_meta( $id, '_meta-generate-page-header-image-id', true ) ) {
		$image_id = get_post_meta( $id, '_meta-generate-page-header-image-id', true );
	} elseif ( has_post_thumbnail( $id ) ) {
		$image_id = get_post_thumbnail_id( $id, 'full' );
	} elseif ( is_singular() ) {
		$image_id = get_post_thumbnail_id( get_the_ID(), 'full' );
	}

	$options = array(
		'page_header_id'			  => $id,
		'image_url' 				  => get_post_meta( $id, '_meta-generate-page-header-image', true ),
		'image_id' 					  => $image_id,
		'image_link' 				  => get_post_meta( $id, '_meta-generate-page-header-image-link', true ),
		'image_resize' 				  => get_post_meta( $id, '_meta-generate-page-header-enable-image-crop', true ),
		'image_width' 				  => get_post_meta( $id, '_meta-generate-page-header-image-width', true ),
		'image_height' 				  => get_post_meta( $id, '_meta-generate-page-header-image-height', true ),
		'content' 					  => get_post_meta( $id, '_meta-generate-page-header-content', true ),
		'autop' 					  => get_post_meta( $id, '_meta-generate-page-header-content-autop', true ),
		'add_padding' 				  => get_post_meta( $id, '_meta-generate-page-header-content-padding', true ),
		'background_image' 			  => get_post_meta( $id, '_meta-generate-page-header-image-background', true ),
		'container_type' 			  => get_post_meta( $id, '_meta-generate-page-header-image-background-type', true ),
		'inner_container' 			  => get_post_meta( $id, '_meta-generate-page-header-inner-container', true ),
		'parallax' 					  => get_post_meta( $id, '_meta-generate-page-header-image-background-fixed', true ),
		'background_overlay'		  => get_post_meta( $id, '_meta-generate-page-header-image-background-overlay', true ),
		'full_screen' 				  => get_post_meta( $id, '_meta-generate-page-header-full-screen', true ),
		'vertical_center' 			  => get_post_meta( $id, '_meta-generate-page-header-vertical-center', true ),
		'alignment' 				  => get_post_meta( $id, '_meta-generate-page-header-image-background-alignment', true ),
		'padding' 					  => get_post_meta( $id, '_meta-generate-page-header-image-background-spacing', true ),
		'padding_unit' 				  => get_post_meta( $id, '_meta-generate-page-header-image-background-spacing-unit', true ),
		'x_padding' 				  => get_post_meta( $id, '_meta-generate-page-header-left-right-padding', true ),
		'x_padding_unit' 			  => get_post_meta( $id, '_meta-generate-page-header-left-right-padding-unit', true ),
		'text_color' 				  => get_post_meta( $id, '_meta-generate-page-header-image-background-text-color', true ),
		'background_color' 			  => get_post_meta( $id, '_meta-generate-page-header-image-background-color', true ),
		'link_color' 				  => get_post_meta( $id, '_meta-generate-page-header-image-background-link-color', true ),
		'link_color_hover' 			  => get_post_meta( $id, '_meta-generate-page-header-image-background-link-color-hover', true ),
		'merge' 					  => get_post_meta( $id, '_meta-generate-page-header-combine', true ),
		'absolute' 					  => get_post_meta( $id, '_meta-generate-page-header-absolute-position', true ),
		'custom_menu_colors' 		  => get_post_meta( $id, '_meta-generate-page-header-transparent-navigation', true ),
		'menu_background_color' 	  => get_post_meta( $id, '_meta-generate-page-header-navigation-background', true ),
		'menu_text_color' 			  => get_post_meta( $id, '_meta-generate-page-header-navigation-text', true ),
		'site_title_color' 			  => get_post_meta( $id, '_meta-generate-page-header-site-title', true ),
		'site_tagline_color' 		  => get_post_meta( $id, '_meta-generate-page-header-site-tagline', true ),
		'menu_background_color_hover' => get_post_meta( $id, '_meta-generate-page-header-navigation-background-hover', true ),
		'menu_text_color_hover' 	  => get_post_meta( $id, '_meta-generate-page-header-navigation-text-hover', true ),
		'menu_background_current' 	  => get_post_meta( $id, '_meta-generate-page-header-navigation-background-current', true ),
		'menu_text_current' 		  => get_post_meta( $id, '_meta-generate-page-header-navigation-text-current', true ),
		'background_video' 			  => get_post_meta( $id, '_meta-generate-page-header-video', true ),
		'background_video_ogv' 		  => get_post_meta( $id, '_meta-generate-page-header-video-ogv', true ),
		'background_video_webm' 	  => get_post_meta( $id, '_meta-generate-page-header-video-webm', true ),
		'background_video_overlay' 	  => get_post_meta( $id, '_meta-generate-page-header-video-overlay', true ),
		'logo_url' 					  => get_post_meta( $id, '_meta-generate-page-header-logo', true ),
		'logo_id' 					  => get_post_meta( $id, '_meta-generate-page-header-logo-id', true ),
		'navigation_logo_url' 		  => get_post_meta( $id, '_meta-generate-page-header-navigation-logo', true ),
		'navigation_logo_id' 		  => get_post_meta( $id, '_meta-generate-page-header-navigation-logo-id', true ),
	);

	return apply_filters( 'generate_page_header_options', $options );
}

/**
 * A helper function to return either the URL or ID of our images.
 *
 * Typically we should have an ID, but this add-on used to only
 * store the image URL, so we have to account for that.
 *
 * If we don't have a custom images, we check to see if we can use
 * the featured image instead.
 *
 * @since 1.4
 *
 * @param $type What to return.
 * @param $id The ID of our post.
 * @return int|string
 */
function generate_page_header_get_image( $type = 'URL', $id = '' ) {
	if ( '' == $id ) {
		$id = get_the_ID();
	}

	if ( is_attachment()  ) {
		return false;
	}

	$options = generate_page_header_get_options();

	if ( ! $options ) {
		return;
	}

	$image_id = $options[ 'image_id' ];
	$image_url = $options[ 'image_url' ];

	// If we're getting the URL
	if ( 'URL' == $type || 'ALL' == $type ) {

		// If we have an image ID, get the link
		if ( '' !== $image_id ) {
			return esc_url( wp_get_attachment_url( $image_id ) );
		}

		// If we don't have the ID, check for the URL
		if ( '' == $image_id && '' !== $image_url ) {
			return esc_url( $image_url );
		}
	}

	// If we're getting the ID
	if ( 'ID' == $type || 'ALL' == $type ) {
		// If we have the ID, return it
		if ( '' !== $image_id ) {
			return absint( $image_id );
		}

		// If we have a URL and no ID, return it
		if ( '' == $image_id && '' !== $image_url ) {
			if ( function_exists( 'attachment_url_to_postid' ) ) {
				return attachment_url_to_postid( esc_url( $image_url ) );
			}
		}
	}

	// Still here?
	return false;
}

/**
 * Output our image HTML.
 *
 * This function figures out if we need to crop/resize the image or not,
 * then it returns the image HTML based on that.
 *
 * @since 1.4
 */
function generate_page_header_get_image_output() {
	$options = generate_page_header_get_options();

	if ( ! $options ) {
		return;
	}

	$image_url = generate_page_header_get_image( 'URL' );
	$image_id = generate_page_header_get_image( 'ID' );

	// If we're not resizing the image, we can just output the HTML here
	if ( 'enable' !== $options[ 'image_resize' ] ) {
		return apply_filters( 'post_thumbnail_html',
			wp_get_attachment_image( $image_id, apply_filters( 'generate_page_header_default_size', 'full' ), '', array( 'itemprop' => 'image' ) ),
			get_the_ID(),
			$image_id,
			apply_filters( 'generate_page_header_default_size', 'full' ),
			''
		);
	}

	// Values when to ignore crop
	$ignore_crop = array( '', '0', '9999' );

	// Set our image attributes
	$image_atts = array(
		'width' => ( in_array( $options[ 'image_width' ], $ignore_crop ) ) ? 9999 : absint( $options[ 'image_width' ] ),
		'height' => ( in_array( $options[ 'image_height' ], $ignore_crop ) ) ? 9999 : absint( $options[ 'image_height' ] ),
		'crop' => ( in_array( $options[ 'image_width' ], $ignore_crop ) || in_array( $options[ 'image_height' ], $ignore_crop ) ) ? false : true
	);

	if ( ! empty( $image_atts ) ) {
		// If there's no height or width, empty the array
		if ( 9999 == $image_atts[ 'width' ] && 9999 == $image_atts[ 'height' ] ) {
			$image_atts = array();
		}
	}

	if ( ! empty( $image_atts ) && 'enable' == $options[ 'image_resize' ] ) {
		return apply_filters( 'post_thumbnail_html',
			wp_get_attachment_image( $image_id, array( $image_atts['width'], $image_atts['height'], $image_atts['crop'] ), '', array( 'itemprop' => 'image' ) ),
			get_the_ID(),
			$image_id,
			apply_filters( 'generate_page_header_default_size', 'full' ),
			''
		);
	} else {
		return apply_filters( 'post_thumbnail_html',
			wp_get_attachment_image( $image_id, apply_filters( 'generate_page_header_default_size', 'full' ), '', array( 'itemprop' => 'image' ) ),
			get_the_ID(),
			$image_id,
			apply_filters( 'generate_page_header_default_size', 'full' ),
			''
		);
	}
}

if ( ! function_exists( 'generate_combined_page_header_start' ) ) {
	add_action( 'generate_inside_merged_page_header', 'generate_combined_page_header_start', 0 );
	/**
	 * Add our generate-combined-header class into the page header.
	 *
	 * This makes the header use position:absolute and places it on top
	 * of the content below it.
	 */
	function generate_combined_page_header_start() {
		$options = generate_page_header_get_options();

		if ( ! $options ) {
			return;
		}

		if ( '' == $options[ 'merge' ] || '' == $options[ 'content' ] || '' == $options[ 'absolute' ] ) {
			return;
		}

		echo '<div class="generate-combined-header">';
	}
}

if ( ! function_exists( 'generate_combined_page_header_end' ) ) {
	add_action( 'generate_after_header', 'generate_combined_page_header_end', 9 );
	/**
	 * End our generate-combined-header element
	 */
	function generate_combined_page_header_end() {
		$options = generate_page_header_get_options();

		if ( ! $options ) {
			return;
		}

		if ( '' == $options[ 'merge' ] || '' == $options[ 'content' ] || '' == $options[ 'absolute' ] ) {
			return;
		}

		echo '</div><!-- .generate-combined-header -->';
	}
}

if ( ! function_exists( 'generate_page_header_enqueue' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_page_header_enqueue' );
	/**
	 * Add our scripts
	 */
	function generate_page_header_enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$options = generate_page_header_get_options();

		if ( ! $options ) {
			return;
		}

		if ( ! empty( $options[ 'full_screen' ] ) && '' !== $options[ 'content' ] ) {
			wp_enqueue_script( 'generate-page-header-full-height', plugin_dir_url( __FILE__ ) . "js/full-height{$suffix}.js", array('jquery'), GENERATE_PAGE_HEADER_VERSION, true );
		}

		if ( ! empty( $options[ 'parallax' ] ) && '' !== $options[ 'content' ] ) {
			wp_enqueue_script( 'generate-page-header-parallax', plugin_dir_url( __FILE__ ) . "js/parallax{$suffix}.js", array(), GENERATE_PAGE_HEADER_VERSION, true );
		}

		if ( ! empty( $options[ 'background_video' ] ) && '' !== $options[ 'content' ] ) {
			wp_enqueue_script( 'generate-page-header-video', plugin_dir_url( __FILE__ ) . "js/jquery.vide.min.js", array('jquery'), GENERATE_PAGE_HEADER_VERSION, true );
		}

		if ( ! empty( $options[ 'content' ] ) ) {
			wp_enqueue_style( 'generate-page-header', plugin_dir_url( __FILE__ ) . "css/page-header{$suffix}.css", array(), GENERATE_PAGE_HEADER_VERSION );
		}
	}
}

if ( ! function_exists( 'generate_page_header_css' ) ) {
	/**
	 * Generate the CSS in the <head> section using the Theme Customizer.
	 *
	 * @since 0.1
	 */
	function generate_page_header_css() {
		// Get our options
		$options = generate_page_header_get_options();

		// If we don't have any content, we don't need any of the below
		if ( empty( $options[ 'content' ] ) ) {
			return;
		}

		// See if we have a video
		$video = ( empty( $options[ 'background_video' ] ) && empty( $options[ 'background_video_ogv' ] ) && empty( $options[ 'background_video_webm' ] ) ) ? false : true;

		// Figure out our background color
		if ( '' !== $options[ 'background_video_overlay' ] && $options[ 'background_video' ] ) {
			if ( substr( $options[ 'background_video_overlay' ], 0, 4 ) === "rgba" ) {
				$background_color = $options[ 'background_video_overlay' ];
			} else {
				$background_color = generate_page_header_hex2rgba( $options[ 'background_video_overlay' ], apply_filters( 'generate_page_header_video_overlay', 0.7 ) ) . ' !important';
			}
		} elseif ( !empty( $options[ 'background_color' ] ) && ! $video ) {
			$background_color = $options[ 'background_color' ];
		} else {
			$background_color = null;
		}

		// Get our image URL
		$image_url = generate_page_header_get_image( 'URL' );

		// Check if we have a background image overlay
		$background_overlay = ! empty( $options[ 'background_image' ] ) && ! empty( $background_color ) && ! empty( $options[ 'background_overlay' ] ) ? true : false;

		$background_image = null;
		if ( ! empty( $options[ 'background_image' ] ) && ! $background_overlay && false == $video ) {
			$background_image = 'url(' . $image_url . ')';
		}

		if ( $background_overlay && ! $video ) {
			$background_image = 'linear-gradient(0deg, ' . $background_color . ',' . $background_color . '), url(' . $image_url . ')';
		}

		// Initiate our CSS class
		require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
		$css = new GeneratePress_Pro_CSS;

		// Page Header container
		$css->set_selector( '.generate-content-header' );

		if ( ! $background_overlay || $video ) {
			$css->add_property( 'background-color', esc_attr( $background_color ) );
		}

		$css->add_property( 'background-image', $background_image );

		if ( ! empty( $options[ 'parallax' ] ) ) {
			$css->add_property( 'background-position', 'center top' );
		} else {
			$css->add_property( 'background-position', 'center center' );
		}

		// Merged header container
		if ( '' !== $options[ 'merge' ] && ! empty( $options[ 'full_screen' ] ) ) {
			$css->set_selector( '.generate-combined-page-header' );
			$css->add_property( 'height', '100vh !important' );
		}

		// Remove the top margin from the container
		if ( 'fluid' == $options[ 'container_type' ] || '' !== $options[ 'merge' ] ) {
			$css->set_selector( '.separate-containers .generate-content-header.generate-page-header' );
			$css->add_property( 'margin-top', '0px' );
		}

		// Remove the content background color if an image or color is set
		$css->set_selector( '.inside-page-header' );
		if ( ! empty( $options[ 'background_image' ] ) || ! empty( $options[ 'background_color' ] ) ) {
			$css->add_property( 'background-color', 'transparent' );
		}

		$css->add_property( 'color', esc_attr( $options[ 'text_color' ] ) );

		// Add the page header content atts
		$css->set_selector( '.page-header-content-container' );
		$css->add_property( 'text-align', esc_attr( $options[ 'alignment' ] ) );

		if ( ! empty( $options[ 'padding' ] ) ) {
			$padding_unit = ( '%' == $options[ 'padding_unit' ] || 'percent' == $options[ 'padding_unit' ] ) ? '%' : 'px';
			$css->add_property( 'padding-top', absint( $options[ 'padding' ] ) . $padding_unit );
			$css->add_property( 'padding-bottom', absint( $options[ 'padding' ] ) . $padding_unit );
		}

		if ( isset( $options[ 'x_padding_unit' ] ) && ! empty( $options[ 'x_padding' ] ) ) {
			$x_padding_unit = ( '%' == $options[ 'x_padding_unit' ] || 'percent' == $options[ 'x_padding_unit' ] ) ? '%' : 'px';
			$css->add_property( 'padding-left', absint( $options[ 'x_padding' ] ) . $x_padding_unit );
			$css->add_property( 'padding-right', absint( $options[ 'x_padding' ] ) . $x_padding_unit );
		}

		$css->add_property( 'color', esc_attr( $options[ 'text_color' ] ) );

		// Set the content links
		$css->set_selector( '.page-header-content-container a:not(.button), .page-header-content-container a:not(.button):visited' );
		$css->add_property( 'color', esc_attr( $options[ 'link_color' ] ) );

		$css->set_selector( '.page-header-content-container a:not(.button):hover, .page-header-content-container a:not(.button):active' );
		$css->add_property( 'color', esc_attr( $options[ 'link_color_hover' ] ) );

		// Headings
		$css->set_selector( '.page-header-content-container h1, .page-header-content-container h2, .page-header-content-container h3, .page-header-content-container h4, .page-header-content-container h5' );
		$css->add_property( 'color', esc_attr( $options[ 'text_color' ] ) );

		// Set box-sizing if merged and contained
		$css->set_selector( '.generate-merged-header .inside-header' );
		if ( '' !== $options[ 'merge' ] && 'fluid' !== $options[ 'container_type' ] ) {
			$css->add_property( '-moz-box-sizing', 'border-box' );
			$css->add_property( '-webkit-box-sizing', 'border-box' );
			$css->add_property( 'box-sizing', 'border-box' );
		}

		// Remove the header background if we're merged
		if ( '' !== $options[ 'merge' ] ) {
			$css->set_selector( '.generate-merged-header .site-header' );
			$css->add_property( 'background', 'transparent' );
		}

		if ( '' !== $options[ 'custom_menu_colors' ] ) {
			// The menu background color
			$css->set_selector( '.generate-merged-header .main-navigation:not(.is_stuck):not(.toggled):not(.mobile-header-navigation)' );
			$menu_background_color = ( '' == $options[ 'menu_background_color' ] ) ? 'transparent' : $options[ 'menu_background_color' ];
			$css->add_property( 'background', $menu_background_color );

			// The menu item text color
			$css->set_selector( '.generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li > a, .generate-merged-header #site-navigation:not(.toggled) .menu-toggle,.generate-merged-header #site-navigation:not(.toggled) .menu-toggle:hover,.generate-merged-header #site-navigation:not(.toggled) .menu-toggle:focus,.generate-merged-header #site-navigation:not(.toggled) .mobile-bar-items a, .generate-merged-header #site-navigation:not(.toggled) .mobile-bar-items a:hover, .generate-merged-header #site-navigation:not(.toggled) .mobile-bar-items a:focus' );
			$css->add_property( 'color', esc_attr( $options[ 'menu_text_color' ] ) );

			// The menu item hover background/text color
			$css->set_selector( '.generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li:hover > a, .generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li:focus > a, .generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li.sfHover > a' );
			if ( '' == $options[ 'menu_background_color_hover' ] ) {
				$css->add_property( 'background', $menu_background_color );
			} else {
				$css->add_property( 'background', $options[ 'menu_background_color_hover' ] );
			}

			if ( '' !== $options[ 'menu_text_color_hover' ] ) {
				$css->add_property( 'color', esc_attr( $options[ 'menu_text_color_hover' ] ) );
			} else {
				$css->add_property( 'color', esc_attr( $options[ 'menu_text_color' ] ) );
			}

			// The current menu item background/text color
			$css->set_selector( '.generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li[class*="current-menu-"] > a, .generate-merged-header #site-navigation:not(.toggled) .main-nav > ul > li[class*="current-menu-"]:hover > a' );
			if ( '' == $options[ 'menu_background_current' ] ) {
				$css->add_property( 'background', $menu_background_color );
			} else {
				$css->add_property( 'background', $options[ 'menu_background_current' ] );
			}

			if ( '' !== $options[ 'menu_text_current' ] ) {
				$css->add_property( 'color', esc_attr( $options[ 'menu_text_current' ] ) );
			} else {
				$css->add_property( 'color', esc_attr( $options[ 'menu_text_color' ] ) );
			}
		}

		// The site title color
		$css->set_selector( '.generate-merged-header .main-title a, .generate-merged-header .main-title a:hover, .generate-merged-header .main-title a:visited' );
		$css->add_property( 'color', esc_attr( $options[ 'site_title_color' ] ) );

		// The site tagline color
		$css->set_selector( '.generate-merged-header .site-description' );
		$css->add_property( 'color', esc_attr( $options[ 'site_tagline_color' ] ) );

		return $css->css_output();
	}
}

if ( ! function_exists( 'generate_page_header_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_page_header_enqueue_scripts', 100 );
	/**
	 * Enqueue scripts and styles
	 */
	function generate_page_header_enqueue_scripts() {
		wp_add_inline_style( 'generate-style', generate_page_header_css() );
	}
}

add_filter( 'generate_page_header_location','generate_page_header_force_above_content' );
/**
 * Forces the page header to be above the content/below the header if it's set
 * to merge or full width.
 *
 * @since 1.4
 */
function generate_page_header_force_above_content( $location ) {
	$options = generate_page_header_get_options();

	if ( ! $options ) {
		return $location;
	}

	if ( '' !== $options[ 'merge' ] && '' !== $options[ 'content' ] ) {
		$location = 'above-content';
	}

	if ( 'fluid' == $options[ 'container_type' ] ) {
		$location = 'above-content';
	}

	if ( get_post_meta( get_the_ID(), '_generate_use_sections', true ) ) {
		$location = 'above-content';
	}

	return $location;
}

if ( ! function_exists( 'generate_page_header_combined' ) ) {
	add_action( 'generate_before_header', 'generate_page_header_combined', 5 );
	/**
	 * Add the start to our page header containers if we're using a merged header
	 *
	 * Doing this allows us to actually wrap the header in our page header element
	 * instead of just making the header position: absolute.
	 */
	function generate_page_header_combined() {
		// Get our options
		$options = generate_page_header_get_options();

		if ( ! $options ) {
			return;
		}

		// Bail if merge isn't activated
		if ( '' == $options[ 'merge' ] ) {
			return;
		}

		// Bail if we're on a single post and it's set to hide
		if ( 'hide' == generate_get_page_header_location() ) {
			return;
		}

		generate_page_header_area_start_container( 'page-header-image', 'page-header-content' );
	}
}

if ( ! function_exists( 'generate_page_header' ) ) {
	/**
	 * Add page header above content
	 *
	 * @since 0.3
	 */
	function generate_page_header() {
		$image_class = 'page-header-image';
		$content_class = 'page-header-content';

		if ( is_single() ) {
			$image_class = 'page-header-image-single';
			$content_class = 'page-header-content-single';

			if ( 'below-title' == generate_get_page_header_location() ) {
				$image_class = 'page-header-image-single page-header-below-title';
				$content_class = 'page-header-content-single page-header-below-title';
			}
		}

		generate_page_header_area( $image_class, $content_class );
	}
}

if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
	/**
	 * Set default options for the Customizer
	 *
	 * These are mainly for the Blog Page Header options.
	 */
	function generate_page_header_get_defaults() {
		$generate_page_header_defaults = array(
			'page_header_position' => 'above-content',
			'post_header_position' => 'inside-content',
			'page_header_image' => '',
			'page_header_logo' => '',
			'page_header_navigation_logo' => '',
			'page_header_url' => '',
			'page_header_hard_crop' => 'disable',
			'page_header_image_width' => '1200',
			'page_header_image_height' => '0',
			'page_header_content' => '',
			'page_header_add_paragraphs' => '0',
			'page_header_add_padding' => '0',
			'page_header_image_background' => '0',
			'page_header_add_parallax' => '0',
			'page_header_full_screen' => '0',
			'page_header_vertical_center' => '0',
			'page_header_container_type' => '',
			'page_header_text_alignment' => 'left',
			'page_header_padding' => '',
			'page_header_padding_unit' => '',
			'page_header_background_color' => '',
			'page_header_text_color' => '',
			'page_header_link_color' => '',
			'page_header_link_color_hover' => '',
			'page_header_video' => '',
			'page_header_video_ogv' => '',
			'page_header_video_webm' => '',
			'page_header_video_overlay' => '',
			'page_header_combine' => '',
			'page_header_absolute_position' => '',
			'page_header_site_title' => '',
			'page_header_site_tagline' => '',
			'page_header_transparent_navigation' => '',
			'page_header_navigation_text' => '',
			'page_header_navigation_background_hover' => '',
			'page_header_navigation_text_hover' => '',
			'page_header_navigation_background_current' => '',
			'page_header_navigation_text_current' => ''
		);

		return apply_filters( 'generate_page_header_option_defaults', $generate_page_header_defaults );
	}
}

if ( ! function_exists( 'generate_page_header_customize_register' ) ) {
	add_action( 'customize_register', 'generate_page_header_customize_register', 100 );
	/**
	 * Add our page header layout Customizer settings.
	 * Would like to revamp these.
	 */
	function generate_page_header_customize_register( $wp_customize ) {
		// Get our defaults
		$defaults = generate_page_header_get_defaults();

		// Get our Customizer helpers
		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Use the Layout panel in the free theme if it exists
		if ( $wp_customize->get_panel( 'generate_layout_panel' ) ) {
			$section = 'generate_layout_page_header';
			$wp_customize->add_section(
				'generate_layout_page_header',
				array(
					'title' => __( 'Page Header', 'gp-premium' ),
					'capability' => 'edit_theme_options',
					'priority' => 35,
					'panel' => 'generate_layout_panel'
				)
			);
		} else {
			$section = 'layout_section';
		}

		$wp_customize->add_setting(
			'generate_page_header_settings[page_header_position]',
			array(
				'default' => $defaults['page_header_position'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices'
			)
		);

		// Location
		$wp_customize->add_control(
			'generate_page_header_settings[page_header_position]',
			array(
				'type' => 'select',
				'label' => __( 'Page Header Location', 'gp-premium' ),
				'section' => $section,
				'choices' => array(
					'above-content' => __( 'Above Content Area', 'gp-premium' ),
					'inside-content' => __( 'Inside Content Area', 'gp-premium' )
				),
				'settings' => 'generate_page_header_settings[page_header_position]',
				'priority' => 100
			)
		);

		$wp_customize->add_setting(
			'generate_page_header_settings[post_header_position]',
			array(
				'default' => $defaults['post_header_position'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices'
			)
		);

		// Single post header location
		$wp_customize->add_control(
			'generate_page_header_settings[post_header_position]',
			array(
				'type' => 'select',
				'label' => __( 'Single Post Header Location', 'gp-premium' ),
				'section' => $section,
				'choices' => array(
					'above-content' => __( 'Above Content Area', 'gp-premium' ),
					'inside-content' => __( 'Inside Content Area', 'gp-premium' ),
					'below-title' => __( 'Below Post Title', 'gp-premium' ),
					'hide'			=> __( 'Hide', 'gp-premium' )
				),
				'settings' => 'generate_page_header_settings[post_header_position]',
				'priority' => 101
			)
		);
	}
}

if ( ! function_exists( 'generate_get_attachment_id_by_url' ) ) {
	/**
	* Return an ID of an attachment by searching the database with the file URL.
	*
	* First checks to see if the $url is pointing to a file that exists in
	* the wp-content directory. If so, then we search the database for a
	* partial match consisting of the remaining path AFTER the wp-content
	* directory. Finally, if a match is found the attachment ID will be
	* returned.
	*
	* @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
	*
	* @return int|null $attachment Returns an attachment ID, or null if no attachment is found
	*/
	function generate_get_attachment_id_by_url( $attachment_url = '' ) {
		global $wpdb;

		$attachment_id = false;

		// If there is no url, return.
		if ( '' == $attachment_url ) {
			return;
		}

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}
}

if ( ! function_exists( 'generate_page_header_hex2rgba' ) ) {
	/**
	 * Convert hex to RGBA
	 */
	function generate_page_header_hex2rgba( $color, $opacity = false ) {
		$default = 'rgb(0,0,0)';

		// Return default if no color provided
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert hexadec to rgb
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb('.implode( ",", $rgb ).')';
		}

		//Return rgb(a) color string
		return $output;
	}
}

if ( ! function_exists( 'generate_page_header_replace_logo' ) ) {
	/**
	 * Check to see if we should replace our logo
	 * Utlilised in generate_page_header_setup()
	 */
	function generate_page_header_replace_logo( $logo ) {
		if ( generate_page_header_logo_exists() ) {
			$options = generate_page_header_get_options();

			if ( ! $options ) {
				return $logo;
			}

			return $options[ 'logo_url' ];
		}

		return $logo;
	}
}

if ( ! function_exists( 'generate_page_header_replace_navigation_logo' ) ) {
	/**
	 * Check to see if we should replace our navigation logo
	 * Utlilised in generate_page_header_setup()
	 */
	function generate_page_header_replace_navigation_logo( $logo ) {
		if ( generate_page_header_navigation_logo_exists() ) {
			$options = generate_page_header_get_options();

			if ( ! $options ) {
				return $logo;
			}

			return $options[ 'navigation_logo_url' ];
		}

		return $logo;
	}
}

if ( ! function_exists( 'generate_page_header_logo_exists' ) ) {
	/**
	 * This is an active_callback
	 * Check if our page header logo exists
	 */
	function generate_page_header_logo_exists() {
		if ( function_exists( 'generate_get_defaults' ) ) {
			$generate_settings = wp_parse_args(
				get_option( 'generate_settings', array() ),
				generate_get_defaults()
			);
		}

		if ( function_exists( 'generate_construct_logo' ) && ( '' !== $generate_settings[ 'logo' ] || get_theme_mod( 'custom_logo' ) ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_page_header_navigation_logo_exists' ) ) {
	/**
	 * This is an active_callback
	 * Check if our page header logo exists
	 */
	function generate_page_header_navigation_logo_exists() {
		if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
			$generate_menu_plus_settings = wp_parse_args(
				get_option( 'generate_menu_plus_settings', array() ),
				generate_menu_plus_get_defaults()
			);

			if ( '' !== $generate_menu_plus_settings[ 'sticky_menu_logo' ] ) {
				return true;
			}
		}

		return false;
	}
}

/**
 * Gets our set page header location and filters it.
 *
 * @since 1.4
 */
function generate_get_page_header_location() {
	$generate_page_header_settings = wp_parse_args(
		get_option( 'generate_page_header_settings', array() ),
		generate_page_header_get_defaults()
	);

	$location = $generate_page_header_settings['page_header_position'];

	if ( is_single() ) {
		$location = $generate_page_header_settings['post_header_position'];
	}

	return apply_filters( 'generate_page_header_location', $location );
}

/**
 * Searches for template tags in the content and replaces them with
 * their respective functions.
 *
 * @since 1.4
 *
 * @param $content The content to look through.
 * @return string The resulting content.
 */
function generate_page_header_template_tags( $content ) {
	$search = array();
	$replace = array();

	$search[] = '{{post_title}}';
	if ( is_singular() ) {
		$replace[] = get_the_title();
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$replace[] = get_queried_object()->name;
	}

	if ( is_singular() ) {
		// Date
		$time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string .= '<time class="updated" datetime="%3$s" itemprop="dateModified" style="display:none;">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$time_string
		);

		$search[] = '{{post_date}}';
		$replace[] = $date;

		// Author
		global $post;
		$author_id = $post->post_author;

		$author = sprintf( '<span class="author vcard" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author"><a class="url fn n" href="%1$s" title="%2$s" rel="author" itemprop="url"><span class="author-name" itemprop="name">%3$s</span></a></span>',
			esc_url( get_author_posts_url( $author_id ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'gp-premium' ), get_the_author_meta( 'display_name', $author_id ) ) ),
			esc_html( get_the_author_meta( 'display_name', $author_id ) )
		);

		$search[] = '{{post_author}}';
		$replace[] = $author;

		// Post terms
		if ( strpos( $content, '{{post_terms' ) !== false ) {
			$data = preg_match_all( '/{{post_terms.([^}]*)}}/', $content, $matches );
			foreach ( $matches[1] as $match ) {
				$search[] = '{{post_terms.' . $match . '}}';
				$replace[] = get_the_term_list( get_the_ID(), $match, apply_filters( 'generate_page_header_terms_before', '' ), apply_filters( 'generate_page_header_terms_separator', ', ' ), apply_filters( 'generate_page_header_terms_after', '' ) );
			}
		}

		// Custom field
		if ( strpos( $content, '{{custom_field' ) !== false ) {
			$data = preg_match_all( '/{{custom_field.([^}]*)}}/', $content, $matches );
			foreach ( $matches[1] as $match ) {
				if ( null !== get_post_meta( get_the_ID(), $match, true ) && '_thumbnail_id' !== $match ) {
					$search[] = '{{custom_field.' . $match . '}}';
					$replace[] = get_post_meta( get_the_ID(), $match, true );
				}
			}

			$thumbnail_id = get_post_meta( get_the_ID(), '_thumbnail_id', true );
			if ( null !== $thumbnail_id ) {
				$search[] = '{{custom_field._thumbnail_id}}';
				$replace[] = wp_get_attachment_image( $thumbnail_id, apply_filters( 'generate_page_header_thumbnail_id_size', 'medium' ) );
			}
		}
	}

	// Taxonomy description
	if ( is_tax() || is_category() || is_tag() ) {
		if ( strpos( $content, '{{custom_field' ) !== false ) {
			$search[] = '{{custom_field.description}}';
			$replace[] = term_description( get_queried_object()->term_id, get_queried_object()->taxonomy );
		}
	}

	return str_replace( $search, $replace, $content );
}

/**
 * When the post title, author or date are in the Page Header, they appear outside of the
 * hentry element. This causes errors in Google Search Console.
 *
 * @since 1.7
 *
 * @param array $classes
 * @return array
 */
function generate_page_header_remove_hentry( $classes ) {
	$classes = array_diff( $classes, array( 'hentry' ) );

	return $classes;
}

add_action( 'admin_init', 'generate_page_header_transfer_blog_header' );
/**
 * Transfers any blog page header settings from the Customizer into a new page header.
 *
 * @since 1.4
 */
function generate_page_header_transfer_blog_header() {
	// Get our migration settings
	$migration_settings = get_option( 'generate_migration_settings', array() );

	// If we've already ran this function, bail
	if ( isset( $migration_settings[ 'blog_page_header' ] ) && 'true' == $migration_settings[ 'blog_page_header' ] ) {
		return;
	}

	$settings = wp_parse_args(
		get_option( 'generate_page_header_options', array() ),
		generate_page_header_get_defaults()
	);

	$defaults = generate_page_header_get_defaults();

	if ( '' !== $settings[ 'page_header_image' ] ) {
		if ( function_exists( 'attachment_url_to_postid' ) ) {
			$image = attachment_url_to_postid( esc_url( $settings[ 'page_header_image' ] ) );
		}
	}

	if ( ! isset( $image ) ) {
		$image = $settings[ 'page_header_image' ];
	}

	$meta = array(
		'_thumbnail_id'													=> is_int( $image ) ? $image : '',
		'_meta-generate-page-header-image'								=> ! is_int( $image ) ? $settings[ 'page_header_image' ] : '',
		'_meta-generate-page-header-image-id'							=> '',
		'_meta-generate-page-header-image-link'							=> $settings[ 'page_header_url' ],
		'_meta-generate-page-header-enable-image-crop'					=> 'disable' == $settings[ 'page_header_hard_crop' ] ? '' : $settings[ 'page_header_hard_crop' ],
		'_meta-generate-page-header-image-width'						=> ! $image || '' == $image ? '' : $settings[ 'page_header_image_width' ],
		'_meta-generate-page-header-image-height'						=> ! $image || '' == $image ? '' : $settings[ 'page_header_image_height' ],
		'_meta-generate-page-header-content'							=> $settings[ 'page_header_content' ],
		'_meta-generate-page-header-content-autop'						=> $settings[ 'page_header_add_paragraphs' ] ? 'yes' : '',
		'_meta-generate-page-header-content-padding'					=> $settings[ 'page_header_add_padding' ] ? 'yes' : '',
		'_meta-generate-page-header-image-background'					=> $settings[ 'page_header_image_background' ] ? 'yes' : '',
		'_meta-generate-page-header-image-background-type'				=> $settings[ 'page_header_container_type' ],
		'_meta-generate-page-header-image-background-fixed'				=> $settings[ 'page_header_add_parallax' ] ? 'yes' : '',
		'_meta-generate-page-header-full-screen'						=> $settings[ 'page_header_full_screen' ] ? 'yes' : '',
		'_meta-generate-page-header-vertical-center'					=> $settings[ 'page_header_vertical_center' ] ? 'yes' : '',
		'_meta-generate-page-header-image-background-alignment'			=> 'left' == $settings[ 'page_header_text_alignment' ] ? '' : $settings[ 'page_header_text_alignment' ],
		'_meta-generate-page-header-image-background-spacing'			=> $settings[ 'page_header_padding' ],
		'_meta-generate-page-header-image-background-spacing-unit'		=> 'percent' == $settings[ 'page_header_padding_unit' ] ? '%' : '',
		'_meta-generate-page-header-image-background-text-color'		=> $settings[ 'page_header_text_color' ],
		'_meta-generate-page-header-image-background-color'				=> $settings[ 'page_header_background_color' ],
		'_meta-generate-page-header-image-background-link-color'		=> $settings[ 'page_header_link_color' ],
		'_meta-generate-page-header-image-background-link-color-hover'	=> $settings[ 'page_header_link_color_hover' ],
		'_meta-generate-page-header-combine'							=> $settings[ 'page_header_combine' ] ? 'yes' : '',
		'_meta-generate-page-header-absolute-position'					=> $settings[ 'page_header_absolute_position' ] ? 'yes' : '',
		'_meta-generate-page-header-transparent-navigation'				=> $settings[ 'page_header_transparent_navigation' ] ? 'yes' : '',
		'_meta-generate-page-header-navigation-text'					=> $settings[ 'page_header_navigation_text' ],
		'_meta-generate-page-header-site-title'							=> $settings[ 'page_header_site_title' ],
		'_meta-generate-page-header-site-tagline'						=> $settings[ 'page_header_site_tagline' ],
		'_meta-generate-page-header-navigation-background-hover'		=> $settings[ 'page_header_navigation_background_hover' ],
		'_meta-generate-page-header-navigation-text-hover'				=> $settings[ 'page_header_navigation_text_hover' ],
		'_meta-generate-page-header-navigation-background-current'		=> $settings[ 'page_header_navigation_background_current' ],
		'_meta-generate-page-header-navigation-text-current'			=> $settings[ 'page_header_navigation_text_current' ],
		'_meta-generate-page-header-video'								=> $settings[ 'page_header_video' ],
		'_meta-generate-page-header-video-ogv'							=> $settings[ 'page_header_video_ogv' ],
		'_meta-generate-page-header-video-webm'							=> $settings[ 'page_header_video_webm' ],
		'_meta-generate-page-header-video-overlay'						=> $settings[ 'page_header_video_overlay' ],
		'_meta-generate-page-header-logo'								=> $settings[ 'page_header_logo' ],
		'_meta-generate-page-header-logo-id'							=> '',
		'_meta-generate-page-header-navigation-logo'					=> $settings[ 'page_header_navigation_logo' ],
		'_meta-generate-page-header-navigation-logo-id'					=> '',
	);

	// Strip empty values (but keep 0s)
	$meta = array_filter( $meta, 'strlen' );

	if ( empty( $meta ) ) {
		return;
	}

	$blog_page_header = array(
		'post_title' => 'Blog Page Header',
		'post_type' => 'generate_page_header',
		'post_status' => 'publish',
		'meta_input' => $meta
	);

	$page_exists = get_page_by_title( 'Blog Page Header', 'OBJECT', 'generate_page_header' );

	if ( null == $page_exists ) {
		wp_insert_post( $blog_page_header );
		$page_exists = get_page_by_title( 'Blog Page Header', 'OBJECT', 'generate_page_header' );

		// If we've created our page header, and content or an image exists
		if ( $page_exists ) {
			$global_locations = wp_parse_args( get_option( 'generate_page_header_global_locations', array() ), '' );
			$new_blog_page_header = array();
			$new_blog_page_header[ 'blog' ] = $page_exists->ID;
			$new_blog_page_header_settings = wp_parse_args( $new_blog_page_header, $global_locations );
			update_option( 'generate_page_header_global_locations', $new_blog_page_header_settings );
			//delete_option( 'generate_page_header_options' );
		}
	}

	// Update our migration option so we don't need to run this again
	$updated = array();
	$updated[ 'blog_page_header' ] = 'true';
	$new_migration_settings = wp_parse_args( $updated, $migration_settings );
	update_option( 'generate_migration_settings', $new_migration_settings );
}
