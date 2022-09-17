<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add any necessary files
require plugin_dir_path( __FILE__ ) . 'customizer/customizer.php';

/**
 * Set the WC option defaults.
 *
 * @since 1.3
 */
function generatepress_wc_defaults() {
	return apply_filters( 'generate_woocommerce_defaults', array(
		'cart_menu_item' => true,
		'cart_menu_item_icon' => 'shopping-cart',
		'cart_menu_item_content' => 'amount',
		'menu_mini_cart' => false,
		'off_canvas_panel_on_add_to_cart' => false,
		'sticky_add_to_cart_panel' => false,
		'sidebar_layout' => 'right-sidebar',
		'single_sidebar_layout' => 'inherit',
		'products_per_page' => 9,
		'columns' => 4,
		'tablet_columns' => 2,
		'mobile_columns' => 1,
		'columns_gap' => 50,
		'tablet_columns_gap' => '',
		'mobile_columns_gap' => '',
		'related_upsell_columns' => 4,
		'tablet_related_upsell_columns' => 2,
		'mobile_related_upsell_columns' => 1,
		'product_archive_image_alignment' => 'center',
		'product_archive_alignment' => 'center',
		'shop_page_title' => true,
		'product_results_count' => true,
		'product_sorting' => true,
		'product_archive_image' => true,
		'product_secondary_image' => true,
		'product_archive_title' => true,
		'product_archive_sale_flash' => true,
		'product_archive_sale_flash_overlay' => true,
		'product_archive_rating' => true,
		'product_archive_price' => true,
		'product_archive_add_to_cart' => true,
		'single_product_sale_flash' => true,
		'single_product_image_width' => '50',
		'product_tabs' => true,
		'product_related' => true,
		'product_upsells' => true,
		'product_meta' => true,
		'product_description' => true,
		'quantity_buttons' => true,
		'breadcrumbs' => true,
		'distraction_free' => true,
		'product_archive_description' => false,
	) );
}

add_filter( 'generate_color_option_defaults', 'generatepress_wc_color_defaults' );
/**
 * Set the WC color option defaults.
 *
 * @since 1.3
 */
function generatepress_wc_color_defaults( $defaults ) {
	$defaults[ 'wc_alt_button_background' ] = '#1e73be';
	$defaults[ 'wc_alt_button_background_hover' ] = '#377fbf';
	$defaults[ 'wc_alt_button_text' ] = '#ffffff';
	$defaults[ 'wc_alt_button_text_hover' ] = '#ffffff';
	$defaults[ 'wc_rating_stars' ] = '#ffa200';
	$defaults[ 'wc_sale_sticker_background' ] = '#222222';
	$defaults[ 'wc_sale_sticker_text' ] = '#ffffff';
	$defaults[ 'wc_price_color' ] = '#222222';
	$defaults[ 'wc_product_tab' ] = '#222222';
	$defaults[ 'wc_product_tab_highlight' ] = '#1e73be';
	$defaults[ 'wc_success_message_background' ] = '#0b9444';
	$defaults[ 'wc_success_message_text' ] = '#ffffff';
	$defaults[ 'wc_info_message_background' ] = '#1e73be';
	$defaults[ 'wc_info_message_text' ] = '#ffffff';
	$defaults[ 'wc_error_message_background' ] = '#e8626d';
	$defaults[ 'wc_error_message_text' ] = '#ffffff';
	$defaults[ 'wc_product_title_color' ] = '';
	$defaults[ 'wc_product_title_color_hover' ] = '';

	$defaults['wc_mini_cart_background_color'] = '#ffffff';
	$defaults['wc_mini_cart_text_color'] = '#000000';
	$defaults['wc_mini_cart_button_background'] = '';
	$defaults['wc_mini_cart_button_text'] = '';
	$defaults['wc_mini_cart_button_background_hover'] = '';
	$defaults['wc_mini_cart_button_text_hover'] = '';

	$defaults['wc_panel_cart_background_color'] = '#ffffff';
	$defaults['wc_panel_cart_text_color'] = '#000000';
	$defaults['wc_panel_cart_button_background'] = '';
	$defaults['wc_panel_cart_button_text'] = '';
	$defaults['wc_panel_cart_button_background_hover'] = '';
	$defaults['wc_panel_cart_button_text_hover'] = '';

	$defaults['wc_price_slider_background_color'] = '#dddddd';
	$defaults['wc_price_slider_bar_color'] = '#666666';

	return $defaults;
}

add_filter( 'generate_font_option_defaults', 'generatepress_wc_typography_defaults' );
/**
 * Set the WC typography option defaults.
 *
 * @since 1.3
 */
function generatepress_wc_typography_defaults( $defaults ) {
	$defaults[ 'wc_product_title_font_weight' ] = 'normal';
	$defaults[ 'wc_product_title_font_transform' ] = 'none';
	$defaults[ 'wc_product_title_font_size' ] = '20';
	$defaults[ 'mobile_wc_product_title_font_size' ] = '';
	$defaults[ 'wc_related_product_title_font_size' ] = '20';
	return $defaults;
}

add_filter( 'generate_navigation_class', 'generatepress_wc_navigation_class' );
/**
 * Add navigation class when the menu icon is enabled.
 *
 * @since 1.3
 */
function generatepress_wc_navigation_class( $classes ) {
	$classes[] = ( generatepress_wc_get_setting( 'cart_menu_item' ) ) ? 'wc-menu-cart-activated' : '';
	return $classes;
}

add_filter( 'post_class', 'generatepress_wc_post_class' );
add_filter( 'product_cat_class', 'generatepress_wc_post_class' );
/**
 * Add post classes to the products.
 *
 * @since 1.3
 *
 * @param array $classes Existing product classes.
 * @return array
 */
function generatepress_wc_post_class( $classes ) {
	if ( 'product' == get_post_type() ) {
		$classes[] = ( generatepress_wc_get_setting( 'product_archive_sale_flash_overlay' ) && generatepress_wc_get_setting( 'product_archive_image' ) ) ? 'sales-flash-overlay' : '';
		$classes[] = 'woocommerce-text-align-' . generatepress_wc_get_setting( 'product_archive_alignment' );

		if ( is_single() ) {
			$classes[] = 'wc-related-upsell-columns-' . generatepress_wc_get_setting( 'related_upsell_columns' );
			$classes[] = 'wc-related-upsell-tablet-columns-' . generatepress_wc_get_setting( 'tablet_related_upsell_columns' );
			$classes[] = 'wc-related-upsell-mobile-columns-' . generatepress_wc_get_setting( 'mobile_related_upsell_columns' );
		} else {
			$classes[] = 'woocommerce-image-align-' . generatepress_wc_get_setting( 'product_archive_image_alignment' );
		}
	}

	if ( 'product' === get_post_type() || is_cart() ) {
		if ( generatepress_wc_get_setting( 'quantity_buttons' ) ) {
			$classes[] = 'do-quantity-buttons';
		}
	}

	return $classes;
}

add_action( 'woocommerce_before_shop_loop', 'generatepress_wc_before_shop_loop' );
/**
 * Add opening element inside shop page.
 *
 * @since 1.3
 */
function generatepress_wc_before_shop_loop() {
	$classes = apply_filters( 'generate_woocommerce_container_classes', array(
		'wc-columns-container',
		'wc-columns-' . generatepress_wc_get_setting( 'columns' ),
		'wc-tablet-columns-' . generatepress_wc_get_setting( 'tablet_columns' ),
		'wc-mobile-columns-' . generatepress_wc_get_setting( 'mobile_columns' ),
	) );

	$classes = array_map('esc_attr', $classes);
	echo '<div id="wc-column-container" class="' . join( ' ', $classes ) . '">';
}

add_action( 'woocommerce_after_shop_loop', 'generatepress_wc_after_shop_loop' );
/**
 * Add closing element inside shop page.
 *
 * @since 1.3
 */
function generatepress_wc_after_shop_loop() {
	echo '</div>';
}

add_action( 'wp_enqueue_scripts', 'generatepress_wc_scripts', 100 );
/**
 * Add scripts and styles.
 *
 * @since 1.3
 */
function generatepress_wc_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'generate-woocommerce', plugin_dir_url( __FILE__ ) . "css/woocommerce{$suffix}.css", array(), GENERATE_WOOCOMMERCE_VERSION );
	wp_enqueue_style( 'generate-woocommerce-mobile', plugin_dir_url( __FILE__ ) . "css/woocommerce-mobile{$suffix}.css", array(), GENERATE_WOOCOMMERCE_VERSION, generate_premium_get_media_query( 'mobile' ) );

	if (
		generatepress_wc_get_setting( 'cart_menu_item' ) ||
		generatepress_wc_get_setting( 'off_canvas_panel_on_add_to_cart' ) ||
		generatepress_wc_show_sticky_add_to_cart() ||
		generatepress_wc_get_setting( 'quantity_buttons' )
	) {
		wp_enqueue_script( 'generate-woocommerce', plugin_dir_url( __FILE__ ) . "js/woocommerce{$suffix}.js", array( 'jquery' ), GENERATE_WOOCOMMERCE_VERSION, true );
	}

	$show_add_to_cart_panel = false;

	if ( ! is_singular() && generatepress_wc_get_setting( 'off_canvas_panel_on_add_to_cart' ) ) {
		$show_add_to_cart_panel = true;
	}

	wp_localize_script(
		'generate-woocommerce',
		'generateWooCommerce',
		array(
			'quantityButtons' => generatepress_wc_get_setting( 'quantity_buttons' ),
			'stickyAddToCart' => generatepress_wc_show_sticky_add_to_cart(),
			'addToCartPanel' => apply_filters( 'generate_woocommerce_show_add_to_cart_panel', $show_add_to_cart_panel ),
		)
	);

	if ( generatepress_wc_get_setting( 'distraction_free' ) && is_checkout() ) {
		wp_dequeue_script( 'generate-advanced-sticky' );
		wp_dequeue_script( 'generate-sticky' );
	}

	$font_icons = true;

	if ( function_exists( 'generate_get_option' ) ) {
		if ( 'font' !== generate_get_option( 'icons' ) ) {
			$font_icons = false;
		}
	}

	if ( $font_icons ) {
		wp_enqueue_style( 'gp-premium-icons' );
	}
}

/**
 * Wrapper class to get the options.
 *
 * @since 1.3
 *
 * @return string $setting The option name.
 * @return string The value.
 */
function generatepress_wc_get_setting( $setting ) {
	$settings = wp_parse_args(
		get_option( 'generate_woocommerce_settings', array() ),
		generatepress_wc_defaults()
	);

	return $settings[ $setting ];
}

add_filter( 'generate_sidebar_layout', 'generatepress_wc_sidebar_layout' );
/**
 * Set the WC sidebars.
 *
 * @since 1.3
 *
 * @param string Existing layout
 * @return string New layout
 */
function generatepress_wc_sidebar_layout( $layout ) {
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		$layout = generatepress_wc_get_setting( 'sidebar_layout' );

		if ( is_single() ) {
			if ( 'inherit' !== generatepress_wc_get_setting( 'single_sidebar_layout' ) ) {
				$layout = generatepress_wc_get_setting( 'single_sidebar_layout' );
			}

			if ( get_post_meta( get_the_ID(), '_generate-sidebar-layout-meta', true ) ) {
				$layout = get_post_meta( get_the_ID(), '_generate-sidebar-layout-meta', true );
			}
		}
	}

	return $layout;
}

add_filter( 'loop_shop_columns', 'generatepress_wc_product_columns', 999 );
/**
 * Set the WC column number.
 *
 * @since 1.3
 */
function generatepress_wc_product_columns() {
	return generatepress_wc_get_setting( 'columns' );
}

add_filter( 'loop_shop_per_page', 'generatepress_wc_products_per_page', 20 );
/**
 * Set the WC products per page.
 *
 * @since 1.3
 */
function generatepress_wc_products_per_page() {
	return generatepress_wc_get_setting( 'products_per_page' );
}

add_action( 'wp', 'generatepress_wc_setup' );
/**
 * Set up WC.
 *
 * @since 1.3
 */
function generatepress_wc_setup() {

	// Add support for WC features
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	remove_action( 'wp_enqueue_scripts', 'generate_woocommerce_css', 100 );

	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );

	add_action( 'woocommerce_before_shop_loop_item_title', 'generatepress_wc_image_wrapper_open', 8 );
	add_action( 'woocommerce_before_subcategory_title', 'generatepress_wc_image_wrapper_open', 8 );
	add_action( 'woocommerce_shop_loop_item_title', 'generatepress_wc_image_wrapper_close', 8 );
	add_action( 'woocommerce_before_subcategory_title', 'generatepress_wc_image_wrapper_close', 20 );

	$archive_results_count       = generatepress_wc_get_setting( 'product_results_count' );
	$archive_sorting             = generatepress_wc_get_setting( 'product_sorting' );
	$archive_image               = generatepress_wc_get_setting( 'product_archive_image' );
	$archive_sale_flash          = generatepress_wc_get_setting( 'product_archive_sale_flash' );
	$archive_sale_flash_overlay  = generatepress_wc_get_setting( 'product_archive_sale_flash_overlay' );
	$archive_rating              = generatepress_wc_get_setting( 'product_archive_rating' );
	$archive_price               = generatepress_wc_get_setting( 'product_archive_price' );
	$archive_add_to_cart         = generatepress_wc_get_setting( 'product_archive_add_to_cart' );
	$archive_title               = generatepress_wc_get_setting( 'product_archive_title' );
	$single_product_sale_flash   = generatepress_wc_get_setting( 'single_product_sale_flash' );
	$product_tabs                = generatepress_wc_get_setting( 'product_tabs' );
	$product_related             = generatepress_wc_get_setting( 'product_related' );
	$product_upsells             = generatepress_wc_get_setting( 'product_upsells' );
	$product_meta                = generatepress_wc_get_setting( 'product_meta' );
	$product_description         = generatepress_wc_get_setting( 'product_description' );
	$breadcrumbs                 = generatepress_wc_get_setting( 'breadcrumbs' );
	$page_title                  = generatepress_wc_get_setting( 'shop_page_title' );
	$distraction_free            = generatepress_wc_get_setting( 'distraction_free' );
	$archive_description         = generatepress_wc_get_setting( 'product_archive_description' );

	if ( false === $page_title ) {
		add_filter( 'woocommerce_show_page_title', '__return_false' );
	}

	if ( false === $archive_results_count ) {
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}

	if ( false === $archive_sorting ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
	}

	if ( false === $archive_image ) {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	}

	if ( false === $archive_sale_flash_overlay ) {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );
	}

	if ( false === $archive_sale_flash ) {
		if ( false === $archive_sale_flash_overlay ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );
		} else {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		}
	}

	if ( false === $single_product_sale_flash ) {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	}

	if ( false === $archive_rating ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}

	if ( false === $archive_price ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}

	if ( false === $archive_add_to_cart ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}

	if ( false === $archive_title ) {
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	}

	if ( false === $product_tabs ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	}

	if ( false === $product_related ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}

	if ( false === $product_upsells ) {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	}

	if ( false === $product_meta ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	}

	if ( false === $product_description ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	}

	if ( false === $breadcrumbs ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	}

	if ( true === $distraction_free ) {
		add_filter( 'generate_sidebar_layout', 'generatepress_wc_checkout_sidebar_layout' );
		add_filter( 'generate_footer_widgets', 'generatepress_wc_checkout_footer_widgets' );
	}

	if ( true === $archive_description && ! is_single() && ! is_cart() ) {
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5 );
		add_action( 'woocommerce_after_subcategory_title', 'generatepress_wc_category_description', 12 );
	}
}

/**
 * Set the WC checkout sidebar layout.
 *
 * @since 1.3
 *
 * @param string $sidebar Existing sidebar layout.
 * @return string New sidebar layout.
 */
function generatepress_wc_checkout_sidebar_layout( $layout ) {
	if ( is_checkout() ) {
		return 'no-sidebar';
	}

	return $layout;
}

/**
 * Set the WC checkout footer widgets.
 *
 * @since 1.3
 *
 * @param int $widgets Existing number of widgets.
 * @return int New number of widgets.
 */
function generatepress_wc_checkout_footer_widgets( $widgets ) {
	if ( is_checkout() ) {
		return '0';
	}

	return $widgets;
}

add_filter( 'wp_nav_menu_items', 'generatepress_wc_menu_cart', 10, 2 );
/**
 * Add the WC cart menu item.
 *
 * @since 1.3
 *
 * @param string $nav The HTML list content for the menu items.
 * @param stdClass $args An object containing wp_nav_menu() arguments.
 * @return string The search icon menu item.
 */
function generatepress_wc_menu_cart( $nav, $args ) {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		return $nav;
	}

	// If our primary menu is set, add the search icon.
	if ( apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) === $args->theme_location && generatepress_wc_get_setting( 'cart_menu_item' ) ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( ! isset( WC()->cart ) ) {
			return;
		}

		$has_items = false;

		if ( ! WC()->cart->is_empty() ) {
			$has_items = 'has-items';
		}

		return sprintf(
			'%1$s
			<li class="wc-menu-item menu-item-align-right %3$s %4$s">
				%2$s
			</li>',
			$nav,
			generatepress_wc_cart_link(),
			is_cart() ? 'current-menu-item' : '',
			$has_items
		);
	}

	// Our primary menu isn't set, return the regular nav.
	return $nav;
}

add_action( 'wp', 'generatepress_wc_add_menu_bar_items' );
/**
 * Add to the menu bar items.
 *
 * @since 1.11.0
 */
function generatepress_wc_add_menu_bar_items() {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		if ( 'secondary' === apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) && generatepress_wc_get_setting( 'cart_menu_item' ) ) {
			add_action( 'generate_secondary_menu_bar_items', 'generate_wc_do_cart_secondary_menu_item', 5 );
		}

		if ( 'primary' === apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) && generatepress_wc_get_setting( 'cart_menu_item' ) ) {
			add_action( 'generate_menu_bar_items', 'generate_wc_do_cart_menu_item', 5 );
		}
	}
}

/**
 * Add the cart menu item to the secondary navigation.
 */
function generate_wc_do_cart_secondary_menu_item() {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		if ( 'secondary' === apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) && generatepress_wc_get_setting( 'cart_menu_item' ) ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			if ( ! isset( WC()->cart ) ) {
				return;
			}

			$has_items = false;

			if ( ! WC()->cart->is_empty() ) {
				$has_items = 'has-items';
			}

			printf(
				'<span class="menu-bar-item wc-menu-item %2$s %3$s">
					%1$s
				</span>',
				generatepress_wc_cart_link(),
				is_cart() ? 'current-menu-item' : '',
				$has_items
			);
		}
	}
}

/**
 * Add the cart menu item to the navigation.
 *
 * @since 1.11.0
 */
function generate_wc_do_cart_menu_item() {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		if ( 'primary' === apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) && generatepress_wc_get_setting( 'cart_menu_item' ) ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			if ( ! isset( WC()->cart ) ) {
				return;
			}

			$has_items = false;

			if ( ! WC()->cart->is_empty() ) {
				$has_items = 'has-items';
			}

			printf(
				'<span class="menu-bar-item wc-menu-item %2$s %3$s">
					%1$s
				</span>',
				generatepress_wc_cart_link(),
				is_cart() ? 'current-menu-item' : '',
				$has_items
			);
		}
	}
}

/**
 * Build the menu cart link.
 *
 * @since 1.3
 */
function generatepress_wc_cart_link() {
	// Kept for backward compatibility.
	$legacy_icon = apply_filters( 'generate_woocommerce_menu_cart_icon', '' );

	// Get the icon type.
	$icon_type = generatepress_wc_get_setting( 'cart_menu_item_icon' );

	$icon = '';

	if ( function_exists( 'generate_get_svg_icon' ) ) {
		$icon = generate_get_svg_icon( $icon_type );
	}

	ob_start();

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! isset( WC()->cart ) ) {
		return;
	}

	$no_items = '';

	if ( ! WC()->cart->get_cart_contents_count() > 0 ) {
		$no_items = 'no-items';
	}

	printf(
		'<a href="%1$s" class="cart-contents %2$s %3$s" title="%4$s">%5$s%6$s%7$s<span class="amount">%8$s</span></a>',
		esc_url( wc_get_cart_url() ),
		esc_attr( $icon_type ),
		$icon ? 'has-svg-icon' : '',
		esc_attr__( 'View your shopping cart', 'gp-premium' ),
		$icon,
		sprintf(
			'<span class="number-of-items %1$s">%2$s</span>',
			$no_items,
			WC()->cart->get_cart_contents_count()
		),
		$legacy_icon,
		WC()->cart->subtotal > 0 ? wp_kses_data( WC()->cart->get_cart_subtotal() ) : ''
	);

	if ( generatepress_wc_get_setting( 'menu_mini_cart' ) && ! is_cart() ) : ?>
		<div id="wc-mini-cart" class="wc-mini-cart" aria-hidden="true">
			<div class="inside-wc-mini-cart">
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			</div>
		</div>
	<?php
	endif;

	return ob_get_clean();
}

add_filter( 'woocommerce_add_to_cart_fragments', 'generatepress_wc_cart_link_fragment' );
/**
 * Make it so the amount can be updated using AJAX.
 *
 * @since 1.3
 *
 * @param array $fragments
 * @return array
 */
function generatepress_wc_cart_link_fragment( $fragments ) {
	global $woocommerce;

	if ( isset( WC()->cart ) ) {
		$fragments['.cart-contents span.amount'] = ( WC()->cart->subtotal > 0 ) ? '<span class="amount">' . wp_kses_data( WC()->cart->get_cart_subtotal() ) . '</span>' : '<span class="amount"></span>';
		$fragments['.cart-contents span.number-of-items'] = ( WC()->cart->get_cart_contents_count() > 0 ) ? '<span class="number-of-items">' . wp_kses_data( WC()->cart->get_cart_contents_count() ) . '</span>' : '<span class="number-of-items no-items"></span>';
	}

	return $fragments;
}

/**
 * Add the cart icon in the mobile menu.
 *
 * @since 1.3
 */
function generatepress_wc_mobile_cart_link() {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		return;
	}

	if ( ! generatepress_wc_get_setting( 'cart_menu_item' ) || 'primary' !== apply_filters( 'generate_woocommerce_menu_item_location', 'primary' ) ) {
		return;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! isset( WC()->cart ) ) {
		return;
	}

	$has_items = false;

	if ( ! WC()->cart->is_empty() ) {
		$has_items = ' has-items';
	}
	?>
	<div class="mobile-bar-items wc-mobile-cart-items<?php echo $has_items; ?>">
		<?php do_action( 'generate_mobile_cart_items' ); ?>
		<?php echo generatepress_wc_cart_link(); ?>
	</div>
	<?php
}

add_action( 'wp', 'generate_woocommerce_do_mobile_cart_link' );
/**
 * Add the mobile cart link to the menus.
 */
function generate_woocommerce_do_mobile_cart_link() {
	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		add_action( 'generate_after_mobile_menu_button', 'generatepress_wc_mobile_cart_link' );
		add_action( 'generate_after_mobile_header_menu_button', 'generatepress_wc_mobile_cart_link' );
	} else {
		add_action( 'generate_inside_navigation', 'generatepress_wc_mobile_cart_link' );
		add_action( 'generate_inside_mobile_header', 'generatepress_wc_mobile_cart_link' );
	}
}

add_filter( 'woocommerce_output_related_products_args', 'generatepress_wc_related_products_count' );
/**
 * Adjust the related products output.
 *
 * @since 1.3
 *
 * @param array $args
 * @return array
 */
function generatepress_wc_related_products_count( $args ) {
	$args['posts_per_page'] = generatepress_wc_get_setting( 'related_upsell_columns' );
	$args['columns'] = generatepress_wc_get_setting( 'related_upsell_columns' );
	return $args;
}

/**
 * Build our dynamic CSS.
 *
 * @since 1.3
 */
function generatepress_wc_css() {
	if ( ! function_exists( 'generate_get_color_defaults' ) || ! function_exists( 'generate_get_defaults' ) || ! function_exists( 'generate_get_default_fonts' ) ) {
		return;
	}

	$defaults = array_merge( generate_get_color_defaults(), generate_get_defaults(), generate_get_default_fonts() );

	$settings = wp_parse_args(
		get_option( 'generate_settings', array() ),
		$defaults
	);

	require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
	$css = new GeneratePress_Pro_CSS();

	// Check if we're using our legacy typography system.
	$using_dynamic_typography = function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography();

	// Product title color.
	$css->set_selector( '.woocommerce ul.products li.product .woocommerce-LoopProduct-link' );
	$css->add_property( 'color', esc_attr( $settings['wc_product_title_color'] ) );

	// Product title color hover.
	$css->set_selector( '.woocommerce ul.products li.product .woocommerce-LoopProduct-link:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_product_title_color_hover'] ) );

	if ( ! $using_dynamic_typography ) {
		// Product title font size.
		$css->set_selector( '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title' );
		$css->add_property( 'font-weight', esc_attr( $settings['wc_product_title_font_weight'] ) );
		$css->add_property( 'text-transform', esc_attr( $settings['wc_product_title_font_transform'] ) );
		$css->add_property( 'font-size', esc_attr( $settings['wc_product_title_font_size'] ), false, 'px' );

		$css->set_selector( '.woocommerce .up-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .cross-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .related ul.products li.product .woocommerce-LoopProduct-link h2' );
		if ( '' !== $settings['wc_related_product_title_font_size'] ) {
			$css->add_property( 'font-size', esc_attr( $settings['wc_related_product_title_font_size'] ), false, 'px' );
		}
	}

	// Primary button.
	$css->set_selector( '.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button' );
	$css->add_property( 'color', esc_attr( $settings['form_button_text_color'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['form_button_background_color'] ) );

	if ( ! $using_dynamic_typography && isset( $settings['buttons_font_size'] ) ) {
		$css->add_property( 'font-weight', esc_attr( $settings['buttons_font_weight'] ) );
		$css->add_property( 'text-transform', esc_attr( $settings['buttons_font_transform'] ) );

		if ( '' !== $settings['buttons_font_size'] ) {
			$css->add_property( 'font-size', absint( $settings['buttons_font_size'] ), false, 'px' );
		}
	}

	if ( $using_dynamic_typography && class_exists( 'GeneratePress_Typography' ) ) {
		$typography = generate_get_option( 'typography' );

		foreach ( (array) $typography as $key => $data ) {
			if ( 'buttons' === $data['selector'] ) {
				if ( ! empty( $data['fontSize'] ) ) {
					$css->add_property( 'font-size', absint( $data['fontSize'] ), false, 'px' );
				}

				if ( ! empty( $data['fontWeight'] ) ) {
					$css->add_property( 'font-weight', absint( $data['fontWeight'] ) );
				}

				if ( ! empty( $data['textTransform'] ) ) {
					$css->add_property( 'text-transform', absint( $data['textTransform'] ) );
				}

				if ( ! empty( $data['fontSizeTablet'] ) ) {
					$css->start_media_query( generate_premium_get_media_query( 'tablet' ) );
					$css->add_property( 'font-size', absint( $data['fontSizeTablet'] ), false, 'px' );
					$css->stop_media_query();
				}

				if ( ! empty( $data['fontSizeMobile'] ) ) {
					$css->start_media_query( generate_premium_get_media_query( 'mobile' ) );
					$css->add_property( 'font-size', absint( $data['fontSizeMobile'] ), false, 'px' );
					$css->stop_media_query();
				}
			}
		}
	}

	// Primary button hover.
	$css->set_selector( '.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover' );
	$css->add_property( 'color', esc_attr( $settings['form_button_text_color_hover'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['form_button_background_color_hover'] ) );

	// Alt button.
	$css->set_selector( '.woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit.alt.disabled, .woocommerce #respond input#submit.alt.disabled:hover, .woocommerce #respond input#submit.alt:disabled, .woocommerce #respond input#submit.alt:disabled:hover, .woocommerce #respond input#submit.alt:disabled[disabled], .woocommerce #respond input#submit.alt:disabled[disabled]:hover, .woocommerce a.button.alt.disabled, .woocommerce a.button.alt.disabled:hover, .woocommerce a.button.alt:disabled, .woocommerce a.button.alt:disabled:hover, .woocommerce a.button.alt:disabled[disabled], .woocommerce a.button.alt:disabled[disabled]:hover, .woocommerce button.button.alt.disabled, .woocommerce button.button.alt.disabled:hover, .woocommerce button.button.alt:disabled, .woocommerce button.button.alt:disabled:hover, .woocommerce button.button.alt:disabled[disabled], .woocommerce button.button.alt:disabled[disabled]:hover, .woocommerce input.button.alt.disabled, .woocommerce input.button.alt.disabled:hover, .woocommerce input.button.alt:disabled, .woocommerce input.button.alt:disabled:hover, .woocommerce input.button.alt:disabled[disabled], .woocommerce input.button.alt:disabled[disabled]:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_alt_button_text'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['wc_alt_button_background'] ) );

	// Alt button hover.
	$css->set_selector( '.woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_alt_button_text_hover'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['wc_alt_button_background_hover'] ) );

	// Star rating.
	$css->set_selector( '.woocommerce .star-rating span:before, .woocommerce p.stars:hover a::before' );
	$css->add_property( 'color', esc_attr( $settings['wc_rating_stars'] ) );

	// Sale sticker.
	$css->set_selector( '.woocommerce span.onsale' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_sale_sticker_background'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_sale_sticker_text'] ) );

	// Price.
	$css->set_selector( '.woocommerce ul.products li.product .price, .woocommerce div.product p.price' );
	$css->add_property( 'color', esc_attr( $settings['wc_price_color'] ) );

	// Product tab.
	$css->set_selector( '.woocommerce div.product .woocommerce-tabs ul.tabs li a' );
	$css->add_property( 'color', esc_attr( $settings['wc_product_tab'] ) );

	// Highlight product tab.
	$css->set_selector( '.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active a' );
	$css->add_property( 'color', esc_attr( $settings['wc_product_tab_highlight'] ) );

	// Success message.
	$css->set_selector( '.woocommerce-message' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_success_message_background'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_success_message_text'] ) );

	$css->set_selector( 'div.woocommerce-message a.button, div.woocommerce-message a.button:focus, div.woocommerce-message a.button:hover, div.woocommerce-message a, div.woocommerce-message a:focus, div.woocommerce-message a:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_success_message_text'] ) );

	// Info message.
	$css->set_selector( '.woocommerce-info' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_info_message_background'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_info_message_text'] ) );

	$css->set_selector( 'div.woocommerce-info a.button, div.woocommerce-info a.button:focus, div.woocommerce-info a.button:hover, div.woocommerce-info a, div.woocommerce-info a:focus, div.woocommerce-info a:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_info_message_text'] ) );

	// Info message.
	$css->set_selector( '.woocommerce-error' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_error_message_background'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_error_message_text'] ) );

	$css->set_selector( 'div.woocommerce-error a.button, div.woocommerce-error a.button:focus, div.woocommerce-error a.button:hover, div.woocommerce-error a, div.woocommerce-error a:focus, div.woocommerce-error a:hover' );
	$css->add_property( 'color', esc_attr( $settings['wc_error_message_text'] ) );

	// Archive short description.
	$css->set_selector( '.woocommerce-product-details__short-description' );
	if ( '' !== $settings['content_text_color'] ) {
		$css->add_property( 'color', esc_attr( $settings['content_text_color'] ) );
	} else {
		$css->add_property( 'color', esc_attr( $settings['text_color'] ) );
	}

	$css->set_selector( '#wc-mini-cart' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_mini_cart_background_color'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_mini_cart_text_color'] ) );

	$css->set_selector( '#wc-mini-cart a:not(.button), #wc-mini-cart a.remove' );
	$css->add_property( 'color', esc_attr( $settings['wc_mini_cart_text_color'] ) );

	$css->set_selector( '#wc-mini-cart .button' );
	if ( $settings['wc_mini_cart_button_background'] ) {
		$css->add_property( 'background-color', esc_attr( $settings['wc_mini_cart_button_background'] ) );
	}

	if ( $settings['wc_mini_cart_button_text'] ) {
		$css->add_property( 'color', esc_attr( $settings['wc_mini_cart_button_text'] ) );
	} else {
		$css->add_property( 'color', esc_attr( $settings['form_button_text_color'] ) );
	}

	$css->set_selector( '#wc-mini-cart .button:hover, #wc-mini-cart .button:focus, #wc-mini-cart .button:active' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_mini_cart_button_background_hover'] ) );

	if ( $settings['wc_mini_cart_button_text_hover'] ) {
		$css->add_property( 'color', esc_attr( $settings['wc_mini_cart_button_text_hover'] ) );
	} else {
		$css->add_property( 'color', esc_attr( $settings['form_button_text_color_hover'] ) );
	}

	$css->set_selector( '.woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images' );
	$css->add_property( 'width', absint( generatepress_wc_get_setting( 'single_product_image_width' ) ), false, '%' );

	if ( ! $using_dynamic_typography && function_exists( 'generate_get_font_family_css' ) ) {
		$buttons_family = generate_get_font_family_css( 'font_buttons', 'generate_settings', generate_get_default_fonts() );
		$css->set_selector( '.woocommerce.widget_shopping_cart .woocommerce-mini-cart__buttons a' );
		$css->add_property( 'font-family', $buttons_family );
	}

	$css->set_selector( '.add-to-cart-panel' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_panel_cart_background_color'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_panel_cart_text_color'] ) );

	$css->set_selector( '.add-to-cart-panel a:not(.button)' );
	$css->add_property( 'color', esc_attr( $settings['wc_panel_cart_text_color'] ) );

	$css->set_selector( '#wc-sticky-cart-panel .button' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_panel_cart_button_background'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_panel_cart_button_text'] ) );

	$css->set_selector( '#wc-sticky-cart-panel .button:hover, #wc-sticky-cart-panel .button:focus, #wc-sticky-cart-panel .button:active' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_panel_cart_button_background_hover'] ) );
	$css->add_property( 'color', esc_attr( $settings['wc_panel_cart_button_text_hover'] ) );

	$transparent_border_color = $settings['text_color'];

	if ( $settings['content_text_color'] ) {
		$transparent_border_color = $settings['content_text_color'];
	}

	$transparent_border_color = generate_premium_check_text_color( $transparent_border_color );

	if ( 'light' === $transparent_border_color ) {
		$transparent_border_elements = '.woocommerce-ordering select, .variations .value select';

		if ( generatepress_wc_get_setting( 'quantity_buttons' ) ) {
			$transparent_border_elements = '.woocommerce form .quantity.buttons-added .qty, .woocommerce form .quantity.buttons-added .minus, .woocommerce form .quantity.buttons-added .plus, .do-quantity-buttons form .quantity:not(.buttons-added):before, .do-quantity-buttons form .quantity:not(.buttons-added):after, .woocommerce-ordering select, .variations .value select';
		}

		$css->set_selector( $transparent_border_elements );
		$css->add_property( 'border-color', 'rgba(255,255,255,0.1)' );

		if ( generatepress_wc_get_setting( 'sticky_add_to_cart_panel' ) ) {
			$css->set_selector( '#wc-sticky-cart-panel .quantity.buttons-added .qty, #wc-sticky-cart-panel .quantity.buttons-added .minus, #wc-sticky-cart-panel .quantity.buttons-added .plus' );
			$css->add_property( 'border-color', 'rgba(255,255,255,0.1)' );
		}
	}

	$css->set_selector( '.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_price_slider_background_color'] ) );

	$css->set_selector( '.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce .widget_price_filter .ui-slider .ui-slider-handle' );
	$css->add_property( 'background-color', esc_attr( $settings['wc_price_slider_bar_color'] ) );

	if ( 'number' === generatepress_wc_get_setting( 'cart_menu_item_content' ) ) {
		$nav_text_color = generate_premium_check_text_color( $settings['navigation_text_color'] );

		$css->set_selector( '.cart-contents > span.number-of-items' );
		if ( 'light' === $nav_text_color ) {
			$css->add_property( 'background-color', 'rgba(255,255,255,0.1)' );
		} else {
			$css->add_property( 'background-color', 'rgba(0,0,0,0.05)' );
		}

		$css->set_selector( '.cart-contents .amount' );
		$css->add_property( 'display', 'none' );

		$css->set_selector( '.cart-contents > span.number-of-items' );
		$css->add_property( 'display', 'inline-block' );
	}

	$font_icons = true;

	if ( function_exists( 'generate_get_option' ) ) {
		if ( 'font' !== generate_get_option( 'icons' ) ) {
			$font_icons = false;
		}
	}

	if ( ! $font_icons ) {
		$css->set_selector( '.woocommerce-MyAccount-navigation li.is-active a:after, a.button.wc-forward:after' );
		$css->add_property( 'display', 'none' );

		$css->set_selector( '#payment .payment_methods>.wc_payment_method>label:before' );
		$css->add_property( 'font-family', 'WooCommerce' );
		$css->add_property( 'content', '"\e039"' );

		$css->set_selector( '#payment .payment_methods li.wc_payment_method>input[type=radio]:first-child:checked+label:before' );
		$css->add_property( 'content', '"\e03c"' );

		$css->set_selector( '.woocommerce-ordering:after' );
		$css->add_property( 'font-family', 'WooCommerce' );
		$css->add_property( 'content', '"\e00f"' );
	}

	$css->set_selector( '.wc-columns-container .products, .woocommerce .related ul.products, .woocommerce .up-sells ul.products' );

	if ( '' !== generatepress_wc_get_setting( 'columns_gap' ) ) {
		$css->add_property( 'grid-gap', generatepress_wc_get_setting( 'columns_gap' ), false, 'px' );
	}

	$css->start_media_query( generate_premium_get_media_query( 'tablet' ) );
		$css->set_selector( '.wc-columns-container .products, .woocommerce .related ul.products, .woocommerce .up-sells ul.products' );

		if ( '' !== generatepress_wc_get_setting( 'tablet_columns_gap' ) ) {
			$css->add_property( 'grid-gap', generatepress_wc_get_setting( 'tablet_columns_gap' ), false, 'px' );
		}

		if ( 3 === generatepress_wc_get_setting( 'tablet_columns' ) ) {
			$css->set_selector( '.woocommerce .wc-columns-container.wc-tablet-columns-3 .products' );
			$css->add_property( '-ms-grid-columns', '(1fr)[3]' );
			$css->add_property( 'grid-template-columns', 'repeat(3, 1fr)' );
		}

		if ( 2 === generatepress_wc_get_setting( 'tablet_columns' ) ) {
			$css->set_selector( '.woocommerce .wc-columns-container.wc-tablet-columns-2 .products' );
			$css->add_property( '-ms-grid-columns', '(1fr)[2]' );
			$css->add_property( 'grid-template-columns', 'repeat(2, 1fr)' );
		}

		if ( 1 === generatepress_wc_get_setting( 'tablet_columns' ) ) {
			$css->set_selector( '.woocommerce .wc-columns-container.wc-tablet-columns-1 .products' );
			$css->add_property( 'width', '100%' );
			$css->add_property( '-ms-grid-columns', '1fr' );
			$css->add_property( 'grid-template-columns', '1fr' );
		}

		if ( 3 === generatepress_wc_get_setting( 'tablet_related_upsell_columns' ) ) {
			$css->set_selector( '.wc-related-upsell-tablet-columns-3 .related ul.products, .wc-related-upsell-tablet-columns-3 .up-sells ul.products' );
			$css->add_property( '-ms-grid-columns', '(1fr)[3]' );
			$css->add_property( 'grid-template-columns', 'repeat(3, 1fr)' );
		}

		if ( 2 === generatepress_wc_get_setting( 'tablet_related_upsell_columns' ) ) {
			$css->set_selector( '.wc-related-upsell-tablet-columns-2 .related ul.products, .wc-related-upsell-tablet-columns-2 .up-sells ul.products' );
			$css->add_property( '-ms-grid-columns', '(1fr)[2]' );
			$css->add_property( 'grid-template-columns', 'repeat(2, 1fr)' );
		}

		if ( 1 === generatepress_wc_get_setting( 'tablet_related_upsell_columns' ) ) {
			$css->set_selector( '.wc-related-upsell-tablet-columns-1 .related ul.products, .wc-related-upsell-tablet-columns-1 .up-sells ul.products' );
			$css->add_property( 'width', '100%' );
			$css->add_property( '-ms-grid-columns', '1fr' );
			$css->add_property( 'grid-template-columns', '1fr' );
		}
	$css->stop_media_query();

	$css->start_media_query( generate_premium_get_media_query( 'mobile' ) );
		if ( ! $using_dynamic_typography ) {
			$css->set_selector( '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title' );
			if ( '' !== $settings[ 'mobile_wc_product_title_font_size' ] ) {
				$css->add_property( 'font-size', esc_attr( $settings[ 'mobile_wc_product_title_font_size' ] ), false, 'px' );
			}
		}

		$css->set_selector( '.add-to-cart-panel .continue-shopping' );
		$css->add_property( 'background-color', esc_attr( $settings['wc_panel_cart_background_color'] ) );

		$css->set_selector( '.wc-columns-container .products, .woocommerce .related ul.products, .woocommerce .up-sells ul.products' );

		if ( '' !== generatepress_wc_get_setting( 'mobile_columns_gap' ) ) {
			$css->add_property( 'grid-gap', generatepress_wc_get_setting( 'mobile_columns_gap' ), false, 'px' );
		}

		$css->set_selector( '.woocommerce #content div.product div.images,.woocommerce div.product div.images,.woocommerce-page #content div.product div.images,.woocommerce-page div.product div.images' );
		$css->add_property( 'width', '100%' );
	$css->stop_media_query();

	$using_flex = false;

	if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
		$using_flex = true;
	}

	$css->start_media_query( generate_premium_get_media_query( 'mobile-menu' ) );
		if ( ! $using_flex ) {
			$css->set_selector( '.mobile-bar-items + .menu-toggle' );
			$css->add_property( 'text-align', 'left' );
		}

		$css->set_selector( 'nav.toggled .main-nav li.wc-menu-item' );
		$css->add_property( 'display', 'none !important' );

		if ( ! $using_flex ) {
			$css->set_selector( 'body.nav-search-enabled .wc-menu-cart-activated:not(#mobile-header) .mobile-bar-items' );
			$css->add_property( 'float', 'right' );
			$css->add_property( 'position', 'relative' );

			$css->set_selector( '.nav-search-enabled .wc-menu-cart-activated:not(#mobile-header) .menu-toggle' );
			$css->add_property( 'float', 'left' );
			$css->add_property( 'width', 'auto' );
		}

		$css->set_selector( '.mobile-bar-items.wc-mobile-cart-items' );
		$css->add_property( 'z-index', '1' );
	$css->stop_media_query();

	return $css->css_output();
}

add_action( 'wp_enqueue_scripts', 'generatepress_wc_enqueue_css', 100 );
/**
 * Enqueue our dynamic CSS.
 *
 * @since 1.3
 */
function generatepress_wc_enqueue_css() {
	wp_add_inline_style( 'generate-woocommerce', generatepress_wc_css() );

	if ( class_exists( 'GeneratePress_Typography' ) ) {
		wp_add_inline_style( 'generate-woocommerce', GeneratePress_Typography::get_css( 'woocommerce' ) );
	}
}

/**
 * Open WC image wrapper.
 *
 * @since 1.3
 */
function generatepress_wc_image_wrapper_open() {
	if ( generatepress_wc_get_setting( 'product_archive_image' ) ) {
		echo '<div class="wc-product-image"><div class="inside-wc-product-image">';
	}
}

/**
 * Close WC image wrapper.
 *
 * @since 1.3
 */
function generatepress_wc_image_wrapper_close() {
	if ( generatepress_wc_get_setting( 'product_archive_image' ) ) {
		echo '</div></div>';
	}
}

add_filter( 'post_class', 'generatepress_wc_product_has_gallery' );
add_filter( 'product_cat_class', 'generatepress_wc_product_has_gallery' );
/**
 * Add product image post classes to products.
 *
 * @since 1.3
 *
 * @param array $classes Existing classes.
 * @return array New classes.
 */
function generatepress_wc_product_has_gallery( $classes ) {

	$post_type = get_post_type( get_the_ID() );

	if ( 'product' === $post_type && method_exists( 'WC_Product', 'get_gallery_image_ids' ) ) {
		$product = wc_get_product( get_the_ID() );
		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && generatepress_wc_get_setting( 'product_secondary_image' ) && generatepress_wc_get_setting( 'product_archive_image' ) && has_post_thumbnail() ) {
			$classes[] = 'wc-has-gallery';
		}
	}

	return $classes;
}

add_action( 'woocommerce_before_shop_loop_item_title', 'generatepress_wc_secondary_product_image' );
/**
 * Add secondary product image.
 *
 * @since 1.3
 */
function generatepress_wc_secondary_product_image() {
	$post_type = get_post_type( get_the_ID() );

	if ( 'product' === $post_type && method_exists( 'WC_Product', 'get_gallery_image_ids' ) ) {
		$product = wc_get_product( get_the_ID() );
		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids && generatepress_wc_get_setting( 'product_secondary_image' ) && generatepress_wc_get_setting( 'product_archive_image' ) && has_post_thumbnail() ) {
			$secondary_image_id = $attachment_ids['0'];
			echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
		}
	}
}

add_filter( 'woocommerce_product_get_rating_html', 'generatepress_wc_rating_html', 10, 2 );
/**
 * Always show ratings area to make sure products are similar heights.
 *
 * @since 1.3.1
 *
 * @param string $rating_html
 * @param int $rating
 * @return string
 */
function generatepress_wc_rating_html( $rating_html, $rating ) {
	if ( $rating > 0 ) {
		$title = sprintf( __( 'Rated %s out of 5', 'gp-premium' ), $rating );
	} else {
		$title = __( 'Not yet rated', 'gp-premium' );
		$rating = 0;
	}

	$rating_html  = '<div class="star-rating" title="' . esc_attr( $title ) . '">';
	$rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . __( 'out of 5', 'gp-premium' ) . '</span>';
	$rating_html .= '</div>';

	return $rating_html;
}

/**
 * Add WC category description.
 *
 * @since 1.3
 *
 * @param array $category
 * @return string
 */
function generatepress_wc_category_description( $category ) {
	$prod_term = get_term( $category->term_id, 'product_cat' );
	$description = $prod_term->description;
	echo '<div class="woocommerce-product-details__short-description">' . $description . '</div>';
}

add_action( 'generate_after_footer', 'generatepress_wc_add_to_cart_helper' );
/**
 * Adds a sticky/slide down navigation with add to cart details.
 *
 * @since 1.8
 */
function generatepress_wc_add_to_cart_helper() {
	if ( ! generatepress_wc_get_setting( 'off_canvas_panel_on_add_to_cart' ) && ! generatepress_wc_get_setting( 'sticky_add_to_cart_panel' ) ) {
		return;
	}

	$outer_classes = array(
		'add-to-cart-panel',
	);

	$inner_classes = array(
		'inside-add-to-cart-panel',
	);

	if ( function_exists( 'generate_get_option' ) ) {
		if ( 'contained-nav' === generate_get_option( 'nav_layout_setting' ) ) {
			$outer_classes[] = 'grid-container grid-parent';
		}

		if ( 'contained' === generate_get_option( 'nav_inner_width' ) ) {
			$inner_classes[] = 'grid-container grid-parent';
		}
	}
	?>
		<div id="wc-sticky-cart-panel" class="<?php echo implode( ' ', $outer_classes ); ?>">
			<div class="<?php echo implode( ' ', $inner_classes ); ?>">

				<?php
				if ( generatepress_wc_get_setting( 'off_canvas_panel_on_add_to_cart' ) && ! is_singular( 'product' ) ) :
					$svg_icon = '';

					if ( function_exists( 'generate_get_svg_icon' ) ) {
						$svg_icon = generate_get_svg_icon( 'pro-close' );
					}
					?>
						<div class="continue-shopping <?php echo $svg_icon ? 'has-svg-icon' : ''; ?>">
							<?php echo $svg_icon; ?>
							<a href="#" class="continue-shopping-link"><span class="continue-shopping-text"><?php _e( 'Continue Shopping', 'gp-premium' ); ?> &rarr;</span></a>
						</div>

						<div class="cart-info">
							<div class="item-added">
								<?php _e( 'Item added to cart.', 'gp-premium' ); ?>
							</div>

							<div class="cart-data">
								<?php
								if ( isset( WC()->cart ) ) {
									echo sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'gp-premium' ), WC()->cart->get_cart_contents_count() ); ?> - <?php echo WC()->cart->get_cart_total();
								}
								?>
							</div>
						</div>

						<?php
							// phpcs:ignore -- No need to escape full thing.
							echo apply_filters(
								'generate_wc_cart_panel_checkout_button_output',
								sprintf(
									'<div class="checkout">
										<a href="%s" class="button">%s</a>
									</div>',
									esc_url( wc_get_checkout_url() ),
									esc_html__( 'Checkout', 'gp-premium' )
								)
							);
						?>
					<?php
				endif;

				if ( generatepress_wc_show_sticky_add_to_cart() ) :
					$product = wc_get_product( get_the_ID() );
					$quantity_buttons = '';

					if ( generatepress_wc_get_setting( 'quantity_buttons' ) ) {
						$quantity_buttons = ' do-quantity-buttons';
					}
					?>
						<div class="product-image">
							<?php the_post_thumbnail( 'thumbnail' ); ?>
						</div>

						<div class="product-title">
							<?php the_title(); ?>
						</div>

						<?php if ( $product->get_price() ) : ?>
							<div class="product-price">
								<?php echo $product->get_price_html(); ?>
							</div>
						<?php endif;

						$action = '';

						if ( $product->is_type( 'simple' ) ) {
							$args = array(
								'min_value' => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
								'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
							);

							$action = sprintf(
								'<form action="%1$s" class="cart%2$s" method="post" enctype="multipart/form-data">
									%3$s
									<button type="submit" class="button alt">%4$s</button>
								</form>',
								esc_url( $product->add_to_cart_url() ),
								$quantity_buttons,
								woocommerce_quantity_input( $args, $product, false ),
								esc_html( $product->add_to_cart_text() )
							);
						}

						if ( $product->is_type( 'variable' ) ) {
							$action = sprintf(
								'<button type="submit" class="button alt go-to-variables">%s</button>',
								esc_html( $product->add_to_cart_text() )
							);
						}

						if ( $product->is_type( 'external' ) ) {
							$action = sprintf(
								'<form action="%1$s" class="cart" method="post" enctype="multipart/form-data">
									<button type="submit" class="button alt">%2$s</button>
								</form>',
								esc_url( $product->add_to_cart_url() ),
								esc_html( $product->add_to_cart_text() )
							);
						}

						echo apply_filters( 'generate_wc_sticky_add_to_cart_action', $action, $product ); // phpcs:ignore -- No escaping needed.
				endif;
				?>

			</div>
		</div>
	<?php
}

add_filter( 'woocommerce_add_to_cart_fragments', 'generatepress_add_to_cart_panel_fragments', 10, 1 );
/**
 * Update cart totals in sticky add to cart panel.
 *
 * @since 1.8
 */
function generatepress_add_to_cart_panel_fragments( $fragments ) {
	if ( isset( WC()->cart ) ) {
		$fragments['.add-to-cart-panel .cart-data'] = '<div class="cart-data">' . sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'gp-premium' ), WC()->cart->get_cart_contents_count() ) . ' - ' .  WC()->cart->get_cart_total() . '</div>';
	}

	return $fragments;
}

/**
 * If we should display the sticky add to cart panel.
 *
 * @since 1.8
 */
function generatepress_wc_show_sticky_add_to_cart() {
	$product = wc_get_product( get_the_ID() );
	$show = false;

	if ( ! $product || ! generatepress_wc_get_setting( 'sticky_add_to_cart_panel' ) || ! is_singular( 'product' ) ) {
		return false;
	}

	if ( ( $product->is_type( 'simple' ) || $product->is_type( 'variable' ) ) && $product->is_purchasable() && $product->is_in_stock() ) {
		$show = true;
	}

	if ( $product->is_type( 'external' ) ) {
		$show = true;
	}

	return apply_filters( 'generate_wc_show_sticky_add_to_cart', $show );
}

/**
 * Checks if a color is light or dark.
 *
 * @since 1.8
 * @param string $color The color to check.
 */
function generate_premium_check_text_color( $color ) {
	// Get the hex value if we're using variables.
	if ( function_exists( 'generate_get_option' ) && strpos( $color, 'var(' ) !== false ) {
		$global_colors = generate_get_option( 'global_colors' );
		$found_color = false;

		// Remove whitespace if it's been added.
		$color = str_replace( ' ', '', $color );

		foreach ( (array) $global_colors as $key => $data ) {
			// Check for the full variable - var(--color) - or a variable with a fallback - var(--color,#fff).
			if ( 'var(--' . $data['slug'] . ')' === $color || strpos( $color, 'var(--' . $data['slug'] . ',' ) !== false ) {
				$color = $data['color'];
				$found_color = true;
				break;
			}
		}

		// If we didn't find the hex value, bail.
		if ( ! $found_color ) {
			return;
		}
	}

	$r = hexdec( substr( $color, 1, 2 ) );
	$g = hexdec( substr( $color, 3, 2 ) );
	$b = hexdec( substr( $color, 5, 2 ) );
	$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

	return ( $yiq >= 128 ) ? 'light' : 'dark';
}

add_filter( 'generate_typography_css_selector', 'generate_woocommerce_typography_selectors' );
/**
 * Add the WooCommerce typography CSS selectors.
 *
 * @since 2.1.0
 * @param string $selector The selector we're targeting.
 */
function generate_woocommerce_typography_selectors( $selector ) {
	switch ( $selector ) {
		case 'woocommerce-catalog-product-titles':
			$selector = '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title';
			break;

		case 'woocommerce-related-product-titles':
			$selector = '.woocommerce .up-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .cross-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .related ul.products li.product .woocommerce-LoopProduct-link h2';
			break;
	}

	return $selector;
}
