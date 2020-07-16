<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Events\Filterbar\Views\V2\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'filterbar.views.v2.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Events\Filterbar\Views\V2\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'filterbar.views.v2.hooks' ), 'some_method' ] );
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */

namespace Tribe\Events\Filterbar\Views\V2;

use Tribe\Events\Views\V2\View_Interface;
use Tribe__Context as Context;

/**
 * Class Hooks.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2
 */
class Hooks extends \tad_DI52_ServiceProvider {
	/**
	 * Whether filters should render at all or not.
	 *
	 * @var bool
	 */
	protected $should_display_filters = true;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9.0
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by each Filterbar Views v2 component.
	 *
	 * @since 4.9.0
	 */
	protected function add_actions() {
		add_action( 'tribe_template_after_include:events/v2/components/filter-bar', [ $this, 'action_include_filter_bar' ], 10, 3 );
		add_action( 'tribe_events_filter_view_do_display_filters', [ $this, 'display_filters' ] );
		add_action( 'tribe_events_pro_shortcode_tribe_events_before_assets', [ $this, 'action_include_assets' ] );
	}

	/**
	 * Adds the filters required by each Filter bar Views v2 component.
	 *
	 * @since 4.9.0
	 */
	protected function add_filters() {
		/*
		 * In the context of Views V2 filters will work differently and their hooking to queries is handled
		 * in the `tribe_events_filter_bar_initialize_filters` filter.
		 */
		add_filter( 'tribe_events_filter_bar_initialize_filters', '__return_false' );
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_view_repository_args' ], 5, 2 );
		add_filter( 'tribe_events_views_v2_url_query_args', [ $this, 'filter_view_url_query_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_rest_params', [ $this, 'filter_view_rest_params' ], 10, 2 );
		add_filter( 'body_class', [ $this, 'filter_body_class' ] );
		add_filter( 'tribe_events_views_v2_cache_html_expiration', [ $this, 'filter_cache_html_expiration' ] );
	}

	/**
	 * Fires to include the Filters container on the Events bar.
	 *
	 * @since 4.9.0
	 *
	 * @param string $file     Complete path to include the PHP File.
	 * @param array  $name     Template name.
	 * @param self   $template Current instance of the Tribe__Template.
	 *
	 * @return string          HTML for template.
	 */
	public function action_include_filters_container( $file, $name, $template ) {
		return $this->container->make( Template::class )->template( 'filters', $template->get_values() );
	}

	/**
	 * Fires to include the search filter tabs on the Events bar.
	 *
	 * @since 4.9.0
	 *
	 * @param string $file     Complete path to include the PHP File.
	 * @param array  $name     Template name.
	 * @param self   $template Current instance of the Tribe__Template.
	 *
	 * @return string          HTML template.
	 */
	public function action_include_tabs( $file, $name, $template ) {
		return $this->container->make( Template::class )->template( 'tabs', $template->get_values() );
	}

	/**
	 * Fires to include the filter bar assets on shortcodes.
	 *
	 * @since 4.9.0
	 */
	public function action_include_assets() {
		return $this->container->make( Assets::class )->load_on_shortcode();
	}

	/**
	 * Fires to include the search button icon on the Events bar.
	 *
	 * @since 4.9.0
	 *
	 * @param string $file     Complete path to include the PHP File.
	 * @param array  $name     Template name.
	 * @param self   $template Current instance of the Tribe__Template.
	 *
	 * @return string          HTML template.
	 */
	public function action_include_search_button_icon( $file, $name, $template ) {
		return $this->container->make( Template::class )->template( 'icon', $template->get_values() );
	}

	/**
	 * Fires to include the filter bar.
	 *
	 * @since 4.9.0
	 *
	 * @param string                          $file     Complete path to include the PHP File.
	 * @param array                           $name     Template name.
	 * @param \Tribe\Events\Views\V2\Template $template Current instance of the template.
	 *
	 * @return string|void
	 */
	public function action_include_filter_bar( $file, $name, $template ) {

		// Prevent Including the Filter Bar if on a Shortcode
		$context = $template->get_context();
		if ( $context->get( 'shortcode'  ) ) {
			return;
		}

		if ( ! $this->container->make( Filters::class )->should_display_filters( $template->get_view() ) ) {
			return;
		}

		return $this->container->make( Template::class )->template( 'filter-bar', $template->get_values() );
	}

	/**
	 * Filters a View repository args to add the filters ones.
	 *
	 * @since 4.9.0
	 *
	 * @param array   $args    An array of repository arguments.
	 * @param Context $context The View context instance.
	 *
	 * @return array The filtered array of View repository arguments.
	 */
	public function filter_view_repository_args( array $args, Context $context ) {
		return $this->container->make( Filters\Factory::class )->for_repository_args( $args, $context );
	}

	/**
	 * Modifies the rest Params to reflect required modifications to make filterbar work as expected.
	 *
	 * @since 4.9.0
	 *
	 * @param array            $params   The Rest params to filter.
	 * @param \WP_REST_Request $request  WP Rest Request used.
	 *
	 * @return array The filtered params.
	 */
	public function filter_view_rest_params( array $params, $request ) {
		return $this->container->make( Filters\Factory::class )->for_rest_params( $params, $request );
	}

	/**
	 * Filters the query arguments Views will use to build their own URL.
	 *
	 * Using the context we'll know what filter are applied and what keys and values to add to the query args.
	 *
	 * @since 4.9.0
	 *
	 * @param array          $query_args The current URL query arguments.
	 * @param View_Interface $view       The instance of the View the URL is being built for.
	 *
	 * @return array The filtered array of URL query arguments.
	 */
	public function filter_view_url_query_args( array $query_args, View_Interface $view ) {
		return $this->container->make( Filters\Url::class )->filter_view_query_args( $query_args, $view );
	}

	/**
	 * Builds each filter according to the current context to display it.
	 *
	 * @since 4.9.0
	 */
	public function display_filters( $context ) {
		if ( false !== $context->get( 'shortcode', false ) ) {
			return;
		}

		$this->container->make( Filters\Factory::class )->for_display( $context );
	}

	/**
	 * Filters the body classes to add theme compatibility ones.
	 *
	 * @since 4.9.0
	 *
	 * @param  array $classes Classes that are been passed to the body.
	 *
	 * @return array $classes
	 */
	public function filter_body_class( $classes ) {
		$layout       = tribe( Filters::class )->get_layout_setting();
		$live_refresh = tribe_get_option( 'liveFiltersUpdate', 'automatic' );

		if ( 'automatic' === $live_refresh ) {
			$classes[] = 'tribe-filters-live-update';
		}

		if ( 'vertical' === $layout ) {
			/**
			 * Allows filtering of whether vertical filters initially display closed.
			 *
			 * @since 4.9.3
			 *
			 * @param bool $init_closed Boolean on whether to initially display vertical filters closed or not.
			 */
			$init_closed = apply_filters( 'tribe_events_filter_bar_views_v2_vertical_init_closed', true );

			$filter_classes = [ 'tribe-filters-closed', 'tribe-filters-open' ];
			if ( empty( $init_closed ) ) {
				$filter_classes = array_reverse( $filter_classes );
			}

			$classes[] = $filter_classes[0];

			// See if we have the opposite filter class.
			$key = array_search( $filter_classes[1], $classes );

			// Remove it from the array
			if ( ! empty( $key ) ) {
				unset( $classes[ $key ] );
			}
		}

		return $classes;
	}

	/**
	 * Listens on the shortcode View hooks toggle to set the `render_filters` property and avoid displaying filters
	 * on shortcodes.
	 *
	 * @since 4.9.0
	 *
	 * @param bool $toggle Whether View hooks are being toggled on or off. Before a shortcode HTML is rendered the
	 *                     toggle will be `true`, `false` after a shortcode HTML rendered.
	 */
	public function on_shortcode_toggle_view_hooks( $toggle ) {
		$this->should_display_filters = ! $toggle;
	}

	/**
	 * Alters the HTML cache ttl
	 *
	 * @since 4.9.0
	 *
	 * @return int
	 */
	public function filter_cache_html_expiration() {
		return HOUR_IN_SECONDS * 11;
	}
}
