<?php


class Tribe__Events__Pro__Recurrence__Children_Events {

	/**
	 * @var array
	 */
	protected $to_delete = array();

	/**
	 * @var Tribe__Cache
	 */
	protected $cache;

	/**
	 * @var array
	 */
	protected $maybe_zombie_children = array();

	/**
	 * Tribe__Events__Pro__Recurrence__Children_Events constructor.
	 *
	 * @param Tribe__Cache|null $cache
	 */
	public function __construct( Tribe__Cache $cache = null ) {
		$this->cache = $cache ? $cache : new Tribe__Cache();
	}

	/**
	 * @var Tribe__Events__Pro__Recurrence__Children_Events
	 */
	protected static $instance;

	/**
	 * Singleton pattern constructor.
	 *
	 * @return Tribe__Events__Pro__Recurrence__Children_Events
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Returns all the IDs of the event posts children to the specified one.
	 *
	 * @param int   $post_id
	 * @param array $args      An array of arguments overriding the default ones.
	 *
	 * @type bool   $use_cache Whether the query should try to hit and update the cache or not.
	 *
	 * @return array|mixed
	 */
	public function get_ids( $post_id, $args = array() ) {
		$use_cache = isset( $args['use_cache'] ) && false == $args['use_cache'] ? false : true;

		if ( isset( $args['fields'] ) ) {
			unset( $args['fields'] );
		}

		if ( $use_cache ) {
			$children = $this->cache->get( 'child_events_' . $post_id, 'save_post' );
			if ( is_array( $children ) ) {
				return $children;
			}
		}

		$args = wp_parse_args( $args, self::defaults_for_post( $post_id ) );

		$children = get_posts( $args );

		if ( $use_cache ) {
			$this->cache->set( 'child_events_' . $post_id, $children, Tribe__Cache::NO_EXPIRATION, 'save_post' );
		}

		return $children;
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public static function defaults_for_post( $post_id ) {
		return array(
			'post_parent'    => $post_id,
			'post_type'      => Tribe__Events__Main::POSTTYPE,
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_status'    => get_post_stati(),
			'meta_key'       => '_EventStartDate',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'tribe_remove_date_filters' => true,
		);
	}

	/**
	 * Restores all the children events of an event post from the trash to their previous state.
	 * @param $post_id
	 */
	public function untrash_all( $post_id ) {
		$children = $this->get_ids( $post_id, array( 'post_status' => 'trash' ) );
		add_filter( 'wp_untrash_post_status', 'wp_untrash_post_set_previous_status', 10, 3 );
		foreach ( $children as $child_id ) {
			wp_untrash_post( $child_id );
		}
		remove_filter( 'wp_untrash_post_status', 'wp_untrash_post_set_previous_status', 10 );
	}

	/**
	 * Trashes all the children events of an event post.
	 *
	 * @param int $post_id
	 */
	public function trash_all( $post_id ) {
		$children = $this->get_ids( $post_id );
		foreach ( $children as $child_id ) {
			wp_trash_post( $child_id );
		}
	}

	/**
	 * Permanently deletes all the children events of an event post.
	 *
	 * @param int  $post_id
	 * @param bool $immediate Whether the deletion should happen immediately (`true`) or
	 *                        at `shutdown` (`false`); default `false`
	 */
	public function permanently_delete_all( $post_id, $immediate = false ) {
		$this->to_delete[] = $post_id;

		add_filter( 'pre_delete_post', array( $this, 'prevent_deletion' ), 10, 2 );
		if ( ! $immediate ) {
			add_action( 'shutdown', array( $this, 'delete_on_shutdown' ) );
		} else {
			$this->delete_on_shutdown();
		}
	}

	/**
	 * Deletes events that are parent to a group of recurring event instances
	 * and the recurrence instances with it.
	 *
	 * This is to force the deletion order so that children events might be deleted
	 * before the parent event but the parent event will be never deleted before its
	 * children triggering the cascade deletion that would cause later attempts to
	 * delete one of the children to fail and with a permission error.
	 */
	public function delete_on_shutdown() {
		foreach ( $this->to_delete as $id ) {
			$children = $this->get_ids( $id );
			foreach ( $children as $child_id ) {
				wp_delete_post( $child_id, true );
			}
			wp_delete_post( $id );
		}
	}

	/**
	 * Filters the `pre_delete_post` hook to stop WordPress from deleting
	 * parent events in a recurrence series before children recurring events.
	 *
	 * @param bool $delete Whether the post should be deleted or not.
	 * @param WP_Post $post The post object that should be deleted.
	 *
	 * @return bool The original value if the post is not among the ones
	 *              that should be deleted on `shutdown` hook, `true`
	 *              otherwise.
	 */
	public function prevent_deletion( $delete, $post ) {
		if ( in_array( $post->ID, $this->to_delete ) ) {
			return true;
		}

		return $delete;
	}
}
