<?php
/**
 * Register feedzy dynamic tag for item image
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 */
class Feedzy_Feed_Items_Image extends \Elementor\Core\DynamicTags\Data_Tag {

	/**
	 * Get dynamic tag categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array(
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
		);
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
	 * Get Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Feedzy item image', 'feedzy-rss-feeds' );
	}

	/**
	 * Get Name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'feedzy-item-image';
	}

	/**
	 * Get tag value.
	 *
	 * @param array $options Available options.
	 */
	public function get_value( array $options = array() ) {
		$item_img = FEEDZY_ABSURL . '/img/feedzy.svg?tag=[#item_img]';
		return array(
			'id'  => 0,
			'url' => $item_img,
		);
	}
}
