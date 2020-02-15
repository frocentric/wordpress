<?php
add_action( 'butterbean_register', 'wpsp_cards_register', 30, 2 );
/**
 * Register our card controls.
 *
 * @since 1.0
 */
function wpsp_cards_register( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_section(
        'wpsp_cards',
        array(
            'label' => esc_html__( 'Cards', 'wp-show-posts-pro' ),
            'icon'  => 'dashicons-admin-page'
        )
    );

	$manager->register_control(
        'wpsp_cards', // Same as setting name.
        array(
            'type'    => 'select',
            'section' => 'wpsp_cards',
            'label'   => esc_html__( 'Cards', 'wp-show-posts-pro' ),
			'description' => sprintf( '- <a href="https://demos.wpshowposts.com/cards" target="_blank" rel="noopener">%s</a>', esc_html__( 'View Examples', 'wp-show-posts-pro' ) ),
            'choices' => array(
				'none' => esc_html__( 'None', 'wp-show-posts-pro' ),
				'base' => esc_html__( 'Base', 'wp-show-posts-pro' ),
				'wpsp-overlap' => esc_html__( 'Overlap', 'wp-show-posts-pro' ),
				'wpsp-row' => esc_html__( 'Row', 'wp-show-posts-pro' ),
				'wpsp-polaroid' => esc_html__( 'Polaroid', 'wp-show-posts-pro' ),
				'wpsp-overlay' => esc_html__( 'Overlay', 'wp-show-posts-pro' ),
				'wpsp-overlay-style-one' => esc_html__( 'Overlay - Style 1', 'wp-show-posts-pro' ),
				'wpsp-overlay-style-two' => esc_html__( 'Overlay - Style 2', 'wp-show-posts-pro' )
			),
			'attr' => array( 'id' => 'wpsp-cards' )
        )
    );

	$manager->register_setting(
        'wpsp_cards', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_cards' ] ? $defaults[ 'wpsp_cards' ] : 'none'
        )
    );
}

add_filter( 'wpsp_settings', 'wpsp_card_settings' );
function wpsp_card_settings( $settings ) {
	if ( ! function_exists( 'wpsp_get_setting' ) ) {
		return $classes;
	}

	$settings['card'] = wpsp_get_setting( $settings['list_id'], 'wpsp_cards' );

	if ( 'none' !== $settings['card'] ) {
		$settings['wrapper_class'][] = 'wpsp-card';
		$settings['image_location'] = 'above-title';

		if ( 'wpsp-overlap' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-overlap';
		}

		if ( 'wpsp-row' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-row';
		}

		if ( 'wpsp-polaroid' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-row';
			$settings['wrapper_class'][] = 'wpsp-polaroid';
		}

		if ( 'wpsp-overlay' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-overlay';
		}

		if ( 'wpsp-overlay-style-one' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-overlay';
			$settings['wrapper_class'][] = 'wpsp-ov-style-one';
		}

		if ( 'wpsp-overlay-style-two' === $settings['card'] ) {
			$settings['wrapper_class'][] = 'wpsp-overlay';
			$settings['wrapper_class'][] = 'wpsp-ov-style-two';
		}
	}

	return $settings;
}

add_action( 'wpsp_before_header', 'wpsp_content_wrap_open', 15 );
function wpsp_content_wrap_open( $settings ) {
	if ( ! function_exists( 'wpsp_get_setting' ) ) {
		return $classes;
	}

	$settings['card'] = wpsp_get_setting( $settings['list_id'], 'wpsp_cards' );

	if ( 'none' !== $settings['card'] ) {
		echo '<div class="wpsp-content-wrap">';
	}
}

add_action( 'wpsp_after_content', 'wpsp_content_wrap_close', 15 );
function wpsp_content_wrap_close( $settings ) {
	if ( ! function_exists( 'wpsp_get_setting' ) ) {
		return $classes;
	}

	$settings['card'] = wpsp_get_setting( $settings['list_id'], 'wpsp_cards' );

	if ( 'none' !== $settings['card'] ) {
		echo '</div><! -- .wpsp-content-wrap -->';
	}
}
