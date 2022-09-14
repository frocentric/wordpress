<?php
/**
 * This file handles the Spacing module functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add any necessary functions.
require_once plugin_dir_path( __FILE__ ) . 'migration.php';
require_once plugin_dir_path( __FILE__ ) . 'customizer/secondary-nav-spacing.php';

if ( ! function_exists( 'generate_spacing_customize_register' ) ) {
	add_action( 'customize_register', 'generate_spacing_customize_register', 99 );
	/**
	 * Add our spacing Customizer options
	 *
	 * @since 0.1
	 * @param object $wp_customize The Customizer object.
	 */
	function generate_spacing_customize_register( $wp_customize ) {
		// Bail if we don't have our defaults.
		if ( ! function_exists( 'generate_spacing_get_defaults' ) ) {
			return;
		}

		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		$defaults = generate_spacing_get_defaults();

		// Register our custom control types.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
			$wp_customize->register_control_type( 'GeneratePress_Spacing_Control' );
		}

		// Add our Spacing panel.
		// This is only used if the Layout panel in the free theme doesn't exist.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			if ( ! $wp_customize->get_panel( 'generate_spacing_panel' ) ) {
				$wp_customize->add_panel(
					'generate_spacing_panel',
					array(
						'capability'     => 'edit_theme_options',
						'theme_supports' => '',
						'title'          => __( 'Spacing', 'gp-premium' ),
						'description'    => __( 'Change the spacing for various elements using pixels.', 'gp-premium' ),
						'priority'       => 35,
					)
				);
			}
		}

		require_once plugin_dir_path( __FILE__ ) . 'customizer/top-bar-spacing.php';
		require_once plugin_dir_path( __FILE__ ) . 'customizer/header-spacing.php';
		require_once plugin_dir_path( __FILE__ ) . 'customizer/content-spacing.php';
		require_once plugin_dir_path( __FILE__ ) . 'customizer/sidebar-spacing.php';
		require_once plugin_dir_path( __FILE__ ) . 'customizer/navigation-spacing.php';
		require_once plugin_dir_path( __FILE__ ) . 'customizer/footer-spacing.php';

	}
}

if ( ! function_exists( 'generate_right_sidebar_width' ) ) {
	add_filter( 'generate_right_sidebar_width', 'generate_right_sidebar_width' );
	/**
	 * Set our right sidebar width.
	 *
	 * @param int $width The sidebar width.
	 */
	function generate_right_sidebar_width( $width ) {
		// Bail if we don't have our defaults.
		if ( ! function_exists( 'generate_spacing_get_defaults' ) ) {
			return $width;
		}

		$spacing_settings = wp_parse_args(
			get_option( 'generate_spacing_settings', array() ),
			generate_spacing_get_defaults()
		);

		return absint( $spacing_settings['right_sidebar_width'] );
	}
}

if ( ! function_exists( 'generate_left_sidebar_width' ) ) {
	add_filter( 'generate_left_sidebar_width', 'generate_left_sidebar_width' );
	/**
	 * Set our left sidebar width.
	 *
	 * @param int $width The sidebar width.
	 */
	function generate_left_sidebar_width( $width ) {
		// Bail if we don't have our defaults.
		if ( ! function_exists( 'generate_spacing_get_defaults' ) ) {
			return $width;
		}

		$spacing_settings = wp_parse_args(
			get_option( 'generate_spacing_settings', array() ),
			generate_spacing_get_defaults()
		);

		return absint( $spacing_settings['left_sidebar_width'] );
	}
}

if ( ! function_exists( 'generate_spacing_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'generate_spacing_customizer_live_preview' );
	/**
	 * Add our live preview JS
	 */
	function generate_spacing_customizer_live_preview() {
		wp_enqueue_script(
			'generate-spacing-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'customizer/js/customizer.js',
			array( 'jquery', 'customize-preview' ),
			GENERATE_SPACING_VERSION,
			true
		);

		wp_localize_script(
			'generate-spacing-customizer',
			'gp_spacing',
			array(
				'mobile' => generate_premium_get_media_query( 'mobile' ),
				'tablet' => generate_premium_get_media_query( 'tablet' ),
				'desktop' => generate_premium_get_media_query( 'desktop' ),
				'isFlex' => function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox(),
			)
		);
	}
}

if ( ! function_exists( 'generate_include_spacing_defaults' ) ) {
	/**
	 * Check if we should include our default.css file.
	 *
	 * @since 1.3.42
	 */
	function generate_include_spacing_defaults() {
		return true;
	}
}

if ( ! function_exists( 'generate_spacing_premium_defaults' ) ) {
	add_filter( 'generate_spacing_option_defaults', 'generate_spacing_premium_defaults' );
	/**
	 * Add premium spacing defaults.
	 *
	 * @since 1.3
	 * @param array $defaults The existing defaults.
	 */
	function generate_spacing_premium_defaults( $defaults ) {
		$defaults['mobile_menu_item'] = '';
		$defaults['mobile_menu_item_height'] = '';
		$defaults['sticky_menu_item_height'] = '';
		$defaults['off_canvas_menu_item_height'] = '';
		$defaults['content_element_separator'] = '2'; // em.

		// These defaults were added to GeneratePress (free) in 3.0.0.
		if ( defined( 'GENERATE_VERSION' ) && version_compare( GENERATE_VERSION, '3.0.0-alpha.1', '<' ) ) {
			$defaults['mobile_header_top'] = '';
			$defaults['mobile_header_right'] = '';
			$defaults['mobile_header_bottom'] = '';
			$defaults['mobile_header_left'] = '';

			$defaults['mobile_widget_top'] = '';
			$defaults['mobile_widget_right'] = '';
			$defaults['mobile_widget_bottom'] = '';
			$defaults['mobile_widget_left'] = '';

			$defaults['mobile_footer_widget_container_top'] = '';
			$defaults['mobile_footer_widget_container_right'] = '';
			$defaults['mobile_footer_widget_container_bottom'] = '';
			$defaults['mobile_footer_widget_container_left'] = '';
		}

		return $defaults;
	}
}

/**
 * Build our premium CSS.
 */
function generate_spacing_do_premium_css() {
	// Bail if we don't have our defaults.
	if ( ! function_exists( 'generate_spacing_get_defaults' ) ) {
		return;
	}

	$spacing_settings = wp_parse_args(
		get_option( 'generate_spacing_settings', array() ),
		generate_spacing_get_defaults()
	);

	require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
	$premium_css = new GeneratePress_Pro_CSS();
	$css_output = '';

	// Mobile spacing.
	$premium_css->start_media_query( generate_premium_get_media_query( 'mobile-menu' ) );

	if ( '' !== $spacing_settings['mobile_menu_item'] ) {
		$premium_css->set_selector( '.main-navigation .menu-toggle,.main-navigation .mobile-bar-items a,.main-navigation .menu-bar-item > a' );
		$premium_css->add_property( 'padding-left', absint( $spacing_settings['mobile_menu_item'] ), false, 'px' );
		$premium_css->add_property( 'padding-right', absint( $spacing_settings['mobile_menu_item'] ), false, 'px' );
	}

	if ( '' !== $spacing_settings['mobile_menu_item_height'] ) {
		$premium_css->set_selector( '.main-navigation .main-nav ul li a,.main-navigation .menu-toggle,.main-navigation .mobile-bar-items a,.main-navigation .menu-bar-item > a' );
		$premium_css->add_property( 'line-height', absint( $spacing_settings['mobile_menu_item_height'] ), false, 'px' );

		$premium_css->set_selector( '.main-navigation .site-logo.navigation-logo img, .mobile-header-navigation .site-logo.mobile-header-logo img, .navigation-search input[type="search"]' );
		$premium_css->add_property( 'height', absint( $spacing_settings['mobile_menu_item_height'] ), false, 'px' );
	}

	$premium_css->stop_media_query();

	// This CSS was added to GeneratePress (free) in 3.0.0.
	if ( defined( 'GENERATE_VERSION' ) && version_compare( GENERATE_VERSION, '3.0.0-alpha.1', '<' ) ) {
		$premium_css->start_media_query( generate_premium_get_media_query( 'mobile' ) );

		$premium_css->set_selector( '.inside-header' );

		if ( '' !== $spacing_settings['mobile_header_top'] ) {
			$premium_css->add_property( 'padding-top', absint( $spacing_settings['mobile_header_top'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_header_right'] ) {
			$premium_css->add_property( 'padding-right', absint( $spacing_settings['mobile_header_right'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_header_bottom'] ) {
			$premium_css->add_property( 'padding-bottom', absint( $spacing_settings['mobile_header_bottom'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_header_left'] ) {
			$premium_css->add_property( 'padding-left', absint( $spacing_settings['mobile_header_left'] ), false, 'px' );
		}

		$premium_css->set_selector( '.widget-area .widget' );

		if ( '' !== $spacing_settings['mobile_widget_top'] ) {
			$premium_css->add_property( 'padding-top', absint( $spacing_settings['mobile_widget_top'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_widget_right'] ) {
			$premium_css->add_property( 'padding-right', absint( $spacing_settings['mobile_widget_right'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_widget_bottom'] ) {
			$premium_css->add_property( 'padding-bottom', absint( $spacing_settings['mobile_widget_bottom'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_widget_left'] ) {
			$premium_css->add_property( 'padding-left', absint( $spacing_settings['mobile_widget_left'] ), false, 'px' );
		}

		$premium_css->set_selector( '.footer-widgets' );

		if ( '' !== $spacing_settings['mobile_footer_widget_container_top'] ) {
			$premium_css->add_property( 'padding-top', absint( $spacing_settings['mobile_footer_widget_container_top'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_footer_widget_container_right'] ) {
			$premium_css->add_property( 'padding-right', absint( $spacing_settings['mobile_footer_widget_container_right'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_footer_widget_container_bottom'] ) {
			$premium_css->add_property( 'padding-bottom', absint( $spacing_settings['mobile_footer_widget_container_bottom'] ), false, 'px' );
		}

		if ( '' !== $spacing_settings['mobile_footer_widget_container_left'] ) {
			$premium_css->add_property( 'padding-left', absint( $spacing_settings['mobile_footer_widget_container_left'] ), false, 'px' );
		}

		$premium_css->stop_media_query();

		$premium_css->set_selector( '.post-image, .page-content, .entry-content, .entry-summary, footer.entry-meta' );
		$premium_css->add_property( 'margin-top', floatval( $spacing_settings['content_element_separator'] ), '2', 'em' );
	} else {
		$premium_css->set_selector( '.post-image:not(:first-child), .page-content:not(:first-child), .entry-content:not(:first-child), .entry-summary:not(:first-child), footer.entry-meta' );
		$premium_css->add_property( 'margin-top', floatval( $spacing_settings['content_element_separator'] ), '2', 'em' );
	}

	$premium_css->set_selector( '.post-image-above-header .inside-article div.featured-image, .post-image-above-header .inside-article div.post-image' );
	$premium_css->add_property( 'margin-bottom', floatval( $spacing_settings['content_element_separator'] ), '2', 'em' );

	if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
		$menu_plus = wp_parse_args(
			get_option( 'generate_menu_plus_settings', array() ),
			generate_menu_plus_get_defaults()
		);

		if ( 'false' !== $menu_plus['sticky_menu'] && '' !== $spacing_settings['sticky_menu_item_height'] ) {
			$premium_css->start_media_query( generate_premium_get_media_query( 'tablet' ) . ',' . generate_premium_get_media_query( 'desktop' ) );

				if ( function_exists( 'generate_is_using_flexbox' ) && generate_is_using_flexbox() ) {
					$premium_css->set_selector( '.main-navigation.sticky-navigation-transition .main-nav > ul > li > a,.sticky-navigation-transition .menu-toggle,.main-navigation.sticky-navigation-transition .menu-bar-item > a, .sticky-navigation-transition .navigation-branding .main-title' );
				} else {
					$premium_css->set_selector( '.main-navigation.sticky-navigation-transition .main-nav > ul > li > a,.sticky-navigation-transition .menu-toggle,.main-navigation.sticky-navigation-transition .mobile-bar-items a, .sticky-navigation-transition .navigation-branding .main-title' );
				}

				$premium_css->add_property( 'line-height', absint( $spacing_settings['sticky_menu_item_height'] ), false, 'px' );

				$premium_css->set_selector( '.main-navigation.sticky-navigation-transition .site-logo img, .main-navigation.sticky-navigation-transition .navigation-search input[type="search"], .main-navigation.sticky-navigation-transition .navigation-branding img' );
				$premium_css->add_property( 'height', absint( $spacing_settings['sticky_menu_item_height'] ), false, 'px' );

			$premium_css->stop_media_query();
		}

		if ( 'false' !== $menu_plus['slideout_menu'] ) {
			$premium_css->set_selector( '.main-navigation.slideout-navigation .main-nav > ul > li > a' );
			if ( '' !== $spacing_settings['off_canvas_menu_item_height'] ) {
				$premium_css->add_property( 'line-height', absint( $spacing_settings['off_canvas_menu_item_height'] ), false, 'px' );
			}
		}
	}

	if ( '' !== $premium_css->css_output() ) {
		$css_output = $premium_css->css_output();
	}

	return $css_output;
}

if ( ! function_exists( 'generate_spacing_premium_css' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_spacing_premium_css', 105 );
	/**
	 * Add premium spacing CSS
	 *
	 * @since 1.3
	 */
	function generate_spacing_premium_css() {
		$css = generate_spacing_do_premium_css();

		if ( 'inline' === generate_get_css_print_method() && $css ) {
			wp_add_inline_style( 'generate-style', $css );
		}
	}
}

add_filter( 'generate_external_dynamic_css_output', 'generate_spacing_add_to_external_stylesheet' );
/**
 * Add CSS to the external stylesheet.
 *
 * @since 1.11.0
 * @param string $css Existing CSS.
 */
function generate_spacing_add_to_external_stylesheet( $css ) {
	if ( 'inline' === generate_get_css_print_method() ) {
		return $css;
	}

	$css .= generate_spacing_do_premium_css();

	return $css;
}
