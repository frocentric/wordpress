/**
 * Controller that handles our cell sortable events.
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		// By default, we aren't dropping on a gutter/divider
		dropping: false,
		received: false,

		initialize: function() {
			/*
			 * Respond to requests for our sortable drag helper.
			 */
			nfRadio.channel( 'layouts' ).reply( 'getHelper:cellSortable', this.getHelper, this );
			
			/*
			 * Listen to events triggered by our cell sortables.
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'over:cellSortable', this.over );
			this.listenTo( nfRadio.channel( 'layouts' ), 'out:cellSortable', this.out );
			this.listenTo( nfRadio.channel( 'layouts' ), 'sort:cellSortable', this.sort );
			this.listenTo( nfRadio.channel( 'layouts' ), 'start:cellSortable', this.start );
			this.listenTo( nfRadio.channel( 'layouts' ), 'stop:cellSortable', this.stop );
			this.listenTo( nfRadio.channel( 'layouts' ), 'update:cellSortable', this.update );
			this.listenTo( nfRadio.channel( 'layouts' ), 'receive:cellSortable', this.receive );
			
			/*
			 * Listen to triggers that we're dragging a new field type.
			 */
			this.listenTo( nfRadio.channel( 'drawer-addField' ), 'drag:type', this.dragFieldType );
		
			/*
			 * Listen to requests to set dropping state.
			 */ 
			this.listenTo( nfRadio.channel( 'layouts' ), 'set:dropping', this.setDropping );
		
			/*
			 * Listen to triggers that we're deleting a cell
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'click:deleteCell', this.deleteCell );
		},

		/**
		 * When we are over a cell sortable, we need to:
		 *
		 * 1) Change the width of the helper to match the sortable
		 * 2) If a gutter has a placholder class, remove it so that we don't have multiple placeholders
		 * 3) If we only have one cell and one field, remove any placeholders in this row.
		 * 4) Trigger the Ninja Forms default handler for being over a field sortable.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		over: function( e, ui, cellView, sortable ) {
			// Change the size of our dragged element.
			jQuery( ui.helper ).css( 'width', jQuery( sortable ).css( 'width' ) );
			// If we have a gutter with a placeholder class, remove it and add a temporary placeholder.
			jQuery( '#nf-main' ).find( '.gutter.nf-fields-sortable-placeholder' ).removeClass( 'nf-fields-sortable-placeholder' );
			// If we only have one cell and one field, remove any placeholders in this row.
			if ( cellView.collection.models.length == 1 && 1 == cellView.cellCollection.length ) {
				jQuery( sortable ).parent().find( '.nf-fields-sortable-placeholder' ).addClass( 'nf-placeholder-removed' ).removeClass( 'nf-fields-sortable-placeholder' );
				jQuery( sortable ).parent().find( '.nf-placeholder-removed' ).prev().css( 'margin-bottom', '0' );
			}
			// Trigger Ninja Forms default handler for being over a field sortable.
			nfRadio.channel( 'app' ).request( 'over:fieldsSortable', ui );
		},

		/**
		 * When we move out from a cell sortable, we need to:
		 *
		 * 1) Trigger the default Ninja Forms handler for mouse out of a sortable.
		 * 2) Add the placeholder class back to any we removed in the over method.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		out: function( e, ui, cellView, sortable ) {
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );
			if ( cellView.collection.models.length == 1 && 1 == cellView.cellCollection.length ) {
				jQuery( sortable ).parent().find( '.nf-placeholder-removed' ).prev().css( 'margin-bottom', '' );
				jQuery( sortable ).parent().find( '.nf-placeholder-removed' ).addClass( 'nf-fields-sortable-placeholder' ).removeClass( 'nf-placeholder-removed' );
			}
		},

		/**
		 * The contents of this method have been commented out because I'm not sure that we need them anymore.
		 * This was a fix for a bug that occurred when dragging the staging area. The placeholder would jump
		 * out of the target sortable and off-screen. This seems to be fixed in the current version, but I'm leaving
		 * this code incase we need to reference it again. It wasn't easy to find.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		sort: function( e, ui, cellView, sortable ) {
			// if ( 0 == ui.placeholder.position().left ) {
        		 // jQuery( ui.item ).before( ui.placeholder );
        	// }
		},

		/**
		 * When we start dragging the sortable items:
		 * 1) Set the fieldCollection property to the cellView collection
		 * 2) Add a dragging class to our layouts row
		 * 3) Add a dragging class to our builder
		 * 4) Trigger our Ninja Forms default start for fields sortable.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		start: function( e, ui, cellView, sortable ) {
			ui.item.fieldCollection = cellView.collection;
			jQuery( '.layouts-row' ).addClass( 'dragging' );
			jQuery( '#nf-builder' ).addClass( 'layouts-dragging' );
			nfRadio.channel( 'app' ).request( 'start:fieldsSortable', ui );
			if ( this.dropping ) {
				nfRadio.channel( 'layouts' ).trigger( 'set:dropping', false );
			}
		},

		/**
		 * When we stop dragging the sortable items:
		 * 1) Remove our dragging classes
		 * 2) Trigger our Ninja Forms default stop for fields sortable.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		stop: function( e, ui, cellView, sortable ) {
			jQuery( '.layouts-row' ).removeClass( 'dragging' );
			jQuery( '#nf-builder' ).removeClass( 'layouts-dragging' );
			nfRadio.channel( 'app' ).request( 'stop:fieldsSortable', ui );
		},

		/**
		 * When we update, check to make sure that we are dragging a sortable item and not a new field type.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		update: function( e, ui, cellView, sortable ) {
			/*
			 * Both the receive event above and the update event are fired when we drag items from one list to another.
			 * We only want to fire this event if we are dragging within the same list.
			 *
			 * Also, if we're dragging a saved field, make sure that receive is triggered.
			 */
			var fieldID = jQuery( ui.item ).data( 'id' );
			var type = nfRadio.channel( 'fields' ).request( 'get:type', fieldID );

			if ( 'undefined' !== typeof type && ! this.received ) {
				this.receive( e, ui, cellView, sortable );
				this.received = false;
				return false;
			}

			if ( sortable === ui.item.parent()[0] && 'undefined' == typeof type ) { // Make sure that we are dragging within the same list
				var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', fieldID );

				// Get our sortable order.
				var order = jQuery( sortable ).sortable( 'toArray' );
				var oldOrder = [];
				/*
				 * We have to update every model's order based upon our order array.
				 * Loop through all of our fields and update their order value
				 */
				_.each( cellView.collection.models, function( field ) {
					var id = field.get( 'id' );
					if ( jQuery.isNumeric( id ) ) {
						var search = 'field-' + id;
					} else {
						var search = id;
					}
					
					// Get the index of our field inside our order array
					var newPos = order.indexOf( search ) + 1;
					oldOrder[ field.get( 'cellOrder' ) ] = field.get( 'id' );
					field.set( 'cellOrder', newPos );
				} );
				// Sort our field collection.
				cellView.collection.sort();

				// Update our field order attribute
				nfRadio.channel( 'layouts' ).request( 'update:fieldOrder', cellView.options.cellCollection.options.rowModel.collection );

				// Set our 'clean' status to false so that we get a notice to publish changes
				nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
				// Update our preview
				nfRadio.channel( 'app' ).request( 'update:db' );

				// Add our field addition to our change log.
				var label = {
					object: 'Field',
					label: fieldModel.get( 'label' ),
					change: 'Re-ordered',
					dashicon: 'sort'
				};

				var data = {
					layouts: true,
					oldOrder: oldOrder,
					fieldCollection: cellView.collection
				};

				/*
				 * Disable the next Layouts change
				 */
				var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
				_.each( changeCollection.models, function( changeModel ) {
					var data = changeModel.get( 'data' );
					if ( 'undefined' != typeof data.layouts && data.layouts ) {
						changeModel.set( 'disabled', true );
					}
				}, this );

				var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'cellSorting', fieldModel, null, label, data );
			}
			this.received = false;
		},

		/**
		 * The 'receive' event fires whenever we drop a new field type, the staging area, or a field from another cell.
		 * We need to react to each of these events differently.
		 *
		 * If we drop a new field type:
		 * 1) Add the field to the Ninja Forms field collection.
		 * 2) Add the field to our collection.
		 * 3) Sort our fields.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		receive: function( e, ui, cellView, sortable ) {
			/*
			 * If we are dropping on a gutter, the this.dropping will be set to true.
			 * Once we know we've dropped, reset dropping to false.
			 * We only want to receive if we didn't drop on a gutter/divider.
			 */
			if ( this.dropping ) {
				nfRadio.channel( 'layouts' ).trigger( 'set:dropping', false );
				return false;
			}

			if ( jQuery( ui.item ).hasClass( 'nf-field-type-draggable' ) ) { // New Field Type Draggable
				this.receiveNewField( e, ui, cellView, sortable );
			} else if ( jQuery( ui.item ).hasClass( 'nf-field-wrap' ) ) { // An item from another cell sortable.
				this.receiveCurrentField( e, ui, cellView, sortable );
			} else { // Staging
				this.receiveFieldStaging( e, ui, cellView, sortable );						
			}
			// Update our field order attribute
			nfRadio.channel( 'layouts' ).request( 'update:fieldOrder', cellView.options.cellCollection.options.rowModel.collection );
			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );

			this.received = true;		
		},

		/**
		 * Fires when we drag a new field type into our cell sortable.
		 * 1) Adds a new field model
		 * 2) Sorts our field collection
		 * 3) Removes the helper
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		receiveNewField: function( e, ui, cellView, sortable ) {
			// Get our type string
			var type = jQuery( ui.item ).data( 'id' );
			/*
			 * Add a field.
			 * Passing the cid of our current cell model causes the field to be added to our cell.
			 */ 
			var newModel = this.addField( type, cellView.model.cid );
			/*
			 * Update our helper id to the tmpID.
			 * We do this so that when we sort, we have the proper ID.
			 */ 
			jQuery( ui.helper ).prop( 'id', newModel.get( 'id' ) );
			var order = jQuery( sortable ).sortable( 'toArray' );
			// Sort our field collection
			this.sortFields( order, cellView.model.get( 'fields' ) );
			// Trigger a drop field type event.
			nfRadio.channel( 'fields' ).trigger( 'drop:fieldType', type, newModel );
			// Remove the helper. Gets rid of a weird type artifact.
			jQuery( ui.helper ).remove();
			if ( null === ui.helper ) {
				jQuery( ui.item ).remove();
			}

			/**
			 * TODO: Add in support for undoing adding a new field.
			 */
			
			// // Add our field addition to our change log.
			// var label = {
			// 	object: 'Field',
			// 	label: newModel.get( 'label' ),
			// 	change: 'Field Added',
			// 	dashicon: 'plus-alt'
			// };

			// var data = {
			// 	layouts: true,
			// 	fieldCollection: cellView.collection
			// };
			
			// /*
			//  * Disable Layouts changes
			//  */
			// var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			// _.each( changeCollection.models, function( changeModel ) {
			// 	var data = changeModel.get( 'data' );
			// 	if ( 'undefined' != typeof data.layouts && data.layouts ) {
			// 		changeModel.set( 'disabled', true );
			// 	}
			// }, this );

			// var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'cellNewField', newModel, null, label, data );

		},

		/**
		 * Fires when we drag the staging area into our cell sortable.
		 * 1) Gets our staging field types
		 * 2) Adds a new field for each of those types
		 * 3) Sort our fields
		 * 4) Remove the helper 
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		receiveFieldStaging: function( e, ui, cellView, sortable ) {
			// Make sure that our staged fields are sorted properly.	
			nfRadio.channel( 'fields' ).request( 'sort:staging' );
			// Grab our staged fields.
			var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );

			// If we're dealing with a sortable that isn't empty, get the order.
			var order = jQuery( sortable ).sortable( 'toArray' );
			// Get the index of our droped element.
			var insertedAt = order.indexOf( 'nf-staged-fields-drag' );
			// Remove our dropped element from our order array.
			order.splice( insertedAt, 1 );

			// Loop through each staged fields model and insert a field.
			var tmpIDs = [];
			_.each( stagedFields.models, function( field, index ) {
				/*
				 * Add a field.
				 * Passing the cid of our current cell model causes the field to be added to our cell.
				 */
				var newModel = this.addField( field.get( 'slug' ), cellView.model.cid );
				// Add this newly created field to our order array.
				order.splice( insertedAt + index, 0, newModel.get( 'id' ) );
			}, this );

			this.sortFields( order, cellView.model.get( 'fields' ) );
			// Clear our staging
			nfRadio.channel( 'fields' ).request( 'clear:staging' );
			// Remove our helper. Fixes a weird artifact.
			jQuery( ui.helper ).remove();

			/**
			 * TODO: Add in support for undoing adding staged fields.
			 */
			
		},

		/**
		 * Fires when we drag a field from another cell into our cell sortable.
		 * 1) Adds the field model to our cell collection
		 * 2) Sort our fields
		 * 3) Remove the field from the original cell
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		receiveCurrentField: function( e, ui, cellView, sortable ) {
			/*
			 * When we receive an item add it to our collection
			 */
			var fieldID = jQuery( ui.item ).data( 'id' );
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', fieldID );

			/*
			 * Get the old order of our sending cell
			 */
			var senderOldOrder = [];
			_.each( ui.item.fieldCollection.models, function( field, index ) {
				senderOldOrder[ field.get( 'cellOrder' ) ] = field.get( 'id' );
				// senderOldOrder.push( fieldModel.get( 'cellOrder' ) );
			} );

			/*
			 * Get the old order of our receiving cell
			 */
			var receiverOldOrder = [];
			_.each( cellView.collection.models, function( field, index ) {
				receiverOldOrder[ field.get( 'cellOrder' ) ] = field.get( 'id' );
				// receiverOldOrder.push( fieldModel.get( 'cellOrder' ) );
			} );

			cellView.collection.add( fieldModel, { silent: true } );

			var order = jQuery( sortable ).sortable( 'toArray' );
			this.sortFields( order, cellView.collection );

			ui.item.fieldCollection.remove( fieldModel );

			// Add our field addition to our change log.
			var label = {
				object: 'Field',
				label: fieldModel.get( 'label' ),
				change: 'Moved Between Cells',
				dashicon: 'randomize'
			};

			var data = {
				layouts: true,
				originalCollection: ui.item.fieldCollection,
				newCollection: cellView.collection,
				senderOldOrder: senderOldOrder,
				receiverOldOrder: receiverOldOrder
			};
			
			/*
			 * Disable Layouts changes
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			_.each( changeCollection.models, function( changeModel ) {
				var data = changeModel.get( 'data' );
				if ( 'undefined' != typeof data.layouts && data.layouts ) {
					changeModel.set( 'disabled', true );
				}
			}, this );

			var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'movedBetweenCells', fieldModel, null, label, data );
		},

		/**
		 * Sort a fields collection by order.
		 * 
		 * @since  3.0
		 * @param  object order        		array
		 * @param  object collection       	Backbone collection
		 * @return void
		 */
		sortFields: function( order, collection ) {
			/*
			 * Loop through our collection and update our order.
			 */
			_.each( collection.models, function( field ) {
				// Get our current position.
				var oldPos = field.get( 'cellOrder' );
				var id = field.get( 'id' );
				if ( jQuery.isNumeric( id ) ) {
					var search = 'field-' + id;
				} else {
					var search = id;
				}
				
				// Get the index of our field inside our order array
				var newPos = order.indexOf( search ) + 1;
				field.set( 'cellOrder', newPos );
			} );
			// Sort our field collection.
			collection.sort();
		},

		/**
		 * Returns the sortable drag helper.
		 * Places the cursor at the top/left of the draggable.
		 * 
		 * @since  3.0
		 * @param  object e        event
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		getHelper: function( e, cellView, sortable ) {
			if ( jQuery( e.target ).hasClass( 'nf-field-label' ) || jQuery( e.target ).hasClass( 'fa' ) ) {
				var el = jQuery( e.target ).parent();
			} else if ( jQuery( e.target ).hasClass( 'required' ) ) {
				var el = jQuery( e.target ).parent().parent();
			} else {
				var el = e.target;
			}
			var width = jQuery( el ).width();
			var height = jQuery( el ).height();
			var element = jQuery( el ).clone();
			var left = width / 4;
			var top = height / 2;
			jQuery( sortable ).sortable( 'option', 'cursorAt', { top: top, left: left } );
			return element;
		},

		/**
		 * Add a field.
		 * Builds the object necessary to add a field to the field model collection.
		 * 
		 * @since  3.0
		 * @param  string 	type   field type
		 * @param  boolean 	silent add silently
		 * @return model 	newModel
		 */
		addField: function( type, cellcid, silent ) {
			// Default to false
			silent = silent || false;
			// Get our field type model
			var fieldType = nfRadio.channel( 'fields' ).request( 'get:type', type ); 
			// Get our tmp ID
			var tmpID = nfRadio.channel( 'fields' ).request( 'get:tmpID' );
			// Add our field
			var newModel = nfRadio.channel( 'fields' ).request( 'add',  { id: tmpID, label: fieldType.get( 'nicename' ), type: type, cellcid: cellcid }, silent, false );
			// Add our field addition to our change log.
			var label = {
				object: 'Field',
				label: newModel.get( 'label' ),
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				layouts: true,
				collection: nfRadio.channel( 'fields' ).request( 'get:collection' )
			}

			/*
			 * Disable Layouts changes
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			_.each( changeCollection.models, function( changeModel ) {
				var data = changeModel.get( 'data' );
				if ( 'undefined' != typeof data.layouts && data.layouts ) {
					changeModel.set( 'disabled', true );
				}
			}, this );

			nfRadio.channel( 'changes' ).request( 'register:change', 'cellNewField', newModel, null, label, data );

			return newModel;
		},

		/**
		 * When we drag our new field type, make sure that its width stays consistent.
		 * 
		 * @since  3.0
		 * @param  object draggable jQuery UI Object
		 * @param  object ui        jQuery UI Object
		 * @param  object e         event object
		 * @return void
		 */
		dragFieldType: function( draggable, ui, e ) {
			if ( ui.helper.hasClass( 'nf-field-type-button' ) ) {
				var draggableInstance = jQuery( draggable ).draggable( 'instance' );
				jQuery( ui.helper ).css( 'width', draggableInstance.helperProportions.width );
			}
		},

		/**
		 * Listens for the 'set dropping' trigger and sets the value on this view accordingly.
		 * 
		 * @since 3.0
		 * @param void
		 */
		setDropping: function( val ) {
			this.dropping = val;
		},

		/**
		 * When we click "delete this cell" in our cell view, remove that cell from its collection
		 * 
		 * @since  3.0
		 * @param  object 			e        	event
		 * @param  Backbone.view 	cellView 	Backbone view representing our single cell view
		 * @return void
		 */
		deleteCell: function( e, cellView ) {
			var cellModel = cellView.model;
			var cellCollection = cellView.model.collection;
			var rowModel = cellView.model.collection.options.rowModel;
			var rowCollection = rowModel.collection;

			/*
			 * Remove our cell model from the collection.
			 */
			cellCollection.remove( cellModel );

			/*
			 * Setup the values to add this to our undo manager
			 */
			var undoData = {
				layouts: true,
				rowCollection: rowCollection,
				cellCollection: cellCollection,
				cellModel: cellModel,
				rowModel: rowModel
			};

			/*
			 * If we have more than one cell, recalculate our widths
			 */
			if ( 1 == cellCollection.models.length ) { // We have one cell.
				/*
				 * If we have one cell, we want to break any fields inside that cell up into their own rows.
				 */
				
				// Get the order of our row
				var order = rowModel.get( 'order' );
				// Store the new rows that we are going to create
				var newRows = [];
				// Create a new row for each field in this cell.
				_.each( cellCollection.models[0].get( 'fields' ).models, function( fieldModel ) {
					var newRowModel = nfRadio.channel( 'layouts' ).request( 'add:row', cellCollection.options.rowModel.collection, { order: order, field: fieldModel.get( 'id' ) } );
					newRows.push( newRowModel );
				}, this );

				/*
				 * After we insert our new rows, we remove our old row.
				 */
				rowCollection.remove( rowModel );

				/*
				 * Add our new rows to the undo data object
				 */
				undoData.newRows = newRows;
			}

			// Add our action deletion to our change log.
			var label = {
				object: 'Cell',
				label: '',
				change: 'Removed',
				dashicon: 'dismiss'
			};

			/*
			 * Disable Layouts changes
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			_.each( changeCollection.models, function( changeModel ) {
				var data = changeModel.get( 'data' );
				if ( 'undefined' != typeof data.layouts && data.layouts ) {
					changeModel.set( 'disabled', true );
				}
			}, this );
			
			nfRadio.channel( 'changes' ).request( 'register:change', 'removedCell', cellModel, null, label, undoData );
			
			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );


		}
	});

	return controller;
} );
