define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'layouts' ).reply( 'update:colClass', this.updateColClass );
		},

		updateColClass: function( num ) {
			var builderEl = nfRadio.channel( 'app' ).request( 'get:builderEl' );
			jQuery( builderEl ).removeClass( 'few several many' );

			if ( num == 3 ) {
				var builderClass = 'few';
			} else if ( num >= 4 && num <= 5 ) {
				var builderClass = 'several';
			} else if ( num >= 6 ) {
				var builderClass = 'many';
			} else {
				var builderClass = '';
			}

			jQuery( builderEl ).addClass( builderClass );

		}

	});

	return controller;
} );
