<?php
/*
  Plugin Name: WPeMatico Professional
  Description: Professional features for WPeMatico (Requires WPeMatico FREE activated)
  Plugin URI: https://etruel.com/downloads/wpematico-professional/
  Version: 2.6
  Author: etruel <esteban@netmdp.com>
  Author URI: https://etruel.com
 */

if(!function_exists('add_filter'))
	return;

// Plugin version
if(!defined('WPEMATICOPRO_VERSION')) {
	define('WPEMATICOPRO_VERSION', '2.6');
}

if(!defined('WPEMATICOPRO_UPDATE_CAMPAIGNS')) {
	define('WPEMATICOPRO_UPDATE_CAMPAIGNS', false);
}

if(is_admin()) {
	include_once( ABSPATH . basename(admin_url()) . '/includes/plugin.php' );
}
include( 'lib/pro_licenser.php' );

add_action('init', array('WPeMaticoPRO', 'init'));

add_filter('pro_check_campaigndata', array('NoNStatic', 'pro_check_campaigndata'), 10, 2);
add_filter('wpematico_before_update_campaign', array('NoNStatic', 'pro_update_campaign'), 10, 1);

register_activation_hook(plugin_basename(__FILE__), array('WPeMaticoPRO', 'activate'));
register_deactivation_hook(plugin_basename(__FILE__), array('WPeMaticoPRO', 'deactivate'));
register_uninstall_hook(plugin_basename(__FILE__), array('WPeMaticoPRO', 'uninstall'));
add_action('plugins_loaded', array('WPeMaticoPRO', 'update_db_check'));
add_action('Wpematico_init_fetching', array('WPeMaticoPRO', 'init_fetching')); //hook for add actions and filter on niit fetching

if(!class_exists('WPeMaticoPRO')) {

	class WPeMaticoPRO {

		const TEXTDOMAIN	 = 'WPeMaticoPRO';
		const OPTION_KEY	 = 'WPeMaticoPRO_Options';
		const STORE_URL	 = 'https://etruel.com';
		const AUTHOR		 = 'Esteban Truelsegaard';
		const NAME		 = 'WPeMatico PRO';

//		public static $version = WPEMATICOPRO_VERSION;
		public static $basen;/** Plugin basename * @var string	 */
		public static $uri							 = '';
		public static $dir							 = '';/** filesystem path to the plugin with trailing slash */
		public static $rssimg_add2img_featured_image = '';
		private $requirement;
		protected static $default_options			 = array(
			'enablemultifeed'				 => true,
			'enableimportfeed'				 => false,
			'enableauthorxfeed'				 => false,
			'enablecustomtitle'				 => false,
			'enablecfields'					 => false,
			'enableimgfilter'				 => false,
			'enabletags'					 => false,
			'enablewcf'						 => false,
			'enablekwordf'					 => false,
			'enableeximport'				 => false,
			'enablepromenu'					 => false,
			'enable_ramdom_words_rewrites'	 => false,
			'end_of_the_line'				 => false,
			'end_of_the_line_characters'	 => '. ? !',
			'enable_custom_feed_tags'		 => false,
			'enable_filter_per_author'		 => false,
			'enable_word_to_taxonomy'		 => false,
		);
		protected $options							 = array();

		public static function init() {
			self :: $uri	 = plugin_dir_url(__FILE__);
			self :: $dir	 = plugin_dir_path(__FILE__);
			self :: $basen	 = plugin_basename(__FILE__);

			new self(TRUE);
		}

		public function __construct($hook_in = FALSE) {

			/**
			 * @meets_requirements is returning false on run of WP-CRON.
			 * Because return false when its executed in requests that admin() == false.
			 */
			$this->requirement = $this->meets_requirements();
			if(!$this->requirement)
				return;
//			$plugin_data = WPeMatico::plugin_get_version( __FILE__ );
//			self :: $version = $plugin_data['Version'];

			$this->load_options();

			require_once('includes/cookies.php');
			require_once('includes/campaign_fetch.php');
			require_once('includes/campaign_edit.php');
			require_once('includes/functions.php');
			require_once('includes/partial_curl.php');
			require_once('includes/xml-importer.php');

			require_once('includes/prosettings.php');
			require_once('includes/prosettingsextra.php');
			require_once('includes/core_settings.php');
			require_once('includes/prohelps.php');
			require_once('includes/debug_page.php');

			$newcfg = get_option('WPeMatico_Options');
			if(!$newcfg['nonstatic']) {
				$newcfg['nonstatic'] = true;
				update_option('WPeMatico_Options', $newcfg);
			}

			if($hook_in) {
				//Additional links on the plugin page
				add_filter('plugin_row_meta', array(__CLASS__, 'init_row_meta'), 10, 2);
				add_filter('plugin_action_links_' . self :: $basen, array(__CLASS__, 'init_action_links'));


				if($this->options['enableeximport']) {
					add_filter('post_row_actions', array('NoNStatic', 'wpematico_quick_actions'), 30, 2);
					add_action('admin_action_wpematico_export_campaign', array('NoNStatic', 'wpematico_export_campaign'));
					//add_action('restrict_manage_posts', array( 'NoNStatic', 'import_in_views'), 9 );
					add_action('views_edit-wpematico', array('NoNStatic', 'import_in_views'), 9);
					add_action('wpematico_import_campaign', array('NoNStatic', 'wpematico_import_campaign'));
					add_filter('bulk_actions-edit-wpematico', array('NoNStatic', 'bulk_actions_import_campaign'));
					add_filter('handle_bulk_actions-edit-wpematico', array('NoNStatic', 'bulk_action_handler_import_campaign'), 10, 3);
				}

				if($this->options['enableimportfeed']) {
					add_action('wpematico_campaign_feed_panel', array('NoNStatic', 'feedlist'));
					add_action('wpematico_campaign_feed_panel_buttons', array('NoNStatic', 'bimport'));
				}

				add_action('wpematico_permalinks_tools', array('NoNStatic', 'google_permalinks_option'), 10, 2);

				add_action('wpematico_permalinks_tools', array(__CLASS__, 'add_no_follow_option'), 11, 2);

				add_action('wpematico_pro_parsers_box', array('NoNStatic', 'delete_from_phrase_box'), 12, 2);
				add_action('wpematico_pro_parsers_box', array('NoNStatic', 'last_html_tag'), 15, 2);
				add_action('wpematico_pro_parsers_box', array('NoNStatic', 'flip_paragraphs_box'), 15, 2);

				add_filter('wpematico_helptip_settings', 'wpematico_pro_helptips', 10, 1);
				add_filter('wpematico_help_settings', 'wpematico_pro_helptips', 10, 1);
				add_filter('wpematico_help_settings_rrewrites', 'wpematico_pro_help_settings_rrewrites', 10, 1);
				add_filter('wpematico_help_campaign', 'wpematico_pro_help_campaign', 10, 1);

				add_action('wpematico_create_metaboxes_before', array('NoNStatic', 'create_metaboxes_before'), 10, 2);

				if($this->options['enable_custom_feed_tags']) {
					add_filter('wpematico_template_tags_campaign_edit', array('NoNStatic', 'template_custom_feed_campaign'), 15, 1);
					add_filter('wpematico_pro_template_tags_cf_help', array('NoNStatic', 'template_custom_feed_campaign'), 15, 1);
				}
			}
		}

		/**
		 * Filters to add to the fetch process (When runs a campaign)
		 */
		public static function init_fetching($campaign) {

			$cfg_core		 = get_option('WPeMatico_Options');
			$cfg_core		 = apply_filters('wpematico_check_options', $cfg_core);
			$options_audios	 = WPeMatico::get_audios_options($cfg_core, $campaign);
			$options_videos	 = WPeMatico::get_videos_options($cfg_core, $campaign);


			if(isset($campaign['activate_ramdom_rewrite']) && $campaign['activate_ramdom_rewrite']) {
				add_filter('wpematico_pre_insert_post', array('NoNStatic', 'process_ramdom_rewrites'), 799, 2);
			}


			//add_filter('wpematico_excludes', array('NoNStatic', 'exclfilters'), 10, 4);

			add_filter('wpematico_set_featured_img', array(__CLASS__, 'rssimg_add2img_set_featured_image'), 10, 5);
			add_filter('wpematico_get_featured_img', array(__CLASS__, 'rssimg_add2img_get_featured_image'), 10, 1);

			/**
			 * @since 1.7.0
			 */
			add_filter('wpematico_get_item_audios', array('NoNStatic', 'find_audios'), 10, 4);
			add_filter('wpematico_get_item_videos', array('NoNStatic', 'find_videos'), 10, 4);



			if($campaign['default_img']) {
				//add_filter('wpematico_set_featured_img', array('NoNStatic','custom_img'), 10,5);
				add_filter('wpematico_featured_image_attach_id', array('NoNStatic', 'custom_img'), 999, 5);
			}

			add_filter('wpematico_pre_insert_post', array('NoNStatic', 'strip_tags_title'), 10, 2);
			if($campaign['add_no_follow']) {
				add_filter('wpematico_pre_insert_post', array(__CLASS__, 'add_no_follow_links'), 10, 2);
			}

			add_action('wpematico_inserted_post', array('NoNStatic', 'assign_custom_taxonomies'), 10, 2);

			add_filter('Wpematico_end_fetching', array('NoNStatic', 'ending'), 10, 2);

			if(isset($campaign['fix_google_links']) && $campaign['fix_google_links'])
				add_filter('wpepro_full_permalink', array('NoNStatic', 'wpematico_googlenewslink'), 10, 1);

			foreach($campaign['campaign_feeds'] as $feed) {
				if($campaign[$feed]['feed_author'] >= "0") {
					add_filter('wpematico_get_author', array('NoNStatic', 'author'), 10, 4);
					break; //add filter just one time.
				}
			}

			if(isset($campaign['strip_all_images']) && $campaign['strip_all_images']) {
				add_filter('wpematico_item_filters_pre_img', array('NoNStatic', 'wpetruel_strip_img_tags_content'), 10, 2);
			}else {
				if(isset($campaign['discardifnoimage']) && $campaign['discardifnoimage']) {

					if(isset($campaign['campaign_thumbnail_scratcher']) && $campaign['campaign_thumbnail_scratcher'] && class_exists('WPeMatico_Thumbnail_Scratcher')) {
						add_filter('wpematico_allow_insertpost', array('NoNStatic', 'discardifnoimage_aux'), 99, 3);
					}else {
						add_filter('wpematico_item_parsers', array('NoNStatic', 'discardifnoimage'), 99, 4);
					}
				}
				if(isset($campaign['overwrite_image']) && $campaign['overwrite_image'] == 'overwrite')
					add_filter('wpematico_overwrite_file', array('NoNStatic', 'wpematico_overwrite_file'), 10, 1);
				if(isset($campaign['overwrite_image']) && $campaign['overwrite_image'] == 'keep')
					add_filter('wpematico_overwrite_file', array('NoNStatic', 'wpematico_keep_file'), 10, 1);
			}




			//clean the image name from queries before save it
			if(isset($campaign['image_src_gettype']) && $campaign['image_src_gettype'])
				add_filter('wpematico_newimgname', array('NoNStatic', 'image_src_gettype'), 9, 4);

			if(isset($campaign['check_image_content']) && $campaign['check_image_content']) {
				add_filter('wpematico_get_item_images', array('NoNStatic', 'check_image_content'), 9, 4);
			}



			if((isset($campaign['campaign_wcf']['great_amount']) && $campaign['campaign_wcf']['great_amount'] > 0 ) ||
				(isset($campaign['campaign_wcf']['cut_amount']) && $campaign['campaign_wcf']['cut_amount'] > 0 ))
				add_filter('wpematico_item_parsers', array('NoNStatic', 'wordcountfilters'), 20, 4);

			if(isset($campaign['campaign_wcf']['less_amount']) && $campaign['campaign_wcf']['less_amount'] > 0)
				add_filter('wpematico_item_parsers', array('NoNStatic', 'discardwordcountless'), 25, 4);

			if(isset($campaign['campaign_custitdup']) && $campaign['campaign_custitdup'] && isset($campaign['campaign_enablecustomtitle']) && $campaign['campaign_enablecustomtitle'])
				add_filter('wpematico_item_parsers', 'wpempro_check_custom_titles', 999, 4);


			if(isset($campaign['campaign_lastag']) && !empty($campaign['campaign_lastag']))
				add_filter('wpematico_item_parsers', array('NoNStatic', 'strip_lastag'), 30, 4);
		}

		protected function load_options() {
			$this->options	 = self :: $default_options;
			$current_options = get_option(self :: OPTION_KEY);
			if(!$current_options) {
				if(empty(self :: $default_options))
					return;
				add_option(self :: OPTION_KEY, $this->options, '', 'yes');
			}else {
				$this->options = array_merge($this->options, $current_options);
				if($this->options != $current_options) { // add the new defaults to the saved
					update_option(self::OPTION_KEY, $this->options);
				}
			}
		}

		public function update_options() {
			return update_option(self :: OPTION_KEY, $this->options);
		}

		/**
		 * Actions-Links del Plugin
		 *
		 * @param   array   $data  Original Links
		 * @return  array   $data  modified Links
		 */
		public static function init_action_links($data) {
			if(!current_user_can('manage_options')) {
				return $data;
			}
			return array_merge(
				$data,
				array(
					'<a href="' . admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=prosettings') . '" title="' . __('Go to WPeMatico Pro Settings Page', self :: TEXTDOMAIN) . '">' . __('Settings', self :: TEXTDOMAIN) . '</a>',
				)
			);
		}

		/**
		 * Meta-Links del Plugin
		 *
		 * @param   array   $data  Original Links
		 * @param   string  $page  plugin actual
		 * @return  array   $data  modified Links
		 */
		public static function init_row_meta($data, $page) {
			if($page != self::$basen) {
				return $data;
			}
			return array_merge(
				$data,
				array(
					'<a href="http://etruel.com/" target="_blank">' . __('etruel Store') . '</a>',
					'<a href="http://etruel.com/my-account/support/" target="_blank">' . __('Support') . '</a>',
					'<a href="https://wordpress.org/support/view/plugin-reviews/wpematico?filter=5&rate=5#postform" target="_Blank" title="Rate 5 stars on Wordpress.org">' . __('Rate Plugin', self :: TEXTDOMAIN) . '</a>'
				)
			);
		}

		/**
		 * Static function update_db_check
		 * Validate the WPeMatico Professional Version if it is changed will update all campaigns.
		 * @access public
		 * @return void
		 * @since 1.7.3
		 */
		public static function update_db_check() {
			$pro_version = get_option('wpematico_pro_db_version', '0.1');
			if(version_compare(WPEMATICOPRO_VERSION, $pro_version, '>')) { // check if updated 
				if(function_exists('wpematico_install')) {
					update_option('wpematico_pro_db_version', WPEMATICOPRO_VERSION);
					/* update_option before than wpematico_install to avoid double execution because redirections. */
					if(WPEMATICOPRO_UPDATE_CAMPAIGNS)
						wpematico_install(WPEMATICOPRO_UPDATE_CAMPAIGNS);
				}
			}
		}

		public static function activate() {
			$newcfg = get_option('WPeMatico_Options');
			if(!empty($newcfg)) {
				$newcfg['nonstatic'] = false;
				update_option('WPeMatico_Options', $newcfg);
			}
			// Send to welcome page.
			//set_transient( '_wpematico_activation_redirect', true, 120 ); 
			if(class_exists('WPeMatico')) {
				WPeMatico::add_wp_notice(array('text' => __('Professional Addon Updated.', self :: TEXTDOMAIN), 'below-h2' => false, 'error' => false));
			}
		}

		public static function deactivate() {
			$newcfg = get_option('WPeMatico_Options');
			if(!empty($newcfg)) {
				$newcfg['nonstatic'] = false;
				update_option('WPeMatico_Options', $newcfg);
			}
		}

		public static function uninstall() {
			global $wpdb, $blog_id;
			$danger						 = get_option('WPeMatico_danger');
			$danger['wpemdeleoptions']	 = (isset($danger['wpemdeleoptions']) && !empty($danger['wpemdeleoptions']) ) ? $danger['wpemdeleoptions'] : false;
			$danger['wpemdelecampaigns'] = (isset($danger['wpemdelecampaigns']) && !empty($danger['wpemdelecampaigns']) ) ? $danger['wpemdelecampaigns'] : false;
			if(is_network_admin() && $danger['wpemdeleoptions']) {
				if(isset($wpdb->blogs)) {
					$blogs = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT blog_id ' .
							'FROM ' . $wpdb->blogs . ' ' .
							"WHERE blog_id <> '%s'",
							$blog_id
						)
					);
					foreach($blogs as $blog) {
						delete_blog_option($blog->blog_id, self :: OPTION_KEY);
					}
				}
			}
			if($danger['wpemdeleoptions'])
				delete_option(self :: OPTION_KEY);
		}

		/*		 * * REQUIREMENTS AND NOTICES 	 */

		/**		 * Check if requirements are met	 */
		function meets_requirements() {
			global $wp_version, $user_ID; //,$wpempro_admin_message;
			$message				 = $wpempro_admin_message	 = '';
			$is_cron				 = defined('DOING_CRON');
			$checks					 = true;
			//if(!is_admin() && !$is_cron ) return false;
			if(is_admin()) {
				if(!is_plugin_active('wpematico/wpematico.php')) {
					$message .= __('You are using WPeMatico PRO.', self :: TEXTDOMAIN) . '<br />';
					$message .= __('Plugin <b>WPeMatico FREE</b> must be activated!', self :: TEXTDOMAIN);
					$message .= ' <a href="' . admin_url('plugins.php') . '#wpematico"> ' . __('Go to Activate Now', self :: TEXTDOMAIN) . '</a>';
					$message .= '<script type="text/javascript">jQuery(document).ready(function($){$("#wpematico").css("backgroundColor","yellow");});</script>';
					$checks	 = false;
				}else {  //WPeMatico is active
					if(!class_exists('WPeMatico')) {
						$message .= __('You are using WPeMatico PRO, but doesn\'t exist class WPeMatico.', self :: TEXTDOMAIN);
						$message .= __('Something is going wrong. May be PHP Version prior to 5.3', self :: TEXTDOMAIN);
						$checks	 = false;
					}
				}
			}else {
				if(!class_exists('WPeMatico')) {
					$message .= __('You are using WPeMatico PRO, but doesn\'t exist class WPeMatico.', self :: TEXTDOMAIN);
					$message .= __('Something is going wrong. May be PHP Version prior to 5.3', self :: TEXTDOMAIN);
					$checks	 = false;
				}
			}


			if(!empty($message))
				$wpempro_admin_message = '<div id="message" class="error fade"><strong>WPeMatico PRO:</strong><br />' . $message . '</div>';

			if(!empty($wpempro_admin_message) && is_admin()) {
				//send response to admin notice : ejemplo con la función dentro del add_action req. php 5.3
				add_action('admin_notices', function() use ($wpempro_admin_message) {
					echo $wpempro_admin_message;
				});
			}
			$this->requirement = $checks;
			return $this->requirement;
		}

		public static function rssimg_add2img_set_featured_image($img, $current_item, $campaign, $feed, $item) {
			if(!empty(self::$rssimg_add2img_featured_image) && $campaign['rssimg_add2img']) {
				return self::$rssimg_add2img_featured_image;
			}else {
				self::$rssimg_add2img_featured_image = '';
			}
			return $img;
		}

		public static function rssimg_add2img_get_featured_image($img) {
			if(!empty(self::$rssimg_add2img_featured_image)) {
				return self::$rssimg_add2img_featured_image;
			}
			return $img;
		}

		public static function add_no_follow_option($campaign_data, $cfgbasic) {
			global $post, $campaign_data, $helptip;
			$add_no_follow					 = $campaign_data['add_no_follow'];
			$campaign_strip_links_options	 = $campaign_data['campaign_strip_links_options'];
			$campaign_striphtml				 = $campaign_data['campaign_striphtml'];
			$campaign_strip_links			 = $campaign_data['campaign_strip_links'];
			?>
			<div id="div_add_no_follow" style="<?php echo (($campaign_striphtml || ($campaign_strip_links && $campaign_strip_links_options['a']) || ($campaign_strip_links && !$campaign_strip_links_options['a'] && !$campaign_strip_links_options['iframe'] && !$campaign_strip_links_options['script'])) ? 'display:none;' : ''); ?>">
				<p>
					<input class="checkbox" type="checkbox"<?php checked($add_no_follow, true); ?> name="add_no_follow" value="1" id="add_no_follow"/> 
					<label for="add_no_follow"><?php echo __('Add <code>rel="nofollow"</code> to links.', 'wpematico'); ?></label>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['add_no_follow']; ?>"></span>
				</p>
			</div>
			<?php
		}

		public static function add_no_follow_links($args, $campaign) {

			trigger_error(sprintf(__('Add nofollow to links in: %1s', 'wpematico'), $args['post_title']), E_USER_NOTICE);
			$args['post_content'] = self::function_no_follow_links($args['post_content']);

			return $args;
		}

		public static function function_no_follow_links($content) {

			$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
			if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
				if(!empty($matches)) {
					//$ownDomain = get_option('home');
					$ownDomain = $_SERVER['HTTP_HOST'];

					for($i = 0; $i < count($matches); $i++) {

						$tag	 = $matches[$i][0];
						$tag2	 = $matches[$i][0];
						$url	 = $matches[$i][0];

						// bypass #more type internal link
						$res = preg_match('/href(\s)*=(\s)*"[#|\/]*[a-zA-Z0-9-_\/]+"/', $url);
						if($res) {
							continue;
						}

						$pos = strpos($url, $ownDomain);
						if($pos === false) {

							$domainCheckFlag = true;


							$noFollow = '';

							//exclude domain or add nofollow
							if($domainCheckFlag) {
								$pattern	 = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
								preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
								if(count($match) < 1)
									$noFollow	 .= ' rel="nofollow"';
							}

							// add nofollow/target attr to url
							$tag	 = rtrim($tag, '>');
							$tag	 .= $noFollow . '>';
							$content = str_replace($tag2, $tag, $content);
						}
					}
				}
			}

			$content = str_replace(']]>', ']]&gt;', $content);
			return $content;
		}

	}

}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if(!class_exists('NoNStatic')) {

	class NoNStatic extends WPeMaticoPRO {

		public static function template_custom_feed_campaign($tags_array) {
			global $post, $campaign_data, $cfg, $helptip;
			if(empty($campaign_data['campaign_cfeed_tags'])) {
				$campaign_data['campaign_cfeed_tags'] = array('name' => array(''), 'value' => array(''));
			}
			foreach($campaign_data['campaign_cfeed_tags']['name'] as $i => $value) {
				$template = ((!empty($campaign_data['campaign_cfeed_tags']['value'][$i])) ? $campaign_data['campaign_cfeed_tags']['value'][$i] : '');
				if(!empty($template)) {
					$tags_array[] = $template;
				}
			}

			return $tags_array;
		}

		static function wpematico_quick_actions($actions) {
			global $post;
			if($post->post_type == 'wpematico' && 'trash' != $post->post_status) {
				$nonce				 = wp_create_nonce('wpemexport-nonce');
				$action_name		 = "wpematico_export_campaign";
				$action				 = '?action=' . $action_name . '&amp;post=' . $post->ID . '&_wpnonce=' . $nonce;
				$link				 = admin_url("admin.php" . $action);
				$actions['export']	 = '<a href="' . $link . '" title="' . esc_attr(__("Export & download Campaign", 'wpematico')) . '">' . __('Export', 'wpematico') . '</a>';
			}
			return $actions;
		}

		static function wpematico_export_campaign($status = '') {
			$nonce = (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce']) ) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
			if(!wp_verify_nonce($nonce, 'wpemexport-nonce'))
				wp_die('Are you sure?');
			if(!( isset($_GET['post']) || isset($_POST['post']) || ( isset($_REQUEST['action']) && 'wpematico_export_campaign' == $_REQUEST['action'] ) )) {
				wp_die(__('No campaign ID has been supplied!', 'wpematico'));
			}
			// Get the original post
			$id = (isset($_GET['post']) ? sanitize_text_field($_GET['post']) : sanitize_text_field($_POST['post']));

			$wpecampaign			 = self::get_exported_campaign($id);
			$new_campaigns_data		 = array();
			$new_campaigns_data[]	 = base64_encode($wpecampaign);
			$new_campaigns_data_json = json_encode($new_campaigns_data);
			$post_name				 = get_post_field('post_name', $id);
			if(is_numeric($post_name)) {
				$post_name = sanitize_title(get_the_title($id));
			}

			// Copy the post and insert it
			if(isset($new_campaigns_data_json) && $new_campaigns_data_json != null) {
				header('Content-type: text/plain');
				header('Content-Disposition: attachment; filename="' . $post_name . '.txt"');
				print $new_campaigns_data_json;
				die();
			}else {
				$post_type_obj = get_post_type_object($post->post_type);
				wp_die(esc_attr(__('Exporting failed, could not find the campaign:', 'wpematico')) . ' ' . $id);
			}
		}

		static function get_exported_campaign($id, $type = 'json') {
			$post		 = get_post($id);
			$wpecampaign = null;
			// Copy the post and insert it
			if(isset($post) && $post != null && $post->post_type == 'wpematico') {
				$exp_post			 = array(
					'menu_order'	 => $post->menu_order,
					'guid'			 => $post->guid,
					'comment_status' => $post->comment_status,
					'ping_status'	 => $post->ping_status,
					'pinged'		 => $post->pinged,
					'post_author'	 => @$post->author,
					//'post_content' => $post->post_content,
					'post_excerpt'	 => $post->post_excerpt,
					'post_mime_type' => $post->post_mime_type,
					'post_parent'	 => $post->post_parent,
					'post_password'	 => $post->post_password,
					'post_status'	 => $post->post_status,
					'post_title'	 => $post->post_title,
					'post_type'		 => $post->post_type,
					'to_ping'		 => $post->to_ping,
					'post_date'		 => $post->post_date,
					'post_date_gmt'	 => get_gmt_from_date($post->post_date)
				);
				$cid				 = WPeMatico::get_campaign($id);
				$taxonomiesNewPost	 = get_object_taxonomies($cid['campaign_customposttype']);
				$taxonomiesNewPost	 = array_diff($taxonomiesNewPost, array('category', 'post_tag', 'post_format'));
				foreach($taxonomiesNewPost AS $tax) {
					$terms	 = wp_get_object_terms($id, $tax);
					$term	 = array();
					foreach($terms AS $t) {
						$term[] = $t->slug;
					}
					$cus_tax[$tax] = $term;
				}
				$campaign				 = array();
				$campaign['exp_post']	 = $exp_post;
				$campaign['data']		 = get_post_custom($post->ID);
				$campaign['cus_tax']	 = (isset($cus_tax) && !empty($cus_tax) ) ? $cus_tax : null;
				foreach($campaign['data'] as $dkey => $value) {
					foreach($value as $vkey => $vvalue) {
						$campaign['data'][$dkey][$vkey] = base64_encode($vvalue);
					}
				}
				switch ($type) {
					case "json":
						$wpecampaign = json_encode($campaign);
						break;

					default:
						$wpecampaign = $campaign;
						break;
				}
			}
			return $wpecampaign;
		}

		public static function get_json_error() {
			$error_json = '';
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$error_json	 = '';
					break;
				case JSON_ERROR_DEPTH:
					$error_json	 = __('Maximum stack depth exceeded', 'wpematico');
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$error_json	 = __('Underflow or the modes mismatch', 'wpematico');
					break;
				case JSON_ERROR_CTRL_CHAR:
					$error_json	 = __('Unexpected control character found', 'wpematico');
					break;
				case JSON_ERROR_SYNTAX:
					$error_json	 = __('Syntax error, malformed JSON', 'wpematico');
					break;
				case JSON_ERROR_UTF8:
					$error_json	 = __('Malformed UTF-8 characters, possibly incorrectly encoded', 'wpematico');
					break;
				default:
					$error_json	 = __('Unknown error', 'wpematico');
					break;
			}
			return $error_json;
		}

		static function wpematico_import_campaign() {
			$nonce = (isset($_REQUEST['wpemimport_nonce']) && !empty($_REQUEST['wpemimport_nonce']) ) ? $_REQUEST['wpemimport_nonce'] : '';
			if(!wp_verify_nonce($nonce, 'import-campaign'))
				wp_die('Can\'t import.');

			$post_type = (isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type']) ) ? $_REQUEST['post_type'] : '';
			if(!$post_type == 'wpematico')
				wp_die('This was wrong.');

			//Allow Uploads files ?
			if(in_array(str_replace('.', '', strrchr($_FILES['txtcampaign']['name'], '.')), explode(',', 'txt')) && ($_FILES['txtcampaign']['type'] == 'text/plain') && !$_FILES['txtcampaign']['error']) {
				
			}else {
				$message = __("** Can't upload! Just .txt files allowed!", 'wpematico');
				WPeMatico::add_wp_notice(array('text' => $message, 'below-h2' => false, 'error' => true));
				return true;
			}
			$campaign = file_get_contents($_FILES['txtcampaign']['tmp_name']);
			unlink($_FILES['txtcampaign']['tmp_name']);

			/**
			 * @since 1.7.5
			 * The BOM is removed from the contents of the file to avoid the malformed JSON error.
			 */
			$bom		 = pack('H*', 'EFBBBF');
			$campaign	 = preg_replace("/^$bom/", '', $campaign);

			$campaign		 = stripslashes($campaign);
			$wpecampaigns	 = json_decode($campaign, true);
			$error_json		 = self::get_json_error();
			if(!empty($error_json)) {
				WPeMatico::add_wp_notice(array('text' => 'Error Json: ' . $error_json, 'below-h2' => false, 'error' => true));
				return true;
			}
			foreach($wpecampaigns as $wpecampaign) {
				$wpecampaign = base64_decode($wpecampaign);
				$wpecampaign = json_decode($wpecampaign, true);
				$error_json	 = self::get_json_error();
				if(!empty($error_json)) {
					WPeMatico::add_wp_notice(array('text' => 'Error Json: ' . $error_json, 'below-h2' => false, 'error' => true));
					return true;
				}
				$new_post_id = wp_insert_post($wpecampaign['exp_post']);

				$post_meta_keys = array_keys($wpecampaign['data']);
				if(!empty($post_meta_keys)) {
					foreach($post_meta_keys as $meta_key) {
						$meta_values = $wpecampaign['data'][$meta_key];
						foreach($meta_values as $meta_value) {
							$meta_value	 = base64_decode($meta_value);
							$meta_value	 = maybe_unserialize($meta_value);
							add_post_meta($new_post_id, $meta_key, $meta_value);
						}
					}
				}
				if(isset($wpecampaign['cus_tax']) && !empty($wpecampaign['cus_tax']))
					foreach($wpecampaign['cus_tax'] as $tax => $term) {
						wp_set_object_terms($new_post_id, $term, $tax);
					}

				$campaign_data				 = WPeMatico :: get_campaign($new_post_id);
				$campaign_data['activated']	 = false;
				WPeMatico :: update_campaign($new_post_id, $campaign_data);
			}

			WPeMatico::add_wp_notice(array('text' => __('Campaigns Imported.', 'wpematico'), 'below-h2' => false));
		}

		public static function bulk_actions_import_campaign($actions) {
			$actions['export_campaigns'] = __('Export campaigns', 'wpematico');
			return $actions;
		}

		public static function bulk_action_handler_import_campaign($redirect_to, $doaction, $post_ids) {
			if($doaction !== 'export_campaigns') {
				return $redirect_to;
			}
			$new_campaigns_data	 = array();
			$file_name			 = 'wpematico_campaigns';
			foreach($post_ids as $post_id) {
				$wpecampaign			 = self::get_exported_campaign($post_id);
				$new_campaigns_data[]	 = base64_encode($wpecampaign);
			}
			$new_campaigns_data_json = json_encode($new_campaigns_data);
			// Copy the post and insert it
			if(isset($new_campaigns_data_json) && $new_campaigns_data_json != null) {
				header('Content-type: text/plain');
				header('Content-Disposition: attachment; filename="' . $file_name . '.txt"');
				print $new_campaigns_data_json;
				die();
			}
			$redirect_to = add_query_arg('bulk_export_campaigns', count($post_ids), $redirect_to);
			return $redirect_to;
		}

		public static function process_ramdom_rewrites($args, $campaign) {
			if(isset($campaign['activate_ramdom_rewrite']) && $campaign['activate_ramdom_rewrite']) {
				trigger_error('<b>' . __('Initiating Ramdom Rewrites Process.', self ::TEXTDOMAIN) . '</b>', E_USER_NOTICE);

				$ramdom_rewrites_options = get_option(WPeMaticoPro_ExtraSettings::RAMDOM_REWRITES_OPTION);
				$ramdom_rewrites_options = wp_parse_args($ramdom_rewrites_options, WPeMaticoPro_ExtraSettings::default_ramdom_rewrites_options(FALSE));
				$ramdom_rewrites_array	 = array();
				$line_arr				 = explode("\n", $ramdom_rewrites_options['words_to_rewrites']);
				foreach($line_arr as $key => $value) {
					$value = trim($value);
					if(!empty($value)) {
						$new_array_words = array();
						$array_words	 = explode(",", $value);
						foreach($array_words as $kw => $valw) {
							$valw = trim($valw);
							if(!empty($valw)) {
								$new_array_words[] = $valw;
							}
						}
						$ramdom_rewrites_array[] = $new_array_words;
					}
				}
				$line_arr = explode("\n", $campaign['words_to_rewrites']);
				foreach($line_arr as $key => $value) {
					$value = trim($value);
					if(!empty($value)) {
						$new_array_words = array();
						$array_words	 = explode(",", $value);
						foreach($array_words as $kw => $valw) {
							$valw = trim($valw);
							if(!empty($valw) && apply_filters('wpe_pro_ramdom_rewrites_accept_word', true, $valw, $args, $campaign)) {
								$new_array_words[] = $valw;
							}
						}
						$ramdom_rewrites_array[] = $new_array_words;
					}
				}
				$ramdom_rewrites_array	 = apply_filters('wpe_pro_ramdom_rewrites_array', $ramdom_rewrites_array, $args, $campaign);
				$maximum_replaces		 = 10;
				if(isset($campaign['ramdom_rewrite_count']) && is_numeric($campaign['ramdom_rewrite_count'])) {
					$maximum_replaces = $campaign['ramdom_rewrite_count'];
				}
				$count_replaces = 0;
				foreach($ramdom_rewrites_array as $rewrite_line) {
					$current_offeset = 0;
					$continue		 = false;
					if(count($rewrite_line) > 1) {
						while ($current_offeset < strlen($args['post_content'])) {
							if($count_replaces >= $maximum_replaces) {
								break 2;
							}
							$kw				 = array_rand($rewrite_line);
							$current_search	 = $rewrite_line[$kw];
							$found_fail		 = array();
							while (strpos($args['post_content'], $current_search, $current_offeset) === false) {
								if(in_array($kw, $found_fail) === false) {
									$found_fail[] = $kw;
								}
								if(count($found_fail) >= count($rewrite_line)) {
									$continue = true;
									break 2;
								}
								$kw				 = array_rand($rewrite_line);
								$current_search	 = $rewrite_line[$kw];
							}
							do {
								$rr				 = array_rand($rewrite_line);
								$random_remplace = $rewrite_line[$rr];
							} while ($kw == $rr);
							$new_replace = replace_first_offset($current_search, $random_remplace, $args['post_content'], $current_offeset);
							if($current_offeset == $new_replace->pos) {
								$current_offeset = $current_offeset + 5;
							}else {
								$current_offeset		 = $new_replace->pos;
								$args['post_content']	 = $new_replace->result;
								$count_replaces++;
							}
						}
					}
					if($continue) {
						$continue = false;
						continue;
					}
				}
				trigger_error('<b>' . sprintf(__('Words replaced by Ramdom Rewrites: %s.', self ::TEXTDOMAIN), $count_replaces) . '</b>', E_USER_NOTICE);
			}



			return $args;
		}

		static function import_in_views($links) {
			global $post_type;
			if($post_type != 'wpematico')
				return $links;
			ob_start();
			?><form style="opacity: 0;position: absolute;" id="importcampaign" method='post' ENCTYPE='multipart/form-data'>
			<?php wp_nonce_field('import-campaign', 'wpemimport_nonce'); ?>
				<input type="hidden" name="wpematico-action" value="import_campaign" />
				<input style="display:none;" type="file" class="button" name='txtcampaign' id='txtcampaign'>
			</form>
			<a id="importcpg" href="Javascript:void(0);" title="<?php echo esc_attr(__("Upload & import a Campaign", 'wpematico')) ?>"><?php echo __('Import campaign', 'wpematico') ?></a>
			<script>(function ($) {
					$('#importcpg').click(function () {
						$('#txtcampaign').click();
					});
					$('#txtcampaign').change(function () {
						$('#importcampaign').submit();
					});
				})(jQuery);
			</script>
			<?php
			$contents = ob_get_contents();
			ob_end_clean();

			$action_name	 = "wpematico_import_campaign";
			$links['import'] = $contents;
			return $links;
		}

		static function print_r_reverse($in) {
			$lines = explode("\n", trim($in));
			if(trim($lines[0]) != 'Array') {
				// bottomed out to something that isn't an array
				return $in;
			}else {
				// this is an array, lets parse it
				if(preg_match("/(\s{5,})\(/", $lines[1], $match)) {
					// this is a tested array/recursive call to this function
					// take a set of spaces off the beginning
					$spaces			 = $match[1];
					$spaces_length	 = strlen($spaces);
					$lines_total	 = count($lines);
					for($i = 0; $i < $lines_total; $i++) {
						if(substr($lines[$i], 0, $spaces_length) == $spaces) {
							$lines[$i] = substr($lines[$i], $spaces_length);
						}
					}
				}
				array_shift($lines); // Array
				array_shift($lines); // (
				array_pop($lines); // )
				$in				 = implode("\n", $lines);
				// make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
				preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
				$pos			 = array();
				$previous_key	 = '';
				$in_length		 = strlen($in);
				// store the following in $pos:
				// array with key = key of the parsed array's item
				// value = array(start position in $in, $end position in $in)
				foreach($matches as $match) {
					$key					 = $match[1][0];
					$start					 = $match[0][1] + strlen($match[0][0]);
					$pos[$key]				 = array($start, $in_length);
					if($previous_key != '')
						$pos[$previous_key][1]	 = $match[0][1] - 1;
					$previous_key			 = $key;
				}
				$ret = array();
				foreach($pos as $key => $where) {
					// recursively see if the parsed out value is an array too
					$ret[$key] = self::print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
				}
				return $ret;
			}
		}

		/**
		 * Static function create_metaboxes_before
		 * @access public
		 * @return void
		 * @since 1.7.4
		 */
		public static function create_metaboxes_before($campaign_data = array(), $cfgbasic) {
			/**
			 * Adds (by js) a Parent Page metabox with a select option when Page custom post type is selected.
			 */
			add_meta_box('pro-parent-page-box', __('Parent Page', 'wpematico'), array('NoNStatic', 'pro_parent_page_box'), 'wpematico', 'side', 'default');
			/**
			 * Adds Contents Parser Metabox separated from Post template.
			 */
			add_meta_box('pro-parsers-box', '<span class="dashicons dashicons-layout"> </span> '.__('Custom Content Parsers', 'wpematico'), array('NoNStatic', 'pro_parsers_box'), 'wpematico', 'normal', 'high');
		}

		// Create new meta boxes
		public static function meta_boxes($campaign_data = array(), $cfgbasic) {
			global $post, $campaign_data;
			$cfg = get_option(self :: OPTION_KEY); //PRO settings
			if($cfg['enablecustomtitle'])   // Si está habilitado en settings, lo muestra 
				add_meta_box('custitle-box', __('Custom Title Options', 'wpematico'), array('WPeMaticoPro_Campaign_Edit', 'custitle_box'), 'wpematico', 'normal', 'default');
			if($cfg['enablekwordf'])   // Si está habilitado en settings, lo muestra 
				add_meta_box('kwordf-box', __('Keywords Filters', 'wpematico'), array('NoNStatic', 'kwordf_box'), 'wpematico', 'normal', 'default');
			if($cfg['enablewcf'])   // Si está habilitado en settings, lo muestra 
				add_meta_box('wcountf-box', __('Word Count Filters', 'wpematico'), array('NoNStatic', 'wcountf_box'), 'wpematico', 'normal', 'default');
			if($cfg['enable_ramdom_words_rewrites'])   // Si está habilitado en settings, lo muestra 
				add_meta_box('ramdom-words-rewrites-box', __('Ramdom Rewrites', 'wpematico'), array('NoNStatic', 'ramdom_words_rewrites_box'), 'wpematico', 'normal', 'default');
			if($cfg['enablecfields'])   // Si está habilitado en settings, lo muestra 
				add_meta_box('cfields-box', __('Custom Fields', 'wpematico'), array('NoNStatic', 'cfields_box'), 'wpematico', 'normal', 'default');

			if($cfg['enable_custom_feed_tags']) {
				add_meta_box('cfeed-tags-box', __('Custom Feed Tags', 'wpematico'), array('NoNStatic', 'cfeed_tags_box'), 'wpematico', 'normal', 'default');
			}
			if($cfg['enable_filter_per_author']) {
				add_meta_box('cfilter-author-box', __('Filter Per Author', 'wpematico'), array('WPeMaticoPro_Campaign_Edit', 'filter_per_author_box'), 'wpematico', 'normal', 'default');
			}

			if($cfg['enable_word_to_taxonomy']) {
				remove_meta_box('word2cats-box', 'wpematico', 'normal');
				add_meta_box('wpepro-word-to-taxonomy', __('Word to Taxonomy', 'wpematico'), array('WPeMaticoPro_Campaign_Edit', 'word_to_taxonomy_box'), 'wpematico', 'normal', 'default');
			}

			add_action('admin_print_scripts-post.php', array(__CLASS__, 'admin_scripts'));
			add_action('admin_print_scripts-post-new.php', array(__CLASS__, 'admin_scripts'));
			add_action('admin_print_styles', array(__CLASS__, 'wpe_m_styles'));
		}

		public static function pro_parsers_box($post, $cfg) {
		/**
		 * An action to allow Addons inserts fields before the post template textarea
		 */
		do_action('wpematico_pro_parsers_box',$post, $cfg);
		/**
		 * 
		 */			
		}
		public static function ramdom_words_rewrites_box() {
			global $post, $campaign_data, $helptip;
			$activate_ramdom_rewrite = @$campaign_data['activate_ramdom_rewrite'];
			$ramdom_rewrite_count	 = @$campaign_data['ramdom_rewrite_count'];
			$words_to_rewrites		 = @$campaign_data['words_to_rewrites'];
			?>
			<input class="checkbox" type="checkbox"<?php checked($activate_ramdom_rewrite, true); ?> name="activate_ramdom_rewrite" value="1" id="activate_ramdom_rewrite"/> <b><?php echo '<label for="activate_ramdom_rewrite">' . __('Activate Ramdom Rewrites.', 'wpematico') . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['activate_ramdom_rewrite']; ?>"></span><br/>
			<div id="div_ramdom_words_rewrites" style="margin-left: 20px; <?php if(!$activate_ramdom_rewrite) echo 'display: none;' ?>">
				<label for="ramdom_rewrite_count"><b>Number of maximum words to replace:</b></label>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['ramdom_rewrite_count']; ?>"></span><br/>
				<input type="number" min="0" size="5" class="small-text" id="ramdom_rewrite_count" name="ramdom_rewrite_count" value="<?php echo $ramdom_rewrite_count; ?>">
				<br/>
				<b><label for="words_to_rewrites"><?php _e('Words to Rewrites:', self :: TEXTDOMAIN); ?></label></b><span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['words_to_rewrites']; ?>"></span><br/>
				<textarea style="width:100%;" id="words_to_rewrites" name="words_to_rewrites"><?php echo $words_to_rewrites; ?></textarea><br>

				<?php _e('Enter a comma-separated list of words for rewrites use each line for different rewriting patterns.', self :: TEXTDOMAIN); ?>
			</div>
			<?php
		}

		static function wpe_m_styles() {
			global $post;
			if($post->post_type != 'wpematico')
				return $post->ID;
			wp_enqueue_style('thickbox');
		}

		static function admin_scripts() { // load javascript 
			global $post;
			if($post->post_type != 'wpematico')
				return $post_id;
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
//			wp_register_script('my-upload',self :: $uri .'lib/myetupload.js', array('jquery','media-upload','thickbox'));
//			wp_enqueue_script('my-upload');
			add_action('admin_head', array(__CLASS__, 'procampaigns_admin_js'));
		}

		static function procampaigns_admin_js() { // load javascript 
			global $post, $campaign_data;
			$cfg = get_option(self :: OPTION_KEY);
			?>
			<script type="text/javascript" language="javascript">
				function LCeros(obj, num) {
					obj.value = Number(obj.value);
					while (obj.value.length < num)
						obj.value = '0' + obj.value;
				}

				function action_strip_links() {
					if (jQuery('#campaign_strip_links').is(':checked') && !jQuery('#campaign_strip_links_options_a').is(':checked') && !jQuery('#campaign_strip_links_options_iframe').is(':checked') && !jQuery('#campaign_strip_links_options_script').is(':checked')) {
						jQuery('#add_no_follow').attr('checked', false);
						jQuery('#div_add_no_follow').fadeOut();
					} else if (jQuery('#campaign_strip_links').is(':checked') && jQuery('#campaign_strip_links_options_a').is(':checked')) {
						jQuery('#add_no_follow').attr('checked', false);
						jQuery('#div_add_no_follow').fadeOut();
					} else {
						jQuery('#div_add_no_follow').fadeIn();
					}
				}
				function add_events_no_follow() {
					jQuery('#campaign_striphtml').change(function () {
						if (jQuery('#campaign_striphtml').is(':checked')) {
							jQuery('#add_no_follow').attr('checked', false);
							jQuery('#div_add_no_follow').fadeOut();

						} else {
							jQuery('#div_add_no_follow').fadeIn();
						}
					});
					jQuery('#campaign_strip_links').change(function () {
						action_strip_links();
					});

					jQuery('#campaign_strip_links_options_a').change(function () {
						action_strip_links();
					});
					jQuery('#campaign_strip_links_options_iframe').change(function () {
						action_strip_links();
					});
					jQuery('#campaign_strip_links_options_script').change(function () {
						action_strip_links();
					});
					jQuery('#add_no_follow').change(function () {
						if (jQuery('#add_no_follow').is(':checked')) {
							jQuery('#campaign_strip_links_options_a').attr('checked', false);
							jQuery('#campaign_striphtml').attr('checked', false);
						}
					});
				}

				function add_event_ramdom_rewrite() {
					jQuery('#activate_ramdom_rewrite').change(function () {
						if (jQuery('#activate_ramdom_rewrite').is(':checked')) {
							jQuery('#div_ramdom_words_rewrites').fadeIn();
						} else {
							jQuery('#div_ramdom_words_rewrites').fadeOut();
						}
					});

				}
				function action_on_change_post_type_pro() {
					if (jQuery('#customtype_page').is(':checked')) {
						jQuery('#pro-parent-page-box').fadeIn();
					} else {
						jQuery('#pro-parent-page-box').fadeOut();
					}
				}
				jQuery(document).ready(function ($) {

					action_on_change_post_type_pro();
					jQuery('input[name="campaign_customposttype"]').change(function () {
						action_on_change_post_type_pro();
					});

					add_events_no_follow();
					add_event_ramdom_rewrite();


			<?php if($cfg['enableimportfeed']) : ?>
						$('#bimport').click(function () {
							$('.feed_header').fadeToggle();
							$('#feeds_list').toggle();
							$('#addmorefeed').toggle();
							$('#checkfeeds').toggle();
							$('#pbfeet').toggle();
							$('#blocktxt_feedlist').toggleClass('hide');
							if ($(this).text() == "<?php _e('Cancel Import', 'wpematico'); ?>")
								$(this).text('<?php _e('Import feed list', 'wpematico'); ?>');
							else
								$(this).text('<?php _e('Cancel Import', 'wpematico'); ?>');
						});
			<?php endif; ?>






					$('.chkgwol').click(function () {
						var wol = $(this).parent().children('#gwol');
						if (true == $(this).is(':checked')) {
							wol.html('words.');
							wol.css('color', 'red');
						} else {
							wol.html('letters.');
							wol.css('color', 'black');
						}
					});
					$('.chkcwol').click(function () {
						var wol = $(this).parent().children('#cwol');
						if (true == $(this).is(':checked')) {
							wol.html('words ');
							wol.css('color', 'red');
						} else {
							wol.html('letters ');
							wol.css('color', 'black');
						}
					});
					$('.chklwol').click(function () {
						var wol = $(this).parent().children('#lwol');
						if (true == $(this).is(':checked')) {
							wol.html('words.');
							wol.css('color', 'red');
						} else {
							wol.html('letters.');
							wol.css('color', 'black');
						}
					});






					jQuery('#addmore_custom_feed_tags').click(function (e) {
						var name_text = <?php echo "'" . __('Feed Tag:', 'wpematico') . "';"; ?>
						var value_text = <?php echo "'" . __('Template:', 'wpematico') . "';"; ?>
						var delete_text = <?php echo "'" . __('Delete this item', 'wpematico') . "';"; ?>
						var new_content = '<div class="custom_feed_tag_element"><div class="clear pDiv jobtype-select rowflex"><div id="cf1" class="rowblock left p4" style="width: 45%;">' + name_text + '<input name="campaign_cfeed_tags[name][]" type="text" value="" class="large-text campaign_cft_name" id="campaign_cft_name" /></div><div class="rowblock left p4" style="width: 45%;">' + value_text + '<input name="campaign_cfeed_tags[value][]" type="text" value="" class="large-text campaign_cft_value" id="campaign_cft_value" /></div><div class="rowactions"><span class="" id="w2cactions"><label title="' + delete_text + '"  class="bicon delete left delete_custom_feed_tag"></label></span></div></div></div>';
						jQuery('#cfeed_tags_edit').append(new_content);
						delete_custom_feed_tags_events();
						e.preventDefault();
						return false;
					});

					function delete_custom_feed_tags_events() {
						jQuery('.delete_custom_feed_tag').click(function (e) {
							jQuery(this).parent().parent().parent().parent().remove();
							e.preventDefault();
							return false;
						});
					}
					delete_custom_feed_tags_events();

					$(document).on("blur", '.campaign_cft_name', function (event) {
						$tagname = $(this).val();
						$tagval = $(this).closest('div').next().find(".campaign_cft_value");
						if ($tagval.val() == '')
							$tagval.val('{' + $tagname + '}');
					});


					$('.tagcf').click(function () {
						lastval = $('#cfield_max').val();
						cval = $('input[name="campaign_cf_value[' + lastval + ']"]').val();
						$('input[name="campaign_cf_value[' + lastval + ']"]').val(cval + $(this).html());
					});

					$('#campaign_autotags').click(function () {
						if (false == $('#campaign_autotags').is(':checked')) {
							$('#manualtags').fadeIn();
							$('#badtags').fadeOut();
						} else {
							$('#manualtags').fadeOut();
							$('#badtags').fadeIn();
						}
					});
				});
			</script>
			<?php
		}

		//*************************************************************************************
		static function feedlist() { // part of feeds metabox for import
			global $post, $campaign_data, $helptip;
			$cfg = get_option(self :: OPTION_KEY);
			if($cfg['enableimportfeed']) :
				?>
				<div id="blocktxt_feedlist" class="hide">
					<p class="he20">
						<span class="left"><?php _e('Type or paste a list of urls, authors.  When update the campaign, the list will be imported as campaign feeds', 'wpematico') ?></span> 
						<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['import_feed_list']; ?>"></span>
					</p>		

					<div id="wpe_post_template_edit" class="inlinetext">
						<textarea class="large-text" rows=7 id="txt_feedlist" name="txt_feedlist" /></textarea>
					</div>
				</div> <?php
			endif;
		}

		static function bimport() {
			$cfg = get_option(self :: OPTION_KEY);
			if($cfg['enableimportfeed']) :
				?>
				<span class="button-primary" id="bimport" style="font-weight: bold; text-decoration: none;" > <?php _e('Import feed list', 'wpematico'); ?></span>
				<?php
			endif;
		}

		//*************************************************************************************
		static function google_permalinks_option($campaign_data, $cfgbasic) {
			global $post, $campaign_data, $helptip;
			$fix_google_links = $campaign_data['fix_google_links'];
//			$cfg = get_option( self :: OPTION_KEY);
			?>
			<p>
				<input class="checkbox" type="checkbox"<?php checked($fix_google_links, true); ?> name="fix_google_links" value="1" id="fix_google_links"/> 
				<label for="fix_google_links"><?php echo __('Sanitize Googlo News permalink.', 'wpematico'); ?></label>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['fix_google_links']; ?>"></span>
			</p>
			<?php
		}

		//*************************************************************************************
		//*************************************************************************************
		//*************************************************************************************
		//*************************************************************************************

		static function protags($post) {
			global $post, $campaign_data, $helptip;
			$cfg				 = get_option(self :: OPTION_KEY);
			$campaign_autotags	 = isset($campaign_data['campaign_autotags']) ? $campaign_data['campaign_autotags'] : $cfg['enabletags'];
			$campaign_badtags	 = @$campaign_data['campaign_badtags'];
			$campaign_tags_feeds = @$campaign_data['campaign_tags_feeds'];

			if($cfg['enabletags']) { // Si está habilitado en settings, lo muestra y usa autotags
				?>
				<p><input class="checkbox" type="checkbox" <?php checked($campaign_tags_feeds, true); ?> name="campaign_tags_feeds" value="1" id="campaign_tags_feeds"/><b><?php echo '<label for="campaign_tags_feeds">' . __('Use &lt;tag&gt; tags from feed if exist.', 'wpematico') . '</label>'; ?></b>
					<small>
						<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_tags_feeds']; ?>"></span>
				</p>


				<p><input class="checkbox" type="checkbox" <?php checked($campaign_autotags, true); ?> name="campaign_autotags" value="1" id="campaign_autotags"/><b><?php echo '<label for="campaign_autotags">' . __('Auto generate tags', 'wpematico') . '</label>'; ?></b>
					<small>
						<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_autotags']; ?>"></span>
				</p>
				<div id="manualtags" <?php if($campaign_autotags) echo 'style="display:none;"'; ?>>
					<?php
				}
			}

			static function protags1($post) {
				global $post, $campaign_data, $helptip;
				$cfg				 = get_option(self :: OPTION_KEY);
				$campaign_autotags	 = isset($campaign_data['campaign_autotags']) ? $campaign_data['campaign_autotags'] : $cfg['enabletags'];
				$campaign_nrotags	 = @$campaign_data['campaign_nrotags'];
				$campaign_badtags	 = @$campaign_data['campaign_badtags'];

				if($cfg['enabletags']) { // Si está habilitado en settings, lo muestra y usa autotags
					?>		
				</div>
				<div id="badtags" <?php if(!$campaign_autotags) echo 'style="display:none;"'; ?>>		



					<p><b><?php echo '<label for="campaign_nrotags">' . __('Limit tags quantity to:', 'wpematico') . '</label>'; ?></b>
						<input style="" class="small-text" id="campaign_nrotags" name="campaign_nrotags" value="<?php echo stripslashes($campaign_nrotags); ?>" />
						<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_nrotags']; ?>"></span>
					</p>
					<p><b><?php echo '<label for="campaign_badtags">' . __('Bad Tags:', 'wpematico') . '</label>'; ?></b><span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_badtags']; ?>"></span>
						<textarea style="" class="large-text" id="campaign_badtags" name="campaign_badtags"><?php echo stripslashes($campaign_badtags); ?></textarea><br />
						<?php echo __('Enter comma separated list of excluded Tags.', 'wpematico'); ?></p>
				</div>
				<?php
			}
		}

		//*************************************************************************************
		static function delete_from_phrase_box($post, $cfgbasic) {
			global $post, $campaign_data, $helptip;

			$campaign_delfphrase			 = $campaign_data['campaign_delfphrase'];
			$campaign_delfphrase_keep		 = $campaign_data['campaign_delfphrase_keep'];
			$campaign_delfphrase_end_line	 = $campaign_data['campaign_delfphrase_end_line'];
			$cfg							 = get_option(WPeMaticoPRO::OPTION_KEY);
			?><hr style="border-color:#FFF;" />
			<div>
				<p class="he20">
					<b class="left"><?php _e('Delete all in the content AFTER a word or phrase till the end.', 'wpematico'); ?></b>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_delfphrase']; ?>"></span>
				</p>
			</div>


			<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
				<label for="campaign_delfphrase"><b><?php _e('Phrases or keywords (one per line, case-insensitive):', 'wpematico'); ?></b></label><br />
				<textarea style="width: 50%; height: 70px;" class="regular-text" id="campaign_delfphrase" name="campaign_delfphrase"><?php echo stripslashes($campaign_delfphrase); ?></textarea><br />

				<p><label for="campaign_delfphrase_keep">
						<input class="checkbox" type="checkbox" <?php checked($campaign_delfphrase_keep, true); ?> name="campaign_delfphrase_keep" value="1" id="campaign_delfphrase_keep"/>
						<b><?php _e('Keep phrase', 'wpematico'); ?></b></label>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_delfphrase_keep']; ?>"></span>
				</p>

				<?php
				if($cfg['end_of_the_line']) :
					?>
					<p><label for="campaign_delfphrase_end_line">
							<input class="checkbox" type="checkbox" <?php checked($campaign_delfphrase_end_line, true); ?> name="campaign_delfphrase_end_line" value="1" id="campaign_delfphrase_end_line"/>
							<b><?php _e('Till the end of the line', 'wpematico'); ?></b></label>
						<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_delfphrase_end_line']; ?>"></span>
					</p>
					<?php
				endif;
				?>
			</div>

			<div class="clear"></div>
			<?php
		}

		static function flip_paragraphs_box($post, $cfgbasic) {
			global $post, $campaign_data, $helptip;

			$campaign_flip_paragraphs		= $campaign_data['campaign_flip_paragraphs'];
			$cfg							= get_option(WPeMaticoPRO::OPTION_KEY);
			?><hr style="border-color:#FFF;" />
			<div>
				<p class="he20">
					<b class="left"><?php _e('Flip Paragraphs.', 'wpematico'); ?></b>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['flip_paragraphs_title']; ?>"></span>
				</p>
			</div>
			<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
				<p style="margin: 0;">
					<label for="campaign_flip_paragraphs">
						<input class="checkbox" type="checkbox" <?php checked($campaign_flip_paragraphs, true); ?> name="campaign_flip_paragraphs" value="1" id="campaign_flip_paragraphs"/>
						<b><?php _e('Activate Flip Paragraphs.', 'wpematico'); ?></b></label>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_flip_paragraphs']; ?>"></span>
				</p>
			</div>

			<div class="clear"></div>
			<?php
		}

		//*************************************************************************************
		static function last_html_tag($post, $cfgbasic) { // part of basic template metabox
			global $post, $campaign_data, $helptip;
			$campaign_lastag = !empty($campaign_data['campaign_lastag']) ? $campaign_data['campaign_lastag'] : "";
			if(is_array($campaign_lastag)) { // and !empty($campaign_data['campaign_lastag']['tag'])) {
				$campaign_lastag = !empty($campaign_data['campaign_lastag']['tag']) ? $campaign_data['campaign_lastag']['tag'] : "";
			}
			?><hr style="border-color:#FFF;" />
			<div>
				<p class="he20">
					<b><?php _e('Last HTML tag to remove', 'wpematico'); ?></b>
					<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_lastag_tag']; ?>"></span>
				</p>
			</div>
			<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
				<p style="margin: 0;">
					<label for="campaign_lastag"><b><</b><input type="text" size="6" class="small-text" id="campaign_lastag" name="campaign_lastag" value="<?php echo stripslashes($campaign_lastag); ?>" /><b>></b> 
						<b><?php _e('HTML tag:', 'wpematico'); ?></b></label><br />
					<span class="description"><?php _e('(example: div, p, span, etc.)', 'wpematico'); ?></span>
				</p>
			</div>
			<div class="clear"></div>
			<?php
		}

		//*************************************************************************************
		static function wcountf_box($post) {
			global $post, $campaign_data, $helptip;
			//if(!is_array($campaign_data['campaign_wcf'])) $campaign_data['campaign_wcf'] = array();
			$campaign_wcf	 = @$campaign_data['campaign_wcf'];
			$cfg			 = get_option(self :: OPTION_KEY);
			?>
			<p class="he20">
				<span class="left"><?php _e('This allow you to ignore a post if below X words or letters in content.  Also allow assign a category to the post if greater than X words.', 'wpematico'); ?></span>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['Word_Count_Filters']; ?>"></span>
			<ul id="wcf_edit" class="inlinetext">
				<li class="jobtype-select">
					<div id="w1" style="float:left;">
						<label for="campaign_wcf_great_amount"><b><?php _e('Greater than:', 'wpematico'); ?></b></label>
						<input type="number" min="0" size="5" class="small-text" id="campaign_wcf_great_amount" name="campaign_wcf_great_amount" value="<?php echo stripslashes($campaign_wcf['great_amount']); ?>" />
						<span id="gwol">
							<?php echo ($campaign_wcf['great_words']) ? __('words.', 'wpematico') : __('letters.', 'wpematico'); ?> 
						</span>
						<br />
						<input name="campaign_wcf_great_words" id="campaign_wcf_great_words" class="checkbox chkgwol" value="1" type="checkbox"<?php checked($campaign_wcf['great_words'], true); ?> /><label for="campaign_wcf_great_words"> <?php _e('Words', 'wpematico'); ?></label>
					</div>
					<div id="c1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="campaign_wcf_category"> <?php _e('To Category:', 'wpematico'); ?></label>
						<?php
						$catselected	 = 'selected=' . $campaign_wcf['category'];
						$catname		 = "name=campaign_wcf_category";
						$catid			 = "id=campaign_wcf_category";
						wp_dropdown_categories('hide_empty=0&hierarchical=1&show_option_none=' . __('Select category', 'wpematico') . '&' . $catselected . '&' . $catname . '&' . $catid);
						?>
					</div>
				</li>
				<?php // cut at if greater   ?>
				<li class="jobtype-select">
					<div id="w1" style="float:left;">
						<label for="campaign_wcf_cut_amount"><b><?php _e('Cut at:', 'wpematico'); ?></b></label>
						<input type="number" min="0" size="5" class="small-text" id="campaign_wcf_cut_amount" name="campaign_wcf_cut_amount" value="<?php echo stripslashes($campaign_wcf['cut_amount']); ?>" />
						<span id="cwol">
							<?php echo ($campaign_wcf['cut_words']) ? __('words.', 'wpematico') : __('letters.', 'wpematico'); ?> 
						</span>
						<?php _e('if greater.', 'wpematico'); ?>
						<br />
						<input name="campaign_wcf_cut_words" id="campaign_wcf_cut_words" class="checkbox chkcwol" value="1" type="checkbox"<?php checked($campaign_wcf['cut_words'], true); ?> /><label for="campaign_wcf_cut_words"> <?php _e('Words', 'wpematico'); ?></label>
					</div>
				</li>
				<?php // Discard is less   ?>
				<li class="jobtype-select">
					<div id="w1" style="float:left;">
						<label for="campaign_wcf_less_amount"><b><?php _e('Discard post is less than:', 'wpematico'); ?></b></label>
						<input type="number" min="0" size="5" class="small-text" id="campaign_wcf_less_amount" name="campaign_wcf_less_amount" value="<?php echo stripslashes($campaign_wcf['less_amount']); ?>" />
						<span id="lwol">
							<?php echo ($campaign_wcf['less_words']) ? __('words.', 'wpematico') : __('letters.', 'wpematico'); ?> 
						</span>
						<br />
						<input name="campaign_wcf_less_words" id="campaign_wcf_less_words" class="checkbox chklwol" value="1" type="checkbox"<?php checked($campaign_wcf['less_words'], true); ?> /><label for="campaign_wcf_less_words"> <?php _e('Words', 'wpematico'); ?></label>
					</div>
				</li>

			</ul>
			<?php
		}

		//*************************************************************************************
		static function kwordf_box($post) {
			global $post, $campaign_data, $helptip;

			$campaign_kwordf = $campaign_data['campaign_kwordf'];
			$cfg			 = get_option(self :: OPTION_KEY);
			?>
			<p class="he20">
				<span class="left"><?php _e('Skip posts with words in content or words not in content.', 'wpematico'); ?></span><span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['skip_posts_with_words']; ?>"></span>


			<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
				<b><?php _e('Must contain', 'wpematico'); ?>:</b><br />
				<div style="padding: 0.5em;float:left;">
					<label><input name="campaign_kwordf_inc_tit" id="campaign_kwordf_inc_tit" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['inctit'], true); ?> /> <?php _e('Search in Title', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_inc_con" id="campaign_kwordf_inc_con" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['inccon'], true); ?> /> <?php _e('Search in Content', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_inc_cat" id="campaign_kwordf_inc_cat" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['inccat'], true); ?> /> <?php _e('Search in Categories', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_inc_anyall" id="campaign_kwordf_any" class="radio" value="anyword" type="radio" <?php checked("anyword" == $campaign_kwordf['inc_anyall'], true); ?> /> <?php _e('Any of these words', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_inc_anyall" id="campaign_kwordf_all" class="checkbox" value="allwords" type="radio"<?php checked("allwords" == $campaign_kwordf['inc_anyall'], true); ?> /> <?php _e('All of these words', 'wpematico'); ?></label><br />
				</div>
				<label for="campaign_kwordf_inc"><?php _e('Words:', 'wpematico'); ?></label><br />
				<textarea style="width: 50%; height: 70px;" class="regular-text" id="campaign_kwordf_inc" name="campaign_kwordf_inc"><?php echo stripslashes($campaign_kwordf['inc']); ?></textarea><br />
				<label for="campaign_kwordf_incregex"><?php _e('RegEx:', 'wpematico'); ?></label>		
				<input class="regular-text" type="text" id="campaign_kwordf_incregex" name="campaign_kwordf_incregex" value="<?php echo stripslashes($campaign_kwordf['incregex']); ?>" />
			</div>
			<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
				<b><?php _e('Cannot contain:', 'wpematico'); ?></b><br />
				<div style="padding: 0.5em;float:left;">
					<label><input name="campaign_kwordf_exc_tit" id="campaign_kwordf_exc_tit" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['exctit'], true); ?> /> <?php _e('Search in Title', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_exc_con" id="campaign_kwordf_exc_con" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['exccon'], true); ?> /> <?php _e('Search in Content', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_exc_cat" id="campaign_kwordf_exc_cat" class="checkbox" value="1" type="checkbox"<?php checked($campaign_kwordf['exccat'], true); ?> /> <?php _e('Search in Categories', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_exc_anyall" id="campaign_kwordf_any" class="radio" value="anyword" type="radio" <?php checked("anyword" == $campaign_kwordf['exc_anyall'], true); ?> /> <?php _e('Any of these words', 'wpematico'); ?></label><br />
					<label><input name="campaign_kwordf_exc_anyall" id="campaign_kwordf_all" class="checkbox" value="allwords" type="radio"<?php checked("allwords" == $campaign_kwordf['exc_anyall'], true); ?> /> <?php _e('All of these words', 'wpematico'); ?></label><br />
				</div>
				<label for="campaign_kwordf_exc"><?php _e('Words:', 'wpematico'); ?></label><br />
				<textarea style="width: 50%; height: 70px;" class="regular-text" id="campaign_kwordf_exc" name="campaign_kwordf_exc"><?php echo stripslashes($campaign_kwordf['exc']); ?></textarea><br />
				<label for="campaign_kwordf_excregex"><?php _e('RegEx:', 'wpematico'); ?></label>		
				<input type="text" class="regular-text" id="campaign_kwordf_excregex" name="campaign_kwordf_excregex" value="<?php echo stripslashes($campaign_kwordf['excregex']); ?>" />				    
			</div>

			<div class="clear"></div>
			<?php
		}

		/**
		 * Static function 
		 * @access public
		 * @return void
		 * @since version
		 */
		public static function cfeed_tags_box() {
			global $post, $campaign_data, $helptip;
			$campaign_cfeed_tags = $campaign_data['campaign_cfeed_tags'];
			if(!($campaign_cfeed_tags)) {
				$campaign_cfeed_tags = array('name' => array(''), 'value' => array(''));
			}
			?>
			<p class="he20">
				<span class="left"><?php _e('Add custom feed tags to use on template or custom fields', 'wpematico') ?></span> 
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['Custom_Feed_Tags']; ?>"></span>
			</p>		

			<div id="cfeed_tags_edit" class="inlinetext">		
				<?php
				foreach($campaign_cfeed_tags['name'] as $i => $value) :
					?>			
					<div class="custom_feed_tag_element">
						<div class="clear pDiv jobtype-select rowflex">
							<div id="cf1" class="rowblock left p4" style="width: 45%;">
								<?php _e('Feed Tag:', 'wpematico') ?>
								<input name="campaign_cfeed_tags[name][]" type="text" value="<?php echo stripslashes(@$campaign_cfeed_tags['name'][$i]) ?>" class="large-text campaign_cft_name" id="campaign_cft_name" />
							</div>
							<div class="rowblock left p4" style="width: 45%;">
								<?php _e('Template:', 'wpematico') ?>
								<input name="campaign_cfeed_tags[value][]" type="text" value="<?php echo stripslashes(@$campaign_cfeed_tags['value'][$i]) ?>" class="large-text campaign_cft_value" id="campaign_cft_value" />
							</div>
							<div class="rowactions">
								<span class="" id="w2cactions">
									<label title="<?php _e('Delete this item', 'wpematico'); ?>" class="bicon delete left delete_custom_feed_tag"></label>
								</span>
							</div>
						</div>
					</div>
					<?php
				endforeach;
				?>

			</div>
			<div class="clear"></div>
			<div id="paging-box" class="clear">		  
				<a href="#" class="button-primary add" id="addmore_custom_feed_tags" style="font-weight: bold; text-decoration: none;"> <?php _e('Add more', 'wpematico'); ?>.</a>
			</div>

			<?php
		}

		static function cfields_box($post) {
			global $post, $campaign_data, $helptip;
			$campaign_cfields	 = $campaign_data['campaign_cfields'];
			if(!($campaign_cfields))
				$campaign_cfields	 = array('name' => array(''), 'value' => array(''));
			?>
			<p class="he20">
				<span class="left"><?php _e('Add custom fields with values as templates.', 'wpematico') ?></span> 
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['Custom_Fields']; ?>"></span>
			</p>		

			<div id="cfield_edit" class="inlinetext">		
				<?php for($i = 0; $i <= count(@$campaign_cfields['name']); $i++) : ?>			
					<div class="<?php
					if(($i % 2) == 0)
						echo 'bw';
					else
						echo 'lightblue';
					?> <?php if($i == count($campaign_cfields['name'])) echo 'hide'; ?>">
						<div class="clear pDiv jobtype-select rowflex" id="nuevocfield">
							<div id="cf1" class="rowblock left p4" style="width: 45%;">
				<?php _e('Name:', 'wpematico') ?>&nbsp;&nbsp;&nbsp;&nbsp; 
								<input name="campaign_cf_name[<?php echo $i; ?>]" type="text" value="<?php echo stripslashes(@$campaign_cfields['name'][$i]) ?>" class="large-text" id="campaign_cf_name" />
							</div>
							<div class="rowblock left p4" style="width: 45%;">
				<?php _e('Value:', 'wpematico') ?>
								<input name="campaign_cf_value[<?php echo $i; ?>]" type="text" value="<?php echo stripslashes(@$campaign_cfields['value'][$i]) ?>" class="large-text" id="campaign_cf_value" />
							</div>
							<div class="rowactions">
								<span class="" id="w2cactions">
									<label title="<?php _e('Delete this item', 'wpematico'); ?>" onclick=" jQuery(this).parent().parent().parent().children('#cf1').children('#campaign_cf_name').val('');
											jQuery(this).parent().parent().parent().fadeOut();" class="bicon delete left"></label>
								</span>
							</div>
						</div>
					</div>
					<?php
					$a = $i;
				endfor
				?>
				<input id="cfield_max" value="<?php echo $a; ?>" type="hidden" name="cfield_max">

			</div>
			<div class="clear"></div>
			<div id="paging-box" class="clear">		  
				<a href="JavaScript:void(0);" class="button-primary add" id="addmorecf" style="font-weight: bold; text-decoration: none;"> <?php _e('Add more', 'wpematico'); ?>.</a>
			</div>

			<?php
		}

		/**
		 * Static function pro_parent_page_box
		 * @access public
		 * @return void
		 * @since 1.7.4
		 */
		public static function pro_parent_page_box() {
			global $post, $campaign_data, $helptip;
			$args = array(
				'selected'			 => $campaign_data['campaign_parent_page'],
				'name'				 => 'campaign_parent_page',
				'show_option_none'	 => __('Select a page', 'wpematico')
			);
			wp_dropdown_pages($args);
		}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// RUNNING FETCHING
		//* Suma la cantidad de post al nro de titulo
		static public function ending($campaign, $fetched_posts) {
			if(isset($campaign['campaign_enablecustomtitle']) && $campaign['campaign_enablecustomtitle']) {
				$campaign['campaign_ctnextnumber'] += $fetched_posts;
			}
			return $campaign;
		}

		// Item author
		static public function author($current_item, $campaign, $feed, $item) {
			$cfg = get_option(self :: OPTION_KEY);
			if(isset($cfg['enableauthorxfeed']) && $cfg['enableauthorxfeed']) {

				if($campaign[$feed]['feed_author'] > "0") {
					$current_item['author'] = $campaign[$feed]['feed_author'];
				}else if($campaign[$feed]['feed_author'] == "0") {
					$current_item = WPeMaticoPro_Campaign_Fetch::get_author_from_feed($current_item, $campaign, $feed, $item);
				}
			}
			trigger_error(sprintf(__('Assigning author %1s to %2s', 'wpematico'), $current_item['author'], $current_item['title']), E_USER_NOTICE);
			return $current_item;
		}

		//static function googlenewslink($permalink) {
		static function wpematico_googlenewslink($permalink) {
			// si es de google news feed toma del enlace destino con la variable &url=
			$urlparsed = parse_url($permalink);
			if(isset($urlparsed['query']) && !empty($urlparsed['query'])) {
				parse_str($urlparsed['query']);
				if(isset($url))
					if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
						
					}else {
						$permalink = $url;
					}
			}
			return $permalink;
		}

		static function assign_custom_taxonomies($post_id, $campaign) {
			// copy taxonomies from campaign to new post, skip categories, tags and post formats 
			$taxonomiesNewPost	 = get_object_taxonomies($campaign['campaign_customposttype']);
			$taxonomiesNewPost	 = array_diff($taxonomiesNewPost, array('category', 'post_tag', 'post_format'));
			foreach($taxonomiesNewPost AS $tax) {
				$terms	 = wp_get_object_terms($campaign['ID'], $tax);
				$term	 = array();
				$taxstr	 = '';
				foreach($terms AS $t) {
					$term[]	 = $t->slug;
					$taxstr	 .= ', ' . $t->slug;
				}
				$taxstr = substr($taxstr, 2);
				trigger_error(sprintf(__('Assigning Taxonomies %1s', 'wpematico'), $taxstr), E_USER_NOTICE);
				wp_set_object_terms($post_id, $term, $tax);
			}
		}

		// Item title
		public static function title($current_item, $campaign, $item, $count) {
			$cfg									 = get_option(self :: OPTION_KEY);
			$enablecustomtitle						 = ( isset($cfg['enablecustomtitle']) && $cfg['enablecustomtitle'] );
			$campaign_enablecustomtitle				 = ( isset($campaign['campaign_enablecustomtitle']) && $campaign['campaign_enablecustomtitle'] );
			$campaign_delete_till_ontitle			 = ( empty($campaign['campaign_delete_till_ontitle']) ? false : $campaign['campaign_delete_till_ontitle']);
			$campaign_delete_till_ontitle_keep		 = ( empty($campaign['campaign_delete_till_ontitle_keep']) ? false : $campaign['campaign_delete_till_ontitle_keep']);
			$campaign_delete_till_ontitle_characters = ( empty($campaign['campaign_delete_till_ontitle_characters']) ? '. ? !' : $campaign['campaign_delete_till_ontitle_characters']);
			$till_characters_array					 = explode(' ', $campaign_delete_till_ontitle_characters);

			$campaign_ontitle_cut_at		 = ( empty($campaign['campaign_ontitle_cut_at']) ? 0 : (int) $campaign['campaign_ontitle_cut_at']);
			$campaign_ontitle_cut_at_words	 = ( empty($campaign['campaign_ontitle_cut_at_words']) ? false : $campaign['campaign_ontitle_cut_at_words']);




			if($enablecustomtitle) {

				if($campaign_enablecustomtitle) {

					$title = $item->get_title();
					if($campaign_delete_till_ontitle) {
						$title = wpepro_delete_text_from_words($till_characters_array, $title, $campaign_delete_till_ontitle_keep);
					}

					if(!empty($campaign_ontitle_cut_at)) {
						if($campaign_ontitle_cut_at_words) {
							$title	 = wpepro_mb_wordcount($title, $campaign_ontitle_cut_at, '');
							$msg	 = $campaign_ontitle_cut_at . " words";
						}else {
							$title	 = mb_substr($title, 0, $campaign_ontitle_cut_at);
							$msg	 = $campaign_ontitle_cut_at . " letters. ";
						}
						trigger_error(sprintf(__('Cutting the title at %1s', 'wpematico'), $msg), E_USER_NOTICE);
					}

					// miro si está y reemplazo la palabra {title}
					$vars					 = array('{title}');
					$replace				 = array($title);
					$current_item['title']	 = str_ireplace($vars, $replace, $campaign['campaign_customtitle']);

					$current_item['title'] = esc_attr($current_item['title']);
					/* .
					  $mustcant = $campaign['campaign_wcf']['cut_amount'];
					  if ($mustcant > 0) {
					  //$current_item['images']=array( '0' => $current_item['images'][0] );
					  $current_item['images']= array_slice($current_item['images'], 0, 1);  // just first for featured img
					  if($campaign['campaign_wcf']['cut_words'] ) {  //Counting words strip html tags
					  $current_item['content'] = self :: wordCount($current_item['content'], $mustcant);
					  $mes = $mustcant. " words";
					  }else{  // counting letters count also html tags and close them after cut
					  //$current_item['content'] = substr($current_item['content'],0,$mustcant);
					  $current_item['content'] = wpempro_closetags( substr($current_item['content'],0,$mustcant) );
					  $mes = $mustcant. " letters. ";
					  }
					  trigger_error(sprintf(__('Cutting at %1s','wpematico'),$mes),E_USER_NOTICE);
					 */

					//$current_item['title'] = $campaign['campaign_customtitle'];
					if($campaign['campaign_ctitlecont']) {
						// si encuentra {counter} en el campo lo reemplaza por el contador, sino lo agrega al final
						$counter = sprintf("%0" . $campaign['campaign_ctdigits'] . "d", ($count + (int) $campaign['campaign_ctnextnumber']));
						$pos	 = strpos($current_item['title'], '{counter}');
						if($pos !== false) {
							$current_item['title'] = str_ireplace('{counter}', $counter, $current_item['title']);
						}else {
							$current_item['title'] = $current_item['title'] . $counter;
						}
					}
				}else {
					$current_item['title'] = esc_attr($item->get_title());
					if($campaign_delete_till_ontitle) {
						$current_item['title'] = wpepro_delete_text_from_words($till_characters_array, $current_item['title'], $campaign_delete_till_ontitle_keep);
					}
					if(!empty($campaign_ontitle_cut_at)) {
						if($campaign_ontitle_cut_at_words) {
							$current_item['title']	 = wpepro_mb_wordcount($current_item['title'], $campaign_ontitle_cut_at);
							$msg					 = $campaign_ontitle_cut_at . " words";
						}else {
							$current_item['title']	 = mb_substr($current_item['title'], 0, $campaign_ontitle_cut_at);
							$msg					 = $campaign_ontitle_cut_at . " letters. ";
						}
						trigger_error(sprintf(__('Cutting the title at %1s', 'wpematico'), $msg), E_USER_NOTICE);
					}
				}
			}else {
				$current_item['title'] = esc_attr($item->get_title());
			}

			trigger_error(sprintf(__('Changing title to %1s', 'wpematico'), $current_item['title']), E_USER_NOTICE);
			return $current_item;
		}

		// Discard post is less than    $current_item, $campaign, $feed, $item
		public static function discardwordcountless($current_item, $campaign, $feed, $item) {
			if($current_item == -1)
				return -1;
			$cfg = get_option(self :: OPTION_KEY);
			if(isset($cfg['enablewcf']) && $cfg['enablewcf']) {
				trigger_error(sprintf(__('Counting Words on %1s', 'wpematico'), $current_item['title']), E_USER_NOTICE);
				$words	 = self :: wordCount($current_item['content']);
				$letters = strlen($current_item['content']);
				trigger_error(sprintf(__('Found %1s words with %2s letters in content.', 'wpematico'), $words, $letters), E_USER_NOTICE);

				// skiping
				$mustcant = $campaign['campaign_wcf']['less_amount'];
				if($mustcant > 0) {
					if($campaign['campaign_wcf']['less_words']) {
						$havecant	 = $words;
						$mes		 = $havecant . " words";
					}else {
						$havecant	 = $letters;
						$mes		 = $havecant . " letters";
					}
					if($havecant < $mustcant) {
						trigger_error(sprintf(__('Skipping: %1s', 'wpematico'), $mes), E_USER_NOTICE);
						$current_item = -1;  // skip the post
					}
				}
			}
			return $current_item;
		}

		// strip only last HTML tag 
		public static function strip_lastag($current_item, $campaign, $feed, $item) {
			if($current_item == -1)
				return -1;
			$cfg = get_option(self :: OPTION_KEY);

			$campaign_lastag = !empty($campaign['campaign_lastag']) ? $campaign['campaign_lastag'] : "";
			if(is_array($campaign_lastag) and!empty($campaign['campaign_lastag']['tag'])) {
				$campaign_lastag = $campaign['campaign_lastag']['tag'];
			}

			// *** Campaign Last html Tag to delete
			if(!empty($campaign_lastag)) {
				trigger_error('Deleting last HTML tag &lt;' . $campaign_lastag . '&gt;<br>', E_USER_NOTICE);
				$current_item['content'] = self :: without_last($current_item['content'], $campaign_lastag);
			}

			return $current_item;
		}

		/**		 * if found, delete the last paragraph of content  * */
		Function without_last($string, $tag = "p") {
			$tag = str_replace(" ", "", $tag);
			if(!empty($tag)) {
				$pos = strripos($string, "<" . $tag);
				if($pos === false) {
					return $string;  // No lo encontró, devuelve todo
				}else { //lo encontró
					$restring	 = substr($string, $pos); //desde la posición que lo encontró hasta el final
					$regex		 = "#([<]" . $tag . ")(.*)([<]/" . $tag . "[>])#";  // tag y cierre de tag
					$cleanend	 = preg_replace($regex, '', $restring); //elimino el tag hasta el cierre
					return substr($string, 0, $pos) . $cleanend; //hasta el tag mas lo que resta sin el contenido del tag
				}
			}
		}

		// *** Word count filters I need the content for this
		public static function wordcountfilters($current_item, $campaign, $feed, $item) {
			if($current_item == -1)
				return -1;
			$cfg = get_option(self :: OPTION_KEY);
			if(@$cfg['enablewcf']) {
				trigger_error(sprintf(__('Processing Words count Filters %1s', 'wpematico'), $current_item['title']), E_USER_NOTICE);
				$words		 = self :: wordCount($current_item['content']);
				$letters	 = strlen($current_item['content']);
				trigger_error(sprintf(__('Found %1s words with %2s letters in content.', 'wpematico'), $words, $letters), E_USER_NOTICE);
				// if greather than x -> category
				$mustcant	 = $campaign['campaign_wcf']['great_amount'];
				if($mustcant > 0) {
					if($campaign['campaign_wcf']['great_words']) {
						$havecant	 = $words;
						$mes		 = $mustcant . " words. ";
					}else {
						$havecant	 = $letters;
						$mes		 = $mustcant . " letters. ";
					}
					if($mustcant <= $havecant) {
						$tocat			 = $campaign['campaign_wcf']['category'];
						$categories[]	 = $tocat;
						trigger_error(sprintf(__('Greater than %1s To Cat_id %2s', 'wpematico'), $mes, $tocat), E_USER_NOTICE);
					}
				}
				// cutting at x
				$mustcant = $campaign['campaign_wcf']['cut_amount'];
				if($mustcant > 0) {
					//$current_item['images']=array( '0' => $current_item['images'][0] );
					$current_item['images'] = array_slice($current_item['images'], 0, 1);  // just first for featured img
					if($campaign['campaign_wcf']['cut_words']) {  //Counting words strip html tags
						$current_item['content'] = self :: wordCount($current_item['content'], $mustcant);
						$mes					 = $mustcant . " words";
					}else {  // counting letters count also html tags and close them after cut
						//$current_item['content'] = substr($current_item['content'],0,$mustcant);
						$current_item['content'] = wpempro_closetags(substr($current_item['content'], 0, $mustcant));
						$mes					 = $mustcant . " letters. ";
					}
					trigger_error(sprintf(__('Cutting at %1s', 'wpematico'), $mes), E_USER_NOTICE);
				}
			} // Word count filters	
			return $current_item;
		}

		static function strip_tags_title($args, $campaign) {
			if(@$campaign['campaign_striptagstitle']) {
				trigger_error(sprintf(__('Strip HTML tags from title %1s', 'wpematico'), $args['post_title']), E_USER_NOTICE);
				$args['post_title'] = strip_tags(htmlspecialchars_decode($args['post_title'], ENT_QUOTES));
			}
			return $args;
		}

		/**		 * Automatic tags  * */
		static Function postags(&$current_item, &$campaign, &$item) {
			$cfg = get_option(self :: OPTION_KEY);

			if(empty($current_item['tags']) || !is_array($current_item['tags'])) {
				$current_item['tags'] = array();
			}

			if($campaign['campaign_tags_feeds'] && $cfg['enabletags']) {
				trigger_error(__('Adding tags from feed to post.', 'wpematico'), E_USER_NOTICE);
				$tags_item = $item->get_item_tags('', 'tag');
				if(is_array($tags_item)) {
					foreach($tags_item as $tagi) {
						$data_tag = $tagi['data'];

						if(strlen($data_tag) > apply_filters('wpem_autotags_min_length', 3) && strpos($data_tag, " ") === false) {
							$current_item['tags'][] = $data_tag;
						}
					}
				}
			}


			if(!empty($campaign['campaign_tags']) && (!$cfg['enabletags'] || !$campaign['campaign_autotags'] )) {
				trigger_error(__('Adding custom tags to post.', 'wpematico'), E_USER_NOTICE);
				if(!is_array($current_item['tags'])) {
					$current_item['tags'] = array();
				}
				$manual_tags = explode(',', $campaign['campaign_tags']);
				foreach($manual_tags as $mt) {
					$current_item['tags'][] = $mt;
				}
			}else {

				if($cfg['enabletags'] && $campaign['campaign_autotags']) {
					if(!is_array($current_item['tags'])) {
						$current_item['tags'] = array();
					}

					trigger_error(__('Adding tags automatically to post.', 'wpematico'), E_USER_NOTICE);

					$badtags0	 = explode(',', sanitize_text_field($campaign['campaign_badtags']));
					$badtags1	 = sanitize_text_field($cfg['all_badtags']);
					$badtags1	 = (isset($badtags1) && empty($badtags1) ) ? $badtags1	 = array() : explode(',', $badtags1);
					//$badtags = array_merge($badtags0, $badtags1);
					$badtags	 = array_map('trim', array_merge($badtags0, $badtags1));
					$badchars	 = array(",", ":", "(", ")", "]", "[", "?", "!", ";", "-", '.', '"', '<', '>');

					$i = count($current_item['tags']);
					if(count($current_item['tags']) >= (int) $campaign['campaign_nrotags']) {
						$current_item['tags']	 = array_slice($current_item['tags'], 0, (int) $campaign['campaign_nrotags']);
						$i						 = count($current_item['tags']);
					}

					$content = str_replace($badchars, "", strip_tags(nl2br(html_entity_decode($current_item['content']))));
					$tags	 = explode(' ', $content);

					foreach($tags as $key => $value) {
						if(strlen($value) > apply_filters('wpem_autotags_min_length', 3) && strpos($value, " ") === false) {
							if(!in_array(strtolower($value), $badtags)) {
								if($i++ >= (int) $campaign['campaign_nrotags']) {
									break;
								}
								$current_item['tags'][] = $value;
							}
						}
					}
				}
			}
			return $current_item;
		}

		/**		 * Custom Fields  * */
		Public static function metaf(&$current_item, &$campaign, &$feed, &$item) {
			$cfg = get_option(self :: OPTION_KEY);
			if(!empty($campaign['campaign_cfields']) && $cfg['enablecfields']) {
				trigger_error(__('Parsing Custom fields values.', 'wpematico'), E_USER_NOTICE);
				$featured_image = (!empty($current_item['featured_image']) ? $current_item['featured_image'] : '');

				$template_vars	 = wpematico_campaign_fetch_functions::default_template_vars(array(), $current_item, $campaign, $feed, $item, $featured_image);
				$vars			 = array();
				$replace		 = array();
				foreach($template_vars as $tvar => $tvalue) {
					$vars[]		 = $tvar;
					$replace[]	 = $tvalue;
				}

				$vars	 = apply_filters('wpematico_post_template_tags', $vars, $current_item, $campaign, $feed, $item);
				$replace = apply_filters('wpematico_post_template_replace', $replace, $current_item, $campaign, $feed, $item);

				for($i = 0; $i < count($campaign['campaign_cfields']['name']); $i++) {
					$cf_name			 = $campaign['campaign_cfields']['name'][$i];
					$cf_value			 = $campaign['campaign_cfields']['value'][$i];
					$cf_value			 = str_ireplace($vars, $replace, $cf_value);
					$arraycf[$cf_name]	 = $cf_value;
				}
				$current_item['meta'] = (isset($current_item['meta']) && !empty($current_item['meta']) ) ? array_merge($current_item['meta'], $arraycf) : $arraycf;
			}
			//trigger_error(print_r($current_item['meta']),E_USER_NOTICE);
			return $current_item;
		}

		/**		 * Count the real words from string
		 *
		 * if limit <> 0 return the new cuted string else return words counted
		 */
		static Public function wordCount($string, $limit = 0, $endstr = ' ...') {
			# strip all html tags
			$text = strip_tags($string);

			/* 	# remove 'words' that don't consist of alphanumerical characters or punctuation
			  $pattern = "#[^(\w|\d|\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]+#";
			  $text = trim(preg_replace($pattern, " ", $text));

			  # remove one-letter 'words' that consist only of punctuation
			  $text = trim(preg_replace("#\s*[(\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]\s*#", " ", $text));

			  # remove superfluous whitespace
			  $text = preg_replace("/\s\s+/", " ", $text);
			 */
			$characterMap	 = 'áéíóúüñ';
			$words			 = str_word_count($text, 2, $characterMap);

			# remove empty elements
			$words = array_filter($words);

			$count = count($words);

			if($limit > 0) {
				$pos = array_keys($words);
				if($count > $limit) {
					$text = substr($text, 0, $pos[$limit]) . $endstr;
				}
			}

			return ($limit == 0) ? $count : $text;
		}

		/**
		 * 
		 */
		static function wpematico_overwrite_file($new_file) {
			if(file_exists($new_file)) {
				if(unlink($new_file))
					trigger_error('Overwriting image ' . $new_file, E_USER_WARNING);
				else
					trigger_error('Can\'t Overwrite image, renaming ' . $new_file, E_USER_WARNING);
			}
			return $new_file;
		}

		static function wpematico_keep_file($new_file) {
			if(file_exists($new_file)) {
				trigger_error('Keeping original image ' . $new_file, E_USER_WARNING);
				return false;
			}
			return $new_file;
		}

		/**
		 * strip if no image on content
		 * @return -1 if skip else $current_item
		 */
		static function discardifnoimage($current_item, $campaign, $feed, $item) {
			if($current_item == -1)
				return -1;
			$images		 = wpematico_campaign_fetch::parseImages($current_item['content']);
			$urls		 = $images[2];
			if($campaign['rssimg_enclosure'] && ($enclosure	 = $item->get_enclosure())) {
				$imgenc	 = $enclosure->get_link();
				$urls[]	 = $imgenc;
			}
			// removes all NULL, FALSE and Empty Strings but leaves 0 (zero) values
			$images = array_values(array_filter($urls, 'strlen'));
			if(sizeof($images) == 0 && empty($current_item['featured_image'])) {
				trigger_error(sprintf(__('No image in content -> skipping', 'wpematico')), E_USER_NOTICE);
				return -1;
			}

			return $current_item;
		}

		static function discardifnoimage_aux($allow, $fetch, $args) {
			if(!$allow) {
				return $allow;
			}
			$campaign		 = $fetch->campaign;
			$current_item	 = $fetch->current_item;
			$images			 = wpematico_campaign_fetch::parseImages($current_item['content']);
			$urls			 = $images[2];

			// removes all NULL, FALSE and Empty Strings but leaves 0 (zero) values
			$images = array_values(array_filter($urls, 'strlen'));
			if(sizeof($images) == 0 && empty($current_item['featured_image'])) {
				trigger_error(sprintf(__('No image in content -> skipping', 'wpematico')), E_USER_NOTICE);
				$allow = false;
			}
			return $allow;
		}

		/**
		 * Static function partial_upload_file
		 * @access public
		 * @return void
		 * @since 1.7.1
		 */
		public static function partial_upload_file($src_real, $file_dest, $options) {
			$dest_file = apply_filters('wpematico_overwrite_file', $file_dest);
			if($dest_file === FALSE) {
				return $file_dest;  // Don't upload it and return the name like it was uploaded
			}
			$file_dest	 = $dest_file;
			$i			 = 1;
			while (file_exists($file_dest)) {
				$file_extension = strrchr($file_dest, '.'); //Will return .JPEG   substr($url_origen, strlen($url_origen)-4, strlen($url_origen));
				if($i == 1) {
					$file_name	 = substr($file_dest, 0, strlen($file_dest) - strlen($file_extension));
					$file_dest	 = $file_name . "-$i" . $file_extension;
				}else {
					$file_name	 = substr($file_dest, 0, strlen($file_dest) - strlen($file_extension) - strlen("-$i"));
					$file_dest	 = $file_name . "-$i" . $file_extension;
				}
				$i++;
			}
			if(wpe_partial_content_curl::start($src_real, $file_dest, $options['upload_range_mb'])) {
				return $file_dest;
			}else {
				return false;
			}
		}

		/**
		 * Static function check_image_content
		 * @access public
		 * @param   $current_item   array    Current post data to be saved
		 * @param   $campaign       array    Current campaign data
		 * @param   $item           object    SimplePie_Item object
		 * @param   $options_images array    Current options of images.
		 * @return  $current_item   array    Current post data to be saved
		 * @since 1.7.3
		 */
		public static function check_image_content($current_item, $campaign, $item, $options_images) {
			if(!isset($current_item['images'])) {
				$current_item['images'] = array();
			}
			$new_array_images = array();
			foreach($current_item['images'] as $image) {
				$headers = get_headers($image, 1);
				if($headers !== false && isset($headers['Content-Type'])) {
					if(strpos($headers['Content-Type'], 'image') === false) {
						if(isset($campaign['strip_image_without_content']) && $campaign['strip_image_without_content']) {
							trigger_error(__('Deleting image with incorrect content:', 'wpematico') . urldecode($image), E_USER_WARNING);
							$current_item['content'] = wpematico_campaign_fetch_functions::strip_Image_by_src($image, $current_item['content']);
						}
					}else {

						$new_array_images[] = $image;
					}
				}
			}
			$current_item['images'] = $new_array_images;
			return $current_item;
		}

		/**
		 * Clean parameters or query vars to a new image name
		 * @param type $newimgname
		 * @return string
		 */
		public static function image_src_gettype($newimgname, $current_item, $campaign, $item) {
			// Find only image filenames after the / and before the ? sign (? = 3F here)
			preg_match('/[^\/\3F]+\.(?:jp[eg]+|png|bmp|giff?|tiff?)/i', $newimgname, $matches);
			if(empty($matches)) {
				preg_match('/[^\/\?]+\.(?:jp[eg]+|png|bmp|giff?|tiff?)/i', $newimgname, $matches);
			}
			if(empty($matches)) { // is not an image extension
				// busco la url completa por el nombre primero si es la featured o el array de imagenes
				$url = '';
				if(sanitize_file_name(urlencode(basename($current_item['featured_image']))) == $newimgname) {
					$url = $current_item['featured_image'];
				}else {
					foreach($current_item['images'] as $image) {
						if(sanitize_file_name(urlencode(basename($image))) == $newimgname) {
							$url = $image;
							break;
						}
					}
				}
				if(!empty($url)) {
					$extension = wpempro_get_img_ext_from_header($url);
				}

				$name = strtok($newimgname, 'F3');
				if($name == $newimgname) {
					$name = strtok($newimgname, '?');
					if($name == $newimgname) { // last resource = harcoded name
						$name = 'codeimg.jpg';
					}
				}
			}else {
				$name = $matches[0];
			}

			// First step of urldecode and sanitize the filename
			$imgname = sanitize_file_name(urldecode(basename($name)));
			// Split the name from the extension
			$parts	 = explode('.', $imgname);
			$name	 = array_shift($parts);

			$extension = (empty($extension)) ? 'jpg' : $extension; // Allways JPG if extension is missing
			// Join all names splitted by dots
			foreach((array) $parts as $part) {
				$name .= '.' . $part;
			}
			// Second step of urldecode and sanitize only the name of the file
			$name		 = sanitize_title(urldecode($name));
			// Join the name with the extension
			//$newimgname = dirname($newimgname) . '/' . $name . '.' . $extension;
			$newimgname	 = $name . '.' . $extension;
			return $newimgname;
		}

		// Strip all images from wpematico posts before insert
		static function wpetruel_strip_img_tags($text) {
			$text = preg_replace("/<img[^>]+\>/i", " ", htmlspecialchars_decode($text, ENT_QUOTES));
			return $text;
		}

		static function wpetruel_strip_img_tags_content($current_item, $campaign) {
			$current_item['content'] = self::wpetruel_strip_img_tags($current_item['content']);
			return $current_item;
		}

		// See if there is image in feed 
		// return array with images 
		static public function imgfind(&$current_item, &$campaign, &$item) {
			$cfg = get_option(self :: OPTION_KEY);

			$urls										 = $current_item['images'];
			WPeMaticoPRO::$rssimg_add2img_featured_image = '';

			$rssurls = array();
			if($campaign['campaign_rssimg']) { // Si busco en el RSS content SIN filtrar
				$execute_rss_images = true;
				if(($campaign['rssimg_ifno']) && (sizeof($current_item['images']) > 0 )) { // // la agrega si no hay img en el contenido
					$execute_rss_images = false;
				}
				/*
				  if( !$campaign['rssimg_ifno'] || (($campaign['rssimg_ifno']) && (sizeof($current_item['images']) == 0 ))) { // // la agrega si no hay img en el contenido
				  $images = wpematico_campaign_fetch_functions :: parseImages( $item->get_content() );
				  $rssurls = $images[2];
				  }
				 */
				//$imgenc = $urls[0];
				if($campaign['rssimg_enclosure'] && $execute_rss_images) {

					if($allenclosures = $item->get_enclosures()) {
						$images_types	 = array('image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'image/gif');
						$images_types	 = apply_filters('wpematico_pro_allow_enclosures_images_mine', $images_types);
						foreach($allenclosures as $enclosure) {

							foreach((array) $enclosure->get_thumbnails() as $thumbnail) {
								$rssurls[] = $thumbnail;
								trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $thumbnail), E_USER_NOTICE);
							}
							if($imgenc = $enclosure->get_link()) {
								$current_type	 = $enclosure->get_type();
								$ok_medium		 = ($enclosure->get_medium() == 'image');
								if(!empty($current_type) or $ok_medium) {
									if(in_array($current_type, $images_types) !== false or $ok_medium) {
										$rssurls[] = $imgenc;
										trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $imgenc), E_USER_NOTICE);
									}else {
										if(wpempro_aunx_check_img_ext_enclosure($imgenc)) {
											$rssurls[] = $imgenc;
											trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $imgenc), E_USER_NOTICE);
										}else {
											trigger_error(sprintf(__('Enclosure type not accepted: %s', 'wpematico'), $current_type), E_USER_NOTICE);
										}
									}
								}else {
									if(wpempro_aunx_check_img_ext_enclosure($imgenc)) {
										$rssurls[] = $imgenc;
										trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $imgenc), E_USER_NOTICE);
									}
								}
							}
						}
					}
					$images_tag		 = $item->get_item_tags('', 'image');
					$images_tag_link = (is_array($images_tag) ? $images_tag[0]['data'] : '');
					if(!empty($images_tag_link)) {
						$rssurls[] = $images_tag_link;
						trigger_error(sprintf(__('Getting image tag: %s', 'wpematico'), $images_tag_link), E_USER_NOTICE);
					}
				}

				if($campaign['rssimg_add2img'] && $execute_rss_images) {  // RSS to Featured  
					if(!empty($rssurls)) {
						$featured_image								 = $rssurls[0];
						WPeMaticoPRO::$rssimg_add2img_featured_image = $rssurls[0];
						trigger_error(sprintf(__('Featured image from enclosure: %s', 'wpematico'), $rssurls[0]), E_USER_NOTICE);
					}

					// sumo las nuevas primero en la lista
					// $urls = array_merge($rssurls,$current_item['images']);
				}else { // sumo las nuevas al final en la lista
					$urls = array_merge($current_item['images'], $rssurls);
				}
			}
			// ************ image filters *****************
			// removes all NULL, FALSE and Empty Strings but leaves 0 (zero) values
			$urls					 = array_values(array_filter($urls, 'strlen'));
			$current_item['images']	 = $urls;
			if(!empty($campaign['imagefilters']) && $cfg['enableimgfilter']) :  // Si está habilitado en settings
				trigger_error(__('Applying Image Filters.', 'wpematico'), E_USER_NOTICE);
				$img2del = array();
				for($j = 0; $j < count($campaign['imagefilters']['value']); $j++) :
					$allow		 = ($campaign['imagefilters']['allow'][$j] == 'Allow') ? true : false;
					$woh		 = $campaign['imagefilters']['woh'][$j];
					$mol		 = ($campaign['imagefilters']['mol'][$j]) == 'more' ? '>=' : '<=';
					$if_value	 = $campaign['imagefilters']['value'][$j];
					for($i = 0; $i < count($urls); $i++) {
						$imageurl	 = $urls[$i];
						$sizeimg	 = self :: getjpegsize($imageurl);  // lee solo header, solo para jpeg
						if($sizeimg == false)
							$sizeimg	 = getimagesize($imageurl);
						if($sizeimg == false) {
							trigger_error(__("Don't works filters with: ", 'wpematico') . '"' . $urls[$i] . '"', E_USER_NOTICE);
							$current_item['content'] = wpematico_campaign_fetch_functions::strip_Image_by_src($urls[$i], $current_item['content']);
							$img2del[]				 = $urls[$i];
							continue;
						}
						list($ancho, $alto) = $sizeimg;
						$imgvalue	 = ($woh == "width") ? $ancho : $alto;
						$imgfilter	 = $imgvalue . $mol . $if_value;
						$compute	 = create_function("", "return (" . $imgfilter . ");");
						// Si no se cumple el filtro lo borra del contenido y del array y continua con la siguiente img
						trigger_error(__("Filter: ", 'wpematico') . '"' . $urls[$i] . '" ===> ' . $imgfilter, E_USER_NOTICE);
						if(!$compute()) {
							$current_item['content'] = wpematico_campaign_fetch_functions::strip_Image_by_src($urls[$i], $current_item['content']);
							$img2del[]				 = $urls[$i];
							continue;
						}
					}
				endfor;
				$urls = array_diff($urls, $img2del);
			endif; //enableimgfilter
			// ************ Featured image filters ***************** 
			$current_item['images']		 = $urls;
			$current_item['nofeatimg']	 = false;
			if(!empty($campaign['featimgfilters'])) :  // Si tiene algun filtro sino queda como está 
				trigger_error(__('Filtering Featured Image.', 'wpematico'), E_USER_NOTICE);
				$img2del = array();
				for($j = 0; $j < count($campaign['featimgfilters']['value']); $j++) :
					$allow		 = ($campaign['featimgfilters']['allow'][$j] == 'Allow') ? true : false;
					$woh		 = $campaign['featimgfilters']['woh'][$j];
					$mol		 = ($campaign['featimgfilters']['mol'][$j]) == 'more' ? '>=' : '<=';
					$if_value	 = $campaign['featimgfilters']['value'][$j];
					for($i = 0; $i < count($urls); $i++) {
						$imageurl	 = $urls[$i];
						$sizeimg	 = self :: getjpegsize($imageurl);  // lee solo header, solo para jpeg
						if($sizeimg == false)
							$sizeimg	 = getimagesize($imageurl);
						if($sizeimg == false) {
							trigger_error(__("Don't works filters with: ", 'wpematico') . '"' . $urls[$i] . '"', E_USER_NOTICE);
							$current_item['content'] = wpematico_campaign_fetch_functions::strip_Image_by_src($urls[$i], $current_item['content']);
							$img2del[]				 = $urls[$i];
							continue;
						}
						list($ancho, $alto) = $sizeimg;
						$imgvalue	 = ($woh == "width") ? $ancho : $alto;
						$imgfilter	 = $imgvalue . $mol . $if_value;
						$compute	 = create_function("", "return (" . $imgfilter . ");");
						// Si se cumple el filtro la pone primero en el array y sale
						trigger_error(__("Filter: ", 'wpematico') . '"' . $urls[$i] . '" ===> ' . $imgfilter, E_USER_NOTICE);
						if(!$compute()) {
							$current_item['nofeatimg'] = true;
							// continue;
						}else {
							$current_item['nofeatimg']	 = false;
							trigger_error(__("First featured image meets filters: ", 'wpematico') . '"' . $urls[$i] . '"', E_USER_NOTICE);
							$newfeat					 = $urls[$i];
							$urls						 = array_splice($urls, $i, 1); // la borra del lugar donde esta
							//$urls = array_splice($urls , 0, 0, $newfeat);
							array_unshift($urls, $newfeat);  // la pone primero
							break;  // sale del for
						}
					}
				endfor;
				$urls = array_diff($urls, $img2del);
			endif; //featimgfilters

			return $urls;
		}

		/**
		 * Static function find_audios
		 * Find audios from enclosures on feeds.
		 * @access public
		 * @return Array of current audios on post.
		 * @since 1.6.4
		 */
		public static function find_audios($current_item, $campaign, $item, $options_audios) {
			$cfg = get_option(self :: OPTION_KEY);

			$rssurls = array();
			if($options_audios['rss_audio']) {
				$execute_rss_audios = true;
				if(($options_audios['rss_audio_ifno']) && (sizeof($current_item['audios']) > 0 )) { // // la agrega si no hay audios en el contenido
					$execute_rss_audios = false;
				}

				if($options_audios['rss_audio_enclosure'] && $execute_rss_audios) {

					if($allenclosures = $item->get_enclosures()) {
						$audios_types	 = array('audio/mpeg', 'audio/ogg', 'audio/x-ms-wma', 'audio/x-wav', 'audio/mp4');
						$audios_types	 = apply_filters('wpematico_pro_allow_enclosures_audio_mine', $audios_types);
						foreach($allenclosures as $enclosure) {

							if($imgenc = $enclosure->get_link()) {
								$current_type = $enclosure->get_type();
								if(!empty($current_type)) {
									if(in_array($current_type, $audios_types) !== false) {
										$rssurls[] = $imgenc;
										trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $imgenc), E_USER_NOTICE);
									}
								}
							}
						}
					}
				}
			}
			$current_item['audios'] = array_merge($current_item['audios'], $rssurls);
			return $current_item;
		}

		/**
		 * Static function find_videos
		 * Find videos from enclosures on feeds.
		 * @access public
		 * @return Array of current videos on post.
		 * @since 1.6.4
		 */
		public static function find_videos($current_item, $campaign, $item, $options_videos) {
			$cfg	 = get_option(self :: OPTION_KEY);
			$rssurls = array();
			if($options_videos['rss_video']) {
				$execute_rss_videos = true;
				if(($options_videos['rss_video_ifno']) && (sizeof($current_item['videos']) > 0 )) { // // la agrega si no hay videos en el contenido
					$execute_rss_videos = false;
				}

				if($options_videos['rss_video_enclosure'] && $execute_rss_videos) {

					if($allenclosures = $item->get_enclosures()) {
						$videos_types	 = array('video/mp4', 'video/quicktime', 'video/x-ms-wmv', 'video/x-msvideo', 'video/mpeg', 'video/ogg', 'video/3gpp', 'video/3gpp', 'video/3gpp2');
						$videos_types	 = apply_filters('wpematico_pro_allow_enclosures_video_mine', $videos_types);
						foreach($allenclosures as $enclosure) {
							if($imgenc = $enclosure->get_link()) {

								$current_type = $enclosure->get_type();

								if(!empty($current_type)) {
									if(in_array($current_type, $videos_types) !== false) {
										$rssurls[] = $imgenc;
										trigger_error(sprintf(__('Getting enclosure link: %s', 'wpematico'), $imgenc), E_USER_NOTICE);
									}
								}
							}
						}
					}
				}
			}
			$current_item['videos'] = array_merge($current_item['videos'], $rssurls);
			return $current_item;
		}

		// Put Default image as featured if there is no images in content
		//$this->current_item['images'][0], $this->current_item
		public static function custom_img($attach_id, $post_id, $current_item, $campaign, $item) {
			if($attach_id != 0) {
				return $attach_id;
			}
			trigger_error(__('Inserting default Image Into Post.', 'wpematico'), E_USER_NOTICE);
			$attach_id = $campaign['default_img_id'];
			set_post_thumbnail($post_id, $attach_id);
			return $attach_id;
		}

		// Put in content 1st image link
		public static function img1s(&$current_item, &$campaign, &$item) {
			// $cfg = get_option( self :: OPTION_KEY);
			if($campaign['add1stimg']) {  // veo si tengo que agregar img primero en el content
				if(!empty($current_item['featured_image'])) {
					$imgstr					 = "<img class=\"wpe_imgrss\" src=\"" . $current_item['featured_image'] . "\">";  //Solo la imagen
					$imgstr					 .= $current_item['content'];
					$current_item['content'] = $imgstr;
				}
			}
			return $current_item['images'];
		}

		// Retrieve JPEG width and height without downloading/reading entire image.
		static private function getjpegsize($img_loc) {
			$handle		 = fopen($img_loc, "rb"); // or die("Invalid file stream.");
			if(!$handle)
				return FALSE;
			$new_block	 = NULL;
			if(!feof($handle)) {
				$new_block	 = fread($handle, 32);
				$i			 = 0;
				if($new_block[$i] == "\xFF" && $new_block[$i + 1] == "\xD8" && $new_block[$i + 2] == "\xFF" && $new_block[$i + 3] == "\xE0") {
					$i += 4;
					if($new_block[$i + 2] == "\x4A" && $new_block[$i + 3] == "\x46" && $new_block[$i + 4] == "\x49" && $new_block[$i + 5] == "\x46" && $new_block[$i + 6] == "\x00") {
						// Read block size and skip ahead to begin cycling through blocks in search of SOF marker
						$block_size	 = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
						$block_size	 = hexdec($block_size[1]);
						while (!feof($handle)) {
							$i			 += $block_size;
							$new_block	 .= fread($handle, $block_size);
							if($new_block[$i] == "\xFF") {
								// New block detected, check for SOF marker
								$sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
								if(in_array($new_block[$i + 1], $sof_marker)) {
									// SOF marker detected. Width and height information is contained in bytes 4-7 after this byte.
									$size_data	 = $new_block[$i + 2] . $new_block[$i + 3] . $new_block[$i + 4] . $new_block[$i + 5] . $new_block[$i + 6] . $new_block[$i + 7] . $new_block[$i + 8];
									$unpacked	 = unpack("H*", $size_data);
									$unpacked	 = $unpacked[1];
									$height		 = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
									$width		 = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
									return array($width, $height);
								}else {
									// Skip block marker and read block size
									$i			 += 2;
									$block_size	 = unpack("H*", $new_block[$i] . $new_block[$i + 1]);
									$block_size	 = hexdec($block_size[1]);
								}
							}else {
								return FALSE;
							}
						}
					}
				}
			}
			return FALSE;
		}

		/*		 * * checkea si existe el usuario
		  Si no existe lo crea con mail username@thisdomain y devuelve el ID	** */

		static private function checkauthor($wpusername) {
			$ID = username_exists($wpusername);
			if(!$ID) { //agrego usuario
				$wpuser	 = sanitize_user($wpusername);
				$ID		 = wp_insert_user(array('user_login' => $wpuser));
			}
			return $ID;
		}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++		
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++ // check required fields values before save post
		public static function Checkp($post_data, $err_message = "") {
			$inctit	 = @$post_data["campaign_kwordf_inc_tit"] == 1 ? true : false;
			$inccon	 = @$post_data["campaign_kwordf_inc_con"] == 1 ? true : false;
			$inccat	 = @$post_data["campaign_kwordf_inc_cat"] == 1 ? true : false;
			if(!$inctit && !$inccon && !$inccat && (!empty($post_data["campaign_kwordf_inc"]) || !empty($post_data["campaign_kwordf_incregex"]))) {
				$err_message = ($err_message != "") ? $err_message . "<br />" : "";
				$err_message .= sprintf(__('There\'s an error in Keyword include filter: You must check at least one search option.', 'wpematico'), '<br />') . ' ';
			}
			$exctit	 = @$post_data["campaign_kwordf_exc_tit"] == 1 ? true : false;
			$exccon	 = @$post_data["campaign_kwordf_exc_con"] == 1 ? true : false;
			$exccat	 = @$post_data["campaign_kwordf_exc_cat"] == 1 ? true : false;
			if(!$exctit && !$exccon && !$exccat && (!empty($post_data["campaign_kwordf_exc"]) || !empty($post_data["campaign_kwordf_excregex"]))) {
				$err_message = ($err_message != "") ? $err_message . "<br />" : "";
				$err_message .= sprintf(__('There\'s an error in Keyword exclude filter: You must check at least one search option.', 'wpematico'), '<br />') . ' ';
			}

			if(!empty($post_data["campaign_kwordf_incregex"]))
				if(false === @preg_match($post_data["campaign_kwordf_incregex"], '')) {
					$err_message = ($err_message != "") ? $err_message . "<br />" : "";
					$err_message .= sprintf(__('There\'s an error with the supplied RegEx expression in Keyword include filter: %s', 'wpematico'), '<br />' . $post_data["campaign_kwordf_incregex"]) . ' ';
				}

			if(!empty($post_data["campaign_kwordf_excregex"]))
				if(false === @preg_match($post_data["campaign_kwordf_excregex"], '')) {
					$err_message = ($err_message != "") ? $err_message . "<br />" : "";
					$err_message .= sprintf(__('There\'s an error with the supplied RegEx expression in Keyword exclude filter: %s', 'wpematico'), '<br />' . $post_data["campaign_kwordf_excregex"]) . ' ';
				}
			return $err_message;
		}

		/**
		 * 
		 * @param type $campaign	array current values
		 * @return type $campaign	array of fixed new and default values of campaign
		 */
		public static function pro_update_campaign($campaign) {  // agregado para que no borre las \ al grabar
			if(isset($campaign['campaign_kwordf']['excregex']) && !empty($campaign['campaign_kwordf']['excregex'])) {
				$campaign['campaign_kwordf']['excregex'] = addslashes($campaign['campaign_kwordf']['excregex']);
			}
			if(isset($campaign['campaign_kwordf']['incregex']) && !empty($campaign['campaign_kwordf']['incregex'])) {
				$campaign['campaign_kwordf']['incregex'] = addslashes($campaign['campaign_kwordf']['incregex']);
			}
			return $campaign;
		}

		/**
		 * Static function get_attachk_id_from_url
		 * @access public
		 * @return void
		 * @since version
		 */
		public static function get_attach_id_from_url($url) {
			$attachment_id	 = 0;
			$dir			 = wp_upload_dir();
			if(false !== strpos($url, $dir['baseurl'] . '/')) { // Is URL in uploads directory?
				$file		 = basename($url);
				$query_args	 = array(
					'post_type'		 => 'attachment',
					'post_status'	 => 'inherit',
					'fields'		 => 'ids',
					'meta_query'	 => array(
						array(
							'value'		 => $file,
							'compare'	 => 'LIKE',
							'key'		 => '_wp_attachment_metadata',
						),
					)
				);
				$query		 = new WP_Query($query_args);
				if($query->have_posts()) {
					foreach($query->posts as $post_id) {
						$meta				 = wp_get_attachment_metadata($post_id);
						$original_file		 = basename($meta['file']);
						$cropped_image_files = wp_list_pluck($meta['sizes'], 'file');
						if($original_file === $file || in_array($file, $cropped_image_files)) {
							$attachment_id = $post_id;
							break;
						}
					}
				}
			}
			return $attachment_id;
		}

		/**
		 * 
		 * @param type $campaign_data	array current values
		 * @param type $post_data		array of new values
		 * @return type $campaign_data	array of fixed new and default values of campaign
		 */
		public static function pro_check_campaigndata($campaign_data = array(), $post_data) {  // save no va mas, ahora chequea y agrega campos a campaign y graba en free
			$cfg = get_option(self :: OPTION_KEY);

			if(isset($cfg['enableimportfeed']) && $cfg['enableimportfeed'] && !empty($post_data["txt_feedlist"])) {  //importa feedlist
				$total	 = nl2br($post_data["txt_feedlist"]);
				$keyarr	 = explode("<br />", $total);
				foreach($keyarr as $key => $value) {
					$value = trim($value);
					if(!empty($value)) { // la linea sirve, agrego el feed 
						list($feed, $author) = explode(",", $value);
						$feed								 = trim($feed);
						$author								 = trim($author);
						if(!empty($feed))
							$campaign_data['campaign_feeds'][]	 = $feed; //agrego feed nuevo
						if(!empty($author)) {
							$campaign_data[$feed]['feed_author'] = self::checkauthor($author);
						}
					}
				}
			}

			// All feed attributes 
			$campaign_feeds = $campaign_data['campaign_feeds'];
			if(isset($cfg['enableauthorxfeed']) && @$cfg['enableauthorxfeed'] && empty($post_data["txt_feedlist"])) { //x si agrega listatxt
				foreach($campaign_feeds as $id => $feed):
					//$campaign_data[$feed]['feed_author'] = $post_data['feed_author'][$id];
					if(isset($post_data[$feed]['feed_author'])) {
						$campaign_data[$feed]['feed_author'] = (isset($post_data[$feed]['feed_author'])) ? $post_data[$feed]['feed_author'] : "-1";
					}else {
						$campaign_data[$feed]['feed_author'] = (isset($post_data['feed_author'])) ? $post_data['feed_author'][$id] : "-1";
					}

					if($campaign_data['campaign_type'] == 'xml') {
						if(!empty($campaign_data['campaign_xml_node']) && !empty($campaign_data['campaign_xml_node']['post_author'])) {
							$campaign_data[$feed]['feed_author'] = "0";
						}
					}
				endforeach;
			}


			if(empty($campaign_data['feed'])) {
				$campaign_data['feed'] = array();
			}

			if(empty($post_data['feed'])) {
				$post_data['feed'] = array();
			}
			$campaign_data['feed']['force_feed'] = ( isset($post_data['feed']['force_feed']) && !empty($post_data['feed']['force_feed']) ) ? $post_data['feed']['force_feed'] : Array();
			if(( isset($post_data['force_feed']) && !empty($post_data['force_feed']) ) && empty($campaign_data['feed']['force_feed'])) {
				$campaign_data['feed']['force_feed'] = $post_data['force_feed'];
			}
			$campaign_data['force_feed'] = array();

			$campaign_data['feed']['user_agent'] = ( isset($post_data['feed']['user_agent']) && !empty($post_data['feed']['user_agent']) ) ? $post_data['feed']['user_agent'] : Array();
			if(( isset($post_data['campaign_user_agent']) && !empty($post_data['campaign_user_agent']) ) && empty($campaign_data['feed']['user_agent'])) {
				$campaign_data['feed']['user_agent'] = $post_data['campaign_user_agent'];
			}
			$campaign_data['campaign_user_agent'] = array();


			$campaign_data['feed']['enable_cookies'] = ( isset($post_data['feed']['enable_cookies']) && !empty($post_data['feed']['enable_cookies']) ) ? $post_data['feed']['enable_cookies'] : Array();
			if(( isset($post_data['enable_cookies']) && !empty($post_data['enable_cookies']) ) && empty($campaign_data['feed']['enable_cookies'])) {
				$campaign_data['feed']['enable_cookies'] = $post_data['enable_cookies'];
			}
			$campaign_data['enable_cookies'] = array();

			$campaign_data['feed']['is_multipagefeed'] = ( isset($post_data['feed']['is_multipagefeed']) && !empty($post_data['feed']['is_multipagefeed']) ) ? $post_data['feed']['is_multipagefeed'] : Array();
			if(( isset($post_data['is_multipagefeed']) && !empty($post_data['is_multipagefeed']) ) && empty($campaign_data['feed']['is_multipagefeed'])) {
				$campaign_data['feed']['is_multipagefeed'] = $post_data['is_multipagefeed'];
			}
			$campaign_data['is_multipagefeed'] = array();


			$campaign_data['feed']['multifeed_maxpages'] = ( isset($post_data['feed']['multifeed_maxpages']) && !empty($post_data['feed']['multifeed_maxpages']) ) ? $post_data['feed']['multifeed_maxpages'] : Array();
			if(( isset($post_data['multifeed_maxpages']) && !empty($post_data['multifeed_maxpages']) ) && empty($campaign_data['feed']['multifeed_maxpages'])) {
				$campaign_data['feed']['multifeed_maxpages'] = $post_data['multifeed_maxpages'];
			}
			$campaign_data['multifeed_maxpages'] = array();

			$campaign_data['feed']['campaign_input_encoding'] = ( isset($post_data['feed']['campaign_input_encoding']) && !empty($post_data['feed']['campaign_input_encoding']) ) ? $post_data['feed']['campaign_input_encoding'] : Array();
			if(( isset($post_data['campaign_input_encoding']) && !empty($post_data['campaign_input_encoding']) ) && empty($campaign_data['feed']['campaign_input_encoding'])) {
				$campaign_data['feed']['campaign_input_encoding'] = $post_data['campaign_input_encoding'];
			}
			$campaign_data['campaign_input_encoding'] = array();




			//$campaign_data['campaign_input_encoding'] = ( isset($post_data['campaign_input_encoding']) && !empty($post_data['campaign_input_encoding']) ) ? $post_data['campaign_input_encoding'] : Array();
			//$campaign_data['multifeed_maxpages'] = ( isset($post_data['multifeed_maxpages']) && !empty($post_data['multifeed_maxpages']) ) ? $post_data['multifeed_maxpages'] : Array();
			//$campaign_data['is_multipagefeed']	 = ( isset($post_data['is_multipagefeed']) && !empty($post_data['is_multipagefeed']) ) ? $post_data['is_multipagefeed'] : Array();
			//$campaign_data['enable_cookies'] = ( isset($post_data['enable_cookies']) && !empty($post_data['enable_cookies']) ) ? $post_data['enable_cookies'] : Array();
			//$campaign_data['campaign_user_agent'] = ( isset($post_data['campaign_user_agent']) && !empty($post_data['campaign_user_agent']) ) ? $post_data['campaign_user_agent'] : Array();
			//$campaign_data['force_feed'] = ( isset($post_data['force_feed']) && !empty($post_data['force_feed']) ) ? $post_data['force_feed'] : Array();

			$campaign_data['feed']['feed_name'] = ( isset($post_data['feed']['feed_name']) && !empty($post_data['feed']['feed_name']) ) ? $post_data['feed']['feed_name'] : Array();


			#Proceso los Word count Filters
			$campaign_wcf = (isset($post_data['campaign_wcf']) && !empty($post_data['campaign_wcf']) ) ? $post_data['campaign_wcf'] : array();
			if(empty($campaign_wcf)) {
				$campaign_wcf['great_amount']	 = (isset($post_data['campaign_wcf_great_amount']) && !empty($post_data['campaign_wcf_great_amount']) ) ? $post_data['campaign_wcf_great_amount'] : 0;
				$campaign_wcf['great_words']	 = (!isset($post_data['campaign_wcf_great_words']) || empty($post_data['campaign_wcf_great_words'])) ? false : ($post_data['campaign_wcf_great_words'] == 1) ? true : false;
				$campaign_wcf['category']		 = (isset($post_data['campaign_wcf_category']) && !empty($post_data['campaign_wcf_category']) ) ? $post_data['campaign_wcf_category'] : "-1";
				$campaign_wcf['cut_amount']		 = (isset($post_data['campaign_wcf_cut_amount']) && !empty($post_data['campaign_wcf_cut_amount']) ) ? $post_data['campaign_wcf_cut_amount'] : 0;
				$campaign_wcf['cut_words']		 = (!isset($post_data['campaign_wcf_cut_words']) || empty($post_data['campaign_wcf_cut_words'])) ? false : ($post_data['campaign_wcf_cut_words'] == 1) ? true : false;
				$campaign_wcf['less_amount']	 = (isset($post_data['campaign_wcf_less_amount']) && !empty($post_data['campaign_wcf_less_amount']) ) ? $post_data['campaign_wcf_less_amount'] : 0;
				$campaign_wcf['less_words']		 = (!isset($post_data['campaign_wcf_less_words']) || empty($post_data['campaign_wcf_less_words'])) ? false : ($post_data['campaign_wcf_less_words'] == 1) ? true : false;
			}
			$campaign_data['campaign_wcf'] = $campaign_wcf;

			// *** Campaign Tags
			$campaign_data['campaign_autotags']		 = (!isset($post_data['campaign_autotags']) || empty($post_data['campaign_autotags'])) ? false : ($post_data['campaign_autotags'] == 1) ? true : false;
			$campaign_data['campaign_tags_feeds']	 = (!isset($post_data['campaign_tags_feeds']) || empty($post_data['campaign_tags_feeds'])) ? false : ($post_data['campaign_tags_feeds'] == 1) ? true : false;

			$campaign_data['campaign_nrotags']	 = (isset($post_data['campaign_nrotags']) && !empty($post_data['campaign_nrotags']) ) ? $post_data['campaign_nrotags'] : 10;
			$campaign_data['campaign_badtags']	 = (isset($post_data['campaign_badtags']) && !empty($post_data['campaign_badtags']) ) ? $post_data['campaign_badtags'] : '';

			// *** Campaign Options
			$campaign_data['fix_google_links'] = (!isset($post_data['fix_google_links']) || empty($post_data['fix_google_links'])) ? false : ($post_data['fix_google_links'] == 1) ? true : false;

			$campaign_data['add_no_follow'] = (!isset($post_data['add_no_follow']) || empty($post_data['add_no_follow'])) ? false : ($post_data['add_no_follow'] == 1) ? true : false;

			$campaign_data['campaign_date_tag'] = (!isset($post_data['campaign_date_tag']) || empty($post_data['campaign_date_tag'])) ? false : ($post_data['campaign_date_tag'] == 1) ? true : false;
			$campaign_data['campaign_date_tag_name']	 = (isset($post_data['campaign_date_tag_name']) && !empty($post_data['campaign_date_tag_name']) ) ? $post_data['campaign_date_tag_name'] : '';

			$campaign_data['campaign_striptagstitle']					 = (!isset($post_data['campaign_striptagstitle']) || empty($post_data['campaign_striptagstitle'])) ? false : ($post_data['campaign_striptagstitle'] == 1) ? true : false;
			$campaign_data['campaign_enablecustomtitle']				 = (!isset($post_data['campaign_enablecustomtitle']) || empty($post_data['campaign_enablecustomtitle'])) ? false : ($post_data['campaign_enablecustomtitle'] == 1) ? true : false;
			$campaign_data['campaign_customtitle']						 = (isset($post_data['campaign_customtitle']) && !empty($post_data['campaign_customtitle']) ) ? $post_data['campaign_customtitle'] : '';
			$campaign_data['campaign_custitdup']						 = (!isset($post_data['campaign_custitdup']) || empty($post_data['campaign_custitdup'])) ? false : ($post_data['campaign_custitdup'] == 1) ? true : false;
			$campaign_data['campaign_ctitlecont']						 = (!isset($post_data['campaign_enablecustomtitle']) || empty($post_data['campaign_enablecustomtitle'])) ? false : ($post_data['campaign_ctitlecont'] == 1) ? true : false;
			$campaign_data['campaign_ctdigits']							 = (isset($post_data['campaign_ctdigits']) && !empty($post_data['campaign_ctdigits']) ) ? $post_data['campaign_ctdigits'] : 6;
			$campaign_data['campaign_ctnextnumber']						 = (isset($post_data['campaign_ctnextnumber']) && !empty($post_data['campaign_ctnextnumber']) ) ? $post_data['campaign_ctnextnumber'] : 0;
			$campaign_data['campaign_delete_till_ontitle']				 = (!isset($post_data['campaign_delete_till_ontitle']) || empty($post_data['campaign_delete_till_ontitle'])) ? false : ($post_data['campaign_delete_till_ontitle'] == 1) ? true : false;
			$campaign_data['campaign_delete_till_ontitle_characters']	 = (!isset($post_data['campaign_delete_till_ontitle_characters']) || empty($post_data['campaign_delete_till_ontitle_characters'])) ? '. ? !' : $post_data['campaign_delete_till_ontitle_characters'];
			$campaign_data['campaign_delete_till_ontitle_keep']			 = (!isset($post_data['campaign_delete_till_ontitle_keep']) || empty($post_data['campaign_delete_till_ontitle_keep'])) ? false : ($post_data['campaign_delete_till_ontitle_keep'] == 1) ? true : false;
			$campaign_data['campaign_ontitle_cut_at']					 = (!isset($post_data['campaign_ontitle_cut_at']) || empty($post_data['campaign_ontitle_cut_at'])) ? 0 : $post_data['campaign_ontitle_cut_at'];
			$campaign_data['campaign_ontitle_cut_at_words']				 = (!isset($post_data['campaign_ontitle_cut_at_words']) || empty($post_data['campaign_ontitle_cut_at_words'])) ? false : ($post_data['campaign_ontitle_cut_at_words'] == 1) ? true : false;


			// *** Sobreescribo Autor por si lo crea en el momento o lo tiene el feed
			$campaign_data['campaign_author'] = (isset($post_data['campaign_author']) && !empty($post_data['campaign_author']) ) ? $post_data['campaign_author'] : '';

			// *** Campaign strip from phrase  
			$campaign_data['campaign_flip_paragraphs']		 = (!isset($post_data['campaign_flip_paragraphs']) || empty($post_data['campaign_flip_paragraphs'])) ? false : ($post_data['campaign_flip_paragraphs'] == 1) ? true : false;

			// *** Campaign strip from phrase  
			$campaign_data['campaign_delfphrase']			 = (isset($post_data['campaign_delfphrase'])) ? $post_data['campaign_delfphrase'] : null;
			$campaign_data['campaign_delfphrase_keep']		 = (!isset($post_data['campaign_delfphrase_keep']) || empty($post_data['campaign_delfphrase_keep'])) ? false : ($post_data['campaign_delfphrase_keep'] == 1) ? true : false;
			$campaign_data['campaign_delfphrase_end_line']	 = (!isset($post_data['campaign_delfphrase_end_line']) || empty($post_data['campaign_delfphrase_end_line'])) ? false : ($post_data['campaign_delfphrase_end_line'] == 1) ? true : false;

			// *** Campaign Last html Tag to delete  (backward compatible)
			$campaign_lastag = !empty($post_data['campaign_lastag']) ? $post_data['campaign_lastag'] : "";
			if(is_array($campaign_lastag)) {
				$campaign_lastag = !empty($post_data['campaign_lastag']['tag']) ? $post_data['campaign_lastag']['tag'] : "";
			}
			unset($campaign_data['campaign_lastag']['tag']);
			$campaign_data['campaign_lastag'] = $campaign_lastag;

			// *** Campaign custom_fields	
			// Proceso los custom fields sacando los que estan en blanco
			if(isset($post_data['campaign_cf_name'])) {
				foreach($post_data['campaign_cf_name'] as $id => $cf_value) {
					$cf_name	 = esc_attr($post_data['campaign_cf_name'][$id]);
					$cf_value	 = esc_attr($post_data['campaign_cf_value'][$id]);
					if(!empty($cf_name)) {
						if(!isset($campaign_cfields))
							$campaign_cfields			 = Array();
						$campaign_cfields['name'][]	 = $cf_name;
						$campaign_cfields['value'][] = $cf_value;
					}
				}
			}
			$cfields							 = (isset($post_data['campaign_cfields']) && !empty($post_data['campaign_cfields']) ) ? $post_data['campaign_cfields'] : array();
			$campaign_data['campaign_cfields']	 = (isset($campaign_cfields) && !empty($campaign_cfields) ) ? $campaign_cfields : $cfields;


			$campaign_data['campaign_cfeed_tags'] = (isset($post_data['campaign_cfeed_tags']) && !empty($post_data['campaign_cfeed_tags']) ) ? $post_data['campaign_cfeed_tags'] : array();

			// *** Campaign Image Filters
			// Proceso los filtros sacando los que los pixels estan en blanco
			if(isset($post_data['campaign_if_value'])) {
				foreach($post_data['campaign_if_value'] as $id => $if_value) {
					$allow	 = $post_data['campaign_if_allow'][$id];
					$woh	 = $post_data['campaign_if_woh'][$id];
					$mol	 = $post_data['campaign_if_mol'][$id];
					//$if_value = $post_data['campaign_if_value'][$id];
					if(!empty($if_value)) {
						if(!isset($imagefilters))
							$imagefilters			 = Array();
						$imagefilters['allow'][] = $allow;
						$imagefilters['woh'][]	 = $woh;
						$imagefilters['mol'][]	 = $mol;
						$imagefilters['value'][] = $if_value;
					}
				}
			}
			$imfilters						 = (isset($post_data['imagefilters']) && !empty($post_data['imagefilters']) ) ? $post_data['imagefilters'] : array();
			$campaign_data['imagefilters']	 = (isset($imagefilters) && !empty($imagefilters) ) ? $imagefilters : $imfilters;

			// *** Campaign Featured Image Filters
			// Proceso los filtros sacando los que los pixels estan en blanco
			if(isset($post_data['campaign_feat_value'])) {
				foreach($post_data['campaign_feat_value'] as $id => $if_value) {
					$allow	 = $post_data['campaign_feat_allow'][$id];
					$woh	 = $post_data['campaign_feat_woh'][$id];
					$mol	 = $post_data['campaign_feat_mol'][$id];
					//$if_value = $post_data['campaign_feat_value'][$id];
					if(!empty($if_value)) {
						if(!isset($featimgfilters))
							$featimgfilters				 = Array();
						$featimgfilters['allow'][]	 = $allow;
						$featimgfilters['woh'][]	 = $woh;
						$featimgfilters['mol'][]	 = $mol;
						$featimgfilters['value'][]	 = $if_value;
					}
				}
			}
			$imfilters						 = (isset($post_data['featimgfilters']) && !empty($post_data['featimgfilters']) ) ? $post_data['featimgfilters'] : array();
			$campaign_data['featimgfilters'] = (isset($featimgfilters) && !empty($featimgfilters) ) ? $featimgfilters : $imfilters;

			// *** Campaign Keyword Filtering contain and not contain  
			$campaign_kwordf					 = (isset($post_data['campaign_kwordf']) && !empty($post_data['campaign_kwordf']) ) ? $post_data['campaign_kwordf'] : array();
			//must include
			$inc								 = (isset($post_data['campaign_kwordf_inc'])) ? $post_data['campaign_kwordf_inc'] : null;
			$increg								 = (isset($post_data['campaign_kwordf_incregex'])) ? ($post_data['campaign_kwordf_incregex']) : null;
			$inctit								 = (!isset($post_data['campaign_kwordf_inc_tit']) || empty($post_data['campaign_kwordf_inc_tit'])) ? false : ($post_data["campaign_kwordf_inc_tit"] == 1) ? true : false;
			$inccon								 = (!isset($post_data['campaign_kwordf_inc_con']) || empty($post_data['campaign_kwordf_inc_con'])) ? false : ($post_data["campaign_kwordf_inc_con"] == 1) ? true : false;
			$inccat								 = (!isset($post_data['campaign_kwordf_inc_cat']) || empty($post_data['campaign_kwordf_inc_cat'])) ? false : ($post_data["campaign_kwordf_inc_cat"] == 1) ? true : false;
			$inc_anyall							 = (!isset($post_data['campaign_kwordf_inc_anyall']) || empty($post_data['campaign_kwordf_inc_anyall'])) ? 'anyword' : $post_data["campaign_kwordf_inc_anyall"];
			$campaign_kwordf['inc']				 = ( isset($campaign_kwordf['inc']) && !empty($campaign_kwordf['inc']) ) ? $campaign_kwordf['inc'] : $inc;
			$campaign_kwordf['incregex']		 = (isset($campaign_kwordf['incregex']) && !empty($campaign_kwordf['incregex']) ) ? $campaign_kwordf['incregex'] : $increg;
			$campaign_kwordf['inctit']			 = (isset($campaign_kwordf['inctit']) && !empty($campaign_kwordf['inctit']) ) ? $campaign_kwordf['inctit'] : $inctit;
			$campaign_kwordf['inccon']			 = (isset($campaign_kwordf['inccon']) && !empty($campaign_kwordf['inccon']) ) ? $campaign_kwordf['inccon'] : $inccon;
			$campaign_kwordf['inccat']			 = (isset($campaign_kwordf['inccat']) && !empty($campaign_kwordf['inccat']) ) ? $campaign_kwordf['inccat'] : $inccat;
			$campaign_kwordf['inc_anyall']		 = (isset($campaign_kwordf['inc_anyall']) && !empty($campaign_kwordf['inc_anyall']) ) ? $campaign_kwordf['inc_anyall'] : $inc_anyall;
			//must exclude
			$exc								 = (isset($post_data['campaign_kwordf_exc'])) ? $post_data['campaign_kwordf_exc'] : null;
			$excreg								 = (isset($post_data['campaign_kwordf_excregex'])) ? ($post_data['campaign_kwordf_excregex']) : null;
			$exctit								 = (!isset($post_data['campaign_kwordf_exc_tit']) || empty($post_data['campaign_kwordf_exc_tit'])) ? false : ($post_data["campaign_kwordf_exc_tit"] == 1) ? true : false;
			$exccon								 = (!isset($post_data['campaign_kwordf_exc_con']) || empty($post_data['campaign_kwordf_exc_con'])) ? false : ($post_data["campaign_kwordf_exc_con"] == 1) ? true : false;
			$exccat								 = (!isset($post_data['campaign_kwordf_exc_cat']) || empty($post_data['campaign_kwordf_exc_cat'])) ? false : ($post_data["campaign_kwordf_exc_cat"] == 1) ? true : false;
			$exc_anyall							 = (!isset($post_data['campaign_kwordf_exc_anyall']) || empty($post_data['campaign_kwordf_exc_anyall'])) ? 'anyword' : $post_data["campaign_kwordf_exc_anyall"];
			$campaign_kwordf['exc']				 = (isset($campaign_kwordf['exc']) && !empty($campaign_kwordf['exc']) ) ? $campaign_kwordf['exc'] : $exc;
			$campaign_kwordf['excregex']		 = (isset($campaign_kwordf['excregex']) && !empty($campaign_kwordf['excregex']) ) ? $campaign_kwordf['excregex'] : $excreg;
			$campaign_kwordf['exctit']			 = (isset($campaign_kwordf['exctit']) && !empty($campaign_kwordf['exctit']) ) ? $campaign_kwordf['exctit'] : $exctit;
			$campaign_kwordf['exccon']			 = (isset($campaign_kwordf['exccon']) && !empty($campaign_kwordf['exccon']) ) ? $campaign_kwordf['exccon'] : $exccon;
			$campaign_kwordf['exccat']			 = (isset($campaign_kwordf['exccat']) && !empty($campaign_kwordf['exccat']) ) ? $campaign_kwordf['exccat'] : $exccat;
			$campaign_kwordf['exc_anyall']		 = (isset($campaign_kwordf['exc_anyall']) && !empty($campaign_kwordf['exc_anyall']) ) ? $campaign_kwordf['exc_anyall'] : $exc_anyall;
			$campaign_data['campaign_kwordf']	 = $campaign_kwordf;

			$campaign_data['strip_all_images']	 = (!isset($post_data['strip_all_images']) || empty($post_data['strip_all_images'])) ? false : ($post_data['strip_all_images'] == 1) ? true : false;
			$campaign_data['overwrite_image']	 = (isset($post_data['overwrite_image']) && !empty($post_data['overwrite_image']) ) ? $post_data['overwrite_image'] : 'rename';
			$campaign_data['clean_images_urls']	 = (!isset($post_data['clean_images_urls']) || empty($post_data['clean_images_urls'])) ? false : ($post_data['clean_images_urls'] == 1) ? true : false;
			$campaign_data['image_src_gettype']	 = (!isset($post_data['image_src_gettype']) || empty($post_data['image_src_gettype'])) ? false : ($post_data['image_src_gettype'] == 1) ? true : false;

			$campaign_data['check_image_content']			 = (!isset($post_data['check_image_content']) || empty($post_data['check_image_content'])) ? false : ($post_data['check_image_content'] == 1) ? true : false;
			$campaign_data['strip_image_without_content']	 = (!isset($post_data['strip_image_without_content']) || empty($post_data['strip_image_without_content'])) ? false : ($post_data['strip_image_without_content'] == 1) ? true : false;

			$campaign_data['discardifnoimage']	 = (!isset($post_data['discardifnoimage']) || empty($post_data['discardifnoimage'])) ? false : ($post_data['discardifnoimage'] == 1) ? true : false;
			$campaign_data['campaign_rssimg']	 = (!isset($post_data['campaign_rssimg']) || empty($post_data['campaign_rssimg'])) ? false : ($post_data['campaign_rssimg'] == 1) ? true : false;
			$campaign_data['rssimg_enclosure']	 = (!isset($post_data['rssimg_enclosure']) || empty($post_data['rssimg_enclosure']) ) ? false : ($post_data['rssimg_enclosure']) ? true : false;
			$campaign_data['rssimg_ifno']		 = (!isset($post_data['rssimg_ifno']) || empty($post_data['rssimg_ifno']) ) ? false : ($post_data['rssimg_ifno']) ? true : false;
			$campaign_data['rssimg_add2img']	 = (!isset($post_data['rssimg_add2img']) || empty($post_data['rssimg_add2img']) ) ? false : ($post_data['rssimg_add2img']) ? true : false;
			$campaign_data['add1stimg']			 = (!isset($post_data['add1stimg']) || empty($post_data['add1stimg']) ) ? false : ($post_data['add1stimg']) ? true : false;
			$campaign_data['rssimg_featured']	 = (!isset($post_data['rssimg_featured']) || empty($post_data['rssimg_featured']) ) ? false : ($post_data['rssimg_featured']) ? true : false;
			$campaign_data['which_featured']	 = (!isset($post_data['which_featured'])) ? 'content1' : $post_data['which_featured'];

			$campaign_data['campaign_enableimgrename']	 = (!isset($post_data['campaign_enableimgrename']) || empty($post_data['campaign_enableimgrename']) ) ? false : ($post_data['campaign_enableimgrename']) ? true : false;
			$campaign_data['campaign_imgrename']		 = (isset($post_data['campaign_imgrename']) && !empty($post_data['campaign_imgrename']) ) ? $post_data['campaign_imgrename'] : '{slug}';

			$campaign_data['default_img']		 = (!isset($post_data['default_img']) || empty($post_data['default_img'])) ? false : ($post_data['default_img'] == 1) ? true : false;
			$campaign_data['default_img_url']	 = (isset($post_data['default_img_url']) && !empty($post_data['default_img_url']) ) ? $post_data['default_img_url'] : '';
			$campaign_data['default_img_link']	 = (isset($post_data['default_img_link']) && !empty($post_data['default_img_link']) ) ? $post_data['default_img_link'] : '';
			$campaign_data['default_img_title']	 = (isset($post_data['default_img_title']) && !empty($post_data['default_img_title']) ) ? $post_data['default_img_title'] : '';
			$campaign_data['default_img_id']	 = (isset($post_data['default_img_id']) && !empty($post_data['default_img_id']) ) ? $post_data['default_img_id'] : '0';
			if($campaign_data['default_img_id'] == 0 && !empty($campaign_data['default_img_link'])) {
				$campaign_data['default_img_id'] = self::get_attach_id_from_url($campaign_data['default_img_link']);
			}



			$campaign_data['activate_ramdom_rewrite']	 = (!isset($post_data['activate_ramdom_rewrite']) || empty($post_data['activate_ramdom_rewrite'])) ? false : ($post_data['activate_ramdom_rewrite'] == 1) ? true : false;
			$campaign_data['ramdom_rewrite_count']		 = (!isset($post_data['ramdom_rewrite_count']) || empty($post_data['ramdom_rewrite_count'])) ? '10' : $post_data['ramdom_rewrite_count'];

			$campaign_data['words_to_rewrites'] = (!isset($post_data['words_to_rewrites']) || empty($post_data['words_to_rewrites'])) ? '' : $post_data['words_to_rewrites'];


			$campaign_data['overwrite_audio']		 = (isset($post_data['overwrite_audio']) && !empty($post_data['overwrite_audio']) ) ? $post_data['overwrite_audio'] : 'rename';
			$campaign_data['clean_audios_urls']		 = (!isset($post_data['clean_audios_urls']) || empty($post_data['clean_audios_urls'])) ? false : ($post_data['clean_audios_urls'] == 1) ? true : false;
			$campaign_data['rss_audio']				 = (!isset($post_data['rss_audio']) || empty($post_data['rss_audio'])) ? false : ($post_data['rss_audio'] == 1) ? true : false;
			$campaign_data['rss_audio_enclosure']	 = (!isset($post_data['rss_audio_enclosure']) || empty($post_data['rss_audio_enclosure']) ) ? false : ($post_data['rss_audio_enclosure']) ? true : false;
			$campaign_data['rss_audio_ifno']		 = (!isset($post_data['rss_audio_ifno']) || empty($post_data['rss_audio_ifno']) ) ? false : ($post_data['rss_audio_ifno']) ? true : false;
			$campaign_data['strip_all_audios']		 = (!isset($post_data['strip_all_audios']) || empty($post_data['strip_all_audios'])) ? false : ($post_data['strip_all_audios'] == 1) ? true : false;

			$campaign_data['enable_audio_rename']	 = (!isset($post_data['enable_audio_rename']) || empty($post_data['enable_audio_rename']) ) ? false : ($post_data['enable_audio_rename']) ? true : false;
			$campaign_data['audio_rename']			 = (isset($post_data['audio_rename']) && !empty($post_data['audio_rename']) ) ? $post_data['audio_rename'] : '{slug}';

			$campaign_data['audio_upload_ranges']	 = (!isset($post_data['audio_upload_ranges']) || empty($post_data['audio_upload_ranges']) ) ? false : ($post_data['audio_upload_ranges']) ? true : false;
			$campaign_data['audio_upload_range_mb']	 = (isset($post_data['audio_upload_range_mb']) && !empty($post_data['audio_upload_range_mb']) ) ? $post_data['audio_upload_range_mb'] : '5';


			$campaign_data['audio_decode_html_ent_url']	 = (!isset($post_data['audio_decode_html_ent_url']) || empty($post_data['audio_decode_html_ent_url']) ) ? false : ($post_data['audio_decode_html_ent_url']) ? true : false;
			$campaign_data['audio_follow_redirection']	 = (!isset($post_data['audio_follow_redirection']) || empty($post_data['audio_follow_redirection']) ) ? false : ($post_data['audio_follow_redirection']) ? true : false;



			$campaign_data['overwrite_video']		 = (isset($post_data['overwrite_video']) && !empty($post_data['overwrite_video']) ) ? $post_data['overwrite_video'] : 'rename';
			$campaign_data['clean_videos_urls']		 = (!isset($post_data['clean_videos_urls']) || empty($post_data['clean_videos_urls'])) ? false : ($post_data['clean_videos_urls'] == 1) ? true : false;
			$campaign_data['rss_video']				 = (!isset($post_data['rss_video']) || empty($post_data['rss_video'])) ? false : ($post_data['rss_video'] == 1) ? true : false;
			$campaign_data['rss_video_enclosure']	 = (!isset($post_data['rss_video_enclosure']) || empty($post_data['rss_video_enclosure']) ) ? false : ($post_data['rss_video_enclosure']) ? true : false;
			$campaign_data['rss_video_ifno']		 = (!isset($post_data['rss_video_ifno']) || empty($post_data['rss_video_ifno']) ) ? false : ($post_data['rss_video_ifno']) ? true : false;
			$campaign_data['strip_all_videos']		 = (!isset($post_data['strip_all_videos']) || empty($post_data['strip_all_videos'])) ? false : ($post_data['strip_all_videos'] == 1) ? true : false;

			$campaign_data['enable_video_rename']	 = (!isset($post_data['enable_video_rename']) || empty($post_data['enable_video_rename']) ) ? false : ($post_data['enable_video_rename']) ? true : false;
			$campaign_data['video_rename']			 = (isset($post_data['video_rename']) && !empty($post_data['video_rename']) ) ? $post_data['video_rename'] : '{slug}';

			$campaign_data['video_upload_ranges']	 = (!isset($post_data['video_upload_ranges']) || empty($post_data['video_upload_ranges']) ) ? false : ($post_data['video_upload_ranges']) ? true : false;
			$campaign_data['video_upload_range_mb']	 = (isset($post_data['video_upload_range_mb']) && !empty($post_data['video_upload_range_mb']) ) ? $post_data['video_upload_range_mb'] : '5';

			$campaign_data['video_decode_html_ent_url']	 = (!isset($post_data['video_decode_html_ent_url']) || empty($post_data['video_decode_html_ent_url']) ) ? false : ($post_data['video_decode_html_ent_url']) ? true : false;
			$campaign_data['video_follow_redirection']	 = (!isset($post_data['video_follow_redirection']) || empty($post_data['video_follow_redirection']) ) ? false : ($post_data['video_follow_redirection']) ? true : false;



			$campaign_data['campaign_parent_page'] = (isset($post_data['campaign_parent_page']) && !empty($post_data['campaign_parent_page']) ) ? $post_data['campaign_parent_page'] : '0';

			/**
			 * @since 1.8.2
			 */
			$campaign_data['campaign_fauthor_inc_words'] = (isset($post_data['campaign_fauthor_inc_words']) && !empty($post_data['campaign_fauthor_inc_words']) ) ? $post_data['campaign_fauthor_inc_words'] : '';
			$campaign_data['campaign_fauthor_inc_regex'] = (isset($post_data['campaign_fauthor_inc_regex']) && !empty($post_data['campaign_fauthor_inc_regex']) ) ? $post_data['campaign_fauthor_inc_regex'] : '';
			$campaign_data['campaign_fauthor_exc_words'] = (isset($post_data['campaign_fauthor_exc_words']) && !empty($post_data['campaign_fauthor_exc_words']) ) ? $post_data['campaign_fauthor_exc_words'] : '';
			$campaign_data['campaign_fauthor_exc_regex'] = (isset($post_data['campaign_fauthor_exc_regex']) && !empty($post_data['campaign_fauthor_exc_regex']) ) ? $post_data['campaign_fauthor_exc_regex'] : '';


			$campaign_word2tax = array();
			if(isset($post_data['campaign_word2tax']['word'])) {

				foreach($post_data['campaign_word2tax']['word'] as $id => $value) {

					$word	 = ($post_data['campaign_word2tax']['word'][$id]);
					$title	 = (isset($post_data['campaign_word2tax']['title'][$id]) && $post_data['campaign_word2tax']['title'][$id] == 1) ? true : false;
					$regex	 = (isset($post_data['campaign_word2tax']['regex'][$id]) && $post_data['campaign_word2tax']['regex'][$id] == 1) ? true : false;
					$cases	 = (isset($post_data['campaign_word2tax']['cases'][$id]) && $post_data['campaign_word2tax']['cases'][$id] == 1) ? true : false;
					$tax	 = (isset($post_data['campaign_word2tax']['tax'][$id]) && !empty($post_data['campaign_word2tax']['tax'][$id]) ) ? $post_data['campaign_word2tax']['tax'][$id] : '';
					$term	 = (isset($post_data['campaign_word2tax']['term'][$id]) && !empty($post_data['campaign_word2tax']['term'][$id]) ) ? $post_data['campaign_word2tax']['term'][$id] : '';
					if(!empty($word)) {
						$campaign_word2tax['word'][]	 = ($regex) ? $word : htmlspecialchars($word);
						$campaign_word2tax['title'][]	 = $title;
						$campaign_word2tax['regex'][]	 = $regex;
						$campaign_word2tax['cases'][]	 = $cases;
						$campaign_word2tax['tax'][]		 = $tax;
						$campaign_word2tax['term'][]	 = $term;
					}
				}
			}

			$campaign_data['campaign_word2tax']				 = (!empty($campaign_word2tax) ) ? (array) $campaign_word2tax : array();
			$campaign_data['campaign_no_setting_word2tax']	 = (!isset($post_data['campaign_no_setting_word2tax']) || empty($post_data['campaign_no_setting_word2tax']) ) ? false : ($post_data['campaign_no_setting_word2tax']) ? true : false;

			$campaign_data['xml_categories_separated_commas']	 = (!isset($post_data['xml_categories_separated_commas']) || empty($post_data['xml_categories_separated_commas']) ) ? false : ($post_data['xml_categories_separated_commas']) ? true : false;
			$campaign_data['xml_tags_separated_commas']			 = (!isset($post_data['xml_tags_separated_commas']) || empty($post_data['xml_tags_separated_commas']) ) ? false : ($post_data['xml_tags_separated_commas']) ? true : false;

			// **** Return campaign_data
			return $campaign_data;
		}

	}

}

