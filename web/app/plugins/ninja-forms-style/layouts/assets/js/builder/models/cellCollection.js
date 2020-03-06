/**
 * Holds all of our cell models.
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/cellModel'], function( cellModel ) {
	var collection = Backbone.Collection.extend( {
		model: cellModel,
		comparator: 'order',

		initialize: function( models, options ) {
			this.options = options;
			this.on( 'change:fields', this.updateRowModel, this );
			this.on( 'add', this.addCell, this );
			this.on( 'remove', this.updateCellWidths, this );
		},

		addCell: function() {
			this.updateCellWidths();
			this.updateRowModel();
			this.options.rowModel.trigger( 'add:cell', this.options.rowModel );
		},

		updateRowModel: function() {
			this.options.rowModel.set( 'cells', this );
			this.options.rowModel.trigger( 'change:cells', this.options.rowModel );
		},

		/**
		 * Update our cell widths.
		 * This is called whenever we add or remove a cell from our cell collection.
		 * 
		 * @since  3.0
		 * @param  Backbone.Model 	cellModel
		 * @return void
		 */
		updateCellWidths: function( cellModel ) {
			// Calculate a new width for our cells.
			var width = Math.round( 100 / this.models.length );

			if ( 100 < width * this.models.length ) {
				width = Math.floor( 100 / this.models.length );
			}

			// Set our width for each cell.
			_.each( this.models, function( cell ) {
				cell.set( 'width', width );
			} );

			this.sort();
		}
	} );
	return collection;
} );