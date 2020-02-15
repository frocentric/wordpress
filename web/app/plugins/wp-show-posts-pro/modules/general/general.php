<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'butterbean_register', 'wpsp_pro_general_options', 25, 2 );
/**
 * Register our general options.
 *
 * @since 1.0
 */
function wpsp_pro_general_options( $butterbean, $post_type ) {
	if ( ! function_exists( 'wpsp_get_defaults' ) ) {
		return;
	}

	$defaults = wpsp_get_defaults();

	$manager = $butterbean->get_manager( 'wp_show_posts' );

	$manager->register_control(
        'wpsp_section_classes', // Same as setting name.
        array(
            'type'    => 'text',
            'section' => 'wpsp_query_args',
            'label'   => esc_html__( 'Section HTML Classes', 'wp-show-posts-pro' ),
			'priority' => 1,
			'description' => esc_html__( 'Separated by spaces', 'wp-show-posts-pro' ),
        )
    );

	$manager->register_setting(
        'wpsp_section_classes', // Same as control name.
        array(
            'sanitize_callback' => 'sanitize_text_field',
			'default' => $defaults[ 'wpsp_section_classes' ] ? $defaults[ 'wpsp_section_classes' ] : ''
        )
    );
}

add_filter( 'wpsp_settings', 'wpsp_set_section_classes' );
function wpsp_set_section_classes( $settings ) {
	if ( ! function_exists( 'wpsp_get_setting' ) ) {
		return $classes;
	}

	$settings['section_classes'] = wpsp_get_setting( $settings['list_id'], 'wpsp_section_classes' );

	if ( $settings['section_classes'] ) {
		$settings['wrapper_class'][] = trim( $settings['section_classes'] );
	}

	$settings['image_overlay_color_static'] = wpsp_get_setting( $settings['list_id'], 'wpsp_image_overlay_color_static' );

	if ( $settings['image_overlay_color_static'] ) {
		$settings['wrapper_class'][] = 'static-image-overlay';
	}

	return $settings;
}
