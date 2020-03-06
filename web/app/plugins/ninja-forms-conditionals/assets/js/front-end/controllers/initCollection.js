/**
 * Initialise condition collection
 * 
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'models/conditionCollection' ], function( ConditionCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function( formModel ) {
			this.collection = new ConditionCollection( formModel.get( 'conditions' ), { formModel: formModel } );
            this.listenTo(nfRadio.channel('fields'), 'reset:collection', this.resetCollection);
		},
        resetCollection: function( fieldsCollection ) {
            var formModel = fieldsCollection.options.formModel;
            this.collection = new ConditionCollection( formModel.get( 'conditions' ), { formModel: formModel } );
        }
	});

	return controller;
} );