<?php
/*
  Plugin Name: WPeMatico Full Content
  Plugin URI: https://etruel.com/downloads/wpematico-full-content/
  Description: Add On for WPeMatico plugin. Add Full Content Parser and editor of config files to get full content from almost all domains.
  Version: 2.0
  Author: etruel
  Author URI: https://www.netmdp.com
  License: GPLv2
 */

if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!defined('WPEFULLCONTENT_VERSION')) {
	define('WPEFULLCONTENT_VERSION', '2.0');
}
if(!defined('WPEFULLCONTENT_REQ_WPEMATICO')) {
	define('WPEFULLCONTENT_REQ_WPEMATICO', '2.5');
}

if(!class_exists('WPeMatico_FullContent')) :

	/**
	 * Main WPeMatico_FullContent class
	 *
	 * @since       1.7.0
	 */
	class WPeMatico_FullContent {

		/**
		 * @var         WPeMatico_FullContent $init Bool  if the class was started
		 * @since       1.7.0
		 */
		private static $init = false;

		/**
		 * Get active init
		 *
		 * @access      public
		 * @since       1.7.0
		 * @return      void
		 */
		public static function init() {
			if(!self::$init) {
				self::setup_constants();
				self::includes();
				self::load_textdomain();
				self::hooks();
			}
			self::$init = true;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access      public
		 * @since       1.7.0
		 * @return      void
		 */
		public static function setup_constants() {

			// Plugin root file
			if(!defined('WPEFULLCONTENT_ROOT_FILE')) {
				define('WPEFULLCONTENT_ROOT_FILE', __FILE__);
			}
			// Plugin URL
			if(!defined('WPEFULLCONTENT_URL')) {
				define('WPEFULLCONTENT_URL', plugin_dir_url(__FILE__));
			}
			if(!defined('WPEFULLCONTENT_PATH')) {
				define('WPEFULLCONTENT_PATH', plugin_dir_path(__FILE__));
			}
			if(!defined('WPEFULLCONTENT_STORE_URL')) {
				define('WPEFULLCONTENT_STORE_URL', 'https://etruel.com');
			}
			if(!defined('WPEFULLCONTENT_ITEM_NAME')) {
				define('WPEFULLCONTENT_ITEM_NAME', 'WPeMatico Full Content');
			}
			if(!defined('WPEFULLCONTENT_TEXTDOMAIN')) {
				define('WPEFULLCONTENT_TEXTDOMAIN', 'WPeMatico_fullcontent');
			}
		}

		/**
		 * Include necessary files
		 *
		 * @access      public
		 * @since       1.7.0
		 * @return      void
		 */
		public static function includes() {
			// Include scripts
			require_once(WPEFULLCONTENT_PATH . 'inc/functions.php' );
			require_once(WPEFULLCONTENT_PATH . 'inc/campaign_edit.php' );
			require_once(WPEFULLCONTENT_PATH . 'inc/getcontent.php' );
			require_once(WPEFULLCONTENT_PATH . 'inc/settings.php' );
			require_once(WPEFULLCONTENT_PATH . 'inc/fulljs.php' );
		}

		/**
		 * Run action and filter hooks
		 *
		 * @access      public
		 * @since       1.7.0
		 * @return      void
		 *
		 */
		public static function hooks() {
			// Register settings
			add_filter('wpematico_settings_extensions', array(__CLASS__, 'settings'), 1);
			add_filter('wpematico_plugins_updater_args', array(__CLASS__, 'add_updater'), 10, 1);
			add_action('admin_init', array(__CLASS__, 'admin_init'));
		}

		public static function add_updater($args) {
			if(empty($args['fullcontent'])) {
				$args['fullcontent']				 = array();
				$args['fullcontent']['api_url']		 = WPEFULLCONTENT_STORE_URL;
				$args['fullcontent']['plugin_file']	 = WPEFULLCONTENT_ROOT_FILE;
				$args['fullcontent']['api_data']	 = array(
					'version'	 => WPEFULLCONTENT_VERSION, // current version number
					'item_name'	 => WPEFULLCONTENT_ITEM_NAME, // name of this plugin
					'author'	 => 'Esteban Truelsegaard'  // author of this plugin
				);
			}

			return $args;
		}

		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.7.0
		 * @return      void
		 */
		public static function load_textdomain() {
			// Set filter for language directory
			$lang_dir	 = WPEFULLCONTENT_PATH . '/languages/';
			$lang_dir	 = apply_filters('boilerplate_languages_directory', $lang_dir);

			// Traditional WordPress plugin locale filter
			$locale	 = apply_filters('plugin_locale', get_locale(), 'WPeMatico_fullcontent');
			$mofile	 = sprintf('%1$s-%2$s.mo', 'WPeMatico_fullcontent', $locale);

			// Setup paths to current locale file
			$mofile_local	 = $lang_dir . $mofile;
			$mofile_global	 = WP_LANG_DIR . '/WPeMatico_fullcontent/' . $mofile;

			if(file_exists($mofile_global)) {
				// Look in global /wp-content/languages/boilerplate/ folder
				load_textdomain('WPeMatico_fullcontent', $mofile_global);
			}elseif(file_exists($mofile_local)) {
				// Look in local /wp-content/plugins/boilerplate/languages/ folder
				load_textdomain('WPeMatico_fullcontent', $mofile_local);
			}else {
				// Load the default language files
				load_plugin_textdomain('WPeMatico_fullcontent', false, $lang_dir);
			}
		}

		/**
		 * admin_init
		 * @access      public
		 * @since       1.7.0
		 */
		public static function admin_init() {
			//Additional links on the plugin page
			add_filter('plugin_row_meta', array(__CLASS__, 'init_row_meta'), 10, 2);
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'init_action_links'));
		}

		/**
		 * Actions-Links del Plugin
		 * @access      public
		 * @param   array   $data  Original Links
		 * @return  array   $data  modified Links
		 * @since       1.7.0
		 */
		public static function init_action_links($data) {
			if(!current_user_can('manage_options')) {
				return $data;
			}
			return array_merge(
				$data,
				array(
					'<a href="' . admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=fullcontent') . '" title="' . __('Go to Full Content Settings Page') . '">' . __('Settings') . '</a>',
				)
			);
		}

		/**
		 * Meta-Links del Plugin
		 * @access      public
		 * @param   array   $data  Original Links
		 * @param   string  $page  plugin actual
		 * @return  array   $data  modified Links
		 * @since       1.7.0
		 */
		public static function init_row_meta($data, $page) {
			if(basename($page) != basename(__FILE__)) {
				return $data;
			}
			return array_merge(
				$data,
				array(
					'<a href="https://etruel.com/" target="_blank">' . __('etruel Store') . '</a>',
					'<a href="https://etruel.com/my-account/support/" target="_blank">' . __('Support') . '</a>',
					'<a href="https://wordpress.org/support/view/plugin-reviews/wpematico?filter=5&rate=5#postform" target="_Blank" title="Rate 5 stars on Wordpress.org">' . __('Rate Plugin') . '</a>'
				)
			);
		}

	}

	endif;


if(!function_exists('WPeMatico_fullcontent_checkPrerequisites')) :

	function WPeMatico_fullcontent_checkPrerequisites() {

		WPeMatico_FullContent::setup_constants();

		// need at least PHP 5.2.11 for libxml_disable_entity_loader()
		$message = '';

		if(!function_exists('get_plugins')) {
			require_once( ABSPATH . basename(admin_url()) . '/includes/plugin.php' );
		}

		if(!is_plugin_active('wpematico/wpematico.php')) {
			$message .= __('You are using WPeMatico Full Content.', 'WPeMatico_fullcontent') . ' ';
			$message .= __('Plugins <b>WPeMatico</b> must be activated!', 'WPeMatico_fullcontent');
			$message .= ' <a href="' . admin_url('plugins.php') . '#wpematico"> ' . __('Go to Activate Now', 'WPeMatico_fullcontent') . '</a>';
			$message .= '<script type="text/javascript">jQuery(document).ready(function($){$("#wpematico").css("backgroundColor","yellow");});</script>';
			$checks	 = false;
		}else {  //WPeMatico is active
			if(!class_exists('WPeMatico')) {
				$message .= __('You are using WPeMatico Full Content, but doesn\'t exist class WPeMatico.', 'WPeMatico_fullcontent');
				$message .= __('Something is going wrong. Contact etruel.', 'WPeMatico_fullcontent');
				$checks	 = false;
			}
			if(version_compare(WPEMATICO_VERSION, WPEFULLCONTENT_REQ_WPEMATICO, '<')) {
				$message = '<p>' . WPEFULLCONTENT_ITEM_NAME . ' requires WPeMatico ' . esc_html(WPEFULLCONTENT_REQ_WPEMATICO) . ' or higher; your website has WPeMatico ' . esc_html(WPEMATICO_VERSION) .
					' which is old, obsolete, and unsupported.</p>
					<p>Please upgrade your WPeMatico Plugin from the Wordpress Plugins page.</p>';
			}
		}

		if(!empty($message)) {
			add_action('admin_notices', function() use ($message) {
				echo '<div id="message" class="error fade">' . $message . '</div>';
			});
			return false;
		}

		return true;
	}

endif;

if(!function_exists('WPeMatico_fullcontent_load')) :

	function WPeMatico_fullcontent_load() {
		if(!WPeMatico_fullcontent_checkPrerequisites()) {
			// When the requirements are not met
		}else {
			return WPeMatico_FullContent::init();
		}
	}

endif;

add_action('plugins_loaded', 'WPeMatico_fullcontent_load', 999);

