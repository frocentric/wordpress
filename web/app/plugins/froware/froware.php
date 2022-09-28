<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ingenyus.com
 * @since             1.0.0
 * @package           Froware
 *
 * @wordpress-plugin
 * Plugin Name:       froware.com site-specific plugin
 * Plugin URI:        https://froware.com
 * Description:       This plugin defines custom functionality for froware.com.
 * Version:           1.16.2
 * Author:            Gary McPherson
 * Author URI:        https://ingenyus.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       froware
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.16.2' );

if ( ! function_exists( 'activate_froware' ) ) {
	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-froware-activator.php
	 */
	function activate_froware() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-froware-activator.php';
		Froware_Activator::activate();
	}
}

if ( ! function_exists( 'deactivate_froware' ) ) {
	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-froware-deactivator.php
	 */
	function deactivate_froware() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-froware-deactivator.php';
		Froware_Deactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'activate_froware' );
register_deactivation_hook( __FILE__, 'deactivate_froware' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-froware.php';

if ( ! function_exists( 'run_froware' ) ) {
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_froware() {

		$plugin = new Froware();
		$plugin->run();

	}
}

run_froware();
