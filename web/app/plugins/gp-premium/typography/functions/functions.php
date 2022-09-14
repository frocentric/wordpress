<?php
/**
 * This file handles the premium Typography functions.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Include necessary files.
require_once trailingslashit( dirname( __FILE__ ) ) . 'migration.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'secondary-nav-fonts.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'woocommerce-fonts.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'slideout-nav-fonts.php';

if ( ! function_exists( 'generate_fonts_customize_register' ) ) {
	add_action( 'customize_register', 'generate_fonts_customize_register' );
	/**
	 * Build the Customizer controls.
	 *
	 * @since 0.1
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_fonts_customize_register( $wp_customize ) {
		// Bail if we don't have our defaults function.
		if ( ! function_exists( 'generate_get_default_fonts' ) ) {
			return;
		}

		// Get our custom controls.
		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Get our defaults.
		$defaults = generate_get_default_fonts();

		// Register our custom control types.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Pro_Typography_Customize_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		// Add the typography panel.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			$wp_customize->add_panel(
				'generate_typography_panel',
				array(
					'priority'       => 30,
					'capability'     => 'edit_theme_options',
					'theme_supports' => '',
					'title'          => __( 'Typography', 'gp-premium' ),
					'description'    => '',
				)
			);
		}

		// Body section.
		$wp_customize->add_section(
			'font_section',
			array(
				'title' => __( 'Body', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 30,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_body_typography_shortcuts',
				array(
					'section' => 'font_section',
					'element' => __( 'Body', 'gp-premium' ),
					'shortcuts' => array(
						'colors' => 'body_section',
						'backgrounds' => 'generate_backgrounds_body',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_body]',
			array(
				'default' => $defaults['font_body'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_body_variants',
			array(
				'default' => $defaults['font_body_variants'],
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'font_body_category',
			array(
				'default' => $defaults['font_body_category'],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[body_font_weight]',
			array(
				'default' => $defaults['body_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[body_font_transform]',
			array(
				'default' => $defaults['body_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'body_typography',
				array(
					'section' => 'font_section',
					'priority' => 1,
					'settings' => array(
						'family' => 'generate_settings[font_body]',
						'variant' => 'font_body_variants',
						'category' => 'font_body_category',
						'weight' => 'generate_settings[body_font_weight]',
						'transform' => 'generate_settings[body_font_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[body_font_size]',
			array(
				'default' => $defaults['body_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[body_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_section',
					'priority' => 40,
					'settings' => array(
						'desktop' => 'generate_settings[body_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 6,
							'max' => 25,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[body_line_height]',
			array(
				'default' => $defaults['body_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[body_line_height]',
				array(
					'description' => __( 'Line height', 'gp-premium' ),
					'section' => 'font_section',
					'priority' => 45,
					'settings' => array(
						'desktop' => 'generate_settings[body_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 1,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => '',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[paragraph_margin]',
			array(
				'default' => $defaults['paragraph_margin'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[paragraph_margin]',
				array(
					'description' => __( 'Paragraph margin', 'gp-premium' ),
					'section' => 'font_section',
					'priority' => 47,
					'settings' => array(
						'desktop' => 'generate_settings[paragraph_margin]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);

		$wp_customize->add_section(
			'generate_top_bar_typography',
			array(
				'title' => __( 'Top Bar', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 30,
				'panel' => 'generate_typography_panel',
			)
		);

		if ( isset( $defaults['font_top_bar'] ) && function_exists( 'generate_is_top_bar_active' ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_top_bar]',
				array(
					'default' => $defaults['font_top_bar'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_top_bar_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_top_bar_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[top_bar_font_weight]',
				array(
					'default' => $defaults['top_bar_font_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[top_bar_font_transform]',
				array(
					'default' => $defaults['top_bar_font_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'top_bar_typography',
					array(
						'section' => 'generate_top_bar_typography',
						'settings' => array(
							'family' => 'generate_settings[font_top_bar]',
							'variant' => 'font_top_bar_variants',
							'category' => 'font_top_bar_category',
							'weight' => 'generate_settings[top_bar_font_weight]',
							'transform' => 'generate_settings[top_bar_font_transform]',
						),
						'active_callback' => 'generate_premium_is_top_bar_active',
					)
				)
			);

		}

		if ( isset( $defaults['top_bar_font_size'] ) && function_exists( 'generate_is_top_bar_active' ) ) {
			$wp_customize->add_setting(
				'generate_settings[top_bar_font_size]',
				array(
					'default' => $defaults['top_bar_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'absint',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[top_bar_font_size]',
					array(
						'description' => __( 'Font size', 'gp-premium' ),
						'section' => 'generate_top_bar_typography',
						'priority' => 75,
						'settings' => array(
							'desktop' => 'generate_settings[top_bar_font_size]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 6,
								'max' => 25,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
						'active_callback' => 'generate_premium_is_top_bar_active',
					)
				)
			);
		}

		$wp_customize->add_section(
			'font_header_section',
			array(
				'title' => __( 'Header', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 40,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_header_typography_shortcuts',
				array(
					'section' => 'font_header_section',
					'element' => __( 'Header', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_header',
						'colors' => 'header_color_section',
						'backgrounds' => 'generate_backgrounds_header',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_site_title]',
			array(
				'default' => $defaults['font_site_title'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_site_title_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_site_title_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_title_font_weight]',
			array(
				'default' => $defaults['site_title_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_title_font_transform]',
			array(
				'default' => $defaults['site_title_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'site_title_typography',
				array(
					'label' => __( 'Site Title', 'gp-premium' ),
					'section' => 'font_header_section',
					'settings' => array(
						'family' => 'generate_settings[font_site_title]',
						'variant' => 'font_site_title_variants',
						'category' => 'font_site_title_category',
						'weight' => 'generate_settings[site_title_font_weight]',
						'transform' => 'generate_settings[site_title_font_transform]',
					),
					'priority' => 50,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_title_font_size]',
			array(
				'default' => $defaults['site_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[tablet_site_title_font_size]',
			array(
				'default' => $defaults['tablet_site_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[mobile_site_title_font_size]',
			array(
				'default' => $defaults['mobile_site_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[site_title_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_header_section',
					'priority' => 75,
					'settings' => array(
						'desktop' => 'generate_settings[site_title_font_size]',
						'tablet' => 'generate_settings[tablet_site_title_font_size]',
						'mobile' => 'generate_settings[mobile_site_title_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 10,
							'max' => 200,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'tablet' => array(
							'min' => 10,
							'max' => 200,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
							'min' => 10,
							'max' => 200,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_site_tagline]',
			array(
				'default' => $defaults['font_site_tagline'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_site_tagline_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_site_tagline_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_tagline_font_weight]',
			array(
				'default' => $defaults['site_tagline_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_tagline_font_transform]',
			array(
				'default' => $defaults['site_tagline_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_site_tagline_control',
				array(
					'label' => __( 'Site Tagline', 'gp-premium' ),
					'section' => 'font_header_section',
					'settings' => array(
						'family' => 'generate_settings[font_site_tagline]',
						'variant' => 'font_site_tagline_variants',
						'category' => 'font_site_tagline_category',
						'weight' => 'generate_settings[site_tagline_font_weight]',
						'transform' => 'generate_settings[site_tagline_font_transform]',
					),
					'priority' => 80,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[site_tagline_font_size]',
			array(
				'default' => $defaults['site_tagline_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[site_tagline_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_header_section',
					'priority' => 105,
					'settings' => array(
						'desktop' => 'generate_settings[site_tagline_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 6,
							'max' => 50,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		$wp_customize->add_section(
			'font_navigation_section',
			array(
				'title' => __( 'Primary Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 50,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_primary_navigation_typography_shortcuts',
				array(
					'section' => 'font_navigation_section',
					'element' => __( 'Primary Navigation', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_navigation',
						'colors' => 'navigation_color_section',
						'backgrounds' => 'generate_backgrounds_navigation',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_navigation]',
			array(
				'default' => $defaults['font_navigation'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_navigation_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_navigation_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_font_weight]',
			array(
				'default' => $defaults['navigation_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_font_transform]',
			array(
				'default' => $defaults['navigation_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'google_font_site_navigation_control',
				array(
					'section' => 'font_navigation_section',
					'settings' => array(
						'family' => 'generate_settings[font_navigation]',
						'variant' => 'font_navigation_variants',
						'category' => 'font_navigation_category',
						'weight' => 'generate_settings[navigation_font_weight]',
						'transform' => 'generate_settings[navigation_font_transform]',
					),
					'priority' => 120,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_font_size]',
			array(
				'default' => $defaults['navigation_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[tablet_navigation_font_size]',
			array(
				'default' => $defaults['tablet_navigation_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[mobile_navigation_font_size]',
			array(
				'default' => $defaults['mobile_navigation_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[navigation_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_navigation_section',
					'priority' => 165,
					'settings' => array(
						'desktop' => 'generate_settings[navigation_font_size]',
						'tablet' => 'generate_settings[tablet_navigation_font_size]',
						'mobile' => 'generate_settings[mobile_navigation_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 6,
							'max' => 30,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'tablet' => array(
							'min' => 6,
							'max' => 30,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
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

		$wp_customize->add_section(
			'font_buttons_section',
			array(
				'title' => __( 'Buttons', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 55,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_buttons_typography_shortcuts',
				array(
					'section' => 'font_buttons_section',
					'element' => __( 'Button', 'gp-premium' ),
					'shortcuts' => array(
						'colors' => 'buttons_color_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		if ( isset( $defaults['font_buttons'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_buttons]',
				array(
					'default' => $defaults['font_buttons'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_buttons_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_buttons_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[buttons_font_weight]',
				array(
					'default' => $defaults['buttons_font_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[buttons_font_transform]',
				array(
					'default' => $defaults['buttons_font_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'font_buttons_control',
					array(
						'section' => 'font_buttons_section',
						'settings' => array(
							'family' => 'generate_settings[font_buttons]',
							'variant' => 'font_buttons_variants',
							'category' => 'font_buttons_category',
							'weight' => 'generate_settings[buttons_font_weight]',
							'transform' => 'generate_settings[buttons_font_transform]',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[buttons_font_size]',
				array(
					'default' => $defaults['buttons_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'buttons_font_sizes',
					array(
						'description' => __( 'Font size', 'gp-premium' ),
						'section' => 'font_buttons_section',
						'settings' => array(
							'desktop' => 'generate_settings[buttons_font_size]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 5,
								'max' => 100,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_section(
			'font_content_section',
			array(
				'title' => __( 'Headings', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 60,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_headings_typography_shortcuts',
				array(
					'section' => 'font_content_section',
					'element' => __( 'Content', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_container',
						'colors' => 'content_color_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_heading_1]',
			array(
				'default' => $defaults['font_heading_1'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_1_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_1_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_1_weight]',
			array(
				'default' => $defaults['heading_1_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_1_transform]',
			array(
				'default' => $defaults['heading_1_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_heading_1_control',
				array(
					'label' => __( 'Heading 1 (H1)', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'family' => 'generate_settings[font_heading_1]',
						'variant' => 'font_heading_1_variants',
						'category' => 'font_heading_1_category',
						'weight' => 'generate_settings[heading_1_weight]',
						'transform' => 'generate_settings[heading_1_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_1_font_size]',
			array(
				'default' => $defaults['heading_1_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[mobile_heading_1_font_size]',
			array(
				'default' => $defaults['mobile_heading_1_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'h1_font_sizes',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[heading_1_font_size]',
						'mobile' => 'generate_settings[mobile_heading_1_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		if ( isset( $defaults['heading_1_line_height'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_1_line_height]',
				array(
					'default' => $defaults['heading_1_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_1_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_1_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['heading_1_margin_bottom'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_1_margin_bottom]',
				array(
					'default' => $defaults['heading_1_margin_bottom'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_1_margin_bottom]',
					array(
						'description' => __( 'Bottom margin', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_1_margin_bottom]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'generate_settings[single_post_title_weight]',
			array(
				'default' => $defaults['single_post_title_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[single_post_title_transform]',
			array(
				'default' => $defaults['single_post_title_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'single_content_title_control',
				array(
					'label' => __( 'Single Content Title (H1)', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'weight' => 'generate_settings[single_post_title_weight]',
						'transform' => 'generate_settings[single_post_title_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[single_post_title_font_size]',
			array(
				'default' => $defaults['single_post_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[single_post_title_font_size_mobile]',
			array(
				'default' => $defaults['single_post_title_font_size_mobile'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'single_post_title_font_sizes',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[single_post_title_font_size]',
						'mobile' => 'generate_settings[single_post_title_font_size_mobile]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[single_post_title_line_height]',
			array(
				'default' => $defaults['single_post_title_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[single_post_title_line_height]',
				array(
					'description' => __( 'Line height', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[single_post_title_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_heading_2]',
			array(
				'default' => $defaults['font_heading_2'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_2_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_2_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_2_weight]',
			array(
				'default' => $defaults['heading_2_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_2_transform]',
			array(
				'default' => $defaults['heading_2_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_heading_2_control',
				array(
					'label' => __( 'Heading 2 (H2)', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'family' => 'generate_settings[font_heading_2]',
						'variant' => 'font_heading_2_variants',
						'category' => 'font_heading_2_category',
						'weight' => 'generate_settings[heading_2_weight]',
						'transform' => 'generate_settings[heading_2_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_2_font_size]',
			array(
				'default' => $defaults['heading_2_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[mobile_heading_2_font_size]',
			array(
				'default' => $defaults['mobile_heading_2_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'h2_font_sizes',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[heading_2_font_size]',
						'mobile' => 'generate_settings[mobile_heading_2_font_size]',
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

		if ( isset( $defaults['heading_2_line_height'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_2_line_height]',
				array(
					'default' => $defaults['heading_2_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_2_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_2_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['heading_2_margin_bottom'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_2_margin_bottom]',
				array(
					'default' => $defaults['heading_2_margin_bottom'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_2_margin_bottom]',
					array(
						'description' => __( 'Bottom margin', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_2_margin_bottom]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'generate_settings[archive_post_title_weight]',
			array(
				'default' => $defaults['archive_post_title_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[archive_post_title_transform]',
			array(
				'default' => $defaults['archive_post_title_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'archive_content_title_control',
				array(
					'label' => __( 'Archive Content Title (H2)', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'weight' => 'generate_settings[archive_post_title_weight]',
						'transform' => 'generate_settings[archive_post_title_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[archive_post_title_font_size]',
			array(
				'default' => $defaults['archive_post_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[archive_post_title_font_size_mobile]',
			array(
				'default' => $defaults['archive_post_title_font_size_mobile'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'archive_post_title_font_sizes',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[archive_post_title_font_size]',
						'mobile' => 'generate_settings[archive_post_title_font_size_mobile]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
							'min' => 15,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[archive_post_title_line_height]',
			array(
				'default' => $defaults['archive_post_title_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[archive_post_title_line_height]',
				array(
					'description' => __( 'Line height', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[archive_post_title_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_heading_3]',
			array(
				'default' => $defaults['font_heading_3'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_3_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_heading_3_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_3_weight]',
			array(
				'default' => $defaults['heading_3_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_3_transform]',
			array(
				'default' => $defaults['heading_3_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_heading_3_control',
				array(
					'label' => __( 'Heading 3 (H3)', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => array(
						'family' => 'generate_settings[font_heading_3]',
						'variant' => 'font_heading_3_variants',
						'category' => 'font_heading_3_category',
						'weight' => 'generate_settings[heading_3_weight]',
						'transform' => 'generate_settings[heading_3_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[heading_3_font_size]',
			array(
				'default' => $defaults['heading_3_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$h3_font_size_options = array(
			'desktop' => array(
				'min' => 10,
				'max' => 80,
				'step' => 1,
				'edit' => true,
				'unit' => 'px',
			),
		);

		$h3_font_size_settings = array(
			'desktop' => 'generate_settings[heading_3_font_size]',
		);

		if ( isset( $defaults['mobile_heading_3_font_size'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[mobile_heading_3_font_size]',
				array(
					'default' => $defaults['mobile_heading_3_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
					'transport' => 'postMessage',
				)
			);

			$h3_font_size_options['mobile'] = array(
				'min' => 10,
				'max' => 80,
				'step' => 1,
				'edit' => true,
				'unit' => 'px',
			);

			$h3_font_size_settings['mobile'] = 'generate_settings[mobile_heading_3_font_size]';
		}

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'h3_font_sizes',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_content_section',
					'settings' => $h3_font_size_settings,
					'choices' => $h3_font_size_options,
				)
			)
		);

		if ( isset( $defaults['heading_3_line_height'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_3_line_height]',
				array(
					'default' => $defaults['heading_3_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_3_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_3_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['heading_3_margin_bottom'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[heading_3_margin_bottom]',
				array(
					'default' => $defaults['heading_3_margin_bottom'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_3_margin_bottom]',
					array(
						'description' => __( 'Bottom margin', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_3_margin_bottom]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['font_heading_4'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_heading_4]',
				array(
					'default' => $defaults['font_heading_4'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_4_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_4_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_4_weight]',
				array(
					'default' => $defaults['heading_4_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_4_transform]',
				array(
					'default' => $defaults['heading_4_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'font_heading_4_control',
					array(
						'label' => __( 'Heading 4 (H4)', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'family' => 'generate_settings[font_heading_4]',
							'variant' => 'font_heading_4_variants',
							'category' => 'font_heading_4_category',
							'weight' => 'generate_settings[heading_4_weight]',
							'transform' => 'generate_settings[heading_4_transform]',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_4_font_size]',
				array(
					'default' => $defaults['heading_4_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
					'transport' => 'postMessage',
				)
			);

			$h4_font_size_options = array(
				'desktop' => array(
					'min' => 10,
					'max' => 80,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			);

			$h4_font_size_settings = array(
				'desktop' => 'generate_settings[heading_4_font_size]',
			);

			if ( isset( $defaults['mobile_heading_4_font_size'] ) ) {
				$wp_customize->add_setting(
					'generate_settings[mobile_heading_4_font_size]',
					array(
						'default' => $defaults['mobile_heading_4_font_size'],
						'type' => 'option',
						'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
						'transport' => 'postMessage',
					)
				);

				$h4_font_size_options['mobile'] = array(
					'min' => 10,
					'max' => 80,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				);

				$h4_font_size_settings['mobile'] = 'generate_settings[mobile_heading_4_font_size]';
			}

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'h4_font_sizes',
					array(
						'description' => __( 'Font size', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => $h4_font_size_settings,
						'choices' => $h4_font_size_options,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_4_line_height]',
				array(
					'default' => $defaults['heading_4_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_4_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_4_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['font_heading_5'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_heading_5]',
				array(
					'default' => $defaults['font_heading_5'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_5_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_5_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_5_weight]',
				array(
					'default' => $defaults['heading_5_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_5_transform]',
				array(
					'default' => $defaults['heading_5_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'font_heading_5_control',
					array(
						'label' => __( 'Heading 5 (H5)', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'family' => 'generate_settings[font_heading_5]',
							'variant' => 'font_heading_5_variants',
							'category' => 'font_heading_5_category',
							'weight' => 'generate_settings[heading_5_weight]',
							'transform' => 'generate_settings[heading_5_transform]',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_5_font_size]',
				array(
					'default' => $defaults['heading_5_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
					'transport' => 'postMessage',
				)
			);

			$h5_font_size_options = array(
				'desktop' => array(
					'min' => 10,
					'max' => 80,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			);

			$h5_font_size_settings = array(
				'desktop' => 'generate_settings[heading_5_font_size]',
			);

			if ( isset( $defaults['mobile_heading_5_font_size'] ) ) {
				$wp_customize->add_setting(
					'generate_settings[mobile_heading_5_font_size]',
					array(
						'default' => $defaults['mobile_heading_5_font_size'],
						'type' => 'option',
						'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
						'transport' => 'postMessage',
					)
				);

				$h5_font_size_options['mobile'] = array(
					'min' => 10,
					'max' => 80,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				);

				$h5_font_size_settings['mobile'] = 'generate_settings[mobile_heading_5_font_size]';
			}

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'h5_font_sizes',
					array(
						'description' => __( 'Font size', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => $h5_font_size_settings,
						'choices' => $h5_font_size_options,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_5_line_height]',
				array(
					'default' => $defaults['heading_5_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_5_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_5_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		if ( isset( $defaults['font_heading_6'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_heading_6]',
				array(
					'default' => $defaults['font_heading_6'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_6_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_heading_6_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_6_weight]',
				array(
					'default' => $defaults['heading_6_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_6_transform]',
				array(
					'default' => $defaults['heading_6_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'font_heading_6_control',
					array(
						'label' => __( 'Heading 6 (H6)', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'family' => 'generate_settings[font_heading_6]',
							'variant' => 'font_heading_6_variants',
							'category' => 'font_heading_6_category',
							'weight' => 'generate_settings[heading_6_weight]',
							'transform' => 'generate_settings[heading_6_transform]',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[heading_6_font_size]',
				array(
					'default' => $defaults['heading_6_font_size'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'h6_font_sizes',
					array(
						'description' => __( 'Font size', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_6_font_size]',
						),
						'choices' => array(
							'desktop' => array(
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

			$wp_customize->add_setting(
				'generate_settings[heading_6_line_height]',
				array(
					'default' => $defaults['heading_6_line_height'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[heading_6_line_height]',
					array(
						'description' => __( 'Line height', 'gp-premium' ),
						'section' => 'font_content_section',
						'settings' => array(
							'desktop' => 'generate_settings[heading_6_line_height]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 5,
								'step' => .1,
								'edit' => true,
								'unit' => 'em',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_section(
			'font_widget_section',
			array(
				'title' => __( 'Widgets', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 60,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_widgets_typography_shortcuts',
				array(
					'section' => 'font_widget_section',
					'element' => __( 'Widgets', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_sidebars',
						'colors' => 'sidebar_widget_color_section',
						'backgrounds' => 'generate_backgrounds_sidebars',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[font_widget_title]',
			array(
				'default' => $defaults['font_widget_title'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_widget_title_category',
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_setting(
			'font_widget_title_variants',
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[widget_title_font_weight]',
			array(
				'default' => $defaults['widget_title_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[widget_title_font_transform]',
			array(
				'default' => $defaults['widget_title_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'google_font_widget_title_control',
				array(
					'label' => __( 'Widget Titles', 'gp-premium' ),
					'section' => 'font_widget_section',
					'settings' => array(
						'family' => 'generate_settings[font_widget_title]',
						'variant' => 'font_widget_title_variants',
						'category' => 'font_widget_title_category',
						'weight' => 'generate_settings[widget_title_font_weight]',
						'transform' => 'generate_settings[widget_title_font_transform]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[widget_title_font_size]',
			array(
				'default' => $defaults['widget_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[widget_title_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_widget_section',
					'settings' => array(
						'desktop' => 'generate_settings[widget_title_font_size]',
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

		if ( isset( $defaults['widget_title_separator'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[widget_title_separator]',
				array(
					'default' => $defaults['widget_title_separator'],
					'type' => 'option',
					'sanitize_callback' => 'absint',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Range_Slider_Control(
					$wp_customize,
					'generate_settings[widget_title_separator]',
					array(
						'description' => __( 'Bottom margin', 'gp-premium' ),
						'section' => 'font_widget_section',
						'settings' => array(
							'desktop' => 'generate_settings[widget_title_separator]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'generate_settings[widget_content_font_size]',
			array(
				'default' => $defaults['widget_content_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[widget_content_font_size]',
				array(
					'description' => __( 'Content font size', 'gp-premium' ),
					'section' => 'font_widget_section',
					'priority' => 240,
					'settings' => array(
						'desktop' => 'generate_settings[widget_content_font_size]',
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

		$wp_customize->add_section(
			'font_footer_section',
			array(
				'title' => __( 'Footer', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'priority' => 70,
				'panel' => 'generate_typography_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_footer_typography_shortcuts',
				array(
					'section' => 'font_footer_section',
					'element' => __( 'Footer', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_footer',
						'colors' => 'footer_color_section',
						'backgrounds' => 'generate_backgrounds_footer',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		if ( isset( $defaults['font_footer'] ) ) {
			$wp_customize->add_setting(
				'generate_settings[font_footer]',
				array(
					'default' => $defaults['font_footer'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_footer_category',
				array(
					'default' => '',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_setting(
				'font_footer_variants',
				array(
					'default' => '',
					'sanitize_callback' => 'generate_premium_sanitize_variants',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[footer_weight]',
				array(
					'default' => $defaults['footer_weight'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[footer_transform]',
				array(
					'default' => $defaults['footer_transform'],
					'type' => 'option',
					'sanitize_callback' => 'sanitize_key',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Pro_Typography_Customize_Control(
					$wp_customize,
					'font_footer_control',
					array(
						'section' => 'font_footer_section',
						'settings' => array(
							'family' => 'generate_settings[font_footer]',
							'variant' => 'font_footer_variants',
							'category' => 'font_footer_category',
							'weight' => 'generate_settings[footer_weight]',
							'transform' => 'generate_settings[footer_transform]',
						),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'generate_settings[footer_font_size]',
			array(
				'default' => $defaults['footer_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[footer_font_size]',
				array(
					'description' => __( 'Font size', 'gp-premium' ),
					'section' => 'font_footer_section',
					'settings' => array(
						'desktop' => 'generate_settings[footer_font_size]',
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

if ( ! function_exists( 'generate_enqueue_google_fonts' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_enqueue_google_fonts', 0 );
	/**
	 * Enqueue Google Fonts.
	 *
	 * @since 0.1
	 */
	function generate_enqueue_google_fonts() {
		// Bail if we don't have our defaults function.
		if ( ! function_exists( 'generate_get_default_fonts' ) ) {
			return;
		}

		$generate_settings = wp_parse_args(
			get_option( 'generate_settings', array() ),
			generate_get_default_fonts()
		);

		// List our non-Google fonts.
		$not_google = str_replace( ' ', '+', generate_typography_default_fonts() );

		// Grab our font family settings.
		$font_settings = array(
			'font_body',
			'font_top_bar',
			'font_site_title',
			'font_site_tagline',
			'font_navigation',
			'font_widget_title',
			'font_buttons',
			'font_heading_1',
			'font_heading_2',
			'font_heading_3',
			'font_heading_4',
			'font_heading_5',
			'font_heading_6',
			'font_footer',
		);

		// Create our Google Fonts array.
		$google_fonts = array();
		if ( ! empty( $font_settings ) ) {
			foreach ( $font_settings as $key ) {
				// If the key isn't set, move on.
				if ( ! isset( $generate_settings[ $key ] ) ) {
					continue;
				}

				// If our value is still using the old format, fix it.
				if ( strpos( $generate_settings[ $key ], ':' ) !== false ) {
					$generate_settings[ $key ] = current( explode( ':', $generate_settings[ $key ] ) );
				}

				// Replace the spaces in the names with a plus.
				$value = str_replace( ' ', '+', $generate_settings[ $key ] );

				// Grab the variants using the plain name.
				$variants = generate_get_google_font_variants( $generate_settings[ $key ], $key, generate_get_default_fonts() );

				// If we have variants, add them to our value.
				$value = ! empty( $variants ) ? $value . ':' . $variants : $value;

				// Make sure we don't add the same font twice.
				if ( ! in_array( $value, $google_fonts ) ) {
					$google_fonts[] = $value;
				}
			}
		}

		// Ignore any non-Google fonts.
		$google_fonts = array_diff( $google_fonts, $not_google );

		// Separate each different font with a bar.
		$google_fonts = implode( '|', $google_fonts );

		// Apply a filter to the output.
		$google_fonts = apply_filters( 'generate_typography_google_fonts', $google_fonts );

		// Get the subset.
		$subset = apply_filters( 'generate_fonts_subset', '' );

		// Set up our arguments.
		$font_args = array();
		$font_args['family'] = $google_fonts;
		if ( '' !== $subset ) {
			$font_args['subset'] = urlencode( $subset ); // phpcs:ignore -- Keeping legacy urlencode().
		}

		$display = apply_filters( 'generate_google_font_display', '' );

		if ( $display ) {
			$font_args['display'] = $display;
		}

		// Create our URL using the arguments.
		$fonts_url = add_query_arg( $font_args, '//fonts.googleapis.com/css' );

		// Enqueue our fonts.
		if ( $google_fonts ) {
			wp_enqueue_style( 'generate-fonts', $fonts_url, array(), null, 'all' ); // phpcs:ignore -- Version not needed.
		}
	}
}

if ( ! function_exists( 'generate_get_all_google_fonts' ) ) {
	/**
	 * Return an array of all of our Google Fonts.
	 *
	 * @since 1.3.0
	 * @param string $amount The number of fonts to load.
	 */
	function generate_get_all_google_fonts( $amount = 'all' ) {
		ob_start();
		include wp_normalize_path( dirname( __FILE__ ) . '/google-fonts.json' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude
		$fonts_json = ob_get_clean();
		$google_fonts = json_decode( $fonts_json );

		// Loop through them and put what we need into our fonts array.
		$fonts = array();
		foreach ( (array) $google_fonts as $item ) {

			// Grab what we need from our big list.
			$atts = array(
				'name'     => $item->family,
				'category' => $item->category,
				'variants' => $item->variants,
			);

			// Create an ID using our font family name.
			$id = strtolower( str_replace( ' ', '_', $item->family ) );

			// Add our attributes to our new array.
			$fonts[ $id ] = $atts;
		}

		if ( 'all' !== $amount ) {
			$fonts = array_slice( $fonts, 0, $amount );
		}

		// Alphabetize our fonts.
		if ( apply_filters( 'generate_alphabetize_google_fonts', true ) ) {
			asort( $fonts );
		}

		// Filter to allow us to modify the fonts array.
		return apply_filters( 'generate_google_fonts_array', $fonts );
	}
}

if ( ! function_exists( 'generate_get_all_google_fonts_ajax' ) ) {
	add_action( 'wp_ajax_generate_get_all_google_fonts_ajax', 'generate_get_all_google_fonts_ajax' );
	/**
	 * Return an array of all of our Google Fonts.
	 *
	 * @since 1.3.0
	 */
	function generate_get_all_google_fonts_ajax() {
		// Bail if the nonce doesn't check out.
		if ( ! isset( $_POST['gp_customize_nonce'] ) || ! wp_verify_nonce( $_POST['gp_customize_nonce'], 'gp_customize_nonce' ) ) {
			wp_die();
		}

		// Do another nonce check.
		check_ajax_referer( 'gp_customize_nonce', 'gp_customize_nonce' );

		// Bail if user can't edit theme options.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die();
		}

		// Get all of our fonts.
		$fonts = apply_filters( 'generate_typography_customize_list', generate_get_all_google_fonts() );

		// Send all of our fonts in JSON format.
		echo wp_json_encode( $fonts );

		die();
	}
}

if ( ! function_exists( 'generate_get_google_font_variants' ) ) {
	/**
	 * Wrapper function to find variants for chosen Google Fonts
	 * Example: generate_get_google_font_variation( 'Open Sans' )
	 *
	 * @since 1.3.0
	 * @param string $font The font we're checking.
	 * @param string $key The key we're checking.
	 * @param array  $default The defaults we're checking.
	 */
	function generate_get_google_font_variants( $font, $key = '', $default = '' ) {
		// Bail if we don't have our defaults function.
		if ( ! function_exists( 'generate_get_default_fonts' ) ) {
			return;
		}

		// Don't need variants if we're using a system font.
		if ( in_array( $font, generate_typography_default_fonts() ) ) {
			return;
		}

		// Return if we have our variants saved.
		if ( '' !== $key && get_theme_mod( $key . '_variants' ) ) {
			return get_theme_mod( $key . '_variants' );
		}

		// Make sure we have defaults.
		if ( '' == $default ) { // phpcs:ignore -- Non-strict allowed.
			$default = generate_get_default_fonts();
		}

		// If our default font is selected and the category isn't saved, we already know the category.
		if ( $default[ $key ] == $font ) { // phpcs:ignore -- Non-strict allowed.
			return $default[ $key . '_variants' ];
		}

		// Grab all of our fonts.
		// It's a big list, so hopefully we're not even still reading.
		$fonts = generate_get_all_google_fonts();

		// Get the ID from our font.
		$id = strtolower( str_replace( ' ', '_', $font ) );

		// If the ID doesn't exist within our fonts, we can bail.
		if ( ! array_key_exists( $id, $fonts ) ) {
			return;
		}

		// Grab all of the variants associated with our font.
		$variants = $fonts[ $id ]['variants'];

		// Loop through them and put them into an array, then turn them into a comma separated list.
		$output = array();
		if ( $variants ) {
			foreach ( $variants as $variant ) {
				$output[] = $variant;
			}

			return implode( ',', apply_filters( 'generate_typography_variants', $output ) );
		}
	}
}

if ( ! function_exists( 'generate_get_google_font_category' ) ) {
	/**
	 * Wrapper function to find the category for chosen Google Font
	 * Example: generate_get_google_font_category( 'Open Sans' )
	 *
	 * @since 1.3.0
	 * @param string $font The font we're checking.
	 * @param string $key The key we're checking.
	 * @param array  $default The defaults we're checking.
	 */
	function generate_get_google_font_category( $font, $key = '', $default = '' ) {
		// Bail if we don't have our defaults function.
		if ( ! function_exists( 'generate_get_default_fonts' ) ) {
			return;
		}

		// Don't need a category if we're using a system font.
		if ( in_array( $font, generate_typography_default_fonts() ) ) {
			return;
		}

		// Return if we have our variants saved.
		if ( '' !== $key && get_theme_mod( $key . '_category' ) ) {
			return ', ' . get_theme_mod( $key . '_category' );
		}

		// Make sure we have defaults.
		if ( '' == $default ) { // phpcs:ignore -- Non-strict allowed.
			$default = generate_get_default_fonts();
		}

		// If our default font is selected and the category isn't saved, we already know the category.
		if ( $default[ $key ] == $font ) { // phpcs:ignore -- Non-strict allowed.
			return ', ' . $default[ $key . '_category' ];
		}

		// Get all of our fonts.
		// It's a big list, so hopefully we're not even still reading.
		$fonts = generate_get_all_google_fonts();

		// Get the ID from our font.
		$id = strtolower( str_replace( ' ', '_', $font ) );

		// If the ID doesn't exist within our fonts, we can bail.
		if ( ! array_key_exists( $id, $fonts ) ) {
			return;
		}

		// Let's grab our category to go with our font.
		$category = ! empty( $fonts[ $id ]['category'] ) ? ', ' . $fonts[ $id ]['category'] : '';

		// Return it to be used by our function.
		return $category;

	}
}

if ( ! function_exists( 'generate_get_font_family_css' ) ) {
	/**
	 * Wrapper function to create font-family value for CSS.
	 *
	 * @since 1.3.0
	 * @param string $font The font we're checking.
	 * @param array  $settings The settings we're checking.
	 * @param array  $default The defaults we're checking.
	 */
	function generate_get_font_family_css( $font, $settings, $default ) {
		$generate_settings = wp_parse_args(
			get_option( $settings, array() ),
			$default
		);

		// We don't want to wrap quotes around these values.
		$no_quotes = array(
			'inherit',
			'Arial, Helvetica, sans-serif',
			'Georgia, Times New Roman, Times, serif',
			'Helvetica',
			'Impact',
			'Segoe UI, Helvetica Neue, Helvetica, sans-serif',
			'Tahoma, Geneva, sans-serif',
			'Trebuchet MS, Helvetica, sans-serif',
			'Verdana, Geneva, sans-serif',
			apply_filters( 'generate_typography_system_stack', '-apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"' ),
		);

		// Get our font.
		$font_family = $generate_settings[ $font ];

		if ( 'System Stack' === $font_family ) {
			$font_family = apply_filters( 'generate_typography_system_stack', '-apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"' );
		}

		// If our value is still using the old format, fix it.
		if ( strpos( $font_family, ':' ) !== false ) {
			$font_family = current( explode( ':', $font_family ) );
		}

		// Set up our wrapper.
		if ( in_array( $font_family, $no_quotes ) ) {
			$wrapper_start = null;
			$wrapper_end = null;
		} else {
			$wrapper_start = '"';
			$wrapper_end = '"' . generate_get_google_font_category( $font_family, $font, $default );
		}

		// Output the CSS.
		$output = ( 'inherit' === $font_family ) ? 'inherit' : $wrapper_start . $font_family . $wrapper_end;
		return $output;
	}
}

if ( ! function_exists( 'generate_typography_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'generate_typography_customizer_live_preview' );
	/**
	 * Add our live preview JS
	 */
	function generate_typography_customizer_live_preview() {
		wp_enqueue_script(
			'generate-typography-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
			array( 'jquery', 'customize-preview' ),
			GENERATE_FONT_VERSION,
			true
		);

		wp_localize_script(
			'generate-typography-customizer',
			'gp_typography',
			array(
				'mobile' => generate_premium_get_media_query( 'mobile' ),
				'tablet' => generate_premium_get_media_query( 'tablet' ),
				'desktop' => generate_premium_get_media_query( 'desktop' ),
			)
		);
	}
}

if ( ! function_exists( 'generate_typography_default_fonts' ) ) {
	/**
	 * Get our system fonts
	 */
	function generate_typography_default_fonts() {
		$fonts = array(
			'inherit',
			'System Stack',
			'Arial, Helvetica, sans-serif',
			'Century Gothic',
			'Comic Sans MS',
			'Courier New',
			'Georgia, Times New Roman, Times, serif',
			'Helvetica',
			'Impact',
			'Lucida Console',
			'Lucida Sans Unicode',
			'Palatino Linotype',
			'Segoe UI, Helvetica Neue, Helvetica, sans-serif',
			'Tahoma, Geneva, sans-serif',
			'Trebuchet MS, Helvetica, sans-serif',
			'Verdana, Geneva, sans-serif',
		);

		return apply_filters( 'generate_typography_default_fonts', $fonts );
	}
}

if ( ! function_exists( 'generate_include_typography_defaults' ) ) {
	/**
	 * Check if we should include our default.css file.
	 *
	 * @since 1.3.42
	 */
	function generate_include_typography_defaults() {
		return true;
	}
}

if ( ! function_exists( 'generate_typography_premium_css_defaults' ) ) {
	add_filter( 'generate_font_option_defaults', 'generate_typography_premium_css_defaults' );
	/**
	 * Add premium control defaults
	 *
	 * @since 1.3
	 * @param array $defaults The existing defaults.
	 */
	function generate_typography_premium_css_defaults( $defaults ) {
		$defaults['tablet_site_title_font_size'] = '';
		$defaults['tablet_navigation_font_size'] = '';
		$defaults['mobile_navigation_font_size'] = '';

		$defaults['single_post_title_weight'] = '';
		$defaults['single_post_title_transform'] = '';
		$defaults['single_post_title_font_size'] = '';
		$defaults['single_post_title_font_size_mobile'] = '';
		$defaults['single_post_title_line_height'] = '';

		$defaults['archive_post_title_weight'] = '';
		$defaults['archive_post_title_transform'] = '';
		$defaults['archive_post_title_font_size'] = '';
		$defaults['archive_post_title_font_size_mobile'] = '';
		$defaults['archive_post_title_line_height'] = '';

		return $defaults;
	}
}

/**
 * Premium typography CSS output.
 */
function generate_typography_get_premium_css() {
	if ( ! function_exists( 'generate_get_default_fonts' ) ) {
		return;
	}

	$generate_settings = wp_parse_args(
		get_option( 'generate_settings', array() ),
		generate_get_default_fonts()
	);

	// Initiate our CSS class.
	require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';

	$premium_css = new GeneratePress_Pro_CSS();

	$site_title_family = false;
	if ( function_exists( 'generate_get_font_family_css' ) ) {
		$site_title_family = generate_get_font_family_css( 'font_site_title', 'generate_settings', generate_get_default_fonts() );
	}

	$premium_css->set_selector( 'h1.entry-title' );
	$premium_css->add_property( 'font-weight', esc_attr( $generate_settings['single_post_title_weight'] ) );
	$premium_css->add_property( 'text-transform', esc_attr( $generate_settings['single_post_title_transform'] ) );

	if ( '' !== $generate_settings['single_post_title_font_size'] ) {
		$premium_css->add_property( 'font-size', absint( $generate_settings['single_post_title_font_size'] ), false, 'px' );
	}

	if ( '' !== $generate_settings['single_post_title_line_height'] ) {
		$premium_css->add_property( 'line-height', floatval( $generate_settings['single_post_title_line_height'] ), false, 'em' );
	}

	$premium_css->set_selector( 'h2.entry-title' );
	$premium_css->add_property( 'font-weight', esc_attr( $generate_settings['archive_post_title_weight'] ) );
	$premium_css->add_property( 'text-transform', esc_attr( $generate_settings['archive_post_title_transform'] ) );

	if ( '' !== $generate_settings['archive_post_title_font_size'] ) {
		$premium_css->add_property( 'font-size', absint( $generate_settings['archive_post_title_font_size'] ), false, 'px' );
	}

	if ( '' !== $generate_settings['archive_post_title_line_height'] ) {
		$premium_css->add_property( 'line-height', floatval( $generate_settings['archive_post_title_line_height'] ), false, 'em' );
	}

	if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
		$menu_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		if ( $menu_settings['navigation_as_header'] || $menu_settings['sticky_navigation_logo'] || 'enable' === $menu_settings['mobile_header'] ) {
			$premium_css->set_selector( '.navigation-branding .main-title' );
			$premium_css->add_property( 'font-weight', esc_attr( $generate_settings['site_title_font_weight'] ) );
			$premium_css->add_property( 'text-transform', esc_attr( $generate_settings['site_title_font_transform'] ) );
			$premium_css->add_property( 'font-size', absint( $generate_settings['site_title_font_size'] ), false, 'px' );

			if ( $site_title_family ) {
				$premium_css->add_property( 'font-family', 'inherit' !== $generate_settings['font_site_title'] ? $site_title_family : null );
			}
		}
	}

	$premium_css->start_media_query( generate_premium_get_media_query( 'tablet' ) );

	if ( '' !== $generate_settings['tablet_navigation_font_size'] ) {
		$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) a, .main-navigation .menu-toggle, .main-navigation .menu-bar-items' );
		$premium_css->add_property( 'font-size', absint( $generate_settings['tablet_navigation_font_size'] ), false, 'px' );

		$tablet_subnav_font_size = $generate_settings['tablet_navigation_font_size'] - 1;

		if ( $generate_settings['tablet_navigation_font_size'] >= 17 ) {
			$tablet_subnav_font_size = $generate_settings['tablet_navigation_font_size'] - 3;
		}

		$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) .main-nav ul ul li a' );
		$premium_css->add_property( 'font-size', absint( $tablet_subnav_font_size ), false, 'px' );
	}

	if ( '' !== $generate_settings['tablet_site_title_font_size'] ) {
		$premium_css->set_selector( '.main-title, .navigation-branding .main-title' );
		$premium_css->add_property( 'font-size', absint( $generate_settings['tablet_site_title_font_size'] ), false, 'px' );
	}

	$premium_css->stop_media_query();

	$premium_css->start_media_query( generate_premium_get_media_query( 'mobile' ) );

	$premium_css->set_selector( 'h1.entry-title' );

	if ( '' !== $generate_settings['single_post_title_font_size_mobile'] ) {
		$premium_css->add_property( 'font-size', absint( $generate_settings['single_post_title_font_size_mobile'] ), false, 'px' );
	}

	$premium_css->set_selector( 'h2.entry-title' );

	if ( '' !== $generate_settings['archive_post_title_font_size_mobile'] ) {
		$premium_css->add_property( 'font-size', absint( $generate_settings['archive_post_title_font_size_mobile'] ), false, 'px' );
	}

	$premium_css->stop_media_query();

	$premium_css->start_media_query( generate_premium_get_media_query( 'mobile-menu' ) );

	if ( ! empty( $generate_settings['mobile_navigation_font_size'] ) ) {
		$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) a, .main-navigation .menu-toggle, .main-navigation .menu-bar-items' );
		$premium_css->add_property( 'font-size', absint( $generate_settings['mobile_navigation_font_size'] ), false, 'px' );

		$mobile_subnav_font_size = $generate_settings['mobile_navigation_font_size'] - 1;

		if ( $generate_settings['mobile_navigation_font_size'] >= 17 ) {
			$mobile_subnav_font_size = $generate_settings['mobile_navigation_font_size'] - 3;
		}

		$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) .main-nav ul ul li a' );
		$premium_css->add_property( 'font-size', absint( $mobile_subnav_font_size ), false, 'px' );
	}

	if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
		$menu_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		if ( $menu_settings['navigation_as_header'] || $menu_settings['sticky_navigation_logo'] || 'enable' === $menu_settings['mobile_header'] ) {
			if ( ! empty( $generate_settings['mobile_site_title_font_size'] ) ) {
				$premium_css->set_selector( '.navigation-branding .main-title' );
				$premium_css->add_property( 'font-size', absint( $generate_settings['mobile_site_title_font_size'] ), false, 'px' );
			}
		}
	}

	$premium_css->stop_media_query();

	return $premium_css->css_output();
}

if ( ! function_exists( 'generate_typography_premium_css' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_typography_premium_css', 100 );
	/**
	 * Add premium control CSS
	 *
	 * @since 1.3
	 */
	function generate_typography_premium_css() {
		if ( 'inline' === generate_get_css_print_method() ) {
			wp_add_inline_style( 'generate-style', generate_typography_get_premium_css() );
		}
	}
}

add_filter( 'generate_external_dynamic_css_output', 'generate_typography_add_to_external_stylesheet' );
/**
 * Add CSS to the external stylesheet.
 *
 * @since 1.11.0
 * @param string $css Existing CSS.
 */
function generate_typography_add_to_external_stylesheet( $css ) {
	if ( 'inline' === generate_get_css_print_method() ) {
		return $css;
	}

	$css .= generate_typography_get_premium_css();

	return $css;
}
