( function( $, api ) {
	api.controlConstructor[ 'gp-copyright' ] = api.Control.extend( {
		ready() {
			var control = this;
			$( '.gp-copyright-area', control.container ).on( 'change keyup',
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		},
	} );
}( jQuery, wp.customize ) );
