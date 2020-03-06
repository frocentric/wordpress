/**
 * Tracks key changes and updates when/then/else models
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:whenModel', this.registerKeyChangeTracker );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:thenModel', this.registerKeyChangeTracker );
			this.listenTo( nfRadio.channel( 'conditions' ), 'init:elseModel', this.registerKeyChangeTracker );
		},

		registerKeyChangeTracker: function( itemModel ) {
            // Update selected field if the selected field's key changes.
            itemModel.listenTo( nfRadio.channel( 'app' ), 'replace:fieldKey', this.updateKey, itemModel );
        },

		updateKey: function( fieldModel, keyModel, settingModel ) {
			var oldKey = keyModel._previousAttributes[ 'key' ];
            var newKey = keyModel.get( 'key' );
            
            if( this.get( 'key' ) == oldKey && this.cid === keyModel.cid ) {
                this.set( 'key', newKey );
            }
		}
	});

	return controller;
} );