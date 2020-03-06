/**
 * Controller that handles row collection sortables.
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		dropping: false,

		initialize: function() {
			/*
			 * Listen for events from our row collection sortable.
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'over:rowsSortable', this.over );
			this.listenTo( nfRadio.channel( 'layouts' ), 'out:rowsSortable', this.out );
			this.listenTo( nfRadio.channel( 'layouts' ), 'start:rowsSortable', this.start );
			this.listenTo( nfRadio.channel( 'layouts' ), 'stop:rowsSortable', this.stop );
			this.listenTo( nfRadio.channel( 'layouts' ), 'update:rowsSortable', this.update );
			this.listenTo( nfRadio.channel( 'layouts' ), 'receive:rowsSortable', this.receive );
			
			/*
			 * Listen to our set dropping trigger
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'set:dropping', this.setDropping );
		},

		/**
		 * When we start dragging, set our dropping value to false and fire the default Ninja Forms event.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		start: function( e, ui, rowsView, sortable ) {
			if ( this.dropping ) {
				nfRadio.channel( 'layouts' ).trigger( 'set:dropping', false );
			}
			nfRadio.channel( 'app' ).request( 'start:fieldsSortable', ui );
		},

		/**
		 * When we drag over our sortable, set our helper width and fire the default Ninja Forms event.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		over: function( e, ui, rowsView, sortable ) {
			jQuery( ui.helper ).css( 'width', jQuery( sortable ).css( 'width' ) );
			nfRadio.channel( 'app' ).request( 'over:fieldsSortable', ui );
		},

		/**
		 * When we drag out of our sortable, fire the default Ninja Forms event.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		out: function( e, ui, rowsView, sortable ) {
			nfRadio.channel( 'app' ).request( 'out:fieldsSortable', ui );
		},

		/**
		 * When we stop dragging our sortable, fire the default Ninja Forms event.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		stop: function( e, ui, rowsView, sortable ) {
			nfRadio.channel( 'app' ).request( 'stop:fieldsSortable', ui );
		},

		/**
		 * When we update the order of our sortable update the order of the models in our collection.
		 * We only want to update if the user didn't drop on a gutter/divider.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		update: function( e, ui, rowsView, sortable ) {
			// Make sure that we're dropping a field and that we aren't dropping on a gutter/divider
			if( ! jQuery( ui.item ).hasClass( 'nf-stage' ) && ! jQuery( ui.item ).hasClass( 'nf-field-wrap' ) && ! this.dropping ) {
				var order = jQuery( sortable ).sortable( 'toArray' );
				var oldOrder = [];
				// var rowCollection = nfRadio.channel( 'layouts-row' ).request( 'get:collection' );
				_.each( order, function( cid, index ) {
					oldOrder[ rowsView.collection.get( { cid: cid } ).get( 'order' ) ] = cid;
					rowsView.collection.get( { cid: cid } ).set( 'order', index + 1 );
				} );

				rowsView.collection.sort();

				// Update our field order attribute
				nfRadio.channel( 'layouts' ).request( 'update:fieldOrder', rowsView.collection );

				var rowcid = jQuery( ui.item ).data( 'id' );
				var droppedRow = rowsView.collection.get( { cid: rowcid } );

				// Add our field addition to our change log.
				var label = {
					object: 'Row',
					label: '',
					change: 'Re-ordered',
					dashicon: 'sort'
				};

				var data = {
					layouts: true,
					oldOrder: oldOrder,
					rowCollection: rowsView.collection
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

				var newChange = nfRadio.channel( 'changes' ).request( 'register:change', 'rowSorting', droppedRow, null, label, data );

				// Set our 'clean' status to false so that we get a notice to publish changes
				nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
				// Update our preview
				nfRadio.channel( 'app' ).request( 'update:db' );
			}
		},

		/**
		 * When we receive an element, we are receiving:
		 * 1) A field from cell sortable
		 * 2) A single new field type
		 * 3) Or the fields staging.
		 * 
		 * @since  3.0
		 * @param  object 			e 		 	event object
		 * @param  object 			ui       	jQuery UI element
		 * @param  Backbone.view 	rowsView 	Backbone view
		 * @param  object 			sortable 	jQuery UI element
		 * @return void
		 */
		receive: function( e, ui, rowsView, sortable ) {
			/*
			 * If we are dropping on a gutter, the this.dropping will be set to true.
			 * Once we know we've dropped, reset dropping to false.
			 * We only want to receive if we didn't drop on a gutter/divider.
			 */
			if ( this.dropping ) {
				nfRadio.channel( 'layouts' ).trigger( 'set:dropping', false );
				return false;
			}

			if( jQuery( ui.item ).hasClass( 'nf-field-wrap' ) ) { // Receiving an item from a cell sortable
				this.receiveCurrentField( e, ui, rowsView, sortable );
			} else if ( jQuery( ui.item ).hasClass( 'nf-field-type-draggable' ) ) { // We've received a field type button
				this.receiveNewField( e, ui, rowsView, sortable );
			} else if ( jQuery( ui.item ).hasClass( 'nf-stage' ) ) { // Staging
				this.receiveFieldStaging( e, ui, rowsView, sortable );
			}
			// Update our field order attribute
			nfRadio.channel( 'layouts' ).request( 'update:fieldOrder', rowsView.collection );
			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		receiveNewField: function( e, ui, rowsView, sortable ) {
			// Get the location where we've dropped the item.	
			var order = ui.helper.index() + 1;
			// Get our type string
			var type = jQuery( ui.item ).data( 'id' );
			// Add a new field for our type, returning its tmp id.
			var fieldModel = this.addField( type, order, true );

			this.addRow( order, rowsView.collection, [ fieldModel.get( 'key' ) ], true );
			// Remove our helper
			jQuery( ui.helper ).remove();

			// Add our field addition to our change log.
			var label = {
				object: 'Field',
				label: fieldModel.get( 'label' ),
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

			var changeModel = nfRadio.channel( 'changes' ).request( 'register:change', 'rowNewField', fieldModel, null, label, data );
		},

		receiveFieldStaging: function( e, ui, rowsView, sortable ) {
			// Get the location where we've dropped the item.	
			var order = ui.helper.index() + 1;
			// Make sure that our staged fields are sorted properly.	
			nfRadio.channel( 'fields' ).request( 'sort:staging' );
			// Grab our staged fields.
			var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );
			
			// Loop through each staged fields model and insert a field.
			_.each( stagedFields.models, function( field, index ) {
				// Add a new field for our type, returning its tmp id.
				var fieldModel = this.addField( field.get( 'slug' ), order, true );
				this.addRow( order, rowsView.collection, [ fieldModel.get( 'id' ) ] );
				order++;
			}, this );

			// Clear our staging
			nfRadio.channel( 'fields' ).request( 'clear:staging' );
			// Remove our helper. Fixes a weird artifact.
			jQuery( ui.helper ).remove();
		},

		receiveCurrentField: function( e, ui, rowsView, sortable ) {
			var oldCID = ui.item.fieldCollection.options.cellModel.collection.options.rowModel.cid;
			var prevOrder = ui.item.fieldCollection.options.cellModel.collection.options.rowModel.get( 'order' );		
			var droppedOrder = ( prevOrder < jQuery( ui.item ).index() ) ? jQuery( ui.item ).index() : jQuery( ui.item ).index() + 1;
			var fieldID = jQuery( ui.item ).data( 'id' );

			var oldOrder = [];
			// Update any rows that have an order equal to or higher than our order.
			_.each( rowsView.collection.models, function( rowModel ) {
				oldOrder[ rowModel.get( 'order') ] = rowModel.cid;
			} );

			/*
			 * Remove the field from its collection.
			 * This will bubble up, causing the row to remove itself as well.
			 */
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', fieldID );
			ui.item.fieldCollection.remove( fieldModel );

			rowsView.collection.each( function( model, index ) {
				model.set( 'order', index + 1 );
			} );

			var rowModel = this.addRow( droppedOrder, rowsView.collection, [ fieldID ] );

			oldOrder[ oldOrder.indexOf( oldCID ) ] = rowModel.cid;

			/*
			 * Register an undo action for moving a current field into the row sortable.
			 */
			var undoLabel = {
				object: 'Field',
				undoLabel: fieldModel.get( 'label' )
			};

			var undoData = {
				layouts: true
			}			

			/*
			 * If we have more than one cell model in our collection, then we've dragged from a cell into a row.
			 *
			 * If we have just one cell model in our collection, then we've dragged a row.
			 * In this case, we are technically sorting, not just adding a new row.
			 */
			if ( 2 <= ui.item.fieldCollection.options.cellModel.collection.length ) {
				var changeAction = 'movedToNewRow';
				var actionModel = fieldModel;
				undoData.originalCollection = ui.item.fieldCollection;
				undoData.rowModel = rowModel;
				undoLabel.dashicon = 'randomize';
				undoLabel.change = 'Moved';
			} else {
				var changeAction = 'rowSorting';
				var actionModel = rowModel;
				undoData.oldOrder = oldOrder;
				undoData.rowCollection = rowsView.collection;
				undoLabel.dashicon = 'sort';
				undoLabel.change = 'Re-ordered';
			}

			/*
			 * Disable Layouts changes
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			_.each( changeCollection.models, function( changeModel ) {
				var data = changeModel.get( 'data' );
				if ( 'undefined' != typeof data.layouts && data.layouts ) {
					changeModel.set( 'disabled', true );

					if ( 'undefined' != typeof data.oldOrder ) {
						data.oldOrder[ data.oldOrder.indexOf( oldCID ) ] = rowModel.cid;
						changeModel.set( 'data', data );
					}
				}
			}, this );

			var changeModel = nfRadio.channel( 'changes' ).request( 'register:change', changeAction, actionModel, null, undoLabel, undoData );
		},

		/**
		 * Add a row to the passed row collection.
		 * 
		 * @since 3.0
		 * @param int 					order      Index of the dropped item
		 * @param Backbone.collection 	collection Row collection
		 * @param array 				fields  IDs of the fields we're adding (optional)
		 * @param bool 					silent     [description]
		 */
		addRow: function( order, collection, fields, silent ) {
			var fields = fields || [];
			var silent = silent || false;

			// Update any rows that have an order equal to or higher than our order.
			_.each( collection.models, function( rowModel ) {
				if ( parseInt( rowModel.get( 'order' ) ) >= order ) {
					var newOrder = rowModel.get( 'order' ) + 1;
					rowModel.set( 'order', newOrder );
				}
			} );
			
			// Add a row model into our collection.
			var newRow = collection.add( {
				order: order,
				cells: [
					{
						order: 0,
						fields: fields,
						width: '100'
					}
				]
			}, { silent: silent } );

			
			collection.sort( { silent: true } );

			/* 
			 * When we add a row to our collection, the order attributes might get askew: 1,3,7 etc.
			 * Update our order so that all of our numbers are consecutive.
			 */
			_.each( collection.models, function( rowModel, index ) {
				rowModel.set( 'order', index + 1 );
			} );

			collection.sort();
		
			return newRow;

		},

		/**
		 * Add a field.
		 * Builds the object necessary to add a field to the field model collection.
		 * 
		 * @since  3.0
		 * @param  string 	type   field type
		 * @param  boolean 	silent add silently
		 * @return string 	tmpID
		 */
		addField: function( type, order, silent ) {
			// Default to false
			silent = silent || false;
			// Get our field type model
			var fieldType = nfRadio.channel( 'fields' ).request( 'get:type', type ); 
			// Add our field
			var newModel = nfRadio.channel( 'fields' ).request( 'add',  { label: fieldType.get( 'nicename' ), type: type }, silent, false );
			return newModel;
		},

		setDropping: function( val ) {
			this.dropping = val;
		}

	});

	return controller;
} );
