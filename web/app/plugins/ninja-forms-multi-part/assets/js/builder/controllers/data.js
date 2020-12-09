/**
 * Holds our part collection.
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'models/partCollection' ], function ( PartCollection) {
	var controller = Marionette.Object.extend( {
		layoutsEnabed: false,

		initialize: function() {
			/*
			 * Instantiate our part collection.
			 */
			nfRadio.channel( 'mp' ).reply( 'init:partCollection', this.initPartCollection, this );

			/*
			 * Listen for requests for our part collection.
			 */
			nfRadio.channel( 'mp' ).reply( 'get:collection', this.getCollection, this );

			/*
			 * If we don't have Layout & Styles active, when we add a field to our field collection, collection, trigger an "add:model"
			 */
			var formContentLoadFilters = nfRadio.channel( 'formContent' ).request( 'get:loadFilters' );

			/*
			 * Layout & Styles compatibility
			 * TODO: Super Hacky Bandaid fix for making sure we don't trigger an duplicating a field if Layouts is enabled.
			 * If it is enabled, Layouts handles adding duplicated items.
			 */
			this.layoutsEnabed = ( 'undefined' != typeof formContentLoadFilters[4] ) ? true : false;
			this.listenTo( nfRadio.channel( 'fields' ), 'render:newField', function( fieldModel, action ){
                action = action || '';
                if ( this.layoutsEnabed && 'duplicate' == action ) return false;
				this.addField( fieldModel, action );
			}, this );
			/* END Layout & Styles compatibility */

			this.listenTo( nfRadio.channel( 'fields' ), 'render:duplicateField', this.addField );
		},

		initPartCollection: function( partCollection ) {
			this.collection = partCollection;
		},

		getCollection: function() {
			return this.collection;
		},

		addField: function( fieldModel, action ) {
			if ( this.layoutsEnabed && 'duplicate' == action ) return false;
			this.collection.getFormContentData().trigger( 'add:field', fieldModel );
			if( 1 == this.collection.getFormContentData().length ) {
				this.collection.getFormContentData().trigger( 'reset' );
			}
		}

	});

	return controller;
} );
