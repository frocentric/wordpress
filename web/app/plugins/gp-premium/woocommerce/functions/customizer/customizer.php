<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_controls_enqueue_scripts', 'generatepress_woocommerce_customizer_scripts' );
/**
 * Add our Customizer scripts.
 */
function generatepress_woocommerce_customizer_scripts() {
	wp_enqueue_script( 'generate-wc-customizer', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js', array( 'jquery','customize-controls' ), GENERATE_WOOCOMMERCE_VERSION, true );
}

add_action( 'customize_preview_init', 'generatepress_wc_live_preview_scripts', 20 );
/**
 * Add our live preview scripts.
 */
function generatepress_wc_live_preview_scripts() {
	wp_enqueue_script( 'generate-wc-colors-customizer' );
}

/**
 * Active callback to check if the cart menu item is active.
 *
 * @since 1.7
 */
function generatepress_wc_menu_cart_active() {
	return generatepress_wc_get_setting( 'cart_menu_item' );
}

add_action( 'customize_register', 'generatepress_woocommerce_customize_register' );
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function generatepress_woocommerce_customize_register( $wp_customize ) {

	// Defaults
	$defaults = generatepress_wc_defaults();

	// Controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Add control types so controls can be built using JS
	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Title_Customize_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Information_Customize_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
	}

	$wp_customize->add_section(
		'generate_woocommerce_layout',
		array(
			'title' => __( 'WooCommerce', 'gp-premium' ),
			'capability' => 'edit_theme_options',
			'priority' => 100,
			'panel' => 'generate_layout_panel'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_woocommerce_layout_shortcuts',
			array(
				'section' => 'generate_woocommerce_layout',
				'element' => __( 'WooCommerce', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'generate_woocommerce_colors',
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
			'generate_woocommerce_general_title',
			array(
				'section'     => 'generate_woocommerce_layout',
				'type'        => 'generatepress-customizer-title',
				'title'			=> __( 'General', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname'
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[cart_menu_item]',
		array(
			'default' => $defaults['cart_menu_item'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[cart_menu_item]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display cart in menu', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[cart_menu_item]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[menu_mini_cart]',
		array(
			'default' => $defaults['menu_mini_cart'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[menu_mini_cart]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display mini cart sub-menu', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[menu_mini_cart]',
			'active_callback' => 'generate_premium_wc_menu_item_active',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[cart_menu_item_icon]',
		array(
			'default' => $defaults['cart_menu_item_icon'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[cart_menu_item_icon]',
		array(
			'type' => 'select',
			'label' => __( 'Menu Item Icon', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'shopping-cart' => __( 'Shopping Cart', 'gp-premium' ),
				'shopping-bag' => __( 'Shopping Bag', 'gp-premium' ),
				'shopping-basket' => __( 'Shopping Basket', 'gp-premium' ),
			),
			'settings' => 'generate_woocommerce_settings[cart_menu_item_icon]',
			'active_callback' => 'generatepress_wc_menu_cart_active',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[cart_menu_item_content]',
		array(
			'default' => $defaults['cart_menu_item_content'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[cart_menu_item_content]',
		array(
			'type' => 'select',
			'label' => __( 'Menu Item Content', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'amount' => __( 'Amount', 'gp-premium' ),
				'number' => __( 'Number of Items', 'gp-premium' ),
			),
			'settings' => 'generate_woocommerce_settings[cart_menu_item_content]',
			'active_callback' => 'generatepress_wc_menu_cart_active',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[breadcrumbs]',
		array(
			'default' => $defaults['breadcrumbs'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[breadcrumbs]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display breadcrumbs', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[breadcrumbs]',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Title_Customize_Control(
			$wp_customize,
			'generate_woocommerce_shop_page_title',
			array(
				'section'     => 'generate_woocommerce_layout',
				'type'        => 'generatepress-customizer-title',
				'title'			=> __( 'Shop', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname'
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[sidebar_layout]',
		array(
			'default' => $defaults['sidebar_layout'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[sidebar_layout]',
		array(
			'type' => 'select',
			'label' => __( 'Sidebar Layout', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'left-sidebar' => __( 'Sidebar / Content', 'gp-premium' ),
				'right-sidebar' => __( 'Content / Sidebar', 'gp-premium' ),
				'no-sidebar' => __( 'Content (no sidebars)', 'gp-premium' ),
				'both-sidebars' => __( 'Sidebar / Content / Sidebar', 'gp-premium' ),
				'both-left' => __( 'Sidebar / Sidebar / Content', 'gp-premium' ),
				'both-right' => __( 'Content / Sidebar / Sidebar', 'gp-premium' )
			),
			'settings' => 'generate_woocommerce_settings[sidebar_layout]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[columns]', array(
			'default' => $defaults['columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[tablet_columns]', array(
			'default' => $defaults['tablet_columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[mobile_columns]', array(
			'default' => $defaults['mobile_columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'gp_woocommerce_columns',
			array(
				'label' => __( 'Product Columns', 'gp-premium' ),
				'section' => 'generate_woocommerce_layout',
				'settings' => array(
					'desktop' => 'generate_woocommerce_settings[columns]',
					'tablet' => 'generate_woocommerce_settings[tablet_columns]',
					'mobile' => 'generate_woocommerce_settings[mobile_columns]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 1,
						'max' => 6,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
					'tablet' => array(
						'min' => 1,
						'max' => 3,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
					'mobile' => array(
						'min' => 1,
						'max' => 3,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
				),
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[columns_gap]', array(
			'default' => $defaults['columns_gap'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[tablet_columns_gap]', array(
			'default' => $defaults['tablet_columns_gap'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[mobile_columns_gap]', array(
			'default' => $defaults['mobile_columns_gap'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'gp_woocommerce_column_gap',
			array(
				'label' => __( 'Column Gap', 'gp-premium' ),
				'section' => 'generate_woocommerce_layout',
				'settings' => array(
					'desktop' => 'generate_woocommerce_settings[columns_gap]',
					'tablet' => 'generate_woocommerce_settings[tablet_columns_gap]',
					'mobile' => 'generate_woocommerce_settings[mobile_columns_gap]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 0,
						'max' => 100,
						'step' => 5,
						'edit' => true,
						'unit' => 'px',
					),
					'tablet' => array(
						'min' => 0,
						'max' => 100,
						'step' => 5,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 0,
						'max' => 100,
						'step' => 5,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_image_alignment]',
		array(
			'default' => $defaults['product_archive_image_alignment'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_image_alignment]',
		array(
			'type' => 'radio',
			'label' => __( 'Image Alignment', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'left' => __( 'Left', 'gp-premium' ),
				'center' => __( 'Center', 'gp-premium' ),
				'right' => __( 'Right', 'gp-premium' ),
			),
			'settings' => 'generate_woocommerce_settings[product_archive_image_alignment]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[products_per_page]',
		array(
			'default' => $defaults['products_per_page'],
			'type' => 'option',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[products_per_page]',
		array(
			'type' => 'text',
			'label' => __( 'Products Per Page', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[products_per_page]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_alignment]',
		array(
			'default' => $defaults['product_archive_alignment'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_alignment]',
		array(
			'type' => 'radio',
			'label' => __( 'Text Alignment', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'left' => __( 'Left', 'gp-premium' ),
				'center' => __( 'Center', 'gp-premium' ),
				'right' => __( 'Right', 'gp-premium' ),
			),
			'settings' => 'generate_woocommerce_settings[product_archive_alignment]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[off_canvas_panel_on_add_to_cart]',
		array(
			'default' => $defaults['off_canvas_panel_on_add_to_cart'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[off_canvas_panel_on_add_to_cart]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display cart panel on add to cart', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[off_canvas_panel_on_add_to_cart]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[shop_page_title]',
		array(
			'default' => $defaults['shop_page_title'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[shop_page_title]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display page title', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[shop_page_title]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_results_count]',
		array(
			'default' => $defaults['product_results_count'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_results_count]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product results count', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_results_count]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_sorting]',
		array(
			'default' => $defaults['product_sorting'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_sorting]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product sorting', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_sorting]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_image]',
		array(
			'default' => $defaults['product_archive_image'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_image]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product image', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_image]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_secondary_image]',
		array(
			'default' => $defaults['product_secondary_image'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_secondary_image]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display secondary image on hover', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_secondary_image]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_title]',
		array(
			'default' => $defaults['product_archive_title'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_title]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product title', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_title]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_sale_flash]',
		array(
			'default' => $defaults['product_archive_sale_flash'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_sale_flash]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display sale flash', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_sale_flash]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_sale_flash_overlay]',
		array(
			'default' => $defaults['product_archive_sale_flash_overlay'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_sale_flash_overlay]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Sale flash over image', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_sale_flash_overlay]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_rating]',
		array(
			'default' => $defaults['product_archive_rating'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_rating]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display rating', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_rating]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_price]',
		array(
			'default' => $defaults['product_archive_price'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_price]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display price', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_price]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_add_to_cart]',
		array(
			'default' => $defaults['product_archive_add_to_cart'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_add_to_cart]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display add to cart button', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_add_to_cart]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_archive_description]',
		array(
			'default' => $defaults['product_archive_description'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_archive_description]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display short description', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_archive_description]',
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Title_Customize_Control(
			$wp_customize,
			'generate_woocommerce_single_product_title',
			array(
				'section'     => 'generate_woocommerce_layout',
				'type'        => 'generatepress-customizer-title',
				'title'			=> __( 'Single Product', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname'
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[single_sidebar_layout]',
		array(
			'default' => $defaults['single_sidebar_layout'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_choices'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[single_sidebar_layout]',
		array(
			'type' => 'select',
			'label' => __( 'Sidebar Layout', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'choices' => array(
				'inherit' => __( 'Inherit', 'gp-premium' ),
				'left-sidebar' => __( 'Sidebar / Content', 'gp-premium' ),
				'right-sidebar' => __( 'Content / Sidebar', 'gp-premium' ),
				'no-sidebar' => __( 'Content (no sidebars)', 'gp-premium' ),
				'both-sidebars' => __( 'Sidebar / Content / Sidebar', 'gp-premium' ),
				'both-left' => __( 'Sidebar / Sidebar / Content', 'gp-premium' ),
				'both-right' => __( 'Content / Sidebar / Sidebar', 'gp-premium' )
			),
			'settings' => 'generate_woocommerce_settings[single_sidebar_layout]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[single_product_image_width]', array(
			'default' => $defaults['single_product_image_width'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'gp_woocommerce_single_product_image_width',
			array(
				'label' => __( 'Product Image Area Width', 'gp-premium' ),
				'section' => 'generate_woocommerce_layout',
				'settings' => array(
					'desktop' => 'generate_woocommerce_settings[single_product_image_width]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 100,
						'step' => 5,
						'edit' => true,
						'unit' => '%',
					),
				),
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[sticky_add_to_cart_panel]',
		array(
			'default' => $defaults['sticky_add_to_cart_panel'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[sticky_add_to_cart_panel]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display add to cart panel on scroll', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[sticky_add_to_cart_panel]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[quantity_buttons]',
		array(
			'default' => $defaults['quantity_buttons'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[quantity_buttons]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display quantity buttons', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[quantity_buttons]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[single_product_sale_flash]',
		array(
			'default' => $defaults['single_product_sale_flash'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[single_product_sale_flash]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display sale flash', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[single_product_sale_flash]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_tabs]',
		array(
			'default' => $defaults['product_tabs'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_tabs]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product tabs', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_tabs]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_related]',
		array(
			'default' => $defaults['product_related'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_related]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display related products', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_related]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_upsells]',
		array(
			'default' => $defaults['product_upsells'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_upsells]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display upsell products', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_upsells]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[related_upsell_columns]', array(
			'default' => $defaults['related_upsell_columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[tablet_related_upsell_columns]', array(
			'default' => $defaults['tablet_related_upsell_columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[mobile_related_upsell_columns]', array(
			'default' => $defaults['mobile_related_upsell_columns'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'gp_woocommerce_related_upsell_columns',
			array(
				'label' => __( 'Related/Upsell Columns', 'gp-premium' ),
				'section' => 'generate_woocommerce_layout',
				'settings' => array(
					'desktop' => 'generate_woocommerce_settings[related_upsell_columns]',
					'tablet' => 'generate_woocommerce_settings[tablet_related_upsell_columns]',
					'mobile' => 'generate_woocommerce_settings[mobile_related_upsell_columns]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 1,
						'max' => 6,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
					'tablet' => array(
						'min' => 1,
						'max' => 3,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
					'mobile' => array(
						'min' => 1,
						'max' => 3,
						'step' => 1,
						'edit' => false,
						'unit' => 'Col',
					),
				),
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_meta]',
		array(
			'default' => $defaults['product_meta'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_meta]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display product meta data', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_meta]',
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[product_description]',
		array(
			'default' => $defaults['product_description'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[product_description]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Display short description', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[product_description]',
		)
	);

	$wp_customize->add_section(
		'generate_woocommerce_colors',
		array(
			'title' => __( 'WooCommerce', 'gp-premium' ),
			'capability' => 'edit_theme_options',
			'priority' => 200,
			'panel' => 'generate_colors_panel'
		)
	);

	$wp_customize->add_section(
		'generate_woocommerce_typography',
		array(
			'title' => __( 'WooCommerce', 'gp-premium' ),
			'capability' => 'edit_theme_options',
			'priority' => 200,
			'panel' => 'generate_typography_panel'
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Title_Customize_Control(
			$wp_customize,
			'generate_woocommerce_checkout_title',
			array(
				'section'     => 'generate_woocommerce_layout',
				'type'        => 'generatepress-customizer-title',
				'title'			=> __( 'Checkout', 'gp-premium' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname'
			)
		)
	);

	$wp_customize->add_setting(
		'generate_woocommerce_settings[distraction_free]',
		array(
			'default' => $defaults['distraction_free'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox'
		)
	);

	$wp_customize->add_control(
		'generate_woocommerce_settings[distraction_free]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Distraction-free mode', 'gp-premium' ),
			'description' => __( 'Remove unnecessary distractions like sidebars, footer widgets and sticky menus.', 'gp-premium' ),
			'section' => 'generate_woocommerce_layout',
			'settings' => 'generate_woocommerce_settings[distraction_free]',
		)
	);

}
