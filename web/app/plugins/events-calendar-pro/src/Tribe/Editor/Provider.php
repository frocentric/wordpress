<?php

class Tribe__Events__Pro__Editor__Provider extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.5
	 *
	 */
	public function register() {
		// Return if we shouldn't load blocks or Events Pro Plugin is active
		if (
			! tribe( 'editor' )->should_load_blocks()
			|| ! tribe( 'events.editor.compatibility' )->is_blocks_editor_toggled_on()
			|| ! class_exists( 'Tribe__Events__Pro__Main' )
		) {
			return;
		}

		$this->container->singleton( 'events-pro.editor', 'Tribe__Events__Pro__Editor' );
		$this->container->singleton( 'events-pro.editor.fields', 'Tribe__Events__Pro__Editor__Additional_Fields' );
		$this->container->singleton( 'events-pro.editor.frontend.template', 'Tribe__Events__Pro__Editor__Template__Frontend' );
		$this->container->singleton( 'events-pro.editor.admin.template', 'Tribe__Events__Pro__Editor__Template__Admin' );
		$this->container->singleton( 'events-pro.editor.configuration', 'Tribe__Events__Pro__Editor__Configuration', array( 'hook' ) );
		$this->container->singleton( 'events-pro.editor.assets', 'Tribe__Events__Pro__Editor__Assets', array( 'register' ) );

		$this->container->singleton( 'events-pro.editor.meta', 'Tribe__Events__Pro__Editor__Meta' );
		$this->container->singleton( 'events-pro.editor.recurrence.provider', 'Tribe__Events__Pro__Editor__Recurrence__Provider' );
		$this->container->singleton( 'events-pro.editor.recurrence.queue-status', 'Tribe__Events__Pro__Editor__Recurrence__Queue_Status' );
		$this->container->singleton( 'events-pro.editor.recurrence.blocks-meta', 'Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta' );

		// Singletons for pro blocks
		$this->container->singleton( 'events-pro.editor.blocks.fields', 'Tribe__Events__Pro__Editor__Blocks__Additional_Fields' );
		$this->container->singleton( 'events-pro.editor.blocks.related-events', 'Tribe__Events__Pro__Editor__Blocks__Related_Events' );

		$this->hook();

		// Initialize the correct Singletons
		tribe( 'events-pro.editor.assets' );
		tribe( 'events-pro.editor.configuration' );
	}

	/**
	 * Any hooking any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.5
	 *
	 */
	protected function hook() {

		tribe( 'events-pro.editor.recurrence.provider' )->hook();
		tribe( 'events-pro.editor.recurrence.queue-status' )->hook();
		add_action( 'init', tribe_callback( 'events-pro.editor.meta', 'register' ), 15 );

		tribe( 'events-pro.editor' )->hook();

		// Setup the registration of blocks
		add_action( 'tribe_editor_register_blocks', tribe_callback( 'events-pro.editor.blocks.fields', 'register' ) );
		add_action( 'tribe_editor_register_blocks', tribe_callback( 'events-pro.editor.blocks.related-events', 'register' ) );
	}

	/**
	 * Binds and sets up implementations at boot time.
	 *
	 * @since 4.5
	 */
	public function boot() {
		// no ops
	}
}
