<?php
/**
 * The UI functionality of the plugin. The extended methods for PRO.
 *
 * @link       http://themeisle.com
 * @since      3.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

/**
 * The UI functionality of the plugin. The extended methods for PRO.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 * @author     Themeisle <friends@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro_Ui {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param      string $plugin_name    The name of this plugin.
	 * @param      string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Load plugin translation for - TinyMCE API
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return  array
	 */
	public function feedzy_add_tinymce_lang() {
		$feedzy_rss_feeds_ui_lang = FEEDZY_PRO_ABSPATH . '/includes/admin/feedzy-rss-feeds-pro-ui-lang.php';
		return $feedzy_rss_feeds_ui_lang;
	}

	/**
	 * Set Form elements for popup tinyMCE window.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @param   array $elements     The array with the form elements.
	 * @return  mixed
	 */
	public function get_form_elements_pro( $elements ) {
		$elements['section_pro']['title']                                 = __( 'Other Options', 'feedzy-rss-feeds' );
		$elements['section_pro']['description']                           = __( 'Need help? Check out our documentation.', 'feedzy-rss-feeds' ) . '<br/>' . '<a href="http://docs.themeisle.com/article/277-feedzy-rss-feeds-hooks" target="_blank"><small>' . __( 'Docs FEEDZY RSS Feeds', 'feedzy-rss-feeds' ) . '</small></a>';
		$elements['section_pro']['elements']['price']['disabled']         = false;
		$elements['section_pro']['elements']['price']['disabled']         = false;
		$elements['section_pro']['elements']['referral_url']['disabled']  = false;
		$elements['section_pro']['elements']['columns']['disabled']       = false;
		$elements['section_pro']['elements']['mapping']['disabled']       = false;
		$elements['section_pro']['elements']['template']['disabled']      = false;
		$elements['section_item']['elements']['keywords_ban']['disabled'] = false;
		$elements['section_item']['elements']['keywords_ban']['disabled'] = false;
		return $elements;
	}
}
