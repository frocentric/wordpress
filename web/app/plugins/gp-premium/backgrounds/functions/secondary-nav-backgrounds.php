<?php
/**
 * This file handles Secondary Nav background images.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'generate_backgrounds_secondary_nav_customizer' ) ) {
	add_action( 'customize_register', 'generate_backgrounds_secondary_nav_customizer', 1000 );
	/**
	 * Adds our Secondary Nav background image options
	 *
	 * These options are in their own function so we can hook it in late to
	 * make sure Secondary Nav is activated.
	 *
	 * 1000 priority is there to make sure Secondary Nav is registered (999)
	 * as we check to see if the layout control exists.
	 *
	 * Secondary Nav now uses 100 as a priority.
	 *
	 * @param object $wp_customize Our Customizer object.
	 */
	function generate_backgrounds_secondary_nav_customizer( $wp_customize ) {
		if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
			return;
		}

		if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
			return;
		}

		$defaults = generate_secondary_nav_get_defaults();

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		$wp_customize->add_section(
			'secondary_bg_images_section',
			array(
				'title' => __( 'Secondary Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'panel' => 'generate_backgrounds_panel',
				'priority' => 21,
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_secondary_navigation_background_image_shortcuts',
				array(
					'section' => 'secondary_bg_images_section',
					'element' => __( 'Secondary Navigation', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'secondary_nav_section',
						'colors' => 'secondary_navigation_color_section',
						'typography' => 'secondary_font_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_image]',
			array(
				'default' => $defaults['nav_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-nav-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[nav_image]',
					'priority' => 750,
					'label' => __( 'Navigation', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_repeat]',
			array(
				'default' => $defaults['nav_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[nav_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[nav_repeat]',
				'priority' => 800,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_image]',
			array(
				'default' => $defaults['nav_item_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-nav-item-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[nav_item_image]',
					'priority' => 950,
					'label' => __( 'Navigation Item', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_repeat]',
			array(
				'default' => $defaults['nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[nav_item_repeat]',
				'priority' => 1000,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_hover_image]',
			array(
				'default' => $defaults['nav_item_hover_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-nav-item-hover-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[nav_item_hover_image]',
					'priority' => 1150,
					'label' => __( 'Navigation Item Hover', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_hover_repeat]',
			array(
				'default' => $defaults['nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[nav_item_hover_repeat]',
				'priority' => 1200,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_current_image]',
			array(
				'default' => $defaults['nav_item_current_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-nav-item-current-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[nav_item_current_image]',
					'priority' => 1350,
					'label' => __( 'Navigation Item Current', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[nav_item_current_repeat]',
			array(
				'default' => $defaults['nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[nav_item_current_repeat]',
				'priority' => 1400,
			)
		);

		$wp_customize->add_section(
			'secondary_subnav_bg_images_section',
			array(
				'title' => __( 'Secondary Sub-Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'panel' => 'generate_backgrounds_panel',
				'priority' => 22,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_image]',
			array(
				'default' => $defaults['sub_nav_item_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-sub-nav-item-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[sub_nav_item_image]',
					'priority' => 1700,
					'label' => __( 'Sub-Navigation Item', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_repeat]',
			array(
				'default' => $defaults['sub_nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[sub_nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[sub_nav_item_repeat]',
				'priority' => 1800,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_hover_image]',
			array(
				'default' => $defaults['sub_nav_item_hover_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-sub-nav-item-hover-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[sub_nav_item_hover_image]',
					'priority' => 2000,
					'label' => __( 'Sub-Navigation Item Hover', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
			array(
				'default' => $defaults['sub_nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[sub_nav_item_hover_repeat]',
				'priority' => 2100,
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_current_image]',
			array(
				'default' => $defaults['sub_nav_item_current_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_secondary_backgrounds-sub-nav-item-current-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'generate_secondary_nav_settings[sub_nav_item_current_image]',
					'priority' => 2300,
					'label' => __( 'Sub-Navigation Item Current', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
			array(
				'default' => $defaults['sub_nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_secondary_nav_settings[sub_nav_item_current_repeat]',
				'priority' => 2400,
			)
		);
	}
}
