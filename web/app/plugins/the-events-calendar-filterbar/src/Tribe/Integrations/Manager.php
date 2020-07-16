<?php
/**
 * Class Tribe__Events__Filterbar__Integrations__Manager
 *
 * Loads and manages the third-party plugins integration implementations.
 *
 * @since 4.5.8
 */
class Tribe__Events__Filterbar__Integrations__Manager {

	/**
	 * Conditionally loads the classes needed to integrate with third-party plugins.
	 *
	 * Third-party plugin integration classes and methods will be loaded only if
	 * supported plugins are activated.
	 *
	 * @since 4.5.8
	 */
	public function hook() {
		$this->load_wpml_integration();
	}

	/**
	 * Loads WPML integration classes and event listeners.
	 *
	 * @since  4.5.8
	 * @return bool
	 */
	public function load_wpml_integration() {
		if ( ! tribe_is_wpml_active() ) {
			return false;
		}

		tribe_singleton( 'filterbar.wpml', 'Tribe__Events__Filterbar__Integrations__WPML__WPML', array( 'hook' ) );
		tribe( 'filterbar.wpml' );

		return true;
	}

}