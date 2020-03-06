/**
 * Layout view for our conditions
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/advanced/whenCollection', 'views/advanced/thenCollection', 'views/advanced/elseCollection' ], function( WhenCollectionView, ThenCollectionView, ElseCollectionView ) {
	var view = Marionette.LayoutView.extend({
		template: "#tmpl-nf-cl-advanced-condition",
		regions: {
			'when': '.nf-when-region',
			'then': '.nf-then-region',
			'else': '.nf-else-region'
		},

		initialize: function() {
			/*
			 * When we change the "collapsed" attribute of our model, re-render.
			 */
			this.listenTo( this.model, 'change:collapsed', this.render );

			/*
			 * When our drawer closes, send out a radio message on our setting type channel.
			 */
			this.listenTo( nfRadio.channel( 'drawer' ), 'closed', this.drawerClosed );
		},

		onRender: function() {
			var firstWhenDiv = jQuery( this.el ).find( '.nf-first-when' );
			this.when.show( new WhenCollectionView( { collection: this.model.get( 'when' ), firstWhenDiv: firstWhenDiv, conditionModel: this.model } ) );
			if ( ! this.model.get( 'collapsed' ) ) {
				this.then.show( new ThenCollectionView( { collection: this.model.get( 'then' ) } ) );
				this.else.show( new ElseCollectionView( { collection: this.model.get( 'else' ) } ) );
			}
		},

		events: {
			'click .nf-remove-condition': 'clickRemove',
			'click .nf-collapse-condition': 'clickCollapse',
			'click .nf-add-when': 'clickAddWhen',
			'click .nf-add-then': 'clickAddThen',
			'click .nf-add-else': 'clickAddElse'
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeCondition', e, this.model );
		},

		clickCollapse: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:collapseCondition', e, this.model );
		},

		clickAddWhen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addWhen', e, this.model );
		},

		clickAddThen: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addThen', e, this.model );
		},

		clickAddElse: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addElse', e, this.model );
		}
	});

	return view;
} );