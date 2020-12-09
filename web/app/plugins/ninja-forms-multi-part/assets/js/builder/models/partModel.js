/**
 * Model that represents part information.
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var model = Backbone.Model.extend( {
		defaults: {
			formContentData: [],
			order: 0,
			type: 'part',
			clean: true,
			title: 'Part Title'
		},

		initialize: function() {
			/*
			 * TODO: For some reason, each part model is being initialized when you add a new part.
			 */
			this.on( 'change:title', this.unclean );
			this.filterFormContentData();
			this.listenTo( this.get( 'formContentData' ), 'change:order', this.sortFormContentData );
			/*
			 * When we remove a field from our field collection, remove it from this part if it exists there.
			 */
			var fieldCollection = nfRadio.channel( 'fields' ).request( 'get:collection' );
			this.listenTo( fieldCollection, 'remove', this.triggerRemove );

			/*
			 * Set a key for part.
			 */
			if ( ! this.get( 'key' ) ) {
				this.set( 'key', Math.random().toString( 36 ).replace( /[^a-z]+/g, '' ).substr( 0, 8 ) );
			}
            // Cast order as a number to avoid string comparison.
            this.set( 'order', Number( this.get( 'order' ) ) );
		},

		unclean: function() {
			this.set( 'clean', false );
		},

		sortFormContentData: function() {
			this.get( 'formContentData' ).sort();
		},

		triggerRemove: function( fieldModel ) {
			if ( _.isArray( this.get( 'formContentData' ) ) ) {
				this.filterFormContentData();
			}
			this.get( 'formContentData' ).trigger( 'remove:field', fieldModel );
		},

		filterFormContentData: function() {
			if ( ! this.get( 'formContentData' ) ) return;

			var formContentData = this.get( 'formContentData' );
			/*
			 * Update our formContentData by running it through our fromContentData filter
			 */
			var formContentLoadFilters = nfRadio.channel( 'formContent' ).request( 'get:loadFilters' );
			/* 
			* Get our second filter, this will be the one with the highest priority after MP Forms.
			*/
			var sortedArray = _.without( formContentLoadFilters, undefined );
			var callback = sortedArray[ 1 ];
			/*
			 * If our formContentData is an empty array, we want to pass the "empty" flag as true so that filters know it's purposefully empty.
			 */
			var empty = ( 0 == formContentData.length ) ? true : false;
			/*
			 * TODO: This is a bandaid fix to prevent forms with layouts and parts from freaking out of layouts & styles are deactivated.
			 * If Layouts is deactivated, it will try to grab the layout data and show the fields on the appropriate parts.
			 */
			if ( 'undefined' == typeof formContentLoadFilters[4] && _.isArray( formContentData ) && 0 != formContentData.length && 'undefined' != typeof formContentData[0].cells ) {
				/*
				 * We need to get our field keys from our layout data.
				 * Layout data looks like:
				 * Rows
				 *   Row
				 *     Cells
				 *       Cell
				 *         Fields
				 *       Cell
				 *         Fields
				 *   Row
				 *     Cells
				 *       Cell
				 *         Fields  
				 */
				var partFields = [];
				var cells = _.pluck( formContentData, 'cells' );
				_.each( cells, function( cell ) {
					var fields = _.flatten( _.pluck( cell, 'fields' ) );
					partFields = _.union( partFields, fields );
				} );

				formContentData = partFields;

				this.set( 'formContentData', formContentData );
			}

			this.set( 'formContentData', callback( formContentData, empty, formContentData ) );
		}

	} );

	return model;
} );
