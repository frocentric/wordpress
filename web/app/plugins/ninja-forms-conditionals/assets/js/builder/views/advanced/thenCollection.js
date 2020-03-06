/**
 * Collection view for our then statements
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/advanced/thenItem' ], function( ThenItem ) {
	var view = Marionette.CollectionView.extend({
		childView: ThenItem,

		initialize: function( options ) {

		}
	});

	return view;
} );