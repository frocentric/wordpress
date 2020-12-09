<?php
/**
 * This recurrene engine can be safely passed around but will not do anything
 * and will never update the database.
 *
 * @since 4.7
 */

class Tribe__Events__Pro__Recurrence__Engines__Null implements Tribe__Events__Pro__Recurrence__Engines__Engine_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_slug() {
		return Tribe__Events__Pro__Service_Providers__RBE::NONE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'None', 'events-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function preview( $data ) {
		return new Tribe__Events__Pro__Recurrence__Engines__Work_Report;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update( $data ) {
		return new Tribe__Events__Pro__Recurrence__Engines__Work_Report;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hook() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function unhook() {
		return true;
	}
}
