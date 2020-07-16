<?php
/**
 * Helper to facilitate the conversion of pre-4.4 recurrence data to the
 * structure used in 4.4 onwards.
 *
 * Note that this is only interested in non-custom rules such as daily,
 * weekly, monthly or yearly. It will not attempt to fix any custom rules
 * set up in a pre-4.4 release.
 *
 * @internal this class is not intended for use outside of Events Calendar PRO
 *           and it or its methods may be deprecated or removed with no notice
 *
 * @since 4.4.11
 */
class Tribe__Events__Pro__Recurrence__Rule_Updater {
	/**
	 * The post which the rules belong to.
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Contains the original set of recurrence rules.
	 *
	 * @var mixed
	 */
	protected $original_rules;

	/**
	 * Contains an updated version of the recurrence rules that matches
	 * the format expected by our current recurrence engine.
	 *
	 * @var array
	 */
	protected $corrected_rules = array();

	/**
	 * @param int   $post_id
	 * @param array $rules
	 */
	public function __construct( $post_id, $rules ) {
		$this->post_id = absint( $post_id );
		$this->original_rules = $rules;
	}

	/**
	 * Checks if the rules require an update and updates if required.
	 *
	 * If no update is required, or if the update cannot be performed, returns
	 * the original rules unmodified. If the update should and can be performed,
	 * returns the updated set of rules.
	 *
	 * Optional param $commit_update (defaults to true) controls whether any
	 * update is committed back to the post meta table.
	 *
	 * @param bool $commit_update
	 *
	 * @return mixed
	 */
	public function update_if_required( $commit_update = true ) {
		if ( $this->should_update() ) {
			return $this->do_update( $commit_update );
		}

		return $this->original_rules;
	}

	/**
	 * Indicates if the rules ought to be updated to match the current
	 * format.
	 *
	 * @return bool
	 */
	public function should_update() {
		// Look for the rules array (which we need to perform updates)
		$rules = Tribe__Utils__Array::get( $this->original_rules, 'rules', false );

		// If $rules is not an array or is empty, we can't work with it
		if ( ! is_array( $rules ) || empty( $rules ) ) {
			return false;
		}

		// Check each rule to see if it might need to be updated
		foreach ( $rules as $single_rule ) {
			if ( ! $this->is_post_44_custom_rule( $single_rule ) ) {
				return true;
			}
		}

		// If we reach this point, we haven't found any rules in need of an update
		return false;
	}

	/**
	 * Checks if the rule array looks like a post-4.4 rule or not.
	 *
	 * @param array $rule
	 *
	 * @return bool
	 */
	protected function is_post_44_custom_rule( array $rule ) {
		return (
			isset( $rule['custom'] )
			&& is_array( $rule['custom'] )
			&& isset( $rule['type'] )
			&& 'Custom' === $rule['type']
		);
	}

	/**
	 * Performs any update that might be required and returns the revised
	 * set of rules.
	 *
	 * By default, the update is committed back to the post meta table,
	 * however this can be turned off by passing false as the first
	 * parameter.
	 *
	 * @param bool $commit_update
	 *
	 * @return array
	 */
	public function do_update( $commit_update = true ) {
		$working_rules = $this->original_rules;

		foreach ( $working_rules['rules'] as $index => $single_rule ) {
			if ( ! $this->is_post_44_custom_rule( $single_rule ) ) {
				$working_rules['rules'][ $index ] = $this->fix_rule( $single_rule );
			}
		}

		$this->corrected_rules = $working_rules;

		if ( $commit_update ) {
			update_post_meta( $this->post_id, '_EventRecurrence', $this->corrected_rules );
		}

		return $this->corrected_rules;
	}

	/**
	 * Given a legacy recurrence rule, attempts to update it to the currently
	 * expected structure.
	 *
	 * @param array $rule
	 *
	 * @return array
	 */
	protected function fix_rule( array $rule ) {
		$original_type = $rule['type'];

		$rule['type'] = 'Custom';
		$rule['custom'] = array(
			'interval' => 1,
			'same-time' => 'yes',
			'type' => $this->convert_type( $original_type ),
		);

		$rule['custom'] = $this->add_rule_details( $rule['custom'] );
		return $rule;
	}

	/**
	 * Attempts to convert a legacy (pre-4.4) recurrence rule type to the
	 * equivalent term used in 4.4 and later.
	 *
	 * @param string $legacy_type
	 *
	 * @return string
	 */
	protected function convert_type( $legacy_type ) {
		$type_map = array(
			'Every Day'   => 'Daily',
			'Every Week'  => 'Weekly',
			'Every Month' => 'Monthly',
			'Every Year'  => 'Yearly',
		);

		$new_type = isset( $type_map[ $legacy_type ] )
			? $type_map[ $legacy_type ]
			: $legacy_type;

		/**
		 * Provides an opportunity to modify any conversion in recurrence type that is
		 * made for backwards compatibility reasons.
		 *
		 * @param string $new_type
		 * @param string $legacy_type
		 * @param int    $post_id
		 */
		return apply_filters( 'tribe_events_pro_legacy_recurrence_type_conversion', $new_type, $legacy_type, $this->post_id );
	}

	/**
	 * Adds any extra detail expected by the recurrence code according to whether
	 * the rule is weekly, monthly or yearly (daily needs no action).
	 *
	 * @param array $custom_rule
	 *
	 * @return array
	 */
	protected function add_rule_details( array $custom_rule ) {
		if ( 'Weekly' === $custom_rule['type'] ) {
			$custom_rule['week'] = array(
				'day' => array(
					date_i18n( 'N', Tribe__Events__Timezones::event_start_timestamp( $this->post_id ) ),
				),
			);
		}

		if ( 'Monthly' === $custom_rule['type'] ) {
			$custom_rule['month'] = array(
				'month' => array(),
			);
		}

		if ( 'Yearly' === $custom_rule['type'] ) {
			$custom_rule['year'] = array(
				'month' => date_i18n( 'n', Tribe__Events__Timezones::event_start_timestamp( $this->post_id ) ),
			);
		}

		return $custom_rule;
	}

	/**
	 * Returns the corrected recurrence data..
	 *
	 * Should only be called *after* the do_update() has run, directly or indirectly via
	 * update_if_required().
	 *
	 * @return array
	 */
	public function get_corrected_rules() {
		return $this->corrected_rules;
	}
}
