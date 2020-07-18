<?php
/**
 * Common functions class for WP Event aggregator.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    WP_Event_Aggregator_Pro
 * @subpackage WP_Event_Aggregator_Pro/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Event_Aggregator_Pro_Common {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'wpea_admin_settings_start', array( $this, 'wpea_render_donot_update_data_settings' ) );
		add_action( 'admin_menu', array( $this, 'wpea_add_licence_menu' ) );
	}

	/**
	 * Add Pro settings in Settings section.
	 * 
	 * @access public
	 * @since 1.5.0
	 * @return void
	 */
	public function wpea_render_donot_update_data_settings(){
		$wpea_options = get_option( WPEA_OPTIONS );
		$aggregator_options = isset($wpea_options['wpea'])? $wpea_options['wpea'] : array();
		$sdontupdate = isset( $aggregator_options['dont_update']['status'] ) ? $aggregator_options['dont_update']['status'] : 'no';
	    $cdontupdate = isset( $aggregator_options['dont_update']['category'] ) ? $aggregator_options['dont_update']['category'] : 'no';
		?>
		<tr>
	        <th scope="row">
	            <?php _e( "Don't Update these data.", "wp-event-aggregator" ); ?> : 
	        </th>
	        <td>
	            <input type="checkbox" name="wpea[dont_update][status]" value="yes" <?php if( $sdontupdate == 'yes' ) { echo 'checked="checked"'; } ?> />
	            <span class="xtei_small">
	                <?php _e( 'Status ( Publish, Pending, Draft etc.. )', 'wp-event-aggregator-pro' ); ?>
	            </span><br/>

	            <input type="checkbox" name="wpea[dont_update][category]" value="yes" <?php if( $cdontupdate == 'yes' ) { echo 'checked="checked"'; } ?> />
	            <span class="xtei_small">
	                <?php _e( 'Event category', 'wp-event-aggregator-pro' ); ?>
	            </span><br/>
	            <span class="wpea_small">
	                <?php _e( "Select data which you don't want to update during existing events update. (This is applicable only if you have checked 'update existing events')", 'wp-event-aggregator-pro' ); ?>
	            </span>

	        </td>
	    </tr>
		<?php
	}

	/**
	 * Create the Admin submenu and page for license activation
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function wpea_add_licence_menu(){

		add_submenu_page( 'import_events', __( 'License', 'wp-event-aggregator-pro' ), __( 'License', 'wp-event-aggregator-pro' ), 'manage_options', 'wpea_license', array( $this, 'wpea_licence_page' ) );
	}

	/**
	 * Load License page.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	function wpea_licence_page() {
		
		?>
		<div class="wrap wpea_admin_panel">
		    <h2><?php esc_html_e( 'WP Event Aggregator Pro License', 'wp-event-aggregator-pro' ); ?></h2>
		    <div id="poststuff">
		        <div id="post-body" class="metabox-holder columns-2">

		            <div id="postbox-container-1" class="postbox-container">
		            	<?php 
		            	// Sidebar here.
		            	?>
		            </div>
		            <div id="postbox-container-2" class="postbox-container">
		                <div class="wp-event-aggregator-page">

		                	<?php
		                	if( function_exists( 'wpea_pro_license_page') ){
	                			wpea_pro_license_page();
	                		}
			                ?>
		                	<div style="clear: both"></div>
		                </div>

		        </div>
		        
		    </div>
		</div>
		<?php
	}
}