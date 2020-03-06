define( [ 'models/rowCollection' ], function( RowCollection ) {
	var controller = Marionette.Object.extend( {
		overSortable: false,
		outFired: false,
		overCell: false,
		overRows: false,

		initialize: function() {
			// Respond to requests to add a row to our collection.
			nfRadio.channel( 'layouts' ).reply( 'add:row', this.addRow, this );
		},

		addRow: function( rowCollection, data ) {

			if ( ! rowCollection ) {
				/*
				 * In the RC for Ninja Forms, 'formContentData' was 'fieldContentsData'.
				 * In 3.0, we changed it to 'formContentData', so this line checks for that old setting name if the new one doesn't exist.
				 * This is for backwards compatibility and can be removed in the future.
				 *
				 * TODO: Remove the || portion of this ternary.
				 */
				rowCollection = nfRadio.channel( 'settings' ).request( 'get:setting', 'formContentData' ) || nfRadio.channel( 'settings' ).request( 'get:setting', 'fieldContentsData' );
				if ( false === rowCollection instanceof RowCollection ) return false;
			}

			if ( 'undefined' == typeof data.order || null == data.order ) {
				/*
				 * Get the order of the last item in our row collection.
				 */
				rowOrder = rowCollection.pluck( 'order' );
				data.order = ( 0 < rowOrder.length ) ? _.max( rowOrder ) + 1 : 1;
			}

			var rowModel = rowCollection.add( {
				order: data.order,
				cells: [
					{
						order: 0,
						fields: [ data.field ],
						width: '100'
					}
				]
			} );

			return rowModel;
		},

		updateOverSortable: function( val ) {
			this.overSortable = val;
		},

		getOverSortable: function() {
			return this.overSortable;
		},

		updateOutFired: function( val ) {
			this.outFired = val;
		},

		getOutFired: function() {
			return this.outFired;
		},

		updateOverCell: function( val ) {
			this.overCell = val;
		},

		getOverCell: function() {
			return this.overCell;
		}
	});

	return controller;
} );