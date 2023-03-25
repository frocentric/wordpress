<?php
/**
 * Register all other API item data objects.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.7.1
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */

/**
 * Register all other API item data objects.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 */
class Feedzy_Rss_Get_Item {

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
	 * Get item ID.
	 *
	 * @param bool $hash MD5 string.
	 * @return string
	 */
	public function get_id( $hash = false ) {
		if ( $hash ) {
			return md5( $this->item->getASIN() );
		}
		return $this->item->getASIN();
	}

	/**
	 * Get title.
	 *
	 * @return string Item title.
	 */
	public function get_title() {
		return $this->item->getItemInfo()->getTitle()->getDisplayValue();
	}

	/**
	 * Get URL.
	 *
	 * @return string Item URL.
	 */
	public function get_permalink() {
		return $this->item->getDetailPageURL();
	}

	/**
	 * Get content.
	 *
	 * @return string Item URL.
	 */
	public function get_content() {
		$content_info      = '';
		$item_info         = $this->item->getItemInfo();
		$item_features     = $item_info->getFeatures()->getDisplayValues();
		$item_features[0] .= '.';
		$item_features     = array_map(
			function( $content ) {
				preg_match( '~.*?[?.!]~s', $content, $sentences );
				if ( empty( $sentences ) ) {
					$content .= '.';
				}
				return $content;
			},
			$item_features
		);
		$item_features     = implode( "\r\n\r\n", $item_features );
		if ( $item_info->getContentInfo() ) {
			$content_info = $item_info->getContentInfo()->getEdition();
		}
		return ! empty( $content_info ) && is_string( $content_info ) ? $content_info : $item_features;
	}

	/**
	 * Get author.
	 *
	 * @return string Item author.
	 */
	public function get_author() {
		return new Feedzy_Rss_Item_Enclosures( $this->item );
	}

	/**
	 * Get date.
	 *
	 * @param string $date_format Date format.
	 * @return string Item author.
	 */
	public function get_date( $date_format = 'j F Y, g:i a' ) {
		if ( $this->item->getItemInfo()->getProductInfo()->getReleaseDate() ) {
			$release_date = $this->item->getItemInfo()->getProductInfo()->getReleaseDate()->getDisplayValue();
			return date_i18n( $date_format, strtotime( $release_date ) );
		}
		return null;
	}

	/**
	 * Get item description.
	 *
	 * @return string Item short description.
	 */
	public function get_description() {
		$item_info     = $this->item->getItemInfo();
		$item_features = $item_info->getFeatures()->getDisplayValues();
		return is_array( $item_features ) ? reset( $item_features ) : '';
	}

	/**
	 * Get item description.
	 *
	 * @return object item object.
	 */
	public function get_feed() {
		return new \Feedzy_Rss_Feeds_Pro_Amazon_Product_Advertising();
	}

	/**
	 * Get enclosures info.
	 *
	 * @return array.
	 */
	public function get_enclosures() {
		return array( new Feedzy_Rss_Item_Enclosures( $this->item ) );
	}

	/**
	 * Get enclosure info.
	 */
	public function get_enclosure() {
		return null;
	}

	/**
	 * Get item tags.
	 *
	 * @param string $namespace Item namespace.
	 * @param string $tag Custom TagName.
	 * @return array
	 */
	public function get_item_tags( $namespace, $tag ) {
		if ( 'price' === $tag ) {
			return array(
				array(
					'data' => $this->item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount(),
				),
			);
		}
		return array();
	}

	/**
	 * Get item categories.
	 */
	public function get_categories() {
		if ( method_exists( $this->item, 'getBrowseNodeInfo' ) ) {
			return array_map(
				function( $node ) {
					return $node->getContextFreeName();
				},
				$this->item->getBrowseNodeInfo()->getBrowseNodes()
			);
		}
		return null;
	}
}
