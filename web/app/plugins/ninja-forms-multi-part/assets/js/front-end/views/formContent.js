define( [ 'views/header', 'views/footer' ], function( HeaderView, FooterView ) {
	var view = Marionette.LayoutView.extend( {
		template: "#tmpl-nf-mp-form-content",

		regions: {
			header: '.nf-mp-header',
			body: '.nf-mp-body',
			footer: '.nf-mp-footer'
		},

		initialize: function( options ) {
			this.formModel = options.formModel;
			this.collection = options.data;
			this.listenTo( this.collection, 'change:part', this.changePart );
			this.listenTo( this.collection, 'change:visible', this.renderHeaderFooter );
		},

		onRender: function() {
			this.header.show( new HeaderView( { collection: this.collection, model: this.collection.getElement() } ) );

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
			
			this.body.show(  new this.formContentView( { collection: this.collection.getElement().get( 'formContentData' ) } ) );
			this.footer.show( new FooterView( { collection: this.collection, model: this.collection.getElement() } ) );
		},

		renderHeaderFooter: function() {
			this.header.show( new HeaderView( { collection: this.collection, model: this.collection.getElement() } ) );
			this.footer.show( new FooterView( { collection: this.collection, model: this.collection.getElement() } ) );
		},

		changePart: function() {
			this.body.show(  new this.formContentView( { collection: this.collection.getElement().get( 'formContentData' ) } ) );
			/*
			 * Scroll the page to the top of the form.
			 */
			var formTop = jQuery( this.body.el ).closest( '.nf-form-cont' ).offset().top;
			if ( jQuery( window ).scrollTop() > formTop - 50 ) {
				jQuery( window ).scrollTop( formTop - 50 );
			}
		},

		events: {
			'click .nf-next': 'clickNext',
			'click .nf-previous': 'clickPrevious'
		},

		clickNext: function( e ) {
			e.preventDefault();
			this.collection.next();
		},

		clickPrevious: function( e ) {
			e.preventDefault();
			this.collection.previous();
		}

	} );

	return view;
} );