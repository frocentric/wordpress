<?php
/**
 * This file handles the customizer fields for the WooCommerce colors.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action('generate_customize_after_controls', 'generate_register_woocommerce_colors', 1000);

/**
 * Register the WooCommerce color fields.
 */
function generate_register_woocommerce_colors($wp_customize)
{
	if ( ! class_exists('GeneratePress_Customize_Field') ) {
		return;
	}

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_woocommerce_colors_shortcuts',
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

	$color_defaults = generate_get_color_defaults();

	$wp_customize->add_section(
		'generate_woocommerce_colors',
		array(
			'title'    => __( 'Colors', 'gp-premium' ),
			'priority' => 40,
			'panel'    => 'woocommerce',
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_button_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Buttons', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-button-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_alt_button_background',
		'generate_woocommerce_colors',
		'woocommerce-button-colors',
		array(
			'generate_settings[wc_alt_button_background]' => array(
				'default_value' => $color_defaults['wc_alt_button_background'],
				'label' => __( 'Alt Button Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt.disabled, .woocommerce #respond input#submit.alt.disabled:hover, .woocommerce #respond input#submit.alt:disabled, .woocommerce #respond input#submit.alt:disabled:hover, .woocommerce #respond input#submit.alt:disabled[disabled], .woocommerce #respond input#submit.alt:disabled[disabled]:hover, .woocommerce a.button.alt.disabled, .woocommerce a.button.alt.disabled:hover, .woocommerce a.button.alt:disabled, .woocommerce a.button.alt:disabled:hover, .woocommerce a.button.alt:disabled[disabled], .woocommerce a.button.alt:disabled[disabled]:hover, .woocommerce button.button.alt.disabled, .woocommerce button.button.alt.disabled:hover, .woocommerce button.button.alt:disabled, .woocommerce button.button.alt:disabled:hover, .woocommerce button.button.alt:disabled[disabled], .woocommerce button.button.alt:disabled[disabled]:hover, .woocommerce input.button.alt.disabled, .woocommerce input.button.alt.disabled:hover, .woocommerce input.button.alt:disabled, .woocommerce input.button.alt:disabled:hover, .woocommerce input.button.alt:disabled[disabled], .woocommerce input.button.alt:disabled[disabled]:hover',
				'property' => 'background-color',
				'hide_label' => false,
			),
			'generate_settings[wc_alt_button_background_hover]' => array(
				'default_value' => $color_defaults['wc_alt_button_background_hover'],
				'label' => __( 'Alt Button Background Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover',
				'property' => 'background-color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_alt_button_text',
		'generate_woocommerce_colors',
		'woocommerce-button-colors',
		array(
			'generate_settings[wc_alt_button_text]' => array(
				'default_value' => $color_defaults['wc_alt_button_text'],
				'label' => __( 'Alt Button Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt.disabled, .woocommerce #respond input#submit.alt.disabled:hover, .woocommerce #respond input#submit.alt:disabled, .woocommerce #respond input#submit.alt:disabled:hover, .woocommerce #respond input#submit.alt:disabled[disabled], .woocommerce #respond input#submit.alt:disabled[disabled]:hover, .woocommerce a.button.alt.disabled, .woocommerce a.button.alt.disabled:hover, .woocommerce a.button.alt:disabled, .woocommerce a.button.alt:disabled:hover, .woocommerce a.button.alt:disabled[disabled], .woocommerce a.button.alt:disabled[disabled]:hover, .woocommerce button.button.alt.disabled, .woocommerce button.button.alt.disabled:hover, .woocommerce button.button.alt:disabled, .woocommerce button.button.alt:disabled:hover, .woocommerce button.button.alt:disabled[disabled], .woocommerce button.button.alt:disabled[disabled]:hover, .woocommerce input.button.alt.disabled, .woocommerce input.button.alt.disabled:hover, .woocommerce input.button.alt:disabled, .woocommerce input.button.alt:disabled:hover, .woocommerce input.button.alt:disabled[disabled], .woocommerce input.button.alt:disabled[disabled]:hover',
				'property' => 'color',
				'hide_label' => false,
			),
			'generate_settings[wc_alt_button_text_hover]' => array(
				'default_value' => $color_defaults['wc_alt_button_text_hover'],
				'label' => __( 'Alt Button Text Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover',
				'property' => 'color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_product_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Products', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-product-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_product_title',
		'generate_woocommerce_colors',
		'woocommerce-product-colors',
		array(
			'generate_settings[wc_product_title_color]' => array(
				'default_value' => $color_defaults['wc_product_title_color'],
				'label' => __( 'Product Title', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '.woocommerce ul.products li.product .woocommerce-LoopProduct-link',
				'property' => 'color',
				'hide_label' => false,
			),
			'generate_settings[wc_product_title_color_hover]' => array(
				'default_value' => $color_defaults['wc_product_title_color_hover'],
				'label' => __( 'Product Title Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '.woocommerce ul.products li.product .woocommerce-LoopProduct-link:hover',
				'property' => 'color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_rating_stars',
		'generate_woocommerce_colors',
		'woocommerce-product-colors',
		array(
			'generate_settings[wc_rating_stars]' => array(
				'default_value' => $color_defaults['wc_rating_stars'],
				'label' => __( 'Star Ratings', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce .star-rating span:before, .woocommerce p.stars:hover a::before',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_sale_sticker_background',
		'generate_woocommerce_colors',
		'woocommerce-product-colors',
		array(
			'generate_settings[wc_sale_sticker_background]' => array(
				'default_value' => $color_defaults['wc_sale_sticker_background'],
				'label' => __( 'Sale Sticker Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce span.onsale',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_sale_sticker_text',
		'generate_woocommerce_colors',
		'woocommerce-product-colors',
		array(
			'generate_settings[wc_sale_sticker_text]' => array(
				'default_value' => $color_defaults['wc_sale_sticker_text'],
				'label' => __( 'Sale Sticker Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce span.onsale',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_price_text',
		'generate_woocommerce_colors',
		'woocommerce-product-colors',
		array(
			'generate_settings[wc_price_color]' => array(
				'default_value' => $color_defaults['wc_price_color'],
				'label' => __( 'Price', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce ul.products li.product .price, .woocommerce div.product p.price',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_panel_cart_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Sticky Panel Cart', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-panel-cart-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_panel_cart_background',
		'generate_woocommerce_colors',
		'woocommerce-panel-cart-colors',
		array(
			'generate_settings[wc_panel_cart_background_color]' => array(
				'default_value' => $color_defaults['wc_panel_cart_background_color'],
				'label' => __( 'Background Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.add-to-cart-panel',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_panel_cart_text',
		'generate_woocommerce_colors',
		'woocommerce-panel-cart-colors',
		array(
			'generate_settings[wc_panel_cart_text_color]' => array(
				'default_value' => $color_defaults['wc_panel_cart_text_color'],
				'label' => __( 'Text Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.add-to-cart-panel, .add-to-cart-panel a:not(.button)',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_panel_cart_button_background',
		'generate_woocommerce_colors',
		'woocommerce-panel-cart-colors',
		array(
			'generate_settings[wc_panel_cart_button_background]' => array(
				'default_value' => $color_defaults['wc_panel_cart_button_background'],
				'label' => __( 'Button Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '#wc-sticky-cart-panel .button',
				'property' => 'background-color',
				'hide_label' => false,
			),
			'generate_settings[wc_panel_cart_button_background_hover]' => array(
				'default_value' => $color_defaults['wc_panel_cart_button_background_hover'],
				'label' => __( 'Button Background Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '#wc-sticky-cart-panel .button:hover, #wc-sticky-cart-panel .button:focus, #wc-sticky-cart-panel .button:active',
				'property' => 'background-color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_panel_cart_button_text',
		'generate_woocommerce_colors',
		'woocommerce-panel-cart-colors',
		array(
			'generate_settings[wc_panel_cart_button_text]' => array(
				'default_value' => $color_defaults['wc_panel_cart_button_text'],
				'label' => __( 'Button Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '#wc-sticky-cart-panel .button',
				'property' => 'color',
				'hide_label' => false,
			),
			'generate_settings[wc_panel_cart_button_text_hover]' => array(
				'default_value' => $color_defaults['wc_panel_cart_button_text_hover'],
				'label' => __( 'Button Text Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '#wc-sticky-cart-panel .button:hover, #wc-sticky-cart-panel .button:focus, #wc-sticky-cart-panel .button:active',
				'property' => 'color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_mini_cart_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Menu Mini Cart', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-mini-cart-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_mini_cart_background',
		'generate_woocommerce_colors',
		'woocommerce-mini-cart-colors',
		array(
			'generate_settings[wc_mini_cart_background_color]' => array(
				'default_value' => $color_defaults['wc_mini_cart_background_color'],
				'label' => __( 'Background Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '#wc-mini-cart',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_mini_cart_text',
		'generate_woocommerce_colors',
		'woocommerce-mini-cart-colors',
		array(
			'generate_settings[wc_mini_cart_text_color]' => array(
				'default_value' => $color_defaults['wc_mini_cart_text_color'],
				'label' => __( 'Text Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '#wc-mini-cart',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_mini_cart_button_background',
		'generate_woocommerce_colors',
		'woocommerce-mini-cart-colors',
		array(
			'generate_settings[wc_mini_cart_button_background]' => array(
				'default_value' => $color_defaults['wc_mini_cart_button_background'],
				'label' => __( 'Button Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '#wc-mini-cart .button',
				'property' => 'background-color',
				'hide_label' => false,
			),
			'generate_settings[wc_mini_cart_button_background_hover]' => array(
				'default_value' => $color_defaults['wc_mini_cart_button_background_hover'],
				'label' => __( 'Button Background Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '#wc-mini-cart .button:hover, #wc-mini-cart .button:focus, #wc-mini-cart .button:active',
				'property' => 'background-color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_mini_cart_button_text',
		'generate_woocommerce_colors',
		'woocommerce-mini-cart-colors',
		array(
			'generate_settings[wc_mini_cart_button_text]' => array(
				'default_value' => $color_defaults['wc_mini_cart_button_text'],
				'label' => __( 'Button Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Initial Color', 'gp-premium' ),
				'element' => '#wc-mini-cart .button',
				'property' => 'color',
				'hide_label' => false,
			),
			'generate_settings[wc_mini_cart_button_text_hover]' => array(
				'default_value' => $color_defaults['wc_mini_cart_button_text_hover'],
				'label' => __( 'Button Text Hover', 'gp-premium' ),
				'tooltip' => __( 'Choose Hover Color', 'gp-premium' ),
				'element' => '#wc-mini-cart .button:hover, #wc-mini-cart .button:focus, #wc-mini-cart .button:active',
				'property' => 'color',
				'hide_label' => true,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_price_slider_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Price Slider Widget', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-price-slider-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_price_slider_background',
		'generate_woocommerce_colors',
		'woocommerce-price-slider-colors',
		array(
			'generate_settings[wc_price_slider_background_color]' => array(
				'default_value' => $color_defaults['wc_price_slider_background_color'],
				'label' => __( 'Slider Background Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_price_slider_bar',
		'generate_woocommerce_colors',
		'woocommerce-price-slider-colors',
		array(
			'generate_settings[wc_price_slider_bar_color]' => array(
				'default_value' => $color_defaults['wc_price_slider_bar_color'],
				'label' => __( 'Slider Bar Color', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce .widget_price_filter .ui-slider .ui-slider-handle',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_product_tabs_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Product Tabs', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-product-tabs-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_product_tab_text',
		'generate_woocommerce_colors',
		'woocommerce-product-tabs-colors',
		array(
			'generate_settings[wc_product_tab]' => array(
				'default_value' => $color_defaults['wc_product_tab'],
				'label' => __( 'Product Tab Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce div.product .woocommerce-tabs ul.tabs li a',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_product_tab_text',
		'generate_woocommerce_colors',
		'woocommerce-product-tabs-colors',
		array(
			'generate_settings[wc_product_tab_highlight]' => array(
				'default_value' => $color_defaults['wc_product_tab_highlight'],
				'label' => __( 'Product Tab Active', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active a',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_woocommerce_messages_colors_title',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Messages', 'gp-premium' ),
			'choices' => array(
				'toggleId' => 'woocommerce-messages-colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_success_message_background',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_success_message_background]' => array(
				'default_value' => $color_defaults['wc_success_message_background'],
				'label' => __( 'Success Message Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-message',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_success_message_text',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_success_message_text]' => array(
				'default_value' => $color_defaults['wc_success_message_text'],
				'label' => __( 'Success Message Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-message',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_info_message_background',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_info_message_background]' => array(
				'default_value' => $color_defaults['wc_info_message_background'],
				'label' => __( 'Info Message Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-info',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_info_message_text',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_info_message_text]' => array(
				'default_value' => $color_defaults['wc_info_message_text'],
				'label' => __( 'Info Message Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-info',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_error_message_background',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_error_message_background]' => array(
				'default_value' => $color_defaults['wc_error_message_background'],
				'label' => __( 'Error Message Background', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-error',
				'property' => 'background-color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_color_field_group(
		'woocommerce_error_message_text',
		'generate_woocommerce_colors',
		'woocommerce-messages-colors',
		array(
			'generate_settings[wc_error_message_text]' => array(
				'default_value' => $color_defaults['wc_error_message_text'],
				'label' => __( 'Error Message Text', 'gp-premium' ),
				'tooltip' => __( 'Choose Color', 'gp-premium' ),
				'element' => '.woocommerce-error',
				'property' => 'color',
				'hide_label' => false,
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'woocommerce_colors_redirect',
		array(
			'section' => 'generate_colors_section',
			'title' => __( 'WooCommerce', 'gp-premium' ),
			'choices' => array(
				'sectionRedirect' => true,
				'toggleId' => 'generate_woocommerce_colors',
			),
		)
	);

	GeneratePress_Customize_Field::add_title(
		'generate_colors_redirect',
		array(
			'section' => 'generate_woocommerce_colors',
			'title' => __( 'Other Theme Colors', 'gp-premium' ),
			'choices' => array(
				'sectionRedirect' => true,
				'toggleId' => 'generate_colors_section',
			),
		)
	);
}
