/**
 * Add our view and content load filters.
 * 
 * @package Ninja Forms Multi-Part
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define(
	[
		'views/layout',
		'views/gutterLeft',
		'views/gutterRight',
		'views/mainContentEmpty',
		'models/partCollection',
	],
	function (
		LayoutView,
		GutterLeftView,
		GutterRightView,
		MainContentEmptyView,
		PartCollection
	)
	{
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'app' ), 'after:loadViews', this.addFilters );
		},

		addFilters: function() {
			nfRadio.channel( 'formContentGutters' ).request( 'add:leftFilter', this.getLeftView, 1, this );
			nfRadio.channel( 'formContentGutters' ).request( 'add:rightFilter', this.getRightView, 1, this );
		
			nfRadio.channel( 'formContent' ).request( 'add:viewFilter', this.getContentView, 1 );
			nfRadio.channel( 'formContent' ).request( 'add:saveFilter', this.formContentSave, 1 );
			
			nfRadio.channel( 'formContent' ).request( 'add:loadFilter', this.formContentLoad, 1 );

			/*
			 * Add a filter so that we can add a "Parts" group to the advanced conditions selects.
			 */
			nfRadio.channel( 'conditions' ).request( 'add:groupFilter', this.conditionsFilter );
			nfRadio.channel( 'conditions-part' ).reply( 'get:triggers', this.conditionTriggers );

			/*
			 * Listen to changes on our "then" statement.
			 */
			// this.listenTo( nfRadio.channel( 'conditions' ), 'change:then', this.maybeAddElse );

			this.emptyView();
		},

		getLeftView: function() {
			return GutterLeftView;
		},

		getRightView: function() {
			return GutterRightView;
		},

		formContentLoad: function( formContentData ) {
			/*
			 * If the data has already been converted, just return it.
			 */
			if ( true === formContentData instanceof PartCollection ) return formContentData;

			/*
			 * If the data isn't converted, but is an array, let's make sure it's part data.
			 */
			if ( _.isArray( formContentData ) && ! _.isEmpty( formContentData )  && 'undefined' != typeof _.first( formContentData ) && 'part' == _.first( formContentData ).type ) {
				/*
				 * We have multi-part data. Let's convert it to a collection.
				 */
				var partCollection = new PartCollection( formContentData );
			} else {
				formContentData = ( 'undefined' == typeof formContentData ) ? nfRadio.channel( 'fields' ).request( 'get:collection' ).pluck( 'key' ) : formContentData; 

				/*
				 * We have unknown data. Create a new part collection and use the data as the content.
				 */
				var partCollection = new PartCollection( { formContentData: formContentData } );
			}
			nfRadio.channel( 'mp' ).request( 'init:partCollection', partCollection );
			return partCollection;
		},

		getContentView: function() {
			return LayoutView;
		},

		formContentSave: function( partCollection ) {
			/*
			 * For each of our part models, call the next save filter for its formContentData
			 */
			var newCollection = new Backbone.Collection();
			/*
			 * If we don't have a filter for our formContentData, default to fieldCollection.
			 */
			var formContentSaveFilters = nfRadio.channel( 'formContent' ).request( 'get:saveFilters' );
			
			partCollection.each( function( partModel ) {
				var attributes = _.clone( partModel.attributes );

				/* 
				 * Get our first filter, this will be the one with the highest priority.
				 */
				var sortedArray = _.without( formContentSaveFilters, undefined );
				var callback = sortedArray[1];
				var formContentData = callback( attributes.formContentData );
				attributes.formContentData = formContentData;

				newCollection.add( attributes );
			} );

			return newCollection.toJSON();
		},

		emptyView: function() {
			this.defaultMainContentEmptyView = nfRadio.channel( 'views' ).request( 'get:mainContentEmpty' );
			nfRadio.channel( 'views' ).reply( 'get:mainContentEmpty', this.getMainContentEmpty, this );
		},

		getMainContentEmpty: function() {
			if ( 1 == nfRadio.channel( 'mp' ).request( 'get:collection' ).length ) {
				return this.defaultMainContentEmptyView;
			} else {
				return MainContentEmptyView;
			}
		},

		conditionsFilter: function( groups, modelType ) {
			var partCollection = nfRadio.channel( 'mp' ).request( 'get:collection' );
			if ( 0 == partCollection.length || 'when' == modelType ) return groups;

			var partOptions = partCollection.map( function( part ) {
				return { key: part.get( 'key' ), label: part.get( 'title' ) };
			} );

			groups.unshift( { label: 'Parts', type: 'part', options: partOptions } );
			return groups;
		},

		conditionTriggers: function( defaultTriggers ) {
			return {
				show_field: {
					label: 'Show Part',
					value: 'show_part'
				},

				hide_field: {
					label: 'Hide Part',
					value: 'hide_part'
				}
			};
		},

		/**
		 * When we change our then condition, if we are show/hiding a part add the opposite.
		 * 
		 * @since  3.0
		 * @param  {[type]} e         [description]
		 * @param  {[type]} thenModel [description]
		 * @return {[type]}           [description]
		 */
		maybeAddElse: function( e, thenModel ) {
			var opposite = false;
			/*
			 * TODO: Make this more dynamic.
			 * Currently, show, hide, show option, and hide option are hard-coded here.
			 */
			var trigger = jQuery( e.target ).val();
			switch( trigger ) {
				case 'show_part':
					opposite = 'hide_part';
					break;
				case 'hide_part':
					opposite = 'show_part';
					break;
			}

			if ( opposite ) {
				var conditionModel = thenModel.collection.options.conditionModel;
				if( 'undefined' == typeof conditionModel.get( 'else' ).findWhere( { 'key': thenModel.get( 'key' ), 'trigger': opposite } ) ) {
					conditionModel.get( 'else' ).add( { type: thenModel.get( 'type' ), key: thenModel.get( 'key' ), trigger: opposite } );
				}
			}
		}
	});

	return controller;
} );