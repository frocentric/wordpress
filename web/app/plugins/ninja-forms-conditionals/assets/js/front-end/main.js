var nfRadio = Backbone.Radio;

require( [ 'controllers/initCollection', 'controllers/showHide', 'controllers/showHideOption', 'controllers/changeValue', 'controllers/selectDeselect', 'controllers/actions' ], function( InitCollection, ShowHide, ShowHideOption, ChangeValue, SelectDeselect, Actions ) {

	var NFConditionalLogic = Marionette.Application.extend( {

		initialize: function( options ) {
			this.listenTo( nfRadio.channel( 'form' ), 'after:loaded', this.loadControllers );
		},

		loadControllers: function( formModel ) {
			new ShowHide();
			new ShowHideOption();
			new ChangeValue();
			new SelectDeselect();
			new Actions();
			new InitCollection( formModel );
		},

		onStart: function() {
			
		}
	} );

	var nfConditionalLogic = new NFConditionalLogic();
	nfConditionalLogic.start();
} );