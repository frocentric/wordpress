/**
 * Layout view for our Action condition
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/actions/whenCollection' ], function( WhenCollection ) {
	var view = Marionette.LayoutView.extend( {
		template: '#tmpl-nf-cl-actions-condition-layout',

		regions: {
			'when': '.nf-when'
		},

		initialize: function( options ) {
			this.model = options.dataModel.get( 'conditions' );
			if ( ! options.dataModel.get( 'conditions' ) ) return;

			this.collection = options.dataModel.get( 'conditions' ).get( 'when' );
			this.conditionModel = options.dataModel.get( 'conditions' );
		},

		onRender: function() {
			if ( ! this.collection ) return;
			/*
			 * Show our "when" collection in the "when" area.
			 */
			this.when.show( new WhenCollection( { collection: this.collection } ) );
		},

		events: {
			'change .condition-setting': 'changeSetting',
			'click .nf-add-when': 'clickAddWhen'
		},

		clickAddWhen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addWhen', e, this.model );
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		}

	});

	return view;
} );