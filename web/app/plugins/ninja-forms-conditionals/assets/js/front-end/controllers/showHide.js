/**
 * Handle showing/hiding fields
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'hide_field', this.hideField, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'show_field', this.showField, this );
		},

		hideField: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
			targetFieldModel.set( 'visible', false );
            if ( ! targetFieldModel.get( 'clean' ) ) {
				targetFieldModel.trigger( 'change:value', targetFieldModel );
			}
			
			nfRadio.channel( 'fields' ).request( 'remove:error', targetFieldModel.get( 'id' ), 'required-error' );
		},

		showField: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
			//TODO: Add an error to let the user know the show/hide field is empty.
			if( 'undefined' == typeof targetFieldModel ) return;
			targetFieldModel.set( 'visible', true );
            if ( ! targetFieldModel.get( 'clean' ) ) {
                targetFieldModel.trigger( 'change:value', targetFieldModel );
			}
			var viewEl = { el: nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:el' ) };
            nfRadio.channel( 'form' ).request( 'init:help', viewEl );
		}
	});

	return controller;
} );