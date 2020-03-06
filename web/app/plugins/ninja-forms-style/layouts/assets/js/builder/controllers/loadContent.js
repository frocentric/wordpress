define( ['views/rowCollection', 'controllers/loadControllers', 'models/rowCollection'], function( RowCollectionView, LoadControllers, RowCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'app' ), 'after:loadControllers', this.loadControllers );
		},

		loadControllers: function() {
			new LoadControllers();

			nfRadio.channel( 'formContent' ).request( 'add:viewFilter', this.getFormContentView, 4, this );
			nfRadio.channel( 'formContent' ).request( 'add:saveFilter', this.formContentSave, 4, this );
			nfRadio.channel( 'formContent' ).request( 'add:loadFilter', this.formContentLoad, 4, this );
		
			/*
			 * In the RC for Ninja Forms, the 'formContent' channel was called 'fieldContents'.
			 * This was changed in version 3.0. These radio messages are here to make sure nothing breaks.
			 *
			 * TODO: Remove this backwards compatibility radio calls.
			 */
			nfRadio.channel( 'fieldContents' ).request( 'add:viewFilter', this.getFormContentView, 4, this );
			nfRadio.channel( 'fieldContents' ).request( 'add:saveFilter', this.formContentSave, 4, this );
			nfRadio.channel( 'fieldContents' ).request( 'add:loadFilter', this.formContentLoad, 4, this );
		},

		getFormContentView: function( collection ) {
			return RowCollectionView;
		},

		/**
		 * When we update our database, set the form setting value of 'formContentData' to our row collection.
		 * To do this, we have to break our row collection down into an object, then remove all the extra field settings
		 * so that we're left with just the field IDs.
		 * 
		 * @since  3.0
		 * @return array 
		 */
		formContentSave: function( rowCollection ) {
			var rows = JSON.parse( JSON.stringify( rowCollection ) );	
			_.each( rows, function( row, rowIndex ) {
				_.each( row.cells, function( cell, cellIndex ) {
					_.each( cell.fields, function( field, fieldIndex ) {
						if ( field.key ) {
							rows[ rowIndex ].cells[ cellIndex].fields[ fieldIndex ] = field.key;
						}
					} );
				} );
			} );

			return rows;
		},

		/**
		 * When we load our builder view, we filter the formContentData.
		 * This turns the saved object into a Backbone Collection.
		 *
		 * If we aren't passed any data, then this form hasn't been modified with layouts yet,
		 * so we default to the nfLayouts.rows global variable that is localised for us.
		 * 
		 * @since  3.0
		 * @param  array 		formContentData 	current value of our formContentData.
		 * @param  bool  		empty				is this a purposefully empty collection?
		 * @param  array		fields				fields array to be turned into rows. This is only passed if MP is also active.
		 * @return Backbone.Collection
		 */
		formContentLoad: function( formContentData, empty, fields ) {
			if ( true === formContentData instanceof RowCollection ) return formContentData;
			
			empty = empty || false;
			fields = fields || false;
			var rowArray = [];

			var formContentLoadFilters = nfRadio.channel( 'formContent' ).request( 'get:loadFilters' );

			/*
			 * TODO: Bandaid fix for making sure that we interpret fields correclty when Multi-Part is active.
			 * Basically, if MP is active, we don't want to ever use the nfLayouts.rows.
			 */
			var mpEnabled = ( 'undefined' != typeof formContentLoadFilters[1] ) ? true : false;

			/*
			 * TODO: Bandaid fix for making sure that Layouts can interpret Multi-Part data if Multi-Part is disabled.
			 */
			if ( ! mpEnabled && _.isArray( formContentData ) && 0 != _.isArray( formContentData ).length  && 'undefined' != typeof _.first( formContentData ) && 'part' == _.first( formContentData ).type ) {
				/* 
				 * Get our layout data from inside MP
				 */
				
				formContentData = _.flatten( _.pluck( formContentData, 'formContentData' ) );
				_.each( formContentData, function( row, index ) {
					row.order = index + 1;
				}, this );
			}

			if ( _.isArray( formContentData ) && 0 != formContentData.length && 'undefined' == typeof formContentData[0].cells ) {
				_.each( formContentData, function( key, index ) {
					rowArray.push( {
						order: index,
						cells: [ {
							order: 0,
							fields: [ key ],
							width: '100'
						} ]
					} );

				} );
			} else if ( _.isEmpty( formContentData ) && 'undefined' != typeof nfLayouts && ! mpEnabled ) {
				rowArray = nfLayouts.rows;
			} else {
				rowArray = formContentData;
			}

			/*
			 * Ensure that our rows don't have any empty fields
			 */
			rowArray = _.filter( rowArray, function( row ) {
				/*
				 * Check to see if any of our row's cells have a field.
				 * If it does, return true and move on.
				 */
				return _.some( row.cells, function( cell ) { 
					return 1 <= cell.fields.length;
				} );
			} );

			return new RowCollection( rowArray );
		}
	});

	return controller;
} );