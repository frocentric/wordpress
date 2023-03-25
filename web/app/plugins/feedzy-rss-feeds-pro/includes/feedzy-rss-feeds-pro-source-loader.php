<?php
/**
 * Register source loader.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.7.1
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
require_once FEEDZY_PRO_ABSPATH . '/includes/source-loader/feedzy-rss-item-enclosures.php';
require_once FEEDZY_PRO_ABSPATH . '/includes/source-loader/feedzy-rss-get-item.php';

/**
 * Register source loader.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
class Feedzy_Rss_Feeds_Pro_Source_Loader {

	/**
	 * Get item source.
	 *
	 * @param object $item Item data.
	 */
	public function get_source( $item ) {
		return new Feedzy_Rss_Get_Item( $item );
	}
}
