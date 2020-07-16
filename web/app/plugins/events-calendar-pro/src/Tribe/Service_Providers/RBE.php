<?php
/**
 * Binds and manages the classes and hooks the filters needed for Recurrence Backend Engine v1 to work.
 *
 * @since 4.7
 */

/**
 * Class Tribe__Events__Pro__Service_Providers__RBE
 *
 * @since 4.7
 */
class
Tribe__Events__Pro__Service_Providers__RBE extends tad_DI52_ServiceProvider {
	const OPTION_NAME = 'rbe_engine';
	const VERSION_1 = 'v1';
	const NONE = 'none';

	/**
	 * The slug of the engine currently in use.
	 *
	 * @var string
	 */
	protected $in_use = 'none';

	/**
	 * Binds the classes and hooks the filters needed for Recurrence Backend Engine v1 to work.
	 */
	public function register() {
		// Register this very service provider in the container to make it accessible to the rest of the code.
		$this->container->singleton( 'events-pro.recurrence-backend-engines', $this );

		$this->hook_common_filters();

		$in_use = $this->get_in_use();
		$engine = $this->make_engine( $in_use );
		$engine->hook();
	}

	/**
	 * Hooks the filters that all Recurrence Backend Engines will use to work.
	 *
	 * Many filters are kept for back-compatibility reasons and different RBE
	 * implementations might not make use of them.
	 *
	 * @since 4.7
	 */
	protected function hook_common_filters() {
		$main = Tribe__Events__Pro__Main::instance();
		// Init the common functions.
		Tribe__Events__Pro__Recurrence__Meta::init( false );
		add_action( 'tribe_events_parse_query', array( $main, 'parse_query' ) );
	}

	/**
	 * Returns the slug, if any, of the engine currently in use.
	 *
	 * The value will be read from the `rbe_engine` Tribe option.
	 *
	 * @since 4.7
	 *
	 * @return string|null The slug of the engine currently in use.
	 */
	public function get_in_use() {
		$slug = tribe_get_option( self::OPTION_NAME, self::VERSION_1 );

		if ( ! array_key_exists( $slug, $this->get_engines_map() ) ) {
			// If the engine that  should be in use is not registered then let's use none.
			return self::NONE;
		}

		return $slug;
	}

	/**
	 * Sets the engine currently in use.
	 *
	 * The method will update the value of the `rbe_engine` Tribe option.
	 *
	 * @since 4.7
	 *
	 * @param string|null $in_use Either the string representing an engine or `null` if no engine is in use.
	 *
	 * @return string The slug of the engine in use.
	 */
	public function set_in_use( $in_use = null ) {
		$engines_map      = $this->get_engines_map();
		$currently_in_use = $this->get_in_use();

		if ( ! array_key_exists( $in_use, $engines_map ) || $currently_in_use === $in_use ) {
			// Let's not change to an engine that does not exist.
			return $currently_in_use;
		}

		$current = $engines_map[ $this->get_in_use() ];
		$current = $current instanceof Tribe__Events__Pro__Recurrence__Engines__Engine_Interface
			? $current
			: $this->container->make( $current );

		$unhooked = $current->unhook();

		if ( false === $unhooked ) {
			// For some reasons the current engine cannot be unhooked, return its slug.
			return $current->get_slug();
		}

		try {
			$engine = $engines_map[ $in_use ];
			$engine = $engine instanceof Tribe__Events__Pro__Recurrence__Engines__Engine_Interface
				? $engine
				: $this->container->make( $engine );
			$hooked = $engine->hook();
		} catch ( Exception $e ) {
			// Something went wrong; let's hook none.
			tribe_update_option( self::OPTION_NAME, self::NONE );

			return self::NONE;
		}

		$now_in_use = empty( $hooked ) ? self::NONE : $engine->get_slug();
		tribe_update_option( self::OPTION_NAME, $now_in_use );

		return $now_in_use;
	}

	/**
	 * Returns the filtered list of available recurrence backend engines.
	 *
	 * @since 4.7
	 *
	 * @return array The filtered map of recurrence backend engines slugs and classes.
	 */
	public function get_engines_map() {
		$map = array(
			self::NONE      => 'Tribe__Events__Pro__Recurrence__Engines__Null',
			self::VERSION_1 => 'Tribe__Events__Pro__Recurrence__Engines__Version_1',
		);

		/**
		 * Filters the slugs and classes of the recurrence backend engines available.
		 *
		 * @since 4.7
		 *
		 * @param array $map A map in the shape [ <slug> => <class> ].
		 */
		return apply_filters( 'tribe_events_pro_recurrence_backend_engines', $map );
	}

	/**
	 * Builds and returns an instance of the engine for the specified slug.
	 *
	 * @since 4.7
	 *
	 * @param string $slug The slug of the engine to build.
	 *
	 * @return mixed|Tribe__Events__Pro__Recurrence__Engines__Engine_Interface The engine for the slug or the Null
	 *                                                                         engine if the slug does not map to any
	 *                                                                         existing engine.
	 */
	public function make_engine( $slug ) {
		$engines_map = $this->get_engines_map();
		if ( ! array_key_exists( $slug, $engines_map ) ) {
			return new Tribe__Events__Pro__Recurrence__Engines__Null();
		}

		return $this->container->make( $engines_map[ $slug ] );
	}
}
