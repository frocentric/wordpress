define( ['views/cellComposite'], function( cellComposite ) {
	var view = Marionette.CompositeView.extend( {
		template: '#nf-tmpl-row',
		childView: cellComposite,
		className: 'nf-row',

		initialize: function() {
			this.collection = this.model.get( 'cells' );

		},

		onAttach: function() {
			if ( 1 < this.collection.length ) {
				jQuery( this.el ).closest( '.nf-form-wrap' ).addClass( 'nf-multi-cell' );
			}
		},

		attachHtml: function( collectionView, childView ) {
			jQuery( collectionView.el ).find( 'nf-cells' ).append( childView.el );
		}
	} );

	return view;
} );