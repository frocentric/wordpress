/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Update the site title in real time...
	wp.customize( 'generate_copyright', function( value ) {
		value.bind( function( newval ) {
			if ( $( '.copyright-bar' ).length ) {
				$( '.copyright-bar' ).html( newval );
			} else {
				$( '.inside-site-info' ).html( newval );
			}
		} );
	} );
}( jQuery ) );
