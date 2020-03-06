/**
 * Listen to changes of any "then" conditions.
 * 
 * If the value is 'show_field' or 'hide_field' and we have not yet added an "opposite," set an "opposite" action in the "else" section.
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'change:then', this.maybeAddElse );
		},

		maybeAddElse: function( e, thenModel ) {
			var opposite = false;
			/*
			 * TODO: Make this more dynamic.
			 * Currently, show, hide, show option, and hide option are hard-coded here.
			 */
			var trigger = jQuery( e.target ).val();
			switch( trigger ) {
				case 'show_field':
					opposite = 'hide_field';
					break;
				case 'hide_field':
					opposite = 'show_field';
					break;
				case 'show_option':
					// opposite = 'hide_option';
					break;
				case 'hide_option':
					// opposite = 'show_option';
					break;
			}

			if ( opposite ) {
				var conditionModel = thenModel.collection.options.conditionModel
				if( 'undefined' == typeof conditionModel.get( 'else' ).findWhere( { 'key': thenModel.get( 'key' ), 'trigger': opposite } ) ) {
					conditionModel.get( 'else' ).add( { type: thenModel.get( 'type' ), key: thenModel.get( 'key' ), trigger: opposite } );
				}
			}
		}
	});

	return controller;
} );
