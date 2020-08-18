/**
 * Handles events for our bottom drawer part title sortable
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define(	[],	function () {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'mp' ), 'start:partSortable', this.start );
			this.listenTo( nfRadio.channel( 'mp' ), 'stop:partSortable', this.stop );
			this.listenTo( nfRadio.channel( 'mp' ), 'update:partSortable', this.update );
		},

		start: function( e, ui, collection, collectionView ) {
			// If we aren't dragging an item in from types or staging, update our change log.
			if( ! jQuery( ui.item ).hasClass( 'nf-field-type-draggable' ) && ! jQuery( ui.item ).hasClass( 'nf-stage' ) ) { 
				jQuery( ui.item ).css( 'opacity', '0.5' ).show();
				jQuery( ui.helper ).css( 'opacity', '0.75' );
			}
		},

		stop: function( e, ui, collection, collectionView ) {
			// If we aren't dragging an item in from types or staging, update our change log.
			if( ! jQuery( ui.item ).hasClass( 'nf-field-type-draggable' ) && ! jQuery( ui.item ).hasClass( 'nf-stage' ) ) { 
				jQuery( ui.item ).css( 'opacity', '' );
			}
		},

		update: function( e, ui, collection, collectionView ) {
			var partModel = collection.findWhere( { key: jQuery( ui.item ).prop( 'id' ) } );
			/*
			 * Store our current order.
			 */
			var oldOrder = {};
			collection.each( function( partModel, index ) {
				oldOrder[ partModel.get( 'key' ) ] = index;
			} );

			jQuery( ui.item ).css( 'opacity', '' );

			var order = _.without( jQuery( collectionView.el ).sortable( 'toArray' ), '' );
			_.each( order, function( key, index ) {
				collection.findWhere( { key: key } ).set( 'order', index );
			}, this );
			collection.sort();

			/*
			 * Register our part change to the change manager.
			 */
			//Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );

			// Add our field addition to our change log.
			var label = {
				object: 'Part',
				label: partModel.get( 'title' ),
				change: 'Sorted',
				dashicon: 'sort'
			};

			var data = {
				oldOrder: oldOrder,
				collection: collection
			};

			var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'sortParts', partModel, null, label, data );
		},

	});

	return controller;
} );