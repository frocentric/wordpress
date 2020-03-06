/**
 * Handle changing a field's value
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'change_value', this.changeValue, this );
		},

		changeValue: function( conditionModel, then ) {
			var targetFieldModel = nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
			/*
			 * If we have a checkbox then we need to change the value that is set
			 * of the then variable to a 1 or 0 to re-render on the front end when
			 * the condition is met.
			 */
			if( 'checkbox' == targetFieldModel.get( 'type' ) ) {
				// We also need to do the opposite of the value that is in the changed model.
				if( 'unchecked' == targetFieldModel.changed.value ) {
					then.value = 1;
                } else if( 'checked' == targetFieldModel ) {
					then.value = 0;
				}
			}
            /*
             * Change the value of our field model, and then trigger a re-render of its view.
             */
			targetFieldModel.set( 'value', then.value );
			targetFieldModel.trigger( 'reRender', targetFieldModel );
		},

	});
	return controller;
} );