<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'generate_slideout_typography_customizer', 150 );
/**
 * Adds our WooCommerce color options
 */
function generate_slideout_typography_customizer( $wp_customize ) {
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) ) {
		return;
	}

	// Get our custom controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Get our defaults
	$defaults = generate_get_default_fonts();

	// Register our custom control types
	if ( method_exists( $wp_customize,'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Pro_Typography_Customize_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
	}

	// Bail if Menu Plus isn't activated
	if ( ! $wp_customize->get_section( 'menu_plus_slideout_menu' ) ) {
		return;
	}

	$wp_customize->add_section(
		'generate_slideout_typography',
		array(
			'title' => __( 'Off Canvas Panel', 'gp-premium' ),
			'capability' => 'edit_theme_options',
			'priority' => 52,
			'panel' => 'generate_typography_panel'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_off_canvas_panel_typography_shortcuts',
			array(
				'section' => 'generate_slideout_typography',
				'element' => esc_html__( 'Off Canvas Panel', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'slideout_color_section',
					'layout' => 'menu_plus_slideout_menu',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_setting(
		'generate_settings[slideout_font_weight]',
		array(
			'default' => $defaults['slideout_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);

	// Text transform
	$wp_customize->add_setting(
		'generate_settings[slideout_font_transform]',
		array(
			'default' => $defaults['slideout_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'slideout_navigation_typography',
			array(
				'label' => esc_html__( 'Menu Items', 'gp-premium' ),
				'section' => 'generate_slideout_typography',
				'settings' => array(
					'weight' => 'generate_settings[slideout_font_weight]',
					'transform' => 'generate_settings[slideout_font_transform]',
				),
			)
		)
	);

	// Font size
	$wp_customize->add_setting(
		'generate_settings[slideout_font_size]',
		array(
			'default' => $defaults['slideout_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_setting(
		'generate_settings[slideout_mobile_font_size]',
		array(
			'default' => $defaults['slideout_mobile_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[slideout_font_size]',
			array(
				'description' => __( 'Font size', 'gp-premium' ),
				'section' => 'generate_slideout_typography',
				'settings' => array(
					'desktop' => 'generate_settings[slideout_font_size]',
					'mobile' => 'generate_settings[slideout_mobile_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
}
