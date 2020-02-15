<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

include_once( 'metaboxes/metabox-functions.php' );

if ( ! function_exists( 'generate_sections_page_template' ) ) {
	add_filter( 'template_include', 'generate_sections_page_template' );
	/**
	 * Use our custom template if sections are enabled
	 */
	function generate_sections_page_template( $template ) {

		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		if ( is_home() || is_archive() || is_search() || is_attachment() || is_tax() ) {
			return $template;
		}

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {

			$new_template = dirname( __FILE__ ) . '/templates/template.php';

			if ( '' != $new_template ) {
				return $new_template;
			}
		}
		return $template;

	}
}

if ( ! function_exists( 'generate_sections_show_excerpt' ) ) {
	add_filter( 'generate_show_excerpt', 'generate_sections_show_excerpt' );
	/**
	 * If Sections is enabled on a post, make sure we use the excerpt field on the blog page
	 */
	function generate_sections_show_excerpt( $show_excerpt ) {
		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {
			return true;
		}

		return $show_excerpt;
	}
}

if ( ! function_exists( 'generate_sections_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_sections_styles' );
	/**
	 * Enqueue necessary scripts if sections are enabled
	 */
	function generate_sections_styles() {

		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		// Bail if we're on a posts page
		if ( is_home() || is_archive() || is_search() || is_attachment() || is_tax() ) {
			return;
		}

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {
			wp_enqueue_style( 'generate-sections-styles', plugin_dir_url( __FILE__ ) . 'css/style.min.css' );
			wp_enqueue_script( 'generate-sections-parallax', plugin_dir_url( __FILE__ ) . 'js/parallax.min.js', array(), GENERATE_SECTIONS_VERSION, true );
		}
	}
}

if ( ! function_exists( 'generate_sections_body_classes' ) ) {
	add_filter( 'body_class', 'generate_sections_body_classes' );
	/**
	 * Add classes to our <body> element when sections are enabled
	 */
	function generate_sections_body_classes( $classes ) {
		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';
		$sidebars = apply_filters( 'generate_sections_sidebars', false );

		// Bail if we're on a posts page
		if ( is_home() || is_archive() || is_search() || is_attachment() || is_tax() ) {
			return $classes;
		}

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {
			$classes[] = 'generate-sections-enabled';
		}

		if ( ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) && ! $sidebars ) {
			$classes[] = 'sections-no-sidebars';
		}

		if ( ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) && $sidebars ) {
			$classes[] = 'sections-sidebars';
		}

		return $classes;
	}
}

if ( ! function_exists( 'generate_sections_add_css' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_sections_add_css', 500 );
	/**
	 * Create the CSS for our sections
	 */
	function generate_sections_add_css() {
		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		if ( ! isset( $use_sections['use_sections'] ) ) {
			return;
		}

		if ( 'true' !== $use_sections['use_sections'] ) {
			return;
		}

		if ( is_home() || is_archive() || is_search() || is_attachment() || is_tax() ) {
			return;
		}

		if ( function_exists( 'generate_spacing_get_defaults' ) ) {
			$spacing_settings = wp_parse_args(
				get_option( 'generate_spacing_settings', array() ),
				generate_spacing_get_defaults()
			);

			$left_padding = $spacing_settings['content_left'];
			$right_padding = $spacing_settings['content_right'];
			$mobile_padding_left = ( isset( $spacing_settings[ 'mobile_content_left' ] ) ) ? $spacing_settings[ 'mobile_content_left' ] : 30;
			$mobile_padding_right = ( isset( $spacing_settings[ 'mobile_content_right' ] ) ) ? $spacing_settings[ 'mobile_content_right' ] : 30;
		} else {
			$right_padding = 40;
			$left_padding = 40;
			$mobile_padding = 30;
		}

		$sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_sections', TRUE) : '';

		// check if the repeater field has rows of data
		if ( $sections && '' !== $sections ) {

			$css = '.generate-sections-inside-container {padding-left:' . $left_padding . 'px;padding-right:' . $right_padding . 'px;}';
			// loop through the rows of data
			$i = 0;
			foreach ( $sections['sections'] as $section ) :
				$i++;

				// Get image details
				$image_id = ( isset( $section['background_image'] ) && '' !== $section['background_image'] ) ? intval( $section['background_image'] ) : '';
				$image_url = ( '' !== $image_id ) ? wp_get_attachment_image_src( $image_id, 'full' ) : '';

				// Get the padding type
				$padding_type = apply_filters( 'generate_sections_padding_type','px' );

				// If someone has changed the padding type using a filter, use their value
				if ( 'px' !== $padding_type ) {
					$top_padding_type = $padding_type;
					$bottom_padding_type = $padding_type;
				} else {
					$top_padding_type = ( isset( $section['top_padding_unit'] ) && '' !== $section['top_padding_unit'] ) ? $section['top_padding_unit'] : $padding_type;
					$bottom_padding_type = ( isset( $section['bottom_padding_unit'] ) && '' !== $section['bottom_padding_unit'] ) ? $section['bottom_padding_unit'] : $padding_type;
				}

				// Default padding top
				$padding_top = apply_filters( 'generate_sections_default_padding_top','40' );

				// Default padding bottom
				$padding_bottom = apply_filters( 'generate_sections_default_padding_bottom','40' );

				$custom_id = ( isset( $section['custom_id'] ) ) ? $section['custom_id'] : '';
				$custom_id = ( '' == $custom_id ) ? "generate-section-$i" : $custom_id;

				// Get the values
				$background_color = ( isset( $section['background_color'] ) && '' !== $section['background_color'] ) ? 'background-color:' . esc_attr( $section['background_color'] ) . ';' : '';
				$background_image = ( ! empty( $image_url[0] ) ) ? 'background-image:url(' . esc_url( $image_url[0] ) . ');' : '';

				if ( isset( $section['background_color_overlay'] ) && '' !== $section['background_color_overlay'] ) {
					if ( '' !== $background_image && '' !== $background_color ) {
						$background_image = 'background-image:linear-gradient(0deg, ' . $section['background_color'] . ',' . $section['background_color'] . '), url(' . esc_url( $image_url[0] ) . ');';
						$background_color = '';
					}
				}

				$text_color = ( isset( $section['text_color'] ) && '' !== $section['text_color'] ) ? 'color:' . esc_attr( $section['text_color'] ) . ';' : '';
				$link_color = ( isset( $section['link_color'] ) && '' !== $section['link_color'] ) ? 'color:' . esc_attr( $section['link_color'] ) . ';' : '';
				$link_color_hover = ( isset( $section['link_color_hover'] ) && '' !== $section['link_color_hover'] ) ? 'color:' . esc_attr( $section['link_color_hover'] ) . ';' : '';
				$top_padding = ( isset( $section['top_padding'] ) && '' !== $section['top_padding'] ) ? 'padding-top:' . absint( $section['top_padding'] ) . $top_padding_type . ';' : 'padding-top:' . $padding_top . 'px;';
				$bottom_padding = ( isset( $section['bottom_padding'] ) && '' !== $section['bottom_padding'] ) ? 'padding-bottom:' . absint( $section['bottom_padding'] ) . $bottom_padding_type . ';' : 'padding-bottom:' . $padding_bottom . 'px;';

				// Outer container
				if ( '' !== $background_color || '' !== $background_image ) {
					$css .= '#' . $custom_id . '.generate-sections-container{' . $background_color . $background_image . '}';
				}

				// Inner container
				if ( '' !== $top_padding || '' !== $bottom_padding || '' !== $text_color ) {
					$css .= '#' . $custom_id . ' .generate-sections-inside-container{' . $top_padding . $bottom_padding . $text_color . '}';
				}

				// Link color
				if ( '' !== $link_color ) {
					$css .= '#' . $custom_id . ' a,#generate-section-' . $i . ' a:visited{' . $link_color . '}';
				}

				// Link color hover
				if ( '' !== $link_color_hover ) {
					$css .= '#' . $custom_id . ' a:hover{' . $link_color_hover . '}';
				}

				$mobile = generate_premium_get_media_query( 'mobile' );
				$css .= '@media ' . esc_attr( $mobile ) . ' {.generate-sections-inside-container {padding-left: ' . $mobile_padding_left . 'px;padding-right: ' . $mobile_padding_right . 'px;}}';
			endforeach;

			// Build CSS
			wp_add_inline_style( 'generate-style', $css );

		}
	}
}

if ( ! function_exists( 'generate_sections_filter_admin_init' ) ) {
	add_action( 'admin_init', 'generate_sections_filter_admin_init' );
	/*
	 * Recreate the default filters on the_content
	 * this will make it much easier to output the meta content with proper/expected formatting
	*/
	function generate_sections_filter_admin_init() {
		if ( user_can_richedit() ) {
			add_filter( 'generate_section_content', 'convert_smilies'    );
			add_filter( 'generate_section_content', 'convert_chars'      );
			add_filter( 'generate_section_content', 'wpautop'            );
			add_filter( 'generate_section_content', 'shortcode_unautop'  );
			add_filter( 'generate_section_content', 'prepend_attachment' );
		}
	}
}

if ( ! function_exists( 'generate_sections_filter' ) ) {
	add_action( 'init', 'generate_sections_filter' );
	/*
	 * Recreate the default filters on the_content
	 * this will make it much easier to output the meta content with proper/expected formatting
	*/
	function generate_sections_filter() {
		if ( is_admin() ) {
			return;
		}

		add_filter( 'generate_section_content', 'convert_smilies'    );
		add_filter( 'generate_section_content', 'convert_chars'      );
		add_filter( 'generate_section_content', 'wpautop'            );
		add_filter( 'generate_section_content', 'shortcode_unautop'  );
		add_filter( 'generate_section_content', 'prepend_attachment' );
		add_filter( 'generate_section_content', 'do_shortcode');

		add_filter( 'generate_the_section_content', array($GLOBALS['wp_embed'], 'autoembed'), 9 );
	}
}

if ( ! function_exists( 'generate_sections_save_content' ) ) {
	add_action( 'save_post', 'generate_sections_save_content', 99, 4 );
	/*
	 * When we save our post, grab all of the section content and save it as regular content.
	 *
	 * This will prevent content loss/theme lock.
	*/
	function generate_sections_save_content( $post_id, $post ) {

		if ( ! isset( $_POST['_generate_sections_nonce'] ) || ! wp_verify_nonce( $_POST['_generate_sections_nonce'], 'generate_sections_nonce' ) ) {
	        return;
	    }

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	        return;
	    }

	    if ( ! current_user_can('edit_post', $post_id ) ) {
	        return;
	    }

		// See if we're using sections
		$use_sections = get_post_meta( $post_id, '_generate_use_sections', true);

		// Make sure use sections exists and that we're not saving a revision
		if ( ! isset( $use_sections['use_sections'] ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Return if sections are set to false
		if ( 'true' !== $use_sections['use_sections'] ) {
			return;
		}

		// Get our sections
		$sections = get_post_meta( $post_id, '_generate_sections', true );

		// Return if there's nothing in our sections
		if ( ! isset( $sections ) || '' == $sections ) {
			return;
		}

		// Prevent infinite loop
	    remove_action( 'save_post', 'generate_sections_save_content', 99, 4 );

		// Let's do some stuff if sections aren't empty
		if ( '' !== $sections ) {
			// Set up our content variable
			$content = '';

			// Loop through each section and add our content to the content variable
			foreach ( $sections['sections'] as $section ) {
				$content .= ( isset( $section['content'] ) && '' !== $section['content'] ) ? $section['content'] . "\n\n" : '';
			}

			// Now update our post if we have section content
			if ( '' !== $content ) {
				$post->post_content = $content;
				wp_update_post( $post, true );
			}
		}

		// Re-hook the save_post action
	    add_action('save_post', 'generate_sections_save_content', 99, 4);
	}
}
