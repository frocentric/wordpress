<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'generate_sections_admin_body_class' ) ) {
	add_filter( 'admin_body_class', 'generate_sections_admin_body_class' );

	function generate_sections_admin_body_class( $classes ) {
		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {
			$classes .= ' generate-sections-enabled';
		}

	    return $classes;
	}
}

if ( ! function_exists( 'generate_sections_content_width' ) ) {
	add_action( 'wp', 'generate_sections_content_width', 50 );
	/**
	 * Set our content width when sections are enabled
	 */
	function generate_sections_content_width() {
		global $post;
		$use_sections = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate_use_sections', TRUE) : '';

		if ( isset( $use_sections['use_sections'] ) && 'true' == $use_sections['use_sections'] ) {
			global $content_width;
			$content_width = 2000;
		}
	}
}

if ( ! function_exists( 'generate_sections_add_metaboxes' ) ) {
	add_action( 'add_meta_boxes', 'generate_sections_add_metaboxes', 5 );
	/*
	 * Functions for creating the metaboxes
	 */
	function generate_sections_add_metaboxes() {

		$post_types = apply_filters( 'generate_sections_post_types', array( 'page', 'post' ) );

		add_meta_box(
			'_generate_use_sections_metabox',
			__( 'Sections', 'gp-premium' ),
			'generate_sections_use_sections_metabox',
			$post_types,
			'side',
			'high'
		);

		add_meta_box(
			'_generate_sections_metabox',
			__( 'Sections', 'gp-premium' ),
			'generate_sections_sections_metabox',
			$post_types,
			'normal',
			'high'
		);

	}
}

if ( ! function_exists( 'generate_sections_sanitize_function' ) ) {
	/*
	 * Sanitize our settings
	*/
	function generate_sections_sanitize_function( $data, $post_id ) {

		$section = array();

		if ( isset( $data['title'] ) ) {
			$section['title'] = sanitize_text_field( $data['title'] );
		}

		if ( isset( $data['content'] ) ) {
			$section['content'] = sanitize_post_field( 'post_content', $data['content'], $post_id, 'db' );
		}

		if ( isset( $data['custom_classes'] ) ) {
			$section['custom_classes'] = sanitize_text_field( $data['custom_classes'] );
		}

		if ( isset( $data['custom_id'] ) ) {
			$section['custom_id'] = sanitize_text_field( $data['custom_id'] );
		}

		if ( isset( $data['top_padding'] ) ) {
			$section['top_padding'] = '' == $data['top_padding'] ? $data['top_padding'] : absint( $data['top_padding'] );
		}

		if ( isset( $data['bottom_padding'] ) ) {
			$section['bottom_padding'] = '' == $data['bottom_padding'] ? $data['bottom_padding'] : absint( $data['bottom_padding'] );
		}

		if ( isset( $data['top_padding_unit'] ) ) {
			$section['top_padding_unit'] = $data['top_padding_unit'] == '%' ? '%' : '';
		}

		if ( isset( $data['bottom_padding_unit'] ) ) {
			$section['bottom_padding_unit'] = $data['bottom_padding_unit'] == '%' ? '%' : '';
		}

		if ( isset( $data['background_image'] ) ) {
			$section['background_image'] = absint( $data['background_image'] );
		}

		if ( isset( $data['background_color'] ) ) {
			$section['background_color'] = generate_sections_sanitize_rgba( $data['background_color'] );
		}

		if ( isset( $data['text_color'] ) ) {
			$section['text_color'] = generate_sections_sanitize_hex_color( $data['text_color'] );
		}

		if ( isset( $data['link_color'] ) ) {
			$section['link_color'] = generate_sections_sanitize_hex_color( $data['link_color'] );
		}

		if ( isset( $data['link_color_hover'] ) ) {
			$section['link_color_hover'] = generate_sections_sanitize_hex_color( $data['link_color_hover'] );
		}

		if ( isset( $data['box_type'] ) ) {
			$section['box_type'] = $data['box_type'] == 'contained' ? 'contained' : '';
		}

		if ( isset( $data['inner_box_type'] ) ) {
			$section['inner_box_type'] = $data['inner_box_type'] == 'fluid' ? 'fluid' : '';
		}

		if ( isset( $data['parallax_effect'] ) ) {
			$section['parallax_effect'] = $data['parallax_effect'] == 'enable' ? 'enable' : '';
		}

		if ( isset( $data['background_color_overlay'] ) ) {
			$section['background_color_overlay'] = $data['background_color_overlay'] == 'enable' ? 'enable' : '';
		}

		return $section;

	}
}

if ( ! function_exists( 'generate_sections_metabox_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'generate_sections_metabox_scripts', 20 );
	/*
	 * Enqueue styles and scripts specific to metaboxs
	 */
	function generate_sections_metabox_scripts( $hook ) {
		// I prefer to enqueue the styles only on pages that are using the metaboxes
		if ( in_array( $hook, array( "post.php", "post-new.php" ) ) ) {

			$post_types = apply_filters( 'generate_sections_post_types', array( 'page', 'post' ) );

			$screen = get_current_screen();
			$post_type = $screen->id;

			if ( in_array( $post_type, (array) $post_types ) ) {
				wp_enqueue_style( 'generate-sections-metabox', plugin_dir_url( __FILE__ ) . 'css/generate-sections-metabox.css', false, GENERATE_SECTIONS_VERSION );
				wp_enqueue_style( 'generate-lc-switch', plugin_dir_url( __FILE__ ) . 'css/lc_switch.css', false, GENERATE_SECTIONS_VERSION );

				//make sure we enqueue some scripts just in case ( only needed for repeating metaboxes )
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'editor' );
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-alpha', GP_LIBRARY_DIRECTORY_URL . 'alpha-color-picker/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '3.0.0', true );

				wp_add_inline_script(
					'wp-color-picker-alpha',
					'jQuery( function() { jQuery( ".color-picker" ).wpColorPicker(); } );'
				);

				wp_enqueue_media();

				if ( function_exists( 'wp_enqueue_editor' ) ) {
					wp_enqueue_editor();

					wp_add_inline_script(
						'editor',
						'window.wp.sectionsEditor = window.wp.editor;',
						'after'
					);
				}

				if ( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
					wp_enqueue_script( 'generate-sections-metabox', plugin_dir_url( __FILE__ ) . 'js/generate-sections-metabox-4.9.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable', 'editor', 'media-upload', 'wp-color-picker' ), GENERATE_SECTIONS_VERSION, true );
				} else {
					wp_enqueue_script( 'generate-sections-metabox', plugin_dir_url( __FILE__ ) . 'js/generate-sections-metabox.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable', 'editor', 'media-upload', 'wp-color-picker' ), GENERATE_SECTIONS_VERSION, true );
				}

				if ( function_exists( 'wp_add_inline_script' ) ) {
					if ( function_exists( 'generate_get_default_color_palettes' ) ) {
						// Grab our palette array and turn it into JS
						$palettes = json_encode( generate_get_default_color_palettes() );

						// Add our custom palettes
						// json_encode takes care of escaping
						wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'generate_sections_admin_footer_scripts' ) ) {
	add_action( 'admin_footer', 'generate_sections_admin_footer_scripts' );

	function generate_sections_admin_footer_scripts() {
		// We don't need this if wp_add_inline_script exists
		if ( function_exists( 'wp_add_inline_script' ) ) {
			return;
		}
		?>
		<script>
			if ( typeof lc_switch !== 'undefined' ) {
				jQuery(document).ready(function($) { $(".use-sections-switch").lc_switch("","");});
			}
		</script>
		<?php
	}
}


if ( ! function_exists( 'generate_sections_use_sections_metabox' ) ) {
	function generate_sections_use_sections_metabox() {
		include_once( plugin_dir_path( __FILE__ ) . 'views/use-sections.php' );
	}
}

if ( ! function_exists( 'generate_sections_sections_metabox' ) ) {
	function generate_sections_sections_metabox() {
		global $post;

		$meta = get_post_meta( $post->ID, '_generate_sections', true );

		$sections = isset( $meta['sections'] ) && is_array( $meta['sections' ] ) ? $meta['sections'] :  array();

		$translation_array = array(
			'confirm' => __( 'This action can not be undone, are you sure?', 'gp-premium' ),
	    	'post_id' => $post->ID,
	    	'sections' => $sections,
	    	'default_title' => __( 'Section', 'gp-premium' ),
			'default_content_title' => __( 'Content', 'gp-premium' ),
	    	'tabs'	=> array(
				array( 'title' => __( 'Settings', 'gp-premium' ), 'target' => 'style', 'active' => 'false' ),
				array( 'title' => __( 'Content', 'gp-premium' ), 'target' => 'content', 'active' => 'true' ),
	    		//array( 'title' => __( 'Layout', 'gp-premium' ), 'target' => 'layout', 'active' => 'false' ),
	    	),
	    	'top_padding' => apply_filters( 'generate_sections_default_padding_top','40' ),
	    	'bottom_padding' => apply_filters( 'generate_sections_default_padding_bottom','40' ),
	    	'media_library_title' => __('Section Background', 'gp-premium' ),
	    	'media_library_button' => __( 'Set as Section Background', 'gp-premium' ),
	    	'generate_nonce' => wp_create_nonce( 'generate_sections_nonce' ),
	    	'default_editor'	=> user_can_richedit() && wp_default_editor() == 'tinymce' ? 'tmce-active' : 'html-active',
	    	'user_can_richedit' => user_can_richedit(),
	    	'insert_into_section'	=> __( 'Insert into Section', 'gp-premium' ),
	    	'edit_section'	=> __( 'Edit Section', 'gp-premium' )

	    );
	    wp_localize_script( 'generate-sections-metabox', 'generate_sections_metabox_i18n', $translation_array );

		include_once( plugin_dir_path( __FILE__ ) . 'views/sections.php' );
		include_once( plugin_dir_path( __FILE__ ) . 'views/sections-template.php' );
		add_action( 'print_media_templates', 'generate_sections_print_templates' );

		do_action( 'generate_sections_metabox' );
	}
}

if ( ! function_exists( 'generate_sections_save_use_metabox' ) ) {
	add_action( 'save_post', 'generate_sections_save_use_metabox' );
	/*
	 * Save the "use" metabox
	 */
	function generate_sections_save_use_metabox( $post_id ) {
	    if ( ! isset( $_POST['_generate_sections_use_sections_nonce'] ) || ! wp_verify_nonce( $_POST['_generate_sections_use_sections_nonce'], 'generate_sections_use_sections_nonce' ) ) {
	        return $post_id;
	    }

	    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	        return $post_id;
	    }

	    if ( ! current_user_can('edit_post', $post_id ) ) {
	        return $post_id;
	    }

	    if  ( isset ( $_POST['_generate_use_sections'] ) && isset ( $_POST['_generate_use_sections']['use_sections'] ) && $_POST['_generate_use_sections']['use_sections'] == 'true' ) {
			update_post_meta( $post_id, '_generate_use_sections', array( 'use_sections' => 'true' ) );
	    } else {
	        delete_post_meta( $post_id, '_generate_use_sections' );
	    }
	}
}

if ( ! function_exists( 'generate_sections_save_sections_metabox' ) ) {
	add_action( 'save_post', 'generate_sections_save_sections_metabox', 20 );
	/*
	 * Save the sections metabox
	 */
	function generate_sections_save_sections_metabox( $post_id ) {

	    if ( ! isset( $_POST['_generate_sections_nonce'] ) || ! wp_verify_nonce( $_POST['_generate_sections_nonce'], 'generate_sections_nonce' ) ) {
			return $post_id;
	    }

	    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	        return $post_id;
	    }

	    if ( ! current_user_can('edit_post', $post_id ) ) {
	        return $post_id;
	    }

	    $clean = array();

	    if  ( isset ( $_POST['_generate_sections'] ) && isset( $_POST['_generate_sections']['sections'] ) && is_array( $_POST['_generate_sections']['sections'] ) ) {

			foreach( $_POST['_generate_sections']['sections'] as $section ) {

				$section = json_decode( stripslashes( trim($section) ), true);

				$section = generate_sections_sanitize_function( $section, $post_id );
				if ( ! empty( $section ) ){
					$clean[] = $section;
				}

			}

	    }

	    // save data
	    if ( ! empty( $clean ) ) {
	    	// this maintains data structure of previous version
	    	$meta = array( 'sections' => $clean );
	        update_post_meta( $post_id, '_generate_sections', $meta );
	    } else {
	        delete_post_meta( $post_id, '_generate_sections' );
	    }

	}
}

if ( ! function_exists( 'generate_sections_sanitize_hex_color' ) ) {
	/*
	 * Sanitize colors
	 * We don't use the built in function so we can use empty values
	 */
	function generate_sections_sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return null;
	}
}

if ( ! function_exists( 'generate_sections_sanitize_rgba' ) ) {
	/**
	 * Sanitize RGBA colors
	 * @since 1.3.42
	 */
	function generate_sections_sanitize_rgba( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// If string does not start with 'rgba', then treat as hex
		// sanitize the hex color and finally convert hex to rgba
		if ( false === strpos( $color, 'rgba' ) ) {
			return generate_sections_sanitize_hex_color( $color );
		}

		// By now we know the string is formatted as an rgba color so we need to further sanitize it.
		$color = str_replace( ' ', '', $color );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba('.$red.','.$green.','.$blue.','.$alpha.')';

		return '';
	}
}
