/**
 * When Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			connector: 'AND',
			key: '',
			comparator: '',
			value: '',
			type: 'field',
			modelType: 'when'
		},

		initialize: function() {
			nfRadio.channel( 'conditions' ).trigger( 'init:whenModel', this );
		}
	} );
	
	return model;
} );