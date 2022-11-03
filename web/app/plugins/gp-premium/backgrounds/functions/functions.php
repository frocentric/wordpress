<?php
/**
 * The functions for our Backgrounds module.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

require_once plugin_dir_path( __FILE__ ) . 'secondary-nav-backgrounds.php';
require_once plugin_dir_path( __FILE__ ) . 'css.php';

if ( ! function_exists( 'generate_get_background_defaults' ) ) {
	/**
	 * Set default options
	 *
	 * @since 0.1
	 */
	function generate_get_background_defaults() {
		$generate_background_defaults = array(
			'body_image' => '',
			'body_repeat' => '',
			'body_size' => '',
			'body_attachment' => '',
			'body_position' => '',
			'top_bar_image' => '',
			'top_bar_repeat' => '',
			'top_bar_size' => '',
			'top_bar_attachment' => '',
			'top_bar_position' => '',
			'header_image' => '',
			'header_repeat' => '',
			'header_size' => '',
			'header_attachment' => '',
			'header_position' => '',
			'nav_image' => '',
			'nav_repeat' => '',
			'nav_item_image' => '',
			'nav_item_repeat' => '',
			'nav_item_hover_image' => '',
			'nav_item_hover_repeat' => '',
			'nav_item_current_image' => '',
			'nav_item_current_repeat' => '',
			'sub_nav_image' => '',
			'sub_nav_repeat' => '',
			'sub_nav_item_image' => '',
			'sub_nav_item_repeat' => '',
			'sub_nav_item_hover_image' => '',
			'sub_nav_item_hover_repeat' => '',
			'sub_nav_item_current_image' => '',
			'sub_nav_item_current_repeat' => '',
			'content_image' => '',
			'content_repeat' => '',
			'content_size' => '',
			'content_attachment' => '',
			'content_position' => '',
			'sidebar_widget_image' => '',
			'sidebar_widget_repeat' => '',
			'sidebar_widget_size' => '',
			'sidebar_widget_attachment' => '',
			'sidebar_widget_position' => '',
			'footer_widget_image' => '',
			'footer_widget_repeat' => '',
			'footer_widget_size' => '',
			'footer_widget_attachment' => '',
			'footer_widget_position' => '',
			'footer_image' => '',
			'footer_repeat' => '',
			'footer_size' => '',
			'footer_attachment' => '',
			'footer_position' => '',
		);

		return apply_filters( 'generate_background_option_defaults', $generate_background_defaults );
	}
}

if ( ! function_exists( 'generate_backgrounds_customize' ) ) {
	add_action( 'customize_register', 'generate_backgrounds_customize', 999 );
	/**
	 * Build our Customizer options
	 *
	 * @since 0.1
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_backgrounds_customize( $wp_customize ) {
		$defaults = generate_get_background_defaults();

		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Background_Images_Customize_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		if ( class_exists( 'WP_Customize_Panel' ) ) {
			if ( ! $wp_customize->get_panel( 'generate_backgrounds_panel' ) ) {
				$wp_customize->add_panel(
					'generate_backgrounds_panel',
					array(
						'capability'     => 'edit_theme_options',
						'theme_supports' => '',
						'title'          => __( 'Background Images', 'gp-premium' ),
						'priority'       => 55,
					)
				);
			}
		}

		$wp_customize->add_section(
			'backgrounds_section',
			array(
				'title' => __( 'Background Images', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 50,
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_body',
			array(
				'title' => __( 'Body', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 5,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_body_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_body',
					'element' => __( 'Body', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_container',
						'colors' => 'body_section',
						'typography' => 'font_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		/**
		 * Body background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[body_image]',
			array(
				'default' => $defaults['body_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_backgrounds-body-image',
				array(
					'section'    => 'generate_backgrounds_body',
					'settings'   => 'generate_background_settings[body_image]',
					'label' => __( 'Body', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[body_repeat]',
			array(
				'default' => $defaults['body_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[body_size]',
			array(
				'default' => $defaults['body_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[body_attachment]',
			array(
				'default' => $defaults['body_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[body_position]',
			array(
				'default' => $defaults['body_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'body_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_body',
					'settings' => array(
						'repeat' => 'generate_background_settings[body_repeat]',
						'size' => 'generate_background_settings[body_size]',
						'attachment' => 'generate_background_settings[body_attachment]',
						'position' => 'generate_background_settings[body_position]',
					),
				)
			)
		);

		/**
		 * Top bar background
		 */
		$wp_customize->add_section(
			'generate_backgrounds_top_bar',
			array(
				'title' => __( 'Top Bar', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 5,
				'panel' => 'generate_backgrounds_panel',
				'active_callback' => 'generate_premium_is_top_bar_active',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[top_bar_image]',
			array(
				'default' => $defaults['top_bar_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_background_settings[top_bar_image]',
				array(
					'section'    => 'generate_backgrounds_top_bar',
					'settings'   => 'generate_background_settings[top_bar_image]',
					'label' => __( 'Top Bar', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[top_bar_repeat]',
			array(
				'default' => $defaults['top_bar_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[top_bar_size]',
			array(
				'default' => $defaults['top_bar_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[top_bar_attachment]',
			array(
				'default' => $defaults['top_bar_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[top_bar_position]',
			array(
				'default' => $defaults['top_bar_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'top_bar_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_top_bar',
					'settings' => array(
						'repeat' => 'generate_background_settings[top_bar_repeat]',
						'size' => 'generate_background_settings[top_bar_size]',
						'attachment' => 'generate_background_settings[top_bar_attachment]',
						'position' => 'generate_background_settings[top_bar_position]',
					),
				)
			)
		);

		/**
		 * Header background
		 */
		$wp_customize->add_section(
			'generate_backgrounds_header',
			array(
				'title' => __( 'Header', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 10,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_header_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_header',
					'element' => __( 'Header', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_header',
						'colors' => 'header_color_section',
						'typography' => 'font_header_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[header_image]',
			array(
				'default' => $defaults['header_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_backgrounds-header-image',
				array(
					'section' => 'generate_backgrounds_header',
					'settings' => 'generate_background_settings[header_image]',
					'label' => __( 'Header', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[header_repeat]',
			array(
				'default' => $defaults['header_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[header_size]',
			array(
				'default' => $defaults['header_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[header_attachment]',
			array(
				'default' => $defaults['header_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[header_position]',
			array(
				'default' => $defaults['header_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'header_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_header',
					'settings' => array(
						'repeat' => 'generate_background_settings[header_repeat]',
						'size' => 'generate_background_settings[header_size]',
						'attachment' => 'generate_background_settings[header_attachment]',
						'position' => 'generate_background_settings[header_position]',
					),
				)
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_navigation',
			array(
				'title' => __( 'Primary Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 15,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_primary_navigation_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_navigation',
					'element' => __( 'Primary Navigation', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_navigation',
						'colors' => 'navigation_color_section',
						'typography' => 'font_navigation_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		/**
		 * Navigation background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[nav_image]',
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
				'generate_background_settings[nav_image]',
				array(
					'section'    => 'generate_backgrounds_navigation',
					'settings'   => 'generate_background_settings[nav_image]',
					'priority' => 750,
					'label' => __( 'Navigation', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[nav_repeat]',
			array(
				'default' => $defaults['nav_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[nav_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_navigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[nav_repeat]',
				'priority' => 800,
			)
		);

		/**
		 * Navigation item background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[nav_item_image]',
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
				'generate_backgrounds-nav-item-image',
				array(
					'section' => 'generate_backgrounds_navigation',
					'settings' => 'generate_background_settings[nav_item_image]',
					'priority' => 950,
					'label' => __( 'Navigation Item', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[nav_item_repeat]',
			array(
				'default' => $defaults['nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_navigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[nav_item_repeat]',
				'priority' => 1000,
			)
		);

		/**
		 * Navigation item hover background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[nav_item_hover_image]',
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
				'generate_backgrounds-nav-item-hover-image',
				array(
					'section'    => 'generate_backgrounds_navigation',
					'settings'   => 'generate_background_settings[nav_item_hover_image]',
					'priority' => 1150,
					'label' => __( 'Navigation Item Hover', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[nav_item_hover_repeat]',
			array(
				'default' => $defaults['nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_navigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[nav_item_hover_repeat]',
				'priority' => 1200,
			)
		);

		/**
		 * Navigation item current background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[nav_item_current_image]',
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
				'generate_backgrounds-nav-item-current-image',
				array(
					'section'    => 'generate_backgrounds_navigation',
					'settings'   => 'generate_background_settings[nav_item_current_image]',
					'priority' => 1350,
					'label' => __( 'Navigation Item Current', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[nav_item_current_repeat]',
			array(
				'default' => $defaults['nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_navigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[nav_item_current_repeat]',
				'priority' => 1400,
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_subnavigation',
			array(
				'title' => __( 'Primary Sub-Navigation', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 20,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		/**
		 * Sub-Navigation item background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_image]',
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
				'generate_background_settings[sub_nav_item_image]',
				array(
					'section'    => 'generate_backgrounds_subnavigation',
					'settings'   => 'generate_background_settings[sub_nav_item_image]',
					'priority' => 1700,
					'label' => __( 'Sub-Navigation Item', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_repeat]',
			array(
				'default' => $defaults['sub_nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[sub_nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_subnavigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[sub_nav_item_repeat]',
				'priority' => 1800,
			)
		);

		/**
		 * Sub-Navigation item hover background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_hover_image]',
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
				'generate_background_settings[sub_nav_item_hover_image]',
				array(
					'section' => 'generate_backgrounds_subnavigation',
					'settings' => 'generate_background_settings[sub_nav_item_hover_image]',
					'priority' => 2000,
					'label' => __( 'Sub-Navigation Item Hover', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_hover_repeat]',
			array(
				'default' => $defaults['sub_nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[sub_nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_subnavigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[sub_nav_item_hover_repeat]',
				'priority' => 2100,
			)
		);

		/**
		 * Sub-Navigation item current background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_current_image]',
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
				'generate_background_settings[sub_nav_item_current_image]',
				array(
					'section'    => 'generate_backgrounds_subnavigation',
					'settings'   => 'generate_background_settings[sub_nav_item_current_image]',
					'priority' => 2300,
					'label' => __( 'Sub-Navigation Item Current', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sub_nav_item_current_repeat]',
			array(
				'default' => $defaults['sub_nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_control(
			'generate_background_settings[sub_nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'generate_backgrounds_subnavigation',
				'choices' => array(
					'' => __( 'Repeat', 'gp-premium' ),
					'repeat-x' => __( 'Repeat x', 'gp-premium' ),
					'repeat-y' => __( 'Repeat y', 'gp-premium' ),
					'no-repeat' => __( 'No Repeat', 'gp-premium' ),
				),
				'settings' => 'generate_background_settings[sub_nav_item_current_repeat]',
				'priority' => 2400,
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_content',
			array(
				'title' => __( 'Content', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 25,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_content_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_content',
					'element' => __( 'Content', 'gp-premium' ),
					'shortcuts' => array(
						'colors' => 'content_color_section',
						'typography' => 'font_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		/**
		 * Content background
		 */
		$wp_customize->add_setting(
			'generate_background_settings[content_image]',
			array(
				'default' => $defaults['content_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_background_settings[content_image]',
				array(
					'section' => 'generate_backgrounds_content',
					'settings' => 'generate_background_settings[content_image]',
					'label' => __( 'Content', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[content_repeat]',
			array(
				'default' => $defaults['content_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[content_size]',
			array(
				'default' => $defaults['content_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[content_attachment]',
			array(
				'default' => $defaults['content_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[content_position]',
			array(
				'default' => $defaults['content_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'content_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_content',
					'settings' => array(
						'repeat' => 'generate_background_settings[content_repeat]',
						'size' => 'generate_background_settings[content_size]',
						'attachment' => 'generate_background_settings[content_attachment]',
						'position' => 'generate_background_settings[content_position]',
					),
				)
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_sidebars',
			array(
				'title' => __( 'Sidebar', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 25,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_sidebar_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_sidebars',
					'element' => __( 'Sidebar', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_sidebars',
						'colors' => 'sidebar_widget_color_section',
						'typography' => 'font_widget_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sidebar_widget_image]',
			array(
				'default' => $defaults['sidebar_widget_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_background_settings[sidebar_widget_image]',
				array(
					'section'    => 'generate_backgrounds_sidebars',
					'settings'   => 'generate_background_settings[sidebar_widget_image]',
					'label' => __( 'Sidebar Widgets', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sidebar_widget_repeat]',
			array(
				'default' => $defaults['sidebar_widget_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sidebar_widget_size]',
			array(
				'default' => $defaults['sidebar_widget_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sidebar_widget_attachment]',
			array(
				'default' => $defaults['sidebar_widget_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[sidebar_widget_position]',
			array(
				'default' => $defaults['sidebar_widget_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'sidebar_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_sidebars',
					'settings' => array(
						'repeat' => 'generate_background_settings[sidebar_widget_repeat]',
						'size' => 'generate_background_settings[sidebar_widget_size]',
						'attachment' => 'generate_background_settings[sidebar_widget_attachment]',
						'position' => 'generate_background_settings[sidebar_widget_position]',
					),
				)
			)
		);

		$wp_customize->add_section(
			'generate_backgrounds_footer',
			array(
				'title' => __( 'Footer', 'gp-premium' ),
				'capability' => 'edit_theme_options',
				'priority' => 30,
				'panel' => 'generate_backgrounds_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_footer_background_image_shortcuts',
				array(
					'section' => 'generate_backgrounds_footer',
					'element' => __( 'Footer', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_footer',
						'colors' => 'footer_color_section',
						'typography' => 'font_footer_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_widget_image]',
			array(
				'default' => $defaults['footer_widget_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_background_settings[footer_widget_image]',
				array(
					'section'    => 'generate_backgrounds_footer',
					'settings'   => 'generate_background_settings[footer_widget_image]',
					'label' => __( 'Footer Widget Area', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_widget_repeat]',
			array(
				'default' => $defaults['footer_widget_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_widget_size]',
			array(
				'default' => $defaults['footer_widget_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_widget_attachment]',
			array(
				'default' => $defaults['footer_widget_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_widget_position]',
			array(
				'default' => $defaults['footer_widget_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'footer_widgets_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_footer',
					'settings' => array(
						'repeat' => 'generate_background_settings[footer_widget_repeat]',
						'size' => 'generate_background_settings[footer_widget_size]',
						'attachment' => 'generate_background_settings[footer_widget_attachment]',
						'position' => 'generate_background_settings[footer_widget_position]',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_image]',
			array(
				'default' => $defaults['footer_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'generate_backgrounds-footer-image',
				array(
					'section' => 'generate_backgrounds_footer',
					'settings' => 'generate_background_settings[footer_image]',
					'label' => __( 'Footer Area', 'gp-premium' ),
				)
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_repeat]',
			array(
				'default' => $defaults['footer_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_size]',
			array(
				'default' => $defaults['footer_size'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_attachment]',
			array(
				'default' => $defaults['footer_attachment'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
			)
		);

		$wp_customize->add_setting(
			'generate_background_settings[footer_position]',
			array(
				'default' => $defaults['footer_position'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Background_Images_Customize_Control(
				$wp_customize,
				'footer_backgrounds_control',
				array(
					'section' => 'generate_backgrounds_footer',
					'settings' => array(
						'repeat' => 'generate_background_settings[footer_repeat]',
						'size' => 'generate_background_settings[footer_size]',
						'attachment' => 'generate_background_settings[footer_attachment]',
						'position' => 'generate_background_settings[footer_position]',
					),
				)
			)
		);
	}
}

if ( ! function_exists( 'generate_backgrounds_css' ) ) {
	/**
	 * Generate the CSS in the <head> section using the Theme Customizer
	 *
	 * @since 0.1
	 */
	function generate_backgrounds_css() {
		$generate_settings = wp_parse_args(
			get_option( 'generate_background_settings', array() ),
			generate_get_background_defaults()
		);

		// Fix size values.
		// Spaces and % are stripped by sanitize_key.
		$generate_settings['body_size'] = ( '100' == $generate_settings['body_size'] ) ? '100% auto' : esc_attr( $generate_settings['body_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['top_bar_size'] = ( '100' == $generate_settings['top_bar_size'] ) ? '100% auto' : esc_attr( $generate_settings['top_bar_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['header_size'] = ( '100' == $generate_settings['header_size'] ) ? '100% auto' : esc_attr( $generate_settings['header_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['content_size'] = ( '100' == $generate_settings['content_size'] ) ? '100% auto' : esc_attr( $generate_settings['content_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['sidebar_widget_size'] = ( '100' == $generate_settings['sidebar_widget_size'] ) ? '100% auto' : esc_attr( $generate_settings['sidebar_widget_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['footer_widget_size'] = ( '100' == $generate_settings['footer_widget_size'] ) ? '100% auto' : esc_attr( $generate_settings['footer_widget_size'] ); // phpcs:ignore -- Non-strict comparison ok.
		$generate_settings['footer_size'] = ( '100' == $generate_settings['footer_size'] ) ? '100% auto' : esc_attr( $generate_settings['footer_size'] ); // phpcs:ignore -- Non-strict comparison ok.

		$css = new GeneratePress_Backgrounds_CSS();

		$css->set_selector( 'body' );
		$css->add_property( 'background-image', esc_url( $generate_settings['body_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['body_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['body_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['body_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['body_position'] ) );

		if ( is_active_sidebar( 'top-bar' ) ) {
			$css->set_selector( '.top-bar' );
			$css->add_property( 'background-image', esc_url( $generate_settings['top_bar_image'] ), 'url' );
			$css->add_property( 'background-repeat', esc_attr( $generate_settings['top_bar_repeat'] ) );
			$css->add_property( 'background-size', esc_attr( $generate_settings['top_bar_size'] ) );
			$css->add_property( 'background-attachment', esc_attr( $generate_settings['top_bar_attachment'] ) );
			$css->add_property( 'background-position', esc_attr( $generate_settings['top_bar_position'] ) );
		}

		$css->set_selector( '.site-header' );
		$css->add_property( 'background-image', esc_url( $generate_settings['header_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['header_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['header_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['header_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['header_position'] ) );

		$css->set_selector( '.main-navigation, .main-navigation .menu-toggle' );
		$css->add_property( 'background-image', esc_url( $generate_settings['nav_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_repeat'] ) );

		$css->set_selector( '.main-navigation .main-nav > ul > li > a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['nav_item_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_repeat'] ) );

		$css->set_selector( '.main-navigation .main-nav > ul > li > a:hover,.main-navigation .main-nav > ul > li.sfHover > a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['nav_item_hover_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_hover_repeat'] ) );

		$css->set_selector( '.main-navigation .main-nav > ul > li[class*="current-menu-"] > a,.main-navigation .main-nav > ul > li[class*="current-menu-"] > a:hover,.main-navigation .main-nav > ul > li[class*="current-menu-"].sfHover > a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['nav_item_current_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['nav_item_current_repeat'] ) );

		$css->set_selector( '.main-navigation ul ul li a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['sub_nav_item_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_repeat'] ) );

		$css->set_selector( '.main-navigation ul ul li > a:hover,.main-navigation ul ul li.sfHover > a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['sub_nav_item_hover_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_hover_repeat'] ) );

		$css->set_selector( '.main-navigation ul ul li[class*="current-menu-"] > a,.main-navigation ul ul li[class*="current-menu-"] > a:hover,.main-navigation ul ul li[class*="current-menu-"].sfHover > a' );
		$css->add_property( 'background-image', esc_url( $generate_settings['sub_nav_item_current_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['sub_nav_item_current_repeat'] ) );

		$css->set_selector( '.separate-containers .inside-article,.separate-containers .comments-area,.separate-containers .page-header,.one-container .container,.separate-containers .paging-navigation,.separate-containers .inside-page-header' );
		$css->add_property( 'background-image', esc_url( $generate_settings['content_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['content_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['content_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['content_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['content_position'] ) );

		$css->set_selector( '.sidebar .widget' );
		$css->add_property( 'background-image', esc_url( $generate_settings['sidebar_widget_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['sidebar_widget_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['sidebar_widget_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['sidebar_widget_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['sidebar_widget_position'] ) );

		$css->set_selector( '.footer-widgets' );
		$css->add_property( 'background-image', esc_url( $generate_settings['footer_widget_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['footer_widget_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['footer_widget_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['footer_widget_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['footer_widget_position'] ) );

		$css->set_selector( '.site-info' );
		$css->add_property( 'background-image', esc_url( $generate_settings['footer_image'] ), 'url' );
		$css->add_property( 'background-repeat', esc_attr( $generate_settings['footer_repeat'] ) );
		$css->add_property( 'background-size', esc_attr( $generate_settings['footer_size'] ) );
		$css->add_property( 'background-attachment', esc_attr( $generate_settings['footer_attachment'] ) );
		$css->add_property( 'background-position', esc_attr( $generate_settings['footer_position'] ) );

		return apply_filters( 'generate_backgrounds_css_output', $css->css_output() );
	}
}

if ( ! function_exists( 'generate_background_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_background_scripts', 70 );
	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 0.1
	 */
	function generate_background_scripts() {
		if ( 'inline' === generate_get_css_print_method() ) {
			wp_add_inline_style( 'generate-style', generate_backgrounds_css() );
		}
	}
}

add_filter( 'generate_external_dynamic_css_output', 'generate_backgrounds_add_external_css' );
/**
 * Add to external stylesheet.
 *
 * @since 1.11.0
 *
 * @param string $css Existing CSS.
 */
function generate_backgrounds_add_external_css( $css ) {
	if ( 'inline' === generate_get_css_print_method() ) {
		return $css;
	}

	$css .= generate_backgrounds_css();

	return $css;
}
