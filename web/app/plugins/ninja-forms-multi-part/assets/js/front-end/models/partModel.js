define( [], function() {
	var model = Backbone.Model.extend( {
		fieldErrors: {},

		defaults: {
			errors: false,
			visible: true,
			title: ''
		},

		initialize: function() {
			this.filterFormContentData();
			this.listenTo( this.get( 'formContentData' ), 'change:errors', this.maybeChangeActivePart );
			this.fieldErrors[ this.cid ] = [];
			this.on( 'change:visible', this.changeVisible, this );
            // Cast order as a number to avoid string comparison.
            this.set( 'order', Number( this.get( 'order' ) ) );
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

			this.set( 'formContentData', callback( formContentData, this.collection.formModel, empty, formContentData ) );
		},

		maybeChangeActivePart: function( fieldModel ) {
			/*
			 * If we have an error on this part, add an error to our part model.
			 *
			 * If we are on a part that has a higher index than the current part, set this as current.
			 */
			if ( 0 < fieldModel.get( 'errors' ).length ) {
				this.set( 'errors', true );
				this.fieldErrors[ this.cid ].push( fieldModel.get( 'key' ) );
				// this.set( 'fieldErrors', fieldModel.get( 'key' ) );
				if (
					this.collection.getElement() != this &&
					this.collection.indexOf( this.collection.getElement() ) > this.collection.indexOf( this )

				) {
					this.collection.setElement( this );
				}
			} else {
				this.fieldErrors[ this.cid ] = _.without( this.fieldErrors[ this.cid ], fieldModel.get( 'key' ) );
				if ( 0 == this.fieldErrors[ this.cid ].length ) {
					this.set( 'errors', false );
				}
			}
		},

		validateFields: function() {
			this.get( 'formContentData' ).validateFields();
		},

		changeVisible: function() {
			if ( this.get( 'visible' ) ) {
				this.get( 'formContentData' ).showFields();
			} else {
				this.get( 'formContentData' ).hideFields();
			}
		}
	} );

	return model;
} );
