<?php
/**
 * This file handles Beaver Builder functionality during import.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Search Beaver Builder content for images to download.
 *
 * @since 1.6
 */
class GeneratePress_Sites_Process_Beaver_Builder {

	/**
	 * Constructor
	 *
	 * @since 1.6
	 */
	public function __construct() {
		$this->image_importer = new GeneratePress_Sites_Image_Importer();
	}

	/**
	 * Import
	 *
	 * @since 1.6
	 * @return void
	 */
	public function import() {
		GeneratePress_Site_Library_Helper::log( '== Start Processing Beaver Builder Images ==' );

		$post_ids = GeneratePress_Site_Library_Helper::get_all_posts();

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				$this->import_single_post( $post_id );
			}
		}
	}

	/**
	 * Update post meta.
	 *
	 * @param  integer $post_id Post ID.
	 * @return void
	 */
	public function import_single_post( $post_id = 0 ) {

		if ( ! empty( $post_id ) ) {

			// Get page builder data.
			$data = get_post_meta( $post_id, '_fl_builder_data', true );

			if ( ! empty( $data ) ) {
				foreach ( $data as $key => $el ) {
					// Import background images.
					if ( 'row' === $el->type || 'column' === $el->type ) {
						$data[ $key ]->settings = $this->import_background_images( $el->settings );
					}

					// Import module images.
					if ( 'module' === $el->type ) {
						$data[ $key ]->settings = $this->import_module_images( $el->settings );
					}
				}

				// Update page builder data.
				update_post_meta( $post_id, '_fl_builder_data', $data );
				update_post_meta( $post_id, '_fl_builder_draft', $data );

				// Clear all cache.
				FLBuilderModel::delete_asset_cache_for_all_posts();
			}
		}
	}

	/**
	 * Import Module Images.
	 *
	 * @param  object $settings Module settings object.
	 * @return object
	 */
	public function import_module_images( $settings ) {

		/**
		 * 1) Set photos.
		 */
		$settings = $this->import_photo( $settings );

		/**
		 * 2) Set `$settings->data` for Only type 'image-icon'
		 *
		 * @todo Remove the condition `'image-icon' === $settings->type` if `$settings->data` is used only for the Image Icon.
		 */
		if ( isset( $settings->data ) && isset( $settings->photo ) && ! empty( $settings->photo ) && 'image-icon' === $settings->type ) {
			$settings->data = FLBuilderPhoto::get_attachment_data( $settings->photo );
		}

		/**
		 * 3) Set `list item` module images
		 */
		if ( isset( $settings->add_list_item ) ) {
			foreach ( $settings->add_list_item as $key => $value ) {
				$settings->add_list_item[ $key ] = $this->import_photo( $value );
			}
		}

		return $settings;
	}

	/**
	 * Helper: Import BG Images.
	 *
	 * @param  object $settings Row settings object.
	 * @return object
	 */
	public function import_background_images( $settings ) {

		if ( ! empty( $settings->bg_image ) && ! empty( $settings->bg_image_src ) ) {
			$image = array(
				'url' => $settings->bg_image_src,
				'id'  => $settings->bg_image,
			);

			$downloaded_image = $this->image_importer->import( $image );

			$settings->bg_image_src = $downloaded_image['url'];
			$settings->bg_image     = $downloaded_image['id'];
		}

		return $settings;
	}

	/**
	 * Helper: Import Photo.
	 *
	 * @param  object $settings Row settings object.
	 * @return object
	 */
	public function import_photo( $settings ) {

		if ( ! empty( $settings->photo ) && ! empty( $settings->photo_src ) ) {
			$image = array(
				'url' => $settings->photo_src,
				'id'  => $settings->photo,
			);

			$downloaded_image = $this->image_importer->import( $image );

			$settings->photo_src = $downloaded_image['url'];
			$settings->photo     = $downloaded_image['id'];
		}

		return $settings;
	}


}
