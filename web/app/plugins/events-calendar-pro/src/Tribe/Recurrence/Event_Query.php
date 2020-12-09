<?php
/**
 * Facilitates setup of the query used to generate the /all/ events page.
 */
class Tribe__Events__Pro__Recurrence__Event_Query {
	/** @var WP_Query */
	protected $query;
	protected $slug = '';
	protected $parent_event;


	/**
	 * This is expected to be called in the context of the tribe_events_pre_get_posts
	 * action and only when it has already been determined that the request is to see
	 * all events making up a recurring sequence.
	 *
	 * @see Tribe__Events__Pro__Main::pre_get_posts()
	 *
	 * @param WP_Query $query
	 */
	public function __construct( WP_Query $query = null ) {
		if ( $query instanceof WP_Query ) {
			$this->query = $query;
			$this->slug = $query->get( 'name' );
		}
	}

	/**
	 * Abuse the WP action to do one last check on the 'all' page to avoid showing a page without anything on it.
	 * @return void
	 */
	public function verify_all_page() {
		if ( ! $wp_query = tribe_get_global_query_object() ) {
			return;
		}

		/**
		 * If we got this far and there are not posts we need to fetch at least the parent to
		 * prevent bugs with the page throwing a 404
		 */
		if ( empty( $wp_query->posts ) && isset( $wp_query->query_vars['post_parent'] ) ) {
			$wp_query->posts = array(
				get_post( $wp_query->query_vars['post_parent'] ),
			);
		}
	}

	/**
	 * Attach all the hooks associated with this class.
	 *
	 * @since 4.4.26
	 */
	public function hook() {
		if ( empty( $this->slug ) ) {
			return;
		}

		$this->setup();
}

	/**
	 * Unattach all the hooks associated with this class.
	 *
	 * @since 4.7
	 */
	public function unhook() {
		if ( empty( $this->slug ) ) {
			return;
		}

		remove_filter( 'posts_where', array( $this, 'include_parent_event' ) );
		remove_action( 'wp', array( $this, 'verify_all_page' ) );
	}

	/**
	 * If appropriate, mould the query to obtain all events belonging to the parent
	 * event of the sequence. Additionally may set up a filter to append a where clause
	 * to obtain the parent post in the same query.
	 */
	protected function setup() {
		unset( $this->query->query_vars['name'], $this->query->query_vars['tribe_events'] );

		$this->get_parent_event();

		if ( empty( $this->parent_event ) ) {
			$this->setup_for_404();
		} else {
			//Query Private Events if Logged In
			$status = current_user_can( 'read_private_tribe_events' ) ? array( 'publish', 'private' ) : 'publish';

			$this->query->set( 'post_parent', $this->parent_event->ID );
			$this->query->set( 'post_status', $status );
			$this->query->set( 'posts_per_page', tribe_get_option( 'postsPerPage', 10 ) );
			$this->query->set( 'tribe_remove_date_filters', $this->should_remove_date_filters() );

			// Configure what this page actually is
			$this->query->is_singular = false;
			$this->query->is_archive = true;
			$this->query->is_post_type_archive = true;

			add_filter( 'posts_where', array( $this, 'include_parent_event' ) );
			add_action( 'wp', array( $this, 'verify_all_page' ) );

			/**
			 * Hooks into query object after we have done the setup.
			 *
			 * @param WP_Query                                    $query       Recurrence query object.
			 * @param Tribe__Events__Pro__Recurrence__Event_Query $event_query Recurrence event query object.
			 *
			 * @since 4.7
			 */
			do_action( 'tribe_events_pro_pre_get_posts_recurrence', $this->query, $this );
		}
	}

	/**
	 * Obtains the parent event post given the slug currently being queried for.
	 */
	protected function get_parent_event() {

		//Query Parent Private Events if Logged In
		$status = current_user_can( 'read_private_tribe_events' ) ? array( 'publish', 'private' ) : 'publish';

		$posts = get_posts( array(
			'name'        => $this->slug,
			'post_type'   => Tribe__Events__Main::POSTTYPE,
			'post_status' => $status,
			'numberposts' => 1,
		) );

		$this->parent_event = reset( $posts );
	}

	/**
	 * Set from the outside the parent event associated with this event
	 *
	 * @since 4.4.26
	 *
	 * @param $parent_post
	 */
	public function set_parent_event( WP_Post $parent_post ) {
		$this->parent_event = $parent_post;
	}

	/**
	 * Effectively trigger a 404, ie if the provided slug was invalid.
	 */
	protected function setup_for_404() {
		$this->query->set( 'p', -1 );
	}

	/**
	 * Ensures the parent event is also included in the query results.
	 *
	 * @param  string $where_sql
	 *
	 * @return string
	 */
	public function include_parent_event( $where_sql ) {
		global $wpdb;

		// Run once only!
		remove_filter( 'posts_where', array( $this, 'include_parent_event' ) );

		$parent_id      = absint( $this->parent_event->ID );
		$where_children = " {$wpdb->posts}.post_parent = $parent_id ";
		$where_parent   = " {$wpdb->posts}.ID = $parent_id ";
		$where_either   = " ( $where_children OR $where_parent ) ";

		return str_replace( $where_children, $where_either, $where_sql );
	}

	/**
	 * Indicates if date filters should be removed for /all/ queries or not.
	 *
	 * Removing the date filters will expose past events from the series, while keeping
	 * them means only upcoming instances will be queried for.
	 *
	 * The default is to only ever remove date filters in the context of the main query
	 * and then only if there are no upcoming events in the series. The twin goals are
	 * to provide more relevant data to typical users (most visitors won't want to see
	 * expired events for a series) while avoiding 404s (which would happen if we apply
	 * date filters but there are no upcoming events in the series).
	 *
	 * @since 4.4.14
	 *
	 * @return bool
	 */
	protected function should_remove_date_filters() {
		$remove_date_filters = false;
		/**
		 * Filters the query args used for the tribe_get_events() call.
		 *
		 * @param array $args List of query args to use in the tribe_get_events() call.
		 *
		 * @since 4.7
		 */
		$args = apply_filters( 'tribe_events_pro_all_events_query_args', array(
			'post_parent'    => $this->parent_event->ID,
			'eventDisplay'   => 'list',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'starts_after'   => tribe_get_request_var( 'tribe-bar-date', 'now' ),
		) );

		$upcoming_instances = tribe_get_events( $args );

		if ( ! count( $upcoming_instances ) && $this->query->is_main_query() ) {
			$remove_date_filters = true;
		}

		/**
		 * Dictates whether date filters should be removed for the /all/ page query or not.
		 *
		 * Removing the date filters means *all* instances including past event instances will
		 * be queried for. Not removing them means only upcoming instances will be returned:
		 * the default behaviour is to remove them only if there are no upcoming events in the
		 * series.
		 *
		 * @since 4.4.14
		 *
		 * @param bool     $remove_date_filters
		 * @param WP_Query $query
		 * @param WP_Post  $series_parent
		 */
		return apply_filters( 'tribe_events_pro_all_events_view_remove_date_filters',
			$remove_date_filters,
			$this->query,
			$this->parent_event
		);
	}
}
