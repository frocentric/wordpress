jQuery( function( $ ) {
	$( '.generatepress-shortcuts a' ).on( 'click', function( e ) {
		e.preventDefault();
		var section = $( this ).attr( 'data-section' ),
			currentSection = $( this ).attr( 'data-current-section' ),
			destinationSectionElement = $( '[id$="' + section + '"]' );

		if ( section ) {
			wp.customize.section( section ).focus();

			destinationSectionElement.find( '.show-shortcuts' ).hide();
			destinationSectionElement.find( '.return-shortcut' ).show();
			destinationSectionElement.find( '.return-shortcut a' ).attr( 'data-return', currentSection );
		}
	} );

	$( '.return-shortcut .dashicons' ).on( 'click', function() {
		var container = $( this ).closest( '.generatepress-shortcuts' );

		container.find( '.show-shortcuts' ).show();
		container.find( '.return-shortcut' ).hide();
	} );

	$( '.return-shortcut a' ).on( 'click', function( e ) {
		e.preventDefault();

		var section = $( this ).attr( 'data-return' );
		var container = $( this ).closest( '.generatepress-shortcuts' );

		if ( section ) {
			wp.customize.section( section ).focus();

			container.find( '.show-shortcuts' ).show();
			container.find( '.return-shortcut' ).hide();
		}
	} );

	var customizeSectionBack = $( '.customize-section-back' );

	if ( customizeSectionBack ) {
		customizeSectionBack.on( 'click', function() {
			$( '.show-shortcuts' ).show();
			$( '.return-shortcut' ).hide();
		} );
	}
} );
