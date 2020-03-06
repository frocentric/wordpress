/*
 * View that is rendered if we have no fields within a cell.
 */
define( [], function() {
	var view = Marionette.ItemView.extend( {
		tagname: 'div',
		template: '#nf-tmpl-empty-cell'
	} );

	return view;
} );