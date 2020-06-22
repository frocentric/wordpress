<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Events\Pro\Views\V2\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'pro.views.v2.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Events\Pro\Views\V2\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'pro.views.v2.hooks' ), 'some_method' ] );
 *
 * @since 4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

use Tribe\Events\Pro\Views\V2\Assets as Pro_Assets;
use Tribe\Events\Pro\Views\V2\Template\Title;
use Tribe\Events\Pro\Views\V2\Views\All_View;
use Tribe\Events\Pro\Views\V2\Views\Map_View;
use Tribe\Events\Pro\Views\V2\Views\Organizer_View;
use Tribe\Events\Pro\Views\V2\Views\Partials\Day_Event_Recurring_Icon;
use Tribe\Events\Pro\Views\V2\Views\Partials\Hide_Recurring_Events_Toggle;
use Tribe\Events\Pro\Views\V2\Views\Partials\List_Event_Recurring_Icon;
use Tribe\Events\Pro\Views\V2\Views\Partials\Location_Search_Field;
use Tribe\Events\Pro\Views\V2\Views\Partials\Month_Calendar_Event_Recurring_Icon;
use Tribe\Events\Pro\Views\V2\Views\Partials\Month_Calendar_Event_Tooltip_Recurring_Icon;
use Tribe\Events\Pro\Views\V2\Views\Partials\Month_Mobile_Event_Recurring_Icon;
use Tribe\Events\Pro\Views\V2\Views\Photo_View;
use Tribe\Events\Pro\Views\V2\Views\Venue_View;
use Tribe\Events\Pro\Views\V2\Views\Week_View;
use Tribe\Events\Views\V2\Messages as TEC_Messages;
use Tribe\Events\Views\V2\View;
use Tribe\Events\Views\V2\View_Interface;
use Tribe__Context as Context;
use Tribe__Events__Main as TEC;
use Tribe__Events__Organizer as Organizer;
use Tribe__Events__Pro__Main as Plugin;
use Tribe__Events__Rewrite as TEC_Rewrite;
use Tribe__Events__Venue as Venue;
use WP_REST_Request as Request;

/**
 * Class Hooks.
 *
 * @since 4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2
 */
class Hooks extends \tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.7.5
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
		$this->remove_filters();
	}

	/**
	 * Adds the actions required by each Pro Views v2 component.
	 *
	 * @since 4.7.5
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'action_disable_shortcode_v1' ], 15 );
		add_action( 'init', [ $this, 'action_add_shortcodes' ], 20 );
		add_action( 'tribe_template_after_include:events/v2/components/top-bar/actions/content', [ $this, 'action_include_hide_recurring_events' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/components/events-bar/search/keyword', [ $this, 'action_include_location_form_field' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/day/event/date/meta', [ $this, 'action_include_day_event_recurring_icon' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/list/event/date/meta', [ $this, 'action_include_list_event_recurring_icon' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/date/meta', [ $this, 'action_include_month_calendar_event_recurring_icon' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/date/meta', [ $this, 'action_include_month_calendar_event_tooltip_recurring_icon' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/date/meta', [ $this, 'action_include_month_mobile_event_recurring_icon' ], 10, 3 );
		add_action( 'tribe_events_views_v2_view_messages_before_render', [ $this, 'before_view_messages_render' ], 10, 3 );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_disable_assets_v1' ], 0 );
		add_action( 'tribe_events_pro_shortcode_tribe_events_after_assets', [ $this, 'action_disable_shortcode_assets_v1' ] );
		add_action( 'tribe_events_pre_rewrite', [ $this, 'on_pre_rewrite' ], 6 );

		add_action( 'template_redirect', [ $this, 'on_template_redirect' ], 50 );
		add_action( 'tribe_template_after_include:events/v2/components/breadcrumbs', [ $this, 'action_include_organizer_meta' ], 10, 3 );
		add_action( 'tribe_template_after_include:events/v2/components/breadcrumbs', [ $this, 'action_include_venue_meta' ], 10, 3 );
	}

	/**
	 * Adds the filters required by each Pro Views v2 component.
	 *
	 * @since 4.7.5
	 */
	protected function add_filters() {
		add_filter( 'tribe_events_rewrite_base_slugs', [ $this, 'filter_add_rewrite_venue_organizer' ] );
		add_filter( 'tribe_events_views', [ $this, 'filter_events_views' ] );
		add_filter( 'tribe_events_views_v2_bootstrap_view_slug', [ $this, 'filter_bootstrap_view_slug' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_events_views_v2_view_repository_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_template_vars', [ $this, 'filter_events_views_v2_view_template_vars' ], 10, 2 );
		add_filter( 'tribe_events_v2_view_title', [ $this, 'filter_tribe_events_v2_view_title' ], 10, 4 );
		add_filter( 'tribe_events_views_v2_view_url', [ $this, 'filter_tribe_events_views_v2_view_url' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_messages_map', [ $this, 'filter_tribe_events_views_v2_messages_map' ] );
		add_filter( 'tribe_events_views_v2_messages_need_events_label_keys', [ $this, 'filter_tribe_events_views_v2_messages_need_events_label_keys' ] );
		add_filter( 'tribe_events_pro_geocode_rewrite_rules', [ $this, 'filter_geocode_rewrite_rules' ], 10, 3 );
		add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );
		add_filter( 'tribe_events_views_v2_view_all_breadcrumbs', [ $this, 'filter_view_all_breadcrumbs' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_view_repository_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_venue_breadcrumbs', [ $this, 'filter_view_venue_breadcrumbs' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_organizer_breadcrumbs', [ $this, 'filter_view_organizer_breadcrumbs' ], 10, 2 );
		add_filter( 'redirect_canonical', [ $this, 'filter_prevent_canonical_redirect' ] );

		add_filter( 'tribe_events_views_v2_rest_params', [ $this, 'filter_rest_request_view_slug' ], 10, 2 );

		add_filter( 'tribe_events_views_v2_view_url', [ $this, 'filter_shortcode_view_url' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_next_url', [ $this, 'filter_shortcode_view_url' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_prev_url', [ $this, 'filter_shortcode_view_url' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_url_query_args', [ $this, 'filter_shortcode_view_url_query_args' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_context', [ $this, 'filter_shortcode_view_context' ], 10, 3 );

		add_filter( 'tribe_events_views_v2_manager_default_view', [ $this, 'filter_shortcode_default_view' ] );

		add_filter( 'tribe_events_views_v2_all_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );
		add_filter( 'tribe_events_views_v2_map_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );
		add_filter( 'tribe_events_views_v2_organizer_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );
		add_filter( 'tribe_events_views_v2_photo_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );
		add_filter( 'tribe_events_views_v2_venue_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );
		add_filter( 'tribe_events_views_v2_week_view_html_classes', [ $this, 'filter_add_events_pro_view_html_class' ] );

		// This is the controlled version of the filtering method removed in the `remove_filters` method.
		add_filter( 'tribe_events_event_schedule_details', [ $this, 'append_recurring_info_tooltip' ], 9, 2 );
		add_filter( 'tribe_events_views_v2_view_html_classes', [ $this, 'filter_view_html_classes' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_data', [ $this, 'filter_view_data' ], 10, 3 );

		// Let's filter AFTER Week View.
		add_filter( 'tribe_rewrite_handled_rewrite_rules', [ $this, 'filter_handled_rewrite_rules' ], 20, 2 );
		add_filter( 'tribe_events_rewrite_matchers_to_query_vars_map', [ $this, 'filter_rewrite_query_vars_map' ] );
		add_filter( 'tribe_events_rewrite_rules_custom', [ $this, 'filter_events_rewrite_rules_custom' ], 20 );

		add_filter( 'tribe_events_filter_bar_views_v2_should_display_filters', [ $this, 'filter_hide_filter_bar_organizer_venue' ], 10, 2 );

		add_filter( 'tribe_events_views_v2_manager_view_label_domain', [ $this, 'filter_view_label_domain'], 10, 3 );
		add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'customizer_inline_stylesheets' ], 12, 2 );
	}

	/**
	 * Remove the filters not required by Pro Views v2.
	 *
	 * @since 4.7.9
	 */
	protected function remove_filters() {

		$plugin = Plugin::instance();

		/*
		 * This is removed here to be re-added in `add_filters` under the control of the `append_recurring_info_tooltip`
		 * method of this class.
		 */
		remove_filter( 'tribe_events_event_schedule_details', [ $plugin, 'append_recurring_info_tooltip' ], 9, 2 );
	}

	/**
	 * Fires to deregister v1 assets correctly.
	 *
	 * @since 4.7.9
	 *
	 * @return  void
	 */
	public function action_disable_assets_v1() {
		$pro_assets = $this->container->make( Pro_Assets::class );
		if ( ! $pro_assets->should_enqueue_frontend() ) {
			return;
		}

		$pro_assets->disable_v1();
	}

	/**
	 * Fires to deregister v1 assets correctly for shortcodes.
	 *
	 * @since 4.7.9
	 *
	 * @return  void
	 */
	public function action_disable_shortcode_assets_v1() {
		$pro_assets = $this->container->make( Pro_Assets::class );
		$pro_assets->disable_v1();
	}

	/**
	 * Filters the available Views to add the ones implemented in PRO.
	 *
	 * @since 4.7.5
	 *
	 * @param array $views An array of available Views.
	 *
	 * @return array The array of available views, including the PRO ones.
	 */
	public function filter_events_views( array $views = [] ) {
		$views['all']       = All_View::class;
		$views['venue']     = Venue_View::class;
		$views['organizer'] = Organizer_View::class;
		$views['photo']     = Photo_View::class;
		$views['week']      = Week_View::class;
		$views['map']       = Map_View::class;

		return $views;
	}

	/**
	 * Filters the slug of the view that will be built according to the request context to add support for Venue and
	 * Organizer Views.
	 *
	 * @since 4.7.9
	 *
	 * @param string          $slug    The View slug that would be loaded.
	 * @param \Tribe__Context $context The current request context.
	 *
	 * @return string The filtered View slug, set to the Venue or Organizer ones, if required.
	 */
	public function filter_bootstrap_view_slug( $slug, $context ) {
		$post_types = [
			Organizer::POSTTYPE => 'organizer',
			Venue::POSTTYPE     => 'venue',
		];
		$post_type  = $context->get( 'post_type', $slug );

		if ( empty( $post_type ) ) {
			return $slug;
		}

		return isset( $post_types[ $post_type ] ) ? $post_types[ $post_type ] : $slug;
	}

	/**
	 * Fires to include the hide recurring template on the end of the actions of the top-bar.
	 *
	 * @since 4.7.5
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_hide_recurring_events( $file, $name, $template ) {
		$this->container->make( Hide_Recurring_Events_Toggle::class )->render( $template );
	}

	/**
	 * Fires to include the location form field after the keyword form field of the events bar.
	 *
	 * @since 4.7.5
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_location_form_field( $file, $name, $template ) {
		$this->container->make( Location_Search_Field::class )->render( $template );
	}

	/**
	 * Fires to include the recurring icon on the day view event.
	 *
	 * @since 4.7.8
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_day_event_recurring_icon( $file, $name, $template ) {
		$this->container->make( Day_Event_Recurring_Icon::class )->render( $template );
	}

	/**
	 * Fires to include the recurring icon on the list view event.
	 *
	 * @since 4.7.8
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_list_event_recurring_icon( $file, $name, $template ) {
		$this->container->make( List_Event_Recurring_Icon::class )->render( $template );
	}

	/**
	 * Fires to include the recurring icon on the month view calendar event.
	 *
	 * @since 4.7.8
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_month_calendar_event_recurring_icon( $file, $name, $template ) {
		$this->container->make( Month_Calendar_Event_Recurring_Icon::class )->render( $template );
	}

	/**
	 * Fires to include the recurring icon on the month view calendar event tooltip.
	 *
	 * @since 4.7.10
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_month_calendar_event_tooltip_recurring_icon( $file, $name, $template ) {
		$this->container->make( Month_Calendar_Event_Tooltip_Recurring_Icon::class )->render( $template );
	}

	/**
	 * Fires to include the recurring icon on the month view mobile event.
	 *
	 * @since 4.7.8
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 */
	public function action_include_month_mobile_event_recurring_icon( $file, $name, $template ) {
		$this->container->make( Month_Mobile_Event_Recurring_Icon::class )->render( $template );
	}

	/**
	 * Fires to disable V1 of shortcodes, normally they would be registered on `init@P10`
	 * so we will trigger this on `init@P15`.
	 *
	 * It's important to leave gaps on priority for better injection.
	 *
	 * @since 4.7.5
	 */
	public function action_disable_shortcode_v1() {
		$this->container->make( Shortcodes\Manager::class )->disable_v1();
	}

	/**
	 * Adds the new shortcodes, this normally will trigger on `init@P20` due to how we the
	 * v1 is added on `init@P10` and we remove them on `init@P15`.
	 *
	 * It's important to leave gaps on priority for better injection.
	 *
	 * @since 4.7.5
	 */
	public function action_add_shortcodes() {
		$this->container->make( Shortcodes\Manager::class )->add_shortcodes();
	}

	/**
	 * Filter the Rest Requests to point to the correct view when dealing with Venue and Organizer.
	 *
	 * @since  5.0.0
	 *
	 * @param  array   $params  Params received on the Request.
	 * @param  Request $request Full WP Rest Request instance.
	 *
	 * @return array            Params after view slug is setup.
	 */
	public function filter_rest_request_view_slug( array $params, Request $request ) {
		return $this->container->make( View_Filters::class )->filter_rest_request_view_slug( $params, $request );
	}

	/**
	 * Filters the View repository args to parse and apply PRO specific View filters.
	 *
	 * @since 4.7.5
	 *
	 * @param array        $repository_args The current repository args.
	 * @param Context|null $context         An instance of the context the View is using or `null` to use the
	 *                                      global Context.
	 *
	 * @return array The filtered repository args.
	 */
	public function filter_events_views_v2_view_repository_args( array $repository_args = [], Context $context = null ) {
		/** @var View_Filters $view_filters */
		$view_filters = $this->container->make( View_Filters::class );

		return $view_filters->filter_repository_args( $repository_args, $context );
	}

	/**
	 * Filters the View template variables before the HTML is generated to add the ones related to this plugin filters.
	 *
	 * @since 4.7.5
	 *
	 * @param array          $template_vars The View template variables.
	 * @param View_Interface $view The current View instance.
	 */
	public function filter_events_views_v2_view_template_vars( array $template_vars, View_Interface $view ) {
		/** @var View_Filters $view_filters */
		$view_filters = $this->container->make( View_Filters::class );

		return $view_filters->filter_template_vars( $template_vars, $view->get_context() );
	}

	/**
	 * Filters the Views v2 event page title, applying modifications for PRO Views.
	 *
	 * @since 4.7.9
	 *
	 * @param string          $title The current page title.
	 * @param bool            $depth Flag to build the title of a taxonomy archive with depth in hierarchical taxonomies or not.
	 * @param \Tribe__Context $context The current title render context.
	 * @param array           $posts An array of events fetched by the View.
	 *
	 * @return string The title, either the modified version if the rendering View is a PRO one requiring it, or the
	 *                original one.
	 */
	public function filter_tribe_events_v2_view_title( $title, $depth = true, $context = null, array $posts = [] ) {
		$new_title = $this->container->make( Title::class )
		                             ->set_context( $context )
		                             ->set_posts( $posts )
		                             ->build_title( $title, $depth );

		return $new_title ?: $title;
	}

	/**
	 * Filters the View URL to add, or remove, URL query arguments managed by PRO.
	 *
	 * @since 4.7.9
	 *
	 * @param string         $url       The current View URL.
	 * @param bool           $canonical Whether to return the canonical (pretty) URL or not.
	 * @param View_Interface $view      The View instance that is currently rendering.
	 *
	 * @return string The filtered View URL.
	 *
	 * @uses  \Tribe\Events\Pro\Views\V2\View_Filters::filter_view_url()
	 */
	public function filter_tribe_events_views_v2_view_url( $url, $canonical, View_Interface $view ) {
		return $this->container->make( View_Filters::class )->filter_view_url( $url, $canonical, $view );
	}

	/**
	 * Filters the View messages map set up by The Events Calendar to add PRO Views specific messages.
	 *
	 * @since 4.7.9
	 *
	 * @param array $map The View messages map set up by The Events Calendar.
	 *
	 * @return array The filtered message map, including PRO Views specific messages.
	 */
	public function filter_tribe_events_views_v2_messages_map( array $map = [] ) {
		return $this->container->make( Messages::class )->filter_map( $map );
	}

	/**
	 * Filters the keys of the messages set up by The Events Calendar to add PRO Views specific keys.
	 *
	 * @since 5.0.3
	 *
	 * @param array $need_events_label_keys Array of keys of the messages set up by The Events Calendar.
	 *
	 * @return array The filtered array of keys, including PRO Views specific keys that need events label.
	 */
	public function filter_tribe_events_views_v2_messages_need_events_label_keys( array $need_events_label_keys ) {
		return $this->container->make( Messages::class )->filter_need_events_label_keys( $need_events_label_keys );
	}

	/**
	 * Filters the user-facing messages a View will print on the frontend to add PRO specific messages.
	 *
	 * @since 4.7.9
	 *
	 * @param TEC_Messages $messages The messages handler object the View used to render the messages.
	 * @param array        $events   An array of the events found by the View that is currently rendering.
	 * @param View         $view     The current View instance being rendered.
	 */
	public function before_view_messages_render( TEC_Messages $messages, array $events, View $view ) {
		$this->container->make( Messages::class )->render_view_messages( $messages, $events, $view );
	}

	/**
	 * Filters the context locations to add the ones used by The Events Calendar PRO for Shortcodes.
	 *
	 * @since 4.7.9
	 *
	 * @param array $locations The array of context locations.
	 *
	 * @return array The modified context locations.
	 */
	public function filter_context_locations( array $locations = [] ) {
		return $this->container->make( Shortcodes\Manager::class )->filter_context_locations( $locations );
	}

	/**
	 * Add rewrite routes for PRO version of Views V2.
	 *
	 * @since 4.7.9
	 *
	 * @param TEC_Rewrite $rewrite The Tribe__Events__Rewrite object
	 */
	public function on_pre_rewrite( $rewrite ) {
		if ( ! $rewrite instanceof TEC_Rewrite ) {
			return;
		}

		/** @var Rewrite $rewrites_handler */
		$rewrites_handler = $this->container->make( Rewrite::class );
		$rewrites_handler->add_rewrites( $rewrite );
	}

	/**
	 * Includes rewrite bases for Organizer and Venue.
	 *
	 * @since  5.0.0
	 *
	 * @param  array $bases Previous set of bases.
	 *
	 * @return array       Bases after adding the venue and organizer.
	 */
	public function filter_add_rewrite_venue_organizer( array $bases ) {
		return $this->container->make( Rewrite::class )->add_base_rewrites( $bases );
	}

	/**
	 * Filters the `redirect_canonical` to prevent any redirects on venue and organizer URLs.
	 *
	 * @since 5.0.0
	 *
	 * @param mixed $redirect_url URL which we will redirect to.
	 *
	 * @return string             Orginial URL redirect or False to prevent canonical redirect.
	 */
	public function filter_prevent_canonical_redirect( $redirect_url = null ) {
		return $this->container->make( Rewrite::class )->filter_prevent_canonical_redirect( $redirect_url );
	}

	/**
	 * Filters the geocode based rewrite rules to add Views v2 specific rules.
	 *
	 * Differently from other Views, the Map View sets up its rewrite rules in the
	 * `Tribe__Events__Pro__Geo_Loc::add_routes` method.
	 *
	 * @since 4.7.9
	 *
	 * @param array $rules The geocode based rewrite rules.
	 *
	 * @return array The filtered geocode based rewrite rules.
	 *
	 * @see \Tribe__Events__Pro__Geo_Loc::add_routes() for where this code is applying.
	 */
	public function filter_geocode_rewrite_rules( $rules ) {
		if ( empty( $rules ) ) {
			return $rules;
		}

		return $this->container->make( Rewrite::class )->add_map_pagination_rules( $rules );
	}

	/**
	 * Filters the View repository args to add the ones required by shortcodes to work.
	 *
	 * @since 4.7.9
	 *
	 * @param array<string,mixed> $repository_args An array of repository arguments that will be set for all Views.
	 * @param \Tribe__Context     $context         The current render context object.
	 *
	 * @return array<string,mixed> The filtered repository arguments.
	 */
	public function filter_view_repository_args( $repository_args, $context ) {
		return $this->container->make( Shortcodes\Tribe_Events::class )->filter_view_repository_args( $repository_args, $context );
	}

	/**
	 * Filters recurring view breadcrumbs
	 *
	 * @since 4.7.9
	 *
	 * @param array $breadcrumbs The breadcrumbs array.
	 * @param array $view        The instance of the view being rendered.
	 *
	 * @return array The filtered breadcrumbs
	 *
	 * @see \Tribe\Events\Views\V2\View::get_breadcrumbs() for where this code is applying.
	 */
	public function filter_view_all_breadcrumbs( $breadcrumbs, $view ) {
		return $this->container->make( All_View::class )->setup_breadcrumbs( $breadcrumbs, $view );
	}

	/**
	 * Filters Organizer view breadcrumbs
	 *
	 * @since 4.7.9
	 *
	 * @param array $breadcrumbs The breadcrumbs array.
	 * @param array $view        The instance of the view being rendered.
	 *
	 * @return array The filtered breadcrumbs
	 *
	 * @see \Tribe\Events\Views\V2\View::get_breadcrumbs() for where this code is applying.
	 */
	public function filter_view_organizer_breadcrumbs( $breadcrumbs, $view ) {
		return $this->container->make( Organizer_View::class )->setup_breadcrumbs( $breadcrumbs, $view );
	}

	/**
	 * Filters Venue view breadcrumbs
	 *
	 * @since 4.7.9
	 *
	 * @param array $breadcrumbs The breadcrumbs array.
	 * @param array $view        The instance of the view being rendered.
	 *
	 * @return array The filtered breadcrumbs
	 *
	 * @see \Tribe\Events\Views\V2\View::get_breadcrumbs() for where this code is applying.
	 */
	public function filter_view_venue_breadcrumbs( $breadcrumbs, $view ) {
		return $this->container->make( Venue_View::class )->setup_breadcrumbs( $breadcrumbs, $view );
	}

	/**
	 * Filters the View URL to add the shortcode query arg, if required.
	 *
	 * @since 4.7.9
	 *
	 * @param string         $url       The View current URL.
	 * @param bool           $canonical Whether the URL is a canonical one or not.
	 * @param View_Interface $view      This view instance.
	 *
	 * @return string  Filtered version for the URL for shortcodes.
	 */
	public function filter_shortcode_view_url( $url, $canonical, $view ) {
		return $this->container->make( Shortcodes\Manager::class )->filter_view_url( $url, $view );
	}

	/**
	 * Filters the default view in the views manager for shortcodes navigation.
	 *
	 * @since 4.7.9
	 *
	 * @param string $view_class Fully qualified class name for default view.
	 *
	 * @return string            Fully qualified class name for default view of the shortcode in question.
	 */
	public function filter_shortcode_default_view( $view_class ) {
		return $this->container->make( Shortcodes\Tribe_Events::class )->filter_default_url( $view_class );
	}

	/**
	 * Alters the context of the view based on the shortcode params stored in the database based on the ID.
	 *
	 * @since  5.0.0
	 *
	 * @param  Context $view_context Context for this request.
	 * @param  string  $view_slug    Slug of the view we are building.
	 * @param  View    $instance     Which view instance we are dealing with.
	 *
	 * @return Context               Altered version of the context ready for shortcodes.
	 */
	public function filter_shortcode_view_context( $view_context, $view_slug, $instance ) {
		return $this->container->make( Shortcodes\Tribe_Events::class )
		                       ->filter_view_context( $view_context, $view_slug, $instance );
	}

	/**
	 * Filters the View URL to add the shortcode query arg, if required.
	 *
	 * @since 4.7.9
	 *
	 * @param array                        $query_args  Arguments used to build the URL.
	 * @param string                       $view_slug   The current view slug.
	 * @param \Tribe\Events\Views\V2\View  $instance    The current View object.
	 *
	 * @return  array  Filtered the query arguments for shortcodes.
	 */
	public function filter_shortcode_view_url_query_args( $query, $view_slug, $view ) {
		return $this->container->make( Shortcodes\Manager::class )->filter_view_url_query_args( $query, $view_slug, $view );
	}

	/**
	 * Filters the View HTML classes to add the pro required classes.
	 *
	 * @since 5.0.0
	 *
	 * @param   array  $html_classes  Array of classes used for this view.
	 *
	 * @return  array                 Array of classes after adding tribe-events-pro
	 */
	public function filter_add_events_pro_view_html_class( $classes ) {
		$classes[] = 'tribe-events-pro';

		return $classes;
	}

	/**
	 * Fires on the `template_redirect` action to allow the conditional redirect, if required.
	 *
	 * @since 4.7.10
	 */
	public function on_template_redirect() {
		$this->container->make( View_Filters::class )->on_template_redirect();
	}

	/**
	 * Filters the event schedule details to add the recurring information tooltip.
	 *
	 * This is a controlled use of a filter we used on all event details rendering in v1, in v2 we only append that
	 * tooltip to single events view.
	 *
	 * @since 5.0.0
	 *
	 * @param string $schedule_details The schedule details HTML, as built so far.
	 * @param int    $event_id         The event post ID.
	 *
	 * @return string The filtered schedule details HTML.
	 */
	public function append_recurring_info_tooltip( $schedule_details, $event_id ) {
		if ( ! is_singular( TEC::POSTTYPE ) ) {
			return $schedule_details;
		}

		return Plugin::instance()->append_recurring_info_tooltip( $schedule_details, $event_id );
	}

	/**
	 * Fires to include the organizer meta to the organizer view.
	 *
	 * @since 5.0.0
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 *
	 * @return string The organizer meta HTML.
	 */
	public function action_include_organizer_meta( $file, $name, $template ) {
		$view      = $template->get_view();

		if ( 'organizer' !== $view->get_slug() ) {
			return;
		}

		$organizer = get_post( $view->get_post_id() );

		if ( ! $organizer || Organizer::POSTTYPE !== $organizer->post_type ) {
			return;
		}

		return $view->render_meta();
	}

	/**
	 * Fires to include the venue meta to the venue view.
	 *
	 * @since 5.0.0
	 *
	 * @param string $file      Complete path to include the PHP File.
	 * @param array  $name      Template name.
	 * @param self   $template  Current instance of the Tribe__Template.
	 *
	 * @return string The venue meta HTML.
	 */
	public function action_include_venue_meta( $file, $name, $template ) {
		$view    = $template->get_view();

		if ( 'venue' !== $view->get_slug() ) {
			return;
		}

		$venue   = tribe_get_venue_object( $view->get_post_id() );

		if ( ! $venue || Venue::POSTTYPE !== $venue->post_type ) {
			return;
		}

		return $view->render_meta();
	}

	/**
	 * Filters the View HTML classes to add some related to PRO features.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string>  $html_classes The current View HTML classes.
	 * @param string         $slug         The View registered slug.
	 * @param View_Interface $view         The View currently rendering.
	 *
	 * @return array<string> The filtered HTML classes.
	 */
	public function filter_view_html_classes( $html_classes, $slug, $view ) {
		$html_classes = $this->container->make( Shortcodes\Tribe_Events::class )
		                                ->filter_view_html_classes( $html_classes, $slug, $view );

		return $html_classes;
	}

	/**
	 * Filters the View data attributes to add some related to PRO features.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,string> $data The current View data attributes.
	 * @param string               $slug The View registered slug.
	 * @param View_Interface       $view The View currently rendering.
	 *
	 * @return array<string,string> The filtered View data attributes.
	 */
	public function filter_view_data( $data, $slug, $view ) {
		$data = $this->container->make( Shortcodes\Tribe_Events::class )
		                        ->filter_view_data( $data, $slug, $view );

		return $data;
	}

	/**
	 * Filters the handled rewrite rules, the one used to parse plain links into permalinks, to add the ones
	 * managed by PRO.
	 *
	 * @since 5.0.1
	 *
	 * @param array<string,string> $handled_rules The handled rules, as produced by The Events Calendar base code; in
	 *                                            the same format used by WordPress to store and manage rewrite rules.
	 * @param array<string,string> $all_rules All the rewrite rules handled by WordPress.
	 *
	 * @return array<string,string> The filtered rewrite rules, including the ones handled by Events PRO; in the same
	 *                              format used by WordPress to handle rewrite rules.
	 *
	 * @see Rewrite::filter_handled_rewrite_rules() for the implementation.
	 */
	public function filter_handled_rewrite_rules( array $handled_rewrite_rules = [], array $all_rewrite_rules = [] ) {
		if ( empty( $all_rewrite_rules ) ) {
			return $handled_rewrite_rules;
		}

		return $this->container->make( Rewrite::class )
		                       ->filter_handled_rewrite_rules( $handled_rewrite_rules, $all_rewrite_rules );
	}

	/**
	 * Filters the query vars map used by the Rewrite component to parse plain links into permalinks to add the elements
	 * needed to support PRO components.
	 *
	 * @since 5.0.1
	 *
	 * @param array<string,string> $query_vars_map The query variables map, as produced by The Events Calendar code.
	 *                                             Shape is `[ <pattern> => <query_var> ].
	 *
	 * @return array<string,string> The query var map, filtered to add the query vars handled by PRO.
	 *
	 * @see   Rewrite::filter_rewrite_query_vars_map for the implementation.
	 */
	public function filter_rewrite_query_vars_map( array $query_vars_map = [] ) {
		return $this->container->make( Rewrite::class )->filter_rewrite_query_vars_map( $query_vars_map );
	}

	/**
	 * Filters the should display filters for organizer and venue views.
	 *
	 * @since 5.0.1
	 *
	 * @param bool           $should_display_filters Boolean on whether to display filters or not.
	 * @param View_Interface $view                   The View currently rendering.
	 *
	 * @return bool
	 */
	public function filter_hide_filter_bar_organizer_venue( $should_display_filters, $view ) {
		$slug = $view->get_slug();

		if ( ! in_array( $slug, [ 'organizer', 'venue' ] ) ) {
			return $should_display_filters;
		}

		return false;
	}

	/**
	 * Filters The Events Calendar custom rewrite rules to fix the order and relative position of some and play
	 * nice with Views v2 canonical URL needs.
	 *
	 * @since 5.0.3
	 *
	 * @param array<string,string> $rewrite_rules An array of The Events Calendar custom rewrite rules, in the same
	 *                                            format used by WordPress: a map of each rule regular expression to the
	 *                                            corresponding query string.
	 *
	 * @return array<string,string> The input map of The Events Calendar rewrite rules, updated to satisfy the needs
	 *                              of Views v2 canonical URL building.
	 */
	public function filter_events_rewrite_rules_custom( $rewrite_rules ) {
		if ( ! is_array( $rewrite_rules ) ) {
			return $rewrite_rules;
		}

		return $this->container->make( Rewrite::class )->filter_events_rewrite_rules_custom( $rewrite_rules );
	}

	public function filter_view_label_domain( $domain, $slug, $view_class ) {
		if (
			'photo' !== $slug
			&& 'week' !== $slug
			&& 'map' !== $slug
		) {
			return $domain;
		}

		return  'tribe-events-calendar-pro';
	}

	/**
	 * Add views stylesheets to customizer styles array to check.
	 * Remove unused legacy stylesheets.
	 *
	 * @param array<string> $sheets Array of sheets to search for.
	 * @param string        $css_template String containing the inline css to add.
	 *
	 * @return array Modified array of sheets to search for.
	 */
	public function customizer_inline_stylesheets( $sheets, $css_template ) {
		$v2_sheets = [
			'tribe-events-pro-views-v2-skeleton',
			'tribe-events-pro-views-v2-full'
		];

		// Unenqueue legacy sheets.
		$keys = array_keys( $sheets, 'tribe-events-calendar-pro-style' );
		if ( ! empty( $keys ) ) {
			foreach ( $keys as $key ) {
				unset( $sheets[ $key ] );
			}
		}

		return array_merge( $sheets, $v2_sheets );
	}
}
