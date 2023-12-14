<?php
/**
 * Register feedzy library document
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 */

use Elementor\Core\Base\Document;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\User;

/**
 * Register feedzy loop template.
 */
class Feedzy_Loop extends Library_Document {

	/**
	 * Get properties.
	 *
	 * @return array
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = 'library';
		$properties['show_in_library'] = true;
		$properties['is_editable'] = true;

		return $properties;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'feedzy-loop';
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return __( 'Feedzy Loop', 'feedzy-rss-feeds' );
	}

	/**
	 * Get plural name.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return __( 'Feedzy Loops', 'feedzy-rss-feeds' );
	}

	/**
	 * Is editable for current user.
	 *
	 * @return bool
	 */
	public function is_editable_by_current_user() {
		return User::is_current_user_can_edit( $this->get_main_id() );
	}

	/**
	 * Save template.
	 *
	 * @param  array $data Data.
	 * @return array
	 */
	public function save( $data ) {
		// Since the method of 'modules/usage::before_document_save' will remove from global if new_status is the same as old.
		$data['settings'] = array( 'post_status' => Document::STATUS_PUBLISH );

		return parent::save( $data );
	}
}
