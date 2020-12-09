<?php


/**
 * Class Tribe__Events__Community__Updater
 *
 * @since 4.5.10
 *
 */
class Tribe__Events__Community__Updater extends Tribe__Events__Updater {

	protected $version_option = 'tribe-events-community-schema-version';

	/**
	 * Force upgrade script to run even without an existing version number
	 * The version was not previously stored for Filter Bar
	 *
	 * @since 4.5.10
	 *
	 * @return bool
	 */
	public function is_new_install() {
		return false;
	}
}
