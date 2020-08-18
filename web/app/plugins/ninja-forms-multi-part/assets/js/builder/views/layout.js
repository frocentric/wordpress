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
define( [ 'views/drawerLayout' ], function( DrawerLayoutView ) {
	var view = Marionette.LayoutView.extend({
		tagName: 'div',
		template: '#nf-tmpl-mp-layout',

		regions: {
			mainContent: '#nf-mp-main-content',
			drawer: '#nf-mp-drawer'
		},

		initialize: function() {
			this.listenTo( this.collection, 'change:part', this.changePart );
		},

		onShow: function() {
			this.drawer.show( new DrawerLayoutView( { collection: this.collection } ) );

			/*
			 * Check our fieldContentViewsFilter to see if we have any defined.
			 * If we do, overwrite our default with the view returned from the filter.
			 */
			var formContentViewFilters = nfRadio.channel( 'formContent' ).request( 'get:viewFilters' );
			
			/* 
			* Get our first filter, this will be the one with the highest priority.
			*/
			var sortedArray = _.without( formContentViewFilters, undefined );
			var callback = sortedArray[1];
			this.formContentView = callback();

			this.mainContent.show(  new this.formContentView( { collection: this.collection.getFormContentData() } ) );
		},

		events: {
			'click .nf-mp-drawer-scroll-previous': 'clickPrevious',
			'click .nf-mp-drawer-scroll-next': 'clickNext'
		},

		clickPrevious: function( e ) {
			var that = this;
			var scrollLeft = jQuery( this.drawer.currentView.viewport.el ).scrollLeft();
			var lis = jQuery( this.drawer.currentView.viewport.currentView.el ).find( 'li' );

			jQuery( lis ).each( function( index ) {
				/*
				 * If scrollLeft <= the left of this li, then we know we're at the first visible LI.
				 * Move our scroll to the previous LI and return false.
				 */
				if ( 0 < jQuery( this ).offset().left ) {
					var marginLeft = parseInt( jQuery( this ).css( 'marginLeft' ).replace( 'px', '' ) );
					var scrollLeft = jQuery( jQuery( lis )[ index - 1 ] ).outerWidth() + marginLeft + 5
					jQuery( that.drawer.currentView.viewport.el ).animate( {
						scrollLeft: '-=' + scrollLeft
					}, 300 );
					return false;			
				}
			} );
			

		},

		clickNext: function( e ) {
			var that = this;
			var ULWidth = jQuery( this.drawer.currentView.viewport.currentView.el ).width();
			var viewportWidth = jQuery( this.drawer.currentView.viewport.el ).width();
			var scrollLeft = jQuery( this.drawer.currentView.viewport.el ).scrollLeft();
			var lis = jQuery( this.drawer.currentView.viewport.currentView.el ).find( 'li' );
			var viewportTotal = viewportWidth + scrollLeft;
			var widthCounter = 0;
			var scrollLeft = 0;

			jQuery( lis ).each( function( index ) {
				var marginLeft = parseInt( jQuery( this ).css( 'marginLeft' ).replace( 'px', '' ) );
				widthCounter += ( jQuery( this ).outerWidth() + marginLeft + 5 );
				if ( widthCounter >= viewportTotal ) {
					scrollLeft = jQuery( this ).outerWidth() + marginLeft + 5;
					jQuery( that.drawer.currentView.viewport.el ).animate( {
						scrollLeft: '+=' + scrollLeft
					}, 300 );					
					return false;
				}
			} );
		},

		changePart: function() {
			var currentIndex = this.collection.indexOf( this.collection.getElement() );
			var previousIndex = this.collection.indexOf( this.collection.previousElement );

			if ( currentIndex > previousIndex ) {
				var hideDir = 'left';
				var showDir = 'right';
			} else {
				var hideDir = 'right';
				var showDir = 'left';
			}

			var that = this;
			/*
			 * Start our current part sliding out.
			 */
			jQuery( this.mainContent.el ).hide( 'slide', { direction: hideDir }, 100, function() {
				that.mainContent.empty();
				that.mainContent.show( new that.formContentView( { collection: that.collection.getFormContentData() } ) );
			} );

			jQuery( this.mainContent.el ).show( 'slide', { direction: showDir }, 100 );
			jQuery( this.el ).closest( '.nf-app-main' ).scrollTop( 0 );
		}
	});

	return view;
} );