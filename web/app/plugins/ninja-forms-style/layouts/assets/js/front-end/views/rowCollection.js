define( ['views/rowComposite'], function( rowComposite ) {
	var view = Marionette.CollectionView.extend({
		tagName: 'nf-rows-wrap',
		childView: rowComposite

	});

	return view;
} );