<?php
/**
 * An extension of The Events Calendar venue base repository to support PRO functions.
 *
 * @since 4.7
 */

/**
 * Class Tribe__Events__Pro__Repositories__Venue
 *
 * @since 4.7
 */
class Tribe__Events__Pro__Repositories__Venue extends Tribe__Events__Repositories__Venue {

	/**
	 * Tribe__Events__Pro__Repositories__Venue constructor.
	 *
	 * @since 4.7
	 */
	public function __construct() {
		parent::__construct();

		$this->add_schema_entry( 'geoloc_lat', array( $this, 'filter_by_geoloc_lat' ) );
		$this->add_schema_entry( 'geoloc_lng', array( $this, 'filter_by_geoloc_lng' ) );
		$this->add_schema_entry( 'geoloc', array( $this, 'filter_by_geoloc' ) );
		$this->add_schema_entry( 'has_geoloc', array( $this, 'filter_by_has_geoloc' ) );
		$this->add_schema_entry( 'near', array( $this, 'filter_by_near' ) );
	}

	/**
	 * Filters venues to include only those that match the provided geoloc latitude and longitude,
	 * optionally providing a distance from geoloc.
	 *
	 * The unit type used will be the same as defined in the calendar settings.
	 *
	 * @since 4.7
	 *
	 * @param float $lat      The center latitude.
	 * @param float $lng      The center longitude.
	 * @param int   $distance Number of units from the center; defaults to 10 units.
	 */
	public function filter_by_geoloc( $lat, $lng, $distance = 10 ) {
		$this->filter_by_geoloc_lat( $lat, $distance );
		$this->filter_by_geoloc_lng( $lng, $distance );
	}

	/**
	 * Filters venues to include only those that match the provided geolocation state.
	 *
	 * @since 4.7
	 *
	 * @param bool $has_geoloc Whether to fetch venues that have geolocation information available or not.
	 */
	public function filter_by_has_geoloc( $has_geoloc = true ) {
		$meta_by = $has_geoloc ? 'meta_exists' : 'meta_not_exists';

		$this->by( $meta_by, Tribe__Events__Pro__Geo_Loc::LAT );
		$this->by( $meta_by, Tribe__Events__Pro__Geo_Loc::LNG );
	}

	/**
	 * Filters venues to include only those that match the provided geoloc latitude, optionally providing a distance
	 * from geoloc.
	 *
	 * The unit type used will be the same as defined in the calendar settings.
	 *
	 * @since 4.7
	 *
	 * @param float|int $lat      The latitude to use as center.
	 * @param int       $distance The radius to search around the latitude.
	 */
	public function filter_by_geoloc_lat( $lat, $distance = 10 ) {
		$this->by( 'meta_between', Tribe__Events__Pro__Geo_Loc::LAT, array(
			$lat - $distance,
			$lat + $distance,
		), 'DECIMAL' );
	}

	/**
	 * Filters venues to include only those that match the provided geoloc longitude optionally providing a distance
	 * from geoloc.
	 *
	 * The unit type used will be the same as defined in the calendar settings.
	 *
	 * @since 4.7
	 *
	 * @param float|int $lng      The longitude to use as center.
	 * @param int       $distance The radius to search around the latitude.
	 */
	public function filter_by_geoloc_lng( $lng, $distance = 10 ) {
		$this->by( 'meta_between', Tribe__Events__Pro__Geo_Loc::LNG, array(
			$lng - $distance,
			$lng + $distance,
		), 'DECIMAL' );
	}

	/**
	 * Filters venues to include only those that are geographically close to the provided address
	 * within a certain distance.
	 *
	 * This filter will be ignored if the address cannot be resolved to a set of latitude
	 * and longitude coordinates.
	 *
	 * @since 4.7
	 *
	 * @param string $address  The address string.
	 * @param int    $distance The distance in units from the resolved address; defaults to 10.
	 */
	public function filter_by_near( $address, $distance = 10 ) {
		$resolved = Tribe__Events__Pro__Geo_Loc::instance()->geocode_address( $address );

		$bad_values = array(
			'',
			null,
		);

		if (
			false === $resolved
			|| ! isset( $resolved['lat'], $resolved['lng'] )
			|| in_array( $resolved['lat'], $bad_values, true )
			|| in_array( $resolved['lng'], $bad_values, true )
		) {
			// Ignore this filter if we could not resolve to a set of coordinates.
			return;
		}

		$this->filter_by_geoloc( $resolved['lat'], $resolved['lng'], $distance );
	}

}
