<?php
/**
 * The interface any Geo Location service should implement.
 *
 * @since 4.7.9
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc\Services;

/**
 * Interface Service_Interface
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Views\V2\Geo_Loc\Services
 */
interface Service_Interface {
	/**
	 * Resolves an address to a set of latitude, longitude and formatted address.
	 *
	 * @since 4.7.9
	 *
	 * @param string $address The address to attempt and resolve.
	 *
	 * @return Geo_Loc_Data|\WP_Error Either the resolved address data as a value object, or an error object detailing
	 *                         the reasons for the failure.
	 */
	public function resolve_to_coords( $address );
}