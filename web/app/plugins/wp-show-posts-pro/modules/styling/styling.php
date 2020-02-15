<?php
// Admin sections
require trailingslashit( dirname( __FILE__ ) ) . 'admin-sections.php';

add_action( 'wpsp_before_wrapper', 'wpsp_pro_styling', 10 );
function wpsp_pro_styling( $settings ) {
	// If our font size doesn't have a unit, add one
	$title_font_size = $settings['title_font_size'];

	if ( is_int( $settings[ 'title_font_size' ] ) ) {
		$title_font_size = $settings[ 'title_font_size' ] . 'px';
	}

	// Get our column gutter unit and remove it from the number
	$column_gutter_unit = ( '' !== $settings[ 'columns_gutter' ] ) ? preg_replace( '/[0-9]+/', '', $settings[ 'columns_gutter' ] ) : null;

	// Divide our column gutter in two, then re-add the unit
	$carousel_gutter = ( '' !== $settings[ 'columns_gutter' ] ) ? absint( $settings[ 'columns_gutter' ] ) / 2 . $column_gutter_unit : null;

	$overlay_color = $settings['image_overlay_color_static'];

	if ( $overlay_color ) {
		if ( '#' === $overlay_color[0] && function_exists( 'wpsp_hex2rgba' ) ) {
			$overlay_color = wpsp_hex2rgba( $overlay_color, apply_filters( 'wpsp_overlay_opacity', 0.7 ) ) . ' !important';
		} else {
			$overlay_color = $overlay_color . ' !important';
		}
	}

	$overlay_color_hover = $settings['image_overlay_color'];

	if ( $overlay_color_hover ) {
		if ( '#' === $overlay_color_hover[0] && function_exists( 'wpsp_hex2rgba' ) ) {
			$overlay_color_hover = wpsp_hex2rgba( $overlay_color_hover, apply_filters( 'wpsp_overlay_opacity', 0.7 ) ) . ' !important';
		} else {
			$overlay_color_hover = $overlay_color_hover . ' !important';
		}
	}

	$id = 'wpsp-' . absint( $settings['list_id'] );

	// Start the magic
	$visual_css = array (
		'.slick-slider#' . $id => array(
			'margin-left' => '0px'
		),

		'.slick-slider#' . $id . ' .wp-show-posts-inner' => array(
			'margin-left' => $carousel_gutter,
			'margin-right' => $carousel_gutter,
		),

		'.wpsp-carousel' => array(
			'opacity' => '0.0',
			'transition' => 'opacity 500ms ease',
		),

		'.wpsp-carousel .wp-show-posts-single:not(:first-child)' => array(
			'display' => 'none'
		),

		'#' . $id . '.wpsp-card .wp-show-posts-single'  => array(
			'margin-bottom' => $settings[ 'columns_gutter' ]
		),

		'#' . $id . ' .wp-show-posts-inner'  => array(
			'background-color' => $settings[ 'background' ],
			'color' => $settings[ 'text_color' ],
			'border' => '' !== $settings[ 'border_color' ] ? '1px solid ' . $settings[ 'border_color' ] : null
		),

		'#' . $id . ':not(.wpsp-card) .wp-show-posts-inner'  => array(
			'padding' => $settings[ 'padding' ]
		),

		'#' . $id . ' .wp-show-posts-inner:hover' => array(
			'background-color' => $settings[ 'background_hover' ],
			'border' => '' !== $settings[ 'border_color_hover' ] ? '1px solid ' . $settings[ 'border_color_hover' ] : null,
		),

		'#' . $id . '.wpsp-card .wpsp-content-wrap' => array(
			'padding' => $settings[ 'padding' ],
			'background-color' => 'wpsp-overlap' === $settings['card'] ? $settings[ 'background' ] : null,
		),

		'#' . $id . ' .wp-show-posts-image .wp-show-posts-image-overlay' => array(
			'background-color' => $overlay_color
		),

		'#' . $id . ' .wp-show-posts-inner:hover .wp-show-posts-image .wp-show-posts-image-overlay' => array(
			'background-color' => $overlay_color_hover
		),

		'#' . $id . '.wp-show-posts .wp-show-posts-entry-title' => array(
			'font-size' => ( $title_font_size > 0 ) ? $title_font_size : null,
		),

		'#' . $id . ' .wp-show-posts-entry-title a' => array(
			'color' => $settings[ 'title_color' ],
		),

		'#' . $id . ' .wp-show-posts-entry-title a:hover' => array(
			'color' => $settings[ 'title_color_hover' ]
		),

		'#' . $id . ' .wp-show-posts-entry-meta' => array(
			'color' => $settings[ 'text_color' ]
		),

		'#' . $id . ' .wp-show-posts-meta a' => array(
			'color' => $settings[ 'meta_color' ]
		),

		'#' . $id . ' .wp-show-posts-meta a:hover' => array(
			'color' => $settings[ 'meta_color_hover' ]
		),

		'#' . $id . ' .wpsp-social'  => array(
			'color' => $settings[ 'meta_color' ]
		),

		'#' . $id . ' .wp-show-posts-entry-summary a, .wp-show-posts-entry-content a' => array(
			'color' => $settings[ 'link_color' ]
		),

		'#' . $id . ' .wp-show-posts-entry-summary a:hover, .wp-show-posts-entry-content a:hover' => array(
			'color' => $settings[ 'link_color_hover' ]
		),

		'#' . $id . ' .wp-show-posts-read-more,
		#' . $id . ' .wp-show-posts-read-more:visited' => array(
			'background' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_background_color' ] : null,
			'border-color' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_border_color' ] : null,
			'color' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_text_color' ] : null
		),

		'#' . $id . ' .wp-show-posts-read-more:hover,
		#' . $id . ' .wp-show-posts-read-more:focus' => array(
			'background' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_background_color_hover' ] : null,
			'border-color' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_border_color_hover' ] : null,
			'color' => ( '' !== $settings[ 'read_more_text' ] ) ? $settings[ 'read_more_text_color_hover' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-twitter,
		#' . $id . ' .wpsp-social-link.wpsp-twitter:visited' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'twitter' ] ) ? $settings[ 'twitter_color' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-twitter:hover,
		#' . $id . ' .wpsp-social-link.wpsp-twitter:focus' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'twitter' ] ) ? $settings[ 'twitter_color_hover' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-facebook,
		#' . $id . ' .wpsp-social-link.wpsp-facebook:visited' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'facebook' ] ) ? $settings[ 'facebook_color' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-facebook:hover,
		#' . $id . ' .wpsp-social-link.wpsp-facebook:focus' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'facebook' ] ) ? $settings[ 'facebook_color_hover' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-pinterest,
		#' . $id . ' .wpsp-social-link.wpsp-pinterest:visited' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'pinterest' ] ) ? $settings[ 'pinterest_color' ] : null
		),

		'#' . $id . ' .wpsp-social-link.wpsp-pinterest:hover,
		#' . $id . ' .wpsp-social-link.wpsp-pinterest:focus' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'pinterest' ] ) ? $settings[ 'pinterest_color_hover' ] : null
		),

		'#' . $id . ' .wpsp-li-button,
		#' . $id . ' .wpsp-li-button:visited' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'love' ] ) ? $settings[ 'love_color' ] : null
		),

		'#' . $id . ' .wpsp-li-button:hover,
		#' . $id . ' .wpsp-li-button:focus' => array(
			'color' => ( $settings[ 'social_sharing' ] && $settings[ 'love' ] ) ? $settings[ 'love_color_hover' ] : null
		)

	);

	if ( 'wpsp-overlay' === $settings['card'] || 'wpsp-overlay-style-one' === $settings['card'] || 'wpsp-overlay-style-two' === $settings['card'] ) {
		$overlay_css = array(
			'.wpsp-overlay .wp-show-posts-inner' => array(
				'display' => 'grid',
				'grid-template-columns' => 'repeat(5, 1fr)',
				'grid-template-rows' => 'repeat(5, 1fr)',
				'position' => 'relative',
			),

			'.wpsp-overlay .wp-show-posts-image,
			.wpsp-card.wpsp-overlay .wpsp-content-wrap' => array(
				'grid-column' => '1 / 6',
				'grid-row' => '1 / 6',
			),

			'.wpsp-overlay .wp-show-posts-image' => array(
				'grid-column' => '1 / 6',
				'grid-row' => '1 / 6',
			),

			'.wpsp-overlay .wp-show-posts-image,
			.wpsp-overlay .wp-show-posts-image img' => array(
				'height' => 'calc(100% + 1em)',
			),

			'.wpsp-overlay .wpsp-content-wrap' => array(
				'z-index' => '5',
				'pointer-events' => 'none',
				'-webkit-transition' => '0.3s ease',
				'transition' => '0.3s ease',
			),

			'.wp-show-posts-image:hover .wp-show-posts-image-overlay,
			.wpsp-overlay .wp-show-posts-inner:hover .wp-show-posts-image .wp-show-posts-image-overlay' => array(
				'z-index' => '3',
				'opacity' => '1',
			),

			'.wp-show-posts-inner:hover .wp-show-posts-image.zoom img' => array(
				'-webkit-transform' => 'scale(1.1)',
				'transform' => 'scale(1.1)',
			)
		);

		$visual_css = array_merge( $visual_css, $overlay_css );
	}

	if ( 'wpsp-overlay-style-one' === $settings['card'] ) {
		$wpsp_overlay_style_one = array(
			'.wpsp-overlay.wpsp-ov-style-one .wpsp-content-wrap' => array(
			    'display' => 'grid',
			    'text-align' => 'center',
			),

			'.wpsp-overlay.wpsp-ov-style-one .wp-show-posts-entry-meta-below-post' => array(
			    '-ms-flex-item-align' => 'end',
			    'align-self' => 'end',
			),

			'.wpsp-overlay.wpsp-ov-style-one .wpsp-social' => array(
			    'grid-row' => '1',
			),

			'.wpsp-overlay.wpsp-ov-style-one .post .wp-show-posts-entry-summary' => array(
			    'opacity' => '0.0',
			    '-webkit-transform' => 'translateY(1em)',
			    'transform' => 'translateY(1em)',
			    '-webkit-transition' => 'all 0.3s cubic-bezier(.33, .66, .66, 1)',
			    'transition' => 'all 0.3s cubic-bezier(.33, .66, .66, 1)',
			    '-webkit-backface-visibility' => 'hidden',
			    'backface-visibility' => 'hidden',
			),

			'.wpsp-overlay.wpsp-ov-style-one .wp-show-posts-inner:hover .wp-show-posts-entry-summary' => array(
			    'opacity' => '1',
			    '-webkit-transform' => 'translateY(0)',
			    'transform' => 'translateY(0)',
			),
		);

		$visual_css = array_merge( $visual_css, $wpsp_overlay_style_one );
	}

	if ( 'wpsp-overlay-style-two' === $settings['card'] ) {
		$wpsp_overlay_style_two = array(
			'.wpsp-overlay.wpsp-ov-style-two .wp-show-posts-inner' => array(
			    '-webkit-transform' => 'translateY(0)',
			    'transform' => 'translateY(0)',
			    '-webkit-transition' => 'all 0.3s cubic-bezier(.33, .66, .66, 1)',
			    'transition' => 'all 0.3s cubic-bezier(.33, .66, .66, 1)',
			),

			'.wpsp-overlay.wpsp-ov-style-two .wp-show-posts-inner:hover' => array(
			    '-webkit-transform' => 'translateY(0.5em)',
			    'transform' => 'translateY(0.5em)',
			),

			'.wpsp-overlay.wpsp-ov-style-two .wp-show-posts-image, .wpsp-overlay.wpsp-ov-style-two .wp-show-posts-image img' => array(
			    'min-height' => '360px',
			),
		);

		$visual_css = array_merge( $visual_css, $wpsp_overlay_style_two );
	}

	if ( 'wpsp-overlap' === $settings['card'] ) {
		$overlap_css = array(
			'#' . $id . ' .wp-show-posts-inner' => array(
				'background-color' => 'transparent',
			),

			'.wpsp-overlap .wpsp-content-wrap' => array(
			    'width' => '75%',
			    'margin-top' => '-3em',
			    'z-index' => '5',
				'position' => 'relative',
			),

			'.wpsp-overlap .wp-show-posts-entry-header' => array(
			    'display' => '-webkit-box',
			    'display' => '-ms-flexbox',
			    'display' => 'flex',
			    '-webkit-box-orient' => 'vertical',
			    '-webkit-box-direction' => 'reverse',
			    '-ms-flex-direction' => 'column-reverse',
			    'flex-direction' => 'column-reverse',
			),
		);

		$visual_css = array_merge( $visual_css, $overlap_css );
	}

	// Output the above CSS
	$output = '';
	foreach( $visual_css as $k => $properties ) {

		if ( ! count( $properties ) ) {
			continue;
		}

		$temporary_output = $k . ' {';
		$elements_added = 0;

		foreach( $properties as $p => $v ) {

			if ( empty( $v ) ) {
				continue;
			}

			$elements_added++;
			$temporary_output .= $p . ': ' . $v . '; ';

		}

		$temporary_output .= "}";

		if ( $elements_added > 0 ) {
			$output .= $temporary_output;
		}

	}

	$output = str_replace(array("\r", "\n"), '', $output);

	if ( '' !== $output ) {
		echo '<style>';
		    echo $output;
		echo '</style>';
	}
}
