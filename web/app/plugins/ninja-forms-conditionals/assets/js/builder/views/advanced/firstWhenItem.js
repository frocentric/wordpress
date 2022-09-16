/**
 * Item view for our condition's first when
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function( ) {
	var view = Marionette.ItemView.extend({
		template: "#tmpl-nf-cl-advanced-first-when-item",
		
		initialize: function() {
			this.listenTo( this.model, 'change', this.render );
		},

		onRender: function() {
			let el = jQuery( this.el ).find( '[data-type="date"]' );
			jQuery( el ).mask( '9999-99-99' );
		},

		events: {
			'change .setting': 'changeSetting',
			'change .extra': 'changeExtra',
		},

		changeSetting: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:setting', e, this.model );
		},

		changeExtra: function( e ) {
			nfRadio.channel( 'conditions' ).trigger( 'change:extra', e, this.model );
		}
	});

	return view;
} );