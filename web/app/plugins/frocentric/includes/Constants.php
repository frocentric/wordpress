<?php
/**
 * Constants
 *
 * @class       Constants
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Constants class
 */
final class Constants {

	/**
	 * Represents a request to the admin area
	 */
	const ADMIN_REQUEST = 'admin';

	/**
	 * Represents an AJAX request
	 */
	const AJAX_REQUEST = 'ajax';

	/**
	 * Represents a cron request
	 */
	const CRON_REQUEST = 'cron';

	/**
	 * Represents a request to the front end
	 */
	const FRONTEND_REQUEST = 'frontend';

	/**
	 * Set the minimum required versions for the plugin.
	 */
	const PLUGIN_REQUIREMENTS = [
		'php_version' => '8.0',
		'wp_version'  => '6.0',
	];
}
