/**
 * Interprets formContent data when a form is loaded.
 * Also returns our MP Layout View to use in place of the standard form layout view.
 * 
 * @package Ninja Forms Front-End
 * @subpackage Main App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/formContent', 'models/partCollection' ], function( FormContentView, PartCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'formContent' ).request( 'add:viewFilter', this.getformContentView, 1 );
			nfRadio.channel( 'formContent' ).request( 'add:loadFilter', this.formContentLoad, 1 );
		},

		/**
		 * Return the MP Content View
		 * 
		 * @since  3.0
		 * @param  {Backbon.Collection} collection formContentData
		 * @return {Backbone.View}            Our MP Content View
		 */
		getformContentView: function( formContentData ) {
			return FormContentView;
		},

		/**
		 * When we load our front-end view, we filter the formContentData.
		 * This turns the saved object into a Backbone Collection.
		 * 
		 * @since  3.0
		 * @param  array formContentData current value of our formContentData.
		 * @return Backbone.Collection
		 */
		formContentLoad: function( formContentData, formModel ) {
			/*
			 * If the data has already been converted, just return it.
			 */
			if ( true === formContentData instanceof PartCollection ) return formContentData;
			/*
			 * If the data isn't converted, but is an array, let's make sure it's part data.
			 */
			if ( _.isArray( formContentData ) && 0 != _.isArray( formContentData ).length  && 'undefined' != typeof _.first( formContentData ) && 'part' == _.first( formContentData ).type ) {
				/*
				 * We have multi-part data. Let's convert it to a collection.
				 */
				var partCollection = new PartCollection( formContentData, { formModel: formModel } );
			} else {
				/*
				 * We have unknown data. Create a new part collection and use the data as the content.
				 */
				var partCollection = new PartCollection( { formContentData: formContentData }, { formModel: formModel } );
			}

			return partCollection;
		}
	});

	return controller;
} );