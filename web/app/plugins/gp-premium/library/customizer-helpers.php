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
	$is_using_dynamic_typography = function_exists( 'generate_is_using_dynamic_typography' ) && generate_is_using_dynamic_typography();

	if ( function_exists( 'generate_typography_default_fonts' ) && ! $is_using_dynamic_typography ) {
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
		$controls_a11y['siteTitleTextColor'] = $color_defaults['site_title_color'];
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

	wp_enqueue_script(
		'generate-pro-customizer-controls',
		GP_PREMIUM_DIR_URL . 'dist/customizer.js',
		array( 'customize-controls', 'wp-i18n', 'wp-element', 'customize-base' ),
		GP_PREMIUM_VERSION,
		true
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'generate-pro-customizer-controls', 'gp-premium' );
	}

	wp_localize_script(
		'generate-pro-customizer-controls',
		'gpCustomizerControls',
		array(
			'hasSecondaryNav' => generatepress_is_module_active( 'generate_package_secondary_nav', 'GENERATE_SECONDARY_NAV' ),
			'hasMenuPlus' => generatepress_is_module_active( 'generate_package_menu_plus', 'GENERATE_MENU_PLUS' ),
			'hasWooCommerce' => class_exists( 'WooCommerce' ) && generatepress_is_module_active( 'generate_package_woocommerce', 'GENERATE_WOOCOMMERCE' ),
		)
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

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_colors_shortcuts',
			array(
				'section' => 'generate_colors_section',
				'element' => __( 'Colors', 'gp-premium' ),
				'shortcuts' => array(),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_control(
		new GeneratePress_Section_Shortcut_Control(
			$wp_customize,
			'generate_typography_shortcuts',
			array(
				'section' => 'generate_typography_section',
				'element' => __( 'Typography', 'gp-premium' ),
				'shortcuts' => array(),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);
}

add_action( 'customize_register', 'generate_premium_layout_block_element_messages', 1000 );
/**
 * Add shortcuts to sections we don't control in this plugin.
 *
 * @since 1.8
 */
function generate_premium_layout_block_element_messages( $wp_customize ) {
	if ( ! class_exists( 'WP_Customize_Panel' ) ) {
		return;
	}

	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Information_Customize_Control' );
	}

	if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
		$footer_sections = array(
			'generate_layout_footer',
			'footer_color_section',
			'font_footer_section',
			'generate_backgrounds_footer',
		);

		foreach ( $footer_sections as $section ) {
			if ( $wp_customize->get_section( $section ) ) {
				$wp_customize->add_control(
					new GeneratePress_Information_Customize_Control(
						$wp_customize,
						'generate_using_site_footer_element_' . $section,
						array(
							'section'     => $section,
							'description' => sprintf(
								/* translators: URL to the Elements dashboard. */
								__( 'This page is using a <a href="%s">Site Footer Element</a>. Some of the options below may not apply.', 'gp-premium' ),
								admin_url( 'edit.php?post_type=gp_elements' )
							),
							'notice' => true,
							'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
							'active_callback' => function() {
								$has_block_element = generate_has_active_element( 'site-footer', true );

								if ( $has_block_element ) {
									return true;
								}

								return false;
							},
							'priority' => 0,
						)
					)
				);
			}
		}

		$header_sections = array(
			'generate_layout_header',
			'header_color_section',
			'font_header_section',
			'generate_backgrounds_header',
		);

		foreach ( $header_sections as $section ) {
			if ( $wp_customize->get_section( $section ) ) {
				$wp_customize->add_control(
					new GeneratePress_Information_Customize_Control(
						$wp_customize,
						'generate_using_site_header_element_' . $section,
						array(
							'section'     => $section,
							'description' => sprintf(
								/* translators: URL to the Elements dashboard. */
								__( 'This page is using a <a href="%s">Site Header Element</a>. Some of the options below may not apply.', 'gp-premium' ),
								admin_url( 'edit.php?post_type=gp_elements' )
							),
							'notice' => true,
							'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
							'active_callback' => function() {
								$has_block_element = generate_has_active_element( 'site-header', true );

								if ( $has_block_element ) {
									return true;
								}

								return false;
							},
							'priority' => 0,
						)
					)
				);
			}
		}

		$sidebar_sections = array(
			'generate_layout_sidebars',
			'sidebar_widget_color_section',
			'font_widget_section',
			'generate_backgrounds_sidebars',
		);

		foreach ( $sidebar_sections as $section ) {
			if ( $wp_customize->get_section( $section ) ) {
				$wp_customize->add_control(
					new GeneratePress_Information_Customize_Control(
						$wp_customize,
						'generate_using_sidebar_element_' . $section,
						array(
							'section'     => $section,
							'description' => sprintf(
								/* translators: URL to the Elements dashboard. */
								__( 'This page is using a <a href="%s">Sidebar Element</a>. Some of the options below may not apply.', 'gp-premium' ),
								admin_url( 'edit.php?post_type=gp_elements' )
							),
							'notice' => true,
							'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
							'active_callback' => function() {
								$has_right_sidebar_block_element = generate_has_active_element( 'right-sidebar', true );

								if ( $has_right_sidebar_block_element ) {
									return true;
								}

								$has_left_sidebar_block_element = generate_has_active_element( 'left-sidebar', true );

								if ( $has_left_sidebar_block_element ) {
									return true;
								}

								return false;
							},
							'priority' => 0,
						)
					)
				);
			}
		}

		if ( $wp_customize->get_section( 'generate_blog_section' ) ) {
			$wp_customize->add_control(
				new GeneratePress_Information_Customize_Control(
					$wp_customize,
					'generate_using_post_loop_item_element',
					array(
						'section'     => 'generate_blog_section',
						'description' => sprintf(
							/* translators: URL to the Elements dashboard. */
							__( 'This page is using a <a href="%s">Content Template Element</a>. Some of the options below may not apply.', 'gp-premium' ),
							admin_url( 'edit.php?post_type=gp_elements' )
						),
						'notice' => true,
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
						'active_callback' => function() {
							$has_block_element = generate_has_active_element( 'content-template', true );

							if ( $has_block_element ) {
								return true;
							}

							return false;
						},
						'priority' => 0,
					)
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Information_Customize_Control(
					$wp_customize,
					'generate_using_page_hero_element',
					array(
						'section'     => 'generate_blog_section',
						'description' => sprintf(
							/* translators: URL to the Elements dashboard. */
							__( 'This page is using a <a href="%s">Page Hero Element</a>. Some of the options below may not apply.', 'gp-premium' ),
							admin_url( 'edit.php?post_type=gp_elements' )
						),
						'notice' => true,
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
						'active_callback' => function() {
							$has_block_element = generate_has_active_element( 'page-hero', true );

							if ( $has_block_element ) {
								return true;
							}

							return false;
						},
						'priority' => 0,
					)
				)
			);

			$wp_customize->add_control(
				new GeneratePress_Information_Customize_Control(
					$wp_customize,
					'generate_using_post_meta_area_element',
					array(
						'section'     => 'generate_blog_section',
						'description' => sprintf(
							/* translators: URL to the Elements dashboard. */
							__( 'This page is using a <a href="%s">Post Meta Template Element</a>. Some of the options below may not apply.', 'gp-premium' ),
							admin_url( 'edit.php?post_type=gp_elements' )
						),
						'notice' => true,
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
						'active_callback' => function() {
							$has_block_element = generate_has_active_element( 'post-meta-template', true );

							if ( $has_block_element ) {
								return true;
							}

							return false;
						},
						'priority' => 0,
					)
				)
			);
		}
	}
}

add_action( 'customize_controls_print_styles', 'generate_premium_customize_print_styles' );
/**
 * Print control styles for the Customizer.
 *
 * @since 1.9
 */
function generate_premium_customize_print_styles() {
	$sizes = apply_filters( 'generate_customizer_device_preview_sizes', array(
		'tablet' => 800,
		'mobile' => 411,
		'mobile_height' => 731,
	) );
    ?>
	    <style>
			.wp-customizer .preview-tablet .wp-full-overlay-main {
				width: <?php echo absint( $sizes['tablet'] ); ?>px;
				margin-left: 0;
				margin-right: 0;
				left: 50%;
				-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
			}

			.wp-customizer .preview-mobile .wp-full-overlay-main {
				width: <?php echo absint( $sizes['mobile'] ); ?>px;
				height: <?php echo absint( $sizes['mobile_height'] ); ?>px;
				margin-left: 0;
				margin-right: 0;
				left: 50%;
				-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
			}

			.rtl.wp-customizer .preview-tablet .wp-full-overlay-main,
			.rtl.wp-customizer .preview-mobile .wp-full-overlay-main {
				-webkit-transform: translateX(50%);
				transform: translateX(50%);
			}
	    </style>
    <?php
}
