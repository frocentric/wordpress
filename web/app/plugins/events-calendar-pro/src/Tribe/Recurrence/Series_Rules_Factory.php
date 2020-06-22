<?php


class Tribe__Events__Pro__Recurrence__Series_Rules_Factory {

	/**
	 * @var Tribe__Events__Pro__Recurrence__Series_Rules_Factory
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Series_Rules_Factory
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		tribe_register_error(
			'pro:recurrence:invalid-meta-rules',
			__( "Expected an array but received a variable of type '%s'", 'tribe-events-calendar-pro' ) )
		;
	}

	/**
	 * Builds and returns the date series rule needed to find the next occurences of the event.
	 *
	 * @param array $recurrence An event recurrence meta entry.
	 * @param string $rule_type The rule type, defaults to `rules`.
	 *
	 * @return Tribe__Events__Pro__Date_Series_Rules__Rules_Interface|WP_Error A date series rule instance or a WP_Error on fail.
	 */
	public function build_from( $recurrence, $rule_type = 'rules' ) {
		if ( ! is_array( $recurrence ) ) {
			return tribe_error( 'pro:recurrence:invalid-meta-rules', array(), array( gettype( $recurrence ) ) );
		}

		if ( 'exclusions' === $rule_type ) {
			$recurrence['type'] = Tribe__Events__Pro__Recurrence__Custom_Types::CUSTOM_TYPE;
		}

		$recurrence['rule_type'] = $rule_type;

		$rule = null;

		if ( Tribe__Events__Pro__Recurrence__Custom_Types::CUSTOM_TYPE === $recurrence['type'] && ! isset( $recurrence['custom']['interval'] ) ) {
			$recurrence['custom']['interval'] = 1;
		}

		$type = $this->get_recurrence_type( $recurrence );

		return $this->build_rule_for_type( $type, $recurrence );
	}

	/**
	 * Convert an ordinal from an ECP recurrence series into an integer
	 *
	 * @param string $ordinal The ordinal number
	 *
	 * @return An integer representation of the ordinal
	 */
	private static function ordinalToInt( $ordinal ) {
		switch ( $ordinal ) {
			case 'First':
				return 1;
			case 'Second':
				return 2;
			case 'Third':
				return 3;
			case 'Fourth':
				return 4;
			case 'Fifth':
				return 5;
			case 'Last':
				return - 1;
			default:
				return null;
		}
	}

	/**
	 * Convert an int to an ordinal from an ECP recurrence series
	 *
	 * @param string $int The int to convert to an ordinal number
	 *
	 * @return An ordinal representation of the int
	 */
	public static function intToOrdinal( $int ) {
		switch ( $int ) {
			case 1:
				return 'First';
			case 2:
				return 'Second';
			case 3:
				return 'Third';
			case 4:
				return 'Fourth';
			case 5:
				return 'Fifth';
			case -1:
				return 'Last';
			default:
				return null;
		}
	}

	private function get_recurrence_type( array $recurrence ) {
		$invalid_type = empty( $recurrence['type'] );

		if ( $invalid_type ) {
			return 'invalid';
		}

		$no_custom_type = empty( $recurrence['custom']['type'] );
		$valid_type     = in_array(
			$recurrence['type'], Tribe__Events__Pro__Recurrence__Custom_Types::get_legit_recurrence_types()
		);
		if ( $no_custom_type && $valid_type ) {
			return $recurrence['type'];
		}
		$is_valid_custom_type = $recurrence['type'] == Tribe__Events__Pro__Recurrence__Custom_Types::CUSTOM_TYPE && in_array(
				$recurrence['custom']['type'], Tribe__Events__Pro__Recurrence__Custom_Types::get_legit_custom_types()
			);
		if ( $is_valid_custom_type ) {
			return $recurrence['custom']['type'];
		}

		return 'invalid';
	}

	/**
	 * @param string $type
	 * @param array  $recurrence
	 *
	 * @return Tribe__Events__Pro__Date_Series_Rules__Date|Tribe__Events__Pro__Date_Series_Rules__Day|Tribe__Events__Pro__Date_Series_Rules__Month|Tribe__Events__Pro__Date_Series_Rules__Week|WP_Error
	 */
	private function build_rule_for_type( $type = Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE, array $recurrence = array() ) {
		switch ( $type ) {
			case Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE:
				$is_same_time = tribe_is_truthy( Tribe__Utils__Array::get( $recurrence, array( 'custom', 'same-time' ), 'no' ) );
				$date_start = Tribe__Utils__Array::get( $recurrence, 'EventStartDate', 'now' );

				if ( 'exclusions' === $recurrence['rule_type'] ) {
					$date = empty( $recurrence['end'] ) ? Tribe__Utils__Array::get( $recurrence, array( 'custom', 'date', 'date' ) ) : $recurrence['end'];
				} else {
					$date = Tribe__Utils__Array::get( $recurrence, array( 'custom', 'date', 'date' ) );
					if ( $is_same_time ) {
						$date .= ' ' . Tribe__Date_Utils::time_only( $date_start );
					} else {
						$date .= ' ' . Tribe__Utils__Array::get( $recurrence, array( 'custom', 'start-time' ) );
					}

					// Clean Date to check for empty
					$date = trim( $date );

					if ( empty( $date ) ) {
						$date = Tribe__Utils__Array::get( $recurrence, array( 'end' ), $date_start );
					}
				}

				$rule = new Tribe__Events__Pro__Date_Series_Rules__Date( strtotime( $date ) );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::EVERY_DAY_TYPE:
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Day( 1 );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::DAILY_CUSTOM_TYPE:
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Day( $recurrence['custom']['interval'] );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::EVERY_WEEK_TYPE:
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Week( 1 );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::WEEKLY_CUSTOM_TYPE:
				$days = empty( $recurrence['custom']['week']['day'] ) ? array() : $recurrence['custom']['week']['day'];
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Week(
					$recurrence['custom']['interval'], $days
				);
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::EVERY_MONTH_TYPE:
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Month( 1 );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::MONTHLY_CUSTOM_TYPE:

				$custom_monthly_interval = (int) empty( $recurrence['custom']['interval'] ) ? 1 : $recurrence['custom']['interval'];

				// These values are often empty if the monthly-type event is recurring on the same day.
				if (
					empty( $recurrence['custom']['month']['number'] )
					&& empty( $recurrence['custom']['month']['day'] )
				) {
					$rule = new Tribe__Events__Pro__Date_Series_Rules__Month( $custom_monthly_interval );
				} else {
					$day_of_month = null;

					if (
						isset( $recurrence['custom']['month']['number'] )
						&& is_numeric( $recurrence['custom']['month']['number'] )
					) {
						$day_of_month = array( $recurrence['custom']['month']['number'] );
					}

					$month_number = self::ordinalToInt( $recurrence['custom']['month']['number'] );
					$rule         = new Tribe__Events__Pro__Date_Series_Rules__Month(
						$custom_monthly_interval,
						$day_of_month,
						$month_number,
						Tribe__Utils__Array::get( $recurrence, array( 'custom', 'month', 'day' ) )
					);
				}
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::EVERY_YEAR_TYPE:
				$rule = new Tribe__Events__Pro__Date_Series_Rules__Year( 1 );
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::YEARLY_CUSTOM_TYPE:
				$year_number  = null;
				$day_of_month = null;

				if ( isset( $recurrence['custom']['year']['number'] ) ) {
					if ( is_numeric( $recurrence['custom']['year']['number'] ) ) {
						$day_of_month = $recurrence['custom']['year']['number'];
					} else {
						$year_number = self::ordinalToInt( $recurrence['custom']['year']['number'] );
					}
				}

				// fetches the Months, and defaults back to current month
				$months = Tribe__Utils__Array::get( $recurrence, [ 'custom', 'year', 'month' ], date( 'n' ) );
				if ( is_string( $months ) ) {
					$months =  explode( ',', $months );
				}
				$months = array_map( 'intval', $months );

				$rule = new Tribe__Events__Pro__Date_Series_Rules__Year(
					$recurrence['custom']['interval'],
					$months,
					isset( $recurrence['custom']['year']['number'] ) ? self::ordinalToInt( $recurrence['custom']['year']['number'] ) : null,
					Tribe__Utils__Array::get( $recurrence, array( 'custom', 'year', 'day' ) ),
					$day_of_month
				);
				break;
			default:
				$data = json_encode( $recurrence );
				$rule = new WP_Error(
					'invalid-recurrence-data', "A recurrence series rule could not be built using the data '{$data}'"
				);
				break;
		}

		return $rule;
	}

}
