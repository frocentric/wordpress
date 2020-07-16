<?php
/**
 * Events Calendar Pro Map View Template Tags
 *
 * Display functions for use in WordPress templates.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {

    /**
     * For use on the Map View when the default Google Maps API key is provided. Attempts
     * to find a Venue from the events in the current loop; if found, will return a Google Map
     * basic embed URL with that Venue's address. Otherwise, returns false.
     *
     * @since 4.4.33
     *
     * @return string|boolean The Google Map embed URL if found, or false.
     */
    function tribe_events_get_map_view_basic_embed_url() {

        global $wp_query;

        if (
            ! isset( $wp_query->posts )
            || empty( $wp_query->posts )
            || ! is_array( $wp_query->posts )
        ) {
            return false;
        }

        $venue_ids = array();

        foreach ( $wp_query->posts as $key => $event ) {
            $venue_id = (int) tribe_get_venue_id( $event->ID );

            if ( 0 < $venue_id ) {
                $venue_ids[] = $venue_id;
            }
        }

        if ( empty( $venue_ids ) || 0 >= $venue_ids[0] ) {
            return false;
        }

        $address_string = '';
        $location_parts = array( 'address', 'city', 'state', 'province', 'zip', 'country' );

        // Form the address string for the map based on the first venue we find.
        foreach ( $location_parts as $val ) {
            $address_part = call_user_func( 'tribe_get_' . $val, $venue_ids[0] );

            if ( $address_part ) {
                $address_string .= $address_part . ' ';
            }
        }

        if ( ! empty( $address_string ) ) {
            return tribe_get_basic_gmap_embed_url( $address_string );
        }

        return false;
    }

}