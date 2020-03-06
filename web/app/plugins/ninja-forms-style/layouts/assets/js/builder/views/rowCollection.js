/**
 * Row collection (sortable) view.
 */
define( ['views/rowItem'], function( RowItemView ) {
	var view = Marionette.CollectionView.extend( {
		tagname: 'div',
		className: 'layouts-row-collection layouts-droppable nf-field-type-droppable nf-fields-sortable',
		childView: RowItemView,
		reorderOnSort: true,

		getEmptyView: function() {
			return nfRadio.channel( 'views' ).request( 'get:mainContentEmpty' );
		},

		initialize: function() {
			this.collection.on( 'add', this.maybeInitSortable, this );
		},

		onBeforeDestroy: function() {
			this.collection.off( 'add', this.maybeInitSortable );
		},

		/**
		 * Remove any rows that are completely empty.
		 * 
		 * @since  3.0
		 * @param  {Backbone.Model} 		rowModel
		 * @param  {int} index
		 * @param  {Backbone.Collection} 	rowCollection [description]
		 * @return {bool}					Should this row be output in the collection view?
		 */
		filter: function( rowModel, index, rowCollection ) {
			var show = false;
			_.each( rowModel.get( 'cells' ).models, function( cell ) {
				if ( 0 != cell.get( 'fields' ).length ) {
					show = true;
				}
			} );

			return show;
		},

		/**
		 * When we render this view, init our rows collection sortable.
		 * 
		 * @since  3.0
		 * @return void
		 */
		onRender: function() {
			this.maybeInitSortable();
		},

		maybeInitSortable: function() {
			if ( 0 < this.collection.models.length ) {
				this.initSortable();
			}			
		},

		initSortable: function() {
			var that = this;
			// Init our sortable.
			jQuery( this.el ).sortable( {
				helper: 'clone',
				handle: '.gutter:first',
				items: '.layouts-row',
				cancel: '.layouts-cell',
				tolerance: 'pointer',
				placeholder: 'nf-fields-sortable-placeholder',
				appendTo: '#nf-main',
				grid: [ 5, 5 ],

				/**
				 * When we start dragging an item, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				start: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'start:rowsSortable', e, ui, that, this );
				},

				/**
				 * When we stop dragging an item, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				stop: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'stop:rowsSortable', e, ui, that, this );
				},

				/**
				 * When we drag an item over our sortable, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				over: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'over:rowsSortable', e, ui, that, this );
				},

				/**
				 * When we move an item off of our sortable, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				out: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'out:rowsSortable', e, ui, that, this );
				},

				/**
				 * When we drop an item on the sortable, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				receive: function( e, ui ) {
					if ( ui.item.dropping ) return;
					nfRadio.channel( 'layouts' ).trigger( 'receive:rowsSortable', e, ui, that, this );
				},

				/**
				 * When we drop an item onto our sortable that changes our item order, trigger an event.
				 * 
				 * @since  3.0
				 * @param  object 	e  event
				 * @param  object 	ui jQuery UI element
				 * @return void
				 */
				update: function( e, ui ) {
					nfRadio.channel( 'layouts' ).trigger( 'update:rowsSortable', e, ui, that, this );
				}
			} );
		}
	} );

	return view;
} );