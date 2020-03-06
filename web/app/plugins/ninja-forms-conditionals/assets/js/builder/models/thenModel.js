/**
 * Then Model
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			key: '',
			trigger: '',
			value: '',
			type: 'field',
			modelType: 'then'
		},

		initialize: function() {
			nfRadio.channel( 'conditions' ).trigger( 'init:thenModel', this );
		}
	} );
	
	return model;
} );