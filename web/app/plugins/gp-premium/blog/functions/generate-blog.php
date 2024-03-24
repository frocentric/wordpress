<?php
defined( 'WPINC' ) or die;

// Pull in our defaults and functions
require plugin_dir_path( __FILE__ ) . 'defaults.php';
require plugin_dir_path( __FILE__ ) . 'images.php';
require plugin_dir_path( __FILE__ ) . 'columns.php';
require plugin_dir_path( __FILE__ ) . 'customizer.php';
require plugin_dir_path( __FILE__ ) . 'migrate.php';

if ( ! function_exists( 'generate_blog_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_blog_scripts', 50 );
	/**
	 * Enqueue scripts and styles
	 */
	function generate_blog_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		wp_add_inline_style( 'generate-style', generate_blog_css() );
		wp_add_inline_style( 'generate-style', generate_blog_columns_css() );

		$deps = array();

		if ( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) {
			$deps[] = 'masonry';
			$deps[] = 'imagesloaded';
		}

		if ( $settings[ 'infinite_scroll' ] && ! is_singular() && ! is_404() && ! is_post_type_archive( 'product' ) ) {
			$deps[] = 'infinite-scroll';
			wp_enqueue_script( 'infinite-scroll', plugin_dir_url( __FILE__ ) . 'js/infinite-scroll.pkgd.min.js', array(), '3.0.6', true );

			$font_icons = true;

			if ( function_exists( 'generate_get_option' ) ) {
				if ( 'font' !== generate_get_option( 'icons' ) ) {
					$font_icons = false;
				}
			}

			if ( $settings['infinite_scroll_button'] && $font_icons ) {
				wp_enqueue_style( 'gp-premium-icons' );
			}
		}

		if ( ( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) || ( $settings[ 'infinite_scroll' ] && ! is_singular() && ! is_404() && ! is_post_type_archive( 'product' ) ) ) {
			wp_enqueue_script( 'generate-blog', plugin_dir_url( __FILE__ ) . "js/scripts{$suffix}.js", $deps, GENERATE_BLOG_VERSION, true );

			wp_localize_script(
				'generate-blog',
				'generateBlog',
				array(
					'more'  => $settings['masonry_load_more'],
					'loading' => $settings['masonry_loading'],
					'icon' => function_exists( 'generate_get_svg_icon' ) ? generate_get_svg_icon( 'spinner' ) : '',
					'masonryInit' => apply_filters(
						'generate_blog_masonry_init',
						array(
							'columnWidth' => '.grid-sizer',
							'itemSelector' => '.masonry-post',
							'stamp' => '.page-header',
							'percentPosition' => true,
							'stagger' => 30,
							'visibleStyle' => array(
								'transform' => 'translateY(0)',
								'opacity' => 1,
							),
							'hiddenStyle' => array(
								'transform' => 'translateY(5px)',
								'opacity' => 0,
							),
						)
					),
					'infiniteScrollInit' => apply_filters(
						'generate_blog_infinite_scroll_init',
						array(
							'path' => '.infinite-scroll-path a',
							'append' => '#main .infinite-scroll-item',
							'history' => false,
							'loadOnScroll' => $settings['infinite_scroll_button'] ? false : true,
							'button' => $settings['infinite_scroll_button'] ? '.load-more a' : null,
							'scrollThreshold' => $settings['infinite_scroll_button'] ? false : 600,
						)
					),
				)
			);
		}

		$needs_columns_css = false;
		$needs_featured_image_css = false;

		if ( generate_blog_get_columns() || $settings['infinite_scroll'] ) {
			$needs_columns_css = true;
		}

		if ( ! is_singular() ) {
			if ( $settings['post_image'] ) {
				$needs_featured_image_css = true;
			}
		}

		if ( is_page() && has_post_thumbnail() ) {
			if ( $settings['page_post_image'] ) {
				$needs_featured_image_css = true;
			}
		}

		if ( is_single() && has_post_thumbnail() ) {
			if ( $settings['single_post_image'] ) {
				$needs_featured_image_css = true;
			}
		}

		if ( $needs_columns_css && $needs_featured_image_css ) {
			wp_enqueue_style( 'generate-blog', plugin_dir_url( __FILE__ ) . "css/style{$suffix}.css", array(), GENERATE_BLOG_VERSION );
		} elseif ( $needs_columns_css ) {
			wp_enqueue_style( 'generate-blog-columns', plugin_dir_url( __FILE__ ) . "css/columns{$suffix}.css", array(), GENERATE_BLOG_VERSION );
		} elseif ( $needs_featured_image_css ) {
			wp_enqueue_style( 'generate-blog-images', plugin_dir_url( __FILE__ ) . "css/featured-images{$suffix}.css", array(), GENERATE_BLOG_VERSION );
		}
	}
}

if ( ! function_exists( 'generate_blog_post_classes' ) ) {
	add_filter( 'post_class', 'generate_blog_post_classes' );
	/**
	 * Adds custom classes to the content container
	 *
	 * @since 0.1
	 */
	function generate_blog_post_classes( $classes ) {
		// Don't add these classes to the GB Query Loop block items.
		if ( in_array( 'gb-query-loop-item', $classes ) ) {
			return $classes;
		}

		global $wp_query;
		$paged = get_query_var( 'paged' );
		$paged = $paged ? $paged : 1;

		// Get our options
		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( $settings['infinite_scroll'] ) {
			$classes[] = 'infinite-scroll-item';
		}

		// Set our masonry class
		if ( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) {
			$classes[] = 'masonry-post';
		}

		// Set our column classes
		if ( generate_blog_get_columns() && ! is_singular() ) {
			$classes[] = 'generate-columns';
			$classes[] = 'tablet-grid-50';
			$classes[] = 'mobile-grid-100';
			$classes[] = 'grid-parent';

			// Set our featured column class
			if ( $wp_query->current_post == 0 && $paged == 1 && $settings['featured_column'] ) {
				if ( 50 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-100';
				}

				if ( 33 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-66';
				}

				if ( 25 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-50';
				}

				if ( 20 == generate_blog_get_column_count() ) {
					$classes[] = 'grid-60';
				}
				$classes[] = 'featured-column';
			} else {
				$classes[] = 'grid-' . generate_blog_get_column_count();
			}
		}

		if ( ! $settings['post_image_padding'] && ! is_singular() ) {
			$classes[] = 'no-featured-image-padding';
		}

		$location = generate_blog_get_singular_template();

		if ( ! $settings[$location . '_post_image_padding'] && is_singular() ) {
			$classes[] = 'no-featured-image-padding';
		}

		$atts = generate_get_blog_image_attributes();

		if ( ! is_singular() && has_post_thumbnail() && ! empty( $atts ) ) {
			$values = array(
				$atts['width'],
				$atts['height'],
				$atts['crop'],
			);

			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID(), 'full' ), $values );

			if ( $image_src && ( ! $image_src[3] || ! apply_filters( 'generate_use_featured_image_size_match', true ) ) ) {
				$classes[] = 'resize-featured-image';
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'generate_blog_body_classes' ) ) {
	add_filter( 'body_class', 'generate_blog_body_classes' );
	/**
	 * Adds custom classes to the body
	 *
	 * @since 0.1
	 */
	function generate_blog_body_classes( $classes ) {
		// Get theme options
		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( is_singular() ) {
			$location = generate_blog_get_singular_template();

			if ( 'below-title' == $settings[$location . '_post_image_position'] ) {
				$classes[] = 'post-image-below-header';
			}

			if ( 'inside-content' == $settings[$location . '_post_image_position'] ) {
				$classes[] = 'post-image-above-header';
			}

			$classes[] = ( ! empty( $settings[$location . '_post_image_alignment'] ) ) ? 'post-image-aligned-' . $settings[$location . '_post_image_alignment'] : 'post-image-aligned-center';
		} else {
			$classes[] = ( '' == $settings['post_image_position'] ) ? 'post-image-below-header' : 'post-image-above-header';
			$classes[] = ( ! empty( $settings['post_image_alignment'] ) ) ? $settings['post_image_alignment'] : 'post-image-aligned-center';
		}

		if ( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) {
			$classes[] = 'masonry-enabled';
		}

		if ( generate_blog_get_columns() && ! is_singular() ) {
			$classes[] = 'generate-columns-activated';
		}

		if ( $settings[ 'infinite_scroll' ] && ! is_singular() ) {
			$classes[] = 'infinite-scroll';
		}

		return $classes;
	}
}

if ( ! function_exists( 'generate_excerpt_length' ) ) {
	add_filter( 'excerpt_length', 'generate_excerpt_length', 15 );
	/**
	 * Set our excerpt length
	 *
	 * @since 0.1
	 */
	function generate_excerpt_length( $length ) {
		$generate_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);
		return absint( apply_filters( 'generate_excerpt_length', $generate_settings['excerpt_length'] ) );
	}
}

if ( ! function_exists( 'generate_blog_css' ) ) {
	/**
	 * Build our inline CSS
	 *
	 * @since 0.1
	 */
	function generate_blog_css() {
		global $post;
		$return = '';

		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( ! defined( 'GENERATE_VERSION' ) ) {
			return;
		}

		if ( version_compare( GENERATE_VERSION, '3.0.0-alpha.1', '<' ) ) {
			// Get disable headline meta.
			$disable_headline = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate-disable-headline', true ) : '';

			if ( ! $settings['categories'] && ! $settings['comments'] && ! $settings['tags'] && ! is_singular() ) {
				$return .= '.blog footer.entry-meta, .archive footer.entry-meta {display:none;}';
			}

			if ( ! $settings['single_date'] && ! $settings['single_author'] && $disable_headline && is_singular() ) {
				$return .= '.single .entry-header{display:none;}.single .entry-content {margin-top:0;}';
			}

			if ( ! $settings['date'] && ! $settings['author'] && ! is_singular() ) {
				$return .= '.entry-header .entry-meta {display:none;}';
			}

			if ( ! $settings['single_date'] && ! $settings['single_author'] && is_singular() ) {
				$return .= '.entry-header .entry-meta {display:none;}';
			}

			if ( ! $settings['single_post_navigation'] && is_singular() ) {
				$return .= '.post-navigation {display:none;}';
			}

			if ( ! $settings['single_categories'] && ! $settings['single_post_navigation'] && ! $settings['single_tags'] && is_singular() ) {
				$return .= '.single footer.entry-meta {display:none;}';
			}
		}

		$separator = 20;
		$content_padding_top = 40;
		$content_padding_right = 40;
		$content_padding_left = 40;
		$mobile_content_padding_top = 30;
		$mobile_content_padding_right = 30;
		$mobile_content_padding_left = 30;

		if ( function_exists( 'generate_spacing_get_defaults' ) ) {
			$spacing_settings = wp_parse_args(
				get_option( 'generate_spacing_settings', array() ),
				generate_spacing_get_defaults()
			);

			$separator = absint( $spacing_settings['separator'] );
			$content_padding_top = absint( $spacing_settings['content_top'] );
			$content_padding_right = absint( $spacing_settings['content_right'] );
			$content_padding_left = absint( $spacing_settings['content_left'] );
			$mobile_content_padding_top = absint( $spacing_settings['mobile_content_top'] );
			$mobile_content_padding_right = absint( $spacing_settings['mobile_content_right'] );
			$mobile_content_padding_left = absint( $spacing_settings['mobile_content_left'] );
		}

		if ( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) {
			$return .= '.page-header {margin-bottom: ' . $separator . 'px;margin-left: ' . $separator . 'px}';
		}

		if ( $settings[ 'infinite_scroll' ] && ! is_singular() ) {
			$return .= '#nav-below {display:none;}';
		}

		if ( ! $settings['post_image_padding'] && 'post-image-aligned-center' == $settings['post_image_alignment']  && ! is_singular() ) {
			$return .= '.no-featured-image-padding .post-image {margin-left:-' . $content_padding_left . 'px;margin-right:-' . $content_padding_right . 'px;}';
			$return .= '.post-image-above-header .no-featured-image-padding .inside-article .post-image {margin-top:-' . $content_padding_top . 'px;}';
		}

		$location = generate_blog_get_singular_template();

		if ( ! $settings[$location . '_post_image_padding'] && 'center' == $settings[$location . '_post_image_alignment'] && is_singular() ) {
			$return .= '.no-featured-image-padding .featured-image {margin-left:-' . $content_padding_left . 'px;margin-right:-' . $content_padding_right . 'px;}';
			$return .= '.post-image-above-header .no-featured-image-padding .inside-article .featured-image {margin-top:-' . $content_padding_top . 'px;}';
		}

		if ( ! $settings['page_post_image_padding'] || ! $settings['single_post_image_padding'] || ! $settings['post_image_padding'] ) {
			$return .= '@media ' . generate_premium_get_media_query( 'mobile' ) . '{';
				if ( ! $settings['post_image_padding'] && 'post-image-aligned-center' == $settings['post_image_alignment'] && ! is_singular() ) {
					$return .= '.no-featured-image-padding .post-image {margin-left:-' . $mobile_content_padding_left . 'px;margin-right:-' . $mobile_content_padding_right . 'px;}';
					$return .= '.post-image-above-header .no-featured-image-padding .inside-article .post-image {margin-top:-' . $mobile_content_padding_top . 'px;}';
				}

				if ( ! $settings[$location . '_post_image_padding'] && 'center' == $settings[$location . '_post_image_alignment'] && is_singular() ) {
					$return .= '.no-featured-image-padding .featured-image {margin-left:-' . $mobile_content_padding_left . 'px;margin-right:-' . $mobile_content_padding_right . 'px;}';
					$return .= '.post-image-above-header .no-featured-image-padding .inside-article .featured-image {margin-top:-' . $mobile_content_padding_top . 'px;}';
				}
			$return .= '}';
		}

		$atts = generate_get_blog_image_attributes();

		if ( ! empty( $atts ) ) {
			$image_width = $atts['width'] && 9999 !== $atts['width'] ? 'width: ' . $atts['width'] . 'px;' : '';
			$image_height = $atts['height'] && 9999 !== $atts['height'] ? 'height: ' . $atts['height'] . 'px;' : '';
			$image_crop = $atts['crop'] ? '-o-object-fit: cover;object-fit: cover;' : '';

			if ( ! $image_width && $image_height ) {
				$image_crop = '-o-object-fit: cover;object-fit: cover;';
			}

			if ( ! is_singular() ) {
				$return .= '.resize-featured-image .post-image img {' . $image_width . $image_height . $image_crop . '}';
			}

			if ( is_single() || is_page() ) {
				$values = array(
					$atts['width'],
					$atts['height'],
					$atts['crop'],
				);

				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID(), 'full' ), $values );

				if ( $image_src && ( ! $image_src[3] || ! apply_filters( 'generate_use_featured_image_size_match', true ) ) ) {
					$return .= '.featured-image img {' . $image_width . $image_height . $image_crop . '}';
				}
			}
		}

		return $return;
	}
}

add_filter( 'generate_excerpt_more_text', 'generate_blog_set_read_more_text' );
/**
 * Set the read more text with our Customizer setting.
 *
 * @param string $text The read more text.
 */
function generate_blog_set_read_more_text( $text ) {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( $settings['read_more'] ) {
		return wp_kses_post( $settings['read_more'] );
	}

	return $text;
}

if ( ! function_exists( 'generate_blog_excerpt_more' ) ) {
	add_filter( 'excerpt_more', 'generate_blog_excerpt_more', 15 );
	/**
	 * Prints the read more HTML
	 */
	function generate_blog_excerpt_more( $more ) {
		$generate_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( '' == $generate_settings['read_more'] ) {
			return '';
		}

		// We don't need to overwrite the entire element just to change its text.
		// If we can filter the text, stop here.
		if ( function_exists( 'generate_get_read_more_text' ) ) {
			return $more;
		}

		return apply_filters(
			'generate_excerpt_more_output',
			sprintf(
				' ... <a title="%1$s" class="read-more" href="%2$s" aria-label="%4$s">%3$s</a>',
				the_title_attribute( 'echo=0' ),
				esc_url( get_permalink( get_the_ID() ) ),
				wp_kses_post( $generate_settings['read_more'] ),
				sprintf(
					/* translators: Aria-label describing the read more button */
					_x( 'More on %s', 'more on post title', 'gp-premium' ),
					the_title_attribute( 'echo=0' )
				)
			)
		);
	}
}

if ( ! function_exists( 'generate_blog_content_more' ) ) {
	add_filter( 'the_content_more_link', 'generate_blog_content_more', 15 );
	/**
	 * Prints the read more HTML
	 */
	function generate_blog_content_more( $more ) {
		$generate_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( '' == $generate_settings['read_more'] ) {
			return '';
		}

		// We don't need to overwrite the entire element just to change its text.
		// If we can filter the text, stop here.
		if ( function_exists( 'generate_get_read_more_text' ) ) {
			return $more;
		}

		return apply_filters(
			'generate_content_more_link_output',
			sprintf(
				'<p class="read-more-container"><a title="%1$s" class="read-more content-read-more" href="%2$s" aria-label="%4$s">%3$s</a></p>',
				the_title_attribute( 'echo=0' ),
				esc_url( get_permalink( get_the_ID() ) . apply_filters( 'generate_more_jump', '#more-' . get_the_ID() ) ),
				wp_kses_post( $generate_settings['read_more'] ),
				sprintf(
					/* translators: Aria-label describing the read more button */
					_x( 'More on %s', 'more on post title', 'gp-premium' ),
					the_title_attribute( 'echo=0' )
				)
			)
		);
	}
}

/**
 * Checks the setting and returns false if $thing is disabled
 *
 * @since 1.4
 *
 * @param String  $data  The original data, passed through if not disabled
 * @param String  $thing The name of the thing to check
 * @return String|False The original data, or false (if disabled)
 */
function generate_disable_post_thing( $data, $thing ) {
	$generate_blog_settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $generate_blog_settings[$thing] ) {
		return false;
	}

	return $data;
}

if ( ! function_exists( 'generate_disable_post_date' ) ) {
	add_filter( 'generate_post_date', 'generate_disable_post_date' );
	/**
	 * Remove the post date if set
	 *
	 * @since 0.1
	 */
	function generate_disable_post_date( $date ) {
		if ( is_singular() ) {
			return generate_disable_post_thing( $date, 'single_date' );
		} else {
			return generate_disable_post_thing( $date, 'date' );
		}
	}
}

if ( ! function_exists( 'generate_disable_post_author' ) ) {
	add_filter( 'generate_post_author', 'generate_disable_post_author' );
	/**
	 * Set the author if set
	 *
	 * @since 0.1
	 */
	function generate_disable_post_author( $author ) {
		if ( is_singular() ) {
			return generate_disable_post_thing( $author, 'single_author' );
		} else {
			return generate_disable_post_thing( $author, 'author' );
		}
	}
}

if ( ! function_exists( 'generate_disable_post_categories' ) ) {
	add_filter( 'generate_show_categories', 'generate_disable_post_categories' );
	/**
	 * Remove the categories if set
	 *
	 * @since 0.1
	 */
	function generate_disable_post_categories( $categories ) {
		if ( is_singular() ) {
			return generate_disable_post_thing( $categories, 'single_categories' );
		} else {
			return generate_disable_post_thing( $categories, 'categories' );
		}
	}
}

if ( ! function_exists( 'generate_disable_post_tags' ) ) {
	add_filter( 'generate_show_tags', 'generate_disable_post_tags' );
	/**
	 * Remove the tags if set
	 *
	 * @since 0.1
	 */
	function generate_disable_post_tags( $tags ) {
		if ( is_singular() ) {
			return generate_disable_post_thing( $tags, 'single_tags' );
		} else {
			return generate_disable_post_thing( $tags, 'tags' );
		}
	}
}

if ( ! function_exists( 'generate_disable_post_comments_link' ) ) {
	add_filter( 'generate_show_comments', 'generate_disable_post_comments_link' );
	/**
	 * Remove the link to comments if set
	 *
	 * @since 0.1
	 */
	function generate_disable_post_comments_link( $comments_link ) {
		return generate_disable_post_thing( $comments_link, 'comments' );
	}
}

add_filter( 'generate_show_post_navigation', 'generate_disable_post_navigation' );
/**
 * Remove the single post navigation
 *
 * @since 1.5
 */
function generate_disable_post_navigation( $navigation ) {
	if ( is_singular() ) {
		return generate_disable_post_thing( $navigation, 'single_post_navigation' );
	} else {
		return $navigation;
	}
}

add_filter( 'generate_excerpt_more_output', 'generate_blog_read_more_button' );
add_filter( 'generate_content_more_link_output', 'generate_blog_read_more_button' );
/**
 * Add the button class to our read more link if set.
 *
 * @since 1.5
 *
 * @param string $output Our existing read more link.
 */
function generate_blog_read_more_button( $output ) {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! $settings['read_more_button'] ) {
		return $output;
	}

	$aria_label = function_exists( 'generate_get_read_more_aria_label' )
		? generate_get_read_more_aria_label()
		: sprintf(
			/* translators: Aria-label describing the read more button */
			_x( 'More on %s', 'more on post title', 'gp-premium' ),
			the_title_attribute( 'echo=0' )
		);

	return sprintf(
		'%5$s<p class="read-more-container"><a title="%1$s" class="read-more button" href="%2$s" aria-label="%4$s">%3$s</a></p>',
		the_title_attribute( 'echo=0' ),
		esc_url( get_permalink( get_the_ID() ) . apply_filters( 'generate_more_jump', '#more-' . get_the_ID() ) ),
		wp_kses_post( $settings['read_more'] ),
		$aria_label,
		'generate_excerpt_more_output' === current_filter() ? ' ... ' : ''
	);
}

if ( ! function_exists( 'generate_blog_load_more' ) ) {
	add_action( 'generate_after_main_content', 'generate_blog_load_more', 20 );
	/**
	 * Build our load more button
	 */
	function generate_blog_load_more() {
		// Get theme options
		$settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( ( ! $settings[ 'infinite_scroll_button' ] || ! $settings[ 'infinite_scroll' ] ) || is_singular() || is_404() ) {
			return;
		}

		global $wp_query;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		if ( is_post_type_archive( 'product' ) ) {
			return;
		}

		if ( is_tax( 'product_cat' ) ) {
			return;
		}

		$icon = '';

		if ( function_exists( 'generate_get_svg_icon' ) ) {
			$icon = generate_get_svg_icon( 'spinner' );
		}

		printf(
			'<div class="masonry-load-more load-more %1$s %2$s">
				<a class="button" href="#">%3$s%4$s</a>
			</div>',
			$icon ? 'has-svg-icon' : '',
			( 'true' == generate_blog_get_masonry() && generate_blog_get_columns() ) ? 'are-images-unloaded' : '',
			$icon,
			wp_kses_post( $settings['masonry_load_more'] )
		);
	}
}

/**
 * Checks to see whether we're getting page or single post options.
 *
 * @since 1.5
 *
 * @return string Name of our singular template
 */
function generate_blog_get_singular_template() {
	$template = 'single';

	if ( is_page() ) {
		$template = 'page';
	}

	return $template;
}

add_action( 'generate_after_footer', 'generate_blog_do_infinite_scroll_path', 500 );
/**
 * Add a next page of posts link for infinite scroll.
 *
 * @since 2.0.0
 */
function generate_blog_do_infinite_scroll_path() {
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return;
	}

	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( $settings['infinite_scroll'] && ! is_singular() && ! is_404() ) {
		printf(
			'<div class="infinite-scroll-path" aria-hidden="true" style="display: none;">%s</div>',
			get_next_posts_link()
		);
	}
}
