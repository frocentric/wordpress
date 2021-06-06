<?php

/**
 * Class NF_Webhooks
 */
class NF_Webhooks
{
	function __construct() {
	    // Define our plugin directory.
	    if ( ! defined( 'NF_WH_DIR' ) )
	        define( 'NF_WH_DIR', plugin_dir_path( __FILE__ ) );

	    // Define our plugin URL.
	    if ( ! defined( 'NF_WH_URL' ) )
	        define( 'NF_WH_URL', plugin_dir_url( __FILE__ ) );
		
		add_filter( 'nf_notification_types', array( $this, 'register_action_type' ) );
		add_action( 'admin_init', array( $this, 'register_licensing' ) );

		add_filter( 'nf_export_form_row', array( $this, 'filter_export_form_row' ) );
	}

	public function register_action_type( $types ) {
		$types['webhooks'] = require_once( NF_WH_DIR . 'classes/action-webhooks.php' );
		return $types;
	}

	public function register_licensing() {
		if ( class_exists( 'NF_Extension_Updater' ) ) {
	    	$NF_Extension_Updater = new NF_Extension_Updater( 'Webhooks', NF_WH_VERSION, 'WP Ninjas', __FILE__ );
		}
	}

	public function filter_export_form_row( $form_row ) {

		if( isset( $form_row[ 'notifications' ] ) ){
			foreach( $form_row[ 'notifications' ] as $id => $notification ){
				
				if( 'webhooks' != $notification[ 'type' ] ) continue;

				$form_row[ 'notifications' ][ $id ][ 'wh-args' ] = nf_get_object_children( $id, 'wh_args' );
			}
		}

		return $form_row;
	}
}

new NF_Webhooks();