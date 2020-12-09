<?php
/**
 * Class for Custom Visual Composer Element
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Element Class
class WPEAPRO_VC_WPEvents extends WPBakeryShortCode {

	// Element Init
    function __construct() {
        add_action( 'init', array( $this, 'vc_wpevents_mapping' ) );
    }

    // Element Mapping
    public function vc_wpevents_mapping() {

        // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }

        $event_cats = get_terms( 'event_category', array( 'hide_empty' => false ) );
        $categories = array( __('All Categories', 'wp-event-aggregator-pro' ) => '' );
        if( !empty( $event_cats ) ){
			foreach ( $event_cats as $event_cat ) {
				$categories[$event_cat->name] = $event_cat->term_id;
		}
        }

        // Map the block with vc_map()
        vc_map(
            array(
                'name' => __('WP Event Aggregator Events', 'wp-event-aggregator-pro'),
                'base' => 'wp_events',
                'category' => __('WP Event Aggregator', 'wp-event-aggregator-pro'),
                'icon' => WPEA_PLUGIN_URL.'assets/images/wpea_icon.jpg',

                'params' => array(
					array(
                        'type' 		  => 'dropdown',
                        'class' 	  => 'category-class',
                        'heading' 	  => __( 'Event Category', 'wp-event-aggregator-pro' ),
                        'param_name'  => 'category',
                        'value' 	  => $categories,
                        'description' => __( 'Select Event Category from which you want to show Events.', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' 	  => 'General',
                    ),

                    array(
                        'type' => 'dropdown',
                        'class' => 'col-class',
                        'heading' => __( 'Columns', 'wp-event-aggregator-pro' ),
                        'param_name' => 'col',
                        'value' => array(
							__( '1 Column', 'wp-event-aggregator-pro' ) => '1',
							__( '2 Columns', 'wp-event-aggregator-pro' ) => '2',
							__( '3 Columns', 'wp-event-aggregator-pro' ) => '3',
							__( '4 Columns', 'wp-event-aggregator-pro' ) => '4',
                        ),
                        'std'         => '3', // default value
                        'description' => __( 'How many columns you want to set in Events grid.', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                    ),

                    array(
                        'type' => 'dropdown',
                        'class' => 'past_events-class',
                        'heading' => __( 'Show Past Events', 'wp-event-aggregator-pro' ),
                        'param_name' => 'past_events',
                        'value' => array(
							__( 'No', 'wp-event-aggregator-pro' ) => 'no',
							__( 'Yes', 'wp-event-aggregator-pro' ) => 'yes'
                        ),
                        'std'         => 'no', // default value
                        'description' => __( 'Want to show past events?', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                    ),

                    array(
                        'type' => 'textfield',
                        'class' => 'start_date-class',
                        'edit_field_class' => 'wpea_datepicker vc_col-xs-6',
                        'heading' => __( 'Start Date', 'wp-event-aggregator-pro' ),
                        'param_name' => 'start_date',
                        'description' => __( 'Show events from this date.', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                        'dependency' => array(
							'element'   => 'past_events',
							'value'		=> 'no',
                        ),
                    ),

                    array(
                        'type' => 'textfield',
                        'edit_field_class' => 'wpea_datepicker vc_col-xs-6',
                        'heading' => __( 'End Date', 'wp-event-aggregator-pro' ),
                        'param_name' => 'end_date',
                        'description' => __( 'Show events till this date.', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                        'dependency' => array(
							'element'   => 'past_events',
							'value'		=> 'no',
                        ),
                    ),

                    array(
                        'type' => 'textfield',
                        'edit_field_class' => 'vc_col-xs-6',
                        'heading' => __( 'Order By', 'wp-event-aggregator-pro' ),
                        'param_name' => 'orderby',
                        'description' => __( 'Enter Events Orderby examples: start_date, end_date. default: start_date.', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                    ),

                    array(
                        'type' => 'dropdown',
                        'class' => 'order-class',
                        'edit_field_class' => 'vc_col-xs-6',
                        'heading' => __( 'Order', 'wp-event-aggregator-pro' ),
                        'param_name' => 'order',
                        'value' => array(
							'ASC',
							'DESC'
                        ),
                        'std'         => 'ASC', // default value
                        'description' => __( 'Order of Events, Depends on orderby', 'wp-event-aggregator-pro' ),
                        'admin_label' => false,
                        'group' => 'General',
                    ),
                ),
			)
        );

    }
}

// Visual Composer Element Class Init
new WPEAPRO_VC_WPEvents();