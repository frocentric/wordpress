<?php
/**
 * Register admin assets.
 *
 * @class       AdminAssets
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric\Admin;

use Frocentric\Assets as AssetsMain;
use Frocentric\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin assets class
 */
final class Assets {

	/**
	 * Hook in methods.
	 */
	public static function hooks() {
		add_action( 'admin_enqueue_scripts', array( AssetsMain::class, 'load_scripts' ) );
		add_action( 'admin_init', array( __CLASS__, 'restrict_wpadmin_access' ) );
		add_action( 'admin_print_footer_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );
		add_action( 'admin_print_scripts', array( AssetsMain::class, 'localize_printed_scripts' ), 5 );
		add_action( 'admin_print_scripts-profile.php', array( __CLASS__, 'hide_admin_bar_prefs' ) );
		add_filter( 'option_active_plugins', array( __CLASS__, 'filter_active_plugins' ), 10, 2 );
		add_filter( 'frocentric_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 9 );
		add_filter( 'frocentric_enqueue_styles', array( __CLASS__, 'add_styles' ), 9 );
	}

	/**
	 * Add scripts for the admin.
	 *
	 * @param  array $scripts Admin scripts.
	 * @return array<string,array>
	 */
	public static function add_scripts( $scripts ) {

		$scripts['frocentric-admin'] = array(
			'src'  => AssetsMain::localize_asset( 'js/admin/frocentric.js' ),
			'data' => array(
				'ajax_url' => Utils::ajax_url(),
			),
		);

		return $scripts;
	}

	/**
	 * Add styles for the admin.
	 *
	 * @param array $styles Admin styles.
	 * @return array<string,array>
	 */
	public static function add_styles( $styles ) {

		$styles['frocentric-admin'] = array(
			'src' => AssetsMain::localize_asset( 'css/admin/frocentric-admin.css' ),
		);

		return $styles;
	}

	/**
	 * Hooks in to the option_active_plugins filter and removes any malformed plugins
	 */
	public static function filter_active_plugins( $value, $option ) {
		if ( ! is_array( $value ) || count( $value ) === 0 ) {
			return $value;
		}

		foreach ( $value as $key => $val ) {
			if ( is_numeric( $val ) ) {
				unset( $value[ $key ] );
			}
		}

		return $value;
	}

	/**
	 * Renders stylesheet to hide admin bar on profile page
	 */
	public static function hide_admin_bar_prefs() { ?>
		<style type="text/css">
			.show-admin-bar {display: none;}
		</style>
		<?php
	}

	/**
	 * Restricts wp-admin access if user can't create/edit posts.
	 */
	public static function restrict_wpadmin_access() {
		if ( wp_doing_ajax() || current_user_can( 'edit_posts' ) ) {
			return;
		} else {
			global $wp_query;
			$wp_query->set_404();
			http_response_code( 404 );
			nocache_headers();
			get_template_part( 'content', '404' );

			die();
		}
	}
}
