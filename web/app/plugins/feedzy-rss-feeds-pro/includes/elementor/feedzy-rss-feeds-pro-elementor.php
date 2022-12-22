<?php
/**
 * The PRO elementor widget functionality of the plugin.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 */

/**
 * The PRO Widget functionality of the plugin.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/elementor
 * @author     Themeisle <friends@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro_Elementor {


	/**
	 * Elementor register feedzy dynamic feed item tags.
	 *
	 * @param object $dynamic_tags Dynamic tags class object.
	 */
	public function feedzy_elementor_register_dynamic_tags( $dynamic_tags ) {
		$post_id = get_the_ID();
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
		$page_settings_model = $page_settings_manager->get_model( $post_id );
		$template_id = $page_settings_model->get_data( 'id' );
		$elementor_type = get_post_meta( $template_id, '_elementor_template_type', true );
		if ( $elementor_type === 'feedzy-loop' ) {
			$dynamic_tags->register_group(
				'feedzy-feed-items',
				array(
					'title' => __( 'Feedzy Feed Items', 'feedzy-rss-feeds' ),
				)
			);
		}
		// Include the Dynamic tag class file.
		require_once FEEDZY_PRO_ABSPATH . '/includes/elementor/dynamic-tags/feedzy-rss-items.php';
		require_once FEEDZY_PRO_ABSPATH . '/includes/elementor/dynamic-tags/feedzy-rss-item-image.php';

		// Register feedzy feed item tags.
		$dynamic_tags->register_tag( 'Feedzy_Feed_Items' );
		$dynamic_tags->register_tag( 'Feedzy_Feed_Items_Image' );
	}

	/**
	 * Elementor register feedzy loop template.
	 *
	 * @param array $types template library types.
	 * @param array $document_types document types.
	 * @return array
	 */
	public function feedzy_elementor_dialog_types( $types, $document_types ) {
		$types['feedzy-loop'] = __( 'Feedzy Loop', 'feedzy-rss-feeds' );
		return $types;
	}

	/**
	 * Elementor register feedzy loop class.
	 *
	 * @param object $elementor Elementor class object.
	 */
	public function feedzy_elementor_register_document( $elementor ) {
		require_once FEEDZY_PRO_ABSPATH . '/includes/elementor/library-document/feedzy-rss-loop-template.php';
		$elementor->register_document_type( 'feedzy-loop', 'Feedzy_Loop' );
	}

	/**
	 * Register feedzy widget.
	 */
	public function feedzy_elementor_widgets_registered() {
		// We check if the Elementor plugin has been installed / activated.
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ) {
				require_once FEEDZY_PRO_ABSPATH . '/includes/elementor/widgets/feedzy-rss-loop-widget.php';
				\Elementor\Plugin::instance()->widgets_manager->register( new Widget_Feedzy_Loop() );
			}
		}
	}
}
