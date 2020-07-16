<?php

use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;

class Tribe__Events__Pro__Recurrence__Exclusions {

	/**
	 * @var string|null
	 */
	protected $timezone_string;

	/**
	 * @param string|null $timezone_string The event timezone string if any.
	 */
	public function __construct( $timezone_string = null ) {
		$this->timezone_string = $timezone_string;
	}

	/**
	 * @var Tribe__Events__Pro__Recurrence__Exclusions
	 */
	protected static $instance;

	/**
	 * @param string|null $timezone_string The event timezone string if any.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Exclusions
	 */
	public static function instance( $timezone_string = null ) {
		if ( empty( self::$instance ) ) {
			self::$instance = new self( $timezone_string );
		}

		return self::$instance;
	}

	/**
	 * Accepts an array of $date_durations and removes any falling on the dates listed
	 * within $exclusion_dates.
	 *
	 * Both parameters are arrays of arrays, each inner array or "date duration" taking the
	 * following form:
	 *
	 *     [ 'timestamp' => int,
	 *       'duration'  => int  ]
	 *
	 * In the case of exclusions, duration will always be zero as custom exclusions do
	 * not currently support custom durations, so that element is ignored during comparison.
	 *
	 * @param array $date_durations The array of date and durations to intersect with the exclusions.
	 * @param array $exclusion_dates The exclusion dates to intersect the dates with.
	 *
	 * @return array An array of dates and durations, the result of the intersection between the original dates and
	 *               durations and the exclusions.
	 */
	public function remove_exclusions( array $date_durations, array $exclusion_dates ) {
		$timezone = Timezones::build_timezone_object( $this->get_timezone() );

		$exclusion_timestamps = [];

		$almost_one_day = DAY_IN_SECONDS - 1;

		foreach ( $exclusion_dates as $exclusion ) {
			$start = Dates::build_date_object( $exclusion['timestamp'], $timezone );

			// Reset to midnight
			$start->setTime( 0, 0 );

			$exclusion_timestamps[] = [
				'start' => $start->getTimestamp(),
				'end'   => $start->getTimestamp() + $almost_one_day,
			];
		}

		foreach ( $date_durations as $key => $date_duration ) {
			foreach ( $exclusion_timestamps as $exclusion_timestamp ) {
				$exclusion_contains_event = ( $exclusion_timestamp['start'] <= $date_duration['timestamp'] )
				                            && ( $date_duration['timestamp'] <= $exclusion_timestamp['end'] );
				if ( $exclusion_contains_event ) {
					unset( $date_durations[ $key ] );
				}
			}
		}

		$date_durations = array_values( $date_durations );

		return $date_durations;
	}

	/**
	 * Return the name of the Timezone being modified
	 *
	 * @since 4.4.26
	 *
	 * @return string
	 */
	public function get_timezone() {
		return class_exists( 'Tribe__Timezones' )
			? Tribe__Timezones::generate_timezone_string_from_utc_offset( $this->timezone_string )
			: 'UTC';
	}
}
