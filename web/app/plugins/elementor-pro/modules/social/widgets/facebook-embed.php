<?php
namespace ElementorPro\Modules\Social\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorPro\Modules\Social\Classes\Facebook_SDK_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Facebook_Embed extends Widget_Base {

	public function get_name() {
		return 'facebook-embed';
	}

	public function get_title() {
		return __( 'Facebook Embed', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-fb-embed';
	}

	public function get_categories() {
		return [ 'pro-elements' ];
	}

	public function get_keywords() {
		return [ 'facebook', 'social', 'embed', 'video', 'post', 'comment' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Embed', 'elementor-pro' ),
			]
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post' => __( 'Post', 'elementor-pro' ),
					'video' => __( 'Video', 'elementor-pro' ),
					'comment' => __( 'Comment', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'post_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'default' => 'https://www.facebook.com/elemntor/posts/1823653464612271',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'post',
				],
				'description' => __( 'Hover over the date next to the post, and copy its link address.', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'video_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'default' => 'https://www.facebook.com/elemntor/videos/1683988961912056/',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'video',
				],
				'description' => __( 'Hover over the date next to the video, and copy its link address.', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'comment_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'default' => 'https://www.facebook.com/elemntor/videos/1811703749140576/?comment_id=1812873919023559',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'type' => 'comment',
				],
				'description' => __( 'Hover over the date next to the comment, and copy its link address.', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'include_parent',
			[
				'label' => __( 'Parent Comment', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Set to include parent comment (if URL is a reply).', 'elementor-pro' ),
				'condition' => [
					'type' => 'comment',
				],
			]
		);

		$this->add_control(
			'show_text',
			[
				'label' => __( 'Full Post', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Show the full text of the post', 'elementor-pro' ),
				'condition' => [
					'type' => [ 'post', 'video' ],
				],
			]
		);

		$this->add_control(
			'video_allowfullscreen',
			[
				'label' => __( 'Allow Full Screen', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type' => 'video',
				],
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label' => __( 'Autoplay', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type' => 'video',
				],
			]
		);

		$this->add_control(
			'video_show_captions',
			[
				'label' => __( 'Captions', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Show captions if available (only on desktop).', 'elementor-pro' ),
				'condition' => [
					'type' => 'video',
				],
			]
		);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['type'] ) ) {
			esc_html_e( 'Please set the embed type', 'elementor-pro' );

			return;
		}

		if ( 'comment' === $settings['type'] && empty( $settings['comment_url'] ) || 'post' === $settings['type'] && empty( $settings['post_url'] ) || 'video' === $settings['type'] && empty( $settings['video_url'] ) ) {
			esc_html_e( 'Please enter a valid URL', 'elementor-pro' );

			return;
		}

		$attributes = [
			// The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
			'style' => 'min-height: 1px',
		];

		switch ( $settings['type'] ) {
			case 'comment':
				$attributes['class'] = 'elementor-facebook-widget fb-comment-embed';
				$attributes['data-href'] = esc_url( $settings['comment_url'] );
				$attributes['data-include-parent'] = 'yes' === $settings['include_parent'] ? 'true' : 'false';
				break;
			case 'post':
				$attributes['class'] = 'elementor-facebook-widget fb-post';
				$attributes['data-href'] = esc_url( $settings['post_url'] );
				$attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
				break;
			case 'video':
				$attributes['class'] = 'elementor-facebook-widget fb-video';
				$attributes['data-href'] = esc_url( $settings['video_url'] );
				$attributes['data-show-text'] = 'yes' === $settings['show_text'] ? 'true' : 'false';
				$attributes['data-allowfullscreen'] = 'yes' === $settings['video_allowfullscreen'] ? 'true' : 'false';
				$attributes['data-autoplay'] = 'yes' === $settings['video_autoplay'] ? 'true' : 'false';
				$attributes['data-show-captions'] = 'yes' === $settings['video_show_captions'] ? 'true' : 'false';
				break;
		}

		$this->add_render_attribute( 'embed_div', $attributes );

		echo '<div ' . $this->get_render_attribute_string( 'embed_div' ) . '></div>'; // XSS ok.
	}

	public function render_plain_content() {}
}
