<?php

/**
 * Class Tribe__Events__Pro__Recurrence__Instance
 */
class Tribe__Events__Pro__Recurrence__Instance {
	private $parent_id = 0;
	private $start_date = null;
	private $duration = null;
	private $end_date = null;
	private $timezone = '';
	private $post_id = 0;

	/**
	 * @var int
	 */
	private $sequence_number;

	/**
	 * @param int       $parent_id
	 * @param int|array $date_duration
	 * @param int       $instance_id
	 * @param int|bool  $sequence_number Whether the recurring event instance is part of a sequence and what number the event is in the sequence.
	 *                                   A "sequence" is a group of events that shares the same start date and/or time. By default an event is the first in its
	 *                                   sequence.
	 */
	public function __construct( $parent_id, $date_duration, $instance_id = 0, $sequence_number = 1 ) {
		$this->parent_id  = $parent_id;
		$this->post_id    = $instance_id;

		// We expect $date_duration to be an array containing the start timestamp and duration in seconds
		if ( is_array( $date_duration ) ) {
			list( $this->start_date, $this->duration ) = array_values( $date_duration );
			$this->start_date = new DateTime( '@' . $this->start_date );
		}
		// It's also possible $date_duration will be an int rather than an array(such as if a recurring event
		// queue was established before updating to 3.12.1 - in which case it will not have duration data)
		else {
			$this->start_date = new DateTime( '@' . $date_duration );
			$this->duration = $this->get_parent_duration();
		}

		$this->sequence_number = $sequence_number;
	}

	/**
	 * Saves the recurrence instance and returns its post ID.
	 *
	 * @return int|\WP_Error
	 */
	public function save() {
		$parent       = get_post( $this->parent_id );
		$post_to_save = get_object_vars( $parent );
		unset( $post_to_save['ID'] );
		unset( $post_to_save['guid'] );
		$post_to_save['post_parent'] = $parent->ID;
		$post_to_save['post_name']   = $parent->post_name . '-' . $this->start_date->format( 'Y-m-d' );

		$this->end_date = $this->get_end_date();
		$this->timezone = Tribe__Events__Timezones::get_event_timezone_string( $this->parent_id );

		if ( ! empty( $this->post_id ) ) { // update the existing post
			$post_to_save['ID'] = $this->post_id;
			if ( get_post_status( $this->post_id ) == 'trash' ) {
				$post_to_save['post_status'] = get_post_status( $this->post_id );
			}

			$this->post_id = wp_update_post( $post_to_save );

			update_post_meta( $this->post_id, '_EventStartDate', $this->db_formatted_start_date() );
			update_post_meta( $this->post_id, '_EventStartDateUTC', $this->db_formatted_start_date_utc() );
			update_post_meta( $this->post_id, '_EventEndDate', $this->db_formatted_end_date() );
			update_post_meta( $this->post_id, '_EventEndDateUTC', $this->db_formatted_end_date_utc() );
			update_post_meta( $this->post_id, '_EventDuration', $this->duration );

			/**
			 * Triggers when a recurring event instance is updated due to the whole series being edited.
			 * This action will not fire if a recurring event instance is broken out of the series (e.g. using the "Edit Single" link).
			 *
			 * @param int $post_id   The updated recurring event instance post ID.
			 * @param int $parent_id The updated recurring event instance `post_parent` post ID.
			 */
			do_action( 'tribe_events_pro_recurring_event_instance_updated', $this->post_id, $this->parent_id );
		} else { // add a new post
			$query_args = array( 'eventDate' => $this->start_date->format( 'Y-m-d' ) );

			// if an event is the first in a sequence do not append the sequence var
			if ( ! empty( $this->sequence_number ) && 1 !== $this->sequence_number ) {
				$query_args['eventSequence'] = $this->sequence_number;

			}

			$post_to_save['guid'] = esc_url( add_query_arg( $query_args, $parent->guid ) );

			$this->post_id = wp_insert_post( $post_to_save );

			// save several queries by calling add_post_meta when we have a new post
			add_post_meta( $this->post_id, '_EventStartDate', $this->db_formatted_start_date() );
			add_post_meta( $this->post_id, '_EventStartDateUTC', $this->db_formatted_start_date_utc() );
			add_post_meta( $this->post_id, '_EventEndDate', $this->db_formatted_end_date() );
			add_post_meta( $this->post_id, '_EventEndDateUTC', $this->db_formatted_end_date_utc() );
			add_post_meta( $this->post_id, '_EventDuration', $this->duration );

			if ( ! empty( $this->sequence_number ) && 1 !== $this->sequence_number ) {

				$sequence_number = max( $this->sequence_number, $this->get_next_sequence_number( $this->parent_id ) );

				add_post_meta( $this->post_id, '_EventSequence', $sequence_number );
			}

			/**
			 * Triggers when a recurring event instance is inserted due to the whole series being created.
			 *
			 * @param int $post_id   The updated recurring event instance post ID.
			 * @param int $parent_id The updated recurring event instance `post_parent` post ID.
			 */
			do_action( 'tribe_events_pro_recurring_event_instance_inserted', $this->post_id, $this->parent_id );
		}

		$this->copy_meta(); // everything else
		$this->set_terms();

		/**
		 * Triggers when a recurring event instance is inserted due to the whole series being created or updated.
		 * This action will not fire if a recurring event instance is broken out of the series (e.g. using the "Edit Single" link).
		 *
		 * @param int $post_id The updated recurring event instance post ID.
		 * @param int $parent_id The updated recurring event instance `post_parent` post ID.
		 */
		do_action( 'tribe_events_pro_recurring_event_save_after', $this->post_id, $this->parent_id );

		return $this->post_id;
	}

	/**
	 * Indicates if this instance already seems to be in existence.
	 *
	 * @return bool
	 */
	public function already_exists() {
		$parent = get_post( $this->parent_id );

		$possible_matches = tribe_get_events( array(
			'post_parent' => $parent->ID,
			'name'        => $parent->post_name . '-' . $this->start_date->format( 'Y-m-d' ),
		) );

		foreach ( $possible_matches as $existing_post ) {
			// We have to compare Duration and Start Date, because we can have events on the same day with different times
			if (
				$this->duration == $existing_post->_EventDuration &&
				$this->start_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT ) == $existing_post->_EventStartDate
			) {
				return true;
			}
		}

		return false;
	}

	public function get_id() {
		return $this->post_id;
	}

	public function get_parent_duration() {
		return get_post_meta( $this->parent_id, '_EventDuration', true );
	}

	public function get_end_date() {
		$end_timestamp = (int) ( $this->start_date->format( 'U' ) ) + $this->duration;
		return new DateTime( '@' . $end_timestamp );
	}

	public function get_organizer() {
		$organizer = get_post_meta( $this->parent_id, '_EventOrganizerID', true );
		if ( empty( $organizer ) ) {
			return 0;
		}

		return (int) $organizer;
	}

	public function get_venue() {
		$venue = get_post_meta( $this->parent_id, '_EventVenueID', true );
		if ( empty( $venue ) ) {
			return 0;
		}

		return (int) $venue;
	}

	/**
	 * Return the next sequence number according to the
	 * number of child events.
	 *
	 * @param int $parent_id Parent post ID
	 *
	 * @since 4.4.30
	 *
	 * @return int The next sequence number
	 */
	public function get_next_sequence_number( $parent_id ) {
		global $wpdb;

		// Get the child events
		$child_events = Tribe__Events__Pro__Recurrence__Children_Events::instance()->get_ids( $parent_id );

		// Bail if there are no child events
		if ( ! $child_events ) {
			return 0;
		}

		// get the child event IDs
		$child_events_ids = implode( ',', array_map( 'intval', $child_events ) );

		// Get the max _EventSequence for the child events of $parent_id
		$sequence = $wpdb->get_col(
			"SELECT max(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = '_EventSequence' AND post_id IN ({$child_events_ids})"
		);

		// if we get results, add one and return
		if ( ! empty( $sequence ) ) {
			return max( $sequence ) + 1;
		}

	}

	private function copy_meta() {
		$copier = new Tribe__Events__Pro__Post_Meta_Copier();
		$copier->copy_meta( $this->parent_id, $this->post_id );
	}

	private function set_terms() {
		$taxonomies = get_object_taxonomies( Tribe__Events__Main::POSTTYPE );
		foreach ( $taxonomies as $tax ) {
			$terms    = get_the_terms( $this->parent_id, $tax );
			$term_ids = empty( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );
			wp_set_object_terms( $this->post_id, $term_ids, $tax );
		}
	}

	/**
	 * @return string instance start_date in "Y-m-d H:i:s" format
	 */
	private function db_formatted_start_date() {
		return $this->start_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	/**
	 * @return string instance start_date (converted to UTC) in "Y-m-d H:i:s" format
	 */
	private function db_formatted_start_date_utc() {
		return ( class_exists( 'Tribe__Events__Timezones' ) && ! empty( $this->timezone ) )
			? Tribe__Events__Timezones::to_utc( $this->db_formatted_start_date(), $this->timezone )
			: $this->db_formatted_start_date();
	}

	/**
	 * @return string instance end_date in "Y-m-d H:i:s" format
	 */
	private function db_formatted_end_date() {
		return $this->end_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	/**
	 * @return string instance end_date (converted to UTC) in "Y-m-d H:i:s" format
	 */
	private function db_formatted_end_date_utc() {
		return ( class_exists( 'Tribe__Events__Timezones' ) && ! empty( $this->timezone ) )
			? Tribe__Events__Timezones::to_utc( $this->db_formatted_end_date(), $this->timezone )
			: $this->db_formatted_end_date();
	}
}

