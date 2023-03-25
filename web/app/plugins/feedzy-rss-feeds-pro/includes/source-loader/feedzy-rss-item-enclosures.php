<?php
/**
 * Get item enclosures.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.7.1
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */

/**
 * Get item enclosures.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
class Feedzy_Rss_Item_Enclosures {

	/**
	 * Item data.
	 *
	 * @var object $item
	 */
	private $item;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $item Item data.
	 */
	public function __construct( $item ) {
		$this->item = $item;
	}

	/**
	 * Get item thumbnail image.
	 *
	 * @return string
	 */
	public function get_thumbnail() {
		return $this->item->getImages()->getPrimary()->getLarge()->getURL();
	}

	/**
	 * Item embed content.
	 *
	 * @return false
	 */
	public function embed() {
		return false;
	}

	/**
	 * Get item link.
	 */
	public function get_link() {
	}

	/**
	 * Get author name.
	 */
	public function get_name() {
		$item_info   = $this->item->getItemInfo();
		$author_name = '';
		if ( $item_info->getByLineInfo()->getManufacturer() ) {
			$author_name = $item_info->getByLineInfo()->getManufacturer()->getDisplayValue();
		}
		if ( $item_info->getByLineInfo()->getBrand() ) {
			$author_name = $item_info->getByLineInfo()->getBrand()->getDisplayValue();
		}
		return $author_name;
	}

	/**
	 * Get author email.
	 */
	public function get_email() {
		return null;
	}

	/**
	 * Get medium.
	 */
	public function get_medium() {
		return $this->item->getImages()->getPrimary()->getMedium();
	}
}
