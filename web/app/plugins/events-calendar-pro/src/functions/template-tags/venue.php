<?php
/**
 * Events Calendar Pro Venue Template Tags
 *
 * Display functions for use in WordPress templates.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {

	/**
	 * Output the upcoming events associated with a venue.
	 *
	 * @param bool|int $post_id  The Venue post ID.
	 * @param array    $wp_query An array of arguments to override the default ones.
	 *
	 * @return null|string Null if the specified venue does not exist, the upcoming events
	 *                     HTML otherwise.
	 */
	function tribe_venue_upcoming_events( $post_id = false, array $args = array() ) {

		$page    = ! empty( $args['page'] ) ? $args['page'] : 1;
		$post_id = Tribe__Events__Main::postIdHelper( $post_id );

		/**
		 * Allow for cusotmizing the number of events that show on each single-venue page.
		 *
		 * @since 4.4.16
		 *
		 * @param int $posts_per_page The number of events to show.
		 */
		$events_per_page = apply_filters( 'tribe_events_single_venue_posts_per_page', 100 );

		/** @var Tribe__Context $context */
		$context = tribe('context');
		$display = $context->get( 'event_display' );
		$date_pivot_key = 'past' === $display ? 'starts_before' : 'starts_after';

		if ( $post_id ) {
			$args = array(
				'venue'          => $post_id,
				'eventDisplay'   => tribe_get_request_var( 'tribe_event_display', 'list' ),
				'posts_per_page' => $events_per_page,
				'paged'          => $page,
				$date_pivot_key  => 'now',
			);

			$html = tribe_include_view_list( $args );

			/**
			 * Allows for customizing the markup of the list of events on single-venue pages.
			 *
			 * @since 4.4.16
			 *
			 * @param string $html The markup of events retrieved for the single-venue page.
			 */
			return apply_filters( 'tribe_venue_upcoming_events', $html );
		}

		return null;
	}

	/**
	 * Gets the URL to the previous events for a venue.
	 *
	 * @param int $page        The current page number
	 * @param int $venue_id    The current venue post ID; will be read from the global `post` object
	 *                         if missing.
	 *
	 * @return string The absolute ugly URL to the previous events for the venue.
	 */
	function tribe_venue_previous_events_link( $page, $venue_id = null ) {
		return tribe_venue_direction_link( $page, $venue_id, 'prev' );
	}

	/**
	 * Gets the URL to the next events for a venue.
	 *
	 * @param int $page        The current page number
	 * @param int $venue_id    The current venue post ID; will be read from the global `post` object
	 *                         if missing.
	 *
	 * @return string The absolute ugly URL to the next events for the venue.
	 */
	function tribe_venue_next_events_link( $page, $venue_id = null ) {
		return tribe_venue_direction_link( $page, $venue_id, 'next' );
	}

	/**
	 * Checks whether a venue has more events in respect to the current page.
	 *
	 * @param int       $page     The current page number.
	 * @param int|array $venue_id The current venue post ID; will be read from the global `post` object
	 *                            if missing. If the value is an array only the first venue ID will be used.
	 *
	 * @return bool `false` if there are no next events, the post is not a venue or the page number is
	 *              not an int value, `true` if there are next events.
	 */
	function tribe_venue_has_next_events( $page, $venue_id = null ) {
		if ( ! is_numeric( $page ) && is_int( $page ) ) {
			return false;
		}

		$venue_id = is_array( $venue_id ) ? reset( $venue_id ) : $venue_id;
		$post_id = Tribe__Main::post_id_helper( $venue_id );

		if ( ! tribe_is_venue( $post_id ) ) {
			return false;
		}

		// Grab Post IDs of events currently on the page to ensure they don't erroneously show up on the "Next" page.
		$wp_query = tribe_get_global_query_object();
		$events_on_this_page = null === $wp_query ? array() : wp_list_pluck( $wp_query->posts, 'ID' );

		/**
		 * Allow for cusotmizing the number of events that show on each single-venue page.
		 *
		 * @since 4.4.16
		 *
		 * @param int $posts_per_page The number of events to show.
		 */
		$events_per_page = apply_filters( 'tribe_events_single_venue_posts_per_page', 100 );

		$display = tribe('context')->get('event_display');

		if ( 'past' === $display ) {
			if ( 1 === (int) $page ) {
				// "Next", on the first page of past events, means first page of upcoming events.
				$date_pivot_key = 'starts_after';
				$page = 1;
			} else {
				$date_pivot_key = 'starts_before';
				++ $page;
			}
		} else {
			$date_pivot_key = 'starts_after';
			++ $page;
		}

		$args = array(
			'venue'          => $venue_id,
			'paged'          => $page,
			'posts_per_page' => $events_per_page,
			'post__not_in'   => $events_on_this_page,
			$date_pivot_key  => 'now',
			'hidden'         => false,
		);

		$found = tribe_events()->by_args( $args )->found();

		return $found > 0;
	}

	/**
	 * Gets the URL to the next or previous events for a venue.
	 *
	 * @param int       $page      The current page number
	 * @param int|array $venue_id  The current venue post ID; will be read from the global `post` object
	 *                             if missing. If passed an array then the first venue ID from the array will be used.
	 * @param string    $direction Either 'next' or 'prev'.
	 *
	 * @return string The absolute ugly URL to the next/previous events for the venue.
	 */
	function tribe_venue_direction_link( $page, $venue_id, $direction = 'next' ) {
		if ( ! in_array( $direction, array( 'next', 'prev' ) ) ) {
			return '';
		}

		if ( ! ( is_numeric( $page ) && is_int( $page ) ) ) {
			return '';
		}

		$venue_id = is_array( $venue_id ) ? reset( $venue_id ) : $venue_id;
		$post_id = Tribe__Main::post_id_helper( $venue_id );

		if ( ! tribe_is_venue( $post_id ) ) {
			return '';
		}

		$name = get_post( $venue_id )->post_name;

		$args = array(
			'display' => 'list',
			'page' => 1,
		);

		// Add a check in case plugins are not updated in order.
		if ( function_exists( 'tribe_get_listview_args' ) ) {
			$args = tribe_get_listview_args( $page, $direction );
		}

		return add_query_arg(
			array(
				Tribe__Events__Venue::POSTTYPE => $name,
				'page' => $args['page'],
				'tribe_event_display' => $args['display'],
			),
			home_url()
		);
	}
}
