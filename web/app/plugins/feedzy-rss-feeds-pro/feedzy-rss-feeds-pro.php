<?php
/**
 * The FEEDZY RSS Feeds bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://themeisle.com/plugins/feedzy-rss-feeds/
 * @since             1.0.0
 * @package feedzy-rss-feeds-pro
 *
 * @wordpress-plugin
 * Plugin Name:     Feedzy RSS Feeds Premium
 * Plugin URI:      http://themeisle.com/plugins/feedzy-rss-feeds/
 * Description:     FEEDZY RSS Feeds Premium extends the functionality of FEEDZY RSS Feeds.
 * Version:         2.4.5
 * Author:          Themeisle
 * Author URI:      https://themeisle.com
 * Text Domain:     feedzy-rss-feeds
 * Domain Path:     /languages
 * WordPress Available:  no
 * Requires License:    yes
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-feedzy-rss-feed-pro-activator.php
 *
 * @since    1.0.0
 */
function activate_feedzy_rss_feeds_pro() {
	Feedzy_Rss_Feeds_Pro_Activator::activate();
}

/**
 * The code that rsaduns during plugin deactivation.
 * This action is documented in includes/class-feedzy-rss-feed-pro-deactivator.php
 *
 * @since    1.0.0
 */
function deactivate_feedzy_rss_feeds_pro() {
	Feedzy_Rss_Feeds_Pro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_feedzy_rss_feeds_pro' );
register_deactivation_hook( __FILE__, 'deactivate_feedzy_rss_feeds_pro' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specificas dsa hooks, and public-facing site hooks.
 *
 * @since    1.0.0
 */
function feedzy_rss_feeds_pro_autoload( $class ) {
	$namespaces = array( 'Feedzy_Rss_Feeds_Pro' );
	foreach ( $namespaces as $namespace ) {
		if ( substr( $class, 0, strlen( $namespace ) ) === $namespace ) {
			$filename = plugin_dir_path( __FILE__ ) . 'includes/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/abstract/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/admin/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/public/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/admin/services/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
			$filename = plugin_dir_path( __FILE__ ) . 'includes/elementor/' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
		}
	}

	return false;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking offsadas the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_feedzy_rss_feeds_pro() {
	define( 'FEEDZY_PRO_BASEFILE', __FILE__ );
	define( 'FEEDZY_PRO_ABSURL', plugins_url( '/', __FILE__ ) );
	define( 'FEEDZY_PRO_BASE', plugin_basename( __FILE__ ) );
	define( 'FEEDZY_PRO_ABSPATH', dirname( __FILE__ ) );
	define( 'FEEDZY_PRO_DIRNAME', basename( FEEDZY_PRO_ABSPATH ) );
	define( 'FEEDZY_ROOT_API', 'https://feedzy.themeisle.com' );
	define( 'FEEDZY_PRO_FULL_CONTENT_URL', FEEDZY_ROOT_API . '/api/feedzyfp/v0/rss/' );
	define( 'FEEDZY_PRO_FETCH_ITEM_IMG_URL', FEEDZY_ROOT_API . '/api/feedzyfp/v1/item/image/' );
	define( 'FEEDZY_PRO_REWRITE_CONTENT_API', FEEDZY_ROOT_API . '/api/feedzyfp/v1/item/content-writers/' );
	define( 'FEEDZY_PRO_AUTO_TRANSLATE_CONTENT', FEEDZY_ROOT_API . '/api/feedzyfp/v1/item/auto-translate/' );
	define( 'FEEDZY_PRO_AWS_PRODUCT_API', FEEDZY_ROOT_API . '/api/feedzyfp/v1/aws-product-advertising' );
	define( 'FEEDZY_PRO_OPENAI_API', FEEDZY_ROOT_API . '/api/feedzyfp/v1/open-ai' );
	define( 'FEEDZY_PRO_OPENAI_SUMMARIZE_API', FEEDZY_PRO_OPENAI_API . '/summarize' );
	define( 'FEEDZY_PRO_OPENAI_GENERATE_IMG_API', FEEDZY_PRO_OPENAI_API . '/image' );
	define( 'FEEDZY_PRO_VERSION', '2.4.5' );
	// this hook will indicate to free that pro is aware of import feeds being shifted to free.
	// avoids doing this by comparing versions.
	add_filter( 'feedzy_free_has_import', '__return_true' );
	$vendor_file = FEEDZY_PRO_ABSPATH . '/vendor/autoload.php';
	if ( is_readable( $vendor_file ) ) {
		include_once $vendor_file;
	}
	$plugin = new Feedzy_Rss_Feeds_Pro();
	$plugin->run();

	add_filter( 'themeisle_sdk_products', 'feedzy_pro_register_sdk', 10, 1 );
	add_filter(
		'themeisle_sdk_compatibilities/' . FEEDZY_PRO_DIRNAME, function ( $compatibilities ) {
			$compatibilities['FeedzyLite'] = array(
				'basefile'  => defined( 'FEEDZY_BASEFILE' ) ? FEEDZY_BASEFILE : '',
				'required'  => '3.8',
				'tested_up' => '4.4',
			);
			return $compatibilities;
		}
	);
	add_filter(
		'themesle_sdk_namespace_' . md5( __FILE__ ), function ( $namespace ) {
			return 'feedzy';
		}
	);
}

/**
 * Registers with the SDK
 *
 * @since    1.0.0
 */
function feedzy_pro_register_sdk( $products ) {
	$products[] = FEEDZY_PRO_BASEFILE;
	return $products;
}


spl_autoload_register( 'feedzy_rss_feeds_pro_autoload' );
run_feedzy_rss_feeds_pro();

