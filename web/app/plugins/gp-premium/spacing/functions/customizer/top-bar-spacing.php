<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $defaults[ 'top_bar_top' ] ) ) {
	// Widget padding top
	$wp_customize->add_setting( 'generate_spacing_settings[top_bar_top]',
		array(
			'default' => $defaults['top_bar_top'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	// Widget padding right
	$wp_customize->add_setting( 'generate_spacing_settings[top_bar_right]',
		array(
			'default' => $defaults['top_bar_right'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	// Widget padding bottom
	$wp_customize->add_setting( 'generate_spacing_settings[top_bar_bottom]',
		array(
			'default' => $defaults['top_bar_bottom'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	// Widget padding left
	$wp_customize->add_setting( 'generate_spacing_settings[top_bar_left]',
		array(
			'default' => $defaults['top_bar_left'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	// Make use of the widget padding settings
	$wp_customize->add_control(
		new GeneratePress_Spacing_Control(
			$wp_customize,
			'top_bar_spacing',
			array(
				'type' 		 => 'generatepress-spacing',
				'label'      => esc_html__( 'Top Bar Padding', 'gp-premium' ),
				'section'    => 'generate_top_bar',
				'settings'   => array(
					'desktop_top'    => 'generate_spacing_settings[top_bar_top]',
					'desktop_right'  => 'generate_spacing_settings[top_bar_right]',
					'desktop_bottom' => 'generate_spacing_settings[top_bar_bottom]',
					'desktop_left'   => 'generate_spacing_settings[top_bar_left]'
				),
				'element'	 => 'top_bar',
				'priority'   => 99,
				'active_callback' => 'generate_premium_is_top_bar_active',
			)
		)
	);
}
