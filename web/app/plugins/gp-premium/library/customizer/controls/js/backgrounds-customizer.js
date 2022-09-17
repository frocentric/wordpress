( function( api ) {
	api.controlConstructor[ 'gp-background-images' ] = api.Control.extend( {
		ready() {
			var control = this;

			control.container.on( 'change', '.generatepress-backgrounds-repeat select',
				function() {
					control.settings.repeat.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change', '.generatepress-backgrounds-size select',
				function() {
					control.settings.size.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change', '.generatepress-backgrounds-attachment select',
				function() {
					control.settings.attachment.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'input', '.generatepress-backgrounds-position input',
				function() {
					control.settings.position.set( jQuery( this ).val() );
				}
			);
		},
	} );
}( wp.customize ) );
