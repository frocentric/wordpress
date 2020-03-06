/**
 * Returns an object with each trigger we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "triggers" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-list' ).reply( 'get:triggers', this.getListTriggers );
			nfRadio.channel( 'conditions-submit' ).reply( 'get:triggers', this.getSubmitTriggers );
		},

		getListTriggers: function( defaultTriggers ) {
			var triggers = _.extend( defaultTriggers, {
				select_option: {
					label: nfcli18n.coreTriggersSelectOption,
					value: 'select_option'
				},

				deselect_option: {
					label: nfcli18n.coreTriggersDeselectOption,
					value: 'deselect_option'
				},

				show_option: {
					label: nfcli18n.coreTriggersShowOption,
					value: 'show_option'
				},

				hide_option: {
					label: nfcli18n.coreTriggersHideOption,
					value: 'hide_option'
				}
			} );

			var triggers = _.omit( defaultTriggers, 'change_value' );

			return triggers;
		},

		getSubmitTriggers: function( defaultTriggers ) {
			return _.omit( defaultTriggers, 'change_value' );
		}

	});

	return controller;
} );