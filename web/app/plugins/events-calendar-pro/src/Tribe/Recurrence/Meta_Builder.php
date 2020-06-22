<?php


class Tribe__Events__Pro__Recurrence__Meta_Builder {

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @var int An event type post ID
	 */
	protected $event_id;
	/**
	 * @var Tribe__Events__Pro__Recurrence__Utils
	 */
	protected $utils;

	/**
	 * Tribe__Events__Pro__Recurrence__Meta_Builder constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $event_id, array $data = array(), Tribe__Events__Pro__Recurrence__Utils $utils = null ) {
		$this->event_id = $event_id;
		$this->data = $data;
		$this->utils = $utils ? $utils : new Tribe__Events__Pro__Recurrence__Utils();
	}

	public function build_meta() {
		if ( empty( $this->data ) || empty( $this->data['recurrence'] ) || ! is_array( $this->data['recurrence'] ) ) {
			return $this->get_zero_array();
		}

		$recurrence_meta = $this->get_zero_array();
		$custom_types = array(
			Tribe__Events__Pro__Recurrence__Custom_Types::DAILY_CUSTOM_TYPE,
			Tribe__Events__Pro__Recurrence__Custom_Types::WEEKLY_CUSTOM_TYPE,
			Tribe__Events__Pro__Recurrence__Custom_Types::MONTHLY_CUSTOM_TYPE,
			Tribe__Events__Pro__Recurrence__Custom_Types::YEARLY_CUSTOM_TYPE,
			Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE,
		);

		if ( isset( $this->data['recurrence']['recurrence-description'] ) ) {
			unset( $this->data['recurrence']['recurrence-description'] );
		}

		foreach ( array( 'rules', 'exclusions' ) as $rule_type ) {
			if ( ! isset( $this->data['recurrence'][ $rule_type ] ) ) {
				continue;
			}

			foreach ( $this->data['recurrence'][ $rule_type ] as $key => &$recurrence ) {
				if ( ! $recurrence ) {
					continue;
				}

				// Ignore the rule if the type isn't set OR the type is set to 'None'
				// (we're not interested in exclusions here)
				if ( empty( $recurrence['type'] ) || 'None' === $recurrence['type'] ) {
					continue;
				}

				if ( in_array( $recurrence['type'], $custom_types ) ) {
					// we now consider all non-blank types to be custom types
					$recurrence['custom']['type'] = $recurrence['type'];
					$recurrence['type'] = 'Custom';
				}

				$has_no_type = empty( $recurrence['type'] ) && empty( $recurrence['custom']['type'] );
				$is_custom_none_recurrence = 'exclusions' == $rule_type && ! empty( $recurrence['custom']['type'] ) && 'None' === $recurrence['custom']['type'];
				if ( $has_no_type || $is_custom_none_recurrence ) {
					unset( $this->data['recurrence'][ $rule_type ][ $key ] );
					continue;
				}

				if ( isset( $recurrence['custom'] ) && isset( $recurrence['custom']['type-text'] ) ) {
					unset( $recurrence['custom']['type-text'] );
				}

				unset( $recurrence['occurrence-count-text'] );

				$datepicker_format = $this->utils->datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

				if ( ! empty( $recurrence['end'] ) ) {
					$recurrence['end'] = $this->utils->datetime_from_format( $datepicker_format, $recurrence['end'] );
				}

				// if the month should use the same day of the month as the main event, then unset irrelevant fields if they exist
				if (
					isset( $recurrence['custom']['month']['same-day'] )
					&& 'yes' === $recurrence['custom']['month']['same-day']
				) {
					$remove             = array( 'number', 'day' );
					$recurrence['custom']['month'] = array_intersect_key( $recurrence['custom']['month'], array_flip( $remove ) );
				}

				if (
					isset( $recurrence['custom']['type'] )
					&& Tribe__Events__Pro__Recurrence__Custom_Types::DATE_CUSTOM_TYPE === $recurrence['custom']['type']
					&& isset( $recurrence['custom']['date']['date'] )
				) {
					$recurrence['custom']['date']['date'] = $this->utils->datetime_from_format( $datepicker_format, $recurrence['custom']['date']['date'] );
				}

				// if this isn't an exclusion and it isn't a Custom rule, then we don't need the custom array index
				if ( 'rules' === $rule_type && 'Custom' !== $recurrence['type'] ) {
					if ( isset( $recurrence['custom'] ) ) {
						unset( $recurrence['custom'] );
					}
				} else {
					$type            = $recurrence['custom']['type'];
					$type_slug       = Tribe__Events__Pro__Recurrence__Custom_Types::to_key( $type );
					$slugs_to_remove = array(
						'date',
						'day',
						'week',
						'month',
						'year',
					);
					$slugs_to_remove = array_diff( $slugs_to_remove, array( $type_slug ) );

					// clean up extraneous array elements
					$recurrence['custom'] = array_diff_key( $recurrence['custom'], array_flip( $slugs_to_remove ) );
				}

				if ( empty( $recurrence['custom']['same-time'] ) ) {
					$recurrence['custom']['same-time'] = 'yes';
				}

				if ( empty( $recurrence['custom']['interval'] ) ) {
					$recurrence['custom']['interval'] = 1;
				}

				$recurrence['EventStartDate'] = $this->data['EventStartDate'];
				$recurrence['EventEndDate']   = $this->data['EventEndDate'];

				if ( $this->utils->is_valid( $this->event_id, $recurrence ) ) {
					$recurrence_meta[ $rule_type ][] = $recurrence;
				}
			}
		}

		return $recurrence_meta;
	}

	private function get_zero_array() {
		$data_recurrence = null;
		if ( ! empty( $this->data['recurrence'] ) ) {
			$data_recurrence = (array) $this->data['recurrence'];
		}

		return array(
			'rules'       => array(),
			'exclusions'  => array(),
			'description' => empty( $data_recurrence['description'] ) ? null : sanitize_text_field( $data_recurrence['description'] ),
		);
	}
}
