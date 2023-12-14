<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://hq.frocentric.io
 * @since             1.0.0
 * @package           Frocentric
 *
 * @wordpress-plugin
 * Plugin Name: Frocentric Platform
 * Plugin URI:  https://github.com/frocentric/wordpress//
 * Description: Custom functionality for the frocentric.io platform
 * Version:     1.18.11
 * Author:      Frocentric CIC
 * Author URI:  https://hq.frocentric.io/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: frocentric
 * Domain Path: /i18n/languages
 */

/**
 * Developer note: updating minimum PHP, WordPress and WooCommerce versions.
 *
 * When updating any version metadata above and below please ensure to update these files:
 * - `phpcs.xml`
 */

namespace Frocentric;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants.
const VERSION     = '1.10.3';
const PLUGIN_FILE = __FILE__;

/**
 * Return error data
 *
 * @return array
 */
function get_error() {
	return array(
		/* translators: 1: composer command. 2: plugin directory */
		'message'   => esc_html__( 'Your installation of Frocentric Platform plugin is incomplete. Please run %1$s within the %2$s directory.', 'frocentric' ),
		'command'   => 'composer install',
		'directory' => esc_html( str_replace( ABSPATH, '', __DIR__ ) ),
	);
}

/**
 * Autoload packages.
 *
 * The package autoloader includes version information which prevents classes in this feature plugin
 * conflicting with WooCommerce core.
 *
 * We want to fail gracefully if `composer install` has not been executed yet, so we are checking for the autoloader.
 * If the autoloader is not present, let's log the failure and display a nice admin notice.
 */
$autoloader = __DIR__ . '/vendor/autoload.php';

if ( ! is_readable( $autoloader ) ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$composer_error = get_error();
		error_log( sprintf( $composer_error['message'], '`' . $composer_error['command'] . '`', '`' . $composer_error['directory'] . '`' ) ); // phpcs:ignore
	}

	/**
	 * Outputs an admin notice if composer install has not been ran.
	 */
	add_action(
		'admin_notices',
		function () {
			$composer_error = get_error();
			?>
			<div class="notice notice-error">
				<p>
					<?php printf( $composer_error['message'], '<code>' . $composer_error['command'] . '</code>', '<code>' . $composer_error['directory'] . '</code>' ); // phpcs:ignore ?>
				</p>
			</div>
			<?php
		}
	);

	return;
}

require $autoloader;

Main::bootstrap();
