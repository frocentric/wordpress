/**
 * When Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/whenModel'], function( WhenModel ) {
	var collection = Backbone.Collection.extend( {
		model: WhenModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );