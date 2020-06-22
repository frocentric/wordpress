<?php


class Tribe__Events__Pro__Recurrence__Sequence {

	/**
	 * @var array
	 */
	protected $sequence;

	/**
	 * @var int
	 */
	protected $parent_event_id;

	/**
	 * @var bool
	 */
	protected $has_sorted_sequence = false;

	/**
	 * @var string
	 */
	protected $timezone_string;

	/**
	 * @var array
	 */
	protected $sorted_sequence;

	/**
	 * Tribe__Events__Pro__Recurrence__Sequence constructor.
	 *
	 * @param array $sequence
	 * @param int $parent_event_id
	 */
	public function __construct( array $sequence, $parent_event_id ) {
		if ( ! empty( $sequence ) ) {
			$this->ensure_sequence_format( $sequence );
		}

		$this->ensure_event( $parent_event_id );

		$this->sequence        = $sequence;
		$this->parent_event_id = $parent_event_id;
	}

	/**
	 * Checks that each sequence element is an array containing a timestamp.
	 *
	 * @param array $sequence
	 */
	private function ensure_sequence_format( array $sequence ) {
		foreach ( $sequence as $key => $value ) {
			if ( ! ( is_array( $value ) && isset( $value['timestamp'] ) && is_numeric( $value['timestamp'] ) ) ) {
				throw new InvalidArgumentException( 'Any sequence entry must contain a Unix timestamp under the `timestamp` key' );
			}
		}
	}

	/**
	 * @param $parent_event_id
	 */
	private function ensure_event( $parent_event_id ) {
		if ( ! tribe_is_event( $parent_event_id ) ) {
			throw new InvalidArgumentException( 'Parent event ID should be a valid event post' );
		}
	}

	/**
	 * @param bool $keep_original_index Whether the original sequence index associated to each element
	 *                                  should be returned in the resulting array or not.
	 *
	 * @return array The sequence sorted by start date (not time) ASC
	 */
	public function get_sorted_sequence_array( $keep_original_index = true ) {
		$this->sort_sequence( $keep_original_index );

		return $this->sequence;
	}

	/**
	 * @param bool $keep_original_index
	 */
	private function sort_sequence( $keep_original_index = true ) {
		if ( $this->has_sorted_sequence ) {
			return;
		}

		// determine the parent event timezone to use for same day comparison between events
		$timezone              = Tribe__Events__Timezones::get_event_timezone_string( $this->parent_event_id );
		$this->timezone_string = Tribe__Events__Timezones::generate_timezone_string_from_utc_offset( $timezone );

		//add the original key to entry array in the sequence
		if ( $keep_original_index ) {
			array_walk( $this->sequence, array( $this, 'set_original_index' ) );
		}

		// sort the dates to create by starting time
		usort( $this->sequence, array( $this, 'sort_by_start_date' ) );
		$this->has_sorted_sequence = true;
	}

	/**
	 * @param bool $keep_original_index Whether the original sequence index associated to each element
	 *                                  should be returned in the resulting array or not.
	 *
	 * @return array The sequence sorted with an added `sequence` key to specify the sequence order.
	 */
	public function get_sorted_sequence( $keep_original_index = true ) {
		$this->sort_sequence( $keep_original_index );

		if ( null !== $this->sorted_sequence ) {
			return $this->sorted_sequence;
		}

		$sequence = $this->sequence;
		$output   = $sequence;

		/**
		 * @todo Extract this function into a method for common as is used across multiple places.
		 */
		$format = 'Y-m-d';
		$parent_event_timestamp = strtotime( get_post_meta( $this->parent_event_id, '_EventStartDate', true ) . '+00:00' );
		$parent_event_start_date = Tribe__Date_Utils::date_only( $parent_event_timestamp, true, $format );

		/**
		 * $event_dates is an array that behaves like hash table that will help with the counting of sequences on the same
		 * dates of events.
		 */
		$event_dates = array(
			$parent_event_start_date => 1,
		);

		foreach ( $sequence as $key => $entry ) {
			$start_date = Tribe__Date_Utils::date_only( $entry['timestamp'], true, $format );

			// Increase count on the hash if is the same date or create a new counter if not present.
			if ( isset( $event_dates[ $start_date ] ) && is_int( $event_dates[ $start_date ] ) ) {
				$event_dates[ $start_date ] = $event_dates[ $start_date ] + 1;
			} else {
				$event_dates[ $start_date ] = 1;
			}

			$sequence_number = $event_dates[ $start_date ];
			$output[ $key ]['sequence'] = $sequence_number;
		}

		$this->sorted_sequence = $output;

		return $output;
	}


	/**
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	private function sort_by_start_date( $a, $b ) {
		$a_timestamp = $a['timestamp'];
		$b_timestamp = $b['timestamp'];

		if ( $a_timestamp == $b_timestamp ) {
			return 0;
		}

		return ( $a_timestamp < $b_timestamp ) ? - 1 : 1;
	}

	/**
	 * @param array $date_duration
	 * @param int $index
	 */
	protected function set_original_index( array &$date_duration, $index ) {
		$date_duration['original_index'] = $index;
	}

}
