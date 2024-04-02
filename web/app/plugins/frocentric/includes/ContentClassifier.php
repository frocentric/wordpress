<?php
/**
 * Utility methods
 *
 * @class       Utils
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

use Frocentric\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * An enhanced Naive Bayes classifier with support for custom tokenization and flexible state management.
 */
class ContentClassifier {
	protected array $state = array();
	protected $tokenizer;

	/**
	 * Constructor with options for customization.
	 *
	 * @param array $options Options for customizing the classifier behavior.
	 */
	public function __construct( array $options = array() ) {
		$this->tokenizer = $options['tokenizer'] ?? function ( $text ) {
			// Default tokenizer: convert to lowercase and split into words
			return explode( ' ', strtolower( $text ) );
		};
	}

	/**
	 * Learn from a single piece of text, with labels now being an associative array of label types
	 * to an array of applicable labels.
	 *
	 * @param string $contentType The type of content being learned.
	 * @param string $text The text to learn from.
	 * @param array $labels An associative array where keys are label types and values are arrays of labels of the text.
	 */
	public function learn( string $contentType, string $text, array $labels ): void {
		$words = call_user_func( $this->tokenizer, $text );

		foreach ( $labels as $labelType => $labelValues ) {
			foreach ( $labelValues as $labelValue ) {
				// Initialize structure if not exists
				if ( ! isset( $this->state[ $contentType ][ $labelType ][ $labelValue ] ) ) {
					$this->state[ $contentType ][ $labelType ][ $labelValue ] = array(
						'count' => 0,
						'wordProbabilities' => array(),
					);
				}

				// Increment label count
				$this->state[ $contentType ][ $labelType ][ $labelValue ]['count']++;

				foreach ( $words as $word ) {
					if ( ! isset( $this->state[ $contentType ][ $labelType ][ $labelValue ]['wordProbabilities'][ $word ] ) ) {
						$this->state[ $contentType ][ $labelType ][ $labelValue ]['wordProbabilities'][ $word ] = 1;
					} else {
						$this->state[ $contentType ][ $labelType ][ $labelValue ]['wordProbabilities'][ $word ]++;
					}
				}
			}
		}

		// Calculate probabilities
		$this->calculateProbabilities();
	}

	/**
	 * Classify a new piece of text.
	 *
	 * @param string $contentType The type of content being classified.
	 * @param string $text The text to classify.
	 */
	public function classify( string $contentType, string $text ): array {
		$words = call_user_func( $this->tokenizer, $text );
		$labelScores = array();

		foreach ( $this->state[ $contentType ] ?? array() as $labelType => $labelValues ) {
			foreach ( $labelValues as $labelValue => $data ) {
				$labelScores[ $labelType ][ $labelValue ] = log( $data['count'] ); // Start with log(count) to avoid underflow

				foreach ( $words as $word ) {
					$prob = $data['wordProbabilities'][ $word ] ?? ( 1 / ( array_sum( $data['wordProbabilities'] ) + count( $words ) ) );
					$labelScores[ $labelType ][ $labelValue ] += log( $prob );
				}
			}
		}

		// Normalize scores back from log space if necessary, or just use as a relative comparison
		return $labelScores;
	}

	/**
	 * Export the current state as JSON.
	 */
	public function exportState(): string {
		return json_encode( $this->state );
	}

	/**
	 * Import a state from a JSON string.
	 * @param string $text The JSON to import from.
	 */
	public function importState( string $json ): void {
		$this->state = json_decode( $json, true );
	}

	/**
	 * Reset the classifier state. Can reset the entire state or just for a specific content type.
	 *
	 * @param string|null $contentType The content type to reset, or null to reset everything.
	 */
	public function reset( ?string $contentType = null ): void {
		if ( $contentType === null ) {
			$this->state = array();
		} else {
			unset( $this->state[ $contentType ] );
		}
	}

	/**
	 * Calculate probabilities for all words
	 */
	protected function calculateProbabilities(): void {
		foreach ( $this->state as $contentType => $labelTypes ) {
			foreach ( $labelTypes as $labelType => $labelValues ) {
				foreach ( $labelValues as $labelValue => $data ) {
					$totalWords = array_sum( $data['wordProbabilities'] );

					foreach ( $data['wordProbabilities'] as $word => $count ) {
						$this->state[ $contentType ][ $labelType ][ $labelValue ]['wordProbabilities'][ $word ] =
							( $count + 1 ) / ( $totalWords + count( $data['wordProbabilities'] ) );
					}
				}
			}
		}
	}
}
