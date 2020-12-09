<?php
_deprecated_file( __FILE__, '4.4.30', 'Deprecated class in favor of using `tribe_asset` registration' );

class Tribe__Events__Pro__Asset__Ajax_Maps extends Tribe__Events__Asset__Abstract_Asset {

	public function handle() {

		$api_url = 'https://maps.google.com/maps/api/js';
		$api_key = tribe_get_option( 'google_maps_js_api_key' );

		if ( ! empty( $api_key ) && is_string( $api_key ) ) {
			$api_url = sprintf( 'https://maps.googleapis.com/maps/api/js?key=%s', trim( $api_key ) );
		}

		/**
		 * Allows for filtering the Google Maps API URL.
		 *
		 * @param string $api_url The Google Maps API URL.
		 */
		$url = apply_filters( 'tribe_events_pro_google_maps_api', $api_url );

		wp_register_script( 'tribe-gmaps', $url, array( 'tribe-events-pro' ) );

		$path = Tribe__Events__Template_Factory::getMinFile( tribe_events_pro_resource_url( 'tribe-events-ajax-maps.js' ), true );

		/**
		 * Allows for filtering the version number of Events Calendar Pro's external Google Maps JS
		 * resource.
		 *
		 * @param int $version The version number, defaults to current Events Calendar Pro version.
		 */
		$pro_js_version = apply_filters( 'tribe_events_pro_js_version', Tribe__Events__Pro__Main::VERSION );

		wp_register_script( 'tribe-events-pro-geoloc', $path, array(
			'tribe-gmaps',
			Tribe__Events__Template_Factory::get_placeholder_handle(),
		), $pro_js_version );

		wp_enqueue_script( 'tribe-events-pro-geoloc' );

		$data   = array(
			'ajaxurl'  => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'nonce'    => wp_create_nonce( 'tribe_geosearch' ),
			'map_view' => 'map' == Tribe__Events__Main::instance()->displaying ? true : false,
			'pin_url'  => Tribe__Customizer::instance()->get_option( array( 'global_elements', 'map_pin' ), false ),
		);

		wp_localize_script( 'tribe-events-pro-geoloc', 'GeoLoc', $data );
	}
}
