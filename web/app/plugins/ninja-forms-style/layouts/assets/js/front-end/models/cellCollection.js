/**
 * Holds all of our cell models.
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/cellModel'], function( cellModel ) {
	var collection = Backbone.Collection.extend( {
		model: cellModel,
		comparator: 'order',

		initialize: function( models, options ) {
			this.rowModel = options.rowModel;
			this.formModel = options.formModel;
		}
	} );
	return collection;
} );