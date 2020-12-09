/**
 * Handles making sure that any help text render if they are on this part.
 *
 * @package Ninja Forms Front-End
 * @subpackage Main App
 * @copyright (c) 2016 WP Ninjas
 * @since 3.0
 */
define( [], function() {
    var controller = Marionette.Object.extend( {
        initialize: function() {
            this.listenTo( nfRadio.channel( 'nfMP' ), 'change:part', this.changePart, this );
        },

        changePart: function( conditionModel, then ) {
            jQuery( '.nf-help' ).each( function() {
                var jBox = jQuery( this ).jBox( 'Tooltip', {
                    theme: 'TooltipBorder',
                    content: jQuery( this ).data( 'text' )
                });
            } );
        },

    });

    return controller;
} );