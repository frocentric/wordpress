<?php

class GeneratePress_Hero {
	/**
	 * Our conditionals for this header.
	 *
	 * @since 1.7
	 */
	protected $conditional = array();

	/**
	 * Our exclusions for this header.
	 *
	 * @since 1.7
	 */
	protected $exclude = array();

	/**
	 * Our user conditionals for this header.
	 *
	 * @since 1.7
	 */
	protected $users = array();

	/**
	 * Our array of available options.
	 *
	 * @since 1.7
	 */
	protected static $options = array();

	/**
	 * The element ID.
	 *
	 * @since 1.7
	 */
	protected static $post_id = '';

	/**
	 * How many times this class has been called per page.
	 *
	 * @since 1.7
	 */
	public static $instances = 0;

	/**
	 * Get our current instance.
	 *
	 * @since 1.7
	 */
	protected static $hero = '';

	/**
	 * Kicks it all off.
	 *
	 * @since 1.7
	 *
	 * @param int The element post ID.
	 */
	function __construct( $post_id ) {

		self::$post_id = $post_id;

		// We need this to reference our instance in remove_hentry().
		self::$hero = $this;

		if ( get_post_meta( $post_id, '_generate_element_display_conditions', true ) ) {
			$this->conditional = get_post_meta( $post_id, '_generate_element_display_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_exclude_conditions', true ) ) {
			$this->exclude = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );
		}

		if ( get_post_meta( $post_id, '_generate_element_user_conditions', true ) ) {
			$this->users = get_post_meta( $post_id, '_generate_element_user_conditions', true );
		}

		$display = apply_filters( 'generate_header_element_display', GeneratePress_Conditions::show_data( $this->conditional, $this->exclude, $this->users ), $post_id );

		if ( $display ) {
			$location = apply_filters( 'generate_page_hero_location', 'generate_after_header', $post_id );

			add_action( $location,					array( $this, 'build_hero' ), 9 );
			add_action( 'wp_enqueue_scripts', 		array( $this, 'enqueue' ), 100 );
			add_action( 'wp', 						array( $this, 'after_setup' ), 100 );

			self::$instances++;
		}

	}

	/**
	 * Add necessary scripts and styles.
	 *
	 * @since 1.7
	 */
	public function enqueue() {
		$options = self::get_options();

		wp_add_inline_style( 'generate-style', self::build_css() );

		if ( $options['parallax'] ) {
			wp_enqueue_script( 'generate-hero-parallax', plugin_dir_url( __FILE__ ) . '/assets/js/parallax.min.js', array(), GP_PREMIUM_VERSION, true );
			wp_localize_script( 'generate-hero-parallax', 'hero', array(
				'parallax' => apply_filters( 'generate_hero_parallax_speed', 2 ),
			) );
		}
	}

	/**
	 * Builds the HTML structure for Page Headers.
	 *
	 * @since 1.7
	 */
	public function build_hero() {
		$options = self::get_options();

		if ( empty( $options['content'] ) ) {
			return;
		}

		$options['container_classes'] = implode( ' ', array(
			'page-hero',
			'contained' === $options['container'] ? 'grid-container grid-parent' : '',
			$options['classes'],
		) );

		$options['inner_container_classes'] = implode( ' ', array(
			'inside-page-hero',
			'full-width' !== $options['inner_container'] ? 'grid-container grid-parent' : '',
		) );

		$options['content'] = self::template_tags( $options['content'] );
		$options['content'] = do_shortcode( $options['content'] );

		echo apply_filters( 'generate_page_hero_output', sprintf(
			'<div class="%1$s">
				<div class="%2$s">
					%3$s
				</div>
			</div>',
			trim( $options['container_classes'] ),
			trim( $options['inner_container_classes'] ),
			$options['content']
		), $options );
	}

	/**
	 * Builds all of our custom CSS for Page Headers.
	 *
	 * @since 1.7
	 *
	 * @return string Dynamic CSS.
	 */
	public static function build_css() {
		$options = self::get_options();

		// Initiate our CSS class
		require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
		$css = new GeneratePress_Pro_CSS;

		$image_url = false;
		if ( $options['background_image'] && function_exists( 'get_the_post_thumbnail_url' ) ) {
			if ( 'featured-image' === $options['background_image'] ) {
				if ( is_singular() ) {
					$image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
				}

				if ( ! $image_url ) {
					$image_url = get_the_post_thumbnail_url( self::$post_id, 'full' );
				}
			}

			if ( 'custom-image' === $options['background_image'] ) {
				$image_url = get_the_post_thumbnail_url( self::$post_id, 'full' );
			}
		}

		$image_url = apply_filters( 'generate_page_hero_background_image_url', $image_url, $options );

		// Figure out desktop units.
		$options['padding_top_unit'] 	= $options['padding_top_unit'] ? $options['padding_top_unit'] : 'px';
		$options['padding_right_unit'] 	= $options['padding_right_unit'] ? $options['padding_right_unit'] : 'px';
		$options['padding_bottom_unit'] = $options['padding_bottom_unit'] ? $options['padding_bottom_unit'] : 'px';
		$options['padding_left_unit'] 	= $options['padding_left_unit'] ? $options['padding_left_unit'] : 'px';

		// Figure out mobile units.
		$options['padding_top_unit_mobile'] 	= $options['padding_top_unit_mobile'] ? $options['padding_top_unit_mobile'] : 'px';
		$options['padding_right_unit_mobile'] 	= $options['padding_right_unit_mobile'] ? $options['padding_right_unit_mobile'] : 'px';
		$options['padding_bottom_unit_mobile'] 	= $options['padding_bottom_unit_mobile'] ? $options['padding_bottom_unit_mobile'] : 'px';
		$options['padding_left_unit_mobile'] 	= $options['padding_left_unit_mobile'] ? $options['padding_left_unit_mobile'] : 'px';

		$css->set_selector( '.page-hero' );

		if ( $options['background_color'] ) {
			$css->add_property( 'background-color', esc_attr( $options['background_color'] ) );
		}

		if ( $image_url ) {
			$css->add_property( 'background-image', 'url(' . esc_url( $image_url ) . ')' );
			$css->add_property( 'background-size', 'cover' );

			if ( $options['background_color'] && $options['background_overlay'] ) {
				$css->add_property( 'background-image', 'linear-gradient(0deg, ' . $options['background_color'] . ',' . $options['background_color'] . '), url(' . $image_url . ')' );
			}

			if ( $options['background_position'] ) {
				$css->add_property( 'background-position', esc_attr( $options['background_position'] ) );
			}

			$css->add_property( 'background-repeat', 'no-repeat' );
		}

		if ( $options['text_color'] ) {
			$css->add_property( 'color', esc_attr( $options['text_color'] ) );
		}

		if ( $options['padding_top'] ) {
			$css->add_property( 'padding-top', absint( $options['padding_top'] ), false, esc_html( $options['padding_top_unit'] ) );
		}

		if ( $options['padding_right'] ) {
			$css->add_property( 'padding-right', absint( $options['padding_right'] ), false, esc_html( $options['padding_right_unit'] ) );
		}

		if ( $options['padding_bottom'] ) {
			$css->add_property( 'padding-bottom', absint( $options['padding_bottom'] ), false, esc_html( $options['padding_bottom_unit'] ) );
		}

		if ( $options['padding_left'] ) {
			$css->add_property( 'padding-left', absint( $options['padding_left'] ), false, esc_html( $options['padding_left_unit'] ) );
		}

		if ( $options['horizontal_alignment'] ) {
			$css->add_property( 'text-align', esc_html( $options['horizontal_alignment'] ) );
		}

		$css->add_property( 'box-sizing', 'border-box' );

		if ( $options['site_header_merge'] && $options['full_screen'] ) {
			$css->add_property( 'min-height', '100vh' );

			if ( $options['vertical_alignment'] ) {
				$css->add_property( 'display', '-webkit-flex' );
				$css->add_property( 'display', '-ms-flex' );
				$css->add_property( 'display', 'flex' );

				if ( 'center' === $options['vertical_alignment'] ) {
					$css->add_property( '-webkit-box', 'center' );
					$css->add_property( '-ms-flex-pack', 'center' );
					$css->add_property( 'justify-content', 'center' );
				} elseif ( 'bottom' === $options['vertical_alignment'] ) {
					$css->add_property( '-webkit-box', 'end' );
					$css->add_property( '-ms-flex-pack', 'end' );
					$css->add_property( 'justify-content', 'flex-end' );
				}

				$css->add_property( '-webkit-box-orient', 'vertical' );
				$css->add_property( '-webkit-box-direction', 'normal' );
				$css->add_property( '-ms-flex-direction', 'column' );
				$css->add_property( 'flex-direction', 'column' );

				$css->set_selector( '.page-hero .inside-page-hero' );
				$css->add_property( 'width', '100%' );
			}
		}

		$css->set_selector( '.page-hero h1, .page-hero h2, .page-hero h3, .page-hero h4, .page-hero h5, .page-hero h6' );
		if ( $options['text_color'] ) {
			$css->add_property( 'color', esc_attr( $options['text_color'] ) );
		}

		$css->set_selector( '.inside-page-hero > *:last-child' );
		$css->add_property( 'margin-bottom', '0px' );

		$css->set_selector( '.page-hero a, .page-hero a:visited' );

		if ( $options['link_color'] ) {
			$css->add_property( 'color', esc_attr( $options['link_color'] ) );
		}

		if ( $options['content'] ) {
			$css->set_selector( '.page-hero time.updated' );
			$css->add_property( 'display', 'none' );
		}

		$css->set_selector( '.page-hero a:hover' );

		if ( $options['link_color_hover'] ) {
			$css->add_property( 'color', esc_attr( $options['link_color_hover'] ) );
		}

		if ( '' !== $options['site_header_merge'] ) {
			if ( 'merge-desktop' === $options['site_header_merge'] ) {
				$css->start_media_query( apply_filters( 'generate_not_mobile_media_query', '(min-width: 769px)' ) );
			}

			$header_background = $options['header_background_color'] ? $options['header_background_color'] : 'transparent';

			if ( $options['site_header_height'] ) {
				$css->set_selector( '.page-hero' );

				if ( $options['padding_top'] ) {
					$css->add_property( 'padding-top', 'calc(' . absint( $options['padding_top'] ) . esc_html( $options['padding_top_unit'] ) . ' + ' . absint( $options['site_header_height'] ) . 'px)' );
				} else {
					$css->add_property( 'padding-top', absint( $options['site_header_height'] ), false, 'px' );
				}
			}

			$css->set_selector( '.header-wrap' );
			$css->add_property( 'position', 'absolute' );
			$css->add_property( 'left', '0px' );
			$css->add_property( 'right', '0px' );
			$css->add_property( 'z-index', '10' );

			$css->set_selector( '.header-wrap .site-header' );
			$css->add_property( 'background', $header_background );

			$css->set_selector( '.header-wrap .main-title a, .header-wrap .main-title a:hover, .header-wrap .main-title a:visited' );
			$css->add_property( 'color', esc_attr( $options['header_title_color'] ) );

			if ( ! GeneratePress_Elements_Helper::does_option_exist( 'navigation-as-header' ) ) {
				$css->set_selector( '.header-wrap .mobile-header-navigation:not(.navigation-stick):not(.toggled) .main-title a, .header-wrap .mobile-header-navigation:not(.navigation-stick):not(.toggled) .main-title a:hover, .header-wrap .mobile-header-navigation:not(.navigation-stick):not(.toggled) .main-title a:visited' );
				$css->add_property( 'color', esc_attr( $options['header_title_color'] ) );
			}

			if ( function_exists( 'generate_get_color_defaults' ) ) {
				$color_settings = wp_parse_args(
					get_option( 'generate_settings', array() ),
					generate_get_color_defaults()
				);

				if ( GeneratePress_Elements_Helper::does_option_exist( 'navigation-as-header' ) ) {
					$css->set_selector( '.header-wrap .toggled .main-title a, .header-wrap .toggled .main-title a:hover, .header-wrap .toggled .main-title a:visited, .header-wrap .navigation-stick .main-title a, .header-wrap .navigation-stick .main-title a:hover, .header-wrap .navigation-stick .main-title a:visited' );
					$css->add_property( 'color', esc_attr( $color_settings['site_title_color'] ) );
				}
			}

			$css->set_selector( '.header-wrap .site-description' );
			$css->add_property( 'color', esc_attr( $options['header_tagline_color'] ) );

			if ( $options['navigation_colors'] ) {
				$navigation_background = $options['navigation_background_color'] ? $options['navigation_background_color'] : 'transparent';
				$navigation_background_hover = $options['navigation_background_color_hover'] ? $options['navigation_background_color_hover'] : 'transparent';
				$navigation_background_current = $options['navigation_background_color_current'] ? $options['navigation_background_color_current'] : 'transparent';

				$css->set_selector( '.header-wrap #site-navigation:not(.toggled), .header-wrap #mobile-header:not(.toggled):not(.navigation-stick)' );
				$css->add_property( 'background', $navigation_background );

				$css->set_selector( '.header-wrap #site-navigation:not(.toggled) .main-nav > ul > li > a, .header-wrap #mobile-header:not(.toggled):not(.navigation-stick) .main-nav > ul > li > a, .header-wrap .main-navigation:not(.toggled):not(.navigation-stick) .menu-toggle, .header-wrap .main-navigation:not(.toggled):not(.navigation-stick) .menu-toggle:hover, .main-navigation:not(.toggled):not(.navigation-stick) .mobile-bar-items a, .main-navigation:not(.toggled):not(.navigation-stick) .mobile-bar-items a:hover, .main-navigation:not(.toggled):not(.navigation-stick) .mobile-bar-items a:focus' );
				$css->add_property( 'color', esc_attr( $options['navigation_text_color' ] ) );

				$css->set_selector( '.header-wrap #site-navigation:not(.toggled) .main-nav > ul > li:hover > a, .header-wrap #site-navigation:not(.toggled) .main-nav > ul > li:focus > a, .header-wrap #site-navigation:not(.toggled) .main-nav > ul > li.sfHover > a, .header-wrap #mobile-header:not(.toggled) .main-nav > ul > li:hover > a' );
				$css->add_property( 'background', $navigation_background_hover );

				if ( '' !== $options[ 'navigation_text_color_hover' ] ) {
					$css->add_property( 'color', esc_attr( $options[ 'navigation_text_color_hover' ] ) );
				} else {
					$css->add_property( 'color', esc_attr( $options[ 'navigation_text_color' ] ) );
				}

				$css->set_selector( '.header-wrap #site-navigation:not(.toggled) .main-nav > ul > li[class*="current-menu-"] > a, .header-wrap #mobile-header:not(.toggled) .main-nav > ul > li[class*="current-menu-"] > a, .header-wrap #site-navigation:not(.toggled) .main-nav > ul > li[class*="current-menu-"]:hover > a, .header-wrap #mobile-header:not(.toggled) .main-nav > ul > li[class*="current-menu-"]:hover > a' );
				$css->add_property( 'background', $navigation_background_current );

				if ( '' !== $options[ 'navigation_text_color_current' ] ) {
					$css->add_property( 'color', esc_attr( $options[ 'navigation_text_color_current' ] ) );
				} else {
					$css->add_property( 'color', esc_attr( $options[ 'navigation_text_color' ] ) );
				}
			}

			if ( $options['site_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'navigation-as-header' ) ) {
				$css->set_selector( '.main-navigation .site-logo, .main-navigation.toggled .page-hero-logo, .main-navigation.navigation-stick .page-hero-logo' );
				$css->add_property( 'display', 'none' );

				$css->set_selector( '.main-navigation .page-hero-logo, .main-navigation.toggled .site-logo:not(.page-hero-logo), #mobile-header .mobile-header-logo' );
				$css->add_property( 'display', 'block' );

				if ( ! GeneratePress_Elements_Helper::does_option_exist( 'sticky-navigation-logo' ) ) {
					$css->set_selector( '.main-navigation.navigation-stick .site-logo:not(.page-hero-logo)' );
					$css->add_property( 'display', 'block' );

					$css->set_selector( '.main-navigation.navigation-stick .page-hero-logo' );
					$css->add_property( 'display', 'none' );
				}
			}

			if ( $options['navigation_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'sticky-navigation' ) ) {
				$css->set_selector( '#site-navigation:not(.navigation-stick):not(.toggled) .navigation-logo:not(.page-hero-navigation-logo)' );
				$css->add_property( 'display', 'none' );

				$css->set_selector( '#sticky-navigation .page-hero-navigation-logo, #site-navigation.navigation-stick .page-hero-navigation-logo, #site-navigation.toggled .page-hero-navigation-logo' );
				$css->add_property( 'display', 'none' );
			}

			if ( $options['mobile_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'mobile-logo' ) ) {
				$css->set_selector( '#mobile-header:not(.navigation-stick):not(.toggled) .mobile-header-logo:not(.page-hero-mobile-logo)' );
				$css->add_property( 'display', 'none' );

				$css->set_selector( '#mobile-header.navigation-stick .page-hero-mobile-logo, #mobile-header.toggled .page-hero-mobile-logo' );
				$css->add_property( 'display', 'none' );
			}

			if ( $options['site_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'site-logo' ) ) {
				$css->set_selector( '.site-logo:not(.page-hero-logo)' );
				$css->add_property( 'display', 'none' );
			}

			if ( 'merge-desktop' === $options['site_header_merge'] ) {
				$css->stop_media_query();
			}

			if ( class_exists( 'Elementor\Plugin' ) ) {
				$css->set_selector( '.elementor-editor-active .header-wrap' );
				$css->add_property( 'pointer-events', 'none' );
			}
		}

		$css->start_media_query( generate_premium_get_media_query( 'mobile' ) );

		$css->set_selector( '.page-hero' );

		if ( $options['padding_top_mobile'] || '0' === $options['padding_top_mobile'] ) {
			$css->add_property( 'padding-top', absint( $options['padding_top_mobile'] ), false, esc_html( $options['padding_top_unit_mobile'] ) );
		}

		if ( 'merge' === $options['site_header_merge'] && $options['site_header_height_mobile'] ) {
			if ( $options['padding_top_mobile'] || '0' === $options['padding_top_mobile'] ) {
				$css->add_property( 'padding-top', 'calc(' . absint( $options['padding_top_mobile'] ) . esc_html( $options['padding_top_unit_mobile'] ) . ' + ' . absint( $options['site_header_height_mobile'] ) . 'px)' );
			} elseif ( $options['padding_top'] ) {
				$css->add_property( 'padding-top', 'calc(' . absint( $options['padding_top'] ) . esc_html( $options['padding_top_unit'] ) . ' + ' . absint( $options['site_header_height_mobile'] ) . 'px)' );
			} else {
				$css->add_property( 'padding-top', absint( $options['site_header_height_mobile'] ), false, 'px' );
			}
		}

		if ( $options['padding_right_mobile'] || '0' === $options['padding_right_mobile'] ) {
			$css->add_property( 'padding-right', absint( $options['padding_right_mobile'] ), false, esc_html( $options['padding_right_unit_mobile'] ) );
		}

		if ( $options['padding_bottom_mobile'] || '0' === $options['padding_bottom_mobile'] ) {
			$css->add_property( 'padding-bottom', absint( $options['padding_bottom_mobile'] ), false, esc_html( $options['padding_bottom_unit_mobile'] ) );
		}

		if ( $options['padding_left_mobile'] || '0' === $options['padding_left_mobile'] ) {
			$css->add_property( 'padding-left', absint( $options['padding_left_mobile'] ), false, esc_html( $options['padding_left_unit_mobile'] ) );
		}

		if ( GeneratePress_Elements_Helper::does_option_exist( 'site-logo' ) && 'merge-desktop' === $options['site_header_merge'] ) {
			$css->set_selector( '.inside-header .page-hero-logo, .main-navigation .page-hero-logo, #mobile-header .page-hero-mobile-logo' );
			$css->add_property( 'display', 'none' );
		}

		$css->stop_media_query();

		return apply_filters( 'generate_page_hero_css_output', $css->css_output(), $options );
	}

	/**
	 * Put all of our meta options within an array.
	 *
	 * @since 1.7
	 *
	 * @return array All Page Header options.
	 */
	public static function get_options() {
		$post_id = self::$post_id;

		return apply_filters( 'generate_hero_options', array(
			'element_id'							=> $post_id,
			'content' 								=> get_post_meta( $post_id, '_generate_element_content', true ),
			'classes'								=> get_post_meta( $post_id, '_generate_hero_custom_classes', true ),
			'container' 							=> get_post_meta( $post_id, '_generate_hero_container', true ),
			'inner_container'						=> get_post_meta( $post_id, '_generate_hero_inner_container', true ),
			'horizontal_alignment'					=> get_post_meta( $post_id, '_generate_hero_horizontal_alignment', true ),
			'full_screen'							=> get_post_meta( $post_id, '_generate_hero_full_screen', true ),
			'vertical_alignment' 					=> get_post_meta( $post_id, '_generate_hero_vertical_alignment', true ),
			'padding_top' 							=> get_post_meta( $post_id, '_generate_hero_padding_top', true ),
			'padding_top_unit' 						=> get_post_meta( $post_id, '_generate_hero_padding_top_unit', true ),
			'padding_right' 						=> get_post_meta( $post_id, '_generate_hero_padding_right', true ),
			'padding_right_unit' 					=> get_post_meta( $post_id, '_generate_hero_padding_right_unit', true ),
			'padding_bottom' 						=> get_post_meta( $post_id, '_generate_hero_padding_bottom', true ),
			'padding_bottom_unit' 					=> get_post_meta( $post_id, '_generate_hero_padding_bottom_unit', true ),
			'padding_left' 							=> get_post_meta( $post_id, '_generate_hero_padding_left', true ),
			'padding_left_unit' 					=> get_post_meta( $post_id, '_generate_hero_padding_left_unit', true ),
			'padding_top_mobile' 					=> get_post_meta( $post_id, '_generate_hero_padding_top_mobile', true ),
			'padding_top_unit_mobile' 				=> get_post_meta( $post_id, '_generate_hero_padding_top_unit_mobile', true ),
			'padding_right_mobile' 					=> get_post_meta( $post_id, '_generate_hero_padding_right_mobile', true ),
			'padding_right_unit_mobile' 			=> get_post_meta( $post_id, '_generate_hero_padding_right_unit_mobile', true ),
			'padding_bottom_mobile' 				=> get_post_meta( $post_id, '_generate_hero_padding_bottom_mobile', true ),
			'padding_bottom_unit_mobile' 			=> get_post_meta( $post_id, '_generate_hero_padding_bottom_unit_mobile', true ),
			'padding_left_mobile' 					=> get_post_meta( $post_id, '_generate_hero_padding_left_mobile', true ),
			'padding_left_unit_mobile' 				=> get_post_meta( $post_id, '_generate_hero_padding_left_unit_mobile', true ),
			'background_image' 						=> get_post_meta( $post_id, '_generate_hero_background_image', true ),
			'disable_featured_image'				=> get_post_meta( $post_id, '_generate_hero_disable_featured_image', true ),
			'background_overlay' 					=> get_post_meta( $post_id, '_generate_hero_background_overlay', true ),
			'background_position' 					=> get_post_meta( $post_id, '_generate_hero_background_position', true ),
			'parallax' 								=> get_post_meta( $post_id, '_generate_hero_background_parallax', true ),
			'background_color' 						=> get_post_meta( $post_id, '_generate_hero_background_color', true ),
			'text_color' 							=> get_post_meta( $post_id, '_generate_hero_text_color', true ),
			'link_color' 							=> get_post_meta( $post_id, '_generate_hero_link_color', true ),
			'link_color_hover' 						=> get_post_meta( $post_id, '_generate_hero_background_link_color_hover', true ),
			'site_header_merge' 					=> get_post_meta( $post_id, '_generate_site_header_merge', true ),
			'site_header_height' 					=> get_post_meta( $post_id, '_generate_site_header_height', true ),
			'site_header_height_mobile' 			=> get_post_meta( $post_id, '_generate_site_header_height_mobile', true ),
			'site_logo' 							=> get_post_meta( $post_id, '_generate_site_logo', true ),
			'retina_logo' 							=> get_post_meta( $post_id, '_generate_retina_logo', true ),
			'navigation_logo'						=> get_post_meta( $post_id, '_generate_navigation_logo', true ),
			'mobile_logo'							=> get_post_meta( $post_id, '_generate_mobile_logo', true ),
			'navigation_location'					=> get_post_meta( $post_id, '_generate_navigation_location', true ),
			'header_background_color'				=> get_post_meta( $post_id, '_generate_site_header_background_color', true ),
			'header_title_color'					=> get_post_meta( $post_id, '_generate_site_header_title_color', true ),
			'header_tagline_color'					=> get_post_meta( $post_id, '_generate_site_header_tagline_color', true ),
			'navigation_colors'						=> get_post_meta( $post_id, '_generate_navigation_colors', true ),
			'navigation_background_color'			=> get_post_meta( $post_id, '_generate_navigation_background_color', true ),
			'navigation_text_color'					=> get_post_meta( $post_id, '_generate_navigation_text_color', true ),
			'navigation_background_color_hover'		=> get_post_meta( $post_id, '_generate_navigation_background_color_hover', true ),
			'navigation_text_color_hover'			=> get_post_meta( $post_id, '_generate_navigation_text_color_hover', true ),
			'navigation_background_color_current'	=> get_post_meta( $post_id, '_generate_navigation_background_color_current', true ),
			'navigation_text_color_current'			=> get_post_meta( $post_id, '_generate_navigation_text_color_current', true ),
		) );
	}

	/**
	 * Does the bulk of the work after everything has initialized.
	 *
	 * @since 1.7
	 */
	public function after_setup() {
		$options = self::get_options();

		if ( $options['disable_featured_image'] && is_singular() ) {
			remove_action( 'generate_after_entry_header', 'generate_blog_single_featured_image' );
			remove_action( 'generate_before_content', 'generate_blog_single_featured_image' );
			remove_action( 'generate_after_header', 'generate_blog_single_featured_image' );
			remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single' );
			remove_action( 'generate_after_header', 'generate_featured_page_header' );
		}

		if ( $options['site_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'site-logo' ) ) {
			if ( '' !== $options['site_header_merge'] ) {
				add_action( 'generate_after_logo', array( $this, 'add_site_logo' ) );
			} else {
				add_filter( 'theme_mod_custom_logo', array( $this, 'replace_logo' ) );

				if ( $options['retina_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'retina-logo' ) ) {
					add_filter( 'generate_retina_logo', array( $this, 'replace_logo' ) );
				}
			}
		}

		if ( $options['navigation_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'navigation-logo' ) ) {
			if ( $options['site_header_merge'] && GeneratePress_Elements_Helper::does_option_exist( 'sticky-navigation' ) ) {
				add_action( 'generate_inside_navigation', array( $this, 'add_navigation_logo' ) );
			} else {
				add_filter( 'generate_navigation_logo', array( $this, 'replace_logo' ) );
			}
		}

		if ( $options['mobile_logo'] && GeneratePress_Elements_Helper::does_option_exist( 'mobile-logo' ) ) {
			if ( 'merge' === $options['site_header_merge'] ) {
				add_action( 'generate_inside_mobile_header', array( $this, 'add_mobile_header_logo' ) );
			} else {
				add_filter( 'generate_mobile_header_logo', array( $this, 'replace_logo' ) );
			}
		}

		if ( $options['navigation_location'] ) {
			add_filter( 'generate_navigation_location', array( $this, 'navigation_location' ) );
		}

		if ( '' !== $options['site_header_merge'] ) {
			add_action( 'generate_before_header', array( $this, 'merged_header_start' ), 1 );
			add_action( 'generate_after_header', array( $this, 'merged_header_end' ), 8 );

			if ( 'contained' === $options['container'] ) {
				add_filter( 'generate_header_class', array( $this, 'site_header_classes' ) );
			}
		}

		if ( $options['content'] ) {
			self::remove_template_elements();
		}
	}

	/**
	 * Returns our custom logos if set within the Page Header.
	 *
	 * @since 1.7
	 *
	 * @return string New URLs to images.
	 */
	public static function replace_logo() {
		$filter = current_filter();
		$options = self::get_options();

		if ( 'theme_mod_custom_logo' === $filter ) {
			return $options['site_logo'];
		}

		if ( 'generate_retina_logo' === $filter ) {
			return wp_get_attachment_url( $options['retina_logo'] );
		}

		if ( 'generate_navigation_logo' === $filter ) {
			return wp_get_attachment_url( $options['navigation_logo'] );
		}

		if ( 'generate_mobile_header_logo' === $filter ) {
			return wp_get_attachment_url( $options['mobile_logo'] );
		}
	}

	/**
	 * Adds a new site logo element if our header is merged on desktop only.
	 *
	 * @since 1.7
	 */
	public static function add_site_logo() {
		$options = self::get_options();

		$logo_url = wp_get_attachment_url( $options['site_logo'] );
		$retina_logo_url = wp_get_attachment_url( $options['retina_logo'] );

		if ( ! $logo_url ) {
			return;
		}

		$attr = apply_filters( 'generate_page_hero_logo_attributes', array(
			'class' => 'header-image',
			'alt'	=> esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
			'src'	=> $logo_url,
			'title'	=> esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
		) );

		if ( '' !== $retina_logo_url ) {
			$attr['srcset'] = $logo_url . ' 1x, ' . $retina_logo_url . ' 2x';

			// Add dimensions to image if retina is set. This fixes a container width bug in Firefox.
			$data = wp_get_attachment_metadata( $options['site_logo'] );

			if ( ! empty( $data ) ) {
				$attr['width'] = $data['width'];
				$attr['height'] = $data['height'];
			}
		}

		$attr = array_map( 'esc_attr', $attr );
		$html_attr = '';

		foreach ( $attr as $name => $value ) {
			$html_attr .= " $name=" . '"' . $value . '"';
		}

		echo apply_filters( 'generate_page_hero_logo_output', sprintf( // WPCS: XSS ok, sanitization ok.
			'<div class="site-logo page-hero-logo">
				<a href="%1$s" title="%2$s" rel="home">
					<img %3$s />
				</a>
			</div>',
			esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
			esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
			$html_attr
		), $logo_url, $html_attr );
	}

	/**
	 * Adds the custom navigation logo if needed.
	 * Only needed if there's a sticky navigation.
	 *
	 * @since 1.7
	 */
	public static function add_navigation_logo() {
		$options = self::get_options();

		printf(
			'<div class="site-logo sticky-logo navigation-logo page-hero-navigation-logo">
				<a href="%1$s" title="%2$s" rel="home">
					<img class="header-image" src="%3$s" alt="%4$s" />
				</a>
			</div>',
			esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
			esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
			esc_url( wp_get_attachment_url( $options['navigation_logo'] ) ),
			esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) )
		);
	}

	/**
	 * Adds the custom mobile header if needed.
	 * Only needed if there's a sticky navigation.
	 *
	 * @since 1.7
	 */
	public static function add_mobile_header_logo() {
		$options = self::get_options();

		if ( 'title' === GeneratePress_Elements_Helper::does_option_exist( 'mobile-header-branding' ) ) {
			return;
		}

		printf(
			'<div class="site-logo mobile-header-logo page-hero-mobile-logo">
				<a href="%1$s" title="%2$s" rel="home">
					<img class="header-image" src="%3$s" alt="%4$s" />
				</a>
			</div>',
			esc_url( apply_filters( 'generate_logo_href' , home_url( '/' ) ) ),
			esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) ),
			esc_url( wp_get_attachment_url( $options['mobile_logo'] ) ),
			esc_attr( apply_filters( 'generate_logo_title', get_bloginfo( 'name', 'display' ) ) )
		);
	}

	/**
	 * Set the navigation location if set.
	 *
	 * @since 1.7
	 *
	 * @return string The navigation location.
	 */
	public static function navigation_location() {
		$options = self::get_options();

		if ( 'no-navigation' === $options['navigation_location'] ) {
			return '';
		} else {
			return $options['navigation_location'];
		}
	}

	/**
	 * The opening merged header element.
	 *
	 * @since 1.7
	 */
	public static function merged_header_start() {
		echo '<div class="header-wrap">';
	}

	/**
	 * The closing merged header element.
	 *
	 * @since 1.7
	 */
	public static function merged_header_end() {
		echo '</div><!-- .header-wrap -->';
	}

	/**
	 * Adds classes to the site header.
	 *
	 * @since 1.7
	 *
	 * @param $classes Existing classes.
	 * @return array New classes.
	 */
	public static function site_header_classes( $classes ) {
		$classes[] = 'grid-container';
		$classes[] = 'grid-parent';

		return $classes;
	}

	/**
	 * Checks if template tags exist, and removes those elements from elsewhere.
	 *
	 * @since 1.7
	 */
	public static function remove_template_elements() {
		$options = self::get_options();

		if ( strpos( $options[ 'content' ], '{{post_title}}' ) !== false ) {
			add_filter( 'generate_show_title', '__return_false' );
			remove_action( 'generate_archive_title', 'generate_archive_title' );
			add_filter( 'post_class', array( self::$hero, 'remove_hentry' ) );
		}

		if ( strpos( $options[ 'content' ], '{{post_date}}' ) !== false ) {
			add_filter( 'generate_post_date', '__return_false' );
			add_filter( 'post_class', array( self::$hero, 'remove_hentry' ) );
		}

		if ( strpos( $options[ 'content' ], '{{post_author}}' ) !== false ) {
			add_filter( 'generate_post_author', '__return_false' );
			add_filter( 'post_class', array( self::$hero, 'remove_hentry' ) );
		}

		if ( strpos( $options[ 'content' ], '{{post_terms.category}}' ) !== false ) {
			add_filter( 'generate_show_categories', '__return_false' );
		}

		if ( strpos( $options[ 'content' ], '{{post_terms.post_tag}}' ) !== false ) {
			add_filter( 'generate_show_tags', '__return_false' );
		}
	}

	/**
	 * Checks for template tags and replaces them.
	 *
	 * @since 1.7
	 *
	 * @param $content The content to check.
	 * @return mixed The content with the template tags replaced.
	 */
	public static function template_tags( $content ) {
		$search = array();
		$replace = array();

		$search[] = '{{post_title}}';
		$post_title = '';

		if ( is_singular() ) {
			$post_title = get_the_title();
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$post_title = get_queried_object()->name;
		} elseif ( is_post_type_archive() ) {
			$post_title = post_type_archive_title( '', false );
		} elseif ( is_archive() && function_exists( 'get_the_archive_title' ) ) {
			$post_title = get_the_archive_title();
		} elseif ( is_home() ) {
			$post_title = __( 'Blog', 'gp-premium' );
		}

		$replace[] = apply_filters( 'generate_page_hero_post_title', $post_title );

		if ( is_singular() ) {
			$time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>' . $time_string;
			}

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

			$search[] = '{{post_date}}';
			$replace[] = apply_filters( 'generate_page_hero_post_date', $time_string );

			// Author
			global $post;
			$author_id = $post->post_author;

			$author = sprintf( '<span class="author vcard" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author"><a class="url fn n" href="%1$s" title="%2$s" rel="author" itemprop="url"><span class="author-name" itemprop="name">%3$s</span></a></span>',
				esc_url( get_author_posts_url( $author_id ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'gp-premium' ), get_the_author_meta( 'display_name', $author_id ) ) ),
				esc_html( get_the_author_meta( 'display_name', $author_id ) )
			);

			$search[] = '{{post_author}}';
			$replace[] = apply_filters( 'generate_page_hero_post_author', $author );

			// Post terms
			if ( strpos( $content, '{{post_terms' ) !== false ) {
				$data = preg_match_all( '/{{post_terms.([^}]*)}}/', $content, $matches );
				foreach ( $matches[1] as $match ) {
					$search[] = '{{post_terms.' . $match . '}}';
					$terms = get_the_term_list( get_the_ID(), $match, apply_filters( 'generate_page_hero_terms_before', '' ), apply_filters( 'generate_page_hero_terms_separator', ', ' ), apply_filters( 'generate_page_hero_terms_after', '' ) );

					if ( ! is_wp_error( $terms ) ) {
						$replace[] = $terms;
					}
				}
			}

			// Custom field
			if ( strpos( $content, '{{custom_field' ) !== false ) {
				$data = preg_match_all( '/{{custom_field.([^}]*)}}/', $content, $matches );
				foreach ( $matches[1] as $match ) {
					if ( null !== get_post_meta( get_the_ID(), $match, true ) && '_thumbnail_id' !== $match ) {
						$search[] = '{{custom_field.' . $match . '}}';
						$replace[] = get_post_meta( get_the_ID(), $match, true );
					}
				}

				$thumbnail_id = get_post_meta( get_the_ID(), '_thumbnail_id', true );
				if ( null !== $thumbnail_id ) {
					$search[] = '{{custom_field._thumbnail_id}}';
					$replace[] = wp_get_attachment_image( $thumbnail_id, apply_filters( 'generate_hero_thumbnail_id_size', 'medium' ) );
				}
			}
		}

		// Taxonomy description
		if ( is_tax() || is_category() || is_tag() ) {
			if ( strpos( $content, '{{custom_field' ) !== false ) {
				$search[] = '{{custom_field.description}}';
				$replace[] = term_description( get_queried_object()->term_id, get_queried_object()->taxonomy );
			}
		}

		return str_replace( $search, $replace, $content );
	}

	/**
	 * When the post title, author or date are in the Page Hero, they appear outside of the
	 * hentry element. This causes errors in Google Search Console.
	 *
	 * @since 1.7
	 *
	 * @param array $classes
	 * @return array
	 */
	public function remove_hentry( $classes ) {
		$classes = array_diff( $classes, array( 'hentry' ) );

		return $classes;
	}
}
