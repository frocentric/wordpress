<?php
/**
 * An extension of The Events Calendar base repository to support PRO functions.
 *
 * @since 4.7
 */

/**
 * Class Tribe__Events__Pro__Repositories__Event
 *
 * @since 4.7
 */
class Tribe__Events__Pro__Repositories__Event extends Tribe__Events__Repositories__Event {
	/**
	 * A map relating the custom fields labels to their slug.
	 *
	 * @var array
	 */
	protected $custom_fields_map;

	/**
	 * The data payload that should be used to create a recurring event.
	 *
	 * This is created during the save operations and unset afterwards.
	 *
	 * @var array
	 */
	protected $create_recurrence_payload = array();

	/**
	 * A map relating the IDs of the posts the repository is updating
	 * with their recurrence payloads if any.
	 *
	 * @var array
	 */
	protected $update_recurrence_payloads = array();

	/**
	 * The full post array of the event that is being created or updated.
	 *
	 * @var array
	 */
	protected $postarr;

	/**
	 * A map relating post IDs to the post array used to update them.
	 *
	 * This is set while filtering the post arrays for update operations.
	 *
	 * @var
	 */
	protected $update_postarrs;

	/**
	 * Tribe__Events__Pro__Repositories__Event constructor.
	 *
	 * @since 4.7
	 */
	public function __construct() {
		parent::__construct();

		$this->add_schema_entry( 'custom_field', array( $this, 'filter_by_custom_field' ) );
		$this->add_schema_entry( 'custom_field_between', array( $this, 'filter_by_custom_field_between' ) );
		$this->add_schema_entry( 'custom_field_less_than', array( $this, 'filter_by_custom_field_less_than' ) );
		$this->add_schema_entry( 'custom_field_greater_than', array( $this, 'filter_by_custom_field_greater_than' ) );
		$this->add_schema_entry( 'geoloc_lat', array( $this, 'filter_by_geoloc_lat' ) );
		$this->add_schema_entry( 'geoloc_lng', array( $this, 'filter_by_geoloc_lng' ) );
		$this->add_schema_entry( 'geoloc', array( $this, 'filter_by_geoloc' ) );
		$this->add_schema_entry( 'has_geoloc', array( $this, 'filter_by_has_geoloc' ) );
		$this->add_schema_entry( 'near', array( $this, 'filter_by_near' ) );
		$this->add_schema_entry( 'series', array( $this, 'filter_by_in_series' ) );
		$this->add_schema_entry( 'in_series', array( $this, 'filter_by_in_series' ) );
		$this->add_schema_entry( 'related_to', array( $this, 'filter_by_related_to' ) );
	}

	/**
	 * Filters events to include events that have a specified custom field value.
	 *
	 * @since 4.7
	 *
	 * @param string $custom_field The custom field name or label.
	 * @param string $value        A LIKE-compatible string or a regular expression will
	 *                             be used for LIKE or REGEXP comparisons. Use a regular
	 *                             expression to get exact matches. The limitations of SQL
	 *                             REGEXP syntax apply (e.g not modifiers).
	 */
	public function filter_by_custom_field( $custom_field, $value ) {
		$custom_field_key = $this->get_field_slug( $custom_field );

		if ( tribe_is_regex( $value ) ) {
			$this->by( 'meta_regexp', $custom_field_key, tribe_unfenced_regex( $value ) );

			return;
		}

		$this->by( 'meta_like', $custom_field_key, $value );
	}

	/**
	 * Returns the `name` of a custom field given its label.
	 *
	 * Here "custom fields" are those managed by the PRO plugin.
	 *
	 * @since 4.7
	 *
	 * @param string $label The custom field label.
	 *
	 * @return string  The custom field name or the input string if not found.
	 */
	protected function get_field_slug( $label ) {
		if ( null === $this->custom_fields_map ) {
			$custom_fields = tribe_get_option( 'custom-fields', array() );

			$this->custom_fields_map = array_combine(
				array_map( 'strtolower', wp_list_pluck( $custom_fields, 'label' ) ),
				wp_list_pluck( $custom_fields, 'name' )
			);
		}

		return Tribe__Utils__Array::get( $this->custom_fields_map, strtolower( $label ), $label );
	}

	/**
	 * Filters events to include events that have a specified custom field between than the specified values.
	 *
	 * Fetch is inclusive.
	 *
	 * @since 4.7
	 *
	 * @param string $custom_field The custom field name or label.
	 * @param mixed  $low          The lower limit of the interval.
	 * @param mixed  $high         The upper limit of the interval.
	 */
	public function filter_by_custom_field_between( $custom_field, $low, $high ) {
		$this->by( 'meta_between', $this->get_field_slug( $custom_field ), array( $low, $high ) );
	}

	/**
	 * Filters events to include events that have a specified custom field less than the specified value.
	 *
	 * Fetch is not inclusive.
	 *
	 * @since 4.7
	 *
	 * @param string $custom_field The custom field name or label.
	 * @param mixed  $value        The value to compare to.
	 */
	public function filter_by_custom_field_less_than( $custom_field, $value ) {
		$this->by( 'meta_less_than', $this->get_field_slug( $custom_field ), $value );
	}

	/**
	 * Filters events to include events that have a specified custom field greater than the specified value.
	 *
	 * Fetch is not inclusive.
	 *
	 * @since 4.7
	 *
	 * @param string $custom_field The custom field name or label.
	 * @param mixed  $value        The value to compare to.
	 */
	public function filter_by_custom_field_greater_than( $custom_field, $value ) {
		$this->by( 'meta_greater_than', $this->get_field_slug( $custom_field ), $value );
	}

	/**
	 * Filters events to include only those that match the provided series state.
	 *
	 * @since 4.7
	 *
	 * @param bool|int $in_series A boolean to indicate whether to filter events that are part of a series (`true`)
	 *                        or not (`false`); a parent post ID to filter by events in a specific series.
	 *
	 * @return array|null Null if getting events in series, an array of query arguments that should be
	 *                    added to the query otherwise.
	 */
	public function filter_by_in_series( $in_series ) {
		global $wpdb;

		if ( (bool) $in_series ) {
			if ( is_numeric( $in_series ) || $in_series instanceof WP_Post ) {
				$parent_post_id = $in_series instanceof WP_Post ? $in_series->ID : absint( $in_series );
				$children_clause = $wpdb->prepare( "{$wpdb->posts}.post_parent = %d", $parent_post_id );
				$parent_clause   = $wpdb->prepare( "{$wpdb->posts}.ID = %d", $parent_post_id );
			} else {
				$children_clause = "{$wpdb->posts}.post_parent != 0";
				$parent_clause   = "{$wpdb->posts}.post_parent = 0";
			}
			$this->filter_query->join( "JOIN {$wpdb->postmeta} in_series_meta ON {$wpdb->posts}.ID = in_series_meta.post_id " );
			$this->filter_query->where( "{$children_clause}
				OR ( 
					{$parent_clause} 
					AND in_series_meta.meta_key = '_EventRecurrence' 
					AND in_series_meta.meta_value IS NOT NULL 
				)"
			);

			return null;
		}

		return array(
			'post_parent' => 0,
			'meta_query'  => array(
				'no-series-meta' => array(
					'key'     => '_EventRecurrence',
					'value'   => '#',
					'compare' => 'NOT EXISTS',
				),
			),
		);
	}

	/**
	 * Filters events to include only those that match the provided geolocation state.
	 *
	 * @since 4.7
	 *
	 * @param bool $has_geoloc Whether to fetch events related to Venues that have geolocation
	 *                         information available or not.
	 */
	public function filter_by_has_geoloc( $has_geoloc = true ) {
		global $wpdb;

		if ( (bool) $has_geoloc ) {

			/*
			 * If the request is to filter by events that have geoloc then keep any event that has a
			 * Venue with complete (lat AND long) geoloc information.
			 */
			$this->filter_query->join( "JOIN {$wpdb->postmeta} has_geoloc_event_venue
				ON has_geoloc_event_venue.post_id = {$wpdb->posts}.ID AND has_geoloc_event_venue.meta_key = '_EventVenueID'" );
			$this->filter_query->join( $wpdb->prepare( "JOIN {$wpdb->postmeta} has_geoloc_venue_lat
				ON ( has_geoloc_venue_lat.post_id = has_geoloc_event_venue.meta_value AND has_geoloc_venue_lat.meta_key = %s )",
				Tribe__Events__Pro__Geo_Loc::LAT
			) );
			$this->filter_query->join( $wpdb->prepare( "JOIN {$wpdb->postmeta} has_geoloc_venue_lng
				ON ( has_geoloc_venue_lng.post_id = has_geoloc_event_venue.meta_value AND has_geoloc_venue_lng.meta_key = %s )",
				Tribe__Events__Pro__Geo_Loc::LNG
			) );

			$this->filter_query->where( 'has_geoloc_venue_lat.meta_value IS NOT NULL' );
			$this->filter_query->where( 'has_geoloc_venue_lng.meta_value IS NOT NULL' );

			return;
		}

		/*
		 * If the request is to filter by events that have no geoloc then keep any event that does not
		 * have a Venue or that's related to a Venue that does not have complete geoloc information.
		 */
		$this->filter_query->join( "LEFT JOIN {$wpdb->postmeta} has_geoloc_event_venue
			ON ( has_geoloc_event_venue.post_id = {$wpdb->posts}.ID AND has_geoloc_event_venue.meta_key = '_EventVenueID')" );
		$this->filter_query->join( $wpdb->prepare( "LEFT JOIN {$wpdb->postmeta} has_geoloc_venue_lat
			ON has_geoloc_venue_lat.post_id = has_geoloc_event_venue.meta_value AND has_geoloc_venue_lat.meta_key = %s",
			Tribe__Events__Pro__Geo_Loc::LAT
		) );
		$this->filter_query->join( $wpdb->prepare( "LEFT JOIN {$wpdb->postmeta} has_geoloc_venue_lng
			ON has_geoloc_venue_lng.post_id = has_geoloc_event_venue.meta_value AND has_geoloc_venue_lng.meta_key = %s",
			Tribe__Events__Pro__Geo_Loc::LNG
		) );
		$this->filter_query->where( 'has_geoloc_event_venue.meta_id IS NULL
			OR ( has_geoloc_venue_lat.meta_id IS NULL or has_geoloc_venue_lng.meta_id IS NULL)' );
	}

	/**
	 * Filters events to include only those that are geographically close to the provided address
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

	/**
	 * Filters events to include only those that match the provided geoloc latitude and longitude,
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
	 * Filters events to include only those that match the provided geoloc latitude, optionally providing a distance
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
		global $wpdb;

		$this->filter_query->join( "
			JOIN {$wpdb->postmeta} event_venues_lat
			ON ( {$wpdb->posts}.ID = event_venues_lat.post_id AND event_venues_lat.meta_key = '_EventVenueID' )
		" );
		$this->filter_query->join(
			$wpdb->prepare( "
				JOIN {$wpdb->postmeta} venues_meta_lat
				ON ( venues_meta_lat.post_id = event_venues_lat.meta_value AND venues_meta_lat.meta_key = %s )
			", Tribe__Events__Pro__Geo_Loc::LAT )
		);

		$this->filter_query->where(
			$wpdb->prepare(
				'venues_meta_lat.meta_value BETWEEN %d AND %d',
				$lat - $distance,
				$lat + $distance
			)
		);
	}

	/**
	 * Filters events to include only those that match the provided geoloc longitude optionally providing a distance
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
		global $wpdb;

		$this->filter_query->join( "
			JOIN {$wpdb->postmeta} event_venues_long
			ON ( {$wpdb->posts}.ID = event_venues_long.post_id AND event_venues_long.meta_key = '_EventVenueID' )
		" );
		$this->filter_query->join(
			$wpdb->prepare( "
				JOIN {$wpdb->postmeta} venues_meta_long
				ON ( venues_meta_long.post_id = event_venues_long.meta_value AND venues_meta_long.meta_key = %s )
			", Tribe__Events__Pro__Geo_Loc::LNG )
		);

		$this->filter_query->where(
			$wpdb->prepare(
				'venues_meta_long.meta_value BETWEEN %d AND %d',
				$lng - $distance,
				$lng + $distance
			)
		);
	}

	/**
	 * Filters events to include only those that are related to a specific post.
	 *
	 * @since 4.7
	 *
	 * @param int|WP_Post $post Post ID or object.
	 *
	 * @return array|null An array of arguments that should be added to the WP_Query object or null if empty post ID.
	 */
	public function filter_by_related_to( $post ) {

		$post_id = Tribe__Events__Main::postIdHelper( $post );

		if ( ! $post_id ) {
			return null;
		}

		$taxonomies = array(
			'post_tag',
			Tribe__Events__Main::TAXONOMY,
		);

		/**
		 * Filter the taxonomies used for related posts queries.
		 *
		 * @param array $taxonomies Taxonomies that are used to look up related posts.
		 *
		 * @since 4.7
		 */
		$taxonomies = apply_filters( 'tribe_related_posts_taxonomies', $taxonomies );

		$args = array(
			'post__not_in' => array(
				$post_id,
			),
			'tax_query'    => array(
				'relation' => 'OR',
			),
		);

		foreach ( $taxonomies as $taxonomy ) {
			$term_ids = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

			if ( $term_ids && ! is_wp_error( $term_ids ) ) {
				$args['tax_query'][ 'by-' . $taxonomy ] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $term_ids,
				);
			}
		}

		$original_args = $args;

		/**
		 * Filter the arguments used for related posts queries. Added for backwards compatibility.
		 *
		 * @param array $args Query arguments for related post lookups.
		 *
		 * @since 3.2
		 */
		$args = apply_filters( 'tribe_related_posts_args', $args );

		// Check for significant change or no tax_query, if none found then no query needs to happen.
		if ( $original_args === $args && 1 === count( $args['tax_query'] ) ) {
			return null;
		}

		return $args;
	}

	/**
	 * Overrides the base method to store and update some values related to recurrences.
	 *
	 * @since 4.7
	 *
	 * @param array $postarr The post array that should be used to create the event.
	 *
	 * @return mixed The original method return value
	 */
	public function filter_postarr_for_create( array $postarr ) {
		// Let TEC do its filtering first.
		$filtered = parent::filter_postarr_for_create( $postarr );

		if ( ! is_array( $filtered ) ) {
			// It might not be an array and just be false due to some bad data detected by TEC.
			return $filtered;
		}

		// Then, if a `recurrence` entry is present, save it to use it after the event has been created.
		if ( isset( $filtered['meta_input']['recurrence'] ) ) {

			/*
			 * Independently of what method is handling the recurrence creation we store the whole
			 * post array meta input as "recurrence payload" for the purpose of back-compatibility and context.
			 * For the same purpose we save the full post array too.
			 */
			$this->create_recurrence_payload = $filtered['meta_input'];
			$this->postarr                   = $filtered;
			unset( $filtered['meta_input']['recurrence'] );
		}

		return $filtered;
	}

	/**
	 * Overrides the base create method to additionally create recurring events after the main event
	 * is saved.
	 *
	 * @since 4.7
	 *
	 * @return false|WP_Post The original return value from the base repository `create`
	 *                                       method.
	 */
	public function create() {
		$event = parent::create();

		// We cannot be 100% this will always be a post object, so let's just try to get it.
		$event_post = get_post( $event );

		if ( empty( $event ) || ! $event_post instanceof WP_Post ) {
			// No sense in going on.
			$this->create_recurrence_payload = false;

			return $event;
		}

		try {
			set_error_handler( array( $this, 'cast_error_to_exception' ) );

			/*
			 * Many methods "down the road" might expect prefixed or un-prefixed meta keys, e.g. `_EventStartDate`
			 * and `EventStartDate` so we "duplicate" them now in the payload; this covers back-compatibility too.
			 * Recurrence handling methods should handle the case where the recurrence data is empty.
			 */
			if ( empty( $this->create_recurrence_payload ) ) {
				// If the recurrence payload information is still empty then fill it up w/ the event meta.
				$event_meta         = Tribe__Utils__Array::flatten(
					Tribe__Utils__Array::filter_prefixed( get_post_meta( $event_post->ID ), '_Event' )
				);
				$recurrence_payload = Tribe__Utils__Array::add_unprefixed_keys_to( $event_meta );
			} else {
				$recurrence_payload = Tribe__Utils__Array::add_unprefixed_keys_to( $this->create_recurrence_payload );
			}

			$callback           = $this->get_recurrence_creation_callback( $event_post->ID, $recurrence_payload, $this->postarr );

			/*
			 * Since the burden of logging and handling falls on the callback we're not collecting this value.
			 * Filtering callbacks might return empty or falsy values for other reasons than a failure; an
			 * exception is the correct way to signal an error.
			 */
			$callback( $event_post->ID, $recurrence_payload );
		} catch ( Exception $e ) {
			// Something happened, let's log and move on.
			tribe( 'logger' )->log(
				'There was an error updating the recurrence rules and/or exclusions for event ' . $event_post->ID . ': ' . $e->getMessage(),
				Tribe__Log::ERROR,
				__CLASS__
			);
			restore_error_handler();

			return $event;
		}

		restore_error_handler();

		return $event;
	}

	/**
	 * Overrides the base method to store and capture the recurrence information.
	 *
	 * @since 4.7
	 *
	 * @param array $postarr The array of updates for the post.
	 * @param  int  $post_id The ID of the post that is being updated.
	 *
	 * @return mixed The base method return value.
	 */
	public function filter_postarr_for_update( array $postarr, $post_id ) {
		// Let TEC do its filtering first.
		$filtered = parent::filter_postarr_for_update( $postarr, $post_id );

		if ( ! is_array( $filtered ) ) {
			// It might not be an array and just be false due to some bad data detected by TEC.
			return $filtered;
		}

		// Then, if a `recurrence` entry is present, save it to use it after the event has been created.
		if ( isset( $filtered['meta_input']['recurrence'] ) ) {

			/*
			 * In the context of updates the client code should be able to make an event non-recurring; to
			 * support this we will transform "falsy" values into an empty array.
			 * The empty array is chosen to indicate the will to update an event to non-recurring for
			 * back-compatibility reasons.
			 */
			if ( ! tribe_is_truthy( $filtered['meta_input']['recurrence'] ) ) {
				$filtered['meta_input']['recurrence'] = array();
			}

			/*
			 * Independently of what method is handling the recurrence creation/update we store the whole
			 * post array meta input as "recurrence payload" for the purpose of back-compatibility and context.
			 * For the same purpose we save the full post array too.
			 */
			$this->update_recurrence_payloads[ (int) $post_id ] = $filtered['meta_input'];
			$this->update_postarrs[ (int) $post_id ]            = $filtered;
			unset( $filtered['meta_input']['recurrence'] );
		}

		return $filtered;
	}


	/**
	 * Overrides the base method to save, along with the events, their additional recurring event instances.
	 *
	 * This method will not try to "predict" the load like the base `save` method does.
	 * While the save method knows for certain how many events it will update the current recurrence implementation
	 * does not allow to have that forecast.
	 *
	 * @since 4.7
	 *
	 * @param bool $return_promise Whether to return a promise object or just the ids
	 *                             of the updated posts; if `true` then a promise will
	 *                             be returned whether the update is happening in background
	 *                             or not.
	 *
	 * @return array|Tribe__Promise A list of the post IDs that have been (synchronous) or will
	 *                              be (asynchronous) updated if `$return_promise` is set to `false`;
	 *                              the Promise object if `$return_promise` is set to `true`.
	 */
	public function save( $return_promise = false ) {
		$base_return = parent::save( $return_promise );

		if ( empty( $this->update_recurrence_payloads ) ) {
			return $base_return;
		}

		// Let's make sure we're iterating on arrays with an equal number of elements and same order.
		ksort( $this->update_recurrence_payloads );
		ksort( $this->update_postarrs );
		// Each payload should have a post array and viceversa.
		$valid_payloads = array_intersect_key( $this->update_recurrence_payloads, $this->update_postarrs );
		$valid_postarrs = array_intersect_key( $this->update_postarrs, $valid_payloads );

		$iterator = new MultipleIterator();
		$iterator->attachIterator( new ArrayIterator( array_keys( $valid_payloads ) ), 'post_id' );
		$iterator->attachIterator( new ArrayIterator( $valid_payloads ), 'recurrence_payload' );
		$iterator->attachIterator( new ArrayIterator( $valid_postarrs ), 'postarr' );

		set_error_handler( array( $this, 'cast_error_to_exception' ) );

		foreach ( $iterator as $item ) {
			list( $post_id, $recurrence_payload, $postarr ) = $item;

			$event_post = get_post( $post_id );

			if ( empty( $event_post ) || ! $event_post instanceof WP_Post ) {
				// Might have been deleted in the meanwhile, bail.
				continue;
			}

			try {

				/*
				 * During update callbacks might expect more information about the event.
				 * For the purpose of completeness and back-compatibility let's fetch more information from the event
				 * meta and apply the post array meta input on top of it to use the very last version.
				 */
				$event_meta = Tribe__Utils__Array::flatten(
					Tribe__Utils__Array::filter_prefixed( get_post_meta( $post_id ), '_Event' )
				);

				if ( isset( $postarr['meta_input'] ) ) {
					$event_meta = array_merge( $event_meta, $postarr['meta_input'] );
				}

				/*
				 * Many methods "down the road" might expect prefixed or un-prefixed meta keys, e.g. `_EventStartDate`
				 * and `EventStartDate` so we "duplicate" them now in the payload; this covers back-compatibility too.
				 */
				$recurrence_payload = Tribe__Utils__Array::add_unprefixed_keys_to( array_merge( $event_meta, $recurrence_payload ) );

				$callback = $this->get_recurrence_update_callback( $event_post->ID, $recurrence_payload, $postarr );

				/*
				 * Since the burden of logging and handling falls on the callback we're not collecting this value.
				 * Filtering callbacks might return empty or falsy values for other reasons than a failure; an
				 * exception is the correct way to signal an error.
				 */
				$callback( $event_post->ID, $recurrence_payload );
			} catch ( Exception $e ) {
				// Something happened, let's log and move on.
				tribe( 'logger' )->log(
					'There was an error updating the recurrence rules and/or exclusions for event ' . $event_post->ID . ': ' . $e->getMessage(),
					Tribe__Log::ERROR,
					__CLASS__
				);
				restore_error_handler();
			}
		}

		restore_error_handler();

		return $base_return;
	}

	/**
	 * Filters and returns the recurrence creation callback the repository should use to create
	 * recurrences.
	 *
	 * @since 4.7
	 *
	 * @param int   $event_id              The post ID of the event that is currently being saved.
	 * @param mixed $recurrence_payload    The recurrence data payload; there is no guarantee on the format
	 *                                     of it as different implementations might use different formats.
	 * @param array $postarr               The full post array that's been used to update or create the current event.
	 *
	 * @return callable The recurrence creation callback.
	 */
	protected function get_recurrence_creation_callback( $event_id, $recurrence_payload, array $postarr = null ) {
		/**
		 * Filters the callback the Event repository should use to create recurrences.
		 *
		 * The callback will be passed exactly the same arguments this filter passes.
		 *
		 * @since 4.7
		 *
		 * @param callable $callback           The callback that should be used to create posts; defaults
		 *                                     to `Tribe__Events__Pro__Recurrence__Meta::updateRecurrenceMeta`.
		 *                                     The burden of logging failures and reasons rests on the callback.
		 * @param int      $event_id           The post ID of the event that is currently being saved.
		 * @param mixed    $recurrence_payload The recurrence data payload; there is no guarantee on the format
		 *                                     of it as different implementations might use different formats.
		 * @param array $postarr               The full post array that's been used to update or create the current event.
		 *
		 */
		$callback = apply_filters(
			'tribe_repository_event_recurrence_create_callback',
			array( 'Tribe__Events__Pro__Recurrence__Meta', 'updateRecurrenceMeta' ),
			$event_id,
			$recurrence_payload,
			$postarr
		);

		return $callback;
	}

	/**
	 * Filters and returns the recurrence update callback the repository should use to update
	 * recurrences.
	 *
	 * @since 4.7
	 *
	 * @param int   $event_id              The post ID of the event that is currently being updated.
	 * @param mixed $recurrence_payload    The recurrence data payload; there is no guarantee on the format
	 *                                     of it as different implementations might use different formats.
	 * @param array $postarr               The full post array that's been used to update or create the current event.
	 *
	 * @return callable The recurrence creation callback.
	 */
	protected function get_recurrence_update_callback( $event_id, $recurrence_payload, array $postarr ) {
		/**
		 * Filters the callback the Event repository should use to update recurrences.
		 *
		 * The callback will be passed exactly the same arguments this filter passes.
		 *
		 * @since 4.7
		 *
		 * @param callable $callback           The callback that should be used to create posts; defaults
		 *                                     to `Tribe__Events__Pro__Recurrence__Meta::updateRecurrenceMeta`.
		 *                                     The burden of logging failures and reasons rests on the callback.
		 * @param int      $event_id           The post ID of the event that is currently being updated.
		 * @param mixed    $recurrence_payload The recurrence data payload; there is no guarantee on the format
		 *                                     of it as different implementations might use different formats.
		 * @param array $postarr               The full post array that's been used to update the current event.
		 *
		 */
		$callback = apply_filters(
			'tribe_repository_event_recurrence_update_callback',
			array( 'Tribe__Events__Pro__Recurrence__Meta', 'updateRecurrenceMeta' ),
			$event_id,
			$recurrence_payload,
			$postarr
		);

		return $callback;
	}

	/**
	 * Overrides the base method to take display and render context into account.
	 *
	 * @since 4.7
	 *
	 * @return WP_Query A built query object, `get_posts` has not been called yet.
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the query would yield no results anyway.
	 */
	public function get_query() {
		// Handle the case where we're fetching by post name and want all event instances.
		$this->maybe_expand_post_name( );

		if ( ! $this->should_collapse_recurring_event_instances() ) {
			// Nothing to do here!
			return parent::get_query();
		}

		global $wpdb;

		if ( ! $this->has_date_filters() ) {

			/*
			 * Let's not add costly queries if not really needed.
			 * If no date filters are being applied we just want the first event of a series.
			 */
			$this->filter_query->where( "{$wpdb->posts}.post_parent = 0" );
		} else {

			/*
			 * To make the cut an event must fit the date(s) criteria and either:
			 * - have no children and no parent (a single event)
			 * - have children and have no children fitting the criteria (a series first event)
			 * - have a parent not fitting the criteria (a series instance)
			 *
			 * Here we clone the query, run it, and get all the events fitting the date criteria.
			 */
			$secondary_query = clone parent::get_query();
			// Lighten the query fetching IDs only.
			$secondary_query->set( 'fields', 'ids' );
			// Fetch ALL matching event IDs to override what limits the pagination would apply.
			$secondary_query->set( 'posts_per_page', -1 );
			// prevent paging so we avoid invalid SQL: LIMIT 0, -1 errors out
			$secondary_query->set( 'nopaging', true );
			// Order events, whatever the criteria applying to the main query, by start date.
			$secondary_query->set( 'orderby', 'meta_value' );
			$secondary_query->set( 'meta_key', '_EventStartDateUTC' );

			/*
			 * Since we use the `post__in` query argument for our logic
			 * let's remove what `post__in` IDs we might have added to make sure we fetch the correct results.
			 */
			$this->clean_post__in( $secondary_query );

			/*
			 * We need the SQL to get the `post_parent` and `meta_value` fields included in the results.
			 * There is no way to do that other than filtering the WordPress query  generated in the context
			 * of a `get_posts` request.
			 */
			$filter = new Tribe__Repository__Query_Filters();
			$filter->set_query( $secondary_query );
			$filter->fields( 'post_parent' );
			$filter->fields( $wpdb->postmeta . '.meta_value' );
			$request = $filter->get_request();

			$all_ids = $wpdb->get_results( $request, ARRAY_N );

			if ( ! empty( $all_ids ) ) {
				/*
				 * Let's put events in races:
				 * 1. group them by post parent, or own if parent, ID
				 * 2. order them by start date in order
				 */
				$order = $secondary_query->get( 'order', 'ASC' );
				$winners = array_reduce( $all_ids, static function ( array $acc, array $result ) use ( $order ) {
					list( $post_id, $post_parent, $start_date ) = $result;
					$post_id     = (int) $post_id;
					$post_parent = (int) $post_parent;
					$post_parent = 0 === $post_parent ? $post_id : $post_parent;
					$current = isset( $acc[ $post_parent ]['start_date'] ) ? $acc[ $post_parent ]['start_date'] : false;

					if ( ! $current ) {
						$acc[ $post_parent ]['start_date'] = $start_date;
					} else {
						$acc[ $post_parent ]['start_date'] = $order === 'DESC' ? max( $start_date,
							$current ) : min( $start_date, $current );
					}

					if ( $acc[ $post_parent ]['start_date'] !== $current ) {
						$acc[ $post_parent ]['ID'] = $post_id;
					}

					return $acc;
				}, [] );

				// Let's add a query argument to keep track of what post IDs we've added in the `post__in` clause.
				$this->query_args['tribe_post__in'] = array_column( $winners, 'ID' );
				$this->where( 'post__in', array_column( $winners, 'ID' ) );
			}
		}

		return parent::get_query();
	}

	/**
	 * Returns a filtered list of display contexts that reauire recurring event
	 * instance collapsing if the "Show only the first instance of each recurring event"
	 * setting is truthy.
	 *
	 * @since 4.7
	 *
	 * @return array A filtered list of display contexts that require recurring event
	 *               instance collapsing if the "Show only the first instance of each recurring event"
	 *               setting is truthy.
	 */
	public function get_display_contexts_requiring_collapse() {
		/**
		 * Filters the list of display contexts that require recurring event
		 * instance collapsing if the "Show only the first instance of each recurring event"
		 * setting is truthy.
		 *
		 * @since 4.7
		 *
		 * @param array $contexts The list of display contexts that require recurring event
		 *                        instance collapsing if the "Show only the first instance of each recurring event"
		 *                        setting is truthy.
		 * @param Tribe__Repository__Interface This repository object.
		 */
		$contexts = apply_filters(
			"tribe_repository_{$this->filter_name}_display_contexts_requiring_collapse",
			array( 'list' ),
			$this
		);

		return $contexts;
	}

	/**
	 * Returns a filtered list of render contexts that require recurring event
	 * instance collapsing if the "Show only the first instance of each recurring event"
	 * setting is truthy.
	 *
	 * @since 4.7
	 *
	 * @return array A filtered list of render contexts that reauire recurring event
	 *               instance collapsing if the "Show only the first instance of each recurring event"
	 *               setting is truthy.
	 */
	public function get_render_contexts_requiring_collapse() {
		/**
		 * Filters the list of render contexts that require recurring event
		 * instance collapsing if the "Show only the first instance of each recurring event"
		 * setting is truthy.
		 *
		 * @since 4.7
		 *
		 * @param array $contexts The list of render contexts that require recurring event
		 *                        instance collapsing if the "Show only the first instance of each recurring event"
		 *                        setting is truthy.
		 * @param Tribe__Repository__Interface This repository object.
		 */
		$contexts = apply_filters(
			"tribe_repository_{$this->filter_name}_render_contexts_requiring_collapse",
			array( 'widget' ),
			$this
		);

		return $contexts;
	}

	/**
	 * Whether recurring event instances should be collapsed or not in the context of the ORM
	 *  queries at all.
	 *
	 * This check is made on the option, the current render and display contexts.
	 *
	 * @since 4.7
	 *
	 * @return bool Whether recurring event instances should be collapsed or not in the context
	 *              of ORM queries.
	 */
	protected function should_collapse_recurring_event_instances() {
		$should_collapse = tribe_is_truthy( tribe_get_option( 'hideSubsequentRecurrencesDefault', false ) );

		// Take the render and display context into account.
		$should_collapse &= in_array( $this->render_context, $this->get_render_contexts_requiring_collapse(), true )
		                    || in_array( $this->display_context, $this->get_display_contexts_requiring_collapse(), true );

		// Take into account an explicitly set collapse flag.
		if ( isset( $this->query_args['hide_subsequent_recurrences'] ) ) {
			$should_collapse = tribe_is_truthy( $this->query_args['hide_subsequent_recurrences'] );
		}

		/**
		 * Filters whether recurring event instances should be collapsed in ORM queries or not.
		 *
		 * The check is made in respect of the setting, the render and display contexts.
		 *
		 * @since 4.7
		 *
		 * @param bool   $should_collapse Whether recurring event instances should be collapsed in ORM queries or not.
		 * @param string $render_context  The current query render context.
		 * @param string $display_context The current query display context.
		 * @param Tribe__Repository__Interface This repository instance.
		 */
		return apply_filters(
			"tribe_repository_{$this->filter_name}_collapse_recurring_event_instances",
			$should_collapse,
			$this->render_context,
			$this->display_context,
			$this
		);
	}

	/**
	 * Handle the case where the current query display context requires name expansion and we're
	 * fetching by name.
	 *
	 * @since 4.7
	 */
	protected function maybe_expand_post_name() {
		$qa                      = $this->query_args;
		$requires_name_expansion = in_array( $this->display_context, $this->get_display_contexts_requiring_name_expansion(), true );
		$querying_by_name        = isset( $qa['name'] ) || isset( $qa['pagename'] ) || isset( $qa['post_name__in'] );

		if ( ! ( $requires_name_expansion && $querying_by_name ) ) {
			return;
		}

		// Get the name from any possible source in cascading order.
		$names = (array) Tribe__Utils__Array::get( $qa,
			'name', Tribe__Utils__Array::get( $qa,
				'pagename', Tribe__Utils__Array::get( $qa,
					'post_name__in', array() ) ) );

		// Unset it as we're now taking care of this.
		unset( $this->query_args['name'], $this->query_args['pagename'], $this->query_args['post_name__in'] );

		if ( ! empty( $names ) ) {
			// Posts with no parent should match exactly, posts with parent should match the regexp.
			global $wpdb;
			$p             = $wpdb->posts;
			$pattern       = sprintf( '^(%s)', implode( '|', $names ) );
			$name_interval = $this->prepare_interval( $names, '%s' );
			$this->filter_query->where( "{$p}.post_name IN {$name_interval}
					OR ({$p}.post_name REGEXP '{$pattern}' AND {$p}.post_parent != 0)" );
		}
	}

	/**
	 * Returns the filtered list of display contexts that will require the `post_name` to match not only
	 * the parent event one but the instances too.
	 *
	 * @since 4.7
	 *
	 * @return array The filtered list of display contexts that will require the `post_name` to match not only
	 *               the parent event one but the instances too.
	 */
	public function get_display_contexts_requiring_name_expansion() {
		/**
		 * Filters the list of display contexts that require the `post_name` to be "expanded",
		 * really used in a regular expression, in ORM queries.
		 *
		 * @since 4.7
		 *
		 * @param array $contexts The the list of display contexts that require the `post_name` to be "expanded",
		 *                        really used in a regular expression, in ORM queries.
		 * @param Tribe__Repository__Interface This repository object.
		 */
		$contexts = apply_filters(
			"tribe_repository_{$this->filter_name}_display_contexts_requiring_name_expansion",
			array( 'all' ),
			$this
		);

		return $contexts;
	}

	/**
	 * Cleans the `post__in` query argument to make sure its content is not filled with values previously
	 * set by the class methods.
	 *
	 * We use the `post__in` query argument when collapsing recurring event instances to show
	 * only the first upcoming instance. In this method we "clean" the `post__in` clause
	 *
	 * @since TB
	 *
	 * @param WP_Query $secondary_query The query object to update if required.
	 */
	protected function clean_post__in( WP_Query $secondary_query ) {
		if ( ! isset( $secondary_query->query_vars['tribe_post__in'] ) ) {
			return;
		}

		$tribe__post_in = $secondary_query->query_vars['tribe_post__in'];
		$post__in       = $secondary_query->get( 'post__in', false );
		if ( false !== $post__in ) {
			$post__in = array_diff(
				array_map( 'intval', (array) $post__in ),
				array_map( 'intval', (array) $tribe__post_in )
			);
			if ( count( $post__in ) ) {
				$secondary_query->query_vars['post__in'] = $post__in;
			} else {
				unset( $secondary_query->query_vars['post__in'] );
			}
		}
		unset( $secondary_query->query_vars['tribe__post_in'] );
	}
}
