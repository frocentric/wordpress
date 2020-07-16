<?php
/**
 * Provides methods to implement a filter that can be used as a single instance and that is attached to a specific
 * context.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

use Tribe__Context as Context;
use Tribe__Utils__Array as Arr;

/**
 * Trait Context_Filter
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
trait Context_Filter {

	/**
	 * The hash string of the context this filter is attached to.
	 *
	 * @since 4.9.0
	 *
	 * @var string
	 */
	protected $context_hash;
	/**
	 * The Context instance this filter is attached to, if any.
	 *
	 * @since 4.9.0
	 *
	 * @var Context
	 */
	protected $context;

	/**
	 * The current WP_Query object the filter is working on.
	 *
	 * This will be set in the `filter_query` method, not earlier.
	 *
	 * @since 4.9.0
	 *
	 * @var \WP_Query
	 */
	protected $query;

	/**
	 * Builds and returns an instance of this filter (the one using this trait) specifically for the specified Context.
	 *
	 * @since 4.9.0
	 *
	 * @param Context $context     The context to attach the filter instance to.
	 * @param string  $context_key The context key the filter should use to fetch its values.
	 *
	 * @return object An instance of this filter.
	 *
	 * @throws \ReflectionException In the (unlikely) case
	 */
	public static function build_for_context( Context $context, $context_key ) {
		$instance = ( new \ReflectionClass( static::class ) )->newInstanceWithoutConstructor();

		$name = self::get_filter_name( static::class, $context_key );

		$instance->set_context( $context );
		$instance->name = $name ?: $context_key;
		$instance->slug = $context_key;

		$instance->settings();

		$instance->type = self::get_filter_type( static::class, $context_key, $instance->type );

		$instance->priority = static::get_filter_priority( static::class, $context_key );

		$instance->isActiveFilter = true;

		$instance->setup_query_filters();

		$instance->currentValue = method_exists( $instance, 'parse_value' )
			? $instance->parse_value( $context->get( $context_key ) )
			: $context->get( $context_key );

		return $instance;
	}

	/**
	 * Sets the instance of the Context this filter is attached to.
	 *
	 * @since 4.9.0
	 *
	 * @param Context|null $context The Context instance this filter instance is attached to.
	 */
	public function set_context( Context $context = null ) {
		$this->context      = $context;
		$this->context_hash = spl_object_hash( $context );
	}

	/**
	 * Filters the query to attach the filter query modifications.
	 *
	 * The query will be filtered only if the context hash on the query is the same as the one of this filter.
	 *
	 * @since 4.9.0
	 *
	 * @param \WP_Query $query The query to manipulate.
	 */
	public function filter_query( \WP_Query $query ) {
		if ( ! $this->is_correct_context( $query ) ) {
			return;
		}

		if ( $this->currentValue === null ) {
			// Bail from the filtering entirely if the values are not set.
			return;
		}

		$this->query = $query;

		$this->setup_join_clause();
		$this->setup_where_clause();
		$this->setup_query_args();

		if ( ! empty( $this->joinClause ) ) {
			add_filter( 'posts_join', array( $this, 'addQueryJoin' ), 11, 2 );
		}

		if ( ! empty( $this->whereClause ) ) {
			add_filter( 'posts_where', array( $this, 'addQueryWhere' ), 11, 2 );
		}

		if ( ! empty( $this->queryArgs ) ) {
			foreach ( $this->queryArgs as $key => $value ) {
				$query->set( $key, $value );
			}
		}

		// We need this to make sure filters will apply.
		$query->tribe_is_event = true;

		$this->pre_get_posts( $query );
	}

	/**
	 * Checks whether the current filter is already filtering a query or not.
	 *
	 * @since 4.9.0
	 *
	 * @return bool Whether the current filter is already filtering a query or not.
	 */
	protected function has_query() {
		return null !== $this->query;
	}

	/**
	 * Checks whether the query context is the one this filter is attached to or not.
	 *
	 * @since 4.9.0
	 *
	 * @param \WP_Query $query The query object to check.
	 *
	 * @return bool Whether the query context is the one this filter is attached to or not.
	 */
	protected function is_correct_context( \WP_Query $query ) {
		$context_hash = $query->get( 'context_hash', false );

		return $context_hash && $context_hash === $this->context_hash;
	}

	/**
	 * Returns the key used to identify a filter options in the `tribe_events_filters_current_active_filters` option.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter fully-qualified class name.
	 * @param string $context_key The context key used to identify the filter values.
	 *
	 * @return string The filter option key, or the input context key if not found.
	 */
	public static function get_filter_option_key( $filter_class, $context_key ) {
		$map = [
			'Tribe\Events\Filterbar\Views\V2\Filters\Additional_Field' => $context_key,
			'Tribe\Events\Filterbar\Views\V2\Filters\Category'         => 'eventcategory',
			'Tribe\Events\Filterbar\Views\V2\Filters\City'             => 'city',
			'Tribe\Events\Filterbar\Views\V2\Filters\Cost'             => 'cost',
			'Tribe\Events\Filterbar\Views\V2\Filters\Country'          => 'country',
			'Tribe\Events\Filterbar\Views\V2\Filters\Day_Of_Week'      => 'dayofweek',
			'Tribe\Events\Filterbar\Views\V2\Filters\Distance'         => 'geofence',
			'Tribe\Events\Filterbar\Views\V2\Filters\Featured_Events'  => 'featuredevent',
			'Tribe\Events\Filterbar\Views\V2\Filters\Organizer'        => 'organizers',
			'Tribe\Events\Filterbar\Views\V2\Filters\State'            => 'state',
			'Tribe\Events\Filterbar\Views\V2\Filters\Tag'              => 'tags',
			'Tribe\Events\Filterbar\Views\V2\Filters\Time_Of_Day'      => 'timeofday',
			'Tribe\Events\Filterbar\Views\V2\Filters\Venue'            => 'venues',
		];

		return Arr::get( $map, $filter_class, $context_key );
	}

	/**
	 * Returns the default name of a Filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter fully-qualified class name.
	 * @param string $context_key The context key used to identify the filter values.
	 *
	 * @return string The filter name, or the input context key if not found.
	 */
	public static function get_filter_default_name( $filter_class, $context_key ) {
		$map = [
			'Tribe\Events\Filterbar\Views\V2\Filters\Additional_Field' => $context_key,
			'Tribe\Events\Filterbar\Views\V2\Filters\Category'         => sprintf( esc_html__( '%s Category', 'tribe-events-filter-view' ), tribe_get_event_label_singular() ),
			'Tribe\Events\Filterbar\Views\V2\Filters\City'             => __( 'City', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Cost'             => __( 'Cost ($)', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Country'          => __( 'Country', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Day_Of_Week'      => __( 'Day', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Distance'         => __( 'Distance', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Featured_Events'  => sprintf( esc_html__( 'Featured %s', 'tribe-events-filter-view' ), tribe_get_event_label_plural() ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Organizer'        => tribe_get_organizer_label_plural(),
			'Tribe\Events\Filterbar\Views\V2\Filters\State'            => __( 'State/Province', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Tag'              => __( 'Tags', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Time_Of_Day'      => __( 'Time', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Venue'            => tribe_get_venue_label_plural(),
		];

		return Arr::get( $map, $filter_class, $context_key );
	}

	/**
	 * Returns the pretty, human-readable title the user assigned to the filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter class fully-qualified name.
	 * @param string $context_key  The context key currently associated to the filter.
	 *
	 * @return string The filter title, if existing, active and set; the input context key otherwise.
	 */
	protected static function get_filter_name( $filter_class, $context_key ) {
		$active_filters = (array) \Tribe__Events__Filterbar__View::instance()->get_filter_settings();
		$key            = static::get_filter_option_key( $filter_class, $context_key );
		$default_name   = static::get_filter_default_name( $filter_class, $context_key );

		return Arr::get( $active_filters, [ $key, 'title' ], $default_name );
	}

	/**
	 * Whether a filter is available or not.
	 *
	 * This checks whether the user made the Filter available via the Events > Settings > Filters > Available Filters
	 * option or not.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter class fully-qualified name.
	 * @param string $context_key  The context key currently associated to the filter.
	 *
	 * @return bool Whether the filter is available or not.
	 */
	public static function is_available( $filter_class, $context_key ) {
		$filter_option_key = static::get_filter_option_key( $filter_class, $context_key );
		$active_filters    = (array) get_option( 'tribe_events_filters_current_active_filters', [] );

		// Everything is active by default.
		if ( empty( $active_filters ) ) {
			return true;
		}

		return array_key_exists( $filter_option_key, $active_filters );
	}

	/**
	 * Returns the type of field assigned by the user to a filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter class fully-qualified name.
	 * @param string $context_key  The context key currently associated to the filter.
	 * @param string $default_type The default type used by this filter option.
	 *
	 * @return int The filter priority.
	 */
	protected static function get_filter_type( $filter_class, $context_key, $default_type ) {
		$active_filters = (array) get_option( 'tribe_events_filters_current_active_filters', [] );
		$key            = static::get_filter_option_key( $filter_class, $context_key );

		return Arr::get( $active_filters, [ $key, 'type' ], $default_type );
	}

	/**
	 * Returns the priority assigned by the user to a filter.
	 *
	 * The priority is the one the user will implicitly assign to each filter when adding them to the list of available
	 * filters and sorting them. Filters with lower priorities will show first.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter class fully-qualified name.
	 * @param string $context_key  The context key currently associated to the filter.
	 *
	 * @return int The filter priority.
	 */
	protected static function get_filter_priority( $filter_class, $context_key ) {
		$active_filters = (array) get_option( 'tribe_events_filters_current_active_filters', [] );
		$key            = static::get_filter_option_key( $filter_class, $context_key );

		return (int) Arr::get( $active_filters, [ $key, 'priority' ], 1 );
	}

	/**
	 * Returns an array of the currently available additional fields.
	 *
	 * Available additional fields are the ones the user made available in the Events > Settings > Filters > Available
	 * Filters section.
	 *
	 * @since 4.9.0
	 *
	 * @return array An array of the currently available additional fields filters slugs, in the shape
	 *              `[ '_ecp_custom_1', '_ecp_custom_2', ... ]`.
	 */
	public static function get_available_additional_fields() {
		$active_filters = (array) get_option( 'tribe_events_filters_current_active_filters', [] );

		return array_filter( array_keys( $active_filters ), static function ( $key ) {
			return 0 === strpos( $key, '_ecp_custom_' );
		} );
	}
}
