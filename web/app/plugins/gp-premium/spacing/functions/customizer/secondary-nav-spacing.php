<?php
/**
 * This file handles the secondary navigation spacing Customizer options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'generate_spacing_secondary_nav_customizer' ) ) {
	add_action( 'customize_register', 'generate_spacing_secondary_nav_customizer', 1000 );
	/**
	 * Adds our Secondary Nav spacing options
	 *
	 * These options are in their own function so we can hook it in late to
	 * make sure Secondary Nav is activated.
	 *
	 * 1000 priority is there to make sure Secondary Nav is registered (999)
	 * as we check to see if the layout control exists.
	 *
	 * Secondary Nav now uses 100 as a priority.
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_spacing_secondary_nav_customizer( $wp_customize ) {

		// Bail if we don't have our defaults.
		if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
			return;
		}

		// Make sure Secondary Nav is activated.
		if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
			return;
		}

		// Get our controls.
		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Get our defaults.
		$defaults = generate_secondary_nav_get_defaults();

		// Remove our old label control if it exists.
		// It only would if the user is using an old Secondary Nav add-on version.
		if ( $wp_customize->get_control( 'generate_secondary_navigation_spacing_title' ) ) {
			$wp_customize->remove_control( 'generate_secondary_navigation_spacing_title' );
		}

		// Menu item width.
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_menu_item]',
			array(
				'default' => $defaults['secondary_menu_item'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_secondary_nav_settings[secondary_menu_item]',
				array(
					'label' => __( 'Menu Item Width', 'gp-premium' ),
					'section' => 'secondary_nav_section',
					'settings' => array(
						'desktop' => 'generate_secondary_nav_settings[secondary_menu_item]',
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
					'priority' => 220,
				)
			)
		);

		// Menu item height.
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_menu_item_height]',
			array(
				'default' => $defaults['secondary_menu_item_height'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_secondary_nav_settings[secondary_menu_item_height]',
				array(
					'label' => __( 'Menu Item Height', 'gp-premium' ),
					'section' => 'secondary_nav_section',
					'settings' => array(
						'desktop' => 'generate_secondary_nav_settings[secondary_menu_item_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 20,
							'max' => 150,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
					'priority' => 240,
				)
			)
		);

		// Sub-menu height.
		$wp_customize->add_setting(
			'generate_secondary_nav_settings[secondary_sub_menu_item_height]',
			array(
				'default' => $defaults['secondary_sub_menu_item_height'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_secondary_nav_settings[secondary_sub_menu_item_height]',
				array(
					'label' => __( 'Sub-Menu Item Height', 'gp-premium' ),
					'section' => 'secondary_nav_section',
					'settings' => array(
						'desktop' => 'generate_secondary_nav_settings[secondary_sub_menu_item_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 50,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
					'priority' => 260,
				)
			)
		);
	}
}
