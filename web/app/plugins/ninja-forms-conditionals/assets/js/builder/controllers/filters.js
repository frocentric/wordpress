/**
 * Register filters for our when/then key groups/settings.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		filters: [],

		initialize: function() {
			nfRadio.channel( 'conditions' ).reply( 'add:groupFilter', this.addFilter, this );
			nfRadio.channel( 'conditions' ).reply( 'get:groupFilters', this.getFilters, this );
		},

		addFilter: function( callback ) {
			this.filters.push( callback );
		},

		getFilters: function() {
			return this.filters;
		}

	});

	return controller;
} );
