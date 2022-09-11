<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_fonts_secondary_nav_customizer' ) ) {
	add_action( 'customize_register', 'generate_fonts_secondary_nav_customizer', 1000 );
	/**
	 * Adds our Secondary Nav typography options
	 *
	 * These options are in their own function so we can hook it in late to
	 * make sure Secondary Nav is activated.
	 *
	 * 1000 priority is there to make sure Secondary Nav is registered (999)
	 * as we check to see if the layout control exists.
	 *
	 * Secondary Nav now uses 100 as a priority.
	 */
	function generate_fonts_secondary_nav_customizer( $wp_customize ) {
		// Bail if we don't have our defaults function
		if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
			return;
		}

		// Make sure Secondary Nav is activated
		if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
			return;
		}

		// Get our controls
		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Get our defaults
		$defaults = generate_secondary_nav_get_defaults();

		// Register our custom controls
		if ( method_exists( $wp_customize,'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Pro_Typography_Customize_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		// Add our section
		$wp_customize->add_section(
			'secondary_font_section',
			array(
				'title' => __( 'Secondary Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 51,
				'panel' => 'generate_typography_panel'
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_secondary_navigation_typography_shortcuts',
				array(
					'section' => 'secondary_font_section',
					'element' => __( 'Secondary Navigation', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'secondary_nav_section',
						'colors' => 'secondary_navigation_color_section',
						'backgrounds' => 'secondary_bg_images_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		// Font family
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[font_secondary_navigation]',
			array(
				'default' => $defaults['font_secondary_navigation'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);

		// Category
		$wp_customize->add_setting(
			'font_secondary_navigation_category',
			array(
				'default' => $defaults['font_secondary_navigation_category'],
				'sanitize_callback' => 'sanitize_text_field'
			)
		);

		// Variants
		$wp_customize->add_setting(
			'font_secondary_navigation_variants',
			array(
				'default' => $defaults['font_secondary_navigation_variants'],
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);

		// Font weight
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_navigation_font_weight]',
			array(
				'default' => $defaults['secondary_navigation_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);

		// Font transform
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_navigation_font_transform]',
			array(
				'default' => $defaults['secondary_navigation_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'google_font_site_secondary_navigation_control',
				array(
					'section' => 'secondary_font_section',
					'settings' => array(
						'family' => 'generate_secondary_nav_settings[font_secondary_navigation]',
						'variant' => 'font_secondary_navigation_variants',
						'category' => 'font_secondary_navigation_category',
						'weight' => 'generate_secondary_nav_settings[secondary_navigation_font_weight]',
						'transform' => 'generate_secondary_nav_settings[secondary_navigation_font_transform]',
					),
				)
			)
		);

		// Font size
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_navigation_font_size]',
			array(
				'default' => $defaults['secondary_navigation_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_secondary_nav_settings[secondary_navigation_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'secondary_font_section',
					'priority' => 165,
					'settings' => array(
						'desktop' => 'generate_secondary_nav_settings[secondary_navigation_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 6,
							'max' => 30,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	}
}
