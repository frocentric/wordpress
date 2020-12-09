<?php
/**
 * Class for Facebook Imports.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Pro_Facebook {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}
	
	/**
	 * get all events for facebook page or organizer
	 *
	 * @since 1.0.0
	 * @return array the events
	 */
	public function get_events_for_facebook_page( $facebook_page_id, $type = 'page' ) {
		global $wpea_errors, $importevents;
		$fields = array(
					'id',
					'name',
					'description',
					'start_time',
					'end_time',
					'event_times',
					'cover',
					'ticket_uri',
					'timezone',
					'place',
				);
		$include_owner = apply_filters( 'wpeapro_import_owner', false );
		if( $include_owner ){
			$fields[] = 'owner';
		}

		$args = array(
			'limit' => 4999,
			'fields' => implode(
				',',
				$fields
			)
		);
		// Add Option for only upcoming events of page
		if( $type == 'page' ){
			$args['time_filter'] = 'upcoming';
		}

		// Add Option for only upcoming events of Group
		if( $type == 'group' ){
			$args['since'] = date( 'Y-m-d' );
		}

		$url = $importevents->facebook->generate_facebook_api_url( $facebook_page_id . '/events', $args );
		$response = $importevents->facebook->get_json_response_from_url( $url );
		if( isset( $response->error->message ) ){
			$wpea_errors[] = $response->error->message;
			return false;
		}

		$response_data = !empty( $response->data ) ? (array) $response->data : array();

		if ( empty( $response_data ) || empty( $response_data[0] ) ) {	
			return false;
		}

		return array_reverse( $response_data );
	}

	/**
	 * Get Group id form url or slug or by ID itself.
	 *
	 * @since    1.0.0
	 */
	public function get_facebook_group_id_by_url( $facebook_group ) {
		global $wpea_errors;
		if( false !== strpos( $facebook_group, '/groups/' ) ){
			$facebook_group_temp = explode('/groups/', $facebook_group );
			if( isset( $facebook_group_temp[1] ) ){
				$facebook_group = str_replace( '/', '', $facebook_group_temp[1] );
			}else{
				return false;
			}
		}

		$facebook_group = trim( $facebook_group );
		if ( is_numeric( $facebook_group ) ) {	
			$is_valid = $this->validate_facebook_group_id( $facebook_group );
			if( $is_valid ){
				return $facebook_group;
			}else{
				return false;
			}
		}else{
			if( false === strpos( $facebook_group, '//facebook.com' ) ){
				$facebook_group_id = $this->get_facebook_group_id_by_group_slug( $facebook_group );
				if( $facebook_group_id ){
					return $facebook_group_id;
				}
			}
		}
		$wpea_errors[] = esc_html__( 'Please insert valid Group URL or Group ID', 'wp-event-aggregator-pro' );
		return false;
	}

	/**
	 * Get Group id form group slug.
	 *
	 * @since    1.0.0
	 */
	public function get_facebook_group_id_by_group_slug( $facebook_group_slug ) {
		global $wpea_errors,$importevents;
		$args = array(
			'q'     => $facebook_group_slug,
			'type'  => 'group',
			'limit' => 1
		);

		$url = $importevents->facebook->generate_facebook_api_url( 'search', $args );
		$response = $importevents->facebook->get_json_response_from_url( $url );

		if( isset( $response->error->message ) ){
			$wpea_errors[] = $response->error->message;
			return false;
		}

		$response_data = !empty( $response->data ) ? $response->data : array();
		if ( !empty( $response_data[0] ) && $response_data[0]->id != '' ) {
			return $response_data[0]->id;
		}
		return false;
	}

	/**
	 * Validate Group id.
	 *
	 * @since    1.2
	 */
	public function validate_facebook_group_id( $facebook_group_id ) {
		global $wpea_errors,$importevents;
		$args = array(
			'fields' => implode(
					',',
					array(
						'id',
						'name'
					)
				)
			);

		$url = $importevents->facebook->generate_facebook_api_url( trim($facebook_group_id), $args );
		$response =  $importevents->facebook->get_json_response_from_url( $url );
		
		if( isset( $response->error->message ) ){
			$wpea_errors[] = $response->error->message;
			return false;
		}

		$group_name = !empty( $response->name ) ? $response->name : '';
		if ( $group_name != '' ) {
			return true;
		}
		return false;
	}
}
