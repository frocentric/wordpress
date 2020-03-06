/**
 * Model that holds our row information
 * 
 * @package Ninja Forms Layouts
 * @subpackage Fields
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( ['models/cellCollection'], function( CellCollection ) {
	var model = Backbone.Model.extend( {
		initialize: function() {
			this.set( 'cells', new CellCollection( this.get( 'cells' ), { rowModel: this, formModel: this.collection.formModel } ) );
            this.set( 'order', Number( this.get( 'order' ) ) );
			this.listenTo( this.get( 'cells' ), 'change:errors', this.triggerErrors );
		},

		triggerErrors: function( fieldModel ) {
			this.collection.trigger( 'change:errors', fieldModel );
		}
	} );
	
	return model;
} );