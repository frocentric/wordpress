<?php
/**
 * Shortcodes manager for the new views.
 *
 * @since   4.7.5
 *
 * @deprecated  5.1.1
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 */
namespace Tribe\Events\Pro\Views\V2\Shortcodes;

use Tribe\Events\Views\V2\View_Interface;
use Tribe\Shortcode\Shortcode_Interface;
use Tribe__Context as Context;
use Tribe__Events__Pro__Shortcodes__Register as Legacy_Shortcodes;

/**
 * Class Shortcode Manager.
 *
 * @since   4.7.5
 *
 * @deprecated 5.1.1
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 */
class Manager extends \Tribe\Shortcode\Manager {
	/**
	 * Get the list of shortcodes available for handling.
	 *
	 * @since  4.7.5
	 *
	 * @deprecated 5.1.1
	 *
	 * @return array An associative array of shortcodes in the shape `[ <slug> => <class> ]`
	 */
	public function get_registered_shortcodes() {
		$shortcodes = parent::get_registered_shortcodes();

		// Do not add more shortcodes here. Use the filter on Common!
		$shortcodes['tribe_events'] = Tribe_Events::class;

		/**
		 * Allow the registering of shortcodes into the our Pro plugin.
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @var array An associative array of shortcodes in the shape `[ <slug> => <class> ]`
		 */
		$shortcodes = apply_filters_deprecated(
			'tribe_events_pro_shortcodes',
			[ $shortcodes ],
			'TBD',
			'tribe_shortcodes'
		);

		return $shortcodes;
	}

	/**
	 * Filters the context locations to add the ones used by Shortcodes.
	 *
	 * @since 4.7.9
	 *
	 * @todo Move this to a method inside of Shortcodes|Tribe_Events
	 *
	 * @param array $locations The array of context locations.
	 *
	 * @return array The modified context locations.
	 */
	public function filter_context_locations( array $locations = [] ) {
		$locations['shortcode'] = [
			'read' => [
				Context::REQUEST_VAR => 'shortcode',
				Context::LOCATION_FUNC => [
					'view_prev_url',
					static function ( $url ) {
						return tribe_get_query_var( $url, 'shortcode', Context::NOT_FOUND );
					},
				],
			],
		];

		return $locations;
	}

	/**
	 * Remove old shortcode methods from views v1.
	 *
	 * @todo Move this to a method inside of Shortcodes|Tribe_Events
	 *
	 * @since  4.7.5
	 *
	 * @return void
	 */
	public function disable_v1() {
		remove_shortcode( 'tribe_events' );

		$legacy_shortcodes_instance = tribe( 'events-pro.main' )->shortcodes;

		// Prevents removal with the incorrect class.
		if ( ! $legacy_shortcodes_instance instanceof Legacy_Shortcodes ) {
			return;
		}

		remove_action( 'tribe_events_ical_before', [ $legacy_shortcodes_instance, 'search_shortcodes' ] );
		remove_action( 'save_post', [ $legacy_shortcodes_instance, 'update_shortcode_main_calendar' ] );
		remove_action( 'trashed_post', [ $legacy_shortcodes_instance, 'maybe_reset_main_calendar' ] );
		remove_action( 'deleted_post', [ $legacy_shortcodes_instance, 'maybe_reset_main_calendar' ] );

		// Hooks attached to the main calendar attribute on the shortcodes
		remove_filter( 'tribe_events_get_link', [ $legacy_shortcodes_instance, 'shortcode_main_calendar_link' ], 10 );
	}

	/**
	 * Filters the View URL to add the shortcode query arg, if required.
	 *
	 * @since 4.7.9
	 *
	 * @todo Move this to a method inside of Shortcodes|Tribe_Events
	 *
	 * @param string         $url   The View current URL.
	 * @param View_Interface $view  This view instance.
	 *
	 * @return string  The URL for the view shortcode.
	 */
	public function filter_view_url( $url, View_Interface $view ) {
		$context = $view->get_context();

		if ( empty( $url ) ) {
			return $url;
		}

		if ( ! $context instanceof Context ) {
			return $url;
		}

		$shortcode_id = $context->get( 'shortcode', false );

		if ( false === $shortcode_id ) {
			return $url;
		}

		return add_query_arg( [ 'shortcode' => $shortcode_id ], $url );
	}

	/**
	 * Filters the query arguments array and add the Shortcodes.
	 *
	 * @since 4.7.9
	 *
	 * @todo Move this to a method inside of Shortcodes|Tribe_Events
	 *
	 * @param array           $query     Arguments used to build the URL.
	 * @param string          $view_slug The current view slug.
	 * @param View_Interface  $view      The current View object.
	 *
	 * @return  array  Filtered the query arguments for shortcodes.
	 */
	public function filter_view_url_query_args( array $query, $view_slug, View_Interface $view ) {
		$context = $view->get_context();

		if ( ! $context instanceof Context ) {
			return $query;
		}

		$shortcode = $context->get( 'shortcode', false );

		if ( false === $shortcode ) {
			return $query;
		}

		$query['shortcode'] = $shortcode;

		return $query;
	}

	/**
	 * Deprecated Alias to `render_shortcode`.
	 *
	 * @since  4.7.5
	 * @deprecated  5.1.1 Use `render_shortcode`
	 *
	 * @param array  $arguments Set of arguments passed to the Shortcode at hand.
	 * @param string $content   Contents passed to the shortcode, inside of the open and close brackets.
	 * @param string $shortcode Which shortcode tag are we handling here.
	 *
	 * @return string The rendered shortcode HTML.
	 */
	public function handle( $arguments, $content, $shortcode ) {
		return $this->render_shortcode( $arguments, $content, $shortcode );
	}
}
