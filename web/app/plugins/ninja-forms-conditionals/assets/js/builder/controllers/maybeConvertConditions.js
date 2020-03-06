/**
 * When we init our action model, check to see if we have a 'conditions' setting that needs to be converted into a collection.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'models/conditionModel' ], function( ConditionModel ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'actions' ), 'init:actionModel', this.maybeConvertConditions );
		},

		maybeConvertConditions: function( actionModel ) {
			var conditions = actionModel.get( 'conditions' );
			if ( ! conditions ) {
				actionModel.set( 'conditions', new ConditionModel() );
			} else if ( false === conditions instanceof Backbone.Model ) {
				actionModel.set( 'conditions', new ConditionModel( conditions ) );
			}
		}

	});

	return controller;
} );
