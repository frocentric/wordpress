/**
 * Else Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/elseModel'], function( ElseModel ) {
	var collection = Backbone.Collection.extend( {
		model: ElseModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );