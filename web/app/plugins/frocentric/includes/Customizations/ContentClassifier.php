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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * An enhanced Naive Bayes classifier with support for custom tokenization and flexible state management.
 */
class ContentClassifier {
	/**
	 * Classify a new piece of text.
	 *
	 * @param string $content_type The type of content being classified.
	 * @param string $text The text to classify.
	 */
	public function classify( string $content_type, string $content ): array {
		$labels = array();
		$text = \wp_strip_all_tags( $content );
		$taxonomies = get_object_taxonomies( $content_type, 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			// TODO: apply filters against taxonomies enabled for content type set in environment variable
			if ( $taxonomy->public ) {
				$terms = \get_terms(
					array(
						'taxonomy' => $taxonomy->name,
						'hide_empty' => true,
					)
				);

				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$labels[ $taxonomy->name ] = array_map( 'strtolower', $terms );
				}
			}
		}

		// TODO: apply filter to allow for type-specific data to be added
		return array(
			'content' => $text,
			'labels' => $labels,
		);
	}
}
