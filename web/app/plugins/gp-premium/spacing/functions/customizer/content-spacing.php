<?php
/**
 * This file handles the content spacing Customizer options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our old Spacing section.
$wp_customize->add_section(
	'generate_spacing_content',
	array(
		'title' => __( 'Content', 'gp-premium' ),
		'capability' => 'edit_theme_options',
		'priority' => 10,
		'panel' => 'generate_spacing_panel',
	)
);

// If we don't have a layout panel, use our old spacing section.
if ( $wp_customize->get_panel( 'generate_layout_panel' ) ) {
	$content_section = 'generate_layout_container';
} else {
	$content_section = 'generate_spacing_content';
}

// Take control of the container width control.
// This control is handled by the free theme, but we take control of it here for consistency between control styles.
$wp_customize->add_control(
	new GeneratePress_Pro_Range_Slider_Control(
		$wp_customize,
		'generate_settings[container_width]',
		array(
			'label' => __( 'Container Width', 'gp-premium' ),
			'section' => 'generate_layout_container',
			'settings' => array(
				'desktop' => 'generate_settings[container_width]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 700,
					'max' => 2000,
					'step' => 5,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 0,
		)
	)
);

// Separating space.
$wp_customize->add_setting(
	'generate_spacing_settings[separator]',
	array(
		'default' => $defaults['separator'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new GeneratePress_Pro_Range_Slider_Control(
		$wp_customize,
		'generate_spacing_settings[separator]',
		array(
			'label' => __( 'Separating Space', 'gp-premium' ),
			'section' => $content_section,
			'settings' => array(
				'desktop' => 'generate_spacing_settings[separator]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 0,
					'max' => 100,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
		)
	)
);

// Content padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[content_top]',
	array(
		'default' => $defaults['content_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Content padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[content_right]',
	array(
		'default' => $defaults['content_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Content padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[content_bottom]',
	array(
		'default' => $defaults['content_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Content padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[content_left]',
	array(
		'default' => $defaults['content_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$content_padding_settings = array(
	'desktop_top'    => 'generate_spacing_settings[content_top]',
	'desktop_right'  => 'generate_spacing_settings[content_right]',
	'desktop_bottom' => 'generate_spacing_settings[content_bottom]',
	'desktop_left'   => 'generate_spacing_settings[content_left]',
);

// If mobile_content_top is set, the rest of them are too.
// We have to check as these defaults are set in the theme.
if ( isset( $defaults['mobile_content_top'] ) ) {
	$content_padding_settings['mobile_top'] = 'generate_spacing_settings[mobile_content_top]';
	$content_padding_settings['mobile_right'] = 'generate_spacing_settings[mobile_content_right]';
	$content_padding_settings['mobile_bottom'] = 'generate_spacing_settings[mobile_content_bottom]';
	$content_padding_settings['mobile_left'] = 'generate_spacing_settings[mobile_content_left]';

	// Mobile content padding top.
	$wp_customize->add_setting(
		'generate_spacing_settings[mobile_content_top]',
		array(
			'default' => $defaults['mobile_content_top'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	// Content padding right.
	$wp_customize->add_setting(
		'generate_spacing_settings[mobile_content_right]',
		array(
			'default' => $defaults['mobile_content_right'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	// Content padding bottom.
	$wp_customize->add_setting(
		'generate_spacing_settings[mobile_content_bottom]',
		array(
			'default' => $defaults['mobile_content_bottom'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	// Content padding left.
	$wp_customize->add_setting(
		'generate_spacing_settings[mobile_content_left]',
		array(
			'default' => $defaults['mobile_content_left'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);
}

// Make use of the content padding settings.
$wp_customize->add_control(
	new GeneratePress_Spacing_Control(
		$wp_customize,
		'content_spacing',
		array(
			'type'     => 'generatepress-spacing',
			'label'    => esc_html__( 'Content Padding', 'gp-premium' ),
			'section'  => $content_section,
			'settings' => $content_padding_settings,
			'element'  => 'content',
			'priority' => 99,
		)
	)
);

$wp_customize->add_setting(
	'generate_spacing_settings[content_element_separator]',
	array(
		'default' => $defaults['content_element_separator'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new GeneratePress_Pro_Range_Slider_Control(
		$wp_customize,
		'generate_spacing_settings[content_element_separator]',
		array(
			'label' => __( 'Content Separator', 'gp-premium' ),
			'sub_description' => __( 'The content separator controls the space between the featured image, title, content and entry meta.', 'gp-premium' ),
			'section' => $content_section,
			'settings' => array(
				'desktop' => 'generate_spacing_settings[content_element_separator]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 0,
					'max' => 10,
					'step' => .1,
					'edit' => true,
					'unit' => 'em',
				),
			),
		)
	)
);
