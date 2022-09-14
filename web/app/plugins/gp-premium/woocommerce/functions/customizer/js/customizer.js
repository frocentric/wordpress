jQuery( function( $ ) {
	$( '#customize-control-generate_woocommerce_primary_button_message a' ).on( 'click', function( e ) {
		e.preventDefault();
		wp.customize.control( 'generate_settings[form_button_background_color]' ).focus();
	} );
} );
