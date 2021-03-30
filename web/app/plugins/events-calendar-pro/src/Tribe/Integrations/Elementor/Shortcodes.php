<?php
/**
 * Handles the support of Shortcodes in the context of Elementor.
 *
 * The class acts, with knowledge of Elementor state and filters, to make sure shortcodes will render correctly
 * in the editor and front-end area.
 *
 * @since   5.1.4
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */

namespace Tribe\Events\Pro\Integrations\Elementor;

use Tribe\Events\Pro\Views\V2\Assets as Pro_Assets;
use Tribe\Events\Views\V2\Assets as Event_Assets;

/**
 * Class Shortcodes
 *
 * @since   5.1.4
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
class Shortcodes {
	/**
	 * The tag of the shortcode used to render event archives.
	 *
	 * @since 5.1.4
	 *
	 * @var string
	 */
	public static $events_archive_tag = 'tribe_events';

	/**
	 * A flag property to indicate whether archive shortcodes have been already supported in this request or not.
	 *
	 * @since 5.1.4
	 *
	 * @var bool
	 */
	protected $did_support_archives = false;

	/**
	 * Supports, embedding on the view HTML the required `script` and `link` HTML tags, the archive shortcode.
	 *
	 * @since 5.1.4
	 *
	 * @param string $output The current shortcode output, as processed by the default shortcode code.
	 * @param string $tag    The tag for the shortcode currently being "done".
	 *
	 * @return string The shortcode HTML, modified to include the `script` (JS) and `link` (CSS) HTML tags if required.
	 */
	public function support_archive_shortcode( $output, $tag ) {
		if ( $this->did_support_archives ) {
			return $output;
		}

		if ( static::$events_archive_tag !== $tag ) {
			// Let's be really sure we're only printing scripts and styles if required.
			return $output;
		}

		if ( ! ( $this->is_archive_request() || $this->post_contains_shortcode( static::$events_archive_tag ) ) ) {
			// If the shortcode is already on the page, as saved in the database, then the scripts will be loaded.
			return $output;
		}

		$this->did_support_archives = true;

		$output .= tribe_asset_print_group( [ Pro_Assets::$group_key, Event_Assets::$group_key ] );

		// Add the Customizer styles after the script, to make sure they will apply.
		$output .= tribe( 'customizer' )->get_styles_scripts();

		// Return the data w/o modification, we're really using the filter as an action.
		return $output;
	}

	/**
	 * Checks whether the current POST request is from Elementor and requires the rendering of an archive request,
	 * or not.
	 *
	 * @since 5.1.4
	 *
	 * @return bool Whether the current POST request is from Elementor and requires the rendering of an archive request,
	 *              or not.
	 */
	protected function is_archive_request() {
		if ( 'elementor_ajax' !== tribe_get_request_var( 'action' ) || ! wp_doing_ajax() ) {
			return false;
		}

		// Since we'll not be using the sanitized version of the `actions` value, we use a simple `isset` here.
		if (
			! (
				isset( $_POST['actions'], $_POST['action'], $_POST['_nonce'] )
				&& wp_verify_nonce( tribe_get_request_var( '_nonce' ), tribe_get_request_var( 'action' ) )
			)
		) {
			return false;
		}

		$filter = FILTER_UNSAFE_RAW;
		if ( defined( 'FILTER_SANITIZE_ADD_SLASHES' ) ) {
			$filter = FILTER_SANITIZE_ADD_SLASHES;
		} elseif ( defined( 'FILTER_SANITIZE_MAGIC_QUOTES' ) ) {
			$filter = FILTER_SANITIZE_MAGIC_QUOTES;
		}

		// We do not use `tribe_get_request_var` here as we need the not decoded version, but still use some care.
		$json_payload = filter_var( $_POST['actions'], $filter );

		/*
		 * `preg_` functions are vulnerable in the `$pattern` argument, where the `/e` flags allows execution of
		 * arbitrary PHP code part of the pattern. But here we control the pattern completely without user input.
		 * Instead of unpacking a rather complex, and variable, JSON object to assess if the request contains a
		 * request for the archive shortcode or not, we simply make a string check.
		 * We use a regular expression here as the specific JSON formatting is not in our control in terms of spacing
		 * and formatting.
		 */
		$pattern = '/"shortcode[^\\[\\]:]*:[^\\[\\]]*"\\[\\s*' . preg_quote( static::$events_archive_tag, '/' ) . '/us';

		return (bool) preg_replace( $pattern, '$1', $json_payload );
	}

	/**
	 * Whether the `post_content` of the current post object contains a shortcode or not.
	 *
	 * @since 5.1.4
	 *
	 * @param string $tag The tag of the shortcode to check.
	 *
	 * @return bool Whether the `post_content` of the current post object contains a shortcode or not.
	 */
	protected function post_contains_shortcode( $tag ) {
		$post_id = tribe_get_request_var( 'editor_post_id', 0 );
		$post    = get_post( $post_id );

		return $post instanceof \WP_Post && has_shortcode( $post->post_content, $tag );
	}
}
