/**
 * Holds all of our row models.
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/rowModel'], function( rowModel ) {
	var collection = Backbone.Collection.extend( {
		model: rowModel,
		comparator: 'order',

		initialize: function( models, options ) {
			this.formModel = options.formModel;
		},

		validateFields: function() {
			/*
			 * Validate the fields in this row collection.
			 */
			this.trigger( 'validate:fields', this );
		},

		showFields: function() {
			this.trigger( 'show:fields', this );
		},

		hideFields: function() {
			this.trigger( 'hide:fields', this );
		}
	} );
	return collection;
} );