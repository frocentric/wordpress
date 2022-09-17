<?php
/**
 * This file handles the Customizer options for the Off-Canvas Panel.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'customize_preview_init', 'generate_menu_plus_live_preview_scripts', 20 );
/**
 * Add live preview JS to the Customizer.
 */
function generate_menu_plus_live_preview_scripts() {
	wp_enqueue_script( 'generate-menu-plus-colors-customizer' );
}

add_action( 'customize_register', 'generate_slideout_navigation_color_controls', 150 );
/**
 * Adds our Slideout Nav color options
 *
 * @since 1.6
 * @param object $wp_customize The Customizer object.
 */
function generate_slideout_navigation_color_controls( $wp_customize ) {
	// Bail if Secondary Nav isn't activated.
	if ( ! $wp_customize->get_section( 'menu_plus_slideout_menu' ) ) {
		return;
	}

	// Bail if we don't have our color defaults.
	if ( ! function_exists( 'generate_get_color_defaults' ) ) {
		return;
	}

	// Add our controls.
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Get our defaults.
	$defaults = generate_get_color_defaults();

	// Add control types so controls can be built using JS.
	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Alpha_Color_Customize_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
	}

	// Get our palettes.
	$palettes = generate_get_default_color_palettes();

	// Add Secondary Navigation section.
	$wp_customize->add_section(
		'slideout_color_section',
		array(
			'title' => __( 'Off Canvas Panel', 'gp-premium' ),
			'capability' => 'edit_theme_options',
			'priority' => 73,
			'panel' => 'generate_colors_panel',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_off_canvas_color_shortcuts',
			array(
				'section' => 'slideout_color_section',
				'element' => __( 'Off Canvas Panel', 'gp-premium' ),
				'shortcuts' => array(
					'layout' => 'menu_plus_slideout_menu',
					'typography' => 'generate_slideout_typography',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Title_Customize_Control(
			$wp_customize,
			'generate_slideout_navigation_items',
			array(
				'section'  => 'slideout_color_section',
				'type'     => 'generatepress-customizer-title',
				'title'    => __( 'Parent Menu Items', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
			)
		)
	);

	// Background.
	$wp_customize->add_setting(
		'generate_settings[slideout_background_color]',
		array(
			'default' => $defaults['slideout_background_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_background_color]',
			array(
				'label' => __( 'Background', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_background_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text.
	$wp_customize->add_setting(
		'generate_settings[slideout_text_color]',
		array(
			'default' => $defaults['slideout_text_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_text_color]',
			array(
				'label' => __( 'Text', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_text_color]',
			)
		)
	);

	// Background hover.
	$wp_customize->add_setting(
		'generate_settings[slideout_background_hover_color]',
		array(
			'default' => $defaults['slideout_background_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_background_hover_color]',
			array(
				'label' => __( 'Background Hover', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_background_hover_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text hover.
	$wp_customize->add_setting(
		'generate_settings[slideout_text_hover_color]',
		array(
			'default' => $defaults['slideout_text_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_text_hover_color]',
			array(
				'label' => __( 'Text Hover', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_text_hover_color]',
			)
		)
	);

	// Background current.
	$wp_customize->add_setting(
		'generate_settings[slideout_background_current_color]',
		array(
			'default' => $defaults['slideout_background_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_background_current_color]',
			array(
				'label' => __( 'Background Current', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_background_current_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text current.
	$wp_customize->add_setting(
		'generate_settings[slideout_text_current_color]',
		array(
			'default' => $defaults['slideout_text_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_text_current_color]',
			array(
				'label' => __( 'Text Current', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_text_current_color]',
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Title_Customize_Control(
			$wp_customize,
			'generate_slideout_navigation_sub_menu_items',
			array(
				'section'  => 'slideout_color_section',
				'type'     => 'generatepress-customizer-title',
				'title'    => __( 'Sub-Menu Items', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
			)
		)
	);

	// Background.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_background_color]',
		array(
			'default' => $defaults['slideout_submenu_background_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_background_color]',
			array(
				'label' => __( 'Background', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_background_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_text_color]',
		array(
			'default' => $defaults['slideout_submenu_text_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_text_color]',
			array(
				'label' => __( 'Text', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_text_color]',
			)
		)
	);

	// Background hover.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_background_hover_color]',
		array(
			'default' => $defaults['slideout_submenu_background_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_background_hover_color]',
			array(
				'label' => __( 'Background Hover', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_background_hover_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text hover.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_text_hover_color]',
		array(
			'default' => $defaults['slideout_submenu_text_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_text_hover_color]',
			array(
				'label' => __( 'Text Hover', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_text_hover_color]',
			)
		)
	);

	// Background current.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_background_current_color]',
		array(
			'default' => $defaults['slideout_submenu_background_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Alpha_Color_Customize_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_background_current_color]',
			array(
				'label' => __( 'Background Current', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_background_current_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text current.
	$wp_customize->add_setting(
		'generate_settings[slideout_submenu_text_current_color]',
		array(
			'default' => $defaults['slideout_submenu_text_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'generate_settings[slideout_submenu_text_current_color]',
			array(
				'label' => __( 'Text Current', 'gp-premium' ),
				'section' => 'slideout_color_section',
				'settings' => 'generate_settings[slideout_submenu_text_current_color]',
			)
		)
	);
}
