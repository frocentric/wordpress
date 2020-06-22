<?php

/**
 * Class Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta {
	protected $rules_key = '_tribe_blocks_recurrence_rules';
	protected $exclusions_key = '_tribe_blocks_recurrence_exclusions';
	protected $description_key = '_tribe_blocks_recurrence_description';

	/**
	 * Meta key used to get the rules associated with the recurrence on the new UI
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function get_rules_key() {
		return $this->rules_key;
	}

	/**
	 * Return the meta key used to get the exclusions in a post.
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function get_exclusions_key() {
		return $this->exclusions_key;
	}

	/**
	 * Return the name of the key used to reference the recurrence description value
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function get_description_key() {
		return $this->description_key;
	}
}
