/**
 * Main layout view
 *
 * Regions:
 * mainContent
 * drawer
 * 
 * @package Ninja Forms builder
 * @subpackage App
 * @copyright (c) 2015 WP Ninjas
 * @since 3.0
 */
define( [ 'views/drawerCollection' ], function( DrawerCollectionView ) {
	var view = Marionette.LayoutView.extend({
		tagName: 'div',
		template: '#nf-tmpl-mp-drawer-layout',
		regions: {
			viewport: '#nf-mp-drawer-viewport',
		},

		initialize: function( options ) {
			/*
			 * Make sure that our drawer resizes to match our screen upon resize or drawer open/close.
			 */
			jQuery( window ).on( 'resize', { context: this }, this.resizeWindow );

			this.listenTo( nfRadio.channel( 'drawer' ), 'before:open', this.beforeDrawerOpen );
			this.listenTo( nfRadio.channel( 'drawer' ), 'before:close', this.beforeDrawerClose );
		},

		onBeforeDestroy: function() {
			jQuery( window ).off( 'resize', this.resizeWindow );
		},

		onShow: function() {
			this.viewport.show( new DrawerCollectionView( { collection: this.collection, drawerLayoutView: this } ) );
		},

		/**
		 * When we attach this el to our dom, resize our viewport.
		 * 
		 * @since  3.0
		 * @return void
		 */
		onAttach: function() {
			this.resizeViewport( this.viewport.el );
		},

		/**
		 * Resize our viewport.
		 * 
		 * @since  3.0
		 * @return void
		 */
		resizeViewport: function( viewportEl) {
			/*
			 * If the drawer is closed, our viewport size is based upon the window size.
			 *
			 * If the drawer is opened, our viewport size is based upon the drawer size.
			 */
			var builderEl = nfRadio.channel( 'app' ).request( 'get:builderEl' );
			if ( jQuery( builderEl ).hasClass( 'nf-drawer-opened' ) ) {
				var drawerEl = nfRadio.channel( 'app' ).request( 'get:drawerEl' );
				var targetWidth = targetWidth || jQuery( drawerEl ).outerWidth() - 140;
			} else {
				var targetWidth = targetWidth || jQuery( window ).width() - 140;
			}
			
			jQuery( viewportEl ).width( targetWidth );
		},

		/**
		 * When we resize our browser window, update our viewport size.
		 * 
		 * @since  3.0
		 * @param  {object} e 	event object
		 * @return void
		 */
		resizeWindow: function( e ) {
			e.data.context.resizeViewport( e.data.context.viewport.el );
		},

		beforeDrawerOpen: function() {
			var that = this;
			var drawerEl = nfRadio.channel( 'app' ).request( 'get:drawerEl' );
			var targetWidth = jQuery( drawerEl ).width() - 60;
			
			jQuery( this.viewport.el ).animate( {
				width: targetWidth
			}, 300, function() {
				that.viewport.currentView.showHidePagination( null, targetWidth );
				that.viewport.currentView.maybeScroll( that.collection );
			} );
		},

		beforeDrawerClose: function() {
			var that = this;
			var targetWidth = jQuery( window ).width() - 140;

			jQuery( this.viewport.el ).animate( {
				width: targetWidth
			}, 500, function() {
				that.viewport.currentView.showHidePagination( null, targetWidth );
				that.viewport.currentView.maybeScroll( that.collection );
			} );
		}
	});

	return view;
} );