<?php
/**
 * Fired during plugin deactivation
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro_Deactivator {
	/**
	 * Called on plugin deactivation
	 *
	 * Add Logic needed on deactivation here
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'feedzy_cron' );
		delete_option( 'feedzy-pro-activated' );
	}
}
