/**
 * Item view for our drawer header
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-drawer-header",

		events: {
			'click .nf-add-new': 'clickAddNew'
		},

		clickAddNew: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:addNew', e );
		}
	});

	return view;
} );