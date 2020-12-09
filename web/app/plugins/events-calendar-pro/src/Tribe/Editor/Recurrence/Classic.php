<?php

/**
 * Class Tribe__Events__Pro__Editor__Recurrence__Blocks
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Recurrence__Classic
	implements Tribe__Events__Pro__Editor__Recurrence__Parser_Interface {

	/**
	 * Reference to the original fields of the request
	 *
	 * @since 4.5
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Default data structure
	 *
	 * @since 4.5
	 *
	 * @var array
	 */
	protected $data = array(
		'type'   => '',
		'custom' => array(),
	);

	/**
	 * Map frmo Gutenberg types into Old types of Recurrence Events
	 *
	 * @since 4.5
	 *
	 * @var array
	 */
	protected $types = array(
		'single'  => Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE,
		'daily'   => Tribe__Events__Pro__Recurrence__Custom_Types::DAILY_CUSTOM_TYPE,
		'weekly'  => Tribe__Events__Pro__Recurrence__Custom_Types::WEEKLY_CUSTOM_TYPE,
		'monthly' => Tribe__Events__Pro__Recurrence__Custom_Types::MONTHLY_CUSTOM_TYPE,
		'yearly'  => Tribe__Events__Pro__Recurrence__Custom_Types::YEARLY_CUSTOM_TYPE,
	);

	/**
	 * Span day and the value of each
	 *
	 * @since 4.5
	 *
	 * @var array
	 */
	protected $day_span = array(
		'same_day'    => 0,
		'next_day'    => 1,
		'second_day'  => 2,
		'third_day'   => 3,
		'fourth_day'  => 4,
		'fifth_day'   => 5,
		'sixth_day'   => 6,
		'seventh_day' => 7,
	);

	/**
	 * Array to map back types from BLocks into Recurrence format
	 *
	 * @since 4.5
	 *
	 * @var array
	 */
	protected $limit_types = array(
		'count' => 'After',
		'date'  => 'On',
		'never' => 'Never',
	);

	/**
	 * Tribe__Events__Pro__Editor__Recurrence__Classic constructor.
	 *
	 * @since 4.5
	 *
	 * @param array $fields
	 */
	public function __construct( $fields = array() ) {
		$this->fields = $fields;
	}

	/**
	 * Setup values into the data variable
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function parse() {

		if ( empty( $this->fields ) || ! $this->has_valid_fields() ) {
			return false;
		}

		$this->set_type( $this->fields['type'] );
		$this->set_custom_args();
		$this->maybe_set_interval();
		$this->maybe_set_limit_type();
		$this->maybe_set_limit();

		return true;
	}

	/**
	 * Check for required field values
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function has_valid_fields() {
		return isset(
			$this->fields['type'],
			$this->fields['start_time'],
			$this->fields['end_time'],
			$this->fields['all_day'],
			$this->fields['multi_day']
		);
	}

	/**
	 * Set the type of the recurrent rule
	 *
	 * @since 4.5
	 *
	 * @param string $type
	 *
	 * @return mixed|void
	 */
	public function set_type( $type = '' ) {
		if ( isset( $this->types[ $type ] ) ) {
			$this->data['type'] = $this->types[ $type ];
		}
	}

	/**
	 * Set the custom arguments / default per recurrence
	 *
	 * @since 4.5
	 */
	protected function set_custom_args() {
		$this->data['custom'] = array(
			'same-time'  => 'no',
			'start-time' => $this->fields['start_time'],
			'end-time'   => $this->fields['end_time'],
		);

		if ( $this->fields['multi_day'] && isset( $this->fields['multi_day_span'] ) ) {
			$this->data['custom']['end-day'] = $this->day_span[ $this->fields['multi_day_span'] ];
		} else {
			$this->data['custom']['end-day'] = $this->day_span['same_day'];
		}
		$this->set_custom_args_per_type();
	}

	/**
	 * Set the between value that sets the inverval of events
	 *
	 * @since 4.5
	 */
	protected function maybe_set_interval() {
		if ( ! isset( $this->fields['between'] ) ) {
			return;
		}
		$this->data['custom']['interval'] = strval( $this->fields['between'] );
	}

	/**
	 * Set the end-type only for events that are not custom
	 *
	 * @since 4.5
	 */
	protected function maybe_set_limit_type() {
		if (
			! isset( $this->fields['limit_type'] )
			|| $this->data['type'] === Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE
		) {
			return;
		}

		$type                   = $this->fields['limit_type'];
		$this->data['end-type'] = isset( $this->limit_types[ $type ] )
			? $this->limit_types[ $type ]
			: $this->limit_types['never'];
	}

	/**
	 * Set limit values only when the end type is:
	 *
	 * - After / After a specific amount of events
	 * - On / On a specific date
	 *
	 * @since 4.5
	 */
	public function maybe_set_limit() {
		if ( ! isset( $this->fields['limit'], $this->data['end-type'] ) ) {
			return;
		}

		switch ( $this->data['end-type'] ) {
			case $this->limit_types['date']:
				$this->data['end'] = $this->fields['limit'];
				break;
			case $this->limit_types['count']:
				$this->data['end-count'] = $this->fields['limit'];
				break;
		}
	}

	/**
	 * Set custom arguments based on the type of recurrence rule.
	 *
	 * @since 4.5
	 */
	protected function set_custom_args_per_type() {
		switch ( $this->data['type'] ) {
			case Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE:
				if ( isset( $this->fields['start_date'] ) ) {
					$this->data['custom']['date'] = array(
						'date' => $this->fields['start_date'],
					);
				}
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::WEEKLY_CUSTOM_TYPE:
				$this->data['custom']['week'] = array(
					'day' => isset( $this->fields['days'] )
						? array_map( 'strval', $this->fields['days'] )
						: array(),
				);
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::MONTHLY_CUSTOM_TYPE:
				$this->data['custom']['month'] = $this->add_week();
				break;
			case Tribe__Events__Pro__Recurrence__Custom_Types::YEARLY_CUSTOM_TYPE:
				$this->data['custom']['year'] = $this->add_months();
				break;
		}
	}


	/**
	 * Convert the week and day values into corresponding values
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	protected function add_week() {
		$fields = array();

		if ( ! isset( $this->fields['day'] ) ) {
			return $fields;
		}

		$fields = array(
			'same-day' => 'no',
			'number'   => $this->fields['day'],
		);

		if ( isset( $this->fields['week'] ) && $this->fields['week'] !== null ) {
			$fields['number'] = ucfirst( $this->fields['week'] );
			$fields['day']    = $this->fields['day'];
		}

		return $fields;
	}

	/**
	 * Set the structure for the month type
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function add_months() {
		$months = isset( $this->fields['month'] ) ? $this->fields['month'] : array();

		return array_merge(
			array( 'month' => $months ),
			$this->add_week()
		);
	}

	/**
	 * Return the parsed data
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_parsed() {
		return $this->data;
	}
}
