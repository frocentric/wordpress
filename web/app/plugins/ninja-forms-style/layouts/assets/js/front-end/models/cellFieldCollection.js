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
		comparator: 'order',

		initialize: function( models, options ) {
			// We've been passed the cellModel to which this collection belongs.
			this.cellModel = options.cellModel;
			_.each( models, function( model ) {
				model.set( 'cellcid', this.cellModel.cid, { silent: true } );
			}, this );

			this.listenTo( this.cellModel.collection.rowModel.collection, 'validate:fields', this.validateFields );
			this.listenTo( this.cellModel.collection.rowModel.collection, 'show:fields', this.showFields );
			this.listenTo( this.cellModel.collection.rowModel.collection, 'hide:fields', this.hideFields );
		
			var fieldCollection = this.cellModel.collection.formModel.get( 'fields' );

			// When we remove a model from our main field collection, make sure it's removed from this collection as well.
			fieldCollection.on( 'reset', this.resetCollection, this );
		},

		validateFields: function() {
			_.each( this.models, function( fieldModel ) {
				fieldModel.set( 'clean', false ); // @todo remove after this is released in core.
				nfRadio.channel( 'submit' ).trigger( 'validate:field', fieldModel );
			}, this );
		},

		showFields: function() {
			this.invoke( 'set', { visible: true } );
            this.invoke( function() {
                this.trigger( 'change:value', this );
            });
		},

		hideFields: function() {
			this.invoke( 'set', { visible: false } );
            this.invoke( function() {
                this.trigger( 'change:value', this );
            });
		},

		/**
		 * When we reset our main field collection, we need to reset any of those fields in this collection.
		 * 
		 * @since  3.0.12
		 * @param  Backbone.Collection 		collection
		 * @return void
		 */
		resetCollection: function( collection ) {
			var fieldModels = [];
			_.each( this.models, function( fieldModel ) {
				if ( 'submit' != fieldModel.get( 'type' ) ) {
					fieldModels.push( collection.findWhere( { key: fieldModel.get( 'key' ) } ) );
				} else {
					fieldModels.push( fieldModel );
				}
			} );
			this.reset( fieldModels );
		}

	} );
	return collection;
} );
