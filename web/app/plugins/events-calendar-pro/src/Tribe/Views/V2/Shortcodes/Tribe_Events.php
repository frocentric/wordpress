<?php
/**
 * Shortcode Tribe_Events.
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 * @since   4.7.5
 */
namespace Tribe\Events\Pro\Views\V2\Shortcodes;

use Tribe\Events\Pro\Views\V2\Assets as Pro_Assets;
use Tribe\Events\Views\V2\Assets as Event_Assets;
use Tribe\Events\Views\V2\Manager as Views_Manager;
use Tribe\Events\Views\V2\Theme_Compatibility;
use Tribe\Events\Views\V2\View;
use Tribe\Events\Views\V2\View_Interface;
use Tribe\Utils\Element_Classes;
use Tribe__Context as Context;
use Tribe__Events__Main as TEC;
use Tribe__Utils__Array as Arr;

/**
 * Class for Shortcode Tribe_Events.
 *
 * @todo On version 5.3 We need to stop using Pro Shortcode Abstract and move towards Common abstract.
 *       We are not moving this into common or TEC just the Abstract usage needs to be removed completely.
 *
 * @since   4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 */
class Tribe_Events extends Shortcode_Abstract implements Shortcode_Interface {

	/**
	 * Prefix for the transient where we will save the base values for the
	 * setup of the context of the shortcode.
	 *
	 * @since 4.7.9
	 *
	 * @var   string
	 */
	const TRANSIENT_PREFIX = 'tribe_events_shortcode_tribe_events_params_';

	/**
	 * {@inheritDoc}
	 */
	protected $slug = 'tribe_events';

	/**
	 * {@inheritDoc}
	 */
	protected $default_arguments = [
		'id'                => null,
		'view'              => null,
		/**
		 * @todo @bordoni @lucatume @be Update this when shortcode URL management is fixed.
		 */
		'should_manage_url' => false,

		// Legacy Params, registered for compatibility
		'date'              => null,
		'tribe-bar'         => true,
		'category'          => null,
		'cat'               => null,
		'featured'          => false,
		'main-calendar'     => false,
	];

	/**
	 * {@inheritDoc}
	 */
	protected $validate_arguments_map = [
		'should_manage_url' => 'tribe_is_truthy',
		'tribe-bar'         => 'tribe_is_truthy',
		'featured'          => 'tribe_is_truthy',
		'main-calendar'     => 'tribe_is_truthy',
	];

	/**
	 * Toggles the filtering of URLs to match the place where.
	 * We tend to hook into P15 to allow other things to happen before shortcode.
	 *
	 * @since  4.7.5
	 *
	 * @param  bool   $toggle  Whether to turn the hooks on or off.
	 *
	 * @return void
	 */
	protected function toggle_view_hooks( $toggle ) {
		if ( $toggle ) {
			add_filter( 'tribe_events_views_v2_url_query_args', [ $this, 'filter_view_query_args' ], 15, 3 );
			add_filter( 'tribe_events_filter_bar_views_v2_should_display_filters', '__return_false', 20 );
		} else {
			remove_filter( 'tribe_events_views_v2_url_query_args', [ $this, 'filter_view_query_args' ], 15 );
			remove_filter( 'tribe_events_filter_bar_views_v2_should_display_filters', '__return_false', 20 );
		}

		/**
		 * Fires after View hooks have been toggled while rendering a shortcode.
		 *
		 * @since 5.0.0
		 *
		 * @param bool   $toggle Whether the hooks should be turned on or off. This value is `true` before a shortcode
		 *                       HTML is rendered and `false` after the shortcode HTML rendered.
		 * @param static $this   The shortcode object that is toggling the View hooks.
		 */
		do_action( 'tribe_events_pro_shortcode_toggle_view_hooks', $toggle, $this );
	}

	/**
	 * Verifies if in this Shortcode we should allow View URL management.
	 *
	 * @since  4.7.5
	 *
	 * @return bool
	 */
	public function should_manage_url() {
		// Defaults to true due to old behaviors on Views V1
		$should_manage_url = $this->get_argument( 'should_manage_url', $this->default_arguments['should_manage_url'] );

		$disallowed_locations = [
			'widget_text_content',
		];

		/**
		 * Allows filtering of the disallowed locations for URL management.
		 *
		 * @since  4.7.5
		 *
		 * @param  mixed  $disallowed_locations Which filters we dont allow URL management.
		 * @param  static $instance             Which instance of shortcode we are dealing with.
		 */
		$disallowed_locations = apply_filters( 'tribe_events_pro_shortcode_tribe_events_manage_url_disallowed_locations', $disallowed_locations, $this );

		// Block certain locations
		foreach ( $disallowed_locations as $location ) {
			// If any we are in any of the disallowed locations
			if ( doing_filter( $location ) ) {
				$should_manage_url = $this->default_arguments['should_manage_url'];
			}
		}

		/**
		 * Allows filtering if a shortcode URL management is active.
		 *
		 * @since  4.7.5
		 *
		 * @param  mixed  $should_manage_url Should we manage the URL for this views shortcode instance.
		 * @param  static $instance          Which instance of shortcode we are dealing with.
		 */
		$should_manage_url = apply_filters( 'tribe_events_pro_shortcode_tribe_events_should_manage_url', $should_manage_url, $this );

		return $should_manage_url;
	}

	/**
	 * Changes the URL to match the Shortcode if needed.
	 *
	 * @since 4.7.5
	 *
	 * @param  array           $query_args  Current URL for this view.
	 *
	 * @return array The filtered View query args, with the shortcode ID added.
	 */
	public function filter_view_query_args( $query_args ) {
		// Always add the id of the shortcode to the URLs
		$query_args['shortcode'] = $this->get_id();

		return $query_args;
	}

	/**
	 * Fetches from the database the params of a given shortcode based on the ID created.
	 *
	 * @since  4.7.9
	 *
	 * @param string $shortcode_id The shortcode identifier, or `null` to use the current one.
	 *
	 * @return array Array of params configuring the Shortcode.
	 */
	public function get_database_arguments( $shortcode_id = null ) {
		$shortcode_id = $shortcode_id ?: $this->get_id();
		$transient_key = static::TRANSIENT_PREFIX . $shortcode_id;
		$transient_arguments = get_transient( $transient_key );

		return $transient_arguments;
	}

	/**
	 * Configures the Relationship between shortcode ID and their params in the database
	 * allowing us to pass the URL as the base for the Queries.
	 *
	 * @since 4.7.9
	 *
	 * @return  bool  Return if we have the arguments configured or not.
	 */
	public function set_database_params() {
		$shortcode_id = $this->get_id();
		$transient_key = static::TRANSIENT_PREFIX . $shortcode_id;
		$db_arguments = $this->get_database_arguments();
		$db_arguments['id'] = $shortcode_id;

		// If the value is the same it's already on the Database.
		if ( $db_arguments === $this->get_arguments() ) {
			return true;
		}

		return set_transient( $transient_key, $this->get_arguments() );
	}

	/**
	 * Alters the shortcode context with its arguments.
	 *
	 * @since  4.7.9
	 *
	 * @param \Tribe__Context $context Context we will use to build the view.
	 *
	 * @return \Tribe__Context Context after shortcodes changes.
	 */
	public function alter_context( Context $context, array $arguments = [] ) {
		$shortcode_id = $context->get( 'id' );
		if ( empty( $arguments ) ) {
			$arguments = $this->get_arguments();
			$shortcode_id = $this->get_id();
		}

		$alter_context = $this->args_to_context( $arguments, $context );

		// The View will consume this information on initial state.
		$alter_context['shortcode'] = $shortcode_id;
		$alter_context['id']        = $shortcode_id;

		$context = $context->alter( $alter_context );

		return $context;
	}

	/**
	 * Based on the either a argument "id" of the shortcode definition
	 * or the 8 first characters of the hashed version of a string serialization
	 * of the params sent to the shortcode we will create/get an ID for this
	 * instance of the tribe_events shortcode
	 *
	 * @since  4.7.9
	 *
	 * @return string The shortcode unique(ish) identifier.
	 */
	public function get_id() {
		$arguments = $this->get_arguments();

		// In case we have the ID argument we just return that.
		if ( ! empty( $arguments['id'] ) ) {
			return $arguments['id'];
		}

		ksort( $arguments );

		/*
		 * Generate a string id based on the arguments used to setup the shortcode.
		 * Note that arguments are sorted to catch substantially same shortcode w. diff. order argument.
		 */
		$hash = substr( md5( maybe_serialize( $arguments ) ), 0, 8 );

		return $hash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_html() {
		$context = tribe_context();

		/**
		 * On blocks editor shortcodes are being rendered in the screen which for some unknown reason makes the admin
		 * URL soft redirect (browser history only) to the front-end view URL of that shortcode.
		 *
		 * @see TEC-3157
		 */
		if ( is_admin() && ! $context->doing_ajax() ) {
			return '';
		}

		// Before anything happens we set a DB ID and value for this shortcode entry.
		$this->set_database_params();

		// Modifies the Context for the shortcode params.
		$context   = $this->alter_context( $context );

		// Fetches if we have a specific view are building.
		$view_slug = $this->get_argument( 'view', $context->get( 'view' ) );

		/**
		 * Triggers an action to allow other plugins or extensions to load assets.
		 *
		 * @since 4.7.9
		 *
		 * @param self $shortcode Instance of this class.
		 */
		do_action( 'tribe_events_pro_shortcode_tribe_events_before_assets', $this );

		// Make sure to enqueue assets.
		tribe_asset_enqueue_group( Pro_Assets::$group_key );
		tribe_asset_enqueue_group( Event_Assets::$group_key );

		/**
		 * Triggers an action to allow other plugins or extensions to load assets.
		 *
		 * @since 4.7.9
		 *
		 * @param self $shortcode Instance of this class.
		 */
		do_action( 'tribe_events_pro_shortcode_tribe_events_after_assets', $this );

		// Removing tribe-bar when that argument is `false`.
		if ( false === $this->get_argument( 'tribe-bar' ) ) {
			add_filter( 'tribe_template_html:events/v2/components/events-bar', '__return_false' );
		}

		// Toggle the shortcode required modifications.
		$this->toggle_view_hooks( true );

		// Setup the view instance.
		$view = View::make( $view_slug, $context );

		// Setup wether this view should manage url or not.
		$view->get_template()->set( 'should_manage_url', $this->should_manage_url() );

		$theme_compatiblity = tribe( Theme_Compatibility::class );

		$html = '';

		if ( $theme_compatiblity->is_compatibility_required() ) {
			$classes = $theme_compatiblity->get_body_classes();
			$element_classes = new Element_Classes( $classes );
			$html .= '<div ' . $element_classes->get_attribute() . '>';
		}

		$html .= $view->get_html();

		if ( $theme_compatiblity->is_compatibility_required() ) {
			$html .= '</div>';
		}

		// Toggle the shortcode required modifications.
		$this->toggle_view_hooks( false );

		return $html;
	}

	/**
	 * Filters the View repository args to add the ones required by shortcodes to work.
	 *
	 * @since 4.7.9
	 *
	 * @param array           $repository_args An array of repository arguments that will be set for all Views.
	 * @param \Tribe__Context $context         The current render context object.
	 *
	 * @return array          Repository arguments after shortcode args added.
	 */
	public function filter_view_repository_args( $repository_args, $context ) {
		if ( ! $context instanceof Context ) {
			return $repository_args;
		}

		$shortcode_id = $context->get( 'shortcode' ,false );

		if ( false === $shortcode_id || $context->doing_php_initial_state() ) {
			return $repository_args;
		}

		$shortcode_args = $this->get_database_arguments( $shortcode_id );

		$repository_args = $this->args_to_repository( (array) $repository_args, (array) $shortcode_args, $context );

		// Removing tribe-bar when that argument is false
		if ( isset( $shortcode_args['tribe-bar'] ) && false === tribe_is_truthy( $shortcode_args['tribe-bar'] ) ) {
			add_filter( 'tribe_template_html:events/v2/components/events-bar', '__return_false' );
		}

		return $repository_args;
	}

	/**
	 * Translates shortcode arguments to their Context argument counterpart.
	 *
	 * @since 4.7.9
	 *
	 * @param array   $arguments The shortcode arguments to translate.
	 * @param Context $context The request context.
	 *
	 * @return array The translated shortcode arguments.
	 */
	protected function args_to_context( array $arguments, Context $context ) {
		$context_args = [];

		$category_input = Arr::get_first_set( $arguments, [ 'cat', 'category' ], false );

		if ( ! empty( $category_input ) ) {
			$context_args['event_category'] = Arr::list_to_array( $category_input );
		}

		if ( ! empty( $arguments['date'] ) ) {
			$context_args['event_date'] = $arguments['date'];
		}

		if ( isset( $arguments['featured'] ) ) {
			$context_args['featured'] = tribe_is_truthy( $arguments['featured'] );
		}

		if ( null === $context->get( 'eventDisplay' ) ) {
			if ( empty( $arguments['view'] ) ) {
				$default_view_class                 = tribe( Views_Manager::class )->get_default_view();
				$context_args['event_display_mode'] = tribe( Views_Manager::class )->get_view_slug_by_class( $default_view_class );
			} else {
				$context_args['event_display_mode'] = $arguments['view'];
			}
		}

		return $context_args;
	}

	/**
	 * Translates shortcode arguments to their Repository argument counterpart.
	 *
	 * @since 4.7.9
	 *
	 * @param array    $repository_args  The current repository arguments.
	 * @param array    $arguments        The shortcode arguments to translate.
	 * @param Context  $context          The shortcode arguments to translate.
	 *
	 * @return array The translated shortcode arguments.
	 */
	public function args_to_repository( array $repository_args, array $arguments, $context ) {
		$category_input = Arr::get_first_set( $arguments, [ 'cat', 'category' ], false );

		if ( ! empty( $category_input ) ) {
			$repository_args['event_category'] = Arr::list_to_array( $category_input );
		}

		if ( isset( $arguments['date'] ) ) {
			// The date can be used in many ways, so we juggle a bit here.
			$date_filters = tribe_events()->get_date_filters();
			$date_keys    = array_filter(
				$repository_args,
				static function ( $key ) use ( $date_filters ) {
					return in_array( $key, $date_filters, true );
				},
				ARRAY_FILTER_USE_KEY
			);

			if ( count( $date_keys ) === 1 ) {
				if ( $date_keys[0] === $arguments['date'] ) {
					// Let's only set it if we are sure.
					$repository_args[ array_keys( $date_keys )[0] ] = $arguments['date'];
				} else {
					$repository_args[ array_keys( $date_keys )[0] ] = reset( $date_keys );
				}
			}
		}

		if ( isset( $arguments['featured'] ) ) {
			$repository_args['featured'] = tribe_is_truthy( $arguments['featured'] );
		}

		return $repository_args;
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
	public function filter_view_context( Context $view_context, $view_slug, $instance ) {
		if ( ! $shortcode_id = $view_context->get( 'shortcode' ) ) {
			return $view_context;
		}

		$arguments = $this->get_database_arguments( $shortcode_id );

		if ( empty( $arguments ) ) {
			return $view_context;
		}

		return $this->alter_context( $view_context, $arguments );
	}

	/**
	 * Filters the default view in the views manager for shortcodes navigation.
	 *
	 * @since  4.7.9
	 *
	 * @param string $view_class Fully qualified class name for default view.
	 *
	 * @return string             Fully qualified class name for default view of the shortcode in question.
	 */
	public function filter_default_url( $view_class ) {
		if ( tribe_context()->doing_php_initial_state() ) {
			return $view_class;
		}

		// Use the global context here as we should be in the context of an AJAX shortcode request.
		$shortcode_id = tribe_context()->get( 'shortcode', false );

		if ( false === $shortcode_id ) {
			// If we're not in the context of an AJAX shortcode request, bail.
			return $view_class;
		}

		$shortcode_args = $this->get_database_arguments( $shortcode_id );

		if ( ! $shortcode_args['view'] ) {
			return $view_class;
		}

		return tribe( Views_Manager::class )->get_view_class_by_slug( $shortcode_args['view'] );
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
		$context = $view->get_context();

		if ( ! $context instanceof Context ) {
			return $html_classes;
		}

		if ( $shortcode = $context->get( 'shortcode', false ) ) {
			$html_classes[] = 'tribe-events-view--shortcode';
			$html_classes[] = 'tribe-events-view--shortcode-' . $shortcode;
		}

		return $html_classes;
	}

	/**
	 * Filters the View data attributes to add some related to PRO features.
	 *
	 * @since 5.0.0
	 *
	 * @param array<string,string> $data The current View data attributes classes.
	 * @param string               $slug The View registered slug.
	 * @param View_Interface       $view The View currently rendering.
	 *
	 * @return array<string,string> The filtered data attributes.
	 */
	public function filter_view_data( $data, $slug, $view ) {
		$context = $view->get_context();

		if ( ! $context instanceof Context ) {
			return $data;
		}

		if ( $shortcode = $context->get( 'shortcode', false ) ) {
			$data['shortcode'] = $shortcode;
		}

		return $data;
	}
}
