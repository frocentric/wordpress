<?php
/**
 * The Language function file for tinyMCE. Feedzy PRO translations.
 *
 * @link       http://themeisle.com
 * @since      3.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */
/**
 *
 * SECURITY : Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not allowed!' );
}


/**
 *
 * Translation for TinyMCE
 */

if ( ! class_exists( '_WP_Editors' ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}

/**
 * Class Feedzy_Rss_Feeds_Pro_Ui_Lang
 */
class Feedzy_Rss_Feeds_Pro_Ui_Lang extends Feedzy_Rss_Feeds_Ui_Lang {

	/**
	 * The strings for translation.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $strings    The ID of this plugin.
	 */
	protected $strings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct() {
		parent::__construct();
		$pro_strings                 = $this->strings;
		$pro_strings['plugin_label'] = __( 'Feedzy RSS Feeds', 'feedzy-rss-feeds' );
		$pro_strings['plugin_title'] = __( 'Insert FEEDZY RSS Feeds Shortcode', 'feedzy-rss-feeds' );
		$pro_strings['pro_button']   = __( 'Read FEEDZY RSS Feeds Docs', 'feedzy-rss-feeds' );
		$pro_strings['pro_url']      = 'http://docs.themeisle.com/article/277-feedzy-rss-feeds-hooks';
		$this->strings               = $pro_strings;
	}

	/**
	 *
	 * The method that returns the translation array
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return  string
	 */
	public function feedzy_tinymce_translation() {

		$locale     = _WP_Editors::$mce_locale;
		$translated = 'tinyMCE.addI18n("' . $locale . '.feedzy_tinymce_plugin", ' . wp_json_encode( $this->strings ) . ");\n";

		return $translated;
	}

}

$feedzyProLangClass = new Feedzy_Rss_Feeds_Pro_Ui_Lang();
$strings            = $feedzyProLangClass->feedzy_tinymce_translation();
