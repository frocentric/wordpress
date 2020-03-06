/**
 * Item view for our condition and
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-when-item",

		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},
		
		events: {
			'change .setting': 'changeSetting',
			'click .nf-remove-when': 'clickRemove'
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model )
		},

		clickRemove: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'click:removeWhen', e, this.model );
		}
	});

	return view;
} );