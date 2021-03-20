<?php
namespace Tribe\Events\Filterbar\Views\V2_1;

/**
 * The main service provider for Filterbar support and additions to the Views V2_1 functions.
 *
 * @since   5.0.0
 * @package Tribe\Events\Filterbar\Views\V2_1
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		if ( ! tribe_events_views_v2_is_enabled() ) {
			return;
		}

		$this->container->singleton( Filters::class, Filters::class );
		$this->container->singleton( Configuration::class, Configuration::class );

		$this->register_hooks();
		$this->register_assets();

		// Register the SP on the container
		$this->container->singleton( 'filterbar.views.v2_1.provider', $this );
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Registers the provider handling assets for Views v2_1.
	 *
	 * @since 5.0.0
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Views v2_1.
	 *
	 * @since 5.0.0
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'filterbar.views.v2_1.hooks', $hooks );
	}
}
