<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */
// @codingStandardsIgnoreStart
class Feedzy_Rss_Feeds_Pro_i18n {
// @codingStandardsIgnoreEnd
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'feedzy-rss-feeds',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
