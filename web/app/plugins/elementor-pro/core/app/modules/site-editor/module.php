<?php
namespace ElementorPro\Core\App\Modules\SiteEditor;

use Elementor\Core\Experiments\Manager;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Frontend\Render_Mode_Manager;
use ElementorPro\Core\App\Modules\SiteEditor\Data\Controller;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use ElementorPro\Plugin;
use ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilderModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Site Editor Module
 *
 * Responsible for initializing Elementor Pro App functionality
 */
class Module extends BaseModule {
	/**
	 * Get name.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'site-editor';
	}

	public function get_template_types() {
		$document_types = Plugin::elementor()->documents->get_document_types( [
			'support_site_editor' => true,
		] );

		// Keep 404 at end of array.
		$error_404 = $document_types['error-404'];
		unset( $document_types['error-404'] );
		$document_types['error-404'] = $error_404;

		// Currently the `single` itself is not supported in site editor.
		// Don't use `support_site_editor=false` in order to support documents that extend it.
		unset( $document_types['single'] );

		$types = [];

		foreach ( $document_types as $type => $class ) {
			$types[] = $class::get_site_editor_config();
		}

		return $types;
	}

	/**
	 * Register ajax actions.
	 *
	 * @access public
	 *
	 * @param Ajax $ajax
	 */
	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'app_site_editor_template_types', [ $this, 'get_template_types' ] );
	}

	/**
	 * @param Render_Mode_Manager $manager
	 *
	 * @throws \Exception
	 */
	public function register_render_mode( Render_Mode_Manager $manager ) {
		$manager->register_render_mode( Render_Mode_Template_Preview::class );
	}

	protected function get_init_settings() {
		return [
			'urls' => [
				'legacy_view' => add_query_arg( 'tabs_group', ThemeBuilderModule::ADMIN_LIBRARY_TAB_GROUP, admin_url( Source_Local::ADMIN_MENU_SLUG ) ),
			],
		];
	}

	private function add_default_new_site_editor_experiments( Manager $manager ) {
		$manager->add_feature( [
			'name' => 'theme_builder_v2',
			'title' => __( 'Default to New Theme Builder', 'elementor-pro' ),
			'description' => __( 'Entering the Theme Builder through WP Dashboard > Templates > Theme Builder opens the New theme builder by default. But don’t worry, you can always view the WP styled version of the screen with a simple click of a button.', 'elementor-pro' ),
			'release_status' => Manager::RELEASE_STATUS_STABLE,
			'default' => Manager::STATE_ACTIVE,
		] );
	}

	private function register_site_editor_menu() {
		if ( ! Plugin::elementor()->experiments->is_feature_active( 'theme_builder_v2' ) ) {
			return;
		}

		// Remove the old theme builder link and add the new one.
		remove_submenu_page(
			Source_Local::ADMIN_MENU_SLUG,
			add_query_arg( 'tabs_group', ThemeBuilderModule::ADMIN_LIBRARY_TAB_GROUP, Source_Local::ADMIN_MENU_SLUG )
		);

		add_submenu_page(
			Source_Local::ADMIN_MENU_SLUG,
			'',
			__( 'Theme Builder', 'elementor-pro' ),
			'publish_posts',
			Plugin::elementor()->app->get_base_url() . '#/site-editor'
		);
	}

	private function add_finder_item( array $categories ) {
		if ( ! Plugin::elementor()->experiments->is_feature_active( 'theme_builder_v2' ) ) {
			return $categories;
		}

		// Replace the old theme builder "create-new" link with the new site-editor.
		$categories['create']['items']['theme-template'] = [
			'title' => __( 'Add New Theme Template', 'elementor-pro' ),
			'icon' => 'plus-circle-o',
			'url' => Plugin::elementor()->app->get_base_url() . '#/site-editor/add-new',
			'keywords' => [ 'template', 'theme', 'new', 'create' ],
		];

		return $categories;
	}

	/**
	 * Module constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		Plugin::elementor()->data_manager->register_controller( Controller::class );

		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ], 11 /* Override core actions */ );
		add_action( 'elementor/frontend/render_mode/register', [ $this, 'register_render_mode' ] );

		add_action( 'elementor/experiments/default-features-registered', function ( Manager $manager ) {
			$this->add_default_new_site_editor_experiments( $manager );
		} );

		add_action( 'admin_menu', function () {
			$this->register_site_editor_menu();
		}, 23 /* After old theme builder */ );

		add_filter( 'elementor/finder/categories', function ( array $categories ) {
			return $this->add_finder_item( $categories );
		}, 11 /* After old theme builder */ );

	}
}
