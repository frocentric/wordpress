<?php
/**
 * Handles the filters applied by this plugin to the Views.
 *
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2
 */

namespace Tribe\Events\Pro\Views\V2;

use Tribe\Events\Pro\Views\V2\Geo_Loc\Handler_Interface as Geo_Loc_Handler;
use Tribe\Events\Views\V2\View_Interface;
use Tribe\Events\Views\V2\Manager;
use Tribe__Context as Context;
use Tribe__Events__Main as TEC;
use Tribe__Events__Organizer as Organizer;
use Tribe__Events__Rewrite as TEC_Rewrite;
use Tribe__Events__Venue as Venue;
use WP_REST_Request as Request;

/**
 * Class View_Filters
 * @since   4.7.5
 * @package Tribe\Events\Pro\Views\V2
 */
class View_Filters {

	/**
	 * The geo location handler.
	 *
	 * @since 4.7.9
	 *
	 * @var Geo_Loc_Handler
	 */
	protected $geo_loc_handler;

	/**
	 * View_Filters constructor.
	 *
	 * @param Geo_Loc_Handler $geo_loc_handler A geo location handler.
	 */
	public function __construct( Geo_Loc_Handler $geo_loc_handler ) {
		$this->geo_loc_handler = $geo_loc_handler;
	}

	/**
	 * Filters the View repository args to apply the applicable filters provided by the plugin.
	 *
	 * @since 4.7.5
	 *
	 * @param array        $repository_args         The current repository args.
	 * @param Context|null $context                 An instance of the context the View is using or `null` to use the
	 *                                              global Context.
	 *
	 * @return array The filtered repository args.
	 */
	public function filter_repository_args( array $repository_args, Context $context = null ) {
		$context = null !== $context ? $context : tribe_context();

		$hide_subsequent_recurrences_default = tribe_is_truthy( tribe_get_option( 'hideSubsequentRecurrencesDefault', false ) );
		$hide_subsequent_recurrences = (bool) $context->get( 'hide_subsequent_recurrences', false );

		// If in Recurring "All" Page or the Day View then always show all the recurring events.
		$view = $context->get( 'view' );
		if ( 'all' === $view || 'day' === $view ) {
			$repository_args['hide_subsequent_recurrences'] = false;
		} elseif ( $hide_subsequent_recurrences_default || $hide_subsequent_recurrences ) {
			$repository_args['hide_subsequent_recurrences'] = true;
		}

		$is_location_search = $context->is( 'geoloc_search' );
		if ( $is_location_search ) {
			$repository_args = (array) $this->geo_loc_handler->filter_repository_args( $repository_args, $context );
		}

		return $repository_args;
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
		$post_types_map = [
			Organizer::POSTTYPE => 'organizer',
			Venue::POSTTYPE => 'venue',
		];

		$intersect_params = array_intersect( array_keys( $params ), array_keys( $post_types_map ) );
		if ( ! count( $intersect_params ) ) {
			return $params;
		}

		$post_type = reset( $intersect_params );

		if ( empty( $post_types_map[ $post_type ] ) ) {
			return $params;
		}

		$params['eventDisplay'] = $post_types_map[ $post_type ];

		return $params;
	}

	/**
	 * Filters the View template variables before the HTML is generated to add the ones related to this plugin filters.
	 *
	 * @since 4.7.5
	 *
	 * @param array   $template_vars The View template variables.
	 * @param Context $context       The View current context.
	 *
	 * @return array The filtered template variables.
	 */
	public function filter_template_vars( array $template_vars, Context $context = null ) {
		$context = null !== $context ? $context : tribe_context();
		if ( empty( $template_vars['bar'] ) ) {
			$template_vars['bar'] = [];
		}

		$hide_subsequent_recurrences = tribe_is_truthy( $context->get( 'hide_subsequent_recurrences', false ) );
		if ( $hide_subsequent_recurrences ) {
			$template_vars['bar']['hide_recurring'] = true;
		}

		$location = $context->get( 'geoloc_search', false );
		if ( ! empty( $location ) ) {
			$template_vars['bar']['location'] = $location;
		}

		$template_vars['display_recurring_toggle'] = tribe_is_truthy( tribe_get_option( 'userToggleSubsequentRecurrences', false ) );

		// When inside of shortcode we need to make sure the correct settings apply.
		if ( $context->get( 'shortcode', false ) ) {
			if ( ! tribe_is_truthy( tribe_get_option( 'tribeEventsShortcodeBeforeHTML', false ) ) ) {
				$template_vars['before_events'] = '';
			}
			if ( ! tribe_is_truthy( tribe_get_option( 'tribeEventsShortcodeAfterHTML', false ) ) ) {
				$template_vars['after_events'] = '';
			}
		}

		return $template_vars;
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
	 */
	public function filter_view_url( $url, $canonical, View_Interface $view ) {
		$context = $view->get_context() ?: tribe_context();

		$search = $context->get( 'geoloc_search' );

		if ( empty( $search ) ) {
			$url = remove_query_arg( 'tribe-bar-location', $url );
		} else {
			$url = add_query_arg( [ 'tribe-bar-location' => $search ], $url );
		}

		$hide_subsequent_recurrences = tribe_is_truthy( $context->get( 'hide_subsequent_recurrences', false ) );
		if ( $hide_subsequent_recurrences ) {
			$url = add_query_arg( [ 'hide_subsequent_recurrences' => true ], $url );
		}

		return $url;
	}

	/**
	 * Redirects the user to the default mobile view if required.
	 *
	 * When on mobile (in terms of device capacity) we redirect to the default mobile View.
	 * To avoid caching issues, where the cache provider would need to keep a mobile and non-mobile version of the
	 * cached pages, we redirect with explicit View slug.
	 *
	 * @since 4.7.10
	 *
	 * @see   wp_is_mobile()
	 * @link  https://developer.wordpress.org/reference/functions/wp_is_mobile/
	 */
	public function on_template_redirect() {
		if (
			! wp_is_mobile()
			|| tribe_is_truthy( tribe_get_request_var( 'tribe_redirected' ) )
			|| is_singular()
			|| ! is_tax( [ TEC::TAXONOMY, 'post_tag' ] )
			|| 'all' === tribe_context()->get( 'view' )
			|| 'embed' === tribe_context()->get( 'view' )
			|| is_front_page()
		) {
			return;
		}

		$context = tribe_context();

		if ( ! $context->get( 'tec_post_type' ) ) {
			return;
		}

		// Make sure users can actually go to the specific views if intentional.
		if ( 'default' !== $context->get( 'view' ) ) {
			return;
		}

		/**
		 * @var Manager $manager
		 */
		$manager = tribe( Manager::class );

		$default_view        = $manager->get_default_view_option( 'desktop' );
		$default_mobile_view = tribe_get_option( 'mobile_default_view', 'default' );

		if ( $default_view === $default_mobile_view ) {
			return;
		}

		global $wp;

		$url = home_url( '/' );

		// Add the base WordPress Url Query arguments.
		$url = add_query_arg( $wp->query_vars, $url );

		// Add our mobile default to the arguments.
		$url = add_query_arg(
			[
				'eventDisplay'     => $default_mobile_view,
				'tribe_redirected' => true,
			],
			$url
		);

		$location = TEC_Rewrite::instance()->get_canonical_url( $url );

		wp_redirect(
			$location,
			302
		);

		tribe_exit();
	}

}
