/**
 * Single row view
 */
define( ['views/cellItem', 'models/cellFieldCollection'], function( CellItemView, CellFieldCollection ) {
	var view = Marionette.CollectionView.extend( {
		tagname: 'div',
		className: 'layouts-row',
		childView: CellItemView,
		reorderOnSort: true,

		initialize: function() {
			// Set our collection to our cells.
			this.collection = this.model.get( 'cells' );
			/*
			 * Set our childViewOptions.
			 * As the variable name suggests, this will be accessible within our child views.
			 */
			this.childViewOptions = {
				cellCollection: this.collection
			};
			// Respond to requests to update our gutter/divider positions
			nfRadio.channel( 'layouts' ).reply( 'update:gutters', this.updateGutters, this );
			
			/*
			 * Bind listeners to row model events.
			 */
			// this.model.on( 'destroy:cell', this.render, this );
			this.collection.on( 'sort', this.render, this );

			/*
			 * Bind listeners to our nf-builder so that we can track when the user is dragging rather than just mouse over.
			 * Because our gutter/divider is a droppable that is very close to a sortable, sometimes the "drop" event will fire when we are just mousing over.
			 * Tracking that state of the mouse lets us prevent this later.
			 * 
			 */
			jQuery( '#nf-builder' ).on( 'mousedown', function() {
				jQuery( this ).data( 'mousedown', true );
			} );
			
			jQuery( '#nf-builder' ).on( 'mouseup', function() {
				jQuery( this ).data( 'mousedown', false );
			} );
		},

		/**
		 * Before we destroy this view, unbind our model change listeners.
		 * If we don't do this, we'll get JS errors.
		 * 
		 * @since  3.0
		 * @return void
		 */
		onBeforeDestroy: function() {
			// this.model.off( 'add:cell', this.render );
			// this.model.off( 'destroy:cell', this.render );
			this.collection.off( 'sort', this.maybeRender );
		},

		maybeRender: function() {
			if ( 1 < this.collection.models.length ) {
				this.render();
			}
		},

		/**
		 * When we render:
		 * 1) Set our el id to the model cid
		 * 2) Add a class based upon the number of cells in the row
		 * 3) Remove any old gutters
		 * 4) Update our gutters/dividers
		 * 5) Init our gutters/dividers as droppables
		 * 
		 * @since  version
		 * @return {[type]} [description]
		 */
		onRender: function() {
			// Set el ID
			jQuery( this.el ).prop( 'id', this.model.cid );
			// Add class based upon number of cells
			if ( this.collection.models.length == 1 ) {
				jQuery( this.el ).addClass( 'single-cell' );
				jQuery( this.el ).removeClass( 'multi-cell' );
			} else {
				jQuery( this.el ).addClass( 'multi-cell' );
				jQuery( this.el ).removeClass( 'single-cell' );
			}
			
			// Remove any gutters. This prevents extra HTML markup from appearing.
			jQuery( this.el ).find( '.gutter' ).remove();
			// Update our gutters/dividers
			this.updateGutters();
			// We want to access our rowView object later
			var rowView = this;
			// Init our droppables.
			jQuery( this.el ).find( '.gutter' ).droppable( {
				// Activate by pointer
				tolerance: 'pointer',
				// Class added when we're dragging over
				hoverClass: 'nf-fields-sortable-placeholder',
				// Which elements do we want to accept?
				accept: '.nf-field-type-draggable, .nf-field-wrap, .nf-stage',

				/**
				 * When we drag over this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				over: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'over:gutterDroppable', e, ui, rowView, this );
				},

				/**
				 * When we drag out of this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				out: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'out:gutterDroppable', e, ui, rowView, this );
				},

				/**
				 * When we drop on this droppable, trigger a radio event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				drop: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'drop:gutterDroppable', e, ui, rowView, this );
				}
			} );
		},

		/**
		 * Check for gutters in our row and activate them as sliders.
		 * 
		 * @since  3.0
		 * @return void
		 */
		updateGutters: function() {
			// Get our gutter elements
			var elements = jQuery( this.el ).find( '.layouts-cell' );
			var that = this;
			// Call split.js to create resizable cells.
			Split( elements, {
				minSize: 50,
				cellCollection: that.collection,
				// When we start resizing our cell, trigger a radio event.
				onDragStart: function( data ) {
					nfRadio.channel( 'layouts' ).trigger( 'dragStart:gutterSlider', data, that.collection );
				},
				// When we drag/resize our cell, trigger a radio event.
				onDrag: function( data ) {
					nfRadio.channel( 'layouts' ).trigger( 'drag:gutterSlider', data, that.collection );
				},
				// When we stop resizing our cell, trigger a radio event.
				onDragEnd: function( data ) {
					nfRadio.channel( 'layouts' ).trigger( 'dragEnd:gutterSlider', data, that.collection );
				}
			} );

			// Set the css width on our gutters
			_.each( jQuery( elements ), function( cell, index ) {
				var width = jQuery( cell ).data( 'width' );
				var gutterWidth = 10;
				if ( 0 == index || index == jQuery( elements ).length - 1 ) {
					// gutterWidth = 5;
				}
				jQuery( cell ).css( 'width', 'calc(' + width + '% - ' + gutterWidth + 'px)' );
			} );

			// Add a gutter/divider before our first cell and after our last cell.
			var html = '<div class="gutter" style="width: 10px; cursor: ew-resize;"></div>';
			jQuery( this.el ).find( '.layouts-cell:first' ).before( html );
			jQuery( this.el ).find( '.layouts-cell:last' ).after( html );
		}

	} );

	return view;
} );