<?php
if ( ! function_exists( 'wpsp_image_lightbox_register' ) ) {
	add_action( 'butterbean_register', 'wpsp_image_lightbox_register', 40, 2 );
	/**
	 * Register our image gallery controls.
	 */
	function wpsp_image_lightbox_register( $butterbean, $post_type ) {
		if ( ! function_exists( 'wpsp_get_defaults' ) ) {
			return;
		}

		$defaults = wpsp_get_defaults();

		$manager = $butterbean->get_manager( 'wp_show_posts' );

		$manager->register_control(
			'wpsp_image_lightbox',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_images',
				'label'       => __( 'Image lightbox','wp-show-posts-pro' ),
				'attr' => array( 'id' => 'wpsp-image-lightbox' )
			)
		);

		$manager->register_setting(
			'wpsp_image_lightbox',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_image_lightbox' ] ? $defaults[ 'wpsp_image_lightbox' ] : false
			)
		);

		$manager->register_control(
			'wpsp_image_gallery',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_images',
				'label'       => __( 'Image lightbox gallery','wp-show-posts-pro' ),
				'attr' => array( 'id' => 'wpsp-image-lightbox-gallery' )
			)
		);

		$manager->register_setting(
			'wpsp_image_gallery',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_image_gallery' ] ? $defaults[ 'wpsp_image_gallery' ] : false
			)
		);
	}
}

add_filter( 'wpsp_settings', 'wpsp_pro_gallery_settings' );
/**
 * Add our image gallery settings.
 *
 * @since 0.1
 */
function wpsp_pro_gallery_settings( $settings ) {
	$settings[ 'image_lightbox' ] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_lightbox' );
	$settings[ 'image_gallery' ] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_gallery' );

	return $settings;
}

if ( ! function_exists( 'wpsp_image_lightbox' ) ) {
	add_filter( 'wpsp_image_href', 'wpsp_image_lightbox', 10, 2 );
	/**
	 * Add the image URL to our image HTML href.
	 *
	 * @since 0.1
	 */
	function wpsp_image_lightbox( $value, $settings ) {
		if ( $settings[ 'image_lightbox' ] && function_exists( 'get_the_post_thumbnail_url' ) ) {
			return esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) );
		}

		return esc_url( get_the_permalink() );
	}
}

if ( ! function_exists( 'wpsp_image_lightbox_data' ) ) {
	add_filter( 'wpsp_image_data', 'wpsp_image_lightbox_data', 10, 2 );
	/**
	 * Add our data attribute to the image.
	 *
	 * @since 0.1
	 */
	function wpsp_image_lightbox_data( $value, $settings ) {
		if ( $settings[ 'image_lightbox' ] && ! $settings[ 'image_gallery' ] ) {
			return 'data-featherlight="image"';
		}

		return '';
	}
}

if ( ! function_exists( 'wpsp_lightbox_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'wpsp_lightbox_scripts' );
	/**
	 * Register our lightbox scripts and styles.
	 *
	 * @since 0.1
	 */
	function wpsp_lightbox_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
		wp_register_script( 'wpsp-featherlight', trailingslashit( plugin_dir_url( __FILE__ ) ) . "featherlight/featherlight{$suffix}.js", array( 'jquery' ) );
		wp_register_script( 'wpsp-featherlight-gallery', trailingslashit( plugin_dir_url( __FILE__ ) ) . "featherlight/featherlight.gallery{$suffix}.js", array( 'wpsp-featherlight' ) );
		wp_register_style( 'wpsp-featherlight', trailingslashit( plugin_dir_url( __FILE__ ) ) . "featherlight/featherlight{$suffix}.css", array() );
		wp_register_style( 'wpsp-featherlight-gallery', trailingslashit( plugin_dir_url( __FILE__ ) ) . "featherlight/featherlight.gallery{$suffix}.css", array( 'wpsp-featherlight' ) );
	}
}

add_action( 'wpsp_after_wrapper', 'wpsp_pro_lightbox_enqueue' );
/**
 * Enqueue our lightbox scripts and styles.
 *
 * @since 0.1
 */
function wpsp_pro_lightbox_enqueue( $settings ) {
	if ( $settings[ 'image_lightbox' ] ) {
		wp_enqueue_script( 'wpsp-featherlight' );
		wp_enqueue_style( 'wpsp-featherlight' );

		$args = apply_filters( 'wpsp_featherlight_gallery_args', array(
			'previousIcon' => '',
			'nextIcon' => '',
		) );

		$args = json_encode( $args );

		if ( $settings[ 'image_gallery' ] ) {
			wp_enqueue_script( 'wpsp-featherlight-gallery' );
			wp_add_inline_script( 'wpsp-featherlight-gallery',
			"jQuery( document ).ready( function( $ ) {
				$('#wpsp-{$settings[ 'list_id' ]} .wp-show-posts-image a').featherlightGallery({$args});
				$('body').on('wpsp_items_loaded',function(){
					$('#wpsp-{$settings[ 'list_id' ]} .wp-show-posts-image a').featherlightGallery({$args});
				});
			} );" );
			wp_enqueue_style( 'wpsp-featherlight-gallery' );
		}
	}
}
