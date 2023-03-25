<?php
/**
 * Discourse Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants as Constants;
use Frocentric\Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discourse Class.
 */
class Discourse {

	/**
	 * Checks if WordPress is configured as a Discourse client.
	 *
	 * @return bool
	 */
	protected static function discourse_client_configured() {
		return class_exists( 'WPDiscourse\Discourse\Discourse' ) && isset( get_option( 'discourse_connect' )['url'] );
	}

	/**
	 * Modifies the comment template to render user avatars as 32x32px
	 */
	public static function discourse_comment_html( $output ) {
		return str_replace( '64', '32', $output );
	}

	/**
	 * Modifies the replies template to add the comment count to the title
	 */
	public static function discourse_replies_html( $output ) {
		$modified = $output;

		if ( isset( $_GET['post_id'] ) && is_numeric( $_GET['post_id'] ) ) {
			$post = get_post( sanitize_key( wp_unslash( $_GET['post_id'] ) ) );

			if ( $post && get_comments_number( $post->ID ) ) {
				$modified = str_replace( '</h2>', ' (<span class="comment-count">' . get_comments_number( $post->ID ) . '</span>)</h2>', $modified );

			}
		}

		$modified = preg_replace_callback(
			'/(<h3\sid="reply-title".*?href="([^"]+)".*?<\/h3>)/s',
			function ( $matches ) {
				$url = $matches[2] . '#reply';

				if ( ! wp_validate_logged_in_cookie( false ) ) {
					$url = home_url( '?discourse_sso=1&redirect_to=' . urlencode( $url ) );
					$url = str_replace( '%7B', '{', str_replace( '%7D', '}', $url ) );
				}

				$value = '<div class="elementor-button-wrapper"><a href="' . $url . '" class="elementor-button-link elementor-button elementor-size-lg" role="button"><span class="elementor-button-content-wrapper"><span class="elementor-button-text">' . esc_html__( 'Reply', 'frocentric' ) . '</span></span></a></div>';

				return $value;
			},
			$modified
		);
		$needle = 'class="comments-area">';

		if ( strpos( $modified, $needle ) !== false ) {
			$modified = str_replace(
				$needle,
				$needle . '<div class="comments-title-wrap"><h2 class="comments-title discourse-comments-title">' . esc_html__( 'No Replies' ) . '</h2></div>',
				$modified
			);
		}

		return $modified;
	}

	/**
	 * Generates the Discourse tags based on WP taxonomies.
	 *
	 * @param int $post_id The post's ID.
	 * @return array
	 */
	protected static function generate_discourse_tags( $post_id ) {
		$tags = array();

		foreach ( Constants::DISCOURSE_TAG_TAXONOMIES as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$tags[] = $term->slug;
				}
			}
		}

		return $tags;
	}

	/**
	 * Uses the Discourse avatar if user has one, otherwise uses the WordPress avatar.
	 *
	 * @param string $url The current URL.
	 * @param mixed  $id_or_email The Gravatar key.
	 * @param array  $args Arguments passed to get_avatar_data.
	 */
	public static function get_avatar_url( $url, $id_or_email, $args ) {
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', $id_or_email );
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', $id_or_email->user_id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}

		if ( $user && $user->ID ) {
			$discourse_user = get_user_meta( $user->ID, 'discourse_user', true );

			if ( $discourse_user && isset( $discourse_user['avatar_url'] ) ) {
				return $discourse_user['avatar_url'];
			}
		}

		return $url;
	}

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( class_exists( '\WPDiscourse\Discourse\Discourse' ) ) {
			// Actions
			add_action( 'set_object_terms', array( __CLASS__, 'set_object_terms' ), 10, 4 );
			add_action( 'wpdc_after_sso_client_user_update', array( __CLASS__, 'wpdc_after_sso_client_user_update' ), 10, 2 );
			add_action( 'wpdc_webhook_before_update_user_data', array( __CLASS__, 'wpdc_webhook_before_update_user_data' ), 10, 3 );

			// Filters.
			add_filter( 'get_avatar_url', array( __CLASS__, 'get_avatar_url' ), 10, 3 );
			add_filter( 'login_redirect', array( __CLASS__, 'login_redirect' ), 10, 3 );
			add_filter( 'wpdc_use_discourse_user_webhook', '__return_true', 10, 1 );

			if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
				add_filter( 'discourse_comment_html', array( __CLASS__, 'discourse_comment_html' ), 10, 1 );
				add_filter( 'discourse_no_replies_html', array( __CLASS__, 'discourse_replies_html' ), 10, 1 );
				add_filter( 'discourse_replies_html', array( __CLASS__, 'discourse_replies_html' ), 10, 1 );
			}
		}
	}

	/**
	 * Redirect user after successful login via Discourse.
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $request URL the user is coming from.
	 * @param object $user Logged user's data.
	 * @return string
	 */
	public static function login_redirect( $redirect_to, $request, $user ) {
		// is there a user to check?
		if ( isset( $user->roles ) && is_array( $user->roles ) && self::discourse_client_configured() ) {
			// check for admin URL
			if ( str_starts_with( $redirect_to, admin_url() ) ) {
				// redirect them to the default location
				return $redirect_to;
			} else {
				// redirect them to the community
				return get_option( 'discourse_connect' )['url'];
			}
		} else {
			return $redirect_to;
		}
	}

	/**
	 * Updates Discourse publishing metadata.
	 *
	 * @param int    $post_id The object's ID.
	 * @param array  $terms An array of object term IDs or slugs.
	 * @param array  $tt_ids An array of term taxonomy IDs.
	 * @param string $taxonomy The taxonomy slug.
	 */
	public static function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy ) {
		if ( ! in_array( $taxonomy, Constants::DISCOURSE_TAG_TAXONOMIES, true ) ) {
			return;
		}

		$post = get_post( $object_id );
		// bail out if this isn't a regular post
		if ( empty( $post ) || is_wp_error( $post ) || $post->post_type !== 'post' ) {
			return;
		}

		// bail out if the post isn't in the Community or Platform categories
		$categories = wp_get_post_categories( $object_id, array( 'fields' => 'slugs' ) );
		if ( ! in_array( 'community', $categories, true ) && ! in_array( 'platform', $categories, true ) ) {
			return;
		}

		$tags = self::generate_discourse_tags( $object_id );
		// Update Discourse tags.
		update_post_meta( $object_id, 'wpdc_topic_tags', $tags );

		if ( ! metadata_exists( 'post', $object_id, 'publish_to_discourse' ) ) {
			// Enable publishing in Discourse, during initial save only.
			update_post_meta( $object_id, 'publish_to_discourse', true );
			$discourse_publish_option = get_option( 'discourse_publish' );

			if ( is_array( $discourse_publish_option ) && array_key_exists( 'publish-category', $discourse_publish_option ) ) {
				$publish_category = $discourse_publish_option['publish-category'];
				update_post_meta( $object_id, 'publish_post_category', $publish_category );
			}
		}
	}

	/**
	 * Saves the Discourse account details to the user metadata.
	 *
	 * @param int   $user_id The WordPress user's ID.
	 * @param array $discourse_user The Discourse user data.
	 */
	public static function wpdc_after_sso_client_user_update( $user_id, $discourse_user ) {
		update_user_meta( $user_id, 'discourse_user', $discourse_user );
	}

	/**
	 * Updates WP user with Discord profile data during save
	 *
	 * @param WPUser                                             $wordpress_user The WordPress user
	 * @param array @discourse_user The Discourse profile fields
	 * @param string @event_type The event type
	 */
	public static function wpdc_webhook_before_update_user_data( $wordpress_user, $discourse_user, $event_type ) {
		$bio  = $discourse_user['bio_raw'];
		$website = $discourse_user['website'];
		$user_fields = $discourse_user['user_fields'];
		$user_id = $wordpress_user->ID;
		$discourse_meta = get_user_meta( $user_id, 'discourse_user', true );

		if ( empty( $discourse_meta ) && ! empty( $user_fields ) ) {
			$discourse_meta = array();
		}

		if ( empty( $user_fields ) ) {
			if ( array_key_exists( 'user_fields', $discourse_meta ) ) {
				unset( $discourse_meta['user_fields'] );
			}
		} else {
			$discourse_meta['user_fields'] = $user_fields;
		}

		update_user_meta( $user_id, 'discourse_user', $discourse_meta );
		update_user_meta( $user_id, 'description', empty( $bio ) ? '' : $bio );
		wp_update_user(
			array(
				'ID' => $user_id,
				'user_url' => empty( $website ) ? '' : $website,
			)
		);
	}
}
