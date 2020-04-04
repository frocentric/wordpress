/**
 * Controller that handles events from the gutter/divider droppable
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		dropped: false,

		initialize: function() {
			/*
			 * Listen for events triggered by our gutter droppables
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'over:gutterDroppable', this.over );
			this.listenTo( nfRadio.channel( 'layouts' ), 'out:gutterDroppable', this.out );
			this.listenTo( nfRadio.channel( 'layouts' ), 'drop:gutterDroppable', this.drop );

			/*
			 * Listen to the drag events of our gutter slider.
			 */
			this.listenTo( nfRadio.channel( 'layouts' ), 'dragStart:gutterSlider', this.dragStart );
			this.listenTo( nfRadio.channel( 'layouts' ), 'drag:gutterSlider', this.drag );
			this.listenTo( nfRadio.channel( 'layouts' ), 'dragEnd:gutterSlider', this.dragEnd );

		},

		/**
		 * When we're over a gutter droppable:
		 * 1) Set our dropped property to false. (Helps prevent duplicate drop events later)
		 * 2) Remove any placeholders on other elements.
		 *
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		over: function( e, ui, rowView, droppable ) {
			this.dropped = false;
			if ( jQuery( ui.helper ).hasClass( 'nf-field-type-draggable' ) ) {
				jQuery( ui.helper ).css( 'width', 300 );
				jQuery( '#nf-main' ).find( '.nf-fields-sortable-placeholder:not(.gutter)' ).addClass( 'nf-sortable-removed' ).removeClass( 'nf-fields-sortable-placeholder' );
			} else {
				jQuery( droppable ).addClass( 'nf-fields-sortable-placeholder' );
				jQuery( '#nf-main' ).find( '.nf-fields-sortable-placeholder:not(.gutter)' ).addClass( 'nf-sortable-removed' ).removeClass( 'nf-fields-sortable-placeholder' );
			}
		},

		/**
		 * When we're out of a gutter droppable:
		 * 1) Reset any placeholders we removed in the over event.
		 *
		 * @since  3.0
		 * @param  object e        event
		 * @param  object ui       ui object
		 * @param  object cellView Backbone view
		 * @param  object sortable jQuery UI element
		 * @return void
		 */
		out: function( e, ui, rowView, droppable ) {
			if ( jQuery( ui.helper ).hasClass( 'nf-field-type-draggable' ) ) {
				jQuery( '#nf-main' ).find( '.nf-sortable-removed' ).addClass( 'nf-fields-sortable-placeholder' );
			} else {
				jQuery( droppable ).removeClass( 'nf-fields-sortable-placeholder' );
				jQuery( '#nf-main' ).find( '.nf-sortable-removed' ).addClass( 'nf-fields-sortable-placeholder' );
			}
		},

		/**
		 * There are three different items that we could be dropping:
		 * 1) A new single field type
		 * 2) The field staging draggable
		 * 3) A field that already exists within another cell.
		 *
		 * Regardless of which we dropped, we create a new cell and eventually re-render the row.
		 *
		 * @since  3.0
		 * @param  object e        	event
		 * @param  object ui       	ui object
		 * @param  object rowView 	Backbone view
		 * @param  object sortable 	jQuery UI element
		 * @return void
		 */
		drop: function( e, ui, rowView, droppable ) {
			/*
			 * Because this droppable is nested inside a sortable, the drop event can be fired when the user drags over it.
			 * The solution is to add a check to see when the user has their mousedown.
			 *
			 * To further make sure that this only fires once, we set "dropped" to false in the over event.
			 * if "dropped" is set to true, we return false.
			 */
			if ( jQuery( '#nf-builder' ).data( 'mousedown' ) || this.dropped ) {
				return false;
			}
			// Prevent this drop event from firing twice from the same drop.
			this.dropped = true;

			// Prevent any sortable lists from accepting this item.
			nfRadio.channel( 'layouts' ).trigger( 'set:dropping', true );

			// Get the order of our gutter.
			var order = jQuery( droppable ).index() / 2;
			// Create a new cell in our row collection.
			var newCell = this.addCell( order, rowView.collection );

			if ( jQuery( ui.helper ).hasClass( 'nf-field-type-draggable' ) ) { // Single Add New Field Type
				this.dropNewField( e, ui, rowView, droppable, newCell );
			} else if ( jQuery( ui.helper ).hasClass( 'nf-stage' ) ) { // Field Staging
				this.dropFieldStaging( e, ui, rowView, droppable, newCell );
			} else { // The field dropped already exists in another cell collection.
				this.dropCurrentField( e, ui, rowView, droppable, newCell );
			}

			// Update our field order attribute
			nfRadio.channel( 'layouts' ).request( 'update:fieldOrder', rowView.model.collection );
			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );
			// Re-apply our sortable class to any that we removed.
			jQuery( '#nf-main' ).find( '.nf-sortable-removed' ).addClass( 'nf-fields-sortable-placeholder' );
			// Re-render our row.
			rowView.render();
		},

		/**
		 * Add a field to our new cell.
		 *
		 * @since  3.0
		 * @param  object 			e         event
		 * @param  object 			ui        jQuery UI element
		 * @param  Backbone.view 	rowView   Single Row View
		 * @param  object 			droppable jQuery UI element
		 * @param  Backbone.model 	newCell   cell model
		 * @return void
		 */
		dropNewField: function( e, ui, rowView, droppable, newCell ) {
			// Get our type string
			var type = jQuery( ui.draggable ).data( 'id' );
			// Add a field (returns the tmp ID )
			var newModel = this.addField( type, newCell, false );
			// Remove our dragged element.
			jQuery( ui.helper ).remove();
		},

		/**
		 * Add a field to our new cell for each item in our staging area.
		 *
		 * @since  3.0
		 * @param  object 			e         event
		 * @param  object 			ui        jQuery UI element
		 * @param  Backbone.view 	rowView   Single Row View
		 * @param  object 			droppable jQuery UI element
		 * @param  Backbone.model 	newCell   cell model
		 * @return void
		 */
		dropFieldStaging: function( e, ui, rowView, droppable, newCell ) {
			// Make sure that our staged fields are sorted properly.
			nfRadio.channel( 'fields' ).request( 'sort:staging' );

			// Grab our staged fields.
			var stagedFields = nfRadio.channel( 'fields' ).request( 'get:staging' );

			// Loop through each staged fields model and insert a field.
			var tmpIDs = [];
			_.each( stagedFields.models, function( field, index ) {
				// Add our field.
				var newModel = this.addField( field.get( 'slug' ), newCell );
			}, this );

			// Clear our staging
			nfRadio.channel( 'fields' ).request( 'clear:staging' );
			// Remove our helper. Fixes a weird artifact.
			jQuery( ui.helper ).remove();
		},

		/**
		 * Copy a field to our new cell that already exists on the form.
		 *
		 * @since  3.0
		 * @param  object 			e         event
		 * @param  object 			ui        jQuery UI element
		 * @param  Backbone.view 	rowView   Single Row View
		 * @param  object 			droppable jQuery UI element
		 * @param  Backbone.model 	newCell   cell model
		 * @return void
		 */
		dropCurrentField: function( e, ui, rowView, droppable, newCell ) {
			// Get our field id.
			var fieldID = jQuery( ui.draggable ).data( 'id' );
			// Get our field model from the ID
			var fieldModel = nfRadio.channel( 'fields' ).request( 'get:field', fieldID );
			var oldCollection = ui.draggable.fieldCollection;
			// Add our field to our new cell model
			newCell.get( 'fields' ).add( fieldModel );
			// Remove our field from its old cell model.
			ui.draggable.fieldCollection.remove( fieldModel );
			// Remove the element that was originally dragged. Keep the helper.
			jQuery( ui.draggable ).remove();
		},

		dragStart: function( data, cellCollection ) {
			var widths = this.getDraggedWidths( data, cellCollection );
			var percentLeft = widths.a;
			var percentRight = widths.b;

			jQuery( data.gutter ).append( '<span class="percent-left">' + percentLeft + '%</span><span class="percent-right">' + percentRight + '%</span>' );
		},

		drag: function( data, cellCollection ) {
			var widths = this.getDraggedWidths( data, cellCollection );
			var percentLeft = widths.a;
			var percentRight = widths.b;

			jQuery( data.gutter ).find( '.percent-left' ).html( percentLeft + '%' );
			jQuery( data.gutter ).find( '.percent-right' ).html( percentRight + '%' );
		},

		/**
		 * When we resize our cell, update the data model.
		 *
		 * @since  3.0
		 * @param  object 	data Split.js data object
		 * @return void
		 */
		dragEnd: function( data, cellCollection ) {
			var widths = this.getDraggedWidths( data, cellCollection );
			var awidth = widths.a;
			var bwidth = widths.b;

			// Get our data models.
			var modelA = jQuery( data.a ).data( 'model' );
			var modelB = jQuery( data.b ).data( 'model' );

			var oldModelAWidth = modelA.get( 'width' );
			var oldModelBWidth = modelB.get( 'width' );

			jQuery( data.gutter ).find( '.percent-left' ).remove();
			jQuery( data.gutter ).find( '.percent-right' ).remove();

			if ( oldModelAWidth == awidth && oldModelBWidth == bwidth ) {
				return false;
			}

			// Update our width
			modelA.set( 'width', awidth );
			modelB.set( 'width', bwidth );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			// Update our preview
			nfRadio.channel( 'app' ).request( 'update:db' );

		},

		/**
		 * Add a field.
		 * Builds the object necessary to add a field to the field model collection.
		 *
		 * @since  3.0
		 * @param  string 				type   		field type
		 * @param  Backbone.object   	newCell 	the cell this field lives in
		 * @param  boolean 				silent 		add silently
		 * @return model 				newModel
		 */
		addField: function( type, newCell, silent ) {
			// Default to false
			silent = silent || false;
			renderField = false;
			// Get our field type model
			var fieldType = nfRadio.channel( 'fields' ).request( 'get:type', type );
			// Get our tmp ID
			var tmpID = nfRadio.channel( 'fields' ).request( 'get:tmpID' );
			// Add our field
			var newModel = nfRadio.channel( 'fields' ).request( 'add',  { id: tmpID, label: fieldType.get( 'nicename' ), type: type, cellcid: newCell.cid }, silent, renderField );
			
			return newModel;
		},

		/**
		 * Add a cell to the passed collection.
		 *
		 * @since  3.0
		 * @param  int 					order      	Order for our new cell in the row
		 * @param  Backbone.collection 	collection 	Cell collection
		 * @param  array 				fields     	Optional array of fields to add to the cell.
		 * @return Backbone.model 		newCell 	New cell model
		 */
		addCell: function( order, collection, fields ) {
			var fields = fields || [];
			// Update any cells that have an order equal to or higher than our order.
			_.each( collection.models, function( cell ) {
				if ( cell.get( 'order' ) >= order ) {
					cell.set( 'order', cell.get( 'order' ) + 1 );
				}
			} );

			// Add a new cell to our cell collection.
			var newCell = collection.add( {
				order: order,
				fields: fields,
				width: ''
			} );

			// collection.sort();

			return newCell;
		},

		getDraggedWidths: function( data, cellCollection ) {
			// Get the widths of the cell to the left and right of the dragged gutter.
			var awidth = jQuery( data.a ).data( 'width' );
			var bwidth = jQuery( data.b ).data( 'width' );

			return {
				a: awidth,
				b: bwidth,
			}
		}
	});

	return controller;
} );
