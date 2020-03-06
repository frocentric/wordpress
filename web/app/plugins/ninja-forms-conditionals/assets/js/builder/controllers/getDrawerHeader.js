/**
 * Returns the view to use in the drawer header.
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/drawerHeader' ], function( DrawerHeaderView ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			nfRadio.channel( 'conditional_logic' ).reply( 'get:drawerHeaderView', this.getDrawerHeaderView, this );
		},

		getDrawerHeaderView: function() {
			return DrawerHeaderView;
		}
	});

	return controller;
} );
