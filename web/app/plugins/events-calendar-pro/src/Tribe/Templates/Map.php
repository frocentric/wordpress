<?php
/**
 * @for     Map Template
 * This file contains hooks and functions required to set up the map view.
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Tribe__Date_Utils as Dates;
use Tribe__Events__Pro__Geo_Loc as Geo_Loc;
use Tribe__Events__Query as Query;

if ( ! class_exists( 'Tribe__Events__Pro__Templates__Map' ) ) {
	class Tribe__Events__Pro__Templates__Map extends Tribe__Events__Pro__Template_Factory {

		protected $body_class = 'events-list';
		const AJAX_HOOK = 'tribe_geosearch';

		/**
		 * The path to the template file used for the view.
		 * This value is used in Shortcodes/Tribe_Events.php to
		 * locate the correct template file for each shortcode
		 * view.
		 *
		 * @var string
		 */
		public $view_path = 'pro/map/content';

		/**
		 * Set up hooks for map view
		 *
		 * @return void
		 **/
		protected function hooks() {
			parent::hooks();

			tribe_asset_enqueue( 'tribe-events-pro-geoloc' );

			add_filter( 'tribe_events_header_attributes', array( $this, 'header_attributes' ) );
			add_action( 'tribe_events_list_before_the_event_title', array( $this, 'add_event_distance' ) );
		}

		/**
		 * Add header attributes for map view
		 *
		 * @return string
		 **/
		public function header_attributes( $attrs ) {
			$attrs['data-startofweek'] = get_option( 'start_of_week' );
			$attrs['data-view']    = 'map';
			$attrs['data-baseurl'] = tribe_get_mapview_link();

			return apply_filters( 'tribe_events_pro_header_attributes', $attrs );
		}

		/**
		 * AJAX handler for the Map view
		 */
		public function ajax_response() {

			$tribe_paged = ! empty( $_POST['tribe_paged'] ) ? $_POST['tribe_paged'] : 1;

			Tribe__Events__Query::init();

			$post_status = array( 'publish' );
			if ( is_user_logged_in() ) {
				$post_status[] = 'private';
			}

			$defaults = array(
				'post_type'      => Tribe__Events__Main::POSTTYPE,
				'posts_per_page' => tribe_get_option( 'postsPerPage', 10 ),
				'paged'          => $tribe_paged,
				'post_status'    => $post_status,
				'eventDisplay'   => 'map',
				'tribe_geoloc'   => true,
			);

			// If the request is false or not set we assume the request is for all events, not just featured ones.
			if (
				tribe( 'tec.featured_events' )->featured_events_requested()
				|| (
					isset( $this->args['featured'] )
					&& tribe_is_truthy( $this->args['featured'] )
				)
			) {
				$args['featured'] = true;
			} else {
				/**
				 * Unset due to how queries featured argument is expected to be non-existent.
				 *
				 * @see #127272
				 */
				if ( isset( $args['featured'] ) ) {
					unset( $args['featured'] );
				}
			}

			if ( (bool) tribe_get_request_var( 'tribeHideRecurrence' ) ) {
				$defaults['hide_subsequent_recurrences'] = true;
			}

			$view_state = 'map';

			$date = tribe_get_request_var( 'tribe-bar-date', 'now' );

			// Handle current or past view distinction.
			if ( 'past' === tribe_get_request_var( 'tribe_event_display' ) ) {
				$view_state                = 'past';
				$defaults['eventDisplay']  = 'past';
				$defaults['order']         = 'DESC';
				$defaults['ends_before'] = Dates::build_date_object( $date );
			} else {
				$defaults['ends_after'] = Dates::build_date_object( $date );
			}

			if ( isset( $_POST['tribe_event_category'] ) ) {
				$defaults[ Tribe__Events__Main::TAXONOMY ] = $_POST['tribe_event_category'];
			}

			if ( isset( $_POST[ Tribe__Events__Main::TAXONOMY ] ) ) {
				$defaults[ Tribe__Events__Main::TAXONOMY ] = $_POST[ Tribe__Events__Main::TAXONOMY ];
			}

			$query = $this->get_geofenced_query( $defaults );

			$have_events = ( 0 < $query->found_posts );

			if ( $have_events && Tribe__Events__Pro__Geo_Loc::instance()->is_geoloc_query() ) {
				$lat = isset( $_POST['tribe-bar-geoloc-lat'] ) ? $_POST['tribe-bar-geoloc-lat'] : 0;
				$lng = isset( $_POST['tribe-bar-geoloc-lng'] ) ? $_POST['tribe-bar-geoloc-lng'] : 0;

				Tribe__Events__Pro__Geo_Loc::instance()->assign_distance_to_posts( $query->posts, $lat, $lng );
			} elseif ( ! $have_events && isset( $_POST['tribe-bar-geoloc'] ) ) {
				Tribe__Notices::set_notice( 'event-search-no-results', sprintf( __( 'No results were found for events in or near <strong>"%s"</strong>.', 'tribe-events-calendar-pro' ), esc_html( $_POST['tribe-bar-geoloc'] ) ) );
			} elseif ( ! $have_events && isset( $_POST['tribe_event_category'] ) ) {
				Tribe__Notices::set_notice( 'events-not-found', sprintf( __( 'No matching events listed under %s. Please try viewing the full calendar for a complete list of events.', 'tribe-events-calendar-pro' ), esc_html( $_POST['tribe_event_category'] ) ) );
			} elseif ( ! $have_events ) {
				Tribe__Notices::set_notice( 'event-search-no-results', __( 'There were no results found.', 'tribe-events-calendar-pro' ) );
			}

			$response = array(
				'html'        => '',
				'markers'     => array(),
				'success'     => true,
				'tribe_paged' => $tribe_paged,
				'max_pages'   => $query->max_num_pages,
				'total_count' => $query->found_posts,
				'view'        => $view_state,
			);

			global $wp_query;

			// @TODO: clean this up / refactor the following conditional
			if ( $have_events ) {
				global $post;

				$data     = $query->posts;
				$post     = $query->posts[0];
				$wp_query = $query;

				Tribe__Events__Main::instance()->displaying = 'map';

				ob_start();

				tribe_get_view( 'pro/map/content' );
				$response['html'] .= ob_get_clean();
				$response['markers'] = Tribe__Events__Pro__Geo_Loc::instance()->generate_markers( $data );
			} else {
				$wp_query = $query;
				Tribe__Events__Main::instance()->setDisplay();

				ob_start();

				tribe_get_view( 'pro/map/content' );
				$response['html'] .= ob_get_clean();
			}

			$response = apply_filters( 'tribe_events_ajax_response', $response );

			header( 'Content-type: application/json' );
			echo json_encode( $response );

			exit;

		}

		/**
		 * Builds, runs and returns a geo-fenced query.
		 *
		 * @since 4.7
		 *
		 * @param array $query_args The arguments that should be used to fetch the view events.
		 *
		 * @return WP_Query The query object, filtered to add geo-fencing.
		 */
		protected function get_geofenced_query( array $query_args ) {
			$callback = [ Geo_Loc::instance(), 'setup_geoloc_in_query' ];
			$applies  = ! has_action( 'pre_get_posts', $callback );

			if ( $applies ) {
				add_action( 'pre_get_posts', $callback );
			}

			$query = Query::getEvents( $query_args, true );

			if ( $applies ) {
				remove_action( 'pre_get_posts', $callback );
			}

			return $query;
		}
	}
}
