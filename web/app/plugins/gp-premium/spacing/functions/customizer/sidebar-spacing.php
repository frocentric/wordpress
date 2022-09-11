<?php
/**
 * This file handles the sidebar spacing Customizer options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our old Sidebars section.
// This section is no longer used but is kept around for back compat.
$wp_customize->add_section(
	'generate_spacing_sidebar',
	array(
		'title' => __( 'Sidebars', 'gp-premium' ),
		'capability' => 'edit_theme_options',
		'priority' => 15,
		'panel' => 'generate_spacing_panel',
	)
);

// Add our controls to the Layout panel if it exists.
// If not, use the old section.
$widget_section = ( $wp_customize->get_panel( 'generate_layout_panel' ) ) ? 'generate_layout_sidebars' : 'generate_spacing_sidebar';

// Widget padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[widget_top]',
	array(
		'default' => $defaults['widget_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[widget_right]',
	array(
		'default' => $defaults['widget_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[widget_bottom]',
	array(
		'default' => $defaults['widget_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[widget_left]',
	array(
		'default' => $defaults['widget_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding top.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_widget_top]',
	array(
		'default' => $defaults['mobile_widget_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding right.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_widget_right]',
	array(
		'default' => $defaults['mobile_widget_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding bottom.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_widget_bottom]',
	array(
		'default' => $defaults['mobile_widget_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Widget padding left.
$wp_customize->add_setting(
	'generate_spacing_settings[mobile_widget_left]',
	array(
		'default' => $defaults['mobile_widget_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Make use of the widget padding settings.
$wp_customize->add_control(
	new GeneratePress_Spacing_Control(
		$wp_customize,
		'widget_spacing',
		array(
			'type'     => 'generatepress-spacing',
			'label'    => esc_html__( 'Widget Padding', 'gp-premium' ),
			'section'  => $widget_section,
			'settings' => array(
				'desktop_top'    => 'generate_spacing_settings[widget_top]',
				'desktop_right'  => 'generate_spacing_settings[widget_right]',
				'desktop_bottom' => 'generate_spacing_settings[widget_bottom]',
				'desktop_left'   => 'generate_spacing_settings[widget_left]',
				'mobile_top'    => 'generate_spacing_settings[mobile_widget_top]',
				'mobile_right'  => 'generate_spacing_settings[mobile_widget_right]',
				'mobile_bottom' => 'generate_spacing_settings[mobile_widget_bottom]',
				'mobile_left'   => 'generate_spacing_settings[mobile_widget_left]',
			),
			'element' => 'widget',
			'priority' => 99,
		)
	)
);

// Left sidebar width.
$wp_customize->add_setting(
	'generate_spacing_settings[left_sidebar_width]',
	array(
		'default' => $defaults['left_sidebar_width'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new GeneratePress_Pro_Range_Slider_Control(
		$wp_customize,
		'generate_spacing_settings[left_sidebar_width]',
		array(
			'label' => esc_html__( 'Left Sidebar Width', 'gp-premium' ),
			'section' => $widget_section,
			'settings' => array(
				'desktop' => 'generate_spacing_settings[left_sidebar_width]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 15,
					'max' => 50,
					'step' => 5,
					'edit' => false,
					'unit' => '%',
				),
			),
			'priority' => 125,
		)
	)
);

// Right sidebar width.
$wp_customize->add_setting(
	'generate_spacing_settings[right_sidebar_width]',
	array(
		'default' => $defaults['right_sidebar_width'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new GeneratePress_Pro_Range_Slider_Control(
		$wp_customize,
		'generate_spacing_settings[right_sidebar_width]',
		array(
			'label' => esc_html__( 'Right Sidebar Width', 'gp-premium' ),
			'section' => $widget_section,
			'settings' => array(
				'desktop' => 'generate_spacing_settings[right_sidebar_width]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 15,
					'max' => 50,
					'step' => 5,
					'edit' => false,
					'unit' => '%',
				),
			),
			'priority' => 125,
		)
	)
);
