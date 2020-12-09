<?php
/**
 * Plugin Name:       WP Event Aggregator Pro
 * Plugin URI:        http://xylusthemes.com/plugins/wp-event-aggregator/
 * Description:       Import Events from anywhere - Facebook, Eventbrite, Meetup, iCalendar and ICS into your WordPress site.
 * Version:           1.5.5
 * Author:            Xylus Themes
 * Author URI:        http://xylusthemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-event-aggregator-pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_Event_Aggregator_Pro' ) ):

/**
* Main WP Event Aggregator class
*/
class WP_Event_Aggregator_Pro{
	
	/** Singleton *************************************************************/
	/**
	 * WP_Event_Aggregator_Pro The one true WP_Event_Aggregator_Pro.
	 */
	private static $instance;

    /**
     * Main WP Event Aggregator Pro Instance.
     * 
     * Insure that only one instance of WP_Event_Aggregator_Pro exists in memory at any one time.
     * Also prevents needing to define globals all over the place.
     *
     * @since 1.0.0
     * @static object $instance
     * @uses WP_Event_Aggregator_Pro::setup_constants() Setup the constants needed.
     * @uses WP_Event_Aggregator_Pro::includes() Include the required files.
     * @uses WP_Event_Aggregator_Pro::laod_textdomain() load the language files.
     * @see run_wp_event_aggregator_pro()
     * @return object| WP Event Aggregator the one true WP Event Aggregator.
     */
	public static function instance() {
		if( ! isset( self::$instance ) && ! (self::$instance instanceof WP_Event_Aggregator_Pro ) ) {
			self::$instance = new WP_Event_Aggregator_Pro;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			add_action( 'plugins_loaded', array( self::$instance, 'wpea_load_pro_addon_classes' ) );
			
			if( wpea_free_plugin_activated() ){
				self::$instance->includes();				
				
				// register the widget
				add_action( 'widgets_init', array( self::$instance, 'wpea_register_upcoming_widget' ) );
				// Before VC Init
				add_action( 'vc_before_init', array( self::$instance, 'wpea_vc_before_init_actions' ) );
			}else{
				add_action( 'admin_notices', array( self::$instance, 'wepa_free_activatation_notice' ) );
			}
			
		}
		return self::$instance;	
	}

	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent WP_Event_Aggregator_Pro from being loaded more than once.
	 *
	 * @since 1.0.0
	 * @see WP_Event_Aggregator_Pro::instance()
	 * @see run_wp_event_aggregator_pro()
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent WP_Event_Aggregator_Pro from being cloned.
	 *
	 * @since 1.0.0
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-event-aggregator-pro' ), '1.5.5' ); }

	/**
	 * A dummy magic method to prevent WP_Event_Aggregator_Pro from being unserialized.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-event-aggregator-pro' ), '1.5.5' ); }


	/**
	 * Register Upcoming Events Widget
	 *
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	public function wpea_register_upcoming_widget(){
		register_widget( 'WP_Event_Aggregator_Pro_Upcoming_Widget' );
	}

	/**
	 * Include Visual Composer Custom element class for WP Events.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	function wpea_vc_before_init_actions() {
		// Require VC Element
		require_once WPEAPRO_PLUGIN_DIR . 'includes/page-builder/class-wpea-vc-wp-events.php';
	}

	/**
	 * Display Notice for install free version of WP Event Aggregator.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	function wepa_free_activatation_notice() {
		?>
		<div class="error">
			<p>
				<?php 
				printf( __( '<strong>WP Event Aggregator Pro</strong> requires free version <a href="%s" target="_blank">WP Event Aggregator</a>. Please <a href="%s" class="thickbox open-plugin-details-modal">Install</a> & Activate it. <a href="%s" target="_blank">More info.</a>', 'wp-event-aggregator-pro' ), 'https://wordpress.org/plugins/wp-event-aggregator/', admin_url( "plugin-install.php?tab=plugin-information&plugin=wp-event-aggregator&TB_iframe=true&width=600&height=550" ), 'http://docs.xylusthemes.com/docs/wp-event-aggregator/plugin-installation-pro/' );
				?>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Setup plugins constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if( ! defined( 'WPEAPRO_VERSION' ) ){
			define( 'WPEAPRO_VERSION', '1.5.5' );
		}

		// Minimum free plugin version.
		if( ! defined( 'WPEA_MIN_FREE_VERSION' ) ){
			define( 'WPEA_MIN_FREE_VERSION', '1.5.5' );
		}

		// Plugin folder Path.
		if( ! defined( 'WPEAPRO_PLUGIN_DIR' ) ){
			define( 'WPEAPRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URL.
		if( ! defined( 'WPEAPRO_PLUGIN_URL' ) ){
			define( 'WPEAPRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin root file.
		if( ! defined( 'WPEAPRO_PLUGIN_FILE' ) ){
			define( 'WPEAPRO_PLUGIN_FILE', __FILE__ );
		}

		// Options
		if( ! defined( 'WPEAPRO_OPTIONS' ) ){
			define( 'WPEAPRO_OPTIONS', 'wpea_options' );
		}
		
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		require_once WPEAPRO_PLUGIN_DIR . 'includes/class-wp-event-aggregator-common.php';
		require_once WPEAPRO_PLUGIN_DIR . 'includes/class-wp-event-aggregator-cron.php';
		require_once WPEAPRO_PLUGIN_DIR . 'includes/class-wp-event-aggregator-widgets.php';
		
		require_once WPEAPRO_PLUGIN_DIR . 'includes/class-wp-event-aggregator-facebook.php';
		require_once WPEAPRO_PLUGIN_DIR . 'includes/class-wp-event-aggregator-eventum.php';
		require_once WPEAPRO_PLUGIN_DIR . 'includes/lib/wp-event-aggregator-license.php';	
	}

	/**
	 * Loads the plugin language files.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain(){

		load_plugin_textdomain(
			'wp-event-aggregator-pro',
			false,
			basename( dirname( __FILE__ ) ) . '/languages'
		);
	
	}

	/**
	 * Loads Pro Addon Classes
	 * 
	 * @access public
	 * @since 1.5.0
	 * @return void
	 */
	public function wpea_load_pro_addon_classes(){
		global $importevents;
		if( !empty( $importevents ) && wpea_free_plugin_activated() ){
			$importevents->common_pro = new WP_Event_Aggregator_Pro_Common();
			$importevents->facebook_pro = new WP_Event_Aggregator_Pro_Facebook();
			$importevents->eventum = new WP_Event_Aggregator_Pro_Eventum();
			$importevents->cron = new WP_Event_Aggregator_Pro_Cron();
		}
	}
}

endif; // End If class exists check.

/**
 * The main function for that returns WP_Event_Aggregator_Pro
 *
 * The main function responsible for returning the one true WP_Event_Aggregator_Pro
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpeaevents_pro = run_wp_event_aggregator_pro(); ?>
 *
 * @since 1.0.0
 * @return object|WP_Event_Aggregator_Pro The one true WP_Event_Aggregator_Pro Instance.
 */
function run_wp_event_aggregator_pro() {
	return WP_Event_Aggregator_Pro::instance();
}

// Get WP_Event_Aggregator_Pro Running.
$wpeaevents_pro = run_wp_event_aggregator_pro();

/**
 * Check Free version of WP Event Aggregator installed or not.
 *
 * @since 1.5.0
 * @return boolean
 */
function wpea_free_plugin_activated(){
	if( !function_exists( 'is_plugin_active' ) ){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	if ( is_plugin_active( 'wp-event-aggregator/wp-event-aggregator.php' ) ) {
		return true;
	}
	return false;
}
