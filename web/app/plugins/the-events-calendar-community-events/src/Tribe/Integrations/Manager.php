<?php

use Tribe\Events\Virtual\Plugin as Virtual_Events_Plugin;

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
		$this->load_virtual_events_integration();
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

	/**
	 * Loads our Virtual Events compatibility layer when required.
	 *
	 * @since4.8.0
	 *
	 * @return bool
	 */
	protected function load_virtual_events_integration() {
		// Check if Virtual Events is activated.
		if ( ! class_exists( '\Tribe\Events\Virtual\Plugin' ) ) {
			return false;
		}

		// Check if we are running the required version (that lets us disable Zoom integration for Event frontend forms).
		if ( version_compare( Virtual_Events_Plugin::VERSION, '1.0.3-dev', '<' ) ) {
			return false;
		}

		/**
		 * Allow filtering whether to enable the Virtual Events integration.
		 *
		 * @since4.8.0
		 *
		 * @param boolean $integration_enabled Whether to enable the Virtual Events integration, default is true.
		 */
		$integration_enabled = apply_filters( 'tribe_community_events_virtual_events_integration_enabled', true );

		// Only load the integration if enabled.
		if ( false === $integration_enabled ) {
			return false;
		}

		tribe( 'community.integrations.virtual-events' );

		return true;
	}
}
