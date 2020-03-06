/**
 * Listens for clicks on our different condition controls
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeCondition', this.removeCondition );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:collapseCondition', this.collapseCondition );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeWhen', this.removeWhen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeThen', this.removeThen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:removeElse', this.removeElse );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addWhen', this.addWhen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addThen', this.addThen );
			this.listenTo( nfRadio.channel( 'conditions' ), 'click:addElse', this.addElse );
		},

		removeCondition: function( e, conditionModel ) {
			var conditionCollection = conditionModel.collection;
			conditionModel.collection.remove( conditionModel );

			/*
			 * Register our remove condition event with our changes manager
			 */

			var label = {
				object: 'Condition',
				label: nfcli18n.clickControlsConditionlabel,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: conditionCollection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeCondition', conditionModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		collapseCondition: function( e, conditionModel ) {
			conditionModel.set( 'collapsed', ! conditionModel.get( 'collapsed' ) );
		},

		removeWhen: function( e, whenModel ) {
			var collection = whenModel.collection;
			this.removeItem( whenModel );
			/*
			 * Register our remove when change.
			 */
			
			var label = {
				object: 'Condition - When',
				label: nfcli18n.clickControlsConditionWhen,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeWhen', whenModel, null, label, data );
		},

		removeThen: function( e, thenModel ) {
			var collection = thenModel.collection;
			this.removeItem( thenModel );
			/*
			 * Register our remove then change.
			 */
			
			var label = {
				object: 'Condition - Then',
				label: nfcli18n.clickControlsConditionThen,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeThen', thenModel, null, label, data );
		},

		removeElse: function( e, elseModel ) {
			var collection = elseModel.collection;
			this.removeItem( elseModel );
			/*
			 * Register our remove else change.
			 */
			
			var label = {
				object: 'Condition - Else',
				label: nfcli18n.clickControlsConditionElse,
				change: 'Removed',
				dashicon: 'dismiss'
			};

			var data = {
				collection: collection
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'removeElse', elseModel, null, label, data );
			
		},

		removeItem: function( itemModel ) {
			itemModel.collection.remove( itemModel );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addWhen: function( e, conditionModel ) {
			var whenModel = conditionModel.get( 'when' ).add( {} );

			/*
			 * Register our add when as a change.
			 */
			
			var label = {
				object: 'Condition - When Criteron',
				label: nfcli18n.clickControlsConditionWhenCriteron,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addWhen', whenModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addThen: function( e, conditionModel ) {
			var thenModel = conditionModel.get( 'then' ).add( {} );

			/*
			 * Register our add then as a change.
			 */
			
			var label = {
				object: 'Condition - Do Item',
				label: nfcli18n.clickControlsConditionDoItem,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addThen', thenModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		},

		addElse: function( e, conditionModel ) {
			var elseModel = conditionModel.get( 'else' ).add( {} );

			/*
			 * Register our add when as a change.
			 */
			
			var label = {
				object: 'Condition - Else Item',
				label: nfcli18n.clickControlsConditionElseItem,
				change: 'Added',
				dashicon: 'plus-alt'
			};

			var data = {
				conditionModel: conditionModel
			}

			nfRadio.channel( 'changes' ).request( 'register:change', 'addElse', elseModel, null, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		}

	});

	return controller;
} );
