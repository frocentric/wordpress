<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Events\Filterbar\Views\V2_1\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'filterbar.views.v2_1.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Events\Filterbar\Views\V2_1\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'filterbar.views.v2_1.hooks' ), 'some_method' ] );
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 */

namespace Tribe\Events\Filterbar\Views\V2_1;

use Tribe\Events\Filterbar\Views\V2\Doing_Filterbar;
use Tribe\Events\Filterbar\Views\V2\Filters\Cost;
use Tribe\Events\Filterbar\Views\V2\Filters\Url;
use Tribe\Events\Filterbar\Views\V2_1\Filters\Factory;
use Tribe\Events\Views\V2\View;
use Tribe\Events\Views\V2\View_Interface;
use Tribe__Context as Context;
use Tribe__Events__Filterbar__View as Main;
use Tribe__Utils__Array as Arr;
use Tribe__Customizer__Section as Customizer_Section;

/**
 * Class Hooks.
 *
 * @since   5.0.0
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 */
class Hooks extends \tad_DI52_ServiceProvider {
	use Doing_Filterbar;

	/**
	 * Whether filters should render at all or not.
	 *
	 * @var bool
	 */
	protected $should_display_filters = true;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		$this->container->singleton( Customizer::class, Customizer::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by each Filterbar Views v2_1 component.
	 *
	 * @since 5.0.0
	 */
	protected function add_actions() {
		add_action( 'tribe_template_before_include:events/v2/components/events-bar/views', [ $this, 'action_include_filter_button' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/components/filter-bar', [ $this, 'action_include_filter_bar' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/components/events-bar', [ $this, 'action_include_horizontal_filter_bar' ], 10, 3 );
	}

	/**
	 * Adds the filters required by each Filter bar Views v2_1 component.
	 *
	 * @since 5.0.0
	 */
	protected function add_filters() {

		/*
		 * In the context of Views V2_1 filters will work differently and their hooking to queries is handled
		 * in the `tribe_events_filter_bar_initialize_filters` filter.
		 */
		add_filter( 'tribe_events_filter_bar_initialize_filters', '__return_false' );
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_view_repository_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_url_query_args', [ $this, 'filter_view_url_query_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_rest_params', [ $this, 'filter_view_rest_params' ], 10, 2 );
		add_filter( 'tribe_template_origin_namespace_map', [ $this, 'filter_add_template_origin_namespace' ], 15 );
		add_filter( 'tribe_template_path_list', [ $this, 'filter_template_path_list' ], 15, 2 );
		add_filter( 'tribe_events_views_v2_view_html_classes', [ $this, 'filter_view_html_classes' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_template_vars', [ $this, 'filter_events_views_v2_1_view_template_vars' ], 10, 2 );
		add_filter( 'tribe_events_filter_bar_views_v2_1_template_vars_filters', [ $this, 'get_filters' ], 20, 3 );
		add_filter( 'tribe_events_filter_bar_views_v2_1_template_vars_selected_filters', [ $this, 'get_selected_filters' ], 20, 3 );
		add_filter( 'tribe-event-filters-settings-fields', [ $this, 'filter_settings_fields' ], 20 );

		add_filter( 'tribe_events_filter_bar_views_v2_1_is_checked_filterbar_cost', [ $this, 'filterbar_cost_is_checked' ], 10, 4 );
		add_filter( 'tribe_events_filter_bar_views_v2_1_range_label_filterbar_cost', [ $this, 'filterbar_cost_range_label' ], 10, 3 );
		add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );

		// Customizer.
		add_filter( 'tribe_customizer_pre_sections', [ $this, 'filter_customizer_sections' ], 20, 2 );
		add_filter( 'tribe_customizer_global_elements_css_template', [ $this, 'filter_global_elements_css_template' ], 10, 3 );
		add_filter( 'tribe_customizer_single_event_css_template', [ $this, 'filter_single_event_css_template' ], 10, 3 );
	}

	/**
	 * Filters a View repository args to add the filters ones.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed> $args    An array of repository arguments.
	 * @param Context             $context The View context instance.
	 *
	 * @return array<string,mixed> The filtered array of View repository arguments.
	 */
	public function filter_view_repository_args( array $args, Context $context ) {
		return $this->container->make( Factory::class )->for_repository_args( $args, $context );
	}

	/**
	 * Filters the query arguments Views will use to build their own URL.
	 *
	 * Using the context we'll know what filter are applied and what keys and values to add to the query args.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed> $query_args The current URL query arguments.
	 * @param View_Interface      $view       The instance of the View the URL is being built for.
	 *
	 * @return array<string,mixed> The filtered array of URL query arguments.
	 */
	public function filter_view_url_query_args( array $query_args, View_Interface $view ) {
		return $this->container->make( Url::class )->filter_view_query_args( $query_args, $view );
	}

	/**
	 * Modifies the rest Params to reflect required modifications to make filterbar work as expected.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed> $params  The Rest params to filter.
	 * @param \WP_REST_Request    $request WP Rest Request used.
	 *
	 * @return array<string,mixed> The filtered params.
	 */
	public function filter_view_rest_params( array $params, $request ) {
		return $this->container->make( Factory::class )->for_rest_params( $params, $request );
	}

	/**
	 * Include the filter button template.
	 *
	 * @since 5.0.0
	 *
	 * @param string                          $file     Complete path to include the PHP File.
	 * @param array                           $name     Template name.
	 * @param \Tribe\Events\Views\V2\Template $template Current instance of the template.
	 */
	public function action_include_filter_button( $file, $name, $template ) {
		if ( ! $this->container->make( Filters::class )->should_display_filters( $template->get_view() ) ) {
			return;
		}

		if ( Template::is_using_shortcode( $template ) ) {
			return;
		}

		$template_vars = array_merge(
			$template->get_values(),
			[ 'default_state' => tribe( Filters::class )->get_open_closed_state() ]
		);

		return $this->container->make( Template::class )
		                       ->template( 'components/events-bar/filter-button', $template_vars );
	}

	/**
	 * Fires to include the filter bar.
	 *
	 * @since 5.0.0
	 *
	 * @param string                          $file     Complete path to include the PHP File.
	 * @param array                           $name     Template name.
	 * @param \Tribe\Events\Views\V2\Template $template Current instance of the template.
	 */
	public function action_include_filter_bar( $file, $name, $template ) {
		if ( ! $this->container->make( Filters::class )->should_display_filters( $template->get_view() ) ) {
			return;
		}

		if ( Template::is_using_shortcode( $template ) ) {
			return;
		}

		// Only display vertical Filter Bar or horizontal Filter Bar with search disabled.
		$values = $template->get_values();

		if (
			'vertical' !== tribe( Filters::class )->get_layout_setting()
			&& empty( Arr::get( $values, 'disable_event_search', false ) )
		) {
			return;
		}

		$this->doing_filterbar( function () use ( $values ) {
			$this->container->make( Template::class )->template( 'filter-bar', $values );
		} );
	}

	/**
	 * Fires to include the horizontal filter bar.
	 *
	 * @since 5.0.0
	 *
	 * @param string                          $file     Complete path to include the PHP File.
	 * @param array                           $name     Template name.
	 * @param \Tribe\Events\Views\V2\Template $template Current instance of the template.
	 */
	public function action_include_horizontal_filter_bar( $file, $name, $template ) {		if ( ! $this->container->make( Filters::class )->should_display_filters( $template->get_view() ) ) {
			return;
		}

		if ( Template::is_using_shortcode( $template ) ) {
			return;
		}

		$values = $template->get_values();

		// Only display horizontal Filter Bar when search is enabled.
		if (
			'horizontal' !== tribe( Filters::class )->get_layout_setting()
			||  Arr::get( $values, 'disable_event_search', false )
		) {
			return;
		}

		return $this->container->make( Template::class )->template( 'filter-bar', $values );
	}

	/**
	 * Includes Filter Bar into the path namespace mapping, allowing for a better namespacing when loading files.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,string> $namespace_map Indexed array containing the namespace as the key and path to `strpos`.
	 *
	 * @return array<string,string>  Namespace map after adding Pro to the list.
	 */
	public function filter_add_template_origin_namespace( $namespace_map ) {
		$main                                       = Main::instance();
		$namespace_map[ $main->template_namespace ] = $main->pluginPath;

		return $namespace_map;
	}

	/**
	 * Filters the list of folders TEC will look up to find templates to add the ones defined by Filter Bar.
	 *
	 * @since 5.0.0
	 *
	 * @param array            $folders  The current list of folders that will be searched template files.
	 * @param \Tribe__Template $template Which template instance we are dealing with.
	 *
	 * @return array<string,array> The filtered list of folders that will be searched for the templates.
	 */
	public function filter_template_path_list( array $folders = [], \Tribe__Template $template ) {
		$main = Main::instance();

		$path = (array) rtrim( $main->pluginPath, '/' );

		// Pick up if the folder needs to be added to the public template path.
		$folder = $template->get_template_folder();

		// Rewrite `v2` to `v2_1` to stick with the version of the FBAR views we're loading.
		$v2_path_frag_index            = array_search( 'v2', $folder );
		$folder[ $v2_path_frag_index ] = 'v2_1';

		if ( ! empty( $folder ) ) {
			$path = array_merge( $path, $folder );
		}

		$folders['events-filterbar'] = [
			'id'        => 'events-filterbar',
			'namespace' => $main->template_namespace,
			'priority'  => 25,
			'path'      => implode( DIRECTORY_SEPARATOR, $path ),
		];

		return $folders;
	}

	/**
	 * Filters the HTML classes applied to a View top-level container.
	 *
	 * @since 5.0.0
	 * @since 5.0.2 Add filter to turn off adding classes to some containers.
	 *
	 * @param array<string> $html_classes Array of classes used for this view.
	 * @param string        $view_slug    The current view slug.
	 * @param View          $instance     The current View object.
	 */
	public function filter_view_html_classes( array $html_classes, $view_slug, View $instance ) {
		/**
		 * Allows views to toggle off FBAR classes on the container.
		 *
		 * @since 5.0.2
		 *
		 * @param boolean $add_html_classes Whether to add classes or not. Defaults to true.
		 * @param string  $view_slug        The current view slug.
		 * @param View    $instance         The current View object.
		 *
		 * @return boolean $add_html_classes Whether to add classes or not.
		 */
		$add_html_classes = apply_filters(
			'tribe_events_views_v2_filter_bar_view_html_classes',
			true,
			$view_slug,
			$instance
		);

		/**
		 * Allows views to tell FBAR to not add its classes to their container.
		 *
		 * @since 5.0.2
		 *
		 * @param boolean $add_html_classes Whether to add classes or not.
		 * @param string  $view_slug        The current view slug.
		 * @param View    $instance         The current View object.
		 *
		 * @return boolean $add_html_classes Whether to add classes or not.
		 */
		$add_html_classes = apply_filters(
			"tribe_events_views_v2_filter_bar_{$view_slug}_view_html_classes",
			$add_html_classes,
			$view_slug,
			$instance
		);

		if ( ! tribe_is_truthy( $add_html_classes ) ) {
			return $html_classes;
		}

		return $this->container->make( Filters\Factory::class )->for_html_classes( $html_classes, $view_slug, $instance );
	}

	/**
	 * Filter the Template Vars for Filterbar V2_1 Views.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed>  $template_vars The View template variables.
	 * @param View_Interface $view                The current View instance.
	 */
	public function filter_events_views_v2_1_view_template_vars( array $template_vars, View_Interface $view ) {
		/** @var Filters $view_filters */
		$view_filters = $this->container->make( Filters::class );

		return $view_filters->filter_template_vars( $template_vars, $view->get_context() );
	}

	/**
	 * Get the active filters for display.
	 *
	 * @since 5.0.0
	 *
	 * @param array   $filters            An array of filter objects to display.
	 * @param Context $context            The View current context.
	 * @param string  $breakpoint_pointer String we use as pointer to the current view we are setting up with breakpoints.
	 *
	 * @return array An array of active filters to display in the Filter bar.
	 */
	public function get_filters( $filters, $context, $breakpoint_pointer ) {
		if ( false !== $context->get( 'shortcode', false ) ) {
			return $filters;
		}

		$active_filters = $this->container->make( Factory::class )->get_active_filters_for_template_vars( $context, $breakpoint_pointer );

		return array_merge( $filters, $active_filters );
	}

	/**
	 * Get the selected filters for display.
	 *
	 * @since 5.0.0
	 *
	 * @param array   $selected_filters An array of selected filters to display.
	 * @param array   $filters          An array of filter objects to display.
	 * @param Context $context          The View current context.
	 *
	 * @return array An array of the selected filters to display on the initial load.
	 */
	public function get_selected_filters( $selected_filters, $filters, $context ) {
		if ( false !== $context->get( 'shortcode', false ) ) {
			return $selected_filters;
		}

		$active_filters = [];

		foreach ( $filters as $filter ) {

			if ( empty( $filter['selections'] ) ) {
				continue;
			}

			$active_filters[] = [
				'label'      => $filter['label'],
				'selections' => $filter['selections'],
				'name'       => $filter['name'],
			];
		}

		return array_merge( $selected_filters, $active_filters );
	}

	/**
	 * Filters the filter settings fields to remove live filters update setting.
	 *
	 * @param array<string,array> $fields Associative array of filters settings.
	 *
	 * @return array<string,array> The filtered array of filter settings fields.
	 */
	public function filter_settings_fields( $fields ) {
		unset( $fields[ 'liveFiltersUpdate' ] );

		return $fields;
	}

	/**
	 * Filter the Cost filter is_checked conditional.
	 *
	 * @since 5.0.0
	 *
	 * @param boolean                   $special_is_checked Whether a special is checked condition has been met.
	 * @param array<string,integer>|int $value              An array or integer of the current fields value.
	 * @param array<string,integer>     $current_value      An array of the selected value(s).
	 * @param string                    $type               The type of field the filter displays as.
	 *
	 * @return boolean                  Whether the cost condition is met for a given value.
	 */
	public function filterbar_cost_is_checked( $is_checked, $value, $current_value, $type ) {
		return Cost::filter_is_checked( $is_checked, $value, $current_value, $type );
	}

	/**
	 * Filter the default range label.
	 *
	 * @since 5.0.0
	 *
	 * @param string $label The default range label.
	 * @param int    $min   The minimum value for the range.
	 * @param int    $min   The maximum value for the range.
	 *
	 * @return string The formatted range label.
	 */
	public function filterbar_cost_range_label( $label, $min, $max ) {
		return Cost::filter_range_label( $label, $min, $max );
	}

	/**
	 * Filters the Context locations to add the ones required by the Filters.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,mixed> $locations The current context locations.
	 *
	 * @return array<string,mixed> The filtered context locations.
	 */
	public function filter_context_locations( $locations ) {
		if ( ! is_array( $locations ) ) {
			return $locations;
		}

		// The state of the whole Filter Bar. If found then it will be one of `open` or `closed`.
		$locations['fbar_state'] = [
			'read' => [
				Context::FUNC => [
					static function () {
						$var = tribe_get_request_var( 'tribe_filter_bar_state', Context::NOT_FOUND );
						if ( Context::NOT_FOUND === $var ) {
							return $var;
						}

						return tribe_is_truthy( $var ) ? 'open' : 'closed';
					},
				],
			],
		];

		// The state of each Filter Bar filter, an integer representing a bitmask.
		$locations['fbar_filters_state'] = [
			'read' => [
				Context::REQUEST_VAR => [ 'tribe_filters_state' ],
			],
		];

		// Add some extra locations to read the current and previous URL from.
		foreach ( [ 'view_url' => 'url', 'view_prev_url' => 'prev_url' ] as $location => $request_var ) {
			if ( isset( $locations[ $location ]['read'] ) ) {
				$prev = Arr::get( $locations[ $location ]['read'], Context::REQUEST_VAR, [] );

				$locations[ $location ]['read'][ Context::REQUEST_VAR ] = array_merge( $prev, [ $request_var ] );
			}
		}

		return $locations;
	}

	/**
	 * Filters the currently registered Customizer sections to add or modify them.
	 *
	 * @since 5.0.3
	 *
	 * @param array<string,array<string,array<string,int|float|string>>> $sections The registered Customizer sections.
	 * @param \Tribe___Customizer $customizer The Customizer object.
	 *
	 * @return array<string,array<string,array<string,int|float|string>>> The filtered sections.
	 */
	public function filter_customizer_sections( $sections, $customizer ) {
		if ( ! ( is_array( $sections ) && $customizer instanceof \Tribe__Customizer ) ) {
			return $sections;
		}

		return $this->container->make( Customizer::class )->filter_sections( $sections, $customizer );
	}

	/**
	 * Filters the Global Elements section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 5.0.3
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Global Elements section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_global_elements_css_template( $css_template, $section, $customizer ) {
		if ( ! ( is_string( $css_template ) && $section instanceof Customizer_Section && $customizer instanceof \Tribe__Customizer ) ) {
			return $css_template;
		}

		return $this->container->make( Customizer::class )->filter_global_elements_css_template( $css_template, $section, $customizer );
	}

	/**
	 * Filters the Single Event section CSS template to add Views v2 related style templates to it.
	 *
	 * @since 5.0.3
	 *
	 * @param string                      $css_template The CSS template, as produced by the Global Elements.
	 * @param \Tribe__Customizer__Section $section      The Single Event section.
	 * @param \Tribe__Customizer          $customizer   The current Customizer instance.
	 *
	 * @return string The filtered CSS template.
	 */
	public function filter_single_event_css_template( $css_template, $section, $customizer ) {
		if ( ! ( is_string( $css_template ) && $section instanceof Customizer_Section && $customizer instanceof \Tribe__Customizer ) ) {
			return $css_template;
		}

		return $this->container->make( Customizer::class )->filter_single_event_css_template( $css_template, $section, $customizer );
	}
}
