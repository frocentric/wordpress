define( ['models/conditionModel'], function( ConditionModel ) {
	var collection = Backbone.Collection.extend( {
		model: ConditionModel,

		initialize: function( models, options ) {
			this.formModel = options.formModel;
		}
	} );
	return collection;
} );