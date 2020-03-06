var nfRadio = Backbone.Radio;

require( [ 'controllers/loadControllers', 'models/conditionCollection' ], function( LoadControllers, ConditionCollection ) {

	var NFConditionalLogic = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'app' ), 'after:appStart', this.afterNFLoad );
		},

		onStart: function() {
			new LoadControllers();
		},

		afterNFLoad: function( app ) {
			/*
			 * Convert our form's "condition" setting into a collection.
			 */
			var conditions = nfRadio.channel( 'settings' ).request( 'get:setting', 'conditions' );

			if ( false === conditions instanceof Backbone.Collection ) {
				conditions = new ConditionCollection( conditions );
				nfRadio.channel( 'settings' ).request( 'update:setting', 'conditions', conditions, true );
			}
		}
	} );

	var nfConditionalLogic = new NFConditionalLogic();
	nfConditionalLogic.start();
} );