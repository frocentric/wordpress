<?php
add_action( 'butterbean_register', 'wpsp_styling_register', 25, 2 );
function wpsp_styling_register( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_control(
        'wpsp_image_overlay_color_static', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_images',
            'label'   => esc_html__( 'Image overlay color', 'wp-show-posts-pro' ),
			'attr' => array(
				'id' => 'wpsp-image-overlay-color-static',
				'data-alpha' => true,
				'maxlength' => '',
			)
        )
    );

	$manager->register_setting(
        'wpsp_image_overlay_color_static', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_rgba_color',
			'default' => $defaults[ 'wpsp_image_overlay_color_static' ] ? $defaults[ 'wpsp_image_overlay_color_static' ] : false
        )
    );

	$manager->register_control(
        'wpsp_image_overlay_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_images',
            'label'   => esc_html__( 'Image overlay hover color', 'wp-show-posts-pro' ),
			'attr' => array(
				'id' => 'wpsp-image-overlay-color',
				'data-alpha' => true,
			)
        )
    );

	$manager->register_setting(
        'wpsp_image_overlay_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_rgba_color',
			'default' => $defaults[ 'wpsp_image_overlay_color' ] ? $defaults[ 'wpsp_image_overlay_color' ] : false
        )
    );

	$manager->register_control(
        'wpsp_image_overlay_icon', // Same as setting name.
        array(
            'type'    => 'select',
            'section' => 'wpsp_images',
            'label'   => esc_html__( 'Image overlay icon', 'wp-show-posts-pro' ),
            'choices' => array(
				'' => '',
				'plus' => __( 'Plus','wp-show-posts-pro' ),
				'eye' => __( 'Eye','wp-show-posts-pro' ),
				'play' => __( 'Play','wp-show-posts-pro' ),
				'heart' => __( 'Heart','wp-show-posts-pro' ),
				'download' => __( 'Download','wp-show-posts-pro' ),
				'cloud-download' => __( 'Cloud download','wp-show-posts-pro' )
			),
			'attr' => array( 'id' => 'wpsp-overlay-content' )
        )
    );

	$manager->register_setting(
        'wpsp_image_overlay_icon', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_image_overlay_icon' ] ? $defaults[ 'wpsp_image_overlay_icon' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_image_hover_effect', // Same as setting name.
        array(
            'type'    => 'select',
            'section' => 'wpsp_images',
            'label'   => esc_html__( 'Image hover effect', 'wp-show-posts-pro' ),
            'choices' => array(
				'' => '',
				'zoom' => __( 'Zoom','wp-show-posts-pro' ),
				'blur' => __( 'Blur','wp-show-posts-pro' ),
				'grayscale' => __( 'Grayscale','wp-show-posts-pro' ),
			),
			'attr' => array( 'id' => 'wpsp-image-effect' )
        )
    );

	$manager->register_setting(
        'wpsp_image_hover_effect', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_image_hover_effect' ] ? $defaults[ 'wpsp_image_hover_effect' ] : ''
        )
    );

	$manager->register_section(
        'wpsp_styling',
        array(
            'label' => esc_html__( 'Styling', 'wp-show-posts-pro' ),
            'icon'  => 'dashicons-admin-customizer'
        )
    );

	$manager->register_control(
        'wpsp_background', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_columns',
            'label'   => esc_html__( 'Background color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-background' )
        )
    );

	$manager->register_setting(
        'wpsp_background', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_background' ] ? $defaults[ 'wpsp_background' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_background_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_columns',
            'label'   => esc_html__( 'Background color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-background-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_background_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_background_hover' ] ? $defaults[ 'wpsp_background_hover' ] : ''
        )
    );

	// Title Font Size
	$manager->register_control(
        'wpsp_title_font_size', // Same as setting name.
        array(
            'type'    => 'text',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Title font size (add unit: px, em etc..)', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-title-font-size' )
        )
    );

	$manager->register_setting(
        'wpsp_title_font_size', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_title_font_size' ] ? $defaults[ 'wpsp_title_font_size' ] : ''
        )
    );

	// Title color
	$manager->register_control(
        'wpsp_title_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Title color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-title-color' )
        )
    );

	$manager->register_setting(
        'wpsp_title_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_title_color' ] ? $defaults[ 'wpsp_title_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_title_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Title color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-title-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_title_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_title_color_hover' ] ? $defaults[ 'wpsp_title_color_hover' ] : ''
        )
    );

	// Meta color
	$manager->register_control(
        'wpsp_meta_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_post_meta',
            'label'   => esc_html__( 'Meta color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-meta-color' )
        )
    );

	$manager->register_setting(
        'wpsp_meta_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_meta_color' ] ? $defaults[ 'wpsp_meta_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_meta_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_post_meta',
            'label'   => esc_html__( 'Meta color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-meta-color' )
        )
    );

	$manager->register_setting(
        'wpsp_meta_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_meta_color_hover' ] ? $defaults[ 'wpsp_meta_color_hover' ] : ''
        )
    );

	// Text color
	$manager->register_control(
        'wpsp_text', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Text color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-text' )
        )
    );

	$manager->register_setting(
        'wpsp_text', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_text' ] ? $defaults[ 'wpsp_text' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_link', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Link color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-link' )
        )
    );

	$manager->register_setting(
        'wpsp_link', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_link' ] ? $defaults[ 'wpsp_link' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_link_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Link color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-link-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_link_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_link_hover' ] ? $defaults[ 'wpsp_link_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_border', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_columns',
            'label'   => esc_html__( 'Border color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-border' ),
        )
    );

	$manager->register_setting(
        'wpsp_border', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_border' ] ? $defaults[ 'wpsp_border' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_border_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_columns',
            'label'   => esc_html__( 'Border color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-border-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_border_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_border_hover' ] ? $defaults[ 'wpsp_border_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_padding', // Same as setting name.
        array(
            'type'    => 'text',
            'section' => 'wpsp_columns',
            'label'   => esc_html__( 'Padding', 'wp-show-posts-pro' ),
			'description' => esc_html__( 'Add the unit: px, em etc..', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-padding' )
        )
    );

	$manager->register_setting(
        'wpsp_padding', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_padding' ] ? $defaults[ 'wpsp_padding' ] : ''
        )
    );

	// Read more button controls
	$manager->register_control(
        'wpsp_read_more_background_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more background', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-background' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_background_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_background_color' ] ? $defaults[ 'wpsp_read_more_background_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_read_more_background_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more background hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-background-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_background_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_background_color_hover' ] ? $defaults[ 'wpsp_read_more_background_color_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_read_more_text_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more text', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-text-color' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_text_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_text_color' ] ? $defaults[ 'wpsp_read_more_text_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_read_more_text_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more text hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-text-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_text_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_text_color_hover' ] ? $defaults[ 'wpsp_read_more_text_color_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_read_more_border_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more border', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-border-color' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_border_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_border_color' ] ? $defaults[ 'wpsp_read_more_border_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_read_more_border_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_content',
            'label'   => esc_html__( 'Read more border hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-read-more-border-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_read_more_border_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_read_more_border_color_hover' ] ? $defaults[ 'wpsp_read_more_border_color_hover' ] : ''
        )
    );

	// Social icon colors
	$manager->register_control(
        'wpsp_twitter_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Twitter color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-twitter-color' )
        )
    );

	$manager->register_setting(
        'wpsp_twitter_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_twitter_color' ] ? $defaults[ 'wpsp_twitter_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_twitter_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Twitter color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-twitter-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_twitter_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_twitter_color_hover' ] ? $defaults[ 'wpsp_twitter_color_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_facebook_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Facebook color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-facebook-color' )
        )
    );

	$manager->register_setting(
        'wpsp_facebook_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_facebook_color' ] ? $defaults[ 'wpsp_facebook_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_facebook_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Facebook color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-facebook-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_facebook_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_facebook_color_hover' ] ? $defaults[ 'wpsp_facebook_color_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_pinterest_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Pinterest color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-pinterest-color' )
        )
    );

	$manager->register_setting(
        'wpsp_pinterest_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_pinterest_color' ] ? $defaults[ 'wpsp_pinterest_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_pinterest_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Pinterest color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-pinterest-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_pinterest_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_pinterest_color_hover' ] ? $defaults[ 'wpsp_pinterest_color_hover' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_love_color', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Love color', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-love-color' )
        )
    );

	$manager->register_setting(
        'wpsp_love_color', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_love_color' ] ? $defaults[ 'wpsp_love_color' ] : ''
        )
    );

	$manager->register_control(
        'wpsp_love_color_hover', // Same as setting name.
        array(
            'type'    => 'color',
            'section' => 'wpsp_social',
            'label'   => esc_html__( 'Love color hover', 'wp-show-posts-pro' ),
			'attr' => array( 'id' => 'wpsp-love-color-hover' )
        )
    );

	$manager->register_setting(
        'wpsp_love_color_hover', // Same as control name.
        array(
            'sanitize_callback' => 'wpsp_pro_sanitize_hex_color',
			'default' => $defaults[ 'wpsp_love_color_hover' ] ? $defaults[ 'wpsp_love_color_hover' ] : ''
        )
    );
}

if ( ! function_exists( 'wpsp_pro_sanitize_hex_color' ) ) {
	function wpsp_pro_sanitize_hex_color( $color ) {
		if ( '' === $color ) {
	        return '';
		}

	    // 3 or 6 hex digits, or the empty string.
	    if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
	        return $color;
		}
	}
}

function wpsp_pro_sanitize_rgba_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	if ( false === strpos( $color, 'rgba' ) ) {
		return wpsp_pro_sanitize_hex_color( $color );
	}

	$color = str_replace( ' ', '', $color );
	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

	return 'rgba('.$red.','.$green.','.$blue.','.$alpha.')';
}

add_filter( 'wpsp_settings', 'wpsp_pro_styling_settings', 10 );
function wpsp_pro_styling_settings( $settings ) {
	$settings[ 'background' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_background' );
	$settings[ 'background_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_background_hover' );
	$settings[ 'title_font_size' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_title_font_size' );
	$settings[ 'title_color' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_title_color' );
	$settings[ 'title_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_title_color_hover' );
	$settings[ 'meta_color' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_meta_color' );
	$settings[ 'meta_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'meta_color_hover' );
	$settings[ 'text_color' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_text' );
	$settings[ 'link_color' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_link' );
	$settings[ 'link_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_link_hover' );
	$settings[ 'border_color' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_border' );
	$settings[ 'border_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_border_hover' );
	$settings[ 'padding' ] 							= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_padding' );
	$settings[ 'read_more_background_color' ] 		= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_background_color' );
	$settings[ 'read_more_background_color_hover' ] = wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_background_color_hover' );

	$settings[ 'image_overlay_color_static' ] 		= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_overlay_color_static' );
	$settings[ 'image_overlay_color' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_overlay_color' );

	if ( $settings['image_overlay_color_static'] && ! $settings['image_overlay_color'] ) {
		$settings['image_overlay_color'] = $settings['image_overlay_color_static'];
	}

	$settings[ 'image_overlay_icon' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_overlay_icon' );
	$settings[ 'image_hover_effect' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_image_hover_effect' );

	$settings[ 'read_more_text_color' ] 			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_text_color' );
	$settings[ 'read_more_text_color_hover' ] 		= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_text_color_hover' );
	$settings[ 'read_more_border_color' ] 			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_border_color' );
	$settings[ 'read_more_border_color_hover' ] 	= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_read_more_border_color_hover' );

	$settings[ 'twitter_color' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_twitter_color' );
	$settings[ 'twitter_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_twitter_color_hover' );
	$settings[ 'facebook_color' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_facebook_color' );
	$settings[ 'facebook_color_hover' ] 			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_facebook_color_hover' );
	$settings[ 'pinterest_color' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_pinterest_color' );
	$settings[ 'pinterest_color_hover' ] 			= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_pinterest_color_hover' );
	$settings[ 'love_color' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_love_color' );
	$settings[ 'love_color_hover' ] 				= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_love_color_hover' );

	$settings[ 'social_sharing' ] 					= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_social_sharing' );
	$settings[ 'twitter' ] 							= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_twitter' );
	$settings[ 'facebook' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_facebook' );
	$settings[ 'pinterest' ] 						= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_pinterest' );
	$settings[ 'love' ] 							= wpsp_get_setting( $settings[ 'list_id' ], 'wpsp_love' );

	return $settings;
}
