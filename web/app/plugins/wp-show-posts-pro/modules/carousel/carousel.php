<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'butterbean_register', 'wpsp_pro_carousel_register', 25, 2 );
function wpsp_pro_carousel_register( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_section(
        'wpsp_carousel',
        array(
            'label' => esc_html__( 'Carousel', 'wp-show-posts-pro' ),
            'icon'  => 'dashicons-images-alt2'
        )
    );

	$manager->register_control(
		'wpsp_carousel',
		array(
			'type'        => 'checkbox',
			'section'     => 'wpsp_carousel',
			'label'       => __( 'Carousel', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-carousel' )
		)
	);

	$manager->register_setting(
		'wpsp_carousel',
		array(
			'sanitize_callback' => 'butterbean_validate_boolean',
			'default' => $defaults[ 'wpsp_carousel' ] ? $defaults[ 'wpsp_carousel' ] : false
		)
	);

	$manager->register_control(
		'wpsp_carousel_slides',
		array(
			'type'        => 'number',
			'section'     => 'wpsp_carousel',
			'label'       => __( 'Posts to show', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-carousel-posts-to-show' )
		)
	);

	$manager->register_setting(
		'wpsp_carousel_slides',
		array(
			'sanitize_callback' => 'absint',
			'default' => $defaults[ 'wpsp_carousel_slides' ] ? $defaults[ 'wpsp_carousel_slides' ] : false
		)
	);

	$manager->register_control(
		'wpsp_carousel_slides_to_scroll',
		array(
			'type'        => 'number',
			'section'     => 'wpsp_carousel',
			'label'       => __( 'Posts to scroll', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-carousel-posts-to-scroll' )
		)
	);

	$manager->register_setting(
		'wpsp_carousel_slides_to_scroll',
		array(
			'sanitize_callback' => 'absint',
			'default' => $defaults[ 'wpsp_carousel_slides_to_scroll' ] ? $defaults[ 'wpsp_carousel_slides_to_scroll' ] : false
		)
	);
}

add_action( 'wp_enqueue_scripts', 'wpsp_pro_carousel_register_scripts' );
/**
 * Register carousel scripts.
 *
 * @since 1.0
 */
function wpsp_pro_carousel_register_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
	wp_register_script( 'wpsp-slick-carousel', trailingslashit( plugin_dir_url( __FILE__ ) ) . "slick/slick{$suffix}.js", array( 'jquery' ), WPSP_PRO_VERSION, true );
	wp_register_style( 'wpsp-slick-carousel', trailingslashit( plugin_dir_url( __FILE__ ) ) . "slick/slick{$suffix}.css", array(), WPSP_PRO_VERSION );
}

add_action( 'wpsp_after_wrapper', 'wpsp_pro_enqueue_carousel' );
/**
 * Enqueue carousel scripts.
 *
 * @since 1.0
 */
function wpsp_pro_enqueue_carousel( $settings ) {
	if ( $settings[ 'carousel' ] ) {
		wp_enqueue_script( 'wpsp-slick-carousel' );
		wp_enqueue_style( 'wpsp-slick-carousel' );

		$args = apply_filters( 'wpsp_carousel_args', array(
			'infinite' => true,
			'slidesToShow' => absint( $settings[ 'carousel_slides' ] ),
			'slidesToScroll' => absint( $settings[ 'carousel_slides_to_scroll' ] ),
			'dots' => true,
			'arrows' => false,
			'lazyLoad' => 'ondemand',
			'responsive' => array(
				array(
					'breakpoint' => 1024,
					'settings' => array(
						'slidesToShow' => 2,
						'slidesToScroll' => 2,
					),
				),
				array(
					'breakpoint' => 768,
					'settings' => array(
						'slidesToShow' => 1,
						'slidesToScroll' => 1,
					),
				)
			),
		) );

		$args = json_encode( $args );

		if ( $settings[ 'carousel_slides' ] > 0 && $settings[ 'carousel_slides_to_scroll' ] > 0 ) {
			$list_id = absint( $settings[ 'list_id' ] );
			wp_add_inline_script( 'wpsp-slick-carousel', "jQuery('.wp-show-posts#wpsp-{$list_id}').slick({$args});" );
		}
	}
}

add_filter( 'wpsp_settings', 'wpsp_pro_carousel_settings' );
/**
 * Add our carousel settings.
 *
 * @since 1.0
 */
function wpsp_pro_carousel_settings( $settings ) {
	$settings['carousel'] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_carousel' );
	$settings['carousel_slides'] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_carousel_slides' );
	$settings['carousel_slides_to_scroll'] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_carousel_slides_to_scroll' );

	if ( $settings['carousel'] ) {
		$settings['columns'] = 'col-12';
		$settings['wrapper_class'][] = ' wpsp-carousel';
	}

	return $settings;
}
