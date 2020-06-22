<?php
/**
 * A Geo Location handler that handles the fencing expecting to find the geo location information resolved in the
 * context.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc;

use Tribe__Context as Context;

/**
 * Class Resolved_Coords_Handler
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */
class Resolved_Coords_Handler extends Base_Handler implements Handler_Interface {

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.7.9
	 */
	public function filter_repository_args( array $repository_args = [], Context $context = null ) {
		$context = $context ?: tribe_context();

		// If there are no venues to start with, then do not even bother making a request and do not fence at all.
		if ( ! tribe_venues()->found() ) {
			return $repository_args;
		}

		$lat = $context->get( 'geoloc_lat', false );
		$lng = $context->get( 'geoloc_lng', false );

		if ( false === $lat || false === $lng ) {
			return $repository_args;
		}

		$venues = $this->fencer->get_venues_in_geofence( $lat, $lng );

		if ( $venues ) {
			$repository_args['venue'] = (array) $venues;
		} else {
			$repository_args['void_query'] = true;
		}

		return $repository_args;
	}
}
