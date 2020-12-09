<?php

/**
 * Class Tribe__Events__Pro__iCal
 *
 * @since 4.7.5
 */
class Tribe__Events__Pro__iCal {

	/**
	 * Attach hooks
	 *
	 * @since 4.7.5
	 */
	public function hook() {
		add_filter( 'tribe_ical_feed_item', [ $this, 'filter_ical_feed_item' ], 10, 2 );
	}

	/**
	 * Filter on tribe_ical_feed_item to add the data information about the lat, lng if present
	 * on the Event.
	 *
	 * @since 4.7.5
	 *
	 * @param $item Array An array with the data of the item that is updated.
	 * @param $event \Wp_Post The Post object being modified.
	 *
	 * @return array
	 */
	public function filter_ical_feed_item( $item, $event ) {
		/** @var \Tribe__Events__Pro__Geo_Loc $geo */
		$geo  = Tribe__Events__Pro__Geo_Loc::instance();
		$long = $geo->get_lng_for_event( $event->ID );
		$lat  = $geo->get_lat_for_event( $event->ID );


		if ( empty( $long ) || empty( $lat ) ) {
			return $item;
		}

		$current = join( '', $item );
		if ( false === strpos( $current, 'GEO:' ) ) {
			$item[] = sprintf( 'GEO:%s;%s', $lat, $long );
		}

		$title = str_replace(
			[ ',', "\n" ],
			[ '\,', '\n' ],
			html_entity_decode( tribe_get_address( $event->ID ), ENT_QUOTES )
		);

		$location = $this->find_location( $item );

		if ( empty( $title ) || empty( $location ) ) {
			return $item;
		}

		if ( false === strpos( $current, 'X-APPLE-STRUCTURED-LOCATION;' ) ) {
			$item[] = sprintf(
				"X-APPLE-STRUCTURED-LOCATION;VALUE=URI;X-ADDRESS=%s;X-APPLE-RADIUS=500;X-TITLE=%s:geo:%s,%s",
				str_replace( '\,', '', trim( $location ) ),
				trim( $title ),
				$long,
				$lat
			);
		}

		return $item;
	}

	/**
	 * Get the LOCATION value from the $item
	 *
	 * @since 4.7.5
	 *
	 * @param $item array The item where to look for the LOCATION value.
	 *
	 * @return string
	 */
	public function find_location( $item ) {
		$results = array_map(
			function ( $row ) {
				return str_replace( 'LOCATION:', '', $row );
			},
			array_filter(
				$item,
				function ( $row ) {
					return strpos( $row, 'LOCATION:' ) !== false;
				}
			)
		);

		$location = reset( $results );
		return empty( $location ) ? '' : $location;
	}
}