var nfRadio = Backbone.Radio;
require( ['controllers/loadContent'], function( LoadContent ) {

	var NFLayouts = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'app' ), 'after:appStart', this.afterNFLoad );
		},

		onStart: function() {
			new LoadContent();
		},

		afterNFLoad: function( app ) {
			var builderEl = nfRadio.channel( 'app' ).request( 'get:builderEl' );
			jQuery( builderEl ).addClass( 'layouts' );
		}
	} );

	var nfLayouts = new NFLayouts();
	nfLayouts.start();
} );