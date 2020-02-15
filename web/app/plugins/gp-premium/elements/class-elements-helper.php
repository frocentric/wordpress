<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

class GeneratePress_Elements_Helper {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.7
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.7
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check to see if specific theme/GPP options exist and are set.
	 *
	 * @since 1.7
	 *
	 * @return bool
	 */
	public static function does_option_exist( $option ) {
		if ( function_exists( 'generate_get_defaults' ) ) {
			$theme_settings = wp_parse_args(
				get_option( 'generate_settings', array() ),
				generate_get_defaults()
			);

			if ( 'site-title' === $option ) {
				return $theme_settings['hide_title'] ? false : true;
			}

			if ( 'site-tagline' === $option ) {
				return $theme_settings['hide_tagline'] ? false : true;
			}

			if ( 'retina-logo' === $option ) {
				return $theme_settings['retina_logo'];
			}
		}

		if ( 'site-logo' === $option ) {
			return get_theme_mod( 'custom_logo' );
		}

		if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
			$menu_settings = wp_parse_args(
				get_option( 'generate_menu_plus_settings', array() ),
				generate_menu_plus_get_defaults()
			);

			if ( 'navigation-as-header' === $option ) {
				return $menu_settings['navigation_as_header'];
			}

			if ( 'mobile-logo' === $option ) {
				return $menu_settings['mobile_header_logo'];
			}

			if ( 'navigation-logo' === $option ) {
				return $menu_settings['sticky_menu_logo'];
			}

			if ( 'sticky-navigation' === $option ) {
				return 'false' !== $menu_settings['sticky_menu'] ? true : false;
			}

			if ( 'sticky-navigation-logo' === $option ) {
				return $menu_settings['sticky_navigation_logo'];
			}

			if ( 'mobile-header-branding' === $option ) {
				return $menu_settings['mobile_header_branding'];
			}

			if ( 'sticky-mobile-header' === $option ) {
				return 'disable' !== $menu_settings['mobile_header_sticky'] ? true : false;
			}
		}

		return false;
	}

	public static function should_execute_php() {
		$php = true;

		if ( defined( 'DISALLOW_FILE_EDIT' ) ) {
			$php = false;
		}

		return apply_filters( 'generate_hooks_execute_php', $php );
	}
}
