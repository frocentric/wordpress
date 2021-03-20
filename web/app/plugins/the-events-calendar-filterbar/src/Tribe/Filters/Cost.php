<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Cost
 */
class Tribe__Events__Filterbar__Filters__Cost extends Tribe__Events__Filterbar__Filter {
	const EXPLICITLY_FREE = 'set_to_0';
	const IMPLICITLY_FREE = 'unset_or_0';

	public $type = 'range';
	public $free = self::IMPLICITLY_FREE;
	private $min_cost = null;
	private $max_cost = null;


	protected function settings() {
		parent::settings();
		$this->free_logic();
	}

	protected function free_logic() {
		$settings = Tribe__Events__Filterbar__View::instance()->get_filter_settings();
		$this->free = isset( $settings[ $this->slug ]['free'] ) && self::EXPLICITLY_FREE === $settings[ $this->slug ]['free']
			? self::EXPLICITLY_FREE : self::IMPLICITLY_FREE;
	}

	/**
	 * Returns the Filter currently submitted value, as read from the request arguments.
	 *
	 * @since 5.0.0.1 Changed the method visibility to `public`.
	 *
	 * @return array<mixed>|mixed|null The submitted value for the Filter, `null` if not submitted.
	 */
	public function get_submitted_value() {
		if ( ! empty( $_REQUEST[ 'tribe_' . $this->slug ] ) ) {
			$value = (array) $_REQUEST[ 'tribe_' . $this->slug ];

			if ( isset( $value['min'] ) && isset( $value['max'] ) ) {
				return array( $value );
			} else {
				foreach ( $value as &$v ) {
					$range = explode( '-', $v );
					if ( ! preg_match( '/[0-9]+\-[0-9]+/', $v ) ) {
						continue;
					}
					$v = array( 'min' => $range[0], 'max' => $range[1] );
				}
				return $value;
			}
		}
		return array();
	}

	public function get_admin_form() {
		$title = $this->get_title_field();
		$type = $this->get_type_field();
		return $title.$type;
	}

	protected function get_type_field() {
		$name = $this->get_admin_field_name( 'type' );
		$type_field = sprintf( __( 'Type: %s %s', 'tribe-events-filter-view' ),
			sprintf( '<label><input type="radio" name="%s" value="range" %s /> %s</label>',
				$name,
				checked( $this->type, 'range', false ),
				__( 'Range Slider', 'tribe-events-filter-view' )
			),
			sprintf( '<label><input type="radio" name="%s" value="checkbox" %s /> %s</label>',
				$name,
				checked( $this->type, 'checkbox', false ),
				__( 'Checkboxes', 'tribe-events-filter-view' )
			)
		);

		$name = $this->get_admin_field_name( 'free' );
		$free_string = $this->get_free_string();
		$free_identifier_title_text = sprintf(
			esc_html__( 'Used as the identifier for free %s', 'tribe-events-filter-view' ),
			tribe_get_event_label_plural()
		);

		$empty_or_zero_label = sprintf(
			__( '"%s" or empty or set to zero', 'tribe-events-filter-view' ),
			"<strong><abbr title='{$free_identifier_title_text}'>{$free_string}</abbr></strong>"
		);

		$empty_or_zero_input = sprintf(
			'<label><input type="radio" name="%s" value="unset_or_0" %s /> %s</label>',
			$name,
			checked( $this->free, 'unset_or_0', false ),
			$empty_or_zero_label
		);

		$only_when_zero = sprintf(
			'<label><input type="radio" name="%s" value="set_to_0" %s /> %s</label>',
			$name,
			checked( $this->free, 'set_to_0', false ),
			__( 'Only when set to zero', 'tribe-events-filter-view' )
		);

		$cost_field = sprintf(
			__( '%s are considered free when cost field is: %s %s', 'tribe-events-filter-view' ),
			tribe_get_event_label_plural(),
			$empty_or_zero_input,
			$only_when_zero
		);

		return '<div class="tribe_events_active_filter_type_options">' . $type_field . $cost_field . '</div>';
	}

	protected function get_free_string() {
		return _x( 'Free', 'Used as the identifier for free Events', 'tribe-events-filter-view' );
	}

	protected function get_values() {
		$this->set_min_and_max();

		if ( $this->type == 'range' ) {
			return array( 'min' => $this->min_cost, 'max' => $this->max_cost );
		}

		$cost_range = array();
		if ( $this->has_non_numeric_costs() ) {
			$cost_range['other'] = __( 'Other', 'tribe-events-filter-view' );
		}

		if ( $this->min_cost == 0 ) {
			$cost_range['0-0'] = __( 'Free', 'tribe-events-filter-view' );
		}
		if ( $this->max_cost == $this->min_cost ) {
			if ( $this->max_cost != 0 ) {
				$cost_range[ $this->min_cost . '-' . $this->max_cost ] = $this->min_cost . '-' . $this->max_cost;
			}
		} else { // break the range into five equal groups
			$cost_chunks = $this->partition_range( floor( $this->min_cost ), floor( $this->max_cost ), ( 5 - count( $cost_range ) ) );
			foreach ( $cost_chunks as &$chunk ) {
				$cost_range[ $chunk['min'] . '-' . $chunk['max'] ] = $chunk['min'] . '-' . $chunk['max'];
			}
		}
		$values = array();
		foreach ( $cost_range as $key => $cost ) {
			$values[] = array(
				'name' => $cost,
				'value' => $key,
			);
		}
		return $values;
	}

	private function partition_range( $min, $max, $count ) {
		$range_size = $max - $min + 1;
		$partition_size = floor( $range_size / $count );
		$partition_remainder = $range_size % $count;
		$partitioned = array();
		$mark = $min;
		for ( $i = 0; $i < $count; $i++ ) {
			$incr = ( $i < $partition_remainder ) ? $partition_size : $partition_size - 1;
			$partitioned[ $i ] = array(
				'min' => $mark,
				'max' => $mark + $incr,
			);
			$mark += $incr + 1;
		}
		return $partitioned;
	}

	protected function is_selected( $option ) {
		if ( preg_match( '/[0-9]*\-[0-9]*/', $option ) ) {
			$option = explode( '-', $option );
			$option = array(
				'min' => $option[0],
				'max' => $option[1],
			);
		} elseif ( in_array( 'other', $this->currentValue ) ) {
			return true;
		}

		return in_array( (array) $option, $this->currentValue );
	}

	protected function setup_query_filters() {
		if ( $this->currentValue ) {
			$this->set_min_and_max();
		}
		parent::setup_query_filters();
	}

	protected function setup_join_clause() {
		global $wpdb;
		$this->joinClause = " INNER JOIN {$wpdb->postmeta} AS cost_filter ON ({$wpdb->posts}.ID = cost_filter.post_id)";
	}

	protected function setup_where_clause() {
		global $wpdb;
		$clauses = array();

		foreach ( $this->currentValue as $value ) {
			$free_clause = '';
			if ( isset( $value['min'] ) ) {
				// Should we exclude events where a cost has not been provided?
				$free_clause = $this->free_clause( $value['min'] );
			}

			if ( 'other' === $value ) {
				$length_clause = null;
				if ( self::IMPLICITLY_FREE === $this->free ) {
					$length_clause = 'AND LENGTH( TRIM( cost_filter.meta_value ) ) > 0';
				}

				$clause = "
					( cost_filter.meta_key = '_EventCost'
					$length_clause
					AND CAST( cost_filter.meta_value AS SIGNED ) = 0
					AND cost_filter.meta_value != '0'
				";

				if ( self::EXPLICITLY_FREE !== $this->free ) {
					$clause .= $wpdb->prepare( ' AND LOWER(cost_filter.meta_value) != %s', strtolower( $this->get_free_string() ) );
				}

				// Close the Conditional
				$clause .= ')';

				$clauses[] = $clause;
			} elseif ( isset( $value['min'], $value['max'] ) && $value['min'] == 0 && $value['max'] == 0 ) {

				$blank_clause = null;
				if ( self::IMPLICITLY_FREE === $this->free ) {
					$blank_clause = "
						OR cost_filter.meta_value = ''
						OR cost_filter.meta_value IS NULL
						OR $free_clause
					";
				}

				$clauses[] = "
					(
						cost_filter.meta_key = '_EventCost'
						AND (
							cost_filter.meta_value = '0'
							$blank_clause
						)
					)
				";
			} else {
				$clauses[] = $wpdb->prepare(
					"(
						cost_filter.meta_key = '_EventCost'
						AND cost_filter.meta_value >= %d
						AND cost_filter.meta_value IS NOT NULL
						AND CAST(cost_filter.meta_value AS SIGNED) BETWEEN %d AND %d
					) ",
					$value['min'],
					$value['min'],
					$value['max']
				);
			}
		}

		$this->whereClause = ' AND (' . implode( ' OR ', $clauses ) . ') ';
	}

	protected function free_clause( $min ) {
		global $wpdb;

		if ( 0 !== (int) $min ) {
			return 'LENGTH( TRIM( cost_filter.meta_value ) ) > 0 AND CAST( cost_filter.meta_value AS SIGNED ) > 0';
		}

		return $wpdb->prepare(
			'(
				LENGTH( TRIM( cost_filter.meta_value ) ) > 0
				AND CAST( cost_filter.meta_value AS SIGNED ) = 0
				AND LOWER( cost_filter.meta_value ) = %s
			)',
			strtolower( $this->get_free_string() )
		);
	}

	private function set_min_and_max() {
		if ( ! isset( $this->max_cost ) || ! isset( $this->min_cost ) ) {
			$this->max_cost = tribe_get_maximum_cost();
			$this->min_cost = tribe_has_uncosted_events() ? 0 : tribe_get_minimum_cost();
		}
	}

	private function has_non_numeric_costs() {
		$costs = tribe( 'tec.cost-utils' )->get_all_costs();
		foreach ( $costs as $index => $cost ) {
			if ( is_numeric( $index ) ) {
				unset( $costs[ $index ] );
			}
		}
		return ! empty( $costs );
	}
}
