/**
 * Holds all of our cell field models.
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( ) {
	var collection = Backbone.Collection.extend( {
		comparator: 'cellOrder',

		initialize: function( models, options ) {
			this.options = options;
			
			// Listen to requests to remove a field from a collection.
			this.listenTo( nfRadio.channel( 'layouts-cell' ), 'remove:field', this.removeField );
			// We've been passed the cellModel to which this collection belongs.
			// this.options.cellModel = options.cellModel;
			_.each( models, function( model ) {
				if ( 'undefined' == typeof model ) return;
				model.set( 'cellcid', this.options.cellModel.cid, { silent: true } );
			}, this );

			// When we add or remove a field from this collection, update our cellModel.
			this.on( 'add', this.addField, this );
			this.on( 'remove', this.updateCellModel, this );
			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );

			// When we remove a model from our main field collection, make sure it's removed from this collection as well.
			fieldCollection.on( 'remove', this.removeModel, this );

			// When we add a model to our main field collection, add it to this collection if its cid matches
			fieldCollection.on( 'add', this.addModel, this );

		},

		/**
		 * Add a field to our cell collection
		 * @since 3.0
		 */
		 addField: function( model ) {
		 	model.set( 'cellcid', this.options.cellModel.cid, { silent: true } );
		 	if ( 1 == this.options.cellModel.collection.length ) {
		 		var order = this.options.cellModel.collection.options.rowModel.get( 'order' );
		 		this.remove( model );
		 		nfRadio.channel( 'layouts' ).request( 'add:row', this.options.cellModel.collection.options.rowModel.collection, { order: order, field: model } );
		 	}
		 	this.updateCellModel();
		 },


		/**
		 * Update our cellModel.
		 * @since 3.0
		 */
		updateCellModel: function() {
			this.options.cellModel.set( 'fields', this );
			this.options.cellModel.trigger( 'change:fields', this.options.cellModel );
		},

		/**
		 * Respond to requests to remove a field from a collection.
		 * @since  3.0
		 * @param  string id field ID
		 * @return void
		 */
		removeField: function( id ) {
			if ( this.get( id ) ) {
				this.remove( this.get( id ) );
			}
		},

		removeModel: function( model ) {
			this.remove( model );
		},

		addModel: function( model ) {
			if ( 'undefined' != typeof this.options.cellModel && this.options.cellModel.cid == model.get( 'cellcid' ) ) {
				this.add( model );
			}
		}
	} );
	return collection;
} );