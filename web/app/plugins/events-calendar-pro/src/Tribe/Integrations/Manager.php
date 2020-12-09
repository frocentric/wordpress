<?php


/**
 * Class Tribe__Events__Pro__Integrations__Manager
 *
 * Loads and manages the third-party plugins integration implementations.
 */
class Tribe__Events__Pro__Integrations__Manager {

	/**
	 * @var Tribe__Events__Pro__Integrations__Manager
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @return Tribe__Events__Pro__Integrations__Manager
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Loads WPML integration classes and event listeners.
	 *
	 * @return bool
	 */
	private function load_wpml_integration() {
		if ( ! ( class_exists( 'SitePress' ) && defined( 'ICL_PLUGIN_PATH' ) ) ) {
			return false;
		}

		Tribe__Events__Pro__Integrations__WPML__WPML::instance()->hook();

		return true;
	}

	/**
	 * Loads WP SEO / WP SEO Premium integration classes and event listeners.
	 *
	 * @since 4.4.14
	 *
	 * @return bool
	 */
	private function load_wpseo_integration() {
		if ( ! class_exists( 'WPSEO_Premium' ) ) {
			return false;
		}

		tribe_singleton( 'pro.integrations.wp-seo', 'Tribe__Events__Pro__Integrations__WP_SEO__WP_SEO', array( 'hook' ) );
		tribe( 'pro.integrations.wp-seo' );

		return true;
	}

	/**
	 * Loads Site Origin integration classes and event listeners.
	 *
	 * @since 4.4.29
	 *
	 * @return bool
	 */
	private function load_site_origin_integration() {
		if ( ! class_exists( 'SiteOrigin_Panels' ) ) {
			return false;
		}

		tribe_singleton( 'pro.integrations.site-origin', 'Tribe__Events__Pro__Integrations__Site_Origin__Page_Builder', array( 'hook' ) );
		tribe( 'pro.integrations.site-origin' );


		return true;
	}


	/**
	 * Conditionally loads the classes needed to integrate with third-party plugins.
	 *
	 * Third-party plugin integration classes and methods will be loaded only if
	 * supported plugins are activated.
	 */
	public function load_integrations() {
		$this->load_wpml_integration();
		$this->load_wpseo_integration();
		$this->load_site_origin_integration();
	}
}
