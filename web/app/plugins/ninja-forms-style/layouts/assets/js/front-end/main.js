var nfRadio = Backbone.Radio;
require( ['controllers/loadControllers'], function( LoadControllers ) {

	var NFLayouts = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'form' ), 'before:filterData', this.loadControllers );
		},

		loadControllers: function( app ) {
			new LoadControllers();
		}
	} );

	var nfLayouts = new NFLayouts();
	nfLayouts.start();
} );