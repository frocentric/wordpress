<?php
/**
 * Handles the integration with Elementor.
 *
 * @since   5.1.4
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */

namespace Tribe\Events\Pro\Integrations\Elementor;

use Elementor\Elements_Manager;

/**
 * Class Service_Provider
 *
 * @since   5.1.4
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Registers the bindings and hooks the filters required for the Elementor integration to work.
	 *
	 * @since 5.1.4
	 */
	public function register() {
		$this->container->singleton( Shortcodes::class, Shortcodes::class );

		// Support Elementor widgets if views v2 is enabled.
		if ( tribe_events_views_v2_is_enabled() ) {
			$this->container->singleton( Widgets\Widget_Countdown::class, Widgets\Widget_Countdown::class );
			$this->container->singleton( Widgets\Widget_Event_List::class, Widgets\Widget_Event_List::class );
			$this->container->singleton( Widgets\Widget_Event_Single_Legacy::class, Widgets\Widget_Event_Single_Legacy::class );
			$this->container->singleton( Widgets\Widget_Events_View::class, Widgets\Widget_Events_View::class );
		}

		// Register the hooks related to this integration.
		$this->register_hooks();
	}

	/**
	 * Register the hooks for Elementor integration.
	 *
	 * @since 5.4.0
	 */
	public function register_hooks() {
		// Hook on the AJAX call Elementor will make during edits to support the archive shortcodes.
		add_action( 'wp_ajax_elementor_ajax', [ $this, 'support_archive_shortcode' ] );

		if ( ! tribe_events_views_v2_is_enabled() ) {
			return;
		}

		add_action( 'elementor/widgets/widgets_registered', [ $this, 'action_register_widgets_manager_registration' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'action_register_elementor_category' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'action_register_elementor_controls' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'action_enqueue_resources' ] );
	}

	/**
	 * Builds and hooks the class that will handle shortcode support in the context of Elementor.
	 *
	 * @since 5.1.4
	 */
	public function support_archive_shortcode() {
		add_filter( 'do_shortcode_tag', [ $this->container->make( Shortcodes::class ), 'support_archive_shortcode' ], 10, 2 );
	}

	/**
	 * Registers controls for Elementor.
	 *
	 * @since 5.4.0
	 */
	public function action_register_elementor_controls() {
		return $this->container->make( Controls_Manager::class )->register();
	}

	/**
	 * Registers widgets for Elementor.
	 *
	 * @since 5.4.0
	 */
	public function action_register_widgets_manager_registration() {
		return $this->container->make( Widgets_Manager::class )->register();
	}

	/**
	 * Registers widget categories for Elementor.
	 *
	 * @since 5.4.0
	 *
	 * @param Elements_Manager $elements_manager Elementor Manager instance.
	 */
	public function action_register_elementor_category( $elements_manager ) {
		$elements_manager->add_category(
			'the-events-calendar',
			[
				'title' => __( 'The Events Calendar', 'tribe-events-calendar-pro' ),
				'icon'  => 'fa fa-calendar-alt',
			]
		);
	}

	/**
	 * Enqueue widget resources.
	 *
	 * @since 5.4.0
	 */
	public function action_enqueue_resources() {
		$this->container[ Widgets\Widget_Countdown::class ]->enqueue_editor_assets();
		$this->container[ Widgets\Widget_Event_List::class ]->enqueue_editor_assets();
		$this->container[ Widgets\Widget_Event_Single_Legacy::class ]->enqueue_editor_assets();
		$this->container[ Widgets\Widget_Events_View::class ]->enqueue_editor_assets();
	}
}
