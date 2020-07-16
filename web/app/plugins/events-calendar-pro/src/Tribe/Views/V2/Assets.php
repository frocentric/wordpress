<?php
/**
 * Handles registering all Assets for the Events Pro V2 Views
 *
 * To remove a Assets:
 * tribe( 'assets' )->remove( 'asset-name' );
 *
 * @since 4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2
 */
namespace Tribe\Events\Pro\Views\V2;

use Tribe\Events\Views\V2\Assets as TEC_Assets;
use Tribe\Events\Views\V2\Template_Bootstrap;
use Tribe__Events__Main;
use Tribe__Events__Pro__Main as Plugin;
use Tribe__Events__Templates;

/**
 * Register the Assets for Events Pro View V2.
 *
 * @since 4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2
 */
class Assets extends \tad_DI52_ServiceProvider {

	/**
	 * Key for this group of assets.
	 *
	 * @since 4.7.5
	 *
	 * @var string
	 */
	public static $group_key = 'events-pro-views-v2';

	/**
	 * Caches the result of the `should_enqueue_frontend` check.
	 *
	 * @since 5.0.0
	 *
	 * @var bool
	 */
	protected $should_enqueue_frontend;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.7.5
	 */
	public function register() {
		$plugin = Plugin::instance();

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-skeleton',
			'views-skeleton.css',
			[ 'tribe-events-views-v2-skeleton' ],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-full',
			'views-full.css',
			[ 'tribe-events-views-v2-full' ],
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
			'tribe-events-pro-views-v2-nanoscroller',
			'vendor/nanoscroller/jquery.nanoscroller.js',
			[ 'jquery-ui-draggable' ],
			null
		);

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-week-grid-scroller',
			'views/week-grid-scroller.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-pro-views-v2-nanoscroller',
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
			'tribe-events-pro-views-v2-week-day-selector',
			'views/week-day-selector.js',
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
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-week-multiday-toggle',
			'views/week-multiday-toggle.js',
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
			]
		);

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-week-event-link',
			'views/week-event-link.js',
			[
				'jquery',
				'tribe-common',
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
			'tribe-events-pro-views-v2-map-events-scroller',
			'views/map-events-scroller.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-pro-views-v2-nanoscroller',
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
			'tribe-events-pro-views-v2-map-events',
			'views/map-events.js',
			[
				'tribe-events-pro-views-v2-map-provider-google-maps',
				'tribe-events-views-v2-accordion',
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
			'swiper',
			'vendor/swiper/dist/js/swiper.js',
			[],
			null,
			[]
		);

		tribe_asset(
			$plugin,
			'tribe-events-pro-views-v2-map-provider-google-maps',
			'views/map-provider-google-maps.js',
			[
				'swiper',
				'tribe-events-pro-views-v2-map-no-venue-modal',
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
			'tribe-events-pro-views-v2-map-no-venue-modal',
			'views/map-no-venue-modal.js',
			[
				'jquery',
				'tribe-common',
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
			'tribe-events-pro-views-v2-tooltip-pro',
			'views/tooltip-pro.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-tooltip',
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
			'tribe-events-pro-views-v2-multiday-events-pro',
			'views/multiday-events-pro.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-multiday-events',
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
			'tribe-events-pro-views-v2-toggle-recurrence',
			'views/toggle-recurrence.js',
			[
				'jquery',
				'tribe-common',
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
			'tribe-events-pro-views-v2-datepicker-pro',
			'views/datepicker-pro.js',
			[
				'jquery',
				'tribe-common',
				'tribe-events-views-v2-bootstrap-datepicker',
				'tribe-events-views-v2-datepicker',
			],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'groups'       => [ static::$group_key ],
			]
		);

		$overrides_stylesheet = Tribe__Events__Templates::locate_stylesheet( 'tribe-events/pro/tribe-events-pro.css' );

		if ( ! empty( $overrides_stylesheet ) ) {
			tribe_asset(
				$plugin,
				'tribe-events-pro-views-v2-override-style',
				$overrides_stylesheet,
				[
					'tribe-common-full-style',
					'tribe-events-pro-views-v2-skeleton',
				],
				'wp_enqueue_scripts',
				[
					'priority'     => 10,
					'conditionals' => [ $this, 'should_enqueue_frontend' ],
					'groups'       => [ static::$group_key ],
				]
			);
		}

		$widget_overrides_stylesheet = Tribe__Events__Templates::locate_stylesheet( 'tribe-events/pro/widget-calendar.css' );

		if ( ! empty( $widget_overrides_stylesheet ) ) {
			tribe_asset(
				$plugin,
				Tribe__Events__Main::POSTTYPE . '-widget-calendar-pro-override-style',
				$widget_overrides_stylesheet,
				[],
				null,
				[]
			);
		}
	}

	/**
	 * Checks if we should enqueue frontend assets for the V2 views
	 *
	 * @since 4.7.5
	 * @since 5.0.0 Cache the check value.
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend() {
		if ( null !== $this->should_enqueue_frontend ) {
			return $this->should_enqueue_frontend;
		}

		$should_enqueue = tribe( Template_Bootstrap::class )->should_load();

		/**
		 * Allow filtering of where the base Frontend Assets will be loaded
		 *
		 * @since 4.7.5
		 *
		 * @param bool $should_enqueue
		 */
		$should_enqueue = apply_filters( 'tribe_events_pro_views_v2_assets_should_enqueue_frontend', $should_enqueue );

		$this->should_enqueue_frontend = $should_enqueue;

		return $should_enqueue;
	}

	/**
	 * Removes assets from View V1 when V2 is loaded.
	 *
	 * @since 4.7.5
	 *
	 * @return void
	 */
	public function disable_v1() {
		// Don't disable V1 on Single Event page
		if ( tribe( Template_Bootstrap::class )->is_single_event() ) {
			return;
		}

		add_filter( 'tribe_asset_enqueue_tribe-events-pro-slimscroll', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-pro-geoloc', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-pro-week', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-pro-photo', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-pro', '__return_false' );

		add_filter( 'tribe_asset_enqueue_tribe-events-calendar-pro-override-style', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-calendar-pro-style', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-full-pro-calendar-style', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-calendar-pro-mobile-style', '__return_false' );
		add_filter( 'tribe_asset_enqueue_tribe-events-calendar-full-pro-mobile-style', '__return_false' );

		remove_action( 'wp_enqueue_scripts', [ Plugin::instance(), 'enqueue_pro_scripts' ], 8 );
	}
}
