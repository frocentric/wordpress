<?php
namespace Tribe\Events\Pro\Integrations\Elementor;

use Elementor\Plugin as Elementor_Plugin;

/**
 * Class Widget_Manager
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
class Widgets_Manager extends Manager_Abstract {
	/**
	 * {@inheritdoc}
	 */
	protected $type = 'widgets';

	/**
	 * Constructor
	 *
	 * @since 5.4.0
	 */
	public function __construct() {
		$this->objects = [
			Widgets\Widget_Countdown::get_slug()           => Widgets\Widget_Countdown::class,
			Widgets\Widget_Event_List::get_slug()          => Widgets\Widget_Event_List::class,
			Widgets\Widget_Event_Single_Legacy::get_slug() => Widgets\Widget_Event_Single_Legacy::class,
			Widgets\Widget_Events_View::get_slug()         => Widgets\Widget_Events_View::class,
		];
	}

	/**
	 * Registers the widgets with Elementor.
	 *
	 * @since 5.4.0
	 */
	public function register() {
		$widgets = $this->get_registered_objects();

		foreach ( $widgets as $slug => $widget_class ) {
			Elementor_Plugin::instance()->widgets_manager->register_widget_type( tribe( $widget_class ) );
		}
	}
}