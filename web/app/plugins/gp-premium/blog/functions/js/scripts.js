jQuery( document ).ready( function( $ ) {
	var $masonry_container = $( '.masonry-container' );
	var msnry = false;

	if ( $masonry_container.length ) {
		var $grid = $masonry_container.masonry({
			columnWidth: '.grid-sizer',
			itemSelector: 'none',
			stamp: '.page-header',
			percentPosition: true,
			stagger: 30,
			visibleStyle: { transform: 'translateY(0)', opacity: 1 },
			hiddenStyle: { transform: 'translateY(5px)', opacity: 0 },
		} );

		msnry = $grid.data( 'masonry' );

		$grid.imagesLoaded( function() {
			$grid.masonry( 'layout' );
			$grid.removeClass( 'are-images-unloaded' );
			$( '.load-more' ).removeClass( 'are-images-unloaded' );
			$( '#nav-below' ).css( 'opacity', '1' );
			$grid.masonry( 'option', { itemSelector: '.masonry-post' });
			var $items = $grid.find( '.masonry-post' );
			$grid.masonry( 'appended', $items );
		} );

		$( '#nav-below' ).insertAfter( '.masonry-container' );

		$( window ).on( "orientationchange", function( event ) {
			$grid.masonry( 'layout' );
		} );
	}

	if ( $( '.infinite-scroll' ).length && $( '.nav-links .next' ).length ) {
		var $container = $( '#main article' ).first().parent();
		var $button = $( '.load-more a' );
		var svgIcon = '';

		if ( blog.icon ) {
			svgIcon = blog.icon;
		}

		$container.infiniteScroll( {
			path: '.nav-links .next',
			append: '#main article',
			history: false,
			outlayer: msnry,
			loadOnScroll: $button.length ? false : true,
			button: $button.length ? '.load-more a' : null,
			scrollThreshold: $button.length ? false : 600,
		} );

		$button.on( 'click', function( e ) {
			$( this ).html( svgIcon + blog.loading ).addClass( 'loading' );
		} );

		$container.on( 'append.infiniteScroll', function( event, response, path, items ) {
			if ( ! $( '.generate-columns-container' ).length ) {
				$container.append( $button.parent() );
			}

			$( items ).find( 'img' ).each( function( index, img ) {
				img.outerHTML = img.outerHTML;
			} );

			if ( $grid ) {
				$grid.imagesLoaded( function() {
					$grid.masonry( 'layout' );
				} );
			}

			$button.html( svgIcon + blog.more ).removeClass( 'loading' );
		} );

		$container.on( 'last.infiniteScroll', function() {
			$( '.load-more' ).hide();
		} );
	}
} );
