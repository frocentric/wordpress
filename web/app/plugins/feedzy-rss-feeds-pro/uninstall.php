<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       http://themeisle.com
 * @since      3.0.0
 *
 * @package    Feedzy_Rss_Feeds
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


// clean up after ourselves, that's a good plugin!
delete_option( 'feedzy_rss_feeds_pro_license_plan' );
delete_option( 'feedzy-rss-feeds-settings' );

global $wpdb;

// delete CPTs.
foreach ( array( 'feedzy_categories', 'feedzy_imports' ) as $type ) {
	// @codingStandardsIgnoreStart
	// phpcs:ignore
	$wpdb->query( "DELETE pm.*, p.* FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.id WHERE p.post_type = '{$type}'" );
	// @codingStandardsIgnoreEnd
}
