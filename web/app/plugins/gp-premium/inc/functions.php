<?php
/**
 * This file handles general functions in the plugin.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Get the requested media query.
 *
 * @since 1.9.0
 * @param string $name Name of the media query.
 */
function generate_premium_get_media_query( $name ) {
	if ( function_exists( 'generate_get_media_query' ) ) {
		return generate_get_media_query( $name );
	}

	// If the theme function doesn't exist, build our own queries.
	$desktop = apply_filters( 'generate_desktop_media_query', '(min-width:1025px)' );
	$tablet = apply_filters( 'generate_tablet_media_query', '(min-width: 769px) and (max-width: 1024px)' );
	$mobile = apply_filters( 'generate_mobile_media_query', '(max-width:768px)' );
	$mobile_menu = apply_filters( 'generate_mobile_menu_media_query', $mobile );

	$queries = apply_filters(
		'generate_media_queries',
		array(
			'desktop'     => $desktop,
			'tablet'      => $tablet,
			'mobile'      => $mobile,
			'mobile-menu' => $mobile_menu,
		)
	);

	return $queries[ $name ];
}

/**
 * Get our CSS print method.
 *
 * @since 1.11.0
 */
function generate_get_css_print_method() {
	$mode = apply_filters( 'generatepress_dynamic_css_print_method', 'inline' );

	if (
		( function_exists( 'is_customize_preview' ) && is_customize_preview() )
		||
		is_preview()
		||
		// AMP inlines all CSS, so inlining from the start improves CSS processing performance.
		( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() )
	) {
		return 'inline';
	}

	if ( ! defined( 'GENERATE_VERSION' ) ) {
		return 'inline';
	}

	return $mode;
}

/**
 * Check to see if we have a Block Element.
 *
 * @since 2.0.0
 * @param string  $element The type of element to check.
 * @param boolean $block_element Whether it's a block element or not.
 */
function generate_has_active_element( $element, $block_element ) {
	global $generate_elements;

	if ( ! empty( $generate_elements ) ) {
		foreach ( (array) $generate_elements as $key => $data ) {
			if ( $element === $data['type'] && $block_element === $data['is_block_element'] ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Check our GeneratePress version.
 *
 * @since 2.1.0
 */
function generate_premium_get_theme_version() {
	return defined( 'GENERATE_VERSION' ) ? GENERATE_VERSION : false;
}

/**
 * Remove the featured-image-active class if needed.
 *
 * @since 2.1.0
 */
function generate_premium_remove_featured_image_class( $classes, $remove_class ) {
	if ( $remove_class && in_array( 'featured-image-active', $classes ) ) {
		$classes = array_diff( $classes, array( 'featured-image-active' ) );
	}

	return $classes;
}

/**
 * Returns the global $wp_filesystem with credentials set.
 * Returns null in case of any errors.
 *
 * @return WP_Filesystem_Base|null
 */
function generate_premium_get_wp_filesystem() {
	global $wp_filesystem;

	$success = true;

	// Initialize the file system if it has not been done yet.
	if ( ! $wp_filesystem ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';

		$constants = array(
			'hostname'    => 'FTP_HOST',
			'username'    => 'FTP_USER',
			'password'    => 'FTP_PASS',
			'public_key'  => 'FTP_PUBKEY',
			'private_key' => 'FTP_PRIKEY',
		);

		$credentials = array();

		// We provide credentials based on wp-config.php constants.
		// Reference https://developer.wordpress.org/apis/wp-config-php/#wordpress-upgrade-constants.
		foreach ( $constants as $key => $constant ) {
			if ( defined( $constant ) ) {
				$credentials[ $key ] = constant( $constant );
			}
		}

		$success = WP_Filesystem( $credentials );
	}

	if ( ! $success || $wp_filesystem->errors->has_errors() ) {
		return null;
	}

	return $wp_filesystem;
}
