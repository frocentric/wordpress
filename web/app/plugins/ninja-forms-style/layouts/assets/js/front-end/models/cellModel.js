/**
 * Model that holds our cell information
 * 
 * @package Ninja Forms Layouts
 * @subpackage Layouts
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/cellFieldCollection'], function( CellFieldCollection) {
	var model = Backbone.Model.extend( {
		initialize: function() {
			var fieldCollection = this.collection.formModel.get( 'fields' );
			var fieldModels = [];
			_.each( this.get( 'fields' ), function( search ) {
				if ( 'undefined' == typeof fieldCollection.get( search ) ) {
					var findField = fieldCollection.findWhere( { key: search } );
					if ( 'undefined' != typeof findField ) {
						fieldModels.push( findField );
					}
				} else {
					fieldModels.push( fieldCollection.get( search ) );
				}
				
			} );
			this.set( 'fields', new CellFieldCollection( fieldModels, { cellModel: this } ) );
            this.set( 'order', Number( this.get( 'order' ) ) );
			this.listenTo( this.get( 'fields' ), 'change:errors', this.triggerErrors );
		},

		triggerErrors: function( fieldModel ) {
			this.collection.trigger( 'change:errors', fieldModel );
		}
		
	} );
	
	return model;
} );