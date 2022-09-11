/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Container width
	wp.customize( 'generate_settings[container_width]', function( value ) {
		value.bind( function() {
			if ( $( '.masonry-container' )[ 0 ] ) {
				jQuery( '.masonry-container' ).imagesLoaded( function() {
					$container = jQuery( '.masonry-container' );
					if ( jQuery( $container ).length ) {
						$container.masonry( {
							columnWidth: '.grid-sizer',
							itemSelector: '.masonry-post',
							stamp: '.page-header',
						} );
					}
				} );
			}
		} );
	} );

	$( 'body' ).on( 'generate_spacing_updated', function() {
		if ( $( '.masonry-container' )[ 0 ] ) {
			jQuery( '.masonry-container' ).imagesLoaded( function() {
				$container = jQuery( '.masonry-container' );
				if ( jQuery( $container ).length ) {
					$container.masonry( {
						columnWidth: '.grid-sizer',
						itemSelector: '.masonry-post',
						stamp: '.page-header',
					} );
				}
			} );
		}
	} );

	/**
	 * The first infinite scroll load in the Customizer misses article classes if they've been
	 * added or removed in the previous refresh.
	 *
	 * This is totally hacky, but I'm just happy I finally got it working!
	 */
	var $container = $( '.infinite-scroll-item' ).first().parent();
	$container.on( 'load.infiniteScroll', function( event, response ) {
		var $posts = $( response ).find( 'article' );
		if ( wp.customize.value( 'generate_blog_settings[column_layout]' )() ) {
			$posts.addClass( 'generate-columns' );
			$posts.addClass( 'grid-parent' );
			$posts.addClass( 'grid-' + wp.customize.value( 'generate_blog_settings[columns]' )() );
			$posts.addClass( 'tablet-grid-50' );
			$posts.addClass( 'mobile-grid-100' );
		} else {
			$posts.removeClass( 'generate-columns' );
			$posts.removeClass( 'grid-parent' );
			$posts.removeClass( 'grid-' + wp.customize.value( 'generate_blog_settings[columns]' )() );
			$posts.removeClass( 'tablet-grid-50' );
			$posts.removeClass( 'mobile-grid-100' );
		}

		if ( wp.customize.value( 'generate_blog_settings[masonry]' )() ) {
			$posts.addClass( 'masonry-post' );
		} else {
			$posts.removeClass( 'masonry-post' );
		}

		if ( ! wp.customize.value( 'generate_blog_settings[post_image_padding]' )() ) {
			$posts.addClass( 'no-featured-image-padding' );
		} else {
			$posts.removeClass( 'no-featured-image-padding' );
		}
	} );
}( jQuery ) );
