<?php


/**
 * Class Tribe__Events__Pro__Integrations__WPML__Defaults
 *
 * Handles sensible defaults for to The Events Calendar Pro in WPML.
 */
class Tribe__Events__Pro__Integrations__WPML__Defaults extends  Tribe__Events__Integrations__WPML__Defaults  {

	/**
	 * @var Tribe__Events__Pro__Integrations__WPML__Defaults
	 */
	protected static $instance;

	/**
	 * @var string The name of the sub-option that will store the first run flag.
	 */
	public $defaults_option_name = 'wpml_did_set_pro_defaults';

	/**
	 * The class singleton constructor
	 *
	 * @return Tribe__Events__Pro__Integrations__WPML__Defaults
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Returns the path to the WPML config file for the plugin.
	 *
	 * @return string
	 */
	protected function get_config_file_path() {
		return Tribe__Events__Pro__Main::instance()->pluginPath . DIRECTORY_SEPARATOR . 'wpml-config.xml';
	}
}
