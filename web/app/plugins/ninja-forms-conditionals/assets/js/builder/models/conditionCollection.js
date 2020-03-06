/**
 * Conditon Collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/conditionModel'], function( ConditionModel ) {
	var collection = Backbone.Collection.extend( {
		model: ConditionModel,

		initialize: function( models, options ) {
			this.options = options;
		}
	} );
	return collection;
} );