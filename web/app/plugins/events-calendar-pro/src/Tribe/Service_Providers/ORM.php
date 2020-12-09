<?php
/**
 * Registers the filters and functions needed to extend The Events Calendar ORM to support
 * PRO functionality.
 *
 * @since 4.7
 */

/**
 * Class Tribe__Events__Pro__Service_Providers__ORM
 *
 * @since 4.7
 */
class Tribe__Events__Pro__Service_Providers__ORM extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations and registers the required filters.
	 *
	 * @since 4.7
	 */
	public function register() {
		// Not bound as a singleton to leverage the repository instance properties and to allow decoration and injection.
		$this->container->bind( 'events-pro.event-repository', 'Tribe__Events__Pro__Repositories__Event' );
		$this->container->bind( 'events-pro.venue-repository', 'Tribe__Events__Pro__Repositories__Venue' );

		add_filter( 'tribe_events_event_repository_map', array( $this, 'filter_event_repository_map' ) );
		add_filter( 'tribe_events_venue_repository_map', array( $this, 'filter_venue_repository_map' ) );
	}

	/**
	 * Filters the repository resolution map to replace the base TEC repository with the PRO one.
	 *
	 * @since 4.7
	 *
	 * @param array $map A map that associates the repository types to their implementations.
	 *
	 * @return array The modified repository map.
	 */
	public function filter_event_repository_map( array $map ) {
		$map['default'] = 'events-pro.event-repository';

		return $map;
	}

	/**
	 * Filters the repository resolution map to replace the base TEC repository with the PRO one.
	 *
	 * @since 4.7
	 *
	 * @param array $map A map that associates the repository types to their implementations.
	 *
	 * @return array The modified repository map.
	 */
	public function filter_venue_repository_map( array $map ) {
		$map['default'] = 'events-pro.venue-repository';

		return $map;
	}
}
