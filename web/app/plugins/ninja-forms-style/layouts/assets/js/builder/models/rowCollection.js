/**
 * Holds all of our row models.
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/rowModel'], function( rowModel ) {
	var collection = Backbone.Collection.extend( {
		model: rowModel,
		comparator: 'order',

		initialize: function( models ) {
			this.updateMaxCols( models );
			this.on( 'add:cell', this.updateMaxCols, this );
			this.on( 'destroy:cell', this.updateMaxCols, this );
			this.on( 'remove:cell', this.updateMaxCols, this );
			this.on( 'destroy', this.updateMaxCols, this );
			
			this.on( 'add:field', this.addField, this );
			this.on( 'append:field', this.appendField, this );
			this.on( 'remove:field', this.removeField, this );
		},

		updateMaxCols: function( models ) {
			var maxCols = 1;
			if ( true === models instanceof Backbone.Model ) {
				models = this.models
			}
			_.each( models, function( row ) {
				if ( 'undefined' != typeof row.cells ) {
					if ( maxCols < row.cells.length ) {
						maxCols = row.cells.length;
					}					
				} else if ( true === row instanceof Backbone.Model ) {
					if ( maxCols < row.get( 'cells' ).length ) {
						maxCols = row.get( 'cells' ).length;
					}
				}
					
			} );

			nfRadio.channel( 'layouts' ).request( 'update:colClass', maxCols );
		},

		addField: function( fieldModel ) {
			if ( ! fieldModel.get( 'oldCellcid' ) ) {
				this.appendField( fieldModel );
				return false;
			}

			var cellModel = false;
			this.every( function( rowModel ) {
				if ( rowModel.get( 'cells' ).get( { cid: fieldModel.get( 'oldCellcid' ) } ) ) {
					cellModel = rowModel.get( 'cells' ).get( { cid: fieldModel.get( 'oldCellcid' ) } );
					return false;
				}
				return true;
			} );

			if ( cellModel ) {
				cellModel.get( 'fields' ).add( fieldModel );
				cellModel.collection.sort();
			} else {
				this.appendField( fieldModel );
			}

			fieldModel.set( 'oldCellcid', false );
		},

		removeField: function( fieldModel ) {
			if ( ! fieldModel.get( 'oldCellcid' ) ) {
				fieldModel.set( 'oldCellcid', fieldModel.get( 'cellcid' ) );
			}
			nfRadio.channel( 'layouts-cell' ).trigger( 'remove:field', fieldModel.get( 'id' ) );
		},

		appendField: function( fieldModel ) {
			nfRadio.channel( 'layouts' ).request( 'add:row', this, { field: fieldModel.get( 'key' ) } );
		}
	} );
	return collection;
} );