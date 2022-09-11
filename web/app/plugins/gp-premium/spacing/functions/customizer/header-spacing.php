<?php
/**
 * This file handles the header spacing Customizer options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our old header section.
$wp_customize->add_section(
	'generate_spacing_header',
	array(
		'title' => __( 'Header', 'gp-premium' ),
		'capability' => 'edit_theme_options',
		'priority' => 5,
		'panel' => 'generate_spacing_panel',
	)
);

// If we don't have a layout panel, use our old spacing section.
$header_section = ( $wp_customize->get_panel( 'generate_layout_panel' ) ) ? 'generate_layout_header' : 'generate_spacing_header';

// Header top.
$wp_customize->add_setting(
	'generate_spacing_settings[header_top]',
	array(
		'default' => $defaults['header_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header right.
$wp_customize->add_setting(
	'generate_spacing_settings[header_right]',
	array(
		'default' => $defaults['header_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[header_bottom]',
	array(
		'default' => $defaults['header_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header left.
$wp_customize->add_setting(
	'generate_spacing_settings[header_left]',
	array(
		'default' => $defaults['header_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_setting(
	'generate_spacing_settings[mobile_header_top]',
	array(
		'default' => $defaults['mobile_header_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header right.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_header_right]',
	array(
		'default' => $defaults['mobile_header_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_header_bottom]',
	array(
		'default' => $defaults['mobile_header_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header left.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_header_left]',
	array(
		'default' => $defaults['mobile_header_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Do something with our header controls.
$wp_customize->add_control(
	new GeneratePress_Spacing_Control(
		$wp_customize,
		'header_spacing',
		array(
			'type' => 'generatepress-spacing',
			'label'       => esc_html__( 'Header Padding', 'gp-premium' ),
			'section'     => $header_section,
			'settings'    => array(
				'desktop_top'    => 'generate_spacing_settings[header_top]',
				'desktop_right'  => 'generate_spacing_settings[header_right]',
				'desktop_bottom' => 'generate_spacing_settings[header_bottom]',
				'desktop_left'   => 'generate_spacing_settings[header_left]',
				'mobile_top'     => 'generate_spacing_settings[mobile_header_top]',
				'mobile_right'   => 'generate_spacing_settings[mobile_header_right]',
				'mobile_bottom'  => 'generate_spacing_settings[mobile_header_bottom]',
				'mobile_left'    => 'generate_spacing_settings[mobile_header_left]',
			),
			'element' => 'header',
		)
	)
);
