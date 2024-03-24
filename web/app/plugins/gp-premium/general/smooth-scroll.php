<?php
/**
 * This file handles the smooth scroll functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'wp_enqueue_scripts', 'generate_smooth_scroll_scripts' );
/**
 * Add the smooth scroll script if enabled.
 *
 * @since 1.6
 */
function generate_smooth_scroll_scripts() {
	if ( ! function_exists( 'generate_get_defaults' ) ) {
		return;
	}

	$settings = wp_parse_args(
		get_option( 'generate_settings', array() ),
		generate_get_defaults()
	);

	if ( ! $settings['smooth_scroll'] ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'generate-smooth-scroll', plugin_dir_url( __FILE__ ) . "js/smooth-scroll{$suffix}.js", array(), GP_PREMIUM_VERSION, true );

	wp_localize_script(
		'generate-smooth-scroll',
		'gpSmoothScroll',
		array(
			'elements' => apply_filters(
				'generate_smooth_scroll_elements',
				array(
					'.smooth-scroll',
					'li.smooth-scroll a',
				)
			),
			'duration' => apply_filters( 'generate_smooth_scroll_duration', 800 ),
			'offset' => apply_filters( 'generate_smooth_scroll_offset', '' ),
		)
	);
}

add_filter( 'generate_option_defaults', 'generate_smooth_scroll_default' );
/**
 * Add the smooth scroll option to our defaults.
 *
 * @since 1.6
 *
 * @param array $defaults Existing defaults.
 * @return array New defaults.
 */
function generate_smooth_scroll_default( $defaults ) {
	$defaults['smooth_scroll'] = false;

	return $defaults;
}

add_action( 'customize_register', 'generate_smooth_scroll_customizer', 99 );
/**
 * Add our smooth scroll option to the Customizer.
 *
 * @since 1.6
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function generate_smooth_scroll_customizer( $wp_customize ) {
	if ( ! function_exists( 'generate_get_defaults' ) ) {
		return;
	}

	$defaults = generate_get_defaults();

	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	$wp_customize->add_setting(
		'generate_settings[smooth_scroll]',
		array(
			'default' => $defaults['smooth_scroll'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'generate_settings[smooth_scroll]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Smooth scroll', 'gp-premium' ),
			'description' => __( 'Initiate smooth scroll on anchor links using the <code>smooth-scroll</code> class.', 'gp-premium' ),
			'section' => 'generate_general_section',
		)
	);
}
