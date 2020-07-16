<?php


class Tribe__Events__Pro__Updates__Recurrence_Meta_To_Child_Post_Converter {
	/**
	 * Update recurring events to use multiple posts for events
	 * in a series
	 *
	 * @return void
	 */
	public function do_conversion() {
		$post_ids = $this->get_recurring_events_still_using_meta_storage();
		foreach ( $post_ids as $p ) {
			$this->convert_recurring_event_to_child_posts( $p );
		}
	}

	private function get_recurring_events_still_using_meta_storage() {
		/** @var wpdb $wpdb */
		global $wpdb;
		$sql      = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_EventStartDate' GROUP BY post_id HAVING COUNT(meta_key) > 1";
		$post_ids = $wpdb->get_col( $sql );

		return $post_ids;
	}

	private function convert_recurring_event_to_child_posts( $event_id ) {
		$start_dates = get_post_meta( $event_id, '_EventStartDate', false );
		if ( ! is_array( $start_dates ) ) {
			return;
		}

		$original = array_shift( $start_dates );
		$duration = (int) get_post_meta( $event_id, '_EventDuration', true );

		$prepared_start_dates = array();
		foreach ( $start_dates as $start_date ) {
			$prepared_start_dates[] = $this->build_sequence_entry( $start_date, $duration );
		}

		$sequence = new Tribe__Events__Pro__Recurrence__Sequence( $prepared_start_dates, $event_id );

		$i = 0;

		foreach ( $sequence->get_sorted_sequence() as $date ) {
			if ( ! empty( $date ) ) {
				tribe_set_time_limit( 30 );
				$instance = new Tribe__Events__Pro__Recurrence__Instance( $event_id, $date, 0, $date['sequence'] );
				$instance->save();

				$original_meta_value = Tribe__Utils__Array::get( $start_dates, $i, false );

				if ( false !== $original_meta_value ) {
					delete_post_meta( $event_id, '_EventStartDate', $original_meta_value );
				}
			}

			$i ++;
		}

		delete_post_meta( $event_id, '_EventStartDate' );
		update_post_meta( $event_id, '_EventStartDate', $original );
	}

	/**
	 * Converts the start date and duration to the format expected by the sequence processor.
	 *
	 * @param string $start_date A `strtotime` parseable date
	 * @param int    $duration   The event duration in seconds
	 *
	 * @return array
	 */
	protected function build_sequence_entry( $start_date, $duration ) {
		return array(
			'timestamp' => strtotime( $start_date ),
			'duration'  => $duration,
		);
	}
}
