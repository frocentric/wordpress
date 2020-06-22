<?php
/**
 * Handles a collection of Maps methods for the PRO plugin.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Pro\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

use Tribe__Events__Google__Maps_API_Key as GMaps;

/**
 * Class Maps
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Pro\Views\V2
 */
class Maps {

	/**
	 * Setup the map provider for the map view, using the template variables.
	 *
	 * @since  5.0.0
	 *
	 * @param  array $template_vars Previous tempalte variables in which the providers will be added to.
	 *
	 * @return array
	 */
	public function setup_map_provider( $template_vars ) {
		$default_api_key = GMaps::$default_api_key;
		$api_key         = (string) tribe_get_option( GMaps::$api_key_option_name, false );

		if ( empty( $api_key ) ) {
			// If an API key has not been set yet, set it now.
			tribe_update_option( GMaps::$api_key_option_name, $default_api_key );
			$api_key = $default_api_key;
		}

		$map_provider = (object) [
			'ID'             => 'google_maps',
			'api_key'        => $api_key,
			'is_premium'     => ! tribe_is_using_basic_gmaps_api(),
			'javascript_url' => 'https://maps.googleapis.com/maps/api/js',
			'iframe_url'     => 'https://www.google.com/maps/embed/v1/place',
			'map_pin_url'    => trailingslashit( \Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/map-pin.svg',
			'zoom'           => (int) tribe_get_option( 'embedGoogleMapsZoom', 10 ),
		];

		$template_vars['map_provider'] = $map_provider;
		$template_vars['enable_maps']  = tribe_get_option( 'embedGoogleMaps', true );

		return $template_vars;
	}

}
