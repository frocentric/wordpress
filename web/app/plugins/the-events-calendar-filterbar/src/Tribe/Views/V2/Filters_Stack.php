<?php
/**
 * Models and manages a stack of Filters that are applied together.
 *
 * @since   5.0.0.1
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */

namespace Tribe\Events\Filterbar\Views\V2;

use Tribe\Events\Filterbar\Views\V2\Filters\Context_Filter;
use Tribe__Context as Context;
use Tribe__Date_Utils as Dates;
use Tribe__Events__Filterbar__Filter as Filter;

/**
 * Class Filters_Stack
 *
 * @since   5.0.0.1
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */
class Filters_Stack {
	/**
	 * A SQL comment that will be added inline to the query to ear-mark the query as filtered by the stack.
	 *
	 * While the Filter Stack filtering and modification of the query could be detected using its `where_clause`, the
	 * comment is a static indication of any filtering happening at all and does not require computation to be spotted.
	 *
	 * @since 5.0.0.1
	 *
	 * @var string
	 */
	public static $query_id_comment = '/* FBAR/Filter_Stack > */';

	/**
	 * An map that contains the stacks built for each Context hash.
	 *
	 * @since 5.0.0.1
	 *
	 * @var array<Filters_Stack>
	 */
	protected static $stacks = [];

	/**
	 * An array cache that will store the current post IDs pool for the stack.
	 *
	 * Its `null` value should be used as an indication the pool has not been initialized yet.
	 *
	 * @since 5.0.0.1
	 *
	 * @var null|array<int>
	 */
	protected $post_ids_pool;

	/**
	 * A map that will map Filter instances to an array of their Reflection properties and methods.
	 * @var \SplObjectStorage<Filter,array<string,\ReflectionProperty|\ReflectionMethod>>
	 */
	protected $filters;

	/**
	 * The Context hash this filter stack is for.
	 *
	 * @since 5.0.0.1
	 *
	 * @var string
	 */
	protected $context_hash;

	/**
	 * A list of all the Filters that, in this stack, have already contributed to the post IDs pool.
	 *
	 * @since 5.0.0.1
	 *
	 * @var \SplObjectStorage<Filter>
	 */
	protected $pool_contributors;

	/**
	 * An array cache of primed matching post IDs pools.
	 *
	 * @since 5.0.0.1
	 *
	 * @var array<string,array<int>>
	 */
	protected $filter_pools = [];

	/**
	 * The current Filter Stack WHERE clause.
	 *
	 * This will be updated on each call to the `setup_query_filters` method.
	 *
	 * @since 5.0.0.1
	 *
	 * @var  string|null
	 */
	protected $where_clause;

	/**
	 * Filters_Stack constructor.
	 *
	 * @since 5.0.0.1
	 *
	 * @param string $context_hash The Context hash of this filter stack.
	 */
	public function __construct( $context_hash ) {
		$this->context_hash      = $context_hash;
		$this->filters           = new \SplObjectStorage();
		$this->pool_contributors = new \SplObjectStorage();
	}

	/**
	 * Returns, building it if required, an instance of the Filters Stack for a specific Context hash.
	 *
	 * @since 5.0.0.1
	 *
	 * @param string $context_hash The Context hash to build the Filters Stack for.
	 */
	public static function for_context_hash( $context_hash ) {
		if ( ! isset( static::$stacks[ $context_hash ] ) ) {
			$stack                           = new static( $context_hash );
			static::$stacks[ $context_hash ] = $stack;
		} else {
			$stack = static::$stacks[ $context_hash ];
		}

		return $stack;
	}

	/**
	 * Sets up the query filters for each Filter part of the stack.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Filter|Context_Filter $filter The filter to add to the stack and set up the query filters for.
	 * @param \DateTimeInterface    $start  The View interval start date.
	 * @param \DateTimeInterface    $end    The View interval end date.
	 *
	 * @return bool Whether the Filter queries were correctly set up in the context of the Stack or not.
	 */
	public function setup_query_filters( Filter $filter, \DateTimeInterface $start, \DateTimeInterface $end ) {
		if ( ! $this->filters->contains( $filter ) || empty( $filter->currentValue ) ) {
			// The filter is not part of the stack, return.
			return false;
		}

		if ( $this->pool_contributors->contains( $filter ) && true !== $this->pool_contributors[ $filter ] ) {
			// This filter already contributed to post ID pool for this context hash, bail.
			$filter->stack_managed = true;

			return true;
		}

		if ( $this->post_ids_pool !== null && count( $this->post_ids_pool ) === 0 ) {
			$filter->stack_managed = true;
			// We know we'll not find anything.
			$this->void_query( $filter );

			return true;
		}

		$pool_key = get_class( $filter ) . '|' . $start->format( Dates::DBDATEFORMAT ) . '|' . $end->format( Dates::DBDATEFORMAT );

		if ( ! isset( $this->filter_pools[ $pool_key ] ) ) {
			$batch_size = 5000;
			/** @var \WP_Query $query */
			$query      = tribe_events()
				->where( 'date_overlaps', $start, $end, null, 2 )
				->per_page( $batch_size )
				->set_found_rows( true )
				->fields( 'ids' )
				->get_query();
			$query->set( 'context_hash', $this->context_hash );
			$query->set( 'paged', 1 );
			$query->tribe_is_event = true;
			$filter->filter_query( $query );

			if ( ! is_null( $this->post_ids_pool ) ) {
				$query->set( 'post__in', $this->post_ids_pool );
			}

			do {
				$page_matches[] = $query->get_posts();
				$query->set( 'paged', $query->get( 'paged', 1 ) + 1 );
			} while ( $query->max_num_pages > count( $page_matches ) );

			$this->filter_pools[ $pool_key ] = array_unique( array_merge( ...$page_matches ), SORT_NUMERIC );
		}

		$matching_ids = $this->filter_pools[ $pool_key ];

		$this->pool_contributors[ $filter ] = true;

		if ( $this->post_ids_pool === null ) {
			// First filter in the stack: initialise the post ID pool.
			$this->post_ids_pool = array_filter( array_map( 'absint', $matching_ids ) );
		} else {
			$this->post_ids_pool = array_intersect( $this->post_ids_pool, $matching_ids );
		}

		if ( count( $this->post_ids_pool ) === 0 ) {
			$filter->stack_managed = true;
			// No post IDs matching this filter query or the intersection with other filters, void.
			$this->void_query( $filter );

			return true;
		}

		/*
		 * Setting a filter `joinClause` and `whereClause` properties to an empty string will prevent the filters
		 * from filtering the query at all. We leverage this here to only attach one filter, this last one.
		 */
		$this->empty_all();

		/** @var \ReflectionMethod $setup_query_args */
		/** @var \ReflectionProperty $join_clause */
		/** @var \ReflectionProperty $where_clause */
		list( $setup_query_args, $join_clause, $where_clause ) = array_values( $this->filters[ $filter ] );

		$setup_query_args->invoke( $filter );

		global $wpdb;

		// No need to JOIN on any table, the stack will operate on the posts table, already part of the query.
		$join_clause->setValue( $filter, '' );
		// Include only the matching post IDs.
		$ids_interval       = implode( ',', array_map( static function ( $id ) use ( $wpdb ) {
			return $wpdb->prepare( '%d', $id );
		}, $this->post_ids_pool ) );
		$comment            = static::$query_id_comment;
		$this->where_clause = " {$comment} AND {$wpdb->posts}.ID IN ( {$ids_interval} )";
		$where_clause->setValue( $filter, $this->where_clause );
		$filter->stack_managed = true;

		return true;
	}

	/**
	 * Uses a filter to void the query by appending a WHERE clause that will always fail.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Filter $filter
	 */
	protected function void_query( Filter $filter ) {
		// Void all filters first.
		$this->empty_all();
		// No JOIN required.
		$join_clause1 = $this->filters[ $filter ]['joinClause'];
		$join_clause1->setValue( $filter, '' );
		// Add a clause to WHERE that will make any query fail.
		$where_clause1      = $this->filters[ $filter ]['whereClause'];
		$comment            = static::$query_id_comment;
		$this->where_clause = " {$comment} AND 1=0";
		$where_clause1->setValue( $filter, $this->where_clause );
	}

	/**
	 * Empties the JOIN and WHERE clauses of all the Filters in the stack.
	 *
	 * @since 5.0.0.1
	 */
	protected function empty_all() {
		foreach ( $this->filters as $filter_object ) {
			$join_clause1 = $this->filters[ $filter_object ]['joinClause'];
			$join_clause1->setValue( $filter_object, '' );
			$where_clause1 = $this->filters[ $filter_object ]['whereClause'];
			$where_clause1->setValue( $filter_object, '' );
		}
	}

	/**
	 * Sets up the Reflection properties and methods required to control the filter part of the stack.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Filter $filter The filter to attach to the stack.
	 *
	 * @return bool Whether the Filter instance was attached to the Filter Stack or not.
	 */
	public function attach( Filter $filter ) {
		// The relevant filter methods and properties might not be accessible, use Reflection to access them.
		try {
			$setup_query_args_method = new \ReflectionMethod( $filter, 'setup_query_args' );
			$setup_query_args_method->setAccessible( true );
			$join_clause_prop = new \ReflectionProperty( $filter, 'joinClause' );
			$join_clause_prop->setAccessible( true );
			$where_clause_prop = new \ReflectionProperty( $filter, 'whereClause' );
			$where_clause_prop->setAccessible( true );
		} catch ( \ReflectionException $e ) {
			// The filter is not conform to what we expect, bail and continue.
			return false;
		}

		if ( ! (
			method_exists( $filter, 'get_context' )
			&& $filter->get_context() instanceof Context
		) ) {
			return false;
		}

		// Store access to the Reflection properties and methods we'll need to control.
		$this->filters[ $filter ] = [
			'setup_query_args' => $setup_query_args_method,
			'joinClause'       => $join_clause_prop,
			'whereClause'      => $where_clause_prop,
		];

		return true;
	}

	/**
	 * Returns whether a Filter instance is part of the Stack or not.
	 *
	 * @since 5.0.0.1
	 *
	 * @param Filter $filter The Filter instance to check.
	 *
	 * @return bool Whether a Filter instance is part of the Stack or not.
	 */
	public function contains( Filter $filter ) {
		return $this->filters->contains( $filter );
	}

	/**
	 * Returns the current WHERE clause for the stack.
	 *
	 * The WHERE clause is updated whenever a Filter is set up.
	 *
	 * @since 5.0.0.1
	 *
	 * @return string|null The current Stack WHERE clause, or `null` if the stack is not adding any WHERE clause to
	 *                     queries.
	 */
	public function get_where_clause() {
		return $this->where_clause;
	}

	/**
	 * Returns a map, an SplObjectStorage, relating each filter to its current JOIN clause.
	 *
	 * @since 5.0.0.1
	 *
	 * @return \SplObjectStorage<Filter,string> A map relating each filter to its current JOIN clause.
	 */
	public function get_filters_join_clauses() {
		return $this->get_filters_prop( 'joinClause' );
	}

	/**
	 * Builds an object storage to map each Filter part of the stack to the specified property.
	 *
	 * @since 5.0.0.1
	 *
	 * @param string $prop_name The name of the property to return the value for.
	 *
	 * @return \SplObjectStorage<Filter,string> A map relating each filter to its current WHERE clause.
	 */
	protected function get_filters_prop( $prop_name ) {
		$storage = new \SplObjectStorage();
		foreach ( $this->filters as $filter ) {
			/** @var \ReflectionProperty $prop */
			$prop = $this->filters[ $filter ][ $prop_name ];
			$storage->attach( $filter, $prop->getValue( $filter ) );
		}

		return $storage;
	}

	/**
	 * Returns a map, an SplObjectStorage, relating each filter to its current WHERE clause.
	 *
	 * @since 5.0.0.1
	 *
	 * @return \SplObjectStorage<Filter,string> A map relating each filter to its current WHERE clause.
	 */
	public function get_filters_where_clauses() {
		return $this->get_filters_prop( 'whereClause' );
	}

	/**
	 * Returns the current matching post IDs pool.
	 *
	 * @since 5.0.5
	 *
	 * @return array<int>|null Either an array of the posts matching the current filter pool, or `null` to indicate the
	 *                         matching post IDs pool has not been initialized yet.
	 */
	public function get_post_ids_pool() {
		return $this->post_ids_pool;
	}
}
