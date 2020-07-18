<?php
/**
 * Class for Import Events into Eventum
 *
 * @link       http://xylusthemes.com/
 * @since      1.2.0
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Pro_Eventum {

	// Eventum Event Taxonomy
	protected $taxonomy;

	// Eventum Event Posttype
	protected $event_posttype;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->taxonomy = 'ecategory';
		$this->event_posttype = 'event';
		add_filter( 'wpea_supported_plugins', array( $this, 'wpea_add_eventum_to_supported_plugins' ) );
	}

	/**
	 * Get Posttype and Taxonomy Functions
	 *
	 * @return string
	 */
	public function get_event_posttype(){
		return $this->event_posttype;
	}	

	public function get_taxonomy(){
		return $this->taxonomy;
	}

	/**
	 * Add Eventum in supported plugins
	 *
	 * @since    1.0.0
	 * @param  array $supported_plugins Supported Plugins array.
	 * @return array $supported_plugins Supported Plugins array.
	 */
	public function wpea_add_eventum_to_supported_plugins( $supported_plugins ){
		// Check Eventum.
		if( defined( 'TEVOLUTION_EVENT_VERSION' ) &&  defined( 'TEVOLUTION_EVENT_DIR' ) ){
			$supported_plugins['eventum'] = __( 'Eventum (Tevolution-Events)', 'wp-event-aggregator-pro' );
		}
		return $supported_plugins;
	}

	/**
	 * import event into Eventum
	 *
	 * @since    1.0.0
	 * @param  array $centralize event array.
	 * @return array
	 */
	public function import_event( $centralize_array, $event_args ){
		global $wpdb, $importevents;

		if( empty( $centralize_array ) || !isset( $centralize_array['ID'] ) ){
			return false;
		}

		$is_exitsing_event = $importevents->common->get_event_by_event_id( $this->event_posttype, $centralize_array );

		if ( $is_exitsing_event ) {
			// Update event or not?
			$options = wpea_get_import_options( $centralize_array['origin'] );
			$update_events = isset( $options['update_events'] ) ? $options['update_events'] : 'no';
			if ( 'yes' != $update_events ) {
				return array(
					'status'=> 'skipped',
					'id' 	=> $is_exitsing_event
				);
			}
		}

		$origin_event_id = $centralize_array['ID'];
		$post_title = isset( $centralize_array['name'] ) ? sanitize_text_field( $centralize_array['name'] ): '';
		$post_description = isset( $centralize_array['description'] ) ? $centralize_array['description'] : '';
		$start_time = $centralize_array['starttime_local'];
		$end_time = $centralize_array['endtime_local'];
		$ticket_uri = $centralize_array['url'];

		$tum_eventdata = array(
			'post_title'  => $post_title,
			'post_content' => $post_description,
			'post_type'   => $this->event_posttype,
			'post_status' => 'pending',
		);
		if ( $is_exitsing_event ) {
			$tum_eventdata['ID'] = $is_exitsing_event;
		}
		if( isset( $event_args['event_status'] ) && $event_args['event_status'] != '' ){
			$tum_eventdata['post_status'] = $event_args['event_status'];
		}

		if ( $is_exitsing_event && ! $importevents->common->wpea_is_updatable('status') ) {
			$tum_eventdata['post_status'] = get_post_status( $is_exitsing_event );
			$event_args['event_status'] = get_post_status( $is_exitsing_event );
		}

		$inserted_event_id = wp_insert_post( $tum_eventdata, true );

		if ( ! is_wp_error( $inserted_event_id ) ) {
			$inserted_event = get_post( $inserted_event_id );
			if ( empty( $inserted_event ) ) { return false;}

			// Asign event category.
			$ife_cats = isset( $event_args['event_cats'] ) ? $event_args['event_cats'] : array();
			if ( ! empty( $ife_cats ) ) {
				foreach ( $ife_cats as $ife_catk => $ife_catv ) {
					$ife_cats[ $ife_catk ] = (int) $ife_catv;
				}
			}
			if ( ! empty( $ife_cats ) ) {
				if (!($is_exitsing_event && ! $importevents->common->wpea_is_updatable('category') )) {
					wp_set_object_terms( $inserted_event_id, $ife_cats, $this->taxonomy );
				}
			}

			// Assign Featured images
			$event_image = $centralize_array['image_url'];
			if( $event_image != '' ){
				$importevents->common->setup_featured_image_to_event( $inserted_event_id, $event_image );
			}else{
				if( $is_exitsing_event ){
					delete_post_thumbnail( $inserted_event_id );
				}
			}

			//////////////////////////////////////////////
			// Event Date & time Details
			$event_start_date = date( 'Y-m-d', $start_time );
			$event_end_date   = date( 'Y-m-d', $end_time );
			$event_start_time = date( 'H:i', $start_time );
			$event_end_time   = date( 'H:i', $end_time );
			$set_st_time      = date( 'Y-m-d H:i', $start_time );
			$set_end_time     = date( 'Y-m-d H:i', $end_time );

			// Venue Deatails
			$address_1 = isset( $venue_array['address_1'] ) ? $venue_array['address_1'] : '';
			$venue_array = isset( $centralize_array['location'] ) ? $centralize_array['location'] : array();
			$venue_name    = isset( $venue_array['name'] ) ? sanitize_text_field( $venue_array['name'] ) : '';
			$eventum_address = '';
			$venue_address = isset( $venue_array['full_address'] ) ? sanitize_text_field( $venue_array['full_address'] ) : sanitize_text_field( $address_1 );
			$venue_city    = isset( $venue_array['city'] ) ? sanitize_text_field( $venue_array['city'] ) : '';
			$venue_state   = isset( $venue_array['state'] ) ? sanitize_text_field( $venue_array['state'] ) : '';
			$venue_country = isset( $venue_array['country'] ) ? sanitize_text_field( $venue_array['country'] ) : '';
			$venue_zipcode = isset( $venue_array['zip'] ) ? sanitize_text_field( $venue_array['zip'] ) : '';

			$venue_lat     = isset( $venue_array['lat'] ) ? sanitize_text_field( $venue_array['lat'] ) : '';
			$venue_lon     = isset( $venue_array['long'] ) ? sanitize_text_field( $venue_array['long'] ) : '';
			$venue_url     = isset( $venue_array['url'] ) ? esc_url( $venue_array['url'] ) : '';

			$address_array = compact( "venue_address", "venue_city", "venue_state", "venue_country", "venue_zipcode" );
			$address_array = array_filter($address_array, create_function('$value', 'return $value != "";' ) );
			$eventum_address = implode(', ', $address_array );

			if( $venue_name != $venue_address && $venue_name != '' ){
				$eventum_address = $venue_name.', '.$eventum_address;
			}
			
			$country_id = $state_id = $city_id = '';
			
			// Check Tevolution Location Manager Plugin is activate
			if( is_plugin_active('Tevolution-LocationManager/location-manager.php') ){
				if( $venue_country != '' ){
					$country_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT country_id FROM ".$wpdb->prefix."countries WHERE country_name = '%s' OR iso_code_2 = '%s' LIMIT 1", $venue_country, strtoupper($venue_country) ) );	
				}

				if( $venue_state != '' ){
					if( $country_id != '' && is_numeric( $country_id ) && $country_id > 0 ){
						$state_query = $wpdb->prepare( "SELECT DISTINCT zones_id FROM ".$wpdb->prefix."zones WHERE country_id = %d AND ( zone_code = '%s' OR zone_name = '%s' ) LIMIT 1", $country_id, strtoupper( $venue_state ), $venue_state );
					}else{
						$state_query = $wpdb->prepare( "SELECT DISTINCT zones_id FROM ".$wpdb->prefix."zones WHERE ( zone_code = '%s' OR zone_name = '%s' ) LIMIT 1", strtoupper( $venue_state ), $venue_state );	
					}
					$state_id = $wpdb->get_var( $state_query );

					if( ( empty( $state_id ) || is_null( $state_id ) ) && $country_id != '' ){
						$state_args = array( 
							'country_id' => $country_id, 
							'zone_code'  => substr( strtoupper( $venue_state ) , 0, 8 ),
							'zone_name'  => $venue_state
						);
						
						$sinserted = $wpdb->insert( $wpdb->prefix."zones", $state_args );
						if( $sinserted ){
							$state_id = $wpdb->insert_id;
						}
					}
				}

				if( $venue_city != '' ){
					if( $country_id != '' || $state_id != '' ){
						if( $country_id != '' || $state_id != '' ){
							$city_query = $wpdb->prepare( "SELECT DISTINCT city_id FROM ".$wpdb->prefix."multicity WHERE country_id = %d AND zones_id = %d AND cityname = '%s' LIMIT 1", $country_id, $state_id, $venue_city );		
						} elseif( $country_id != '' ){
							$city_query = $wpdb->prepare( "SELECT DISTINCT city_id FROM ".$wpdb->prefix."multicity WHERE country_id = %d AND cityname = '%s' LIMIT 1", $country_id, $venue_city );

						} elseif( $zones_id != '' ){
							$city_query = $wpdb->prepare( "SELECT DISTINCT city_id FROM ".$wpdb->prefix."multicity WHERE zones_id = %d AND cityname = '%s' LIMIT 1", $state_id, $venue_city );
						}

					}else{
						$city_query = $wpdb->prepare( "SELECT DISTINCT city_id FROM ".$wpdb->prefix."multicity WHERE cityname = '%s' LIMIT 1", $venue_city );	
					}
					$city_id = $wpdb->get_var( $city_query );
					if( ( empty( $city_id ) || is_null( $city_id ) ) && $country_id != '' ){
						if( $state_id == '' ){
							$state_id = 0;
						}
						$city_args = array( 
							'country_id' 	=> $country_id, 
							'zones_id'   	=> $state_id,
							'cityname'   	=> $venue_city,
							'city_slug'   	=> sanitize_title( $venue_city ),
							'scall_factor' 	=> 13,
							'is_zoom_home' 	=> 1,
							'map_type'	   	=> 'ROADMAP',
							'post_type'    	=> 'event',
							'categories'	=> 'all,',

						);
						$cinserted = $wpdb->insert( $wpdb->prefix."multicity", $city_args );
						if( $cinserted ){
							$city_id = $wpdb->insert_id;
						}
					}
				}
			}

			// Oraganizer Deatails
			$organizer_array = isset( $centralize_array['organizer'] ) ? $centralize_array['organizer'] : array();
			$organizer_name  = isset( $organizer_array['name'] ) ? sanitize_text_field( $organizer_array['name'] ) : '';
			$organizer_email = isset( $organizer_array['email'] ) ? sanitize_text_field( $organizer_array['email'] ) : '';
			$organizer_phone = isset( $organizer_array['phone'] ) ? sanitize_text_field( $organizer_array['phone'] ) : '';
			$organizer_url   = isset( $organizer_array['url'] ) ? sanitize_text_field( $organizer_array['url'] ) : '';

			// Save Event Data
			// Date & Time
			update_post_meta( $inserted_event_id, 'event_type', 'Regular event' );
			update_post_meta( $inserted_event_id, 'featured_h', 'n' );
			update_post_meta( $inserted_event_id, 'featured_c', 'n' );
			update_post_meta( $inserted_event_id, 'featured_type', 'none' );
			update_post_meta( $inserted_event_id, 'st_date', $event_start_date );
			update_post_meta( $inserted_event_id, 'end_date', $event_end_date );
			update_post_meta( $inserted_event_id, 'st_time', $event_start_time );
			update_post_meta( $inserted_event_id, 'end_time', $event_end_time );
			update_post_meta( $inserted_event_id, 'set_st_time', $set_st_time );			
			update_post_meta( $inserted_event_id, 'set_end_time', $set_end_time );
			update_post_meta( $inserted_event_id, 'website', $ticket_uri );

			// Venue
			update_post_meta( $inserted_event_id, 'address', $eventum_address );
			update_post_meta( $inserted_event_id, 'geo_latitude', $venue_lat );
			update_post_meta( $inserted_event_id, 'geo_longitude', $venue_lon );

			update_post_meta( $inserted_event_id, '_venue_name', $venue_name );
			update_post_meta( $inserted_event_id, '_venue_address', $venue_address );
			update_post_meta( $inserted_event_id, '_venue_city', $venue_city );
			update_post_meta( $inserted_event_id, '_venue_state', $venue_state );
			update_post_meta( $inserted_event_id, '_venue_country', $venue_country );
			update_post_meta( $inserted_event_id, '_venue_zipcode', $venue_zipcode );
			update_post_meta( $inserted_event_id, '_venue_lat', $venue_lat );
			update_post_meta( $inserted_event_id, '_venue_lon', $venue_lon );
			update_post_meta( $inserted_event_id, '_venue_url', $venue_url );
			if( $country_id != '' && is_numeric( $country_id ) && $country_id > 0 ){
				update_post_meta( $inserted_event_id, 'country_id', $country_id );
			}
			if( $state_id != '' && is_numeric( $state_id ) && $state_id > 0 ){
				update_post_meta( $inserted_event_id, 'zones_id', $state_id );
			}
			if( $city_id != '' && is_numeric( $city_id ) && $city_id > 0 ){	
				update_post_meta( $inserted_event_id, 'post_city_id', $city_id );
			}

			// Organizer
			update_post_meta( $inserted_event_id, 'organizer_name', $organizer_name );
			update_post_meta( $inserted_event_id, 'organizer_email', $organizer_email );
			update_post_meta( $inserted_event_id, 'organizer_mobile', $organizer_phone );
			update_post_meta( $inserted_event_id, 'organizer_website', $organizer_url );

			update_post_meta( $inserted_event_id, 'wpea_event_id', $centralize_array['ID'] );
			update_post_meta( $inserted_event_id, 'wpea_event_link', esc_url( $ticket_uri ) );
			update_post_meta( $inserted_event_id, 'wpea_event_origin', $event_args['import_origin'] );
			update_post_meta( $inserted_event_id, '_wpea_starttime_str', $start_time );
			update_post_meta( $inserted_event_id, '_wpea_endtime_str', $end_time );


			if ( $is_exitsing_event ) {
				do_action( 'wpea_after_update_eventum_'.$centralize_array["origin"].'_event', $inserted_event_id, $centralize_array );
				return array(
					'status' => 'updated',
					'id' 	 => $inserted_event_id
				);
			}else{
				do_action( 'wpea_after_create_eventum_'.$centralize_array["origin"].'_event', $inserted_event_id, $centralize_array );
				return array(
					'status' => 'created',
					'id' 	 => $inserted_event_id
				);
			}

		}else{
			return array( 'status'=> 0, 'message'=> 'Something went wrong, please try again.' );
		}

	}

}
