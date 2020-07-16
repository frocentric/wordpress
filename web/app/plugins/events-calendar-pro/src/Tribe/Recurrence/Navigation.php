<?php
/**
 * Class Tribe__Events__Pro__Recurrence__Navigation
 *
 * @since 4.4.26
 */
class Tribe__Events__Pro__Recurrence__Navigation {

	/**
	 * Variable to hold the state during the life span of this class to hold the value of an a recurrence ajax call.
	 *
	 * @since 4.4.26
	 *
	 * @var bool
	 */
	private $is_recurrence_ajax_call = false;

	/**
	 * Method called by the Singleton registration of this class.
	 *
	 * @since 4.4.26
	 */
	public function hook() {
		add_action( 'tribe_events_parse_query', array( $this, 'parse_query' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'tribe_is_past', array( $this, 'is_past' ) );
		add_filter( 'tribe_events_view_data_attributes', array( $this, 'events_attributes' ) );
		add_filter( 'tribe_events_listview_ajax_get_event_args', array( $this, 'listview_ajax' ), 10, 2 );
		add_filter( 'tribe_events_listview_ajax_event_display', array( $this, 'ajax_event_display' ) );
	}

	/**
	 * Parse query to global query to add a new member associated with recurrence list view
	 *
	 * @since 4.4.26
	 *
	 * @param $query
	 */
	public function parse_query( $query ) {

		if ( ! $this->is_doing_ajax() ) {
			$this->is_recurrence_ajax_call = false;
		}

		if ( ! $query->is_main_query() || ! $query->tribe_is_event ) {
			return;
		}

		remove_filter( 'tribe_events_parse_query', array( $this, 'parse_query' ) );

		$query->tribe_is_recurrence_list = (bool) $query->get( 'tribe_recurrence_list' );
	}

	/**
	 * Register the new variable available on the permalink structure
	 *
	 * @since 4.4.26
	 *
	 * @param $vars array An array with the query variables
	 *
	 * @return array
	 */
	public function query_vars( $vars ) {
		$vars[] = 'tribe_recurrence_list';
		return $vars;
	}

	/**
	 * Past is not always true on recurrence events due the presence of All variable all the time, so in order to
	 * ensure the correct behavior we need to overwrite it based on the global query variables
	 *
	 * @since 4.4.26
	 *
	 * @param $past boolean Either true if is on the past or false otherwise
	 *
	 * @return bool
	 */
	public function is_past( $past ) {
		$wp_query = tribe_get_global_query_object();
		if ( null === $wp_query ) {
			return $past;
		}

		$is_past = $wp_query->tribe_is_past;

		if ( ! $is_past ) {
			return $past;
		}

		$is_showing_all = tribe_is_showing_all();

		$is_recurrence_ajax_call = $this->is_doing_ajax() ? $this->is_recurrence_ajax_call : false;
		$is_recurrence = $wp_query->tribe_is_recurrence_list || $is_recurrence_ajax_call;

		return $is_showing_all && $is_recurrence ? $is_past : $past;
	}

	/**
	 * Add a new header attribute into the events container to identify if the view is rendered on the list view
	 * used to render all the recurrence childs.
	 *
	 * @since 4.4.26
	 *
	 * @param $attributes
	 *
	 * @return mixed
	 */
	public function events_attributes( $attributes ) {

		$wp_query = tribe_get_global_query_object();

		if ( null === $wp_query ) {
			return $attributes;
		}

		$is_recurrence_list = absint( $wp_query->tribe_is_recurrence_list );
		if ( $is_recurrence_list ) {
			$attributes['recurrence-list'] = "{$is_recurrence_list}";

			$post_parent = $wp_query->get( 'post_parent' );
			if ( $post_parent ) {
				$attributes['tribe_post_parent'] = $post_parent;
			}
		}

		return $attributes;
	}

	/**
	 * Listen for the Ajax calls into the list view and detect if the variable that identify a reference to the
	 * recurrence list is present there we can toggle the class variable
	 *
	 * @since 4.4.26
	 *
	 * @param $args
	 * @param $post
	 *
	 * @return mixed
	 */
	public function listview_ajax( $args, $post ) {
		$is_recurrence = empty( $post['is_recurrence_list'] ) ? false : tribe_is_truthy( $post['is_recurrence_list'] );
		$this->is_recurrence_ajax_call = $is_recurrence;

		// Add filter to the query before is executed via ajax.
		if ( ! empty( $args['post_parent'] ) && is_numeric( $args['post_parent'] ) ) {
			$recurrence_query = new Tribe__Events__Pro__Recurrence__Event_Query();
			$recurrence_query->set_parent_event( get_post( $args['post_parent'] ) );
			add_filter( 'posts_where', array( $recurrence_query, 'include_parent_event' ), 100 );
		}

		return $args;
	}

	/**
	 * Set the value for tribe_is_recurrence_list when an Ajax request has been fired.
	 *
	 * @since 4.4.26
	 * @param $display
	 *
	 * @return mixed
	 */
	public function ajax_event_display( $display ) {

		$wp_query = tribe_get_global_query_object();
		if ( null !== $wp_query ) {
			$wp_query->tribe_is_recurrence_list = $this->is_recurrence_ajax_call;
		}

		return $display;
	}

	/**
	 * Internal abstraction to prevent errors if the container has not been created yet.
	 *
	 * @since 4.4.26
	 *
	 * @return bool
	 */
	private function is_doing_ajax() {
		return class_exists( 'Tribe__Context' )
			? tribe( 'context' )->doing_ajax()
			: defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}
