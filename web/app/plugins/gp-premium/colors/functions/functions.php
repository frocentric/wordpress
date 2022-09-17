<?php
/**
 * This file handles most of our Colors functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add necessary files.
require_once trailingslashit( dirname( __FILE__ ) ) . 'secondary-nav-colors.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'woocommerce-colors.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'slideout-nav-colors.php';

if ( ! function_exists( 'generate_colors_customize_register' ) ) {
	add_action( 'customize_register', 'generate_colors_customize_register', 5 );
	/**
	 * Add our Customizer options.
	 *
	 * @since 0.1
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_colors_customize_register( $wp_customize ) {
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
			$wp_customize->register_control_type( 'GeneratePress_Title_Customize_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		// Get our palettes.
		$palettes = generate_get_default_color_palettes();

		// Add our Colors panel.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			$wp_customize->add_panel(
				'generate_colors_panel',
				array(
					'priority'       => 30,
					'theme_supports' => '',
					'title'          => __( 'Colors', 'gp-premium' ),
					'description'    => '',
				)
			);
		}

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_body_color_shortcuts',
				array(
					'section' => 'body_section',
					'element' => __( 'Body', 'gp-premium' ),
					'shortcuts' => array(
						'typography' => 'font_section',
						'backgrounds' => 'generate_backgrounds_body',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Add Top Bar Colors section.
		if ( isset( $defaults['top_bar_background_color'] ) && function_exists( 'generate_is_top_bar_active' ) ) {
			$wp_customize->add_section(
				'generate_top_bar_colors',
				array(
					'title' => __( 'Top Bar', 'gp-premium' ),
					'priority' => 40,
					'panel' => 'generate_colors_panel',
				)
			);

			$wp_customize->add_setting(
				'generate_settings[top_bar_background_color]',
				array(
					'default'     => $defaults['top_bar_background_color'],
					'type'        => 'option',
					'transport'   => 'postMessage',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Alpha_Color_Customize_Control(
					$wp_customize,
					'generate_settings[top_bar_background_color]',
					array(
						'label'     => __( 'Background', 'gp-premium' ),
						'section'   => 'generate_top_bar_colors',
						'settings'  => 'generate_settings[top_bar_background_color]',
						'palette'   => $palettes,
						'show_opacity'  => true,
						'priority' => 1,
						'active_callback' => 'generate_is_top_bar_active',
					)
				)
			);

			// Add color settings.
			$top_bar_colors = array();
			$top_bar_colors[] = array(
				'slug' => 'top_bar_text_color',
				'default' => $defaults['top_bar_text_color'],
				'label' => __( 'Text', 'gp-premium' ),
				'priority' => 2,
			);
			$top_bar_colors[] = array(
				'slug' => 'top_bar_link_color',
				'default' => $defaults['top_bar_link_color'],
				'label' => __( 'Link', 'gp-premium' ),
				'priority' => 3,
			);
			$top_bar_colors[] = array(
				'slug' => 'top_bar_link_color_hover',
				'default' => $defaults['top_bar_link_color_hover'],
				'label' => __( 'Link Hover', 'gp-premium' ),
				'priority' => 4,
			);

			foreach ( $top_bar_colors as $color ) {
				$wp_customize->add_setting(
					'generate_settings[' . $color['slug'] . ']',
					array(
						'default' => $color['default'],
						'type' => 'option',
						'sanitize_callback' => 'generate_premium_sanitize_hex_color',
						'transport' => 'postMessage',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$color['slug'],
						array(
							'label' => $color['label'],
							'section' => 'generate_top_bar_colors',
							'settings' => 'generate_settings[' . $color['slug'] . ']',
							'priority' => $color['priority'],
							'palette'   => $palettes,
							'active_callback' => 'generate_is_top_bar_active',
						)
					)
				);
			}
		}

		// Add Header Colors section.
		$wp_customize->add_section(
			'header_color_section',
			array(
				'title' => __( 'Header', 'gp-premium' ),
				'priority' => 50,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_header_color_shortcuts',
				array(
					'section' => 'header_color_section',
					'element' => __( 'Header', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_header',
						'typography' => 'font_header_section',
						'backgrounds' => 'generate_backgrounds_header',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[header_background_color]',
			array(
				'default'     => $defaults['header_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[header_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'header_color_section',
					'settings'  => 'generate_settings[header_background_color]',
					'palette'   => $palettes,
					'show_opacity'  => true,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$header_colors = array();
		$header_colors[] = array(
			'slug' => 'header_text_color',
			'default' => $defaults['header_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
			'priority' => 2,
		);
		$header_colors[] = array(
			'slug' => 'header_link_color',
			'default' => $defaults['header_link_color'],
			'label' => __( 'Link', 'gp-premium' ),
			'priority' => 3,
		);
		$header_colors[] = array(
			'slug' => 'header_link_hover_color',
			'default' => $defaults['header_link_hover_color'],
			'label' => __( 'Link Hover', 'gp-premium' ),
			'priority' => 4,
		);
		$header_colors[] = array(
			'slug' => 'site_title_color',
			'default' => $defaults['site_title_color'],
			'label' => __( 'Site Title', 'gp-premium' ),
			'priority' => 5,
		);
		$header_colors[] = array(
			'slug' => 'site_tagline_color',
			'default' => $defaults['site_tagline_color'],
			'label' => __( 'Tagline', 'gp-premium' ),
			'priority' => 6,
		);

		foreach ( $header_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'header_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
						'palette'   => $palettes,
					)
				)
			);
		}

		// Add Navigation section.
		$wp_customize->add_section(
			'navigation_color_section',
			array(
				'title' => __( 'Primary Navigation', 'gp-premium' ),
				'priority' => 60,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_primary_navigation_color_shortcuts',
				array(
					'section' => 'navigation_color_section',
					'element' => __( 'Primary Navigation', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_navigation',
						'typography' => 'font_navigation_section',
						'backgrounds' => 'generate_backgrounds_navigation',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_primary_navigation_parent_items',
				array(
					'section'  => 'navigation_color_section',
					'type'     => 'generatepress-customizer-title',
					'title'    => __( 'Parent Items', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_background_color]',
			array(
				'default'     => $defaults['navigation_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[navigation_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[navigation_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_background_hover_color]',
			array(
				'default'     => $defaults['navigation_background_hover_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[navigation_background_hover_color]',
				array(
					'label'     => __( 'Background Hover', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[navigation_background_hover_color]',
					'palette'   => $palettes,
					'priority' => 3,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[navigation_background_current_color]',
			array(
				'default'     => $defaults['navigation_background_current_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[navigation_background_current_color]',
				array(
					'label'     => __( 'Background Current', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[navigation_background_current_color]',
					'palette'   => $palettes,
					'priority' => 5,
				)
			)
		);

		// Add color settings.
		$navigation_colors = array();
		$navigation_colors[] = array(
			'slug' => 'navigation_text_color',
			'default' => $defaults['navigation_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
			'priority' => 2,
		);
		$navigation_colors[] = array(
			'slug' => 'navigation_text_hover_color',
			'default' => $defaults['navigation_text_hover_color'],
			'label' => __( 'Text Hover', 'gp-premium' ),
			'priority' => 4,
		);
		$navigation_colors[] = array(
			'slug' => 'navigation_text_current_color',
			'default' => $defaults['navigation_text_current_color'],
			'label' => __( 'Text Current', 'gp-premium' ),
			'priority' => 6,
		);

		foreach ( $navigation_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'navigation_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_primary_navigation_sub_menu_items',
				array(
					'section'  => 'navigation_color_section',
					'type'     => 'generatepress-customizer-title',
					'title'    => __( 'Sub-Menu Items', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 7,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[subnavigation_background_color]',
			array(
				'default'     => $defaults['subnavigation_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[subnavigation_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[subnavigation_background_color]',
					'palette'   => $palettes,
					'priority' => 8,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[subnavigation_background_hover_color]',
			array(
				'default'     => $defaults['subnavigation_background_hover_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[subnavigation_background_hover_color]',
				array(
					'label'     => __( 'Background Hover', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[subnavigation_background_hover_color]',
					'palette'   => $palettes,
					'priority' => 10,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[subnavigation_background_current_color]',
			array(
				'default'     => $defaults['subnavigation_background_current_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[subnavigation_background_current_color]',
				array(
					'label'     => __( 'Background Current', 'gp-premium' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'generate_settings[subnavigation_background_current_color]',
					'palette'   => $palettes,
					'priority' => 12,
				)
			)
		);

		// Add color settings.
		$subnavigation_colors = array();
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_color',
			'default' => $defaults['subnavigation_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
			'priority' => 9,
		);
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_hover_color',
			'default' => $defaults['subnavigation_text_hover_color'],
			'label' => __( 'Text Hover', 'gp-premium' ),
			'priority' => 11,
		);
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_current_color',
			'default' => $defaults['subnavigation_text_current_color'],
			'label' => __( 'Text Current', 'gp-premium' ),
			'priority' => 13,
		);

		foreach ( $subnavigation_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'navigation_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		if ( isset( $defaults['navigation_search_background_color'] ) ) {
			$wp_customize->add_control(
				new GeneratePress_Title_Customize_Control(
					$wp_customize,
					'generate_primary_navigation_search',
					array(
						'section'  => 'navigation_color_section',
						'type'     => 'generatepress-customizer-title',
						'title'    => __( 'Navigation Search', 'gp-premium' ),
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
						'priority' => 15,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[navigation_search_background_color]',
				array(
					'default'     => $defaults['navigation_search_background_color'],
					'type'        => 'option',
					'transport'   => 'postMessage',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Alpha_Color_Customize_Control(
					$wp_customize,
					'generate_settings[navigation_search_background_color]',
					array(
						'label'     => __( 'Background', 'gp-premium' ),
						'section'   => 'navigation_color_section',
						'settings'  => 'generate_settings[navigation_search_background_color]',
						'palette'   => $palettes,
						'priority' => 16,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[navigation_search_text_color]',
				array(
					'default' => $defaults['navigation_search_text_color'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'generate_settings[navigation_search_text_color]',
					array(
						'label' => __( 'Text', 'gp-premium' ),
						'section' => 'navigation_color_section',
						'settings' => 'generate_settings[navigation_search_text_color]',
						'priority' => 17,
					)
				)
			);
		}

		$wp_customize->add_section(
			'buttons_color_section',
			array(
				'title' => __( 'Buttons', 'gp-premium' ),
				'priority' => 75,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_buttons_color_shortcuts',
				array(
					'section' => 'buttons_color_section',
					'element' => __( 'Button', 'gp-premium' ),
					'shortcuts' => array(
						'typography' => 'font_buttons_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_button_background_color]',
			array(
				'default'     => $defaults['form_button_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_button_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'buttons_color_section',
					'settings'  => 'generate_settings[form_button_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_button_text_color]',
			array(
				'default' => $defaults['form_button_text_color'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'form_button_text_color',
				array(
					'label' => __( 'Text', 'gp-premium' ),
					'section' => 'buttons_color_section',
					'settings' => 'generate_settings[form_button_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_button_background_color_hover]',
			array(
				'default'     => $defaults['form_button_background_color_hover'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_button_background_color_hover]',
				array(
					'label'     => __( 'Background Hover', 'gp-premium' ),
					'section'   => 'buttons_color_section',
					'settings'  => 'generate_settings[form_button_background_color_hover]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_button_text_color_hover]',
			array(
				'default' => $defaults['form_button_text_color_hover'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'form_button_text_color_hover',
				array(
					'label' => __( 'Text Hover', 'gp-premium' ),
					'section' => 'buttons_color_section',
					'settings' => 'generate_settings[form_button_text_color_hover]',
				)
			)
		);

		// Add Content Colors section.
		$wp_customize->add_section(
			'content_color_section',
			array(
				'title' => __( 'Content', 'gp-premium' ),
				'priority' => 80,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_content_color_shortcuts',
				array(
					'section' => 'content_color_section',
					'element' => __( 'Content', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_container',
						'typography' => 'font_content_section',
						'backgrounds' => 'generate_backgrounds_content',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[content_background_color]',
			array(
				'default'     => $defaults['content_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[content_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'content_color_section',
					'settings'  => 'generate_settings[content_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$content_colors = array();
		$content_colors[] = array(
			'slug' => 'content_text_color',
			'default' => $defaults['content_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
			'priority' => 2,
		);
		$content_colors[] = array(
			'slug' => 'content_link_color',
			'default' => $defaults['content_link_color'],
			'label' => __( 'Link', 'gp-premium' ),
			'priority' => 3,
		);
		$content_colors[] = array(
			'slug' => 'content_link_hover_color',
			'default' => $defaults['content_link_hover_color'],
			'label' => __( 'Link Hover', 'gp-premium' ),
			'priority' => 4,
		);
		$content_colors[] = array(
			'slug' => 'content_title_color',
			'default' => $defaults['content_title_color'],
			'label' => __( 'Content Title', 'gp-premium' ),
			'priority' => 5,
		);
		$content_colors[] = array(
			'slug' => 'blog_post_title_color',
			'default' => $defaults['blog_post_title_color'],
			'label' => __( 'Archive Content Title', 'gp-premium' ),
			'priority' => 6,
		);
		$content_colors[] = array(
			'slug' => 'blog_post_title_hover_color',
			'default' => $defaults['blog_post_title_hover_color'],
			'label' => __( 'Archive Content Title Hover', 'gp-premium' ),
			'priority' => 7,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_text_color',
			'default' => $defaults['entry_meta_text_color'],
			'label' => __( 'Entry Meta Text', 'gp-premium' ),
			'priority' => 8,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_link_color',
			'default' => $defaults['entry_meta_link_color'],
			'label' => __( 'Entry Meta Links', 'gp-premium' ),
			'priority' => 9,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_link_color_hover',
			'default' => $defaults['entry_meta_link_color_hover'],
			'label' => __( 'Entry Meta Links Hover', 'gp-premium' ),
			'priority' => 10,
		);
		$content_colors[] = array(
			'slug' => 'h1_color',
			'default' => $defaults['h1_color'],
			'label' => __( 'Heading 1 (H1) Color', 'gp-premium' ),
			'priority' => 11,
		);
		$content_colors[] = array(
			'slug' => 'h2_color',
			'default' => $defaults['h2_color'],
			'label' => __( 'Heading 2 (H2) Color', 'gp-premium' ),
			'priority' => 12,
		);
		$content_colors[] = array(
			'slug' => 'h3_color',
			'default' => $defaults['h3_color'],
			'label' => __( 'Heading 3 (H3) Color', 'gp-premium' ),
			'priority' => 13,
		);

		if ( isset( $defaults['h4_color'] ) ) {
			$content_colors[] = array(
				'slug' => 'h4_color',
				'default' => $defaults['h4_color'],
				'label' => __( 'Heading 4 (H4) Color', 'gp-premium' ),
				'priority' => 14,
			);
		}

		if ( isset( $defaults['h5_color'] ) ) {
			$content_colors[] = array(
				'slug' => 'h5_color',
				'default' => $defaults['h5_color'],
				'label' => __( 'Heading 5 (H5) Color', 'gp-premium' ),
				'priority' => 15,
			);
		}

		foreach ( $content_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'content_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Sidebar Widget colors.
		$wp_customize->add_section(
			'sidebar_widget_color_section',
			array(
				'title' => __( 'Sidebar Widgets', 'gp-premium' ),
				'priority' => 90,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_sidebar_color_shortcuts',
				array(
					'section' => 'sidebar_widget_color_section',
					'element' => __( 'Sidebar', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_sidebars',
						'typography' => 'font_widget_section',
						'backgrounds' => 'generate_backgrounds_sidebars',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[sidebar_widget_background_color]',
			array(
				'default'     => $defaults['sidebar_widget_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[sidebar_widget_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'sidebar_widget_color_section',
					'settings'  => 'generate_settings[sidebar_widget_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$sidebar_widget_colors = array();
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_text_color',
			'default' => $defaults['sidebar_widget_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
			'priority' => 2,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_link_color',
			'default' => $defaults['sidebar_widget_link_color'],
			'label' => __( 'Link', 'gp-premium' ),
			'priority' => 3,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_link_hover_color',
			'default' => $defaults['sidebar_widget_link_hover_color'],
			'label' => __( 'Link Hover', 'gp-premium' ),
			'priority' => 4,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_title_color',
			'default' => $defaults['sidebar_widget_title_color'],
			'label' => __( 'Widget Title', 'gp-premium' ),
			'priority' => 5,
		);

		foreach ( $sidebar_widget_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'sidebar_widget_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Form colors.
		$wp_customize->add_section(
			'form_color_section',
			array(
				'title' => __( 'Forms', 'gp-premium' ),
				'priority' => 130,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_background_color]',
			array(
				'default'     => $defaults['form_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_background_color]',
				array(
					'label'     => __( 'Form Background', 'gp-premium' ),
					'section'   => 'form_color_section',
					'settings'  => 'generate_settings[form_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_background_color_focus]',
			array(
				'default'     => $defaults['form_background_color_focus'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_background_color_focus]',
				array(
					'label'     => __( 'Form Background Focus', 'gp-premium' ),
					'section'   => 'form_color_section',
					'settings'  => 'generate_settings[form_background_color_focus]',
					'palette'   => $palettes,
					'priority' => 3,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_border_color]',
			array(
				'default'     => $defaults['form_border_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_border_color]',
				array(
					'label'     => __( 'Form Border', 'gp-premium' ),
					'section'   => 'form_color_section',
					'settings'  => 'generate_settings[form_border_color]',
					'palette'   => $palettes,
					'priority' => 5,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[form_border_color_focus]',
			array(
				'default'     => $defaults['form_border_color_focus'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[form_border_color_focus]',
				array(
					'label'     => __( 'Form Border Focus', 'gp-premium' ),
					'section'   => 'form_color_section',
					'settings'  => 'generate_settings[form_border_color_focus]',
					'palette'   => $palettes,
					'priority' => 6,
				)
			)
		);

		// Add color settings.
		$form_colors = array();
		$form_colors[] = array(
			'slug' => 'form_text_color',
			'default' => $defaults['form_text_color'],
			'label' => __( 'Form Text', 'gp-premium' ),
			'priority' => 2,
		);
		$form_colors[] = array(
			'slug' => 'form_text_color_focus',
			'default' => $defaults['form_text_color_focus'],
			'label' => __( 'Form Text Focus', 'gp-premium' ),
			'priority' => 4,
		);

		foreach ( $form_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'form_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Footer colors.
		$wp_customize->add_section(
			'footer_color_section',
			array(
				'title' => __( 'Footer', 'gp-premium' ),
				'priority' => 150,
				'panel' => 'generate_colors_panel',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_footer_color_shortcuts',
				array(
					'section' => 'footer_color_section',
					'element' => __( 'Footer', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_layout_footer',
						'typography' => 'font_footer_section',
						'backgrounds' => 'generate_backgrounds_footer',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_footer_widgets_title',
				array(
					'section' => 'footer_color_section',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Footer Widgets', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[footer_widget_background_color]',
			array(
				'default'     => $defaults['footer_widget_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[footer_widget_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'footer_color_section',
					'settings'  => 'generate_settings[footer_widget_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Add color settings.
		$footer_widget_colors = array();
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_text_color',
			'default' => $defaults['footer_widget_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_link_color',
			'default' => $defaults['footer_widget_link_color'],
			'label' => __( 'Link', 'gp-premium' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_link_hover_color',
			'default' => $defaults['footer_widget_link_hover_color'],
			'label' => __( 'Link Hover', 'gp-premium' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_title_color',
			'default' => $defaults['footer_widget_title_color'],
			'label' => __( 'Widget Title', 'gp-premium' ),
		);

		foreach ( $footer_widget_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'footer_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
					)
				)
			);
		}

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_footer_title',
				array(
					'section' => 'footer_color_section',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Footer Bar', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[footer_background_color]',
			array(
				'default'     => $defaults['footer_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[footer_background_color]',
				array(
					'label'     => __( 'Background', 'gp-premium' ),
					'section'   => 'footer_color_section',
					'settings'  => 'generate_settings[footer_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Add color settings.
		$footer_colors = array();
		$footer_colors[] = array(
			'slug' => 'footer_text_color',
			'default' => $defaults['footer_text_color'],
			'label' => __( 'Text', 'gp-premium' ),
		);
		$footer_colors[] = array(
			'slug' => 'footer_link_color',
			'default' => $defaults['footer_link_color'],
			'label' => __( 'Link', 'gp-premium' ),
		);
		$footer_colors[] = array(
			'slug' => 'footer_link_hover_color',
			'default' => $defaults['footer_link_hover_color'],
			'label' => __( 'Link Hover', 'gp-premium' ),
		);

		foreach ( $footer_colors as $color ) {
			$wp_customize->add_setting(
				'generate_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'footer_color_section',
						'settings' => 'generate_settings[' . $color['slug'] . ']',
					)
				)
			);
		}

		if ( isset( $defaults['back_to_top_background_color'] ) ) {
			$wp_customize->add_control(
				new GeneratePress_Title_Customize_Control(
					$wp_customize,
					'generate_back_to_top_title',
					array(
						'section' => 'footer_color_section',
						'type' => 'generatepress-customizer-title',
						'title' => __( 'Back to Top Button', 'gp-premium' ),
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[back_to_top_background_color]',
				array(
					'default' => $defaults['back_to_top_background_color'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Alpha_Color_Customize_Control(
					$wp_customize,
					'generate_settings[back_to_top_background_color]',
					array(
						'label' => __( 'Background', 'gp-premium' ),
						'section' => 'footer_color_section',
						'settings' => 'generate_settings[back_to_top_background_color]',
						'palette' => $palettes,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[back_to_top_text_color]',
				array(
					'default' => $defaults['back_to_top_text_color'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'generate_settings[back_to_top_text_color]',
					array(
						'label' => __( 'Text', 'gp-premium' ),
						'section' => 'footer_color_section',
						'settings' => 'generate_settings[back_to_top_text_color]',
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[back_to_top_background_color_hover]',
				array(
					'default' => $defaults['back_to_top_background_color_hover'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Alpha_Color_Customize_Control(
					$wp_customize,
					'generate_settings[back_to_top_background_color_hover]',
					array(
						'label'     => __( 'Background Hover', 'gp-premium' ),
						'section'   => 'footer_color_section',
						'settings'  => 'generate_settings[back_to_top_background_color_hover]',
						'palette'   => $palettes,
					)
				)
			);

			$wp_customize->add_setting(
				'generate_settings[back_to_top_text_color_hover]',
				array(
					'default' => $defaults['back_to_top_text_color_hover'],
					'type' => 'option',
					'sanitize_callback' => 'generate_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'generate_settings[back_to_top_text_color_hover]',
					array(
						'label' => __( 'Text Hover', 'gp-premium' ),
						'section' => 'footer_color_section',
						'settings' => 'generate_settings[back_to_top_text_color_hover]',
					)
				)
			);
		}
	}
}

if ( ! function_exists( 'generate_get_color_setting' ) ) {
	/**
	 * Wrapper function to get our settings
	 *
	 * @since 1.3.42
	 * @param string $setting The setting to check.
	 */
	function generate_get_color_setting( $setting ) {

		// Bail if we don't have our color defaults.
		if ( ! function_exists( 'generate_get_color_defaults' ) ) {
			return;
		}

		if ( function_exists( 'generate_get_defaults' ) ) {
			$defaults = array_merge( generate_get_defaults(), generate_get_color_defaults() );
		} else {
			$defaults = generate_get_color_defaults();
		}

		$generate_settings = wp_parse_args(
			get_option( 'generate_settings', array() ),
			$defaults
		);

		return $generate_settings[ $setting ];
	}
}

if ( ! function_exists( 'generate_colors_rgba_to_hex' ) ) {
	/**
	 * Convert RGBA to hex if necessary
	 *
	 * @since 1.3.42
	 * @param string $rgba The string to convert to hex.
	 */
	function generate_colors_rgba_to_hex( $rgba ) {
		// If it's not rgba, return it.
		if ( false === strpos( $rgba, 'rgba' ) ) {
			return $rgba;
		}

		return substr( $rgba, 0, strrpos( $rgba, ',' ) ) . ')';
	}
}

if ( ! function_exists( 'generate_get_default_color_palettes' ) ) {
	/**
	 * Set up our colors for the color picker palettes and filter them so you can change them
	 *
	 * @since 1.3.42
	 */
	function generate_get_default_color_palettes() {
		$palettes = array(
			generate_colors_rgba_to_hex( generate_get_color_setting( 'link_color' ) ),
			generate_colors_rgba_to_hex( generate_get_color_setting( 'background_color' ) ),
			generate_colors_rgba_to_hex( generate_get_color_setting( 'navigation_background_color' ) ),
			generate_colors_rgba_to_hex( generate_get_color_setting( 'navigation_background_hover_color' ) ),
			'#F1C40F',
			'#1e72bd',
			'#1ABC9C',
			'#3498DB',
		);

		return apply_filters( 'generate_default_color_palettes', $palettes );
	}
}

if ( ! function_exists( 'generate_enqueue_color_palettes' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'generate_enqueue_color_palettes', 1001 );
	/**
	 * Add our custom color palettes to the color pickers in the Customizer.
	 * Hooks into 1001 priority to show up after Secondary Nav.
	 *
	 * @since 1.3.42
	 */
	function generate_enqueue_color_palettes() {
		// Old versions of WP don't get nice things.
		if ( ! function_exists( 'wp_add_inline_script' ) ) {
			return;
		}

		// Grab our palette array and turn it into JS.
		$palettes = wp_json_encode( generate_get_default_color_palettes() );

		// Add our custom palettes.
		// json_encode takes care of escaping.
		wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
	}
}

if ( ! function_exists( 'generate_colors_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'generate_colors_customizer_live_preview' );
	/**
	 * Add our live preview javascript.
	 *
	 * @since 0.1
	 */
	function generate_colors_customizer_live_preview() {
		wp_enqueue_script(
			'generate-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
			array( 'jquery', 'customize-preview' ),
			GENERATE_COLORS_VERSION,
			true
		);

		wp_register_script(
			'generate-wc-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/wc-customizer.js',
			array( 'jquery', 'customize-preview', 'generate-colors-customizer' ),
			GENERATE_COLORS_VERSION,
			true
		);

		wp_register_script(
			'generate-menu-plus-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/menu-plus-customizer.js',
			array( 'jquery', 'customize-preview', 'generate-colors-customizer' ),
			GENERATE_COLORS_VERSION,
			true
		);
	}
}
