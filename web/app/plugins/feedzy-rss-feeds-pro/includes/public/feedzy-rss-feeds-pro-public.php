<?php
/**
 * The public-specific functionality of the plugin.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/public
 */
/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/public
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */

/**
 * Class Feedzy_Rss_Feed_Pro_Public
 */
class Feedzy_Rss_Feeds_Pro_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @access      public
	 *
	 * @param       string $plugin_name The name of this plugin.
	 * @param       string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the meta tags.
	 *
	 * @access      public
	 */
	public function wp() {
		global $wp_version;

		$free_settings = get_option( 'feedzy-settings', array() );
		if ( ! isset( $free_settings['canonical'] ) || 1 !== intval( $free_settings['canonical'] ) ) {
			return;
		}

		// Yoast.
		add_filter( 'wpseo_canonical', array( $this, 'get_canonical_url' ) );

		// All In One SEO.
		add_filter( 'aioseop_canonical_url', array( $this, 'get_canonical_url' ) );

		if ( version_compare( $wp_version, '4.6.0', '>=' ) ) {
			// Fallback if none of the above plugins is present.
			add_filter( 'get_canonical_url', array( $this, 'get_canonical_url' ) );
		}
	}

	/**
	 * Return the author.
	 *
	 * @access      public
	 */
	public function the_author( $author ) {
		global $post;

		if ( ! $post ) {
			return $author;
		}

		$feedzy      = get_post_meta( $post->ID, 'feedzy', true );
		$job_id      = get_post_meta( $post->ID, 'feedzy_job', true );
		$item_author = get_post_meta( $post->ID, 'feedzy_item_author', true );
		if ( intval( $feedzy ) !== 1 || empty( $job_id ) || empty( $item_author ) ) {
			return $author;
		}

		$link_author_admin  = get_post_meta( $job_id, 'import_link_author_admin', true );
		$link_author_public = get_post_meta( $job_id, 'import_link_author_public', true );

		if ( $link_author_admin === 'yes' && is_admin() ) {
			$author = $item_author;
		} elseif ( $link_author_public === 'yes' && ! is_admin() ) {
			$author = $item_author;
		}
		return $author;
	}

	/**
	 * Return the author link.
	 *
	 * @access      public
	 */
	public function author_link( $link, $author_id, $author_nicename ) {
		global $post;

		if ( ! $post ) {
			return $link;
		}

		$feedzy   = get_post_meta( $post->ID, 'feedzy', true );
		$job_id   = get_post_meta( $post->ID, 'feedzy_job', true );
		$item_url = get_post_meta( $post->ID, 'feedzy_item_url', true );
		if ( intval( $feedzy ) !== 1 || empty( $job_id ) || empty( $item_url ) ) {
			return $link;
		}

		$link_author_public = get_post_meta( $job_id, 'import_link_author_public', true );

		if ( $link_author_public === 'yes' ) {
			$link = $item_url;
		}

		return $link;
	}

	/**
	 * Return the canonical URL.
	 *
	 * @access      public
	 */
	public function get_canonical_url( $canonical_url ) {
		if ( ! is_singular() ) {
			return $canonical_url;
		}

		global $post;
		if ( ! $post ) {
			return $canonical_url;
		}

		// let's check if the post has been imported by feedzy.
		if ( 1 === intval( get_post_meta( $post->ID, 'feedzy', true ) ) ) {
			$url = get_post_meta( $post->ID, 'feedzy_item_url', true );
			if ( ! empty( $url ) ) {
				$canonical_url = $url;
			}
		}
		return $canonical_url;
	}
}
