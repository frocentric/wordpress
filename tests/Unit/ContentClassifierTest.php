<?php

namespace Tests\Unit;

use Frocentric\ContentClassifier;

class ContentClassifierTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected $sameContentTypeData = array(
		array(
			'text' => 'Great quality product, very satisfied',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'quality' => array( 'high', 'top' ),
			),
		),
		array(
			'text' => 'Poor build, not what was expected at all',
			'labels' => array(
				'sentiment' => array( 'negative' ),
				'quality' => array( 'low' ),
			),
		),
		array(
			'text' => 'Exceptional service and fast delivery',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'service' => array( 'good', 'great' ),
			),
		),
		array(
			'text' => 'Battery life is shorter than advertised',
			'labels' => array(
				'sentiment' => array( 'negative' ),
				'quality' => array( 'low' ),
			),
		),
		array(
			'text' => 'Highly recommend this to anyone looking for quality',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'quality' => array( 'high' ),
			),
		),
		array(
			'text' => 'Not worth the price',
			'labels' => array(
				'sentiment' => array( 'negative' ),
				'value' => array( 'poor', 'low' ),
			),
		),
		array(
			'text' => 'Perfect for my needs',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'fit' => array( 'good', 'great', 'satisfactory' ),
			),
		),
		array(
			'text' => 'Disappointed with the performance',
			'labels' => array(
				'sentiment' => array( 'negative' ),
				'performance' => array( 'poor', 'low' ),
			),
		),
		array(
			'text' => 'Amazing experience from start to finish',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'experience' => array( 'good', 'great', 'excellent' ),
			),
		),
		array(
			'text' => 'I had higher expectations based on the reviews',
			'labels' => array(
				'sentiment' => array( 'neutral' ),
				'expectations' => array( 'not met' ),
			),
		),
	);

	protected $differentContentTypesData = array(
		array(
			'contentType' => 'reviews',
			'text' => 'An excellent product with great features',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'features' => array( 'excellent' ),
			),
		),
		array(
			'contentType' => 'reviews',
			'text' => 'Fantastic product, would buy again',
			'labels' => array(
				'sentiment' => array( 'positive' ),
				'features' => array( 'excellent' ),
			),
		),
		array(
			'contentType' => 'reviews',
			'text' => 'Terrible customer service, would not recommend',
			'labels' => array(
				'sentiment' => array( 'negative' ),
				'service' => array( 'poor' ),
			),
		),
		array(
			'contentType' => 'news',
			'text' => 'New technology promises to revolutionize the industry',
			'labels' => array(
				'topic' => array( 'technology' ),
				'impact' => array( 'high' ),
			),
		),
		array(
			'contentType' => 'news',
			'text' => 'Economic downturn expected to last several years',
			'labels' => array(
				'topic' => array( 'economy' ),
				'outlook' => array( 'negative' ),
			),
		),
		array(
			'contentType' => 'blogs',
			'text' => 'Exploring the benefits of a plant-based diet',
			'labels' => array(
				'theme' => array( 'health' ),
				'diet' => array( 'plant-based' ),
			),
		),
		array(
			'contentType' => 'blogs',
			'text' => 'Why mindfulness is key to a balanced life',
			'labels' => array(
				'theme' => array( 'well-being' ),
				'practice' => array( 'mindfulness' ),
			),
		),
	);

	protected function _before() {
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', '' );
		}
	}

	protected function _after() {
	}

	// Tests
	public function testLearningAndClassification() {
		$classifier = new ContentClassifier();

		// Test learning with single content type and multiple labels
		$this->learnFromSameContentType( $classifier, 'text', $this->sameContentTypeData );

		// Assert internal state changes
		$state = $classifier->get_state();

		verify( $state )->arrayHasKey( 'text', 'Classifier state should not be empty after learning.' );

		// Test classification
		$classification = $classifier->classify( 'text', 'Very pleased with the product' );

		verify( $classification )->arrayHasKey( 'quality', 'Classification should contain quality labels.' );
		verify( $classification['quality'] )->arrayHasKey( 'high', 'High quality label should be recognized.' );
	}

	public function testExtendedLearningAndClassification() {
		$classifier = new ContentClassifier();

		// Test learning with single content type and multiple labels
		$this->importState( $classifier );

		// Assert internal state changes
		$state = $classifier->get_state();
		$text = file_get_contents( __DIR__ . '/event.txt' );

		verify( $state )->arrayHasKey( 'tribe_events', 'Classifier state should not be empty after learning.' );

		// Test classification
		$classification = $classifier->classify( 'tribe_events', $text );
		$this->log( $classification );
		verify( $classification )->arrayHasKey( 'discipline', 'This is a post about building WordPress websites' );
		verify( $classification['discipline'] )->arrayHasKey( 'web development', 'Web development label should be recognized.' );
	}

	public function testResetFunctionality() {
		$classifier = new ContentClassifier();

		// Test learning with multiple content type and multiple labels
		$this->learnFromDifferentContentTypes( $classifier, $this->differentContentTypesData );
		// Resetting specific content type
		$classifier->reset( 'blogs' );

		$state = $classifier->get_state();

		verify( $state )->arrayHasNotKey( 'blogs', 'State for a specific content type should be empty after resetting it.' );
		verify( $state )->arrayCount( 2, 'State should contain remaining content types after resetting a specific one.' );

		// Global reset
		$classifier->reset();

		$state = $classifier->get_state();

		verify( $state )->arrayCount( 0, 'State should be completely empty after global reset.' );
	}

	// Additional tests for tokenizer functionality, import/export state, etc., can be added here.
	public function testCustomTokenizerFunctionality() {
		$customTokenizer = function ( $text ) {
			// A simple custom tokenizer that splits on spaces and periods.
			return array_filter(
				preg_split( '/[\s\.]+/', strtolower( $text ) ),
				function ( $word ) {
					return ! empty( $word );
				}
			);
		};

		$classifier = new ContentClassifier( array( 'tokenizer' => $customTokenizer ) );

		// Proceed to train the classifier with some data and assert tokenizer has worked as expected.
		// This could involve checking if the learned word probabilities match expectations
		// after training with known data where the custom tokenizer's behavior would be evident.
	}

	public function testImportget_state() {
		$classifier = new ContentClassifier();

		$this->learnFromSameContentType( $classifier, 'text', $this->sameContentTypeData );

		$exportedState = $classifier->get_state();

		verify( $exportedState )->arrayNotCount( 0, 'Exported state should not be empty.' );

		// Create a new classifier instance and import the state
		$newClassifier = new ContentClassifier();

		$newClassifier->set_state( $exportedState );

		// Assert that the new classifier's exported state matches the original exported state
		verify( $newClassifier->get_state() )->equals( $exportedState, 'Imported state should match exported state.' );
	}

	/**
	 * Learns from multiple text items of the same content type.
	 *
	 * @param ContentClassifier $classifier The classifier instance.
	 * @param string $contentType The content type of the text items.
	 * @param array $textItems An array of text items and their labels. Each item is an array with 'text' and 'labels' keys.
	 */
	function learnFromSameContentType( ContentClassifier $classifier, string $contentType, array $textItems ): void {
		$classifier->reset();

		foreach ( $textItems as $item ) {
			$classifier->learn( $contentType, $item['text'], $item['labels'] );
		}
	}

	/**
	 * Learns from multiple text items of different content types.
	 *
	 * @param ContentClassifier $classifier The classifier instance.
	 * @param array $textItems An array of text items, their content types, and labels. Each item is an array with 'contentType', 'text', and 'labels' keys.
	 */
	function learnFromDifferentContentTypes( ContentClassifier $classifier, array $textItems ): void {
		$classifier->reset();

		foreach ( $textItems as $item ) {
			$classifier->learn( $item['contentType'], $item['text'], $item['labels'] );
		}
	}

	/**
	 * Learns from multiple text items of different content types.
	 *
	 * @param ContentClassifier $classifier The classifier instance.
	 * @param array $textItems An array of text items, their content types, and labels. Each item is an array with 'contentType', 'text', and 'labels' keys.
	 */
	function importState( ContentClassifier $classifier ): void {
		$json = file_get_contents( __DIR__ . '/state.json' );
		$state = json_decode( $json, true );

		$classifier->reset();
		$classifier->set_state( $state );
	}

	function log( $value ) {
		var_dump( $value );
		ob_flush();
	}
}
