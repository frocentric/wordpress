<?php
/**
 * Register feedzy dynamic tag
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 */
class Feedzy_Feed_Items extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Get Name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'feedzy-feed-items';
	}

	/**
	 * Get Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Feedzy Feed Items', 'feedzy-rss-feeds' );
	}

	/**
	 * Get Group.
	 *
	 * @return string
	 */
	public function get_group() {
		return 'feedzy-feed-items';
	}

	/**
	 * Get dynamic tag categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array(
			\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY,
		);
	}

	/**
	 * Option setting option.
	 */
	public function is_settings_required() {
		return true;
	}

	/**
	 * Register Controls.
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$feed_tags = array(
			'[#item_url]' => __( 'Item URL', 'feedzy-rss-feeds' ),
			'[#item_title]' => __( 'Item title', 'feedzy-rss-feeds' ),
			'[#item_date]' => __( 'Item date', 'feedzy-rss-feeds' ),
			'[#item_date_local]' => __( 'Item local date', 'feedzy-rss-feeds' ),
			'[#item_date_feed]' => __( 'Item feed date', 'feedzy-rss-feeds' ),
			'[#item_author]' => __( 'Item author', 'feedzy-rss-feeds' ),
			'[#item_description]' => __( 'Item Description', 'feedzy-rss-feeds' ),
			'[#item_content]' => __( 'Item Content', 'feedzy-rss-feeds' ),
			'[#item_source]' => __( 'Item source', 'feedzy-rss-feeds' ),
		);
		$this->add_control(
			'feed_tags',
			array(
				'label'   => __( 'Feed tags', 'feedzy-rss-feeds' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $feed_tags,
			)
		);
	}

	/**
	 * Render
	 *
	 * @return void
	 */
	public function render() {
		$param_name = $this->get_settings( 'feed_tags' );

		if ( ! $param_name ) {
			return;
		}

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo wp_kses_post( $param_name );
			return;
		}

		// Replace shortcode to placeholders.
		$param_name = str_replace(
			array(
				'[#item_url]',
				'[#item_title]',
				'[#item_date]',
				'[#item_date_local]',
				'[#item_date_feed]',
				'[#item_author]',
				'[#item_description]',
				'[#item_content]',
				'[#item_source]',
			),
			array(
				esc_url( home_url() ),
				__( 'Feed item title', 'feedzy-rss-feeds' ),
				__( 'Feed item date', 'feedzy-rss-feeds' ),
				__( 'Feed item local date', 'feedzy-rss-feeds' ),
				__( 'Feed date', 'feedzy-rss-feeds' ),
				__( 'Feed item author', 'feedzy-rss-feeds' ),
				__( 'Feed item description', 'feedzy-rss-feeds' ),
				__( 'Feed item content', 'feedzy-rss-feeds' ),
				__( 'Feed item source', 'feedzy-rss-feeds' ),
			),
			$param_name
		);

		echo wp_kses_post( $param_name );
	}
}
