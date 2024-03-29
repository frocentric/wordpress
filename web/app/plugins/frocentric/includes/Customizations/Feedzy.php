<?php
/**
 * Feedzy Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants;
use Frocentric\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedzy Class.
 */
class Feedzy {

	/**
	 * Modifies the post content when the "Post Content" placeholder has been inserted
	 */
	public static function feedzy_content( $item_content, $item ) {
		$placeholder = esc_html__( 'Post Content', 'feedzy-rss-feeds' );

		if ( strpos( $item_content, $placeholder ) === 0 ) {
			$item_content = str_replace( $placeholder, '', $item_content );
		}

		return $item_content;
	}

	/**
	 * Modifies the post arguments when importing an item.
	 */
	public static function feedzy_insert_post_args( $args, $item, $post_title, $post_content, $index, $job ) {
		$args = self::set_post_author( $args, $item );
		$args = self::set_post_canonical_url( $args, $item );
		$args = self::set_post_format( $args, $item );

		return $args;
	}

	/**
	 * Add attributes to $item_array.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array  $item_array The item attributes array.
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   int    $index The item number (may not be the same as the item_index).
	 * @param   int    $item_index The real index of this items in the feed (maybe be different from $index if filters are used).
	 *
	 * @return mixed
	 */
	public static function feedzy_item_filter( $item_array, $item, $sc = null, $index = null, $item_index = null ) {
		// Embed enclosure for podcast feeds
		if ( is_array( $item->data['enclosures'] ) && count( $item->data['enclosures'] ) > 0 ) {
			$enclosure = $item->data['enclosures'][0];
			$item_array['item_content'] .= '[audio src="' . $enclosure->link . '"]';
		}

		return $item_array;
	}

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
			add_action( 'feedzy_after_post_import', array( __CLASS__, 'save_item_title' ), 10, 3 );
			add_filter( 'feedzy_content', array( __CLASS__, 'feedzy_content' ), 5, 2 );
			add_filter( 'feedzy_insert_post_args', array( __CLASS__, 'feedzy_insert_post_args' ), 10, 6 );
			add_filter( 'feedzy_item_filter', array( __CLASS__, 'feedzy_item_filter' ), 10, 5 );
			add_filter( 'the_content', array( __CLASS__, 'set_post_citation' ) );
		}
	}

	/**
	 * Replaces the default "Read More" link to the source with a formatted citation
	 */
	protected static function replace_citation( $content, $author, $title, $url ) {
		$pattern = '/<a\b[^>]*href=["\']' . preg_quote( $url, '/' ) . '["\'][^>]*class=["\']feedzy-rss-link-icon["\'][^>]*>' . __( 'Read More', 'feedzy-rss-feeds' ) . '<\/a>/i';
		$citation = '<aside class="cite">' . __( 'Originally posted by ', 'frocentric' ) . $author . ' to <a href="' . $url . '" target="_blank" rel="nofollow">' . $title . '</a></aside>';

		return preg_replace( $pattern, $citation, $content );
	}

	/**
	 * Saves the original item title as metadata for the imported post
	 */
	public static function save_item_title( $post_id, $feedzy_item, $feedzy_settings ) {
		update_post_meta( $post_id, 'feedzy_item_title', sanitize_text_field( $feedzy_item['item_title'] ) );
	}

	/**
	 * Sets the post author based on the provided querystring value
	 * Requires "feed_author" parameter to be added to feed URL in Feedzy control panel. Can be set to either a user ID or login.
	 */
	private static function set_post_author( $args, $item ) {
		$source = $item['item']->get_feed()->subscribe_url();
		$source = parse_url( $source );
		$author = null;

		if ( isset( $source['query'] ) ) {
			parse_str( $source['query'], $params );

			// Set post author
			$feed_author = isset( $params['feed_author'] ) ? $params['feed_author'] : 0;

			if ( $feed_author ) {
				$author = is_numeric( $feed_author ) ? get_user_by( 'ID', (int) $feed_author ) : get_user_by( 'login', $feed_author );

				if ( $author ) {
					$args['post_author'] = $author->ID;
				}
			}
		}

		return $args;
	}

	/**
	 * Sets the canonical link to the source item
	 */
	private static function set_post_canonical_url( $args, $item ) {
		$args['meta_input']['_genesis_canonical_uri'] = $item['item_url'];

		return $args;
	}

	/**
	 * Replaces the default "Read More" link to the source with a formatted citation
	 */
	public static function set_post_citation( $content ) {
		global $post;

		if ( ! empty( $post ) && get_post_meta( $post->ID, 'feedzy', true ) ) {
			$author = get_the_author_meta( 'display_name', intval( $post->post_author ) );
			$title = get_post_meta( $post->ID, 'feedzy_item_title', true );
			$url = get_post_meta( $post->ID, 'feedzy_item_url', true );

			if ( empty( $author ) ) {
				$author = get_post_meta( $post->ID, 'feedzy_item_author', true );
			}

			if ( empty( $title ) ) {
				$title = $post->post_title;
			}

			if ( ! empty( $author ) && ! empty( $title ) && ! empty( $url ) ) {
				$content = self::replace_citation( $content, $author, $title, $url );
			}
		}

		return $content;
	}

	/**
	 * Sets the post format based on the provided querystring value
	 * Requires "post_format" parameter to be added to feed URL in Feedzy control panel. Can be set to any standard post format.
	 */
	private static function set_post_format( $args, $item ) {
		$source = $item['item']->get_feed()->subscribe_url();
		$source = parse_url( $source );
		$post_format = null;

		if ( isset( $source['query'] ) ) {
			parse_str( $source['query'], $params );

			// Set post format
			$post_format = isset( $params['post_format'] ) ? $params['post_format'] : null;

			if ( $post_format ) {
				$formats = array_keys( get_post_format_slugs() );

				if ( empty( $args['meta_input'] ) ) {
					$args['meta_input'] = array();
				}

				$args['meta_input']['post_format'] = in_array( $post_format, $formats, true ) ? $post_format : 'standard';
			}
		}

		return $args;
	}
}
