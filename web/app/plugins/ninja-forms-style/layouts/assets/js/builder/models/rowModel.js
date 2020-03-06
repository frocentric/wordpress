/**
 * Model that holds our row information
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/cellCollection'], function( CellCollection ) {
	var model = Backbone.Model.extend( {

		initialize: function( models, options ) {
			this.options = options;
			this.set( 'cells', new CellCollection( this.get( 'cells' ), { rowModel: this } ) );
			this.on( 'change:cells', this.checkEmptyCells, this );
            this.set( 'order', Number( this.get( 'order' ) ) );
		},

		checkEmptyCells: function( model ) {
			/*
			 * Check to see if all our cells are empty. If they are, self destruct.
			 */
			var remove = true;
			_.each( this.get( 'cells' ).models, function( cell ) {
				if ( 0 != cell.get( 'fields' ).length ) {
					remove = false;
				}
			} );

			if ( remove && 'undefined' != typeof this.collection ) {
				this.collection.remove( this );
				return false;
			}

			return true;
		}		
	} );
	
	return model;
} );