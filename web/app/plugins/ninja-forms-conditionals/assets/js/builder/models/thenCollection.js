/**
 * Then Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/thenModel'], function( ThenModel ) {
	var collection = Backbone.Collection.extend( {
		model: ThenModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );