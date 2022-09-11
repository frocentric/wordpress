<?php
/**
 * This file handles the customizer fields for the secondary navigation.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'generate_register_secondary_navigation_colors' ) ) {
	add_action('generate_customize_after_primary_navigation', 'generate_register_secondary_navigation_colors', 1000);

	/**
	 * Register the secondary navigation color fields.
	 */
	function generate_register_secondary_navigation_colors()
	{
		if ( ! class_exists('GeneratePress_Customize_Field') ) {
			return;
		}

		$secondary_color_defaults = generate_secondary_nav_get_defaults();

		$menu_hover_selectors = '.secondary-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .secondary-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .secondary-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a, .secondary-navigation .menu-bar-item:hover > a, .secondary-navigation .menu-bar-item.sfHover > a';
		$menu_current_selectors = '.secondary-navigation .main-nav ul li[class*="current-menu-"] > a';
		$submenu_hover_selectors = '.secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a, .secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a, .secondary-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a';
		$submenu_current_selectors = '.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a';

		GeneratePress_Customize_Field::add_title(
			'generate_secondary_navigation_colors_title',
			array(
				'section' => 'generate_colors_section',
				'title' => __( 'Secondary Navigation', 'gp-premium' ),
				'choices' => array(
					'toggleId' => 'secondary-navigation-colors',
				),
			)
		);

		// Navigation background group.
		GeneratePress_Customize_Field::add_color_field_group(
			'secondary_navigation_background',
			'generate_colors_section',
			'secondary-navigation-colors',
			array(
				'generate_secondary_nav_settings[navigation_background_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_color'],
					'label' => __( 'Navigation Background', 'gp-premium' ),
					'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
					'element' => '.secondary-navigation',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'generate_secondary_nav_settings[navigation_background_hover_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_hover_color'],
					'label' => __( 'Navigation Background Hover', 'gp-premium' ),
					'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
					'element' => $menu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'generate_secondary_nav_settings[navigation_background_current_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_current_color'],
					'label' => __( 'Navigation Background Current', 'gp-premium' ),
					'tooltip' => __( 'Choose Current Color', 'gp-premium' ),
					'element' => $menu_current_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
			)
		);

		// Navigation text group.
		GeneratePress_Customize_Field::add_color_field_group(
			'secondary_navigation_text',
			'generate_colors_section',
			'secondary-navigation-colors',
			array(
				'generate_secondary_nav_settings[navigation_text_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_color'],
					'label' => __( 'Navigation Text', 'gp-premium' ),
					'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
					'element' => '.secondary-navigation .main-nav ul li a, .secondary-navigation .menu-toggle, .secondary-navigation button.menu-toggle:hover, .secondary-navigation button.menu-toggle:focus, .secondary-navigation .mobile-bar-items a, .secondary-navigation .mobile-bar-items a:hover, .secondary-navigation .mobile-bar-items a:focus, .secondary-navigation .menu-bar-items',
					'property' => 'color',
					'hide_label' => false,
				),
				'generate_secondary_nav_settings[navigation_text_hover_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_hover_color'],
					'label' => __( 'Navigation Text Hover', 'gp-premium' ),
					'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
					'element' => $menu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'generate_secondary_nav_settings[navigation_text_current_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_current_color'],
					'label' => __( 'Navigation Text Current', 'gp-premium' ),
					'tooltip' => __( 'Choose Current Color', 'gp-premium' ),
					'element' => $menu_current_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
			)
		);

		// Sub-Menu background group.
		GeneratePress_Customize_Field::add_color_field_group(
			'secondary_navigation_submenu_background',
			'generate_colors_section',
			'secondary-navigation-colors',
			array(
				'generate_secondary_nav_settings[subnavigation_background_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_color'],
					'label' => __( 'Sub-Menu Background', 'gp-premium' ),
					'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
					'element' => '.secondary-navigation ul ul',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'generate_secondary_nav_settings[subnavigation_background_hover_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_hover_color'],
					'label' => __( 'Sub-Menu Background Hover', 'gp-premium' ),
					'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
					'element' => $submenu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'generate_secondary_nav_settings[subnavigation_background_current_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_current_color'],
					'label' => __( 'Sub-Menu Background Current', 'gp-premium' ),
					'tooltip' => __( 'Choose Current Color', 'gp-premium' ),
					'element' => $submenu_current_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
			)
		);

		// Sub-Menu text group.
		GeneratePress_Customize_Field::add_color_field_group(
			'secondary_navigation_submenu_text',
			'generate_colors_section',
			'secondary-navigation-colors',
			array(
				'generate_secondary_nav_settings[subnavigation_text_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_color'],
					'label' => __( 'Sub-Menu Text', 'gp-premium' ),
					'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
					'element' => '.secondary-navigation .main-nav ul ul li a',
					'property' => 'color',
					'hide_label' => false,
				),
				'generate_secondary_nav_settings[subnavigation_text_hover_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_hover_color'],
					'label' => __( 'Sub-Menu Text Hover', 'gp-premium' ),
					'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
					'element' => $submenu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'generate_secondary_nav_settings[subnavigation_text_current_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_current_color'],
					'label' => __( 'Sub-Menu Text Current', 'gp-premium' ),
					'tooltip' => __( 'Choose Current Color', 'gp-premium' ),
					'element' => $submenu_current_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
			)
		);
	}
}
