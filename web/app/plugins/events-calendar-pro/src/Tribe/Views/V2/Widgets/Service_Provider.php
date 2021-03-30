<?php
/**
 * The main service provider for version 2 of the Pro Widgets.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Widgets\Widget_List;
use Tribe\Events\Pro\Views\V2\Views\Widgets\Countdown_View;
use Tribe\Events\Pro\Views\V2\Views\Widgets\Venue_View;

/**
 * Class Service_Provider
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Variable that holds the name of the widgets being created.
	 *
	 * @since 5.2.0
	 *
	 * @var array<string>
	 */
	protected $widgets = [
		// 'widget-mini-calendar',
	];

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added Countdown Widget, separated shortcode hooks.
	 */
	public function register() {
		// Activate the compatibility coding for V1 and V2 Event List Widgets.
		add_filter( 'tribe_events_views_v2_advanced_list_widget_primary', '__return_true' );

		// Determine if V2 widgets should load.
		if ( ! tribe_events_widgets_v2_is_enabled() ) {
			return;
		}

		$this->register_hooks();
		$this->hook_widgets();
		$this->hook_widget_shortcodes();
	}

	/**
	 * Registers the provider handling for first level v2 widgets.
	 *
	 * @since 5.2.0
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'pro.views.v2.widgets.hooks', $hooks );

		$this->container->singleton( 'pro.views.v2.widgets.taxonomy', Taxonomy_Filter::class );
	}

	/**
	 * Function used to attach the widget hooks associated with this class.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added Countdown Widget, separated shortcode hooks.
	 */
	public function hook_widgets() {
		add_filter( 'tribe_widgets', [ $this, 'register_widget' ] );
		add_filter( 'tribe_events_views', [ $this, 'add_views' ] );
	}

	/**
	 * Function used to attach the shortcode hooks associated with this class.
	 *
	 * @since 5.3.0 Separated shortcode hooks. Renamed alter_widget_class function to indicate it is specific to the List Widget.
	 */
	public function hook_widget_shortcodes() {
		add_filter( 'tribe_events_pro_shortcodes_list_widget_class', [ $this, 'alter_list_widget_class' ], 10, 2 );
		add_filter( 'tribe_events_pro_shortcodes_countdown_widget_class', [ $this, 'alter_countdown_widget_class' ], 10, 2 );
		add_filter( 'tribe_events_pro_shortcodes_venue_widget_class', [ $this, 'alter_venue_widget_class' ], 10, 2 );
	}

	/**
	 * Add the widgets to register with WordPress.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added Countdown Widget.
	 *
	 * @param array<string,string> $widgets An array of widget classes to register.
	 *
	 * @return array<string,string> An array of registered widget classes.
	 */
	public function register_widget( $widgets ) {
		$widgets['tribe_events_countdown_widget']      = Widget_Countdown::class;
		$widgets['tribe_events_featured_venue_widget'] = Widget_Featured_Venue::class;

		return $widgets;
	}

	/**
	 * Add the widget views to the view manager.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added Countdown Widget view.
	 *
	 * @param array<string,string> $views An associative array of views in the shape `[ <slug> => <class> ]`.
	 *
	 * @return array<string,string> $views The modified array of views in the shape `[ <slug> => <class> ]`.
	 */
	public function add_views( $views ) {
		$views['widget-countdown']      = Countdown_View::class;
		$views['widget-featured-venue'] = Venue_View::class;

		return $views;
	}

	/**
	 * Swaps in the new V2 widget for the old one in the widget shortcode.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 renamed to indicate this is specific to the List Widget.
	 *
	 * @param string              $widget_class The widget class name we're currently implementing.
	 * @param array<string,mixed> $arguments    The widget arguments.
	 *
	 * @return string             $widget_class The modified (V2) widget class name we want to implement.
	 */
	public function alter_list_widget_class( $widget_class, $arguments ) {
		return Widget_List::class;
	}

	/**
	 * Swaps in the new Countdonw V2 widget for the old one in the widget shortcode.
	 *
	 * @since 5.3.0
	 *
	 * @param string              $widget_class The widget class name we're currently implementing.
	 * @param array<string,mixed> $arguments    The widget arguments.
	 *
	 * @return string             $widget_class The modified (V2) widget class name we want to implement.
	 */
	public function alter_countdown_widget_class( $widget_class, $arguments ) {
		return Widget_Countdown::class;
	}

	/**
	 * Swaps in the new Featured Venue V2 widget for the old one in the widget shortcode.
	 *
	 * @since 5.3.0
	 *
	 * @param string              $widget_class The widget class name we're currently implementing.
	 * @param array<string,mixed> $arguments    The widget arguments.
	 *
	 * @return string             $widget_class The modified (V2) widget class name we want to implement.
	 */
	public function alter_venue_widget_class( $widget_class, $arguments ) {
		return Widget_Featured_Venue::class;
	}
}
