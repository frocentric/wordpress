<?php
/**
 * Utility methods
 *
 * @class       Utils
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

use Frocentric\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utils class
 */
final class Utils {

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron, frontend or login.
	 * @return bool
	 */
	public static function is_request( $type ) {

		switch ( $type ) {
			case Constants::ADMIN_REQUEST:
				return is_admin();
			case Constants::AJAX_REQUEST:
				return defined( 'DOING_AJAX' ) && DOING_AJAX;
			case Constants::CRON_REQUEST:
				return defined( 'DOING_CRON' ) && DOING_CRON;
			case Constants::FRONTEND_REQUEST:
				return ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) && ( ! defined( 'DOING_CRON' ) || ! DOING_CRON );
			case Constants::LOGIN_REQUEST:
				return is_login();
		}
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public static function template_path() {
		// Allow 3rd party plugin filter template path from their plugin.
		return apply_filters( 'frocentric_template_path', 'frocentric/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public static function ajax_url() {
		return admin_url( 'admin-ajax.php' );
	}

	/**
	 * Replacement for deprecated `get_page_by_title` function
	 */
	public static function get_page_by_title( $title, $output = OBJECT, $type = 'page' ) {
		$posts = get_posts(
			array(
				'title'     => $title,
				'post_type' => $type,
			)
		);

		return count( $posts ) > 0 && ! empty( $posts[0] ) ? get_post( $posts[0], $output ) : null;
	}

	public static function customise_error_reporting( $levels ) {
		if ( ! is_array( $levels ) ) {
			return;
		}

		$error_mask = E_ALL;
		foreach ( $levels as $level_name ) {
			if ( defined( $level_name ) ) {
				$level = constant( $level_name );

				if ( is_numeric( $level ) ) {
					$error_mask &= ~$level;
				}
			}
		}
		// error_reporting(E_ALL);
		// error_reporting(E_NOTICE);
		// error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
		error_reporting( $error_mask );
	}
}
