/**
 * Returns an object with each comparator we'd like to use.
 * This covers all the core field types.
 *
 * Add-ons can copy this code structure in order to get custom "comparators" for conditions.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditions-checkbox' ).reply( 'get:comparators', this.getCheckboxComparators );
			nfRadio.channel( 'conditions-listradio' ).reply( 'get:comparators', this.getListSingleComparators );
			nfRadio.channel( 'conditions-listselect' ).reply( 'get:comparators', this.getListSingleComparators );
			nfRadio.channel( 'conditions-list' ).reply( 'get:comparators', this.getListComparators );
		},

		getCheckboxComparators: function( defaultComparators ) {
			return {
				is: {
					label: nfcli18n.coreComparatorsIs,
					value: 'equal'
				},

				isnot: {
					label: nfcli18n.coreComparatorsIsNot,
					value: 'notequal'
				}
			}
		},

		getListComparators: function( defaultComparators ) {
			return {
				has: {
					label: nfcli18n.coreComparatorsHasSelected,
					value: 'contains'
				},

				hasnot: {
					label: nfcli18n.coreComparatorsDoesNotHaveSelected,
					value: 'notcontains'
				}
			}
		},

		getListSingleComparators: function( defaultComparators, currentComparator ) {
			/*
			 * Radio and Select lists need to use equal and notequal.
			 * In previous versions, however, they used contains and notcontains.
			 * In order to keep forms working that were made in those previous versions,
			 * we check to see if the currentComparator is contains or notcontains.
			 * If it is, we return those values; else we return equal or not equal.
			 */
			if ( 'contains' == currentComparator || 'notcontains' == currentComparator ) {
				return {
					has: {
						label: nfcli18n.coreComparatorsHasSelected,
						value: 'contains'
					},

					hasnot: {
						label: nfcli18n.coreComparatorsDoesNotHaveSelected,
						value: 'notcontains'
					}
				}		
			}

			return {
					has: {
						label: nfcli18n.coreComparatorsHasSelected,
						value: 'equal'
					},

					hasnot: {
						label: nfcli18n.coreComparatorsDoesNotHaveSelected,
						value: 'notequal'
					}
				}	
		},


	});

	return controller;
} );
