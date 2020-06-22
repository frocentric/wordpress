<?php
use Tribe__Date_Utils as Dates;

/**
 * This file contains hooks and functions required to set up the photo view.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Pro__Templates__Photo' ) ) {
	class Tribe__Events__Pro__Templates__Photo extends Tribe__Events__Pro__Template_Factory {

		protected $body_class = 'events-photo';
		const AJAX_HOOK = 'tribe_photo';
		public $view_path = 'pro/photo/content';
		public $photoSlug = 'photo';

		/**
		 * Array of asset packages needed for this template
		 *
		 * @var array
		 **/
		protected $asset_packages = array();

		protected function hooks() {
			parent::hooks();
			tribe_asset_enqueue( 'tribe-events-pro-photo' );
			add_filter( 'tribe_events_header_attributes', array( $this, 'header_attributes' ) );
		}

		/**
		 * Add header attributes for photo view
		 *
		 * @return string
		 **/
		public function header_attributes( $attrs ) {
			$attrs['data-startofweek'] = get_option( 'start_of_week' );
			$attrs['data-view']        = 'photo';
			$attrs['data-baseurl']     = tribe_get_photo_permalink( false );

			$term         = false;
			$term_name    = get_query_var( Tribe__Events__Main::TAXONOMY );

			if ( ! empty( $term_name ) ) {
				$term_obj = get_term_by( 'slug', $term_name, Tribe__Events__Main::TAXONOMY );

				if ( ! empty( $term_obj ) ) {
					$term = 0 < $term_obj->term_id ? $term_obj->term_id : false;
					if ( $term ) {
						$term_link = get_term_link( (int) $term, Tribe__Events__Main::TAXONOMY );
						if ( ! is_wp_error( $term_link ) ) {
							$attrs['data-baseurl'] = trailingslashit( $term_link . $this->photoSlug );
						}
					}
				}
			}

			return apply_filters( 'tribe_events_pro_header_attributes', $attrs );
		}


		/**
		 * Add event classes specific to photo view
		 *
		 * @param $classes
		 *
		 * @return array
		 **/
		public function event_classes( $classes ) {
			$classes[] = 'tribe-events-photo-event';

			return $classes;
		}

		/**
		 * AJAX handler for Photo view
		 *
		 * @return void
		 */
		public function ajax_response() {

			$tec = Tribe__Events__Main::instance();

			Tribe__Events__Query::init();

			$tribe_paged = ! empty( $_POST['tribe_paged'] ) ? intval( $_POST['tribe_paged'] ) : 1;

			$post_status = array( 'publish' );
			if ( is_user_logged_in() ) {
				$post_status[] = 'private';
			}

			// Set the date explicitly if not set.
			$date = tribe_get_request_var( 'tribe-bar-date', 'now' );

			$args = array(
				'eventDisplay' => 'list',
				'post_type'    => Tribe__Events__Main::POSTTYPE,
				'post_status'  => $post_status,
				'paged'        => $tribe_paged,
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

			$view_state = 'photo';

			if ( isset( $_POST['tribe_event_category'] ) ) {
				$args[ Tribe__Events__Main::TAXONOMY ] = $_POST['tribe_event_category'];
			}

			if ( (bool) tribe_get_request_var( 'tribeHideRecurrence' ) ) {
				$args['hide_subsequent_recurrences'] = true;
			}
			// Apply display and date.
			$date = tribe_get_request_var( 'tribe-bar-date', 'now' );

			if ( 'now' === $date ) {
				/*
				 * When defaulting to "now" let's round down to the lower half hour.
				 * This way we avoid invalidating the hash on requests following each other
				 * in reasonable (30') time.
				 */
				$date = Dates::build_date_object( 'now' );
				$minutes = $date->format( 'm' );
				$date->setTime(
					$date->format( 'H' ),
					$minutes - ( $minutes % 30 )
				);
				$date = $date->format( Dates::DBDATETIMEFORMAT );
			}

			// Handle current or past view distinction.
			if ( 'past' === tribe_get_request_var( 'tribe_event_display' ) ) {
				$view_state            = 'past';
				$args['eventDisplay']  = 'past';
				$args['order']         = 'DESC';
				$args['ends_before']   = $date;
			} else {
				$args['ends_after'] = $date;
			}

			$query = Tribe__Events__Query::getEvents( $args, true );
			$hash  = $args;

			$hash['paged']      = null;
			$hash['start_date'] = null;
			$hash_str           = md5( maybe_serialize( $hash ) );

			if ( ! empty( $_POST['hash'] ) && $hash_str !== $_POST['hash'] ) {
				$tribe_paged   = 1;
				$args['paged'] = 1;
				$query         = Tribe__Events__Query::getEvents( $args, true );
			}

			$response = array(
				'html'        => '',
				'success'     => true,
				'max_pages'   => $query->max_num_pages,
				'hash'        => $hash_str,
				'tribe_paged' => $tribe_paged,
				'view'        => $view_state,
			);

			global $post;
			global $wp_query;

			$wp_query = $query;
			if ( ! empty( $query->posts ) ) {
				$post = $query->posts[0];
			}

			add_filter( 'tribe_events_list_pagination', array( 'Tribe__Events__Main', 'clear_module_pagination' ), 10 );

			$tec->displaying = 'photo';

			ob_start();

			tribe_get_view( $this->view_path );

			$response['html'] .= ob_get_clean();

			apply_filters( 'tribe_events_ajax_response', $response );

			header( 'Content-type: application/json' );
			echo json_encode( $response );

			die();
		}

	}
}
