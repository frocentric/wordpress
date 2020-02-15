<?php
defined( 'WPINC' ) or die;

// Controls
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-information-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-backgrounds-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-refresh-button-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-alpha-color-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-copyright-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-spacing-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-range-slider-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-title-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-typography-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-control-toggle.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-action-button-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-section-shortcuts-control.php';
require plugin_dir_path( __FILE__ ) . 'customizer/controls/class-deprecated.php';

// Other
require plugin_dir_path( __FILE__ ) . 'customizer/sanitize.php';
require plugin_dir_path( __FILE__ ) . 'customizer/active-callbacks.php';
require plugin_dir_path( __FILE__ ) . 'customizer/deprecated.php';

add_action( 'customize_controls_enqueue_scripts', 'generate_premium_control_inline_scripts', 100 );
/**
 * Add misc inline scripts to our controls.
 *
 * We don't want to add these to the controls themselves, as they will be repeated
 * each time the control is initialized.
 *
 * @since 1.4
 */
function generate_premium_control_inline_scripts() {
	if ( function_exists( 'generate_typography_default_fonts' ) ) {
		$number_of_fonts = apply_filters( 'generate_number_of_fonts', 200 );

		wp_localize_script( 'generatepress-pro-typography-customizer', 'gp_customize', array( 'nonce' => wp_create_nonce( 'gp_customize_nonce' ) ) );
		wp_localize_script( 'generatepress-pro-typography-customizer', 'typography_defaults', generate_typography_default_fonts() );
		wp_localize_script(
			'generatepress-pro-typography-customizer',
			'generatePressTypography',
			array(
				'googleFonts' => apply_filters( 'generate_typography_customize_list', generate_get_all_google_fonts( $number_of_fonts ) )
			)
		);
	}

	wp_enqueue_script( 'generatepress-pro-customizer-controls', plugin_dir_url( __FILE__ )  . 'customizer/controls/js/generatepress-controls.js', array( 'customize-controls', 'jquery' ), GP_PREMIUM_VERSION, true );

	$overlay_defaults = apply_filters( 'generate_off_canvas_overlay_style_defaults', array(
		'backgroundColor' => 'rgba(10,10,10,0.95)',
		'textColor' => '#ffffff',
		'backgroundHoverColor' => 'rgba(0,0,0,0)',
		'backgroundCurrentColor' => 'rgba(0,0,0,0)',
		'subMenuBackgroundColor' => 'rgba(0,0,0,0)',
		'subMenuTextColor' => '#ffffff',
		'subMenuBackgroundHoverColor' => 'rgba(0,0,0,0)',
		'subMenuBackgroundCurrentColor' => 'rgba(0,0,0,0)',
		'fontWeight' => 200,
		'fontSize' => 25,
	) );

	wp_localize_script(
		'gp-button-actions',
		'gpButtonActions',
		array(
			'warning' => esc_html__( 'This will design your overlay by changing options in the Customizer for you. Once saved, this can not be undone.', 'gp-premium' ),
			'styling' => $overlay_defaults,
		)
	);

	$controls_a11y = array(
		'fontSizeLabel' => esc_html__( 'Font size', 'gp-premium' ),
		'mobileHeaderFontSizeLabel' => esc_html__( 'Mobile header font size', 'gp-premium' ),
	);

	if ( function_exists( 'generate_get_default_fonts' ) ) {
		$font_defaults = generate_get_default_fonts();

		$controls_a11y['siteTitleFontSize'] = $font_defaults['site_title_font_size'];
		$controls_a11y['mobileSiteTitleFontSize'] = $font_defaults['mobile_site_title_font_size'];
	}

	if ( function_exists( 'generate_get_color_defaults' ) ) {
		$color_defaults = generate_get_color_defaults();

		$controls_a11y['navigationTextColor'] = $color_defaults['navigation_text_color'];
		$controls_a11y['headerTextColor'] = $color_defaults['header_text_color'];
	}

	if ( function_exists( 'generate_get_defaults' ) ) {
		$defaults = generate_get_defaults();

		$controls_a11y['navigationAlignment'] = $defaults['nav_alignment_setting'];
	}

	wp_localize_script(
		'generatepress-pro-customizer-controls',
		'gpControls',
		$controls_a11y
	);
}

add_action( 'customize_register', 'generate_premium_customizer_shortcut_controls', 100 );
/**
 * Add shortcuts to sections we don't control in this plugin.
 *
 * @since 1.8
 */
function generate_premium_customizer_shortcut_controls( $wp_customize ) {
	if ( ! class_exists( 'WP_Customize_Panel' ) ) {
		return;
	}

	if ( ! $wp_customize->get_panel( 'generate_layout_panel' ) ) {
		return;
	}

	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Section_Shortcut_Control' );
	}

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_header_layout_shortcuts',
			array(
				'section' => 'generate_layout_header',
				'element' => __( 'Header', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'header_color_section',
					'typography' => 'font_header_section',
					'backgrounds' => 'generate_backgrounds_header',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_primary_navigation_layout_shortcuts',
			array(
				'section' => 'generate_layout_navigation',
				'element' => __( 'Primary Navigation', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'navigation_color_section',
					'typography' => 'font_navigation_section',
					'backgrounds' => 'generate_backgrounds_navigation',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	if ( $wp_customize->get_control( 'blogname' ) ) {
		$wp_customize->get_control( 'generate_settings[container_width]' )->priority = 1;
	}

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_content_layout_shortcuts',
			array(
				'section' => 'generate_layout_container',
				'element' => __( 'Content', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'content_color_section',
					'typography' => 'font_content_section',
					'backgrounds' => 'generate_backgrounds_content',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 0,
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_sidebar_layout_shortcuts',
			array(
				'section' => 'generate_layout_sidebars',
				'element' => __( 'Sidebar', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'sidebar_widget_color_section',
					'typography' => 'font_widget_section',
					'backgrounds' => 'generate_backgrounds_sidebars',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_footer_layout_shortcuts',
			array(
				'section' => 'generate_layout_footer',
				'element' => __( 'Footer', 'gp-premium' ),
				'shortcuts' => array(
					'colors' => 'footer_color_section',
					'typography' => 'font_footer_section',
					'backgrounds' => 'generate_backgrounds_footer',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);
}

add_action( 'customize_controls_print_styles', 'generate_premium_customize_print_styles' );
/**
 * Print control styles for the Customizer.
 *
 * @since 1.9
 */
function generate_premium_customize_print_styles() {
	$sizes = apply_filters( 'generate_customizer_device_preview_sizes', array(
		'tablet' => 900,
		'mobile' => 640,
	) );
    ?>
	    <style>
			.wp-customizer .preview-tablet .wp-full-overlay-main {
				width: <?php echo absint( $sizes['tablet'] ); ?>px;
				margin: 0 auto;
				left: 50%;
				-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
			}

			.wp-customizer .preview-mobile .wp-full-overlay-main {
				width: <?php echo absint( $sizes['mobile'] ); ?>px;
				margin: 0 auto;
				left: 50%;
				-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
				height: 100%;
			}
	    </style>
    <?php
}
