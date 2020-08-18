/**
 * Handles showing and hiding parts in response to Conditional Logic triggers.
 * 
 * @package Ninja Forms Front-End
 * @subpackage Main App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'condition:trigger' ).reply( 'show_part', this.showPart, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'hide_part', this.hidePart, this );
		},

		showPart: function( conditionModel, then ) {
			conditionModel.set( 'alreadyTriggered', true );
			this.changePartVisibility( conditionModel, then, true );
			conditionModel.set( 'alreadyTriggered', false );
		},

		hidePart: function( conditionModel, then ) {
			conditionModel.set( 'alreadyTriggered', true );
			this.changePartVisibility( conditionModel, then, false );
			conditionModel.set( 'alreadyTriggered', false );
		},

		changePartVisibility: function( conditionModel, then, visible ) {


            /**
			 * Multi-Part Reset Flag
			 *   Identifies the initial request of a nested loop of conditions
			 *   so that the alreadyTriggered flags can be cleared.
             */
			var mpResetFlag = Date.now();
			if( ! conditionModel.collection.mpResetFlag ){
                conditionModel.collection.mpResetFlag = mpResetFlag;
			}

			var partCollection = conditionModel.collection.formModel.get( 'formContentData' );
			partCollection.findWhere( { key: then.key } ).set( 'visible', visible );

			// Check our conditions again because we have just shown/hidden a part that could have conditions on it.
			conditionModel.collection.each( function( model ) {
				// Avoid triggering a model's own conditions.
				if( model == conditionModel ) return;
				// Avoid re-triggering conditions, which may cause an infinite loop.
				if( model.get( 'alreadyTriggered' ) ) return;
				// Trigger conditions.
                model.checkWhen();
                // Set a flag to avoid re-triggering these conditions.
                model.set( 'alreadyTriggered', true );
			} );

            /**
			 * Reset Flag Check
			 *   If is the initial request of the nested loop of conditions
			 *   then clear the previously set alreadyTriggered flags.
             */
			if( mpResetFlag == conditionModel.collection.mpResetFlag ){
				// Clear all alreadyTriggered flags.
                conditionModel.collection.invoke( 'set', { 'alreadyTriggered': false } );
                // Clear the Reset Flag.
                conditionModel.collection.mpResetFlag = false;
			}
		}
	});

	return controller;
} );
