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
			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );
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

			if ( false === this.get( 'fields' ) instanceof Backbone.Collection ) {
				this.set( 'fields', new CellFieldCollection( fieldModels, { cellModel: this } ) );
			}
            this.set( 'order', Number( this.get( 'order' ) ) );
		}
		
	} );
	
	return model;
} );