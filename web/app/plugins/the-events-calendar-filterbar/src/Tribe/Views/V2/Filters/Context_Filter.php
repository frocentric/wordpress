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

use Tribe\Events\Filterbar\Views\V2\Filters_Stack;
use Tribe__Context as Context;
use Tribe__Date_Utils as Dates;
use Tribe__Events__Filterbar__Filter as Filter;
use Tribe__Events__Template__Month as Month;
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
	 * A list of Filter classes and Context hashes to keep track of which filter did already contribute to the
	 * post IDs pool.
	 *
	 * @since 5.0.0.1
	 *
	 * @var array<string>
	 */
	protected static $post_ids_pools_contributors = [];
	/**
	 * A pool of post IDs shared among all the Context Filters.
	 *
	 * @since 5.0.0.1
	 *
	 * @var array<string<array<int>>
	 */
	protected static $post_ids_pools = [];
	/**
	 * Whether this filter is part of a Filter_Stack, and is managed by it, or not.
	 *
	 * @since 5.0.0.1
	 *
	 * @var bool
	 */
	public $stack_managed = false;
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
	 * A callback that will be called, if defined, on the filter data.
	 *
	 * @since 5.0.0
	 *
	 * @var callable
	 */
	protected $data_visitor;
	/**
	 * A callback that will be called on each filter display value, if set.
	 *
	 * @since 5.0.0
	 *
	 * @var callable
	 */
	protected $display_value_visitor;

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
		/** @var Context_Filter|Filter $filter */
		$filter = ( new \ReflectionClass( static::class ) )->newInstanceWithoutConstructor();

		if ( method_exists( $filter, 'set_meta_key' ) ) {
			$filter->set_meta_key( $context_key );
		}

		$name = self::get_filter_name( static::class, $context_key );

		$filter->set_context( $context );
		$filter->name = $name ?: $context_key;
		$filter->slug = $context_key;

		$filter->settings();

		$filter->type = self::get_filter_type( static::class, $context_key, $filter->type );

		$filter->priority = static::get_filter_priority( static::class, $context_key );

		$filter->isActiveFilter = true;

		list( $start, $end ) = static::view_start_end( $context );

		$use_filters_stack = $start instanceof \DateTimeInterface && $end instanceof \DateTimeInterface;

		/**
		 * Filters whether to use the Filter Stack or not.
		 *
		 * By default the Filter Stack will be used on Views fetching events day-by-day, e.g. Month and Week View
		 * that can provide valid start and end of a time interval.
		 *
		 * @since 5.0.0.1
		 *
		 * @param bool    $use_filters_stack Whether to use the Filter Stack or not.
		 * @param Context $context           The context the Filter is being built in.
		 */
		$use_filters_stack = apply_filters( 'tribe_events_filter_bar_use_stack', $use_filters_stack, $context );

		if ( ! $use_filters_stack ) {
			// Business as usual, the original implementation.
			$filter->setup_query_filters();
			// This MUST happen AFTER the `Filter::setup_query_filters` call.
			self::set_filter_current_value( $filter, $context, $context_key );
		} else {
			// This MUST happen BEFORE the `Filter_Stack::setup_query_filters` call.
			self::set_filter_current_value( $filter, $context, $context_key );
			$filter_stack = Filters_Stack::for_context_hash( $filter->context_hash );
			$filter_stack->attach( $filter );
			$filter_stack->setup_query_filters( $filter, $start, $end );
		}

		return $filter;
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
	 * Returns the key used to identify a filter options in the `tribe_events_filters_current_active_filters` option.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter fully-qualified class name.
	 * @param string $context_key  The context key used to identify the filter values.
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

		/**
		 * Allows filtering the option key map, so other plugins can add their own filters.
		 *
		 * @since 5.0.0
		 *
		 * @param array<string,string> $map The map of classes to option keys.
		 */
		$map = apply_filters( 'tribe_events_filter_bar_option_key_map', $map );

		return Arr::get( $map, $filter_class, $context_key );
	}

	/**
	 * Returns the default name of a Filter.
	 *
	 * @since 4.9.0
	 *
	 * @param string $filter_class The filter fully-qualified class name.
	 * @param string $context_key  The context key used to identify the filter values.
	 *
	 * @return string The filter name, or the input context key if not found.
	 */
	public static function get_filter_default_name( $filter_class, $context_key ) {
		$map = [
			'Tribe\Events\Filterbar\Views\V2\Filters\Additional_Field' => $context_key,
			'Tribe\Events\Filterbar\Views\V2\Filters\Category'         => sprintf( esc_html__( '%s Category',
				'tribe-events-filter-view' ), tribe_get_event_label_singular() ),
			'Tribe\Events\Filterbar\Views\V2\Filters\City'             => __( 'City', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Cost'             => __( 'Cost ($)', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Country'          => __( 'Country', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Day_Of_Week'      => __( 'Day', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Distance'         => __( 'Distance', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Featured_Events'  => sprintf( esc_html__( 'Featured %s',
				'tribe-events-filter-view' ), tribe_get_event_label_plural() ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Organizer'        => tribe_get_organizer_label_plural(),
			'Tribe\Events\Filterbar\Views\V2\Filters\State'            => __( 'State/Province',
				'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Tag'              => __( 'Tags', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Time_Of_Day'      => __( 'Time', 'tribe-events-filter-view' ),
			'Tribe\Events\Filterbar\Views\V2\Filters\Venue'            => tribe_get_venue_label_plural(),
		];

		/**
		 * Allows filtering the default names map, so other plugins can add their own filters.
		 *
		 * @since 5.0.0
		 *
		 * @param array<string,string> $map The map of classes to filter names.
		 */
		$map = apply_filters( 'tribe_events_filter_bar_default_filter_names_map', $map );

		return Arr::get( $map, $filter_class, $context_key );
	}

	/**
	 * Sets the Filter current value.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Filter  $filter      The Filter instance the value is being set for.
	 * @param Context $context     The current Filter Context.
	 * @param string  $context_key The Filter context key.
	 */
	protected static function set_filter_current_value(
		Filter $filter,
		Context $context,
		$context_key
	) {
		if ( ! is_string( $context_key ) ) {
			return;
		}

		$filter->currentValue = method_exists( $filter, 'parse_value' )
			? $filter->parse_value( $context->get( $context_key ) )
			: $context->get( $context_key, $context->get( 'tribe_' . $context_key, null ) );
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

		if (
			$this->currentValue === null
			|| ( is_array( $this->currentValue ) && count( $this->currentValue ) === 0 )
		) {
			// Bail from the filtering entirely if the values are not set.
			return;
		}

		$this->query = $query;

		if ( ! $this->stack_managed ) {
			$this->setup_join_clause();
			$this->setup_where_clause();
			$this->setup_query_args();

			if ( ! empty( $this->queryArgs ) ) {
				foreach ( $this->queryArgs as $key => $value ) {
					$query->set( $key, $value );
				}
			}
		}

		if ( ! empty( $this->joinClause ) ) {
			add_filter( 'posts_join', array( $this, 'addQueryJoin' ), 11, 2 );
		}

		if ( ! empty( $this->whereClause ) ) {
			add_filter( 'posts_where', array( $this, 'addQueryWhere' ), 11, 2 );
		}

		// We need this to make sure filters will apply.
		$query->tribe_is_event = true;

		$this->pre_get_posts( $query );
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
	 * Sets the Filter data visitor that will be used to modify the filter data.
	 *
	 * The visitor callback should have signature `function( array &$field_data ) : void`; the data should be modified
	 * by reference.
	 *
	 * @since 5.0.0
	 *
	 * @param callable $data_visitor The Filter field data visitor.
	 */
	public function set_data_visitor( callable $data_visitor ) {
		$this->data_visitor = $data_visitor;
	}

	/**
	 * Sets the display value visitor that will be used to modify the filter display value.
	 *
	 * The visitor callback should have signature `function( string $display_value, Filter $filter ) : string`.
	 *
	 * @since 5.0.0
	 *
	 * @param callable $display_value_visitor The Filter display value visitor.
	 */
	public function set_display_value_visitor( callable $display_value_visitor ) {
		$this->display_value_visitor = $display_value_visitor;
	}

	/**
	 * Overrides the base method to apply a series of modification to adapt the field definition format.
	 *
	 * If a data visitor is set, then the method will call it on each entry of the filter data, else the data
	 * will be left untouched.
	 *
	 * The visitor callback should have signature `function( array &$field_data ) : void`; the data should be modified
	 * by reference.
	 * The reason we're not using the Filter API for this is that we need the data to conform before filtering it and
	 * want to avoid the possible issues w/ filters (the WordPress ones).
	 *
	 * @since 5.0.0
	 *
	 * @return array<string,array> The filter fields by data type, modified if required.
	 */
	public function get_fields_data_by_type() {
		$fields_data = parent::get_fields_data_by_type();

		if ( is_callable( $this->data_visitor ) ) {
			array_walk( $fields_data, $this->data_visitor );
		}

		return $fields_data;
	}

	/**
	 * Overrides the base method to modify the display value by means of a display value visitor callback if set.
	 *
	 * The visitor callback should have signature `function( string $display_value, Filter $filter ) : string`.
	 * The reason we're not using the Filter API for this is that we need the data to conform before filtering it and
	 * want to avoid the possible issues w/ filters (the WordPress ones).
	 *
	 * @since 5.0.0
	 *
	 * @return string|null The updated display value.
	 */
	public function get_current_value_for_display() {
		$display_value = parent::get_current_value_for_display();

		if ( is_callable( $this->display_value_visitor ) ) {
			$display_value = call_user_func( $this->display_value_visitor, $display_value, $this );
		}

		return $display_value;
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
	 * Checks all pre-conditions for a range type of filter are met.
	 *
	 * This method is an override of the base one to update the check to the logic used by Filter Bar in Views v2
	 * context.
	 *
	 * @since 5.0.0
	 *
	 * @return bool Whether all pre-conditions for a range type of filter are met.
	 */
	protected function check_range_pre_conditions() {
		return isset( $this->values['min'], $this->values['max'] );
	}

	/**
	 * Returns the current Filter Context, if any.
	 *
	 * @since 5.0.0.1
	 *
	 * @return Context|null The current Filter context, if any.
	 */
	public function get_context() {
		return $this->context;
	}

	/**
	 * Builds and returns the tuple representing a View query interval start and end.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Context|null $context The context to fetch the View and the Date from.
	 *
	 * @return array<\DateTimeInterface|false> Either the dates tuple, or `false` tuple on failure.
	 */
	public static function view_start_end( Context $context = null ) {
		if ( null === $context ) {
			return [ false, false ];
		}

		$view = $context->get( 'view', false );

		$map = [
			'month' => static function ( $date ) {
				return [
					tribe_beginning_of_day( Month::calculate_first_cell_date( $date ) ),
					tribe_end_of_day( Month::calculate_final_cell_date( $date ) ),
				];
			},
			'week'  => static function ( $date ) use ( $context ) {
				return Dates::get_week_start_end( $date, (int) $context->get( 'start_of_week', 0 ) );
			},
		];

		/**
		 * Filters the map of callables that should be used to calculate a View interval start and end.
		 *
		 * @since 5.0.0.1
		 *
		 * @param array<string,callable> $map     A list callbacks, each one will receive the Date as input.
		 * @param Context                $context The context that provided the View and Date.
		 */
		$map = apply_filters( 'tribe_events_filter_bar_start_end_calculate_map', $map, $context );

		if ( ! isset( $map[ $view ] ) ) {
			return [ false, false ];
		}

		$dates = call_user_func( $map[ $view ], $context->get( 'event_date', false ) );

		if ( 2 !== count( array_filter( $dates ) ) ) {
			return [ false, false ];
		}

		return array_map( [ Dates::class, 'build_date_object' ], $dates );
	}

	/**
	 * Returns the current filter meta key.
	 *
	 * @since 5.0.5
	 *
	 * @return string The current filter meta key.
	 */
	public function get_meta_key() {
		return $this->meta_key;
	}
}
