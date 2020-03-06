define( [], function() {
	var view = Marionette.CompositeView.extend( {
		template: '#nf-tmpl-cell',
		className: 'nf-cell',

		getChildView: function() {
			return nfRadio.channel( 'views' ).request( 'get:fieldLayout' );
		},

		initialize: function() {
			this.collection = this.model.get( 'fields' );
			// Get our fieldItem view.
			jQuery( this.el ).css( 'width', this.model.get( 'width' ) + '%' );
		},

		onRender: function() {
			if ( 0 == this.collection.length ) {
				jQuery( this.el ).html( '&nbsp;' );
			}
		},

		attachHtml: function( collectionView, childView ) {
			jQuery( collectionView.el ).find( 'nf-fields' ).append( childView.el );
		}
	} );

	return view;
} );