<?php
/**
 * Utility methods
 *
 * @class       Utils
 * @version     1.0.0
 * @package     Frocentric/Customizations/
 */

namespace Frocentric\Customizations;

use Frocentric\Constants;
use Frocentric\Customizations\ContentClassifier\OpenAiApi;
use Frocentric\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * An enhanced Naive Bayes classifier with support for custom tokenization and flexible state management.
 */
class ContentClassifier {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( Utils::is_request( Constants::ADMIN_REQUEST ) ) {
			add_action( 'save_post', array( __CLASS__, 'handle_post_creation' ), 10, 3 );
		}
	}

	/**
	 * Handle the creation of a new post.
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post The post object.
	 * @param bool $update Whether this is an existing post being updated.
	 *
	 * @return void
	 */
	public static function handle_post_creation( $post_id, $post, $update ) {
		// Avoid infinite loop.
		remove_action( 'save_post', array( __CLASS__, 'handle_post_creation' ), 10 );

		// Check if this is a post or tribe_events.
		if ( $post->post_type === 'post' && $post->post_status === 'draft' ||
			 $post->post_type === 'tribe_events' && $post->post_status === 'pending' ) {

			$raw_content = self::get_raw_content( $post );
			$classified_taxonomies = self::classify( $raw_content, array( 'audience', 'discipline', 'interest' ) );

			self::classify_post( $post_id, $classified_taxonomies );
		}
		// Add action back to avoid breaking any workflow.
		add_action( 'save_post', array( __CLASS__, 'handle_post_creation' ), 10, 3 );
	}

	/**
	 * Classify a new piece of text.
	 *
	 * @param string $content The content to classify.
	 * @param array $taxonomies The taxonomies to analyse.
	 * @return array
	 */
	public static function classify( string $content, array $taxonomies ): array {
		$taxonomy_terms = self::get_all_taxonomy_terms( $taxonomies );
		$message = self::build_message( $content, $taxonomy_terms );
		$response = '';//OpenAiApi::get_response( $message );
		$wp_taxonomies = get_object_taxonomies( 'post', 'names' );
		self::log( $wp_taxonomies );
		self::log( $response );
		// TODO: apply filter to allow for type-specific data to be added
		return self::extract_json_object( $response );
	}

	/**
	 * Get all taxonomy terms for a given content type.
	 *
	 * @param array $taxonomies The taxonomies to retrieve terms for.
	 * @return array
	 */
	protected static function get_all_taxonomy_terms( $taxonomies ) {
		$taxonomy_terms = array();

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $terms ) ) {
				continue;
			}

			$term_names = array();

			foreach ( $terms as $term ) {
				$term_names[] = $term->name;
			}

			$taxonomy_terms[ $taxonomy ] = $term_names;
		}
		self::log( $taxonomy_terms );
		return $taxonomy_terms;
	}

	/**
	 * Build a message for the OpenAI API.
	 *
	 * @param string $content The content to classify.
	 * @param array $taxonomies The taxonomies to analyse.
	 * @return string
	 */
	protected static function build_message( $content, $taxonomies ) {
		$prefix = 'Analyse the content property of the JSON object below and determine which of the terms from each of the other properties is most relevant. Return a JSON object that contains all the other property keys from the original object, where the value for each key is an array of objects. Each object should have a single property using the original term as the key for a numeric value between 0 and 1 representing the strength of the match. Do not provide any explanation or any other output in your response.';
		$json = json_encode(
			array(
				'content' => $content,
				'taxonomies' => $taxonomies,
			)
		);

		$message = "$prefix\n\n$json";
		self::log( $message );
		return $message;
	}

	/** Extracts a JSON object from a string, by pattern matching the opening and closing curly braces.
	 *
	 * @param string $string The string to extract the JSON object from.
	 * @return array|null
	 */
	public static function extract_json_object( $string ) {
		$pattern = '/\{(?:[^{}]|(?R))*\}/';
		preg_match( $pattern, $string, $matches );

		if ( count( $matches ) > 0 ) {
			return json_decode( $matches[0], true );
		}

		return null;
	}

	/**
	 * Classify the post based on its content and update its status.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $classified_taxonomies Classified taxonomies with their terms.
	 *
	 * @return void
	*/
	protected static function classify_post( $post_id, $classified_taxonomies ) {
		foreach ( $classified_taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term => $relevance ) {
				if ( $relevance >= CLASSIFIER_RELEVANCE_THRESHOLD && term_exists( $term, $taxonomy ) ) {
					wp_set_object_terms( $post_id, $term, $taxonomy, true );
				}
			}
		}

		// Update post status to publish.
		wp_update_post(
			array(
				'ID' => $post_id,
				'post_status' => 'publish',
			)
		);
	}

	/**
	 * Extracts the raw content from a post.
	 *
	 * @param object $post The post to extract the content from.
	 * @return string
	 */
	protected static function get_raw_content( $post ) {
		if ( empty( $post ) ) {
			return '';
		}

		return strip_shortcodes( wp_strip_all_tags( get_the_content( null, false, $post ) ) );
	}

	protected static function log( $value ) {
		echo( "\n" );
		var_dump( $value );
		ob_flush();
	}
}

ContentClassifier::hooks();
