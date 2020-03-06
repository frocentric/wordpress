/**
 * Individual cell view.
 *
 */
define( ['views/emptyCell'], function( EmptyCell ) {
	var view = Marionette.CollectionView.extend( {
		tagname: 'div',
		className: 'layouts-cell',
		emptyView: EmptyCell,
		dropping: false,

		initialize: function( options ) {
			// Set our collection to the fields within this cell
			this.collection = this.model.get( 'fields' );
			// Get our field view from Ninja Forms core.
			this.childView = nfRadio.channel( 'views' ).request( 'get:fieldItem' );
			// Get the collection to which this cell belongs.
			this.cellCollection = options.cellCollection;
		},

		/**
		 * When we render we need to:
		 * 1) Set a data attribute on our cell element representing width.
		 * 2) Set a data attribute on our cell element representing the cell model.
		 * 3) Set classes on our cell element based upon how many fields it contains.
		 * 4) Make our cell sortable droppable if we have more than one cell in the collection.
		 * 5) Init our sortable.
		 * 
		 * @since  version
		 * @return {[type]} [description]
		 */
		onRender: function() {
			jQuery( this.el ).data( 'width', this.model.get( 'width' ) );
			jQuery( this.el ).data( 'model', this.model );
			// Used during troubleshooting to add a class to the cell element.
			// jQuery( this.el ).addClass( this.model.cid );

			// if ( 1 < this.cellCollection.length ) {
				// jQuery( this.el ).addClass( 'layouts-droppable nf-fields-sortable' );	
			// } else { // we want a draggable.

			// }
			
			// Set a class based upon how many fields are in our cell.
			if ( this.collection.models.length == 1 ) {
				jQuery( this.el ).addClass( 'single-field' );
				jQuery( this.el ).removeClass( 'multi-field' );
			} else {
				jQuery( this.el ).addClass( 'multi-field' );
				jQuery( this.el ).removeClass( 'single-field' );
			}

			// Make this cell droppable if we have more than one field.
			if ( 1 < this.cellCollection.length ) {
				jQuery( this.el ).addClass( 'layouts-droppable' );	
				// If we have multiple cells in this row, make this cell droppable for new fields
				jQuery( this.el ).addClass( 'nf-field-type-droppable' );
			} else {
				jQuery( this.el ).removeClass( 'nf-field-type-droppable' );
				jQuery( this.el ).removeClass( 'layouts-droppable' );	
			}

			// Init our sortable.
			this.initSortable();
		},

		/**
		 * Initialize our sortable.
		 * Sends out radio messages when there are sortable events.
		 * 
		 * @since  3.0
		 * @return void
		 */
		initSortable: function() {
			var that = this;
			jQuery( this.el ).sortable( {
				// Don't let the item controls be used as a handle.
				cancel: '.nf-item-controls',
				// Class name of our placeholder. Adds the green highlight.
				placeholder: 'nf-fields-sortable-placeholder',
				// Opacity of the draggable
				opacity: 0.95,
				// Acceptable items.
				items: '.nf-field-wrap, .nf-stage',
				// We care about the pointer, not an intersection.
				tolerance: 'pointer',
				// Allows us to drop items from this sortable into others.
				connectWith: '.layouts-droppable',
				// Update droppable areas as we drag. Important because of the "swell" effect on the builder.
				refreshPositions: true,
				appendTo: '#nf-main',
				
				/**
				 * Return a helper that will be used for the drag event of the sortable.
				 * 
				 * @since  3.0
				 * @param  object e event object
				 * @return object   drag element
				 */
				helper: function( e ) {
					var element = nfRadio.channel( 'layouts' ).request( 'getHelper:cellSortable', e, that, this );
					return element;
				},

				/**
				 * When we hover over our sortable while dragging, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				over: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'over:cellSortable', e, ui, that, this );
				},

				/**
				 * When we hover over out of our sortable while dragging, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				out: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'out:cellSortable', e, ui, that, this );
				},

				/**
				 * When we sort our sortable while dragging, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
		        sort: function( e, ui) {
		        	     	nfRadio.channel( 'layouts' ).trigger( 'sort:cellSortable', e, ui, that, this );
		        },
				
				/**
				 * When we receive an item, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				receive: function( e, ui ) {
					if ( ui.item.dropping ) return;
					nfRadio.channel( 'layouts' ).trigger( 'receive:cellSortable', e, ui, that, this );
				},

				/**
				 * When we start dragging, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				start: function( e, ui ) {

					nfRadio.channel( 'layouts' ).trigger( 'start:cellSortable', e, ui, that, this );
				},

				/**
				 * When we stop dragging, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				stop: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'stop:cellSortable', e, ui, that, this );
				},

				/**
				 * When we update our sortable order, send out a radio message.
				 * 
				 * @since  3.0
				 * @param  object e  event object
				 * @param  object ui jQuery UI object
				 * @return void
				 */
				update: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'update:cellSortable', e, ui, that, this );
				}
			} );
		},

		/**
		 * Template helper functions
		 * 
		 * @since  3.0
		 * @return void
		 */
		templateHelpers: function() {
			return {
				renderHandleBefore: function() {
					return '<div class="layouts-handle"></div>';
				},

				renderHandleAfter: function() {
					return '<div class="layouts-handle"></div>';
				}
			};
		},

		/**
		 * View events
		 * 
		 * @type {Object}
		 */
		events: {
			'click .delete': 'clickDeleteCell'
		},

		/**
		 * When the user clicks to delete a cell, remove the model.
		 * 
		 * @since  3.0
		 * @param  {Object} e event object
		 * @return void
		 */
		clickDeleteCell: function( e ) {
			nfRadio.channel( 'layouts' ).trigger( 'click:deleteCell', e, this );
		}
	} );

	return view;
} );