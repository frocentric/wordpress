/**
 * Item view for our condition then
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-trigger-item",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-else': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeElse', e, this.model );
		}
	});

	return view;
} );