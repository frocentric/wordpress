<?php
/**
 * The main service provider for PRO support and additions to the Views V2 functions.
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

use Tribe\Events\Pro\Views\V2\Geo_Loc\Geocoding_Handler;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Handler_Interface as Geo_Loc_Handler;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Google_Maps;
use Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Service_Interface as Geo_Loc_API_Service;

/**
 * Class Service_Provider
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.7.5
	 */
	public function register() {
		if ( ! tribe_events_views_v2_is_enabled() ) {
			return;
		}

		require_once tribe( 'events-pro.main' )->pluginPath . 'src/Tribe/Views/V2/functions/classes.php';

		$this->container->singleton( Shortcodes\Manager::class, Shortcodes\Manager::class );
		$this->register_geolocation_classes();

		$this->register_hooks();
		$this->register_assets();

		// Register the SP on the container
		$this->container->singleton( 'pro.views.v2.provider', $this );
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Views v2.
	 *
	 * @since 4.7.5
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Views v2.
	 *
	 * @since 4.7.5
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'pro.views.v2.hooks', $hooks );
	}

	/**
	 * Sets up the geolocation classes allowing their filtering.
	 *
	 * @since 4.7.9
	 */
	protected function register_geolocation_classes() {
		$this->container->bind( Geo_Loc_API_Service::class, function () {
			/**
			 * Filters the Geo Location API Service object that should be used to resolve addresses to a set of
			 * coordinates.
			 *
			 * If not provided, then the Google Maps API service  will be used.
			 *
			 * @since 4.7.9
			 *
			 * @param Geo_Loc_Handler null|$geo_location_handler A Geo Location handler object, it must implement
			 *                        the `Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Service_Interface` interface.
			 *
			 * @see   Tribe\Events\Pro\Views\V2\Geo_Loc\Services\Google_Maps for the default service.
			 */
			$geo_location_service = apply_filters( 'tribe_events_pro_views_v2_geo_location_service', null );

			if ( null !== $geo_location_service ) {
				return $geo_location_service;
			}

			return $this->container->make( Google_Maps::class );
		} );

		$this->container->singleton( Geo_Loc_Handler::class, function () {
			/**
			 * Filters the Geo Location Handler object that should be used to resolve geo location search requests
			 * and fence queries.
			 *
			 * If not provided, then the default Geocoding Handler will be used.
			 *
			 * @since 4.7.9
			 *
			 * @param Geo_Loc_Handler null|$geo_location_handler A Geo Location handler object, it must implement
			 *                        the `Tribe\Events\Pro\Views\V2\Geo_Loc\Handler_Interface` interface.
			 *
			 * @see   Tribe\Events\Pro\Views\V2\Geo_Loc\Geocoding_Handler for the default resolver.
			 */
			$geo_location_handler = apply_filters( 'tribe_events_pro_views_v2_geo_location_handler', null );

			if ( null !== $geo_location_handler ) {
				return $geo_location_handler;
			}

			return $this->container->make( Geocoding_Handler::class );
		} );
	}


}
