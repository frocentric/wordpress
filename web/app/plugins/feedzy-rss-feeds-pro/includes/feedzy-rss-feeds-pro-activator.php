<?php
/**
 * Fired during plugin activation
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro_Activator {
	/**
	 * Called on plugin activation.
	 *
	 * Add Logic needed on activation here
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function activate() {
		add_option( 'feedzy-pro-activated', true );
	}
}
