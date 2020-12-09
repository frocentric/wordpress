<?php


/**
 * Class Tribe__Events__Filterbar__Updater
 *
 * @since 4.5
 *
 */
class Tribe__Events__Filterbar__Updater extends Tribe__Events__Updater {

	protected $version_option = 'filter-bar-schema-version';

	/**
	 * Returns an array of callbacks with version strings as keys.
	 * Any key higher than the version recorded in the DB
	 * and lower than $this->current_version will have its
	 * callback called.
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_update_callbacks() {
		return array(
			'4.4' => array( $this, 'migrate_multiselect' ),
		);
	}

	/**
	 * Update Autocomplete type to Multiselect for all filters with that type
	 *
	 * @since 4.5
	 *
	 */
	public function migrate_multiselect() {

		$filter_options = get_option( 'tribe_events_filters_current_active_filters' );

		if ( ! is_array( $filter_options ) ) {
			return;
		}

		foreach ( $filter_options as $key => $filter ) {
			if ( ! empty( $filter['type'] ) && 'autocomplete' === $filter['type'] ) {
				$filter_options[ $key ]['type'] = 'multiselect';
			}
		}

		update_option( 'tribe_events_filters_current_active_filters', $filter_options );

	}

	/**
	 * Force upgrade script to run even without an existing version number
	 * The version was not previously stored for Filter Bar
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function is_new_install() {
		return false;
	}
}
