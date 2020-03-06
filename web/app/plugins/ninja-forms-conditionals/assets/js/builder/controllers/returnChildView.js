/**
 * Returns the childview we need to use for our conditional logic form settings.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/advanced/conditionCollection', 'views/actions/conditionLayout' ], function( AdvancedView, ActionsView ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'advanced_conditions' ).reply( 'get:settingChildView', this.getAdvancedChildView );
			nfRadio.channel( 'action_conditions' ).reply( 'get:settingChildView', this.getActionChildView );
		},

		getAdvancedChildView: function( settingModel ) {
			return AdvancedView;
		},

		getActionChildView: function( settingModel ) {
			return ActionsView;
		}

	});

	return controller;
} );
