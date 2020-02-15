<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'generate_get_blog_page_header' ) ) {
	/**
	 * Apply a filter to see if we should display the blog page header
	 * This allows you to show the Blog Page Header anywhere you want
	 */
	function generate_get_blog_page_header() {
		$page_header = ( is_home() ) ? true : false;
		return apply_filters( 'generate_get_blog_page_header', $page_header );
	}
}

if ( ! function_exists( 'generate_page_header_area_start_container' ) ) {
	/**
	 * Start our page header container
	 *
	 * This doesn't finish the container, as we can move it above the header
	 * which allows us to merge the header and page header without position:absolute
	 */
	function generate_page_header_area_start_container( $image_class, $content_class ) {
		$options = generate_page_header_get_options();
		$image_url = generate_page_header_get_image( 'URL' );
		$container_type = ( 'fluid' !== $options[ 'container_type' ] ) ? ' page-header-contained grid-container grid-parent' : '';

		// Parallax variable
		$parallax = ( ! empty( $options[ 'parallax' ] ) ) ? ' parallax-enabled' : '';
		$parallax_speed = apply_filters( 'generate_page_header_parallax_speed', 2 );

		// Full screen variable
		$full_screen = ( ! empty( $options[ 'full_screen' ] ) ) ? ' fullscreen-enabled' : '';

		// Vertical center variable
		$vertical_center_container = ( ! empty( $options[ 'vertical_center' ] ) && ! empty( $options[ 'full_screen' ] ) ) ? ' vertical-center-container' : '';
		$vertical_center = ( ! empty( $options[ 'vertical_center' ] ) && ! empty( $options[ 'full_screen' ] ) ) ? ' vertical-center-enabled' : '';

		// Do we have a video?
		$video_enabled = ( empty( $options[ 'background_video' ] ) && empty( $options[ 'background_video_ogv' ] ) && empty( $options[ 'background_video_webm' ] ) ) ? false : true;

		// Which types?
		$video_types = array(
			'mp4' => ( ! empty( $options[ 'background_video' ] ) ) ? 'mp4:' . esc_url( $options[ 'background_video' ] ) : null,
			'ogv' => ( ! empty( $options[ 'background_video_ogv' ] ) ) ? 'ogv:' . esc_url( $options[ 'background_video_ogv' ] ) : null,
			'webm' => ( ! empty( $options[ 'background_video_webm' ] ) ) ? 'webm:' . esc_url( $options[ 'background_video_webm' ] ) : null,
			'poster' => ( ! empty( $image_url ) ) ? 'poster:' . esc_url( $image_url ) : null
		);

		// Add our videos to a string
		$video_output = array();
		foreach( $video_types as $video => $val ) {
			$video_output[] = $val;
		}

		$video = null;
		// Video variable
		if ( $video_enabled && '' !== $options[ 'content' ] ) {

			$ext = ( ! empty( $image_url ) ) ? pathinfo( $image_url, PATHINFO_EXTENSION ) : false;
			$video_options = array();

			if ( $ext ) {
				$video_options[ 'posterType' ] = 'posterType:' . $ext;
			} else {
				$video_options[ 'posterType' ] = 'posterType: none';
			}

			$video_options[ 'className' ] = 'className:generate-page-header-video';

			if ( apply_filters( 'generate_page_header_video_loop', true ) ) {
				$video_options[ 'loop' ] = 'loop:true';
			} else {
				$video_options[ 'loop' ] = 'loop:false';
			}

			if ( apply_filters( 'generate_page_header_video_muted', true ) ) {
				$video_options[ 'muted' ] = 'muted:true';
			} else {
				$video_options[ 'muted' ] = 'muted:false';
			}

			$video_options[ 'autoplay' ] = 'autoplay:false';

			$video = sprintf( ' data-vide-bg="%1$s" data-vide-options="%2$s"',
				implode( ', ', array_filter( $video_output ) ),
				implode( ', ', array_filter( $video_options ) )
			);
		}

		// Write a class if we're merging the header
		$combined_content = ( '' !== $options[ 'merge' ] ) ? ' generate-combined-page-header' : '';

		// If content is set, show it
		if ( '' !== $options[ 'content' ] && false !== $options[ 'content' ] ) {
			printf(
				'<div id="page-header-%7$s" %1$s class="%2$s" %6$s>
					<div %3$s class="inside-page-header-container inside-content-header %4$s %5$s">',
				( 'fluid' == $options[ 'container_type' ] ) ? $video : null,
				$content_class . $parallax . $full_screen . $vertical_center_container . $container_type . $combined_content . ' generate-page-header generate-content-header',
				( 'fluid' !== $options[ 'container_type' ] ) ? $video : null,
				$vertical_center,
				( '' !== $options[ 'merge' ] ) ? 'generate-merged-header' : '',
				( ! empty( $parallax ) ) ? 'data-parallax-speed="' . esc_attr( $parallax_speed ) . '"' : '',
				$options[ 'page_header_id' ]
			);
		}

		do_action( 'generate_inside_merged_page_header' );
	}
}

if ( ! function_exists( 'generate_page_header_area' ) ) {
	/**
	 * Build our entire page header.
	 *
	 * @since 0.1
	 *
	 * @param $image_class The class to give our element if it's an image.
	 * @param $content_class The class to give our element if it's content.
	 */
	function generate_page_header_area( $image_class, $content_class ) {
		// Get our options
		$options = generate_page_header_get_options();

		// Get out of here if we don't have content or an image
		if ( '' == $options[ 'content' ] && ! generate_page_header_get_image( 'ALL' ) ) {
			return;
		}

		$inner_container = ( '' == $options[ 'inner_container' ] ) ? ' grid-container grid-parent' : '';

		do_action( 'generate_before_page_header' );

		// If an image is set and no content is set
		if ( '' == $options[ 'content' ] && generate_page_header_get_image( 'ALL' ) ) {

			printf(
				'<div class="%1$s">
					%2$s
						%4$s
					%3$s
				</div>',
				esc_attr( $image_class ) . $inner_container . ' generate-page-header',
				( ! empty( $options[ 'image_link' ] ) ) ? '<a href="' . esc_url( $options[ 'image_link' ] ) . '"' . apply_filters( 'generate_page_header_link_target','' ) . '>' : null,
				( ! empty( $options[ 'image_link' ] ) ) ? '</a>' : null,
				generate_page_header_get_image_output()
			);

		}

		// If content is set, show it
		if ( '' !== $options[ 'content' ] && false !== $options[ 'content' ] ) {
			// If we're not merging our header, we can start the container here
			// If we were merging, the container would be added in the generate_before_header hook
			if ( '' == $options[ 'merge' ] ) {
				generate_page_header_area_start_container( 'page-header-image', 'page-header-content' );
			}

			// Replace any found template tags
			$options[ 'content' ] = generate_page_header_template_tags( $options[ 'content' ] );

			// Print the rest of our page header HTML
			// The starting elements are inside generate_page_header_area_start_container()
			printf ( '<div class="page-header-content-wrapper %1$s %2$s">
						<div class="%3$s page-header-content-container">
							%4$s
								%6$s
							%5$s
						</div>
					 </div>
					</div>
				</div>',
				( '' !== $options[ 'merge' ] ) ? 'generate-combined-content' : '',
				$inner_container,
				( '' !== $options[ 'merge' ] ) ? 'generate-inside-combined-content' : 'generate-inside-page-header-content',
				( ! empty( $options[ 'add_padding' ] ) ) ? '<div class="inside-page-header">' : null,
				( ! empty( $options[ 'add_padding' ] ) ) ? '</div>' : null,
				( ! empty( $options[ 'autop' ] ) ) ? do_shortcode( wpautop( $options[ 'content' ] ) ) : do_shortcode( $options[ 'content' ] )
			);
		}

		do_action( 'generate_after_page_header' );
	}
}
