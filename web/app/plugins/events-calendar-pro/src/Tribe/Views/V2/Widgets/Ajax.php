<?php
/**
 * Utility class that provides ajax methods for widget admin dropdowns.
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe__Date_Utils as Dates;
use Tribe__Events__Main as TEC_Main;

/**
 * Class Ajax
 *
 * @since   5.3.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Ajax {
	/**
	 * AJAX handler for the Widget Event Select2
	 *
	 * @since 5.3.0
	 *
	 * @return array<string,mixed> Events in an array format.
	 */
	public function get_events() {
		$selected = tribe_get_request_var( 'selected' );
		$search   = tribe_get_request_var( 'search' );
		$page     = ! empty( $search['page'] ) ? $search['page'] : 1;

		/* @var \Tribe__Ajax__Dropdown $dropdown  */
		$dropdown = tribe( 'ajax.dropdown' );

		$now  = Dates::build_date_object( tribe_context()->get( 'now', 'now' ) );

		// Determine the query with repository.
		$events_repo = tribe_events()
			->by( 'starts_after', $now->format( Dates::DBDATETIMEFORMAT ) )
			->by( 'search', $search )
			->page( $page )
			->order_by( [ 'event_date_utc' => 'ASC' ] );

		$events = $events_repo->all();

		$has_pagination = count( $events ) < $events_repo->found();

		// Include the formatted title into the object for each event, including the date.
		foreach ( $events as &$event ) {
			$event_date = $event->dates->start->format( Dates::DBDATEFORMAT );
			$event->post_title_formatted = $event->post_title . "({$event_date})";
		}

		$results = $dropdown->format_posts_for_dropdown( $events, $selected, $has_pagination );

		// If none are selected, default to first one.
		if ( empty( $selected ) ) {
			$results['posts'][0]['selected'] = true;
		}

		wp_send_json_success(
			[
				'results'    => $results['posts'],
				'pagination' => [ 'more' => $results['pagination'] ],
			]
		);
	}

	/**
	 * AJAX handler for the Widget Venue Select2.
	 *
	 * @since 5.3.0
	 *
	 * @return array<string,mixed> Venues in an array format.
	 */
	public function get_venues() {
		$selected = tribe_get_request_var( 'selected' );
		$search   = tribe_get_request_var( 'search' );
		$page     = ! empty( $search['page'] ) ? $search['page'] : 1;

		/* @var \Tribe__Ajax__Dropdown $dropdown  */
		$dropdown = tribe( 'ajax.dropdown' );

		// Determine the query with repository.
		$venues_repo = tribe_venues()
			->by( 'search', $search )
			->page( $page )
			->order_by( [ 'post_title' => 'ASC' ] );

		$venues = $venues_repo->all();

		$has_pagination = count( $venues ) < $venues_repo->found();

		$results = $dropdown->format_posts_for_dropdown( $venues, $selected, $has_pagination );

		// If none are selected, default to first one.
		if ( empty( $selected ) ) {
			$results['posts'][0]['selected'] = true;
		}

		wp_send_json_success(
			[
				'results'    => $results['posts'],
				'pagination' => [ 'more' => $results['pagination'] ],
			]
		);
	}
}
