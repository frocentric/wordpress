<?php
/**
 * Handles registering all Assets for the Events Filterbar V2 Views
 *
 * To remove an Asset:
 * tribe( 'assets' )->remove( 'asset-name' );
 *
 * @since 4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */
namespace Tribe\Events\Filterbar\Views\V2;

use Tribe__Events__Filterbar__View as Plugin;
use Tribe\Events\Views\V2\Template_Bootstrap;
use Tribe\Events\Views\V2\Assets as TEC_Assets;
/**
 * Register the Assets for Events Filterbar View V2.
 *
 * @since 4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */
class Assets extends \tad_DI52_ServiceProvider {

	/**
	 * Key for this group of assets.
	 *
	 * @since 4.9.0
	 *
	 * @var string
	 */
	public static $group_key = 'events-filterbar-views-v2';

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9.0
	 */
	public function register() {
		$plugin = Plugin::instance();

		$required_styles = [
			'tribe-events-views-v2-skeleton',
			'tribe-events-custom-jquery-styles'
		];

		if ( ! tribe( TEC_Assets::class )->is_skeleton_style() ) {
			$required_styles[] = 'tribe-events-views-v2-full';
		}

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-bar-styles',
			'views-filter-bar.css',
			$required_styles,
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-bar-js',
			'views/filter-bar.js',
			[
				'jquery',
				'tribe-common',
				'jquery-ui-slider',
				'tribe-dropdowns',
				'tribe-events-views-v2-viewport',
			],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-admin-settings',
			'admin-settings-tab.js',
			[
				'jquery',
				'tribe-common',
			],
			null
		);

		add_action( 'wp_enqueue_scripts', [ $this, 'disable_v1' ], 0 );
	}

	/**
	 * Removes assets from View V1 when V2 is loaded.
	 *
	 * @since 4.9.5
	 *
	 * @return void
	 */
	public function disable_v1() {
		// Dont disable V1 on Single Event page
		if ( tribe( Template_Bootstrap::class )->is_single_event() ) {
			return;
		}

		add_filter( 'tribe_asset_enqueue_tribe-filterbar-js', '__return_false' );
	}

	/**
	 * Checks if we should enqueue frontend assets for the V2 views
	 *
	 * @since 4.9.0
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend() {

		$should_enqueue = tribe( Template_Bootstrap::class )->should_load();

		/**
		 * Allow filtering of where the base Frontend Assets will be loaded
		 *
		 * @since 4.9.0
		 *
		 * @param bool $should_enqueue
		 */
		return apply_filters( 'tribe_events_pro_views_v2_assets_should_enqueue_frontend', $should_enqueue );
	}

	/**
	 * Fires to include the filter bar assets on shortcodes.
	 *
	 * @since 4.9.0
	 *
	 * @return void
	 */
	public function load_on_shortcode() {
		tribe_asset_enqueue_group( static::$group_key );
	}
}
