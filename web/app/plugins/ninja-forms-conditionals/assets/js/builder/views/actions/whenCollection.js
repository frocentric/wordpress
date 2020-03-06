/**
 * Collection view for our when collection
 *
 * @package Ninja Forms Conditional Logic
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [ 'views/actions/whenItem' ], function( WhenItem ) {
	var view = Marionette.CollectionView.extend({
		childView: WhenItem,

		initialize: function( options ) {

		},

        onShow: function() {
            /*
             * If we don't have any conditions, add an empty one as we render.
             */
            if ( 0 == this.collection.length ) {
                this.collection.add( {} );
            }
        },

        onBeforeDestroy: function() {
            /*
             * If we don't have any conditions or we have more than one, just return.
             */
            if ( 0 == this.collection.length || 1 < this.collection.length ) return;
            /*
             * If we only have one condition, and we didn't change the "key" attribute, reset our collection.
             * This empties it.
             */
            if ( '' == this.collection.models[0].get( 'key' ) ) {
                this.collection.reset();
            }
        }

	} );

	return view;
} );