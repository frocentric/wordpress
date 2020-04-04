/**
 * Setting/unsetting required.
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2019 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'set_required', this.setRequired, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'unset_required', this.unsetRequired, this );
		},

		setRequired: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
            targetFieldModel.set( 'required', 1 );
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

		unsetRequired: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );

			if( 'undefined' == typeof targetFieldModel ) return;
            targetFieldModel.set( 'required', 0 );
            targetFieldModel.trigger( 'reRender', targetFieldModel );
            // Ensure we resolve any errors when the field is no longer required.
			nfRadio.channel( 'fields' ).request( 'remove:error', targetFieldModel.get( 'id' ), 'required-error' );
        }
        
	});

	return controller;
} );