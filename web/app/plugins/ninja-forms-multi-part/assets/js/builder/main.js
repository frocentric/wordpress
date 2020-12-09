var nfRadio = Backbone.Radio;

require( [ 'controllers/loadControllers', 'controllers/filters' ], function( LoadControllers, LoadFilters ) {

	var NFMultiPart = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'app' ), 'after:loadControllers', this.loadControllers );
		},

		loadControllers: function() {
			new LoadControllers();
		},

		onStart: function() {
			new LoadFilters();
		}
	} );

	var nfMultiPart = new NFMultiPart();
	nfMultiPart.start();
} );