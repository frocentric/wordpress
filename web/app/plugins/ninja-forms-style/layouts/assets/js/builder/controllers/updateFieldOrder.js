/**
 * Recalculates our field order attribute.
 * This isn't the attribute used by Layouts but the one used by core.
 *
 * @since  3.0
 */ 
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'layouts' ).reply( 'update:fieldOrder', this.updateFieldOrder );
		},

		updateFieldOrder: function( rowCollection ) {
			var order = 1;
			/*
			 * Loop over our row collection and set the order attribute for any fields we find inside the cells.
			 */
			_.each( rowCollection.models, function( rowModel, rowIndex ) {
				/*
				 * Loop over our cells and update our field models 'order' attribute.
				 */
				_.each( rowModel.get( 'cells' ).models, function( cellModel, cellIndex ) {
					/*
					 * Loop over every field in our cell model and update its 'order' attribute.
					 */
					_.each( cellModel.get( 'fields' ).models, function( fieldModel, fieldIndex ) {
						fieldModel.set( 'order', order, { silent: true } );
						order++;
					} );
				} );
			} );
		}

	});

	return controller;
} );
