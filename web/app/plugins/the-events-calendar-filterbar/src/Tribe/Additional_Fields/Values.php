<?php
/**
 * Tools to efficiently grab an accurate list of assigned values for a post meta field.
 */
class Tribe__Events__Filterbar__Additional_Fields__Values {
	const CACHE_KEY   = 'additional_field_values';
	const CACHE_GROUP = 'tribe_events_filterbar';

	/**
	 * Default period of time to cache meta value lists for.
	 *
	 * @var int
	 */
	protected static $cache_expiry = 14400; // 4hrs


	/**
	 * Setup cache management and invalidation.
	 */
	public static function init() {
		/**
		 * Controls the length of time in seconds for which meta value lists are
		 * maintained before automatically being invalidated.
		 *
		 * @var int $cache_expiry
		 */
		self::$cache_expiry = (int) apply_filters( 'tribe_events_filterbar_additional_fields_cache_expiry', self::$cache_expiry );

		// We invalidate the entire cache any time an event is updated, created or deleted or when event
		add_action( 'save_post_' . Tribe__Events__Main::POSTTYPE, array( __CLASS__, 'cache_invalidate' ) );
		add_action( 'deleted_post', array( __CLASS__, 'cache_invalidate_on_event_deletion' ) );
		add_action( 'added_post_meta', array( __CLASS__, 'cache_invalidate_on_meta_change' ), 10, 2 );
		add_action( 'updated_post_meta', array( __CLASS__, 'cache_invalidate_on_meta_change' ), 10, 2 );
		add_action( 'deleted_post_meta', array( __CLASS__, 'cache_invalidate_on_meta_change' ), 10, 2 );
	}

	/**
	 * Returns a list of unique meta values relating to the specified meta key.
	 *
	 * @param string $meta_key
	 *
	 * @return array
	 */
	public static function fetch( $meta_key ) {
		global $wpdb;

		$statuses = self::get_post_statuses( $meta_key );
		$cached   = self::cache_get( $meta_key, $statuses );

		if ( $cached ) {
			return $cached;
		}

		$post_statuses = self::prepare_statuses( $statuses );

		$and_post_status_set = ! empty( $post_statuses )
			? " AND post_status IN ( $post_statuses ) "
			: '';

		$query = "
			SELECT     DISTINCT( meta_value )
			FROM       $wpdb->postmeta
			INNER JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->postmeta.post_id
			WHERE      $wpdb->postmeta.meta_key = %s
			  AND      $wpdb->posts.post_type = %s
			           $and_post_status_set
		";

		/**
		 * Allows filtering the additional fields query
		 *
		 * @param string The SQL query
		 * @since 4.5.8
		 *
		 */
		$query = apply_filters( 'tribe_events_filter_additional_fields_query', $query );

		$values = (array) $wpdb->get_col( $wpdb->prepare(
			$query, $meta_key, Tribe__Events__Main::POSTTYPE
		) );

		self::cache_set( $meta_key, $statuses, $values );
		return $values;
	}


	/**
	 * Returns a list of post statuses to apply when querying for assigned meta values.
	 *
	 * @param string $meta_key
	 *
	 * @return array
	 */
	public static function get_post_statuses( $meta_key ) {
		$statuses = is_user_logged_in()
			? array( 'publish', 'private' )
			: array( 'publish' );

		/**
		 * When generating a list of currently-assigned values for a given meta key, only
		 * posts set to these statuses will be considered.
		 *
		 * @var array $statuses
		 */
		return (array) apply_filters( 'tribe_events_filter_additional_field_post_statuses', $statuses, $meta_key );
	}

	/**
	 * Returns a comma separated, prepared list of post statuses that can be used
	 * directly within a SQL (NOT) IN clause when querying for assigned meta values.
	 *
	 * @param array $statuses
	 *
	 * @return string
	 */
	protected static function prepare_statuses( array $statuses ) {
		global $wpdb;

		$prepared_statuses = $statuses;

		foreach ( $prepared_statuses as &$status ) {
			$status = $wpdb->prepare( '%s', trim( $status ) );
		}

		return join( ',', $prepared_statuses );
	}

	/**
	 * Returns the cached list of values for the specified meta key and post statuses,
	 * or false if nothing is currently stored.
	 *
	 * @param string $meta_key
	 * @param array  $statuses
	 *
	 * @return bool|array
	 */
	protected static function cache_get( $meta_key, array $statuses ) {
		$cache = (array) wp_cache_get( self::CACHE_KEY, self::CACHE_GROUP );
		$statuses = join( '|', $statuses );

		if ( ! empty( $cache[ $meta_key ] ) && ! empty( $cache[ $meta_key ][ $statuses ] ) ) {
			return $cache[ $meta_key ][ $statuses ];
		}

		return false;
	}

	/**
	 * Store the list of values for a specific combination of meta_key and post statuses.
	 *
	 * @param string $meta_key
	 * @param array  $statuses
	 * @param string $value
	 */
	protected static function cache_set( $meta_key, array $statuses, $value ) {
		$cache = (array) wp_cache_get( self::CACHE_KEY, self::CACHE_GROUP );
		$statuses = join( '|', $statuses );

		$cache[ $meta_key ][ $statuses ] = $value;
		wp_cache_set( self::CACHE_KEY, $cache, self::CACHE_GROUP, self::$cache_expiry );
	}

	/**
	 * If an event post is deleted then invalidate the cache.
	 *
	 * @param int $post_id
	 */
	public static function cache_invalidate_on_event_deletion( $post_id ) {
		if ( Tribe__Events__Main::POSTTYPE === get_post_type( $post_id ) ) {
			self::cache_invalidate();
		}
	}

	/**
	 * If post meta data for an event is updated, created or deleted then invalidate the cache.
	 *
	 * @param int $meta_id (unused)
	 * @param int $post_id
	 */
	public static function cache_invalidate_on_meta_change( $meta_id, $post_id ) {
		if ( Tribe__Events__Main::POSTTYPE === get_post_type( $post_id ) ) {
			self::cache_invalidate();
		}
	}

	/**
	 * Invalidate the cache.
	 */
	public static function cache_invalidate() {
		wp_cache_delete( self::CACHE_KEY, self::CACHE_GROUP );
	}
}