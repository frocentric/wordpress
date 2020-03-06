/**
 * Collection view for our else statements
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/advanced/elseItem' ], function( ElseItem ) {
	var view = Marionette.CollectionView.extend({
		childView: ElseItem,

		initialize: function( options ) {

		}
	});

	return view;
} );