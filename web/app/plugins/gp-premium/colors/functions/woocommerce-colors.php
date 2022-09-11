<?php
/**
 * This file handles the Customizer options for the WooCommerce module.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'generate_colors_wc_customizer' ) ) {
	add_action( 'customize_register', 'generate_colors_wc_customizer', 100 );
	/**
	 * Adds our WooCommerce color options
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_colors_wc_customizer( $wp_customize ) {
		// Bail if WooCommerce isn't activated.
		if ( ! $wp_customize->get_section( 'generate_woocommerce_colors' ) ) {
			return;
		}

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
			$wp_customize->register_control_type( 'GeneratePress_Information_Customize_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
		}

		// Get our palettes.
		$palettes = generate_get_default_color_palettes();

		$wp_customize->add_control(
			new GeneratePress_Section_Shortcut_Control(
				$wp_customize,
				'generate_woocommerce_color_shortcuts',
				array(
					'section' => 'generate_woocommerce_colors',
					'element' => __( 'WooCommerce', 'gp-premium' ),
					'shortcuts' => array(
						'layout' => 'generate_woocommerce_layout',
						'typography' => 'generate_woocommerce_typography',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_button_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Buttons', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Information_Customize_Control(
				$wp_customize,
				'generate_woocommerce_primary_button_message',
				array(
					'section' => 'generate_woocommerce_colors',
					'label' => __( 'Primary Button Colors', 'gp-premium' ),
					'description' => __( 'Primary button colors can be set <a href="#">here</a>.', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_alt_button_background]',
			array(
				'default'     => $defaults['wc_alt_button_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_alt_button_background]',
				array(
					'label'     => __( 'Alt Button Background', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_alt_button_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_alt_button_background_hover]',
			array(
				'default'     => $defaults['wc_alt_button_background_hover'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_alt_button_background_hover]',
				array(
					'label'     => __( 'Alt Button Background Hover', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_alt_button_background_hover]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_alt_button_text]',
			array(
				'default' => $defaults['wc_alt_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_alt_button_text]',
				array(
					'label' => __( 'Alt Button Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_alt_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_alt_button_text_hover]',
			array(
				'default' => $defaults['wc_alt_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_alt_button_text_hover]',
				array(
					'label' => __( 'Alt Button Text Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_alt_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_product_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Products', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_product_title_color]',
			array(
				'default' => $defaults['wc_product_title_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_product_title_color]',
				array(
					'label' => __( 'Product Title', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_product_title_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_product_title_color_hover]',
			array(
				'default' => $defaults['wc_product_title_color_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_product_title_color_hover]',
				array(
					'label' => __( 'Product Title Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_product_title_color_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_rating_stars]',
			array(
				'default'     => $defaults['wc_rating_stars'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => '',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_rating_stars]',
				array(
					'label'     => __( 'Star Ratings', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_rating_stars]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_sale_sticker_background]',
			array(
				'default'     => $defaults['wc_sale_sticker_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_sale_sticker_background]',
				array(
					'label'     => __( 'Sale Sticker Background', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_sale_sticker_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_sale_sticker_text]',
			array(
				'default' => $defaults['wc_sale_sticker_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_sale_sticker_text]',
				array(
					'label' => __( 'Sale Sticker Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_sale_sticker_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_price_color]',
			array(
				'default' => $defaults['wc_price_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_price_color]',
				array(
					'label' => __( 'Price', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_price_color]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_panel_cart_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Sticky Panel Cart', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_background_color]',
			array(
				'default'     => $defaults['wc_panel_cart_background_color'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_background_color]',
				array(
					'label'     => __( 'Background Color', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_panel_cart_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_text_color]',
			array(
				'default' => $defaults['wc_panel_cart_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_text_color]',
				array(
					'label' => __( 'Text Color', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_panel_cart_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_button_background]',
			array(
				'default' => $defaults['wc_panel_cart_button_background'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_button_background]',
				array(
					'label' => __( 'Button Background', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_panel_cart_button_background]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_button_background_hover]',
			array(
				'default' => $defaults['wc_panel_cart_button_background_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_button_background_hover]',
				array(
					'label' => __( 'Button Background Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_panel_cart_button_background_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_button_text]',
			array(
				'default' => $defaults['wc_panel_cart_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_button_text]',
				array(
					'label' => __( 'Button Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_panel_cart_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_panel_cart_button_text_hover]',
			array(
				'default' => $defaults['wc_panel_cart_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_panel_cart_button_text_hover]',
				array(
					'label' => __( 'Button Text Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_panel_cart_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_mini_cart_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Menu Mini Cart', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_background_color]',
			array(
				'default'     => $defaults['wc_mini_cart_background_color'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_background_color]',
				array(
					'label'     => __( 'Cart Background Color', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_mini_cart_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_text_color]',
			array(
				'default' => $defaults['wc_mini_cart_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_text_color]',
				array(
					'label' => __( 'Cart Text Color', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_mini_cart_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_button_background]',
			array(
				'default' => $defaults['wc_mini_cart_button_background'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_button_background]',
				array(
					'label' => __( 'Button Background', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_mini_cart_button_background]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_button_background_hover]',
			array(
				'default' => $defaults['wc_mini_cart_button_background_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_button_background_hover]',
				array(
					'label' => __( 'Button Background Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_mini_cart_button_background_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_button_text]',
			array(
				'default' => $defaults['wc_mini_cart_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_button_text]',
				array(
					'label' => __( 'Button Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_mini_cart_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_mini_cart_button_text_hover]',
			array(
				'default' => $defaults['wc_mini_cart_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_mini_cart_button_text_hover]',
				array(
					'label' => __( 'Button Text Hover', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_mini_cart_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_price_slider_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Price Slider Widget', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_price_slider_background_color]',
			array(
				'default' => $defaults['wc_price_slider_background_color'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_price_slider_background_color]',
				array(
					'label' => __( 'Slider Background Color', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_price_slider_background_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_price_slider_bar_color]',
			array(
				'default' => $defaults['wc_price_slider_bar_color'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_price_slider_bar_color]',
				array(
					'label' => __( 'Slider Bar Color', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_price_slider_bar_color]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_product_tabs_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Product Tabs', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_product_tab]',
			array(
				'default' => $defaults['wc_product_tab'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_product_tab]',
				array(
					'label' => __( 'Product Tab Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_product_tab]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_product_tab_highlight]',
			array(
				'default' => $defaults['wc_product_tab_highlight'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_product_tab_highlight]',
				array(
					'label' => __( 'Product Tab Active', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_product_tab_highlight]',
				)
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Title_Customize_Control(
				$wp_customize,
				'generate_woocommerce_messages_title',
				array(
					'section' => 'generate_woocommerce_colors',
					'type' => 'generatepress-customizer-title',
					'title' => __( 'Messages', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_success_message_background]',
			array(
				'default'     => $defaults['wc_success_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_success_message_background]',
				array(
					'label'     => __( 'Success Message Background', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_success_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_success_message_text]',
			array(
				'default' => $defaults['wc_success_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_success_message_text]',
				array(
					'label' => __( 'Success Message Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_success_message_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_info_message_background]',
			array(
				'default'     => $defaults['wc_info_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_info_message_background]',
				array(
					'label'     => __( 'Info Message Background', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_info_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_info_message_text]',
			array(
				'default' => $defaults['wc_info_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_info_message_text]',
				array(
					'label' => __( 'Info Message Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_info_message_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_error_message_background]',
			array(
				'default'     => $defaults['wc_error_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'generate_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Alpha_Color_Customize_Control(
				$wp_customize,
				'generate_settings[wc_error_message_background]',
				array(
					'label'     => __( 'Error Message Background', 'gp-premium' ),
					'section'   => 'generate_woocommerce_colors',
					'settings'  => 'generate_settings[wc_error_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'generate_settings[wc_error_message_text]',
			array(
				'default' => $defaults['wc_error_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'generate_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'generate_settings[wc_error_message_text]',
				array(
					'label' => __( 'Error Message Text', 'gp-premium' ),
					'section' => 'generate_woocommerce_colors',
					'settings' => 'generate_settings[wc_error_message_text]',
				)
			)
		);

	}
}
