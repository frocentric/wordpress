/**
 * Keep an internal record for which actions are active and deactive.
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		actions: {},
		
		initialize: function() {
			/*
			 * Listen for activate/deactivate action messages.
			 */
			nfRadio.channel( 'condition:trigger' ).reply( 'activate_action', this.activateAction, this );
			nfRadio.channel( 'condition:trigger' ).reply( 'deactivate_action', this.deactivateAction, this );
		
			/*
			 * Reply to requests for action status.
			 */
			nfRadio.channel( 'actions' ).reply( 'get:status', this.getStatus, this );
		},

		activateAction: function( conditionModel, thenObject ) {
			this.actions[ thenObject.key ] = true;
			console.log( 'activate action' );
		},

		deactivateAction: function( conditionModel, thenObject ) {
			console.log( 'deactivate action' );
			this.actions[ thenObject.key ] = false;
		},

		getStatus: function( $id ) {
			return this.actions[ $id ];
		}
	});

	return controller;
} );