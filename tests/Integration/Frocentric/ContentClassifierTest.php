<?php
/**
 * ContentClassifier WP unit test suite
 */

namespace Tests\Frocentric;

use Frocentric\Customizations\ContentClassifier;

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

	public function test_classify(): void {
		$content = 'In celebration of Black History Month this social games night Coding Black Females & Black Create Connect. You\'re invited to an exciting night of learning sharing and celebrating Black Women\'s History Game Night on Tuesday, October 31 at 6pm. Let\'s recognise and celebrate the impact and achievements made within the tech space. Join us for games, drinks, and delicious food, hosted by Coding Black Females and Black Create Connect. If you\'re looking to play some games, network with our communities, and have a laugh then we\'d love to have you. Don\'t miss this fun and educational evening - all ages are welcome! See you there! **Limited Spaces Available** We can\'t wait to see you there! Venue: Zetland House, Floor 2, 5-25 Scrutton Street, London EC2A 4HJ';
		$taxonomies = array(
			'discipline',
			'audience',
			'interest',
		);
		// $taxonomies = json_decode( '{ "discipline": [ "affiliate-marketing", "agile", "animation", "artificial-intelligence", "audio-production", "augmented-reality", "automation", "blockchain", "brand-design", "cloud-computing", "computer-vision", "content-marketing", "cryptocurrency", "cybersecurity", "data", "data-analysis", "data-engineering", "data-science", "data-visualisation", "database-administration", "design", "desktop-development", "devops", "digital-marketing", "email-marketing", "game-development", "graphic-design", "hardware-engineering", "iaas", "infosec", "infrastructure", "internet-of-things", "it-support", "machine-learning", "media-production", "mixed-reality", "mobile-advertising", "mobile-development", "network-administration", "no-code", "paas", "ppc-advertising", "product-management", "project-management", "robotics", "saas", "search-engine-marketing", "search-engine-optimisation", "security", "site-reliability-engineering", "social-media-marketing", "software-engineering", "systems-administration", "telecoms", "testing", "ui-design", "ux-design", "video-production", "viral-marketing", "virtual-reality", "web-development" ], "audience": [ "children", "disabled", "lgbtq", "neurodiverse", "parents", "students", "teens", "women" ], "category": [ "awards", "conference", "graduation", "hackathon", "launch", "lecture", "networking", "panel", "pitch", "recruitment", "social", "speech", "training", "workshop" ] }', true );
		// Test classification
		$classification = ContentClassifier::classify( $content, $taxonomies );

		$this->log( $classification );
		verify( $classification )->arrayHasKey( 'discipline', 'Classification should contain quality labels.' );
	}

	public function test_extract_json_object() {
		$content = '```json\n{"content":"In celebration of Black History Month this social games night Coding Black Females & Black Create Connect. You\'re invited to an exciting night of learning sharing and celebrating Black Women\'s History Game Night on Tuesday, October 31 at 6pm. Let\'s recognise and celebrate the impact and achievements made within the tech space. Join us for games, drinks, and delicious food, hosted by Coding Black Females and Black Create Connect. If you\'re looking to play some games, network with our communities, and have a laugh then we\'d love to have you. Don\'t miss this fun and educational evening - all ages are welcome! See you there! **Limited Spaces Available** We can\'t wait to see you there! Venue: Zetland House, Floor 2, 5-25 Scrutton Street, London EC2A 4HJ","taxonomies":{"audience":["Men","Women","Students","Junior","Mid-level","Senior","Leadership"]}}\n```';
		$result = ContentClassifier::extract_json_object( $content );

		$this->log( $result );
		$this->assertIsArray( $result );
	}

	public function log( $value ) {
		echo( "\n" );
		var_dump( $value );
		ob_flush();
	}
}
