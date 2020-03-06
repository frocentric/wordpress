/**
 * Handle adding or removing an option from our list
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'show_option', this.showOption, this );

			nfRadio.channel( 'condition:trigger' ).reply( 'hide_option', this.hideOption, this );
		},

		showOption: function( conditionModel, then ) {
			var option = this.getOption( conditionModel, then );
			option.visible = true;
			this.updateFieldModel( conditionModel, then );
		},

		hideOption: function( conditionModel, then ) {
			var option = this.getOption( conditionModel, then );
			option.visible = false;
			this.updateFieldModel( conditionModel, then );
		},

		getFieldModel: function( conditionModel, then ) {
			return nfRadio.channel( 'form-' + conditionModel.collection.formModel.get( 'id' ) ).request( 'get:fieldByKey', then.key );
		},

		getOption: function( conditionModel, then ) {
			var targetFieldModel = this.getFieldModel( conditionModel, then );
			var options = targetFieldModel.get( 'options' );
			return _.find( options, function( option ) { return option.value == then.value } );
		},

		updateFieldModel: function( conditionModel, then ) {
			var targetFieldModel = this.getFieldModel( conditionModel, then );
			var options = targetFieldModel.get( 'options' );
			targetFieldModel.set( 'options', options );
			targetFieldModel.trigger( 'reRender' );
		}
	});

	return controller;
} );