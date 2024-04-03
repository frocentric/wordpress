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

	/**
	 * Learn from a single piece of text, with labels now being an associative array of label types
	 * to an array of applicable labels.
	 *
	 * @param string $content_type The type of content being learned.
	 * @param string $text The text to learn from.
	 * @param array $labels An associative array where keys are label types and values are arrays of labels of the text.
	 */
	// phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
	public function learn( string $content_type, string $text, array $labels, array $stop_words = null ): void {
		$words = $this->tokenize( $text, $stop_words );

		foreach ( $labels as $label_type => $label_values ) {
			foreach ( $label_values as $label_value ) {
				// Initialize structure if not exists
				if ( ! isset( $this->state[ $content_type ][ $label_type ][ $label_value ] ) ) {
					$this->state[ $content_type ][ $label_type ][ $label_value ] = array(
						'count' => 0,
						'word_probabilities' => array(),
					);
				}

				// Increment label count
				$this->state[ $content_type ][ $label_type ][ $label_value ]['count']++;

				foreach ( $words as $word ) {
					if ( ! isset( $this->state[ $content_type ][ $label_type ][ $label_value ]['word_probabilities'][ $word ] ) ) {
						$this->state[ $content_type ][ $label_type ][ $label_value ]['word_probabilities'][ $word ] = 1;
					} else {
						$this->state[ $content_type ][ $label_type ][ $label_value ]['word_probabilities'][ $word ]++;
					}
				}
			}
		}

		$this->calculate_probabilities();
	}

	/**
	 * Classify a new piece of text.
	 *
	 * @param string $content_type The type of content being classified.
	 * @param string $text The text to classify.
	 */
	public function classify( string $content_type, string $text, array $stop_words = null ): array {
		$words = $this->tokenize( $text, $stop_words );
		$label_scores = array();

		foreach ( $this->state[ $content_type ] ?? array() as $label_type => $label_values ) {
			foreach ( $label_values as $label_value => $data ) {
				$label_scores[ $label_type ][ $label_value ] = log( $data['count'] ); // Start with log(count) to avoid underflow

				foreach ( $words as $word ) {
					$prob = $data['word_probabilities'][ $word ] ?? ( 1 / ( array_sum( $data['word_probabilities'] ) + count( $words ) ) );
					$label_scores[ $label_type ][ $label_value ] += log( $prob );
				}
			}
		}

		// Normalize scores back from log space if necessary, or just use as a relative comparison
		return $label_scores;
	}

	/**
	 * Gets the current state.
	 */
	public function get_state(): array {
		return $this->state;
	}

	/**
	 * Sets the state.
	 * @param array $state The new state array.
	 */
	public function set_state( array $state ): void {
		$this->state = $state;
	}

	/**
	 * Reset the classifier state. Can reset the entire state or just for a specific content type.
	 *
	 * @param string|null $content_type The content type to reset, or null to reset everything.
	 */
	public function reset( ?string $content_type = null ): void {
		if ( $content_type === null ) {
			$this->state = array();
		} else {
			unset( $this->state[ $content_type ] );
		}
	}

	protected function tokenize( string $text, array $stop_words = null ): array {
		$text = mb_strtolower( $text );
		$final_stop_words = $stop_words ?? Constants::CLASSIFIER_STOP_WORDS;
		// escape the stopword array and implode with pipe
		$filter_words = '~\b(' . implode( '|', array_map( 'preg_quote', $final_stop_words ) ) . ')\b~';

		// remove stop words
		$text = preg_replace( $filter_words, '', $text );

		// split the words
		preg_match_all( '/[[:alpha:]]+/u', $text, $matches );

		// first match list of words
		return $matches[0];
	}

	/**
	 * Calculate probabilities for all words
	 */
	// phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
	protected function calculate_probabilities(): void {
		foreach ( $this->state as $content_type => $label_types ) {
			foreach ( $label_types as $label_type => $label_values ) {
				foreach ( $label_values as $label_value => $data ) {
					$total_words = array_sum( $data['word_probabilities'] );

					foreach ( $data['word_probabilities'] as $word => $count ) {
						$this->state[ $content_type ][ $label_type ][ $label_value ]['word_probabilities'][ $word ] =
							( $count + 1 ) / ( $total_words + count( $data['word_probabilities'] ) );
					}
				}
			}
		}
	}
}
