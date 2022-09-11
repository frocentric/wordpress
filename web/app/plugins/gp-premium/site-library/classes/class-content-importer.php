<?php
/**
 * This file extends the Content Importer.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Extend the Importer.
 */
class GeneratePress_Sites_Content_Importer extends GeneratePress\WPContentImporter2\WXRImporter {
	/**
	 * Constructor method.
	 *
	 * @param array $options Importer options.
	 */
	public function __construct( $options = array() ) {
		parent::__construct( $options );

		// Set current user to $mapping variable.
		// Fixes the [WARNING] Could not find the author for ... log warning messages.
		$current_user_obj = wp_get_current_user();
		$this->mapping['user_slug'][ $current_user_obj->user_login ] = $current_user_obj->ID;
	}

	/**
	 * Get all protected variables from the WXR_Importer needed for continuing the import.
	 */
	public function get_importer_data() {
		return array(
			'mapping' => $this->mapping,
		);
	}

	/**
	 * Sets all protected variables from the WXR_Importer needed for continuing the import.
	 *
	 * @param array $data with set variables.
	 */
	public function set_importer_data( $data ) {
		// phpcs:ignore -- Commented out code for now.
		// $this->mapping            = empty( $data['mapping'] ) ? array() : $data['mapping'];
		// $this->requires_remapping = empty( $data['requires_remapping'] ) ? array() : $data['requires_remapping'];
	}
}
