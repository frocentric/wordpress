<?php
/**
 * ContentClassifier WP unit test suite
 */

namespace Tests\Frocentric;

use Frocentric\ContentClassifier;

class ContentClassifierTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * @var \IntegrationTester
	 */
	protected $tester;

	protected $same_content_type_data = array(
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

	protected $different_content_types_data = array(
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

	public function setUp(): void {
		// Before...
		parent::setUp();

		// Your set up methods here.
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', '' );
		}
	}

	public function tearDown(): void {
		// Your tear down methods here.
		// Then...
		parent::tearDown();
	}

	// Tests
	public function test_factory(): void {
		$post = static::factory()->post->create_and_get();

		$this->assertInstanceOf( \WP_Post::class, $post );
	}

	public function test_classification() {
		$classifier = new ContentClassifier();

		// Test classification
		$classification = $classifier->classify( 'post', 'Very pleased with the product' );

		$this->log( $classification );
		verify( $classification )->arrayHasKey( 'content', 'Classification should contain quality labels.' );
	}

	public function log( $value ) {
		var_dump( $value );
		ob_flush();
	}
}
