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
		add_filter( 'plugin_name_enqueue_styles', [ __CLASS__, 'add_styles' ], 9 );
		add_filter( 'plugin_name_enqueue_scripts', [ __CLASS__, 'add_scripts' ], 9 );
		add_action( 'admin_enqueue_scripts', [ AssetsMain::class, 'load_scripts' ] );
		add_action( 'admin_print_scripts', [ AssetsMain::class, 'localize_printed_scripts' ], 5 );
		add_action( 'admin_print_footer_scripts', [ AssetsMain::class, 'localize_printed_scripts' ], 5 );
	}

	/**
	 * Add styles for the admin.
	 *
	 * @param array $styles Admin styles.
	 * @return array<string,array>
	 */
	public static function add_styles( $styles ) {

		$styles['frocentric-admin'] = [
			'src' => AssetsMain::localize_asset( 'css/admin/frocentric-admin.css' ),
		];

		return $styles;
	}

	/**
	 * Add scripts for the admin.
	 *
	 * @param  array $scripts Admin scripts.
	 * @return array<string,array>
	 */
	public static function add_scripts( $scripts ) {

		$scripts['frocentric-admin'] = [
			'src'  => AssetsMain::localize_asset( 'js/admin/frocentric.js' ),
			'data' => [
				'ajax_url' => Utils::ajax_url(),
			],
		];

		return $scripts;
	}
}
