<?php

/**
 * Class Tribe__Events__Integrations__Manager
 *
 * Loads and manages the third-party plugins integration implementations.
 */
class Tribe__Events__Community__Integrations__Manager {

	/**
	 * @var Tribe__Events__Community__Integrations__Manager
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @since 4.5.10
	 *
	 * @return Tribe__Events__Community__Integrations__Manager
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Conditionally loads the classes needed to integrate with third-party plugins.
	 *
	 * Third-party plugin integration classes and methods will be loaded only if
	 * supported plugins are activated.
	 *
	 * @since 4.5.10
	 */
	public function load_integrations() {
		$this->load_wp_edit_integration();
		$this->load_divi_integration();
	}

	/**
	 * Loads WP Edit integration classes and event listeners.
	 *
	 * @since 4.5.10
	 *
	 * @return bool
	 */
	private function load_wp_edit_integration() {

		if ( ! class_exists( 'JWL_Toggle_wpautop' ) ) {
			return false;
		}

		Tribe__Events__Community__Integrations__WP_Edit::instance()->prevent_wpautop_conflict();

		return true;
	}

	/**
	 * Loads our Divi compatibility layer when required.
	 *
	 * @since 4.5.10
	 *
	 * @return bool
	 */
	protected function load_divi_integration() {
		if ( ! function_exists( 'et_setup_theme' ) ) {
			return false;
		}

		tribe( 'community.integrations.divi' );
		return true;
	}
}