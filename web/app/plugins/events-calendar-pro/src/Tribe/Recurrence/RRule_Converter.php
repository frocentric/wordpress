<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Pro__Recurrence__RRule_Converter' ) ) {
	return;
}

/**
 * Tribe Events Pro rrule to recurrence meta converter
 *
 * Turns a iCal RRULE into a Tribe Events recurrence meta array
 *
 * @todo revise and improve compatibility with recent Events Calendar PRO builds as the currently
 *       generated meta array may not be parsed correctly
 *
 * @param string $rrule
 *
 * @param array $event - event array
 *
 * @example FREQ
 * 		* DAILY
 * 		* WEEKLY
 * 		* MONTHLY
 * 		* YEARLY
 * 		* HOURLY
 * 		* MINUTELY
 *
 * @example COUNT - number of events
 *
 * @example UNTIL - iCal formatted ending date
 *
 * @example BYDAY - comma separated
 * 		* SU,MO,TU,WE,TH,FR,SA - WEEKLY
 * 		* 1FR,-1FR - MONTHLY (first and last friday of the month)
 *
 * @example BYMONTHDAY
 * 		* 2,-2 - MONTHLY comma separated ( second and second to last day of month )
 *
 * @example BYYEARDAY
 * 		* 1,90 - YEARLY comma separated days of the year
 *
 * @example BYMONTH
 * 		* 6,7 - YEARLY comma separated months
 *
 * @example BYWEEKNO
 * 		* 20 - YEARLY commas separated week of the year
 *
 * @example INTERVAL
 * 		* 2 - every other ( Month or week )
 *
 * @example BYSETPOS
 * 		* -2 - MONTHLY position of day of the month ( Second to last day )
 *
 *
 *
 * @uses $meta = new Tribe__Events__Pro__Recurrence__RRule_Converter( $rrule );
 * 	* $meta->get_meta()
 * @package Tribe__Events__Pro__Main
 *
 *
 */
class Tribe__Events__Pro__Recurrence__Rrule_Converter {

	/**
	 * Recurrence Meta
	 *
	 * Holds the meta as we build it
	 *
	 * 'type' => null, // string - None, Daily, Weekly, Monthly, Yearly, Custom
		 * 'end-type' => null, // string - On, After, Never
		 * 'end' => null, // string - YYYY-MM-DD - If end-type is On, recurrence ends on this date
		 * 'end-count' => null, // int - If end-type is After, recurrence ends after this many instances
		 * 'custom-type' => null, // string - Daily, Weekly, Monthly, Yearly - only used if type is Custom
		 * 'custom-interval' => null, // int - If type is Custom, the interval between custom-type units
		 * 'custom-type-text' => null, // string - Display value for admin
		 * 'occurrence-count-text' => null, // string - Display value for admin
		 * 'recurrence-description' => null, // string - Custom description for the recurrence pattern
		 * 'custom-week-day' => null, // int[] - 1 = Monday, 7 = Sunday, days when type is Custom
		 * 'custom-month-number' => null, // string|int - 1-31, First-Fifth, or Last
		 * 'custom-month-day' => null, // int - 1 = Monday, 7 = Sunday
		 * 'custom-year-month' => array(), // int[] - 1 = January
	 *
	 * ** These 3 must work together
		 * 'custom-year-filter' => null, // int - 1 or 0 = if using a custom-year-month-number or custom-year-month-day
		 * 'custom-year-month-number' => null, // int - 1-4 = week of the month
		 * 'custom-year-month-day' => null // int - 1 = Monday, 7 = Sunday
	 *
	 *
	 * @var array
	 */
	private $recurrence_meta = array();

	/**
	 * Retrieved
	 *
	 * So we don't parse the meta for every event in a series
	 *
	 * @var array( uid => meta )
	 */
	private static $retrieved = array();

	/**
	 * Args
	 *
	 * The parse ical rrule into an array
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Days
	 *
	 * Holds codes for weekdays
	 *
	 *
	 */
	private $days = array(
		'MO'   => 1,
		'TU'   => 2,
		'WE'   => 3,
		'TH'   => 4,
		'FR'   => 5,
		'SA'   => 6,
		'SU'   => 7,
	);

	/**
	 * Constructor
	 *
	 * The only method you should interact with
	 *
	 * @param string $rule - iCal formatted rule
	 *
	 * @param array $event - the event array
	 *
	 * @uses $this->recurrence_meta;
	 *
	 */
	public function __construct( $rule, $event ) {

		//if we already have what we need, set it and send it back
		if ( ! empty( $event[ 'parent_uid' ] ) ) {
			if ( ! empty( self::$retrieved[ $event[ 'parent_uid' ] ] ) ) {
				$this->recurrence_meta = self::$retrieved[ $event[ 'parent_uid' ] ];
				return;
			}
		} else {
			if ( ! empty( self::$retrieved[ $event[ 'uid' ] ] ) ) {
				$this->recurrence_meta = self::$retrieved[ $event[ 'uid' ] ];
				return;
			}
		}

		$this->args = wp_parse_args( str_replace( ';', '&', $rule ) );

		//parse the meta
		$this->freq = strtolower( $this->args[ 'FREQ' ] );
		if ( method_exists( $this, $this->freq ) ) {
			$this->{$this->freq}();
		} else {
			$this->recurrence_meta = new WP_Error( 1, 'No method exists to parse the event frequency', __CLASS__ );
		}

		//cache
		if ( ! empty( $event[ 'parent_uid' ] ) ) {
			self::$retrieved[ $event[ 'parent_uid' ] ] = $this->recurrence_meta;
		} else {
			self::$retrieved[ $event[ 'uid' ] ] = $this->recurrence_meta;
		}

	}

	/**
	 * Get Meta
	 *
	 * Get the parsed meta
	 *
	 * @return array Tribe Event Recurrence Formatted Meta Array
	 */
	public function get_meta() {
		return $this->recurrence_meta;
	}

	/**
	 * Daily
	 *
	 * Parse a daily event
	 *
	 * @return void
	 */
	private function daily() {
		if ( empty( $this->args['INTERVAL'] ) ) {
			$this->recurrence_meta[ 'type' ] = 'Daily';
			$this->end_type( );
			return;
		}

		$this->recurrence_meta[ 'custom-type' ] = 'Daily';
		$this->recurrence_meta[ 'custom-type-text' ] = 'Daily';

		$this->end_type();
		$this->interval();
	}

	/**
	 * Weekly
	 *
	 * Parse a weekly event
	 *
	 * @return void
	 */
	private function weekly() {
		if ( empty( $this->args['BYDAY'] ) && empty( $this->args['INTERVAL'] ) ) {
			$this->recurrence_meta[ 'type' ] = 'Weekly';
			$this->end_type( );
			return;
		}

		$this->recurrence_meta[ 'custom-type' ] = 'Weekly';
		$this->recurrence_meta[ 'custom-type-text' ] = 'Weekly';

		$this->end_type();
		$this->interval();
		$this->by_day();
	}

	/**
	 * Montly
	 *
	 * Parse a montly event
	 *
	 * @return void
	 */
	private function monthly() {
		if ( empty( $this->args['BYDAY'] )
			&& empty( $this->args['INTERVAL'] )
			&& empty( $this->args['BYSETPOS'] )
			&& empty( $this->args['BYMONTHDAY'] ) ) {

			$this->recurrence_meta[ 'type' ] = 'Monthly';
			$this->end_type();
			return;
		}

		$this->recurrence_meta[ 'custom-type' ] = 'Monthly';
		$this->recurrence_meta[ 'custom-type-text' ] = 'Monthly';

		if ( ! empty( $this->args['BYMONTHDAY'] ) ) {
			$parts = explode( ',', $this->args['BYMONTHDAY'] );
			$num = array_shift( $parts );
			$this->recurrence_meta[ 'custom-month-number' ] = $num;
		} elseif ( ! empty( $this->args['BYSETPOS'] ) ) {
			$parts = explode( ',', $this->args['BYSETPOS'] );
			$num = array_shift( $parts );
			$this->recurrence_meta[ 'custom-month-number' ] = $num;
		}

		$this->end_type();
		$this->interval();
		$this->by_day();
	}

	/**
	 * Yearly
	 *
	 * Parse a yearly event
	 *
	 * @return void
	 */
	private function yearly() {
		if ( empty( $this->args['BYDAY'] )
			&& empty( $this->args['INTERVAL'] )
			&& empty( $this->args['BYSETPOS'] )
			&& empty( $this->args['BYMONTHDAY'] )
			&& empty( $this->args['BYYEARDAY'] )
			&& empty( $this->args['BYWEEKNO'] ) ) {

			$this->recurrence_meta[ 'type' ] = 'Yearly';
			$this->end_type();
			return;
		}

		$this->recurrence_meta[ 'custom-type' ] = 'Yearly';
		$this->recurrence_meta[ 'custom-type-text' ] = 'Yearly';

		if ( ! empty( $this->args['BYMONTH'] ) ) {
			$months = explode( ',', $this->args['BYMONTH'] );
			foreach ( $months as $month ) {
				$this->recurrence_meta[ 'custom-year-month' ][] = (int) $month;
			}
		}

		if ( ! empty( $this->args['BYMONTHDAY'] ) ) {
			$num = array_shift( explode( ',', $this->args['BYMONTHDAY'] ) );
			$this->recurrence_meta[ 'custom-month-number' ] = $num;
		} elseif ( ! empty( $this->args['BYSETPOS'] ) ) {
			$num = array_shift( explode( ',', $this->args['BYSETPOS'] ) );
			$this->recurrence_meta[ 'custom-month-number' ] = $num;
		}

		if ( ! empty( $this->args['BYDAY'] ) ) {
			$days = explode( ',', $this->args['BYDAY'] );
			$this->recurrence_meta['custom-month-day'] = array_shift( $days );
		}

		$this->end_type();
		$this->interval();
	}

	/**
	 * End Type
	 *
	 * Parse the basic end types
	 * This may only be used when not doing a custom type
	 *
	 * @return void
	 */
	private function end_type() {

		if ( ! empty( $this->args[ 'UNTIL' ] ) ) {
			$this->recurrence_meta[ 'end-type' ] = 'On';

			$date = iCalPropertyFactory::strdate2arr( $this->args[ 'UNTIL' ] );
			$this->recurrence_meta[ 'end' ] = $date['year'].'-'.$date['month'].'-'.$date['day'];


		} elseif ( ! empty( $this->args[ 'COUNT' ] ) ) {
			$this->recurrence_meta[ 'end-type' ] = 'After';
			$this->recurrence_meta[ 'end-count' ] = (int) $this->args[ 'COUNT' ];
		} else {
			$this->recurrence_meta[ 'end-type' ] = 'On';
			$this->recurrence_meta[ 'end' ] = date( 'Y-m-d', strtotime( '+2 Years' ) );
		}
	}

	/**
	 * Interval
	 *
	 * Parse the interval argument
	 *
	 * @return void
	 */
	private function interval() {
		$this->recurrence_meta[ 'type' ] = 'Custom';

		if ( ! empty( $this->args[ 'INTERVAL' ] ) ) {
			$this->recurrence_meta[ 'custom-interval' ] = (int) $this->args[ 'INTERVAL' ];
		}
	}

	/**
	 * By Day
	 *
	 * Parse the by day arguments
	 *
	 * @return void
	 */
	private function by_day() {
		if ( empty( $this->args[ 'BYDAY' ] ) )
			return;

		$days = explode( ',', $this->args[ 'BYDAY' ] );

		$type = str_replace( 'ly', '', $this->freq );

		foreach ( $days as $day ) {
			$this->recurrence_meta[ 'custom-'.$type.'-day' ][ ] = $this->days[ $day ];
		}
	}
}
