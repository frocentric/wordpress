<?php
/**
 * Compatibility fixes for smoother interoperation with Yoast's WP SEO and
 * WP SEO Premium plugins.
 *
 * @since 4.4.14
 */
class Tribe__Events__Pro__Integrations__WP_SEO__WP_SEO {
	/**
	 * Sets up fixes required for improved compatability with WP SEO.
	 *
	 * @since 4.4.14
	 */
	public function hook() {
		add_action( 'post_updated', array( $this, 'unhook_slug_change_detector' ), 10, 3 );
	}

	/**
	 * WP SEO Premium tries to detect slug changes in order to pre-emptively configure
	 * redirect rules from the old slug to the new one - bug gets confused when recurring
	 * events are updated.
	 *
	 * This method runs during post_updated a little earlier than the relevant WP SEO
	 * Premium method, which it attempts to unhook unless the post slug really has changed.
	 *
	 * @since 4.4.14
	 *
	 * @param int     $post_id
	 * @param WP_Post $updated_post
	 * @param WP_Post $original_post
	 */
	public function unhook_slug_change_detector( $post_id, $updated_post, $original_post ) {
		// If this helper isn't available (such as if an earlier version of TEC is running) then we can't do anything
		if ( ! function_exists( 'tribe_retrieve_object_by_hook' ) ) {
			return;
		}

		// Obtain the WP SEO post watcher object or bail out
		if ( ! ( $wp_seo_post_watcher = tribe_retrieve_object_by_hook( 'WPSEO_Post_Watcher', 'post_updated', 12 ) ) ) {
			return;
		}

		// If the original and current post slugs match, unhook WP SEO's slug change detector
		if ( $original_post->post_name === $updated_post->post_name ) {
			remove_action( 'post_updated', array( $wp_seo_post_watcher, 'detect_slug_change' ), 12, 3 );
		}
	}
}
