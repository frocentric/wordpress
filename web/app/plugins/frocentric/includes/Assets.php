<?php
/**
 * Handle scripts register and enqueue.
 *
 * @class       Assets
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main assets class
 */
abstract class Assets {

	/**
	 * Contains an array of script handles registered by WC.
	 *
	 * @var array<string>
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by WC.
	 *
	 * @var array<string>
	 */
	private static $styles = array();

	/**
	 * Contains an array of script handles localized by WC.
	 *
	 * @var array<string>
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Tries to localize the minified version if required and exists, otherwise load the unminified version
	 *
	 * @param  string $path Path of the asset to locate.
	 * @return string
	 */
	public static function localize_asset( $path ) {

		$assets_path     = Utils::plugin_path() . '/assets/source/';
		$assets_path_url = str_replace( array( 'http:', 'https:' ), '', Utils::plugin_url() ) . '/assets/source/';

		if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {

			$ext_pos = strrpos( $path, '.' );

			if ( is_numeric( $ext_pos ) ) {

				$clean_path = substr( $path, 0, $ext_pos );
				$ext        = substr( $path, $ext_pos );
				$min_path   = $clean_path . '.min' . $ext;

				if ( file_exists( $assets_path . $min_path ) ) {
					$path = $min_path;
				}
			}
		}

		return $assets_path_url . $path;
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array<string,array>
	 */
	public static function get_styles() {
		// Allow to change the list of styles.
		return apply_filters( 'frocentric_enqueue_styles', array() );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array<string,array>
	 */
	public static function get_scripts() {
		// Allow to change the list of scripts.
		return apply_filters( 'frocentric_enqueue_scripts', array() );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string|bool      $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 *                                    If source is set to false, script is an alias of other scripts it depends on.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $version   Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                    as a query string for cache busting purposes. If version is set to false, a version
	 *                                    number is automatically added equal to current installed WordPress version.
	 *                                    If set to null, no version is added.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
	 *                                    Default 'false'.
	 *
	 * @return void
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string|bool      $path      Full URL of the script, or path of the script relative to the WordPress root directory.
	 *                                    If source is set to false, script is an alias of other scripts it depends on.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $version   Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                    as a query string for cache busting purposes. If version is set to false, a version
	 *                                    number is automatically added equal to current installed WordPress version.
	 *                                    If set to null, no version is added.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
	 *                                    Default 'false'.
	 *
	 * @return void
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = VERSION, $in_footer = true ) {

		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @param string           $handle  Name of the stylesheet. Should be unique.
	 * @param string|bool      $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 *                                  If source is set to false, stylesheet is an alias of other stylesheets it depends on.
	 * @param string[]         $deps    Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string|bool|null $version Optional. String specifying stylesheet version number, if it has one, which is added to the URL
	 *                                  as a query string for cache busting purposes. If version is set to false, a version
	 *                                  number is automatically added equal to current installed WordPress version.
	 *                                  If set to null, no version is added.
	 * @param string           $media   Optional. The media for which this stylesheet has been defined.
	 *                                  Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
	 *                                  '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @return void
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @param string           $handle  Name of the stylesheet. Should be unique.
	 * @param string|bool      $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 *                                  If source is set to false, stylesheet is an alias of other stylesheets it depends on.
	 * @param string[]         $deps    Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string|bool|null $version Optional. String specifying stylesheet version number, if it has one, which is added to the URL
	 *                                  as a query string for cache busting purposes. If version is set to false, a version
	 *                                  number is automatically added equal to current installed WordPress version.
	 *                                  If set to null, no version is added.
	 * @param string           $media   Optional. The media for which this stylesheet has been defined.
	 *                                  Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
	 *                                  '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @return void
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = VERSION, $media = 'all' ) {

		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}

		wp_enqueue_style( $handle );
	}

	/**
	 * Register/queue frontend scripts.
	 *
	 * @return void
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	public static function load_scripts() {

		if ( ! did_action( 'before_frocentric_init' ) ) {
			return;
		}

		// JS Scripts.
		$enqueue_scripts = static::get_scripts();
		if ( $enqueue_scripts ) {

			foreach ( $enqueue_scripts as $handle => $args ) {
				$args = wp_parse_args(
					$args,
					array(
						'src'       => '',
						'deps'      => array( 'jquery' ),
						'version'   => VERSION,
						'in_footer' => true,
						'enqueue'   => true,
					)
				);

				if ( $args['enqueue'] ) {
					self::enqueue_script( $handle, $args['src'], $args['deps'], $args['version'], $args['in_footer'] );
				} else {
					self::register_script( $handle, $args['src'], $args['deps'], $args['version'], $args['in_footer'] );
				}
			}
		}

		// CSS Styles.
		$enqueue_styles = static::get_styles();
		if ( $enqueue_styles ) {

			foreach ( $enqueue_styles as $handle => $args ) {
				$args = wp_parse_args(
					$args,
					array(
						'src'     => '',
						'deps'    => '',
						'version' => VERSION,
						'media'   => 'all',
						'enqueue' => true,
					)
				);

				if ( $args['enqueue'] ) {
					self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
				} else {
					self::register_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
				}
			}
		}
	}

	/**
	 * Localize a WC script once.
	 *
	 * @since  1.0.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 * @param  string $handle Handle of the script to localize.
	 *
	 * @return void
	 */
	private static function localize_script( $handle ) {

		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {

			$data = self::get_script_data( $handle );
			if ( $data ) {
				$name                        = str_replace( '-', '_', $handle ) . '_params';
				self::$wp_localize_scripts[] = $handle;
				// Let plugins to filter the script data.
				wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
			}
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Handle of the script to add data for.
	 * @return array<string,mixed>|bool
	 */
	private static function get_script_data( $handle ) {

		$scripts = self::get_scripts();
		if ( isset( $scripts[ $handle ] ) && isset( $scripts[ $handle ]['data'] ) ) {

			$data = $scripts[ $handle ]['data'];
			if ( is_callable( $data ) ) {
				$data = $data();
			}

			return $data;
		}

		return false;
	}

	/**
	 * Localize scripts only when enqueued.
	 *
	 * @return void
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}
