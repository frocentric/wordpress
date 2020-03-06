/**
 * Handles undoing everything for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'changes' ).reply( 'undo:addCondition', this.undoAddCondition, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeCondition', this.undoRemoveCondition, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addWhen', this.undoAddWhen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addThen', this.undoAddThen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:addElse', this.undoAddElse, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeWhen', this.undoRemoveWhen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeThen', this.undoRemoveThen, this );
			nfRadio.channel( 'changes' ).reply( 'undo:removeElse', this.undoRemoveElse, this );
		},

		undoAddCondition: function( change, undoAll ) {
			var dataModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.remove( dataModel );

			/*
			 * Loop through our change collection and remove any setting changes that belong to the condition we've added.
			 */
			var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
			var results = changeCollection.where( function( changeModel ) {
				if ( ( changeModel = dataModel ) || 'undefined' != typeof changeModel.get( 'data' ).conditionModel && changeModel.get( 'data' ).conditionModel == dataModel ) {
					return true;
				} else {
					return false;
				}
			} );

			_.each( results, function( model ) {
				changeCollection.remove( model );
			} );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveCondition: function( change, undoAll ) {
			var dataModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( dataModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddWhen: function( change, undoAll ) {
			var whenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'when' ).remove( whenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddThen: function( change, undoAll ) {
			var thenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'then' ).remove( thenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoAddElse: function( change, undoAll ) {
			var elseModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.conditionModel.get( 'else' ).remove( elseModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveWhen: function( change, undoAll ) {
			var whenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( whenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveThen: function( change, undoAll ) {
			var thenModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( thenModel );

			this.maybeRemoveChange( change, undoAll );
		},

		undoRemoveElse: function( change, undoAll ) {
			var elseModel = change.get( 'model' );
			var data = change.get( 'data' );
			
			data.collection.add( elseModel );

			this.maybeRemoveChange( change, undoAll );
		},

		/**
		 * If our undo action was requested to 'remove' the change from the collection, remove it.
		 * 
		 * @since  3.0
		 * @param  backbone.model 	change 	model of our change
		 * @param  boolean 			remove 	should we remove this item from our change collection
		 * @return void
		 */
		maybeRemoveChange: function( change, undoAll ) {			
			var undoAll = typeof undoAll !== 'undefined' ? undoAll : false;
			if ( ! undoAll ) {
				// Update preview.
				nfRadio.channel( 'app' ).request( 'update:db' );
				var changeCollection = nfRadio.channel( 'changes' ).request( 'get:collection' );
				changeCollection.remove( change );
				if ( 0 == changeCollection.length ) {
					nfRadio.channel( 'app' ).request( 'update:setting', 'clean', true );
					nfRadio.channel( 'app' ).request( 'close:drawer' );
				}
			}
		}

	});

	return controller;
} );
