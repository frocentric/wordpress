/**
 * Listen and respond to undo events.
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'changes' ).reply( 'undo:movedBetweenCells', this.undoMovedBetweenCells, this );
			nfRadio.channel( 'changes' ).reply( 'undo:gutterDropNewField', this.undoGutterDropNewField, this );
			nfRadio.channel( 'changes' ).reply( 'undo:gutterSplitCell', this.undoGutterSplitCell, this );
			nfRadio.channel( 'changes' ).reply( 'undo:cellSorting', this.undoCellSorting, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removedCell', this.undoRemovedCell, this );
			nfRadio.channel( 'changes' ).reply( 'undo:cellNewField', this.undoCellNewField, this );
			nfRadio.channel( 'changes' ).reply( 'undo:rowNewField', this.undoRowNewField, this );
			nfRadio.channel( 'changes' ).reply( 'undo:gutterResize', this.undoGutterResize, this );
			nfRadio.channel( 'changes' ).reply( 'undo:movedToNewRow', this.undoMovedToNewRow, this );
			nfRadio.channel( 'changes' ).reply( 'undo:rowSorting', this.undoRowSorting, this );
		},

		/**
		 * Undo moving a field between cells.
		 * 
		 * @since  3.0
		 * @param  Backbone.model 	change  change model
		 * @param  bool 			undoAll are we undoing everything?
		 * @return void
		 */
		undoMovedBetweenCells: function( change, undoAll ) {
			var fieldModel = change.get( 'model' );
			var senderOldOrder = change.get( 'data' ).senderOldOrder;
			var receiverOldOrder = change.get( 'data' ).receiverOldOrder;
			
			var originalCollection = change.get( 'data' ).originalCollection;
			var newCollection = change.get( 'data' ).newCollection;
			
			originalCollection.add( fieldModel );
			/*
			 * We have to update every model's order based upon our order array.
			 * Loop through all of our fields and update their order value
			 */
			_.each( originalCollection.models, function( field ) {
				var id = field.get( 'id' );
				
				// Get the index of our field inside our order array
				var newPos = senderOldOrder.indexOf( id );
				field.set( 'cellOrder', newPos );
			} );

			originalCollection.sort();

			newCollection.remove( fieldModel );

			/*
			 * We have to update every model's order based upon our order array.
			 * Loop through all of our fields and update their order value
			 */
			_.each( newCollection.models, function( field ) {
				var id = field.get( 'id' );
				
				// Get the index of our field inside our order array
				var newPos = receiverOldOrder.indexOf( id );
				field.set( 'cellOrder', newPos );
			} );

			newCollection.sort();

			this.maybeRemoveChange( change, undoAll );
			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		/**
		 * Undo dropping a new field type onto our gutter/divider
		 * 
		 * @since  3.0
		 * @param  Backbone.model 	change  change model
		 * @param  bool 			undoAll are we undoing everything?
		 * @return void
		 */
		undoGutterDropNewField: function( change, undoAll ) {
			// Remove our new field
			var fieldModel = change.get( 'model' );
			var fieldCollection = change.get( 'data' ).fieldCollection;
			var newCell = change.get( 'data' ).newCell;

			fieldCollection.remove( fieldModel );
			// Remove our new cell if we don't have any fields left
			if ( 0 == newCell.get( 'fields' ).models.length ) {
				newCell.collection.remove( newCell );				
			}

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		/**
		 * Undo dropping an existing field onto a gutter and adding a new cell.
		 * 
		 * @since  3.0
		 * @param  Backbone.model 	change  change model
		 * @param  bool 			undoAll are we undoing everything?
		 * @return void
		 */
		undoGutterSplitCell: function( change, undoAll ) {
			var fieldModel = change.get( 'model' );
			var oldCollection = change.get( 'data' ).oldCollection;
			var newCell = change.get( 'data' ).newCell;
			var cellCollection = change.get( 'data' ).cellCollection;

			/*
			 * Check to see if this was the only item in a row.
			 * If it was, we need to insert a new row.
			 */
			if( 'undefined' == typeof oldCollection.options.cellModel.collection.options.rowModel.collection ) {
				var order = oldCollection.options.cellModel.collection.options.rowModel.get( 'order' );
				var newRowModel = nfRadio.channel( 'layouts' ).request( 'add:row', cellCollection.options.rowModel.collection, { order: order, field: fieldModel.get( 'id' ) } );
			} else { // We can just add the field back to it's original collection because a row already exists.
				oldCollection.add( fieldModel );
			}

			// Remove our new cell
			newCell.get( 'fields' ).remove( fieldModel );
			cellCollection.remove( newCell )
			cellCollection.sort();	

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoCellSorting: function( change, undoAll ) {
			var data = change.get( 'data' );
			var fieldCollection = data.fieldCollection;
			var order = data.oldOrder;

			/*
			 * We have to update every model's order based upon our order array.
			 * Loop through all of our fields and update their order value
			 */
			_.each( fieldCollection.models, function( field ) {
				var id = field.get( 'id' );
				
				// Get the index of our field inside our order array
				var newPos = order.indexOf( id );
				field.set( 'cellOrder', newPos );
			} );

			fieldCollection.sort();

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoRemovedCell: function( change, undoAll ) {
			var data = change.get( 'data' );
			var cellModel = data.cellModel;
			var cellCollection = data.cellCollection;
			var rowModel = data.rowModel;
			var rowCollection = data.rowCollection;

			/*
			 * Put the cell back into our cell collection.
			 */
			cellCollection.add( cellModel );

			/*
			 * If we don't have a data.newRows property, then we didn't create new rows as a result of our cell removal.
			 */
			if ( 'undefined' != typeof data.newRows ) { // We removed a cell that resulted in creating a bunch of new rows.
				var newRows = data.newRows;
				// Remove our old rows
				rowCollection.remove( newRows );
				// Re-add our row model that was removed.
				rowCollection.add( rowModel );
			}

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoCellNewField: function( change, undoAll ) {
			// Remove our new field
			var fieldModel = change.get( 'model' );
			var fieldCollection = change.get( 'data' ).collection;
			fieldCollection.remove( fieldModel );

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoRowNewField: function( change, undoAll ) {
			// Remove our new field
			var fieldModel = change.get( 'model' );
			var fieldCollection = change.get( 'data' ).collection;
			fieldCollection.remove( fieldModel );

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoGutterResize: function( change, undoAll ) {
			// Reset our sizes
			var data = change.get( 'data' );
			var gutter = data.gutter; 
			var cellCollection = data.cellCollection;
			var modelA = data.modelA;
			var modelB = data.modelB;

			var oldModelAWidth = data.oldModelAWidth;
			var oldModelBWidth = data.oldModelBWidth;

			modelA.set( 'width', oldModelAWidth );
			modelB.set( 'width', oldModelBWidth );

			jQuery( gutter ).find( '.percent-left' ).remove();
			jQuery( gutter ).find( '.percent-right' ).remove();

			cellCollection.sort();

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();

		},

		undoMovedToNewRow: function( change, undoAll ) {
			/*
			 * Move the field back to its original cell.
			 */
			var fieldModel = change.get( 'model' );
			var originalCollection = change.get( 'data' ).originalCollection;
			originalCollection.add( fieldModel );

			/*
			 * Remove our new row model
			 */
			var rowModel = change.get( 'data' ).rowModel;
			rowModel.collection.remove( rowModel );

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		undoRowSorting: function( change, undoAll ) {
			var oldOrder = change.get( 'data' ).oldOrder;
			var rowCollection = change.get( 'data' ).rowCollection;

			/*
			 * We have to update every model's order based upon our order array.
			 * Loop through all of our fields and update their order value
			 */
			_.each( rowCollection.models, function( rowModel ) {
				var cid = rowModel.cid;
				
				// Get the index of our field inside our order array
				var newPos = oldOrder.indexOf( cid );
				rowModel.set( 'order', newPos );
			} );

			rowCollection.sort();

			this.maybeRemoveChange( change, undoAll );

			/*
			 * Enable the next Layouts change
			 */
			this.enableNextChange();
		},

		enableNextChange: function() {
			/*
			 * Enable the next Layouts change
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			var found = false;
			_.each( changeCollection.models, function( changeModel ) {
				var data = changeModel.get( 'data' );
				if ( ! found && 'undefined' != typeof data.layouts && data.layouts ) {
					changeModel.set( 'disabled', false );
					found = true;
				}
			}, this );
		},

		/**
		 * If our undo action was requested to 'remove' the change from the collection, remove it.
		 * 
		 * @since  3.0
		 * @param  backbone.model 	change 	model of our change
		 * @param  boolean 			remove 	should we remove this item from our change collection
		 * @return void
		 */
		maybeRemoveChange: function( change, undoAll ) {			
			var undoAll = typeof undoAll !== 'undefined' ? undoAll : false;
			if ( ! undoAll ) {
				// Update preview.
				nfRadio.channel( 'app' ).request( 'update:db' );
				var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
				changeCollection.remove( change );
				if ( 0 == changeCollection.length ) {
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', true );
					nfRadio.channel( 'app' ).request( 'close:drawer' );
				}
			}
		}
	});

	return controller;
} );
