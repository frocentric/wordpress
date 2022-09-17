<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_mobile_header_activated' ) ) {
	/**
	 * Check to see if the mobile header is activated
	 */
	function generate_mobile_header_activated() {
		if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
			return false;
		}

		$generate_menu_plus_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		return ( 'enable' == $generate_menu_plus_settings[ 'mobile_header' ] ) ? true : false;
	}
}

/**
 * If we're using the mobile header, and have the logo set as the branding type.
 *
 * @since 1.8
 */
function generate_mobile_header_logo_active_callback() {
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}

	$settings = wp_parse_args(
		get_option( 'generate_menu_plus_settings', array() ),
		generate_menu_plus_get_defaults()
	);

	return ( 'enable' === $settings['mobile_header'] && 'logo' === $settings['mobile_header_branding'] ) ? true : false;
}

if ( ! function_exists( 'generate_mobile_header_sticky_activated' ) ) {
	/**
	 * Check to see if the mobile header is activated
	 */
	function generate_mobile_header_sticky_activated() {
		if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
			return false;
		}

		$generate_menu_plus_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		return ( 'enable' == $generate_menu_plus_settings[ 'mobile_header' ] && 'enable' == $generate_menu_plus_settings[ 'mobile_header_sticky' ] ) ? true : false;
	}
}

if ( ! function_exists( 'generate_sticky_navigation_activated' ) ) {
	/**
	 * Check to see if the sticky navigation is activated
	 */
	function generate_sticky_navigation_activated() {
		if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
			return false;
		}

		$generate_menu_plus_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		return ( 'false' !== $generate_menu_plus_settings[ 'sticky_menu' ] ) ? true : false;
	}
}

if ( ! function_exists( 'generate_navigation_logo_activated' ) ) {
	/**
	 * Check to see if the sticky navigation is activated
	 */
	function generate_navigation_logo_activated() {
		if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
			return false;
		}

		$generate_menu_plus_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		return ( '' !== $generate_menu_plus_settings[ 'sticky_menu_logo' ] ) ? true : false;
	}
}

if ( ! function_exists( 'generate_slideout_navigation_activated' ) ) {
	/**
	 * Check to see if the sticky navigation is activated
	 */
	function generate_slideout_navigation_activated() {
		if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
			return false;
		}

		$generate_menu_plus_settings = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		return ( 'false' !== $generate_menu_plus_settings[ 'slideout_menu' ] ) ? true : false;
	}
}

/**
 * Check whether the overlay off canvas panel is activated.
 *
 * @since 1.8
 */
function generate_is_overlay_navigation_active_callback() {
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}

	$settings = wp_parse_args(
		get_option( 'generate_menu_plus_settings', array() ),
		generate_menu_plus_get_defaults()
	);

	return ( 'false' !== $settings['slideout_menu'] && 'overlay' === $settings['slideout_menu_style'] ) ? true : false;
}

/**
 * Check whether the slideout off canvas panel is activated.
 *
 * @since 1.8
 */
function generate_is_slideout_navigation_active_callback() {
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}

	$settings = wp_parse_args(
		get_option( 'generate_menu_plus_settings', array() ),
		generate_menu_plus_get_defaults()
	);

	return ( 'false' !== $settings['slideout_menu'] && 'slide' === $settings['slideout_menu_style'] ) ? true : false;
}

if ( ! function_exists( 'generate_page_header_blog_content_exists' ) ) {
	/**
	 * This is an active_callback
	 * Check if page header content exists
	 */
	function generate_page_header_blog_content_exists() {
		if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
			return false;
		}

		$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
		if ( isset( $options[ 'page_header_content' ] ) && '' !== $options[ 'page_header_content' ] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_page_header_blog_image_exists' ) ) {
	/**
	 * This is an active_callback
	 * Check if page header image exists
	 */
	function generate_page_header_blog_image_exists() {
		if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
			return false;
		}

		$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
		if ( isset( $options[ 'page_header_image' ] ) && '' !== $options[ 'page_header_image' ] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_page_header_blog_crop_exists' ) ) {
	/**
	 * This is an active_callback
	 * Check if page header image resizing is enabled
	 */
	function generate_page_header_blog_crop_exists() {
		if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
			return false;
		}

		$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );

		if ( isset( $options[ 'page_header_hard_crop' ] ) && 'disable' !== $options[ 'page_header_hard_crop' ] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_page_header_blog_combined' ) ) {
	/**
	 * This is an active_callback
	 * Check if page header is merged
	 */
	function generate_page_header_blog_combined() {
		if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
			return false;
		}

		$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
		if ( isset( $options[ 'page_header_combine' ] ) && '' !== $options[ 'page_header_combine' ] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_page_header_full_screen_vertical' ) ) {
	/**
	 * This is an active_callback
	 * Check if our page header is full screen and vertically centered
	 */
	function generate_page_header_full_screen_vertical() {
		if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
			return false;
		}

		$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );

		if ( $options[ 'page_header_full_screen' ] && $options[ 'page_header_vertical_center' ] ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_secondary_nav_show_merge_top_bar' ) ) {
	/**
	 * This is an active callback
	 * Determines whether we should show the Merge with Secondary Navigation option
	 */
	function generate_secondary_nav_show_merge_top_bar() {
		if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
			return false;
		}

		$generate_settings = wp_parse_args(
			get_option( 'generate_secondary_nav_settings', array() ),
			generate_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-above-header' == $generate_settings[ 'secondary_nav_position_setting' ] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'generate_premium_is_top_bar_active' ) ) {
	/**
	 * Check to see if the top bar is active
	 *
	 * @since 1.3.45
	 */
	function generate_premium_is_top_bar_active() {
		$top_bar = is_active_sidebar( 'top-bar' ) ? true : false;
		return apply_filters( 'generate_is_top_bar_active', $top_bar );
	}
}

if ( ! function_exists( 'generate_masonry_callback' ) ) {
	/**
	 * Check to see if masonry is activated
	 */
	function generate_masonry_callback() {
		if ( ! function_exists( 'generate_blog_get_defaults' ) ) {
			return false;
		}

		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		// If masonry is enabled, set to true
		return ( 'true' == $generate_blog_settings['masonry'] ) ? true : false;

	}
}

if ( ! function_exists( 'generate_premium_is_posts_page' ) ) {
	/**
	 * Check to see if we're on a posts page
	 */
	function generate_premium_is_posts_page() {
		$blog = ( is_home() || is_archive() || is_attachment() || is_tax() ) ? true : false;

		return $blog;
	}
}

if ( ! function_exists( 'generate_premium_is_posts_page_single' ) ) {
	/**
	 * Check to see if we're on a posts page or a single post
	 */
	function generate_premium_is_posts_page_single() {
		$blog = ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) ? true : false;

		return $blog;
	}
}

if ( ! function_exists( 'generate_premium_is_excerpt' ) ) {
	/**
	 * Check to see if we're displaying excerpts
	 */
	function generate_premium_is_excerpt() {
		if ( ! function_exists( 'generate_get_defaults' ) ) {
			return false;
		}

		$generate_settings = wp_parse_args(
			get_option( 'generate_settings', array() ),
			generate_get_defaults()
		);

		return ( 'excerpt' == $generate_settings['post_content'] ) ? true : false;
	}
}

/**
 * Check to see if featured images are active.
 *
 * @since 1.5
 * @return bool Whether featured images are active or not
 */
function generate_premium_featured_image_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'post_image' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if featured images on single posts are active.
 *
 * @since 1.5
 * @return bool Whether featured images on single posts are active or not.
 */
function generate_premium_single_featured_image_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'single_post_image' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if featured images on single posts are active.
 *
 * @since 1.5
 * @return bool Whether featured images on single posts are active or not.
 */
function generate_premium_single_page_featured_image_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'page_post_image' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if the blog columns Customizer control is true.
 *
 * @since 1.5
 * @return bool Whether columns are active or not
 */
function generate_premium_blog_columns_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'column_layout' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if the blog masonry Customizer control is true.
 *
 * @since 1.5
 * @return bool Whether masonry is active or not
 */
function generate_premium_blog_masonry_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'column_layout' ] ) {
		return false;
	}

	if ( ! $settings[ 'masonry' ] ) {
		return false;
	}

	return true;
}

/**
 * Only show padding around image control when alignment is centered.
 *
 * @since 1.5
 * @return bool
 */
function generate_premium_display_image_padding() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'post_image' ] ) {
		return false;
	}

	if ( 'post-image-aligned-center' !== $settings[ 'post_image_alignment' ] ) {
		return false;
	}

	return true;
}

/**
 * Only show padding around image control when alignment is centered and not
 * set to display above our content area.
 *
 * @since 1.5
 * @return bool
 */
function generate_premium_display_image_padding_single() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'single_post_image' ] ) {
		return false;
	}

	if ( 'center' !== $settings[ 'single_post_image_alignment' ] ) {
		return false;
	}

	if ( 'above-content' == $settings[ 'single_post_image_position' ] ) {
		return false;
	}

	return true;
}

/**
 * Only show padding around image control when alignment is centered and not
 * set to display above our content area.
 *
 * @since 1.5
 * @return bool
 */
function generate_premium_display_image_padding_single_page() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'page_post_image' ] ) {
		return false;
	}

	if ( 'center' !== $settings[ 'page_post_image_alignment' ] ) {
		return false;
	}

	if ( 'above-content' == $settings[ 'page_post_image_position' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if infinite scroll is activated.
 *
 * @since 1.5
 * @return bool
 */
function generate_premium_infinite_scroll_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'infinite_scroll' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if infinite scroll is activated and we're using a button.
 *
 * @since 1.5
 * @return bool
 */
function generate_premium_infinite_scroll_button_active() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings[ 'infinite_scroll' ] ) {
		return false;
	}

	if ( ! $settings[ 'infinite_scroll_button' ] ) {
		return false;
	}

	return true;
}

/**
 * Check to see if the WooCommerce menu item is active.
 *
 * @since 1.8
 * @return bool Whether the cart item is active.
 */
function generate_premium_wc_menu_item_active() {
	if ( ! function_exists( 'generatepress_wc_defaults' ) ) {
		return false;
	}

	$settings = wp_parse_args(
		get_option( 'generate_woocommerce_settings', array() ),
		generatepress_wc_defaults()
	);

	if ( ! $settings[ 'cart_menu_item' ] ) {
		return false;
	}

	return true;
}

/**
 * Checks to see if we're using external CSS file.
 *
 * @since 1.11.0
 */
function generate_is_using_external_css_file_callback() {
	if ( ! function_exists( 'generate_get_option' ) ) {
		return false;
	}

	if ( 'file' === generate_get_option( 'css_print_method' ) ) {
		return true;
	}

	return false;
}
