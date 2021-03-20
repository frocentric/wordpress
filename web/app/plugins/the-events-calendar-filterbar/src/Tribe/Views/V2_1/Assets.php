<?php
/**
 * Handles registering all Assets for the Events Filterbar v2-1 Views
 *
 * To remove an Asset:
 * tribe( 'assets' )->remove( 'asset-name' );
 *
 * @since 5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\v2_1
 */
namespace Tribe\Events\Filterbar\Views\V2_1;

use Tribe__Events__Filterbar__View as Plugin;
use Tribe\Events\Views\V2\Template_Bootstrap;
use Tribe\Events\Views\V2\Assets as TEC_Assets;
/**
 * Register the Assets for Events Filterbar View v2-1.
 *
 * @since 5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\v2-1
 */
class Assets extends \tad_DI52_ServiceProvider {

	/**
	 * Key for this group of assets.
	 *
	 * @since 5.0.0
	 *
	 * @var string
	 */
	public static $group_key = 'events-filterbar-views-v2-1';

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		$plugin = Plugin::instance();

		$required_styles = [
			'tribe-select2-css',
			'tribe-events-views-v2-skeleton',
			'tribe-events-custom-jquery-styles',
		];

		if ( ! tribe( TEC_Assets::class )->is_skeleton_style() ) {
			$required_styles[] = 'tribe-events-views-v2-full';
		}

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-v2-1-filter-bar-skeleton',
			'views-filter-bar-skeleton.css',
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
			'tribe-events-filterbar-views-v2-1-filter-bar-full',
			'views-filter-bar-full.css',
			[ 'tribe-events-filterbar-views-v2-1-filter-bar-skeleton' ],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [
					'operator' => 'AND',
					[ $this, 'should_enqueue_frontend' ],
					[ tribe( TEC_Assets::class ), 'should_enqueue_full_styles' ],
				],
				'groups'       => [ static::$group_key ],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-bar-state-js',
			'views/filter-bar-state.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-accordion',
			],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
				'in_footer'    => false,
				'localize'     => [
					'name' => 'tribe_events_filter_bar_js_config',
					'data' => $this->container->make( Configuration::class )->localize(),
				],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-toggle-js',
			'views/filter-toggle.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-accordion',
				'tribe-events-views-v2-viewport',
			],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
			]
		);

		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
			tribe_asset(
				$plugin,
				'swiper',
				'vendor/swiper/dist/js/swiper.js',
				[],
				null,
				[]
			);
		}

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-bar-slider-js',
			'views/filter-bar-slider.js',
			[
				'jquery',
				'swiper',
				'tribe-common',
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
			'tribe-events-filterbar-views-filter-button-js',
			'views/filter-button.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-viewport',
				'tribe-events-filterbar-views-filter-bar-state-js',
				'tribe-events-filterbar-views-filter-bar-slider-js',
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
			'tribe-events-filterbar-views-filters-js',
			'views/filters.js',
			[
				'jquery',
				'tribe-common',
			],
			null
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-checkboxes-js',
			'views/filter-checkboxes.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'tribe-events-filterbar-views-filter-radios-js',
			'views/filter-radios.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'jquery-ui-touch-punch',
			'vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js',
			[
				'jquery',
				'jquery-ui-slider',
				'tribe-common',
			],
			null
		);

		tribe_asset(
			$plugin,
			'tribe-events-filterbar-views-filter-range-js',
			'views/filter-range.js',
			[
				'jquery',
				'jquery-ui-slider',
				'jquery-ui-touch-punch',
				'underscore',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'tribe-events-filterbar-views-filter-dropdowns-js',
			'views/filter-dropdowns.js',
			[
				'jquery',
				'tribe-dropdowns',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'tribe-events-filterbar-views-filter-multiselects-js',
			'views/filter-multiselects.js',
			[
				'jquery',
				'tribe-dropdowns',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'tribe-events-filterbar-views-filter-remove-js',
			'views/filter-remove.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
			'tribe-events-filterbar-views-filter-clear-js',
			'views/filter-clear.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-filterbar-views-filters-js',
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
	 * Removes assets from View V1 when v2-1 is loaded.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function disable_v1() {
		// Dont disable V1 on Single Event page
		if ( tribe( Template_Bootstrap::class )->is_single_event() ) {
			return;
		}

		add_filter( 'tribe_asset_enqueue_tribe-filterbar-js', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-filterbar-styles', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-filterbar-mobile-styles', '__return_false' );
		remove_action( 'wp_enqueue_scripts', [ Plugin::instance(), 'enqueueStylesAndScripts' ], 11 );
	}

	/**
	 * Checks if we should enqueue frontend assets for the v2-1 views
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend() {

		$should_enqueue = tribe( Template_Bootstrap::class )->should_load();

		/**
		 * Allow filtering of where the base Frontend Assets will be loaded
		 *
		 * @since 5.0.0
		 *
		 * @param bool $should_enqueue
		 */
		return apply_filters( 'tribe_events_filter_bar_views_v2_assets_should_enqueue_frontend', $should_enqueue );
	}

	/**
	 * Fires to include the filter bar assets on shortcodes.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function load_on_shortcode() {
		tribe_asset_enqueue_group( static::$group_key );
	}
}
