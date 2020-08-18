/**
 * Drawer collection view.
 * 
 * @package Ninja Forms builder
 * @subpackage App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/drawerItem' ], function( DrawerItemView ) {
	var view = Marionette.CollectionView.extend( {
		tagName: 'ul',
		childView: DrawerItemView,
		reorderOnSort: true,
		
		initialize: function( options ) {
			this.drawerLayoutView = options.drawerLayoutView;

			/*
			 * When we resize our window, maybe show/hide pagination.
			 */
			jQuery( window ).on( 'resize', { context: this }, this.resizeEvent );

			/*
			 * If our new part title is off screen in the drawer, scroll to it.
			 */
			this.listenTo( this.collection, 'change:part', this.maybeScroll );
		},

		maybeScroll: function( partCollection ) {
			var li = jQuery( this.el ).children( '#' + partCollection.getElement().get( 'key' ) );
			if ( 0 == jQuery( li ).length ) return false;
			var marginLeft = parseInt( jQuery( li ).css( 'marginLeft' ).replace( 'px', '' ) );
			var viewportWidth = jQuery( this.drawerLayoutView.viewport.el ).width();
			var diff = jQuery( li ).offset().left + jQuery( li ).outerWidth() + marginLeft - viewportWidth;

			jQuery( this.drawerLayoutView.viewport.el ).animate( {
				scrollLeft: '+=' + diff
			}, 100 );
		},

		resizeEvent: function( e ) {
			e.data.context.showHidePagination( e.data.context );
		},

		childViewOptions: function( model, index ){
			var that = this;
			return {
				collectionView: that
			}
		},

		onShow: function() {
			var that = this;
			jQuery( this.el ).sortable( {
				items: 'li:not(.no-sort)',
				helper: 'clone',

				update: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'update:partSortable', e, ui, that.collection, that );
				},

				start: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'start:partSortable', e, ui, that.collection, that );
				},

				stop: function( e, ui ) {
					nfRadio.channel( 'mp' ).trigger( 'stop:partSortable', e, ui, that.collection, that );
				}
			} );
		},

		/**
		 * Set our UL width when we attach the html to the dom.
		 *
		 * @since  3.0
		 * @return void
		 */
		onAttach: function() {
			this.setULWidth( this.el );

			/*
			 * When load, hide the pagination arrows if they aren't needed.
			 */
			this.showHidePagination();
		},

		/**
		 * Set the width of our UL based upon the size of its items.
		 * 
		 * @since 3.0
		 * @return void
		 */
		setULWidth: function( el ) {
			if ( 0 == jQuery( el ).find( 'li' ).length ) return;

			var ulWidth = 0;
			jQuery( el ).find( 'li' ).each( function() {
				var marginLeft = parseInt( jQuery( this ).css( 'marginLeft' ).replace( 'px', '' ) );
				ulWidth += ( jQuery( this ).outerWidth() + marginLeft + 2 );
			} );

			jQuery( el ).width( ulWidth );			
		},

		onRemoveChild: function() {
			/* 
			 * Change the size of our collection UL
			 */
			this.setULWidth( this.el );
		},

		onAddChild: function() {
			/* 
			 * Change the size of our collection UL
			 */
			this.setULWidth( this.el );

			this.maybeScroll( this.collection );
		},

		onBeforeAddChild: function( childView ) {
			jQuery( this.el ).css( 'width', '+=100' );
		},

		showHidePagination: function( context, viewportWidth ) {
			context = context || this;

			viewportWidth = viewportWidth || jQuery( context.el ).parent().parent().width() - 120;

			if ( jQuery( context.el ).width() >= viewportWidth ) {
				if ( ! jQuery( context.drawerLayoutView.el ).find( '.nf-mp-drawer-scroll' ).is( ':visible' ) ) {
					jQuery( context.drawerLayoutView.el ).find( '.nf-mp-drawer-scroll' ).show();
				}
			} else {
				if ( jQuery( context.drawerLayoutView.el ).find( '.nf-mp-drawer-scroll' ).is( ':visible' ) ) {
					jQuery( context.drawerLayoutView.el ).find( '.nf-mp-drawer-scroll' ).hide();
					nfRadio.channel( 'app' ).request( 'update:gutters' );
				}
			}
		}
	} );

	return view;
} );