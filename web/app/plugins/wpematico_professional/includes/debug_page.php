<?php
/**
* It will be used to manage all feature on the debug page.
* @package     WPeMatico Professional
* @subpackage  Campaign fetch.
* @since       1.7.5
*/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
* Debug Page Class 
* @since 1.8.0
*/
if (!class_exists('WPeMaticoPro_Debug_Page')) :
class WPeMaticoPro_Debug_Page {
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.8.0
	*/
	public static function hooks() {
		if (isset($_GET['page']) && $_GET['page'] == 'wpematico_settings' &&
				 isset($_GET['tab']) && $_GET['tab'] == 'debug_info') {
		//Debug data in debug page
		add_filter('wpematico_sysinfo_after_wpematico_config', array( __CLASS__, 'debug_data_cfg'), 5);
		add_action('wpematico_download_debug_file_extra_data', array(__CLASS__, 'debug_file_campaigns'));
		add_action('wpematico_debug_page_form_options', array(__CLASS__, 'debug_form_options'));
		add_action('admin_print_scripts', array(__CLASS__, 'scripts'));
		add_action('admin_print_styles', array(__CLASS__, 'styles'));
		}
	}
	/**
	* Static function scripts
	* @access public
	* @return void
	* @since 1.8.0
	*/
	public static function scripts() {
//		if (isset($_GET['tab']) && $_GET['tab'] == 'debug_info') { // Only print the JS file on debug page.
			wp_enqueue_script('wpepro-debug-page',  WPeMaticoPRO::$uri.'assets/js/debug_page.js', array( 'jquery' ), WPEMATICOPRO_VERSION, true );
//		}
	}
	/**
	* Static function styles
	* @access public
	* @return void
	* @since 1.8.0
	*/
	public static function styles() {
//		if (isset($_GET['tab']) && $_GET['tab'] == 'debug_info') { // Only print the CSS file on debug page.
			wp_enqueue_style('wpepro-debug-page-css', WPeMaticoPRO::$uri .'assets/css/debug_page.css');	
//		}
	}
	/**
	* Static function debug_form_options
	* @access public
	* @return void
	* @since 1.8.0
	*/
	public static function debug_form_options() {
		?>
		<label><input class="checkbox" value="1" type="checkbox" name="include_cammpaigns" id="include_cammpaigns" /> <?php _e('Select campaigns to include.', 'wpematico' ); ?></label><br/>
		<div id="debug_page_include_campaigns_div">
			<table class="wp-list-table widefat fixed striped posts debug-list-campaigns">
				<tr>
					<td class="debug-list-td-check">
						<?php _e('Check', 'wpematico' ); ?>
					</td>
					<td>
						<?php _e('Campaign Name', 'wpematico' ); ?>
					</td>
				</tr>
			
			<?php 
			$args = array(
				'orderby'         => 'ID',
				'order'           => 'ASC',
				'post_type'       => 'wpematico', 
				'numberposts' => -1
			);
			$campaigns = get_posts( $args );
			foreach( $campaigns as $post ): ?>
				<tr>
					<td>
						<input class="checkbox" value="<?php echo $post->ID; ?>" type="checkbox" name="checked_campaign[]" id="checked_campaign_<?php echo $post->ID; ?>" /> 
					</td>
					<td>
						<?php echo $post->post_title; ?>
					</td>
				</tr>
			<?php 
			endforeach;  
			?>
			</table>
		</div>
		<?php
	}
	/**
	* Static function debug_file_campaigns
	* @access public
	* @return void
	* @since 1.8.0
	*/
	public static function debug_file_campaigns() {
		if (!empty($_REQUEST['include_cammpaigns'])) {
			
			if (empty($_REQUEST['checked_campaign'])) {
				$_REQUEST['checked_campaign'] = array();
				$args = array(
					'orderby'         => 'ID',
					'order'           => 'ASC',
					'post_type'       => 'wpematico', 
					'numberposts'	  => -1
				);
				$campaigns = get_posts( $args );
				foreach( $campaigns as $post ) {
					$_REQUEST['checked_campaign'][] = $post->ID;
				} 
			}
			
			if (is_array($_REQUEST['checked_campaign'])) {
				$new_campaigns_data = array();
				foreach ($_REQUEST['checked_campaign'] as $index => $id) {
					if (!is_numeric($id)) {
						continue;
					}
					$wpecampaign = NoNStatic::get_exported_campaign($id);
					$new_campaigns_data[] = base64_encode($wpecampaign);
				}
				$new_campaigns_data_json = json_encode($new_campaigns_data);
				echo "\r\nCampaign code start ------- \r\n" . $new_campaigns_data_json . "\r\n ------- Campaign code end \r\n";
			
			}
		}
		
	}
	public static function debug_data_cfg($return) {
			// WPeMatico PRO configuration
		$cfg = get_option(WPeMaticoPRO::OPTION_KEY);
		$return .= "\n" . '-- WPeMatico PROFESSIONAL Configuration' . "\n\n";
		$return .= 'Version:                  ' . WPEMATICOPRO_VERSION . "\n";

		foreach($cfg as $name => $value): 
			if ( wpematico_option_blacklisted($name)) continue; 
			$value = sanitize_option($name, $value); 
			$return .= $name . ":\t\t" . ((is_array($value))? print_r($value,1): esc_html($value)) . "\n";
		endforeach;
			
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		$plugin_args_name = 'pro_licenser';
		$args_plugin = $plugins_args[$plugin_args_name];
		$license = wpematico_licenses_handlers::get_key($plugin_args_name);
		$license_status = wpematico_licenses_handlers::get_license_status($plugin_args_name);
		$expire_license = 'No expiration';
		if ($license != false) {		
			$args_check = array(
				'license' 	=> $license,
				'item_name' => urlencode($args_plugin['api_data']['item_name']),
				'url'       => home_url(),
				'version' 	=> $args_plugin['api_data']['version'],
				'author' 	=> 'Esteban Truelsegaard'	
			);
			$api_url = $args_plugin['api_url'];
			$license_data = wpematico_licenses_handlers::check_license($api_url, $args_check);
			if (is_object($license_data)) {
								
				$expires = !empty($license_data->expires) ? $license_data->expires : 0;
				$expires = substr( $expires, 0, strpos( $expires, " "));
								
				if (!empty($license_data->payment_id) && !empty($license_data->license_limit)) {
					$expire_license = $expires;
				}
			}
		}

		if ($license_status == false) {
			$license_status = 'No license';
		}
		$return .= 'License Status:           ' . $license_status . "\n";
		$return .= 'License Expiration:       ' . $expire_license . "\n";


		/*	
		$return .= "\n" . '-- First 3 CAMPAIGNS' . "\n\n";
		$allcampaigns = WPeMatico::get_campaigns();
		$qty = 1;
		$new_campaigns_data = array();
		foreach($allcampaigns as $key => $campaign ): 
			$wpecampaign = NoNStatic::get_exported_campaign($campaign['ID']);
			$new_campaigns_data[] = base64_encode($wpecampaign);
			if ($qty++ == 3) break ;
		endforeach;
		$new_campaigns_data_json = json_encode($new_campaigns_data);
		$return .= "Campaign code start -------" . $new_campaigns_data_json . "\n ------- Campaign code end \n\n";
		*/
		return $return;
	}
}
endif;
WPeMaticoPro_Debug_Page::hooks();