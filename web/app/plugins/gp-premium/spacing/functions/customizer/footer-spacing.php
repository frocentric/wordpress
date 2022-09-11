<?php
/**
 * This file handles the footer spacing Customizer options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our section.
// This isn't used anymore if the Layout panel exists.
$wp_customize->add_section(
	'generate_spacing_footer',
	array(
		'title' => __( 'Footer', 'gp-premium' ),
		'capability' => 'edit_theme_options',
		'priority' => 20,
		'panel' => 'generate_spacing_panel',
	)
);

// Use our layout panel if it exists.
if ( $wp_customize->get_panel( 'generate_layout_panel' ) ) {
	$footer_section = 'generate_layout_footer';
} else {
	$footer_section = 'generate_spacing_footer';
}

// Footer widget area padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_widget_container_top]',
	array(
		'default' => $defaults['footer_widget_container_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_widget_container_right]',
	array(
		'default' => $defaults['footer_widget_container_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_widget_container_bottom]',
	array(
		'default' => $defaults['footer_widget_container_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_widget_container_left]',
	array(
		'default' => $defaults['footer_widget_container_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_footer_widget_container_top]',
	array(
		'default' => $defaults['mobile_footer_widget_container_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_footer_widget_container_right]',
	array(
		'default' => $defaults['mobile_footer_widget_container_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_footer_widget_container_bottom]',
	array(
		'default' => $defaults['mobile_footer_widget_container_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer widget area padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_footer_widget_container_left]',
	array(
		'default' => $defaults['mobile_footer_widget_container_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Make use of the Footer widget area padding settings.
$wp_customize->add_control(
	new GeneratePress_Spacing_Control(
		$wp_customize,
		'footer_widget_area_spacing',
		array(
			'type'     => 'generatepress-spacing',
			'label'    => esc_html__( 'Footer Widget Area Padding', 'gp-premium' ),
			'section'  => $footer_section,
			'settings' => array(
				'desktop_top'    => 'generate_spacing_settings[footer_widget_container_top]',
				'desktop_right'  => 'generate_spacing_settings[footer_widget_container_right]',
				'desktop_bottom' => 'generate_spacing_settings[footer_widget_container_bottom]',
				'desktop_left'   => 'generate_spacing_settings[footer_widget_container_left]',
				'mobile_top'     => 'generate_spacing_settings[mobile_footer_widget_container_top]',
				'mobile_right'   => 'generate_spacing_settings[mobile_footer_widget_container_right]',
				'mobile_bottom'  => 'generate_spacing_settings[mobile_footer_widget_container_bottom]',
				'mobile_left'    => 'generate_spacing_settings[mobile_footer_widget_container_left]',
			),
			'element'  => 'footer_widget_area',
			'priority' => 99,
		)
	)
);

// Footer padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_top]',
	array(
		'default' => $defaults['footer_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_right]',
	array(
		'default' => $defaults['footer_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_bottom]',
	array(
		'default' => $defaults['footer_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Footer padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[footer_left]',
	array(
		'default' => $defaults['footer_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Make use of the footer padding settings.
$wp_customize->add_control(
	new GeneratePress_Spacing_Control(
		$wp_customize,
		'footer_spacing',
		array(
			'type'     => 'generatepress-spacing',
			'label'    => esc_html__( 'Footer Padding', 'gp-premium' ),
			'section'  => $footer_section,
			'settings' => array(
				'desktop_top'    => 'generate_spacing_settings[footer_top]',
				'desktop_right'  => 'generate_spacing_settings[footer_right]',
				'desktop_bottom' => 'generate_spacing_settings[footer_bottom]',
				'desktop_left'   => 'generate_spacing_settings[footer_left]',
			),
			'element'  => 'footer',
			'priority' => 105,
		)
	)
);
