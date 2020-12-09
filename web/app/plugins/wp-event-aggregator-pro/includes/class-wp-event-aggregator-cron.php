<?php
/**
 * Import Events Cron.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Pro_Cron {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct() {
		$this->load_scheduler();
	}

	/**
	 * Load the all requred hooks for run scheduler
	 *
	 * @since    1.0.0
	 */
	public function load_scheduler() {
		// Remove cron on delete meetup url.
		add_action( 'delete_post', array( $this, 'remove_scheduled_import' ) );

		// setup custom cron recurrences.
		add_filter( 'cron_schedules', array( $this, 'setup_custom_cron_recurrences' ) );
		
		// run scheduled importer
		add_action( 'xt_run_scheduled_import', array( $this, 'run_scheduled_importer' ), 100 );
	}

	/**
	 * Run scheduled event importer.
	 *
	 * @since    1.0.0
	 * @param int $post_id Options.
	 * @return null/void
	 */
	public function run_scheduled_importer( $post_id = 0 ) {
		global $importevents;

		$post = get_post( $post_id );
		if( !$post || empty( $post ) ){
			return; 
		}
		$import_origin = get_post_meta( $post_id, 'import_origin', true );
		$import_eventdata = get_post_meta( $post_id, 'import_eventdata', true );

		if( 'eventbrite' == $import_origin ){

			$import_events = $importevents->eventbrite->import_events( $import_eventdata );
			if( $import_events && !empty( $import_events ) ){
				$importevents->common->display_import_success_message( $import_events, $import_eventdata, $post_id );
			}

		}elseif( 'meetup' == $import_origin ){

			$import_events = $importevents->meetup->import_events( $import_eventdata );
			if( $import_events && !empty( $import_events ) ){
				$importevents->common->display_import_success_message( $import_events, $import_eventdata, $post_id );
			}

		}elseif( 'facebook' == $import_origin ){

			$import_events = $importevents->facebook->import_events( $import_eventdata );
			if( $import_events && !empty( $import_events ) ){
				$importevents->common->display_import_success_message( $import_events, $import_eventdata, $post_id );
			}

		}elseif( 'ical' == $import_origin ){

			$import_events = $importevents->ical->import_events( $import_eventdata );
			if( $import_events && !empty( $import_events ) ){

				$imported_ids = array();
				foreach ($import_events as $imported_event ) {
					$imported_ids[] = $imported_event['id'];
				}
				$imported_ids = array_unique( $imported_ids );
				$old_imported_ids = get_post_meta( $post_id, 'wpea_sync_events', true );
				
				// Advanced Synchronization Start
				$wpea_ical_options = get_option( WPEA_OPTIONS );
				$advanced_sync = isset($wpea_ical_options['ical']['advanced_sync']) ? $wpea_ical_options['ical']['advanced_sync'] : 'no';
				if( $advanced_sync == 'yes' ){
					if( is_array( $old_imported_ids ) ){
						$array_diff = array_diff( $old_imported_ids, $imported_ids );
						if( !empty( $array_diff ) ){
							$this->wpea_delete_events_during_sync( $array_diff );
						}
					}
				}else{
					if( is_array( $old_imported_ids ) ){
						$imported_ids = array_merge( $old_imported_ids, $imported_ids );
					}
					$imported_ids = array_unique( $imported_ids );
				}
				// Advanced Synchronization End

				update_post_meta( $post_id, 'wpea_sync_events', $imported_ids );
				
				$importevents->common->display_import_success_message( $import_events, $import_eventdata, $post_id );
			}
		}

	}

	/**
	 * Setup cron on add new scheduled import.
	 *
	 * @since    1.0.0
	 * @param int 	 $post_id Post ID.
	 * @param object $post Post.
	 * @param bool   $update is update or new insert.
	 * @return void
	 */
	public function setup_scheduled_import( $post_id, $post, $update ) {
		// check if not post update.
		if ( ! $update ) {

			$import_eventdata = get_post_meta( $post_id, 'import_eventdata', true );
			$import_frequency = isset( $import_eventdata['import_frequency']) ? $import_eventdata['import_frequency'] : 'twicedaily';
			wp_schedule_event( time(), $import_frequency, 'xt_run_scheduled_import', array( 'post_id' => $post_id ) );

		}
	}

	/**
	 * Remove saved cron scheduled import on delete scheduled event.
	 *
	 * @since    1.0.0
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function remove_scheduled_import( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type == 'xt_scheduled_imports' ){
			wp_clear_scheduled_hook( 'xt_run_scheduled_import', array( 'post_id' => $post_id ) );
		}
	}

	/**
	 * Setup custom cron recurrences.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function setup_custom_cron_recurrences( $schedules ) {
		// Weekly Schedule.
		$schedules['weekly'] = array(
			'display' => __( 'Once Weekly', 'wp-event-aggregator-pro' ),
			'interval' => 604800,
		);
		// Monthly Schedule.
		$schedules['monthly'] = array(
			'display' => __( 'Once a Month', 'wp-event-aggregator-pro' ),
			'interval' => 2635200,
		);
		return $schedules;
	}

	/**
	 * Delete events during sync
	 *
	 * @since    1.2.5
	 * @access   public
	 */
	public function wpea_delete_events_during_sync( $events ) {
		if( !empty( $events ) ){
			foreach ($events as $event ) {
				$wpea_event_origin = get_post_meta( $event, 'wpea_event_origin', true );
				if( $wpea_event_origin == 'ical' ){
					wp_trash_post( $event );
				}
			}
		}
	}	

}
