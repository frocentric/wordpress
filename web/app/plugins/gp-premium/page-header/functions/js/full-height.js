/**
 * Author: Tom Usborne
 * jQuery Simple Parallax for Page Header background
 *
 */

// Build the header height function
function generateHeaderHeight() {
	// If we're not using a full screen element, bail.
	if ( ! jQuery( '.fullscreen-enabled' ).length )
		return;
	
	// Set up some variables
	var page_header_content;
	var window_height = jQuery( window ).height();
	
	// Get our page header content div
	if ( jQuery( '.inside-page-header' ).length ) {
		page_header_content = jQuery( '.inside-page-header' );
	} else if ( jQuery( '.generate-inside-combined-content' ).length ) {
		page_header_content = jQuery( '.generate-inside-combined-content' );
	} else {
		page_header_content = jQuery( '.generate-inside-page-header-content' );
	}
	
	// Get any space above our page header
	var offset = jQuery(".fullscreen-enabled").offset().top;
	
	// Apply the height to our div
	jQuery( '.fullscreen-enabled' ).css( 'height', window_height - offset + 'px' );

	// If our page header content is taller than our window, remove the height
	if ( page_header_content.outerHeight() > ( window_height - offset ) ) {
		jQuery( '.fullscreen-enabled' ).attr( 'style', 'height: initial !important' );
	}
}

jQuery(document).ready(function($) {
	
	// Run the header height function
	generateHeaderHeight();
	
	// Set up the resize timer
	var generateResizeTimer;
	
	if ( jQuery('.generate-page-header.fullscreen-enabled')[0] ) {
		// Initiate full window height on resize
		var width = $(window).width();
		$(window).resize(function() {
			if($(window).width() != width){
				clearTimeout(generateResizeTimer);
				generateResizeTimer = setTimeout(generateHeaderHeight, 200);
				width = $(window).width();
			}
		});
		
		$( window ).on( "orientationchange", function( event ) {
			if($(window).width() != width){
				clearTimeout(generateResizeTimer);
				generateResizeTimer = setTimeout(generateHeaderHeight, 200);
				width = $(window).width();
			}
		});
	}

});