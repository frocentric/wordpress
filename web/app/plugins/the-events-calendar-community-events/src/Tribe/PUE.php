<?php
/**
 * Manage upgrades for the Tribe Community Events plugin
 *
 * @author Peter Chester
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Community__PUE' ) ) {
	class Tribe__Events__Community__PUE {

		/**
		 * @var string slug used for the plugin update engine
		 */
		private static $pue_slug = 'events-community';

		/**
		 * @var string plugin update url
		 */
		private static $update_url = 'http://tri.be/';

		/**
		 * @var string plugin file name
		 */
		private static $plugin_file;

		/**
		 * Constructor function. a.k.a. Let's get this party started!
		 *
		 * @param string $plugin_file file path.
		 */
		public function __construct( $plugin_file ) {
			self::$plugin_file = $plugin_file;
			$this->load_plugin_update_engine();
			register_activation_hook( self::$plugin_file, [ $this, 'register_uninstall_hook' ] );
		}

		/**
		 * Load the Plugin Update Engine
		 */
		public function load_plugin_update_engine() {
			if ( apply_filters( 'tribe_enable_pue', true, self::$pue_slug ) && class_exists( 'Tribe__PUE__Checker' ) ) {
				$this->pue_instance = new Tribe__PUE__Checker( self::$update_url, self::$pue_slug, [], plugin_basename( self::$plugin_file ) );
			}
		}

		/**
		 * Register the uninstall hook on activation
		 */
		public function register_uninstall_hook() {
			register_uninstall_hook( self::$plugin_file, [ get_class( $this ), 'uninstall' ] );
		}

		/**
		 * The uninstall hook for the pue option.
		 */
		public function uninstall() {
			$slug = str_replace( '-', '_', self::$pue_slug );
			delete_option( 'pue_install_key_' . $slug );
			delete_option( 'pu_dismissed_upgrade_' . $slug );
		}
	}
}
