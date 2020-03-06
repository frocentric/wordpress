/**
 * Updates condition settings on field change or drawer close
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.listenTo( nfRadio.channel( 'conditions' ), 'change:setting', this.updateSetting );
		},

		updateSetting: function( e, dataModel ) {
			var value = jQuery( e.target ).val();
			var id = jQuery( e.target ).data( 'id' );
			var before = dataModel.get( id );

			if ( jQuery( e.target ).find( ':selected' ).data( 'type' ) ) {
				dataModel.set( 'type', jQuery( e.target ).find( ':selected' ).data( 'type' ) );
			}

			dataModel.set( id, value );

			var after = value;

			var changes = {
				attr: id,
				before: before,
				after: after
			};

			/*
			 * The "Advanced" domain uses a collection of conditions, while the "Actions" domain uses a single collection.
			 * Here, if we don't have a collection property, then dataModel must be our conditionModel.
			 */
			var conditionModel = ( 'undefined' == typeof dataModel.collection ) ? dataModel : dataModel.collection.options.conditionModel;

			var data = {
				conditionModel: conditionModel
			}

			var label = {
				object: 'Condition',
				label: 'Condition',
				change: 'Changed ' + id + ' from ' + before + ' to ' + after
			};

			nfRadio.channel( 'changes' ).request( 'register:change', 'changeSetting', dataModel, changes, label, data );

			// Set our 'clean' status to false so that we get a notice to publish changes
			nfRadio.channel( 'app' ).request( 'update:setting', 'clean', false );
			nfRadio.channel( 'app' ).request( 'update:db' );
		}

	});

	return controller;
} );