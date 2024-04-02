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
	private array $labelCounts = array();
	private array $wordProbabilities = array();
	private $tokenizer;

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
				$this->labelCounts[ $contentType ][ $labelType ][ $labelValue ] = ( $this->labelCounts[ $contentType ][ $labelType ][ $labelValue ] ?? 0 ) + 1;

				foreach ( $words as $word ) {
					$this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ][ $word ] = ( $this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ][ $word ] ?? 0 ) + 1;
				}
			}
		}

		// Calculate probabilities
		foreach ( $labels as $labelType => $labelValues ) {
			foreach ( $labelValues as $labelValue ) {
				$totalWords = array_sum( $this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ] ?? array() );
				foreach ( ( $this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ] ?? array() ) as $word => $count ) {
					$this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ][ $word ] = ( $count + 1 ) / ( $totalWords + count( $this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ] ) );
				}
			}
		}
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

		foreach ( $this->labelCounts[ $contentType ] ?? array() as $labelType => $labels ) {
			foreach ( $labels as $labelValue => $count ) {
				$labelScores[ $labelType ][ $labelValue ] = 1; // Initialize score
				foreach ( $words as $word ) {
					$labelScores[ $labelType ][ $labelValue ] *= $this->wordProbabilities[ $contentType ][ $labelType ][ $labelValue ][ $word ] ?? 1 / ( array_sum( $labels ) + count( $words ) );
				}
				$labelScores[ $labelType ][ $labelValue ] *= $count / array_sum( $labels );
			}
		}

		foreach ( $labelScores as $labelType => &$scores ) {
			arsort( $scores ); // Sort by probability in descending order
		}

		return $labelScores;
	}

	/**
	 * Export the current state as JSON.
	 */
	public function exportState(): string {
		return json_encode(
			array(
				'labelCounts' => $this->labelCounts,
				'wordProbabilities' => $this->wordProbabilities,
			)
		);
	}

	/**
	 * Import a state from a JSON string.
	 * @param string $text The JSON to import from.
	 */
	public function importState( string $json ): void {
		$state = json_decode( $json, true );
		$this->labelCounts = $state['labelCounts'] ?? array();
		$this->wordProbabilities = $state['wordProbabilities'] ?? array();
	}

	/**
	 * Reset the classifier state. Can reset the entire state or just for a specific content type.
	 *
	 * @param string|null $contentType The content type to reset, or null to reset everything.
	 */
	public function reset( ?string $contentType = null ): void {
		if ( $contentType === null ) {
			$this->labelCounts = array();
			$this->wordProbabilities = array();
		} else {
			unset( $this->labelCounts[ $contentType ] );
			unset( $this->wordProbabilities[ $contentType ] );
		}
	}
}
