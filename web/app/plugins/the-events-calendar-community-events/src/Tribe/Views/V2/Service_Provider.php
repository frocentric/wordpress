<?php
/**
 * Handles the plugin integration with Views v2.
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */

namespace Tribe\Events\Community\Views\V2;

/**
 * Class Service_Provider
 *
 * @since   4.8.3
 *
 * @package Tribe\Events\Community\Views\V2
 */
class Service_Provider extends \tad_DI52_ServiceProvider {
	/**
	 * Registers the bindings required for the plugin integration with Views v2 to work.
	 *
	 * @since 4.8.3
	 */
	public function register() {
		if ( ! (
			function_exists( 'tribe_events_views_v2_is_enabled' )
			&& tribe_events_views_v2_is_enabled()
		) ) {
			// If The Events Calendar is not active or Views v2 is not enabled, bail.
			return;
		}

		// Register this Service Provider on the container.
		$this->container->singleton( 'community.views.v2.provider', $this );
		$this->container->singleton( static::class, $this );

		$this->register_hooks();
	}

	/**
	 * Registers the hooks service provider.
	 *
	 * @since 4.8.3
	 */
	private function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'community.views.v2.hooks', $hooks );
	}
}
