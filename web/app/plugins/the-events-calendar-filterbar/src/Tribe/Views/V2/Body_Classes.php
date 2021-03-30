<?php
/**
 * Handles all body class functionality and hooking.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */

namespace Tribe\Events\Filterbar\Views\V2;

use Tribe\Utils\Body_Classes as Body_Classes_Object;

/**
 * Class managing body classes for the Views V2.
 *
 * @package Tribe\Events\Filterbar\Views\V2

 * @since   5.0.0
 */
class Body_Classes {
	/**
	 * Holds the allowed body classes for this object.
	 *
	 * @since 5.0.0
	 *
	 * @var array<string>
	 */
	public $body_classes = [
		'tribe-filters-closed',
		'tribe-filters-live-update',
		'tribe-filters-open',
	];

	/**
	 * Register our actions and filters.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required for Filterbar Views v2 body classes.
	 *
	 * @since 5.0.0
	 */
	protected function add_actions() {
		/**
		 * Run on 'wp' to be sure all functions we may rely on are available.
		 * High priority to ensure we go after TEC and ECP.
		 */
		add_action( 'wp', [ $this, 'add_body_classes' ], 100 );
	}

	/**
	 * Adds the filters required for Filterbar Views v2 body classes.
	 *
	 * @since 5.0.0
	 */
	protected function add_filters() {
		// Priority ensures we run after TEC & ECP.
		add_filter( 'tribe_body_class_should_add_to_queue', [ $this, 'filter_body_class_should_add_to_queue' ], 20, 3 );
		add_filter( 'tribe_body_classes_should_add', [ $this, 'filter_body_classes_should_add' ], 20, 4 );
	}

	/**
	 * Hook in and add FE body classes to the queue.
	 *
	 * @since 5.0.0
	 */
	public function add_body_classes() {
		/** @var Body_Classes_Object $body_classes */
		$body_classes = tribe( Body_Classes_Object::class );

		$body_classes->add_classes( $this->body_classes );
	}

	/**
	 * Contains the logic for if this object's classes should be added to the queue.
	 *
	 * @since 5.0.0
	 *
	 * @param boolean $add   Whether to add the class to the queue or not.
	 * @param string  $class The body class name to add.
	 * @param string  $queue The queue we want to get 'admin', 'display', 'all'.
	 *
	 * @return boolean Whether to add the class to the queue or not.
	 */
	public function filter_body_class_should_add_to_queue( $add, $class, $queue = 'display' ) {
		// Bail if it's not a class we care about.
		if ( ! in_array( $class, $this->body_classes ) ) {
			return $add;
		}

		// Bail on non-FE queues.
		if ( 'display' !== $queue ) {
			return false;
		}

		if ( ! $this->is_filterable_front_end() ) {
			return false;
		}

		// Per-class logic:
		if ( 'tribe-filters-live-update' === $class ) {
			return 'automatic' === tribe_get_option( 'liveFiltersUpdate', 'automatic' );
		}

		// The following checks only apply to vertical layout. So bail if we're not doing vertical.
		if ( Filters::LAYOUT_VERTICAL !== tribe( Filters::class )->get_layout_setting() ) {
			return false;
		}

		$init_closed = $this->get_init_closed();

		if ( 'tribe-filters-closed' === $class ) {
			return ! $init_closed;
		}

		if ( 'tribe-filters-open' === $class ) {
			return $init_closed;
		}

		// Fallback.
		return false;
	}

	/**
	 * Function for determining if we should add a open/closed class.
	 *
	 * @since 5.0.0
	 *
	 * @return boolean Should we add a open/closed class.
	 */
	protected function get_init_closed() {
		/**
		 * Allows filtering of whether vertical filters initially display closed.
		 *
		 * @since 4.9.3
		 * @since 5.0.0 moved to get_init_closed. Now defaults to false.
		 *
		 * @param bool $init_closed Boolean on whether to initially display vertical filters closed or not.
		 */
		 return tribe_is_truthy(
			 apply_filters(
				 'tribe_events_filter_bar_views_v2_vertical_init_closed',
				 false
			)
		);
	}

	/**
	 * Logic for whether we should add the classes to the body or not.
	 *
	 * @since 5.0.0
	 *
	 * @param boolean $add                     Whether to add classes or not.
	 * @param string  $unused_queue            The queue we want to get 'admin', 'display', 'all'.
	 * @param array   $add_classes             The array of body class names to add.
	 * @param array   $unused_existing_classes An array of existing body class names from WP.
	 *
	 * @return boolean Whether to add classes or not.
	 */
	public function filter_body_classes_should_add( $add, $unused_queue, $add_classes, $unused_existing_classes ) {
		// We want to be sure to add our classes,
		// they've already been checked for appropriateness when added to the queue.
		if ( ! empty( array_intersect( $this->body_classes, $add_classes ) ) ) {
			return true;
		}

		return $add;
	}

	/**
	 * Logic to determine if we are on a front-end page that Filter Bar is appropriate for.
	 *
	 * @since 5.0.0
	 *
	 * @return boolean Are we on a Filter Bar front-end page?
	 */
	public function is_filterable_front_end() {
		if ( is_admin() ) {
			return false;
		}

		// Filter Bar is not available on singles.
		if ( is_singular() ) {
			return false;
		}

		// Bail if we're not doing v2.
		if ( ! tribe_events_views_v2_is_enabled() ) {
			return false;
		}

		// Bail if we're not doing an event query.
		if ( ! tribe_is_event_query() ) {
			return false;
		}

		return true;
	}
}
