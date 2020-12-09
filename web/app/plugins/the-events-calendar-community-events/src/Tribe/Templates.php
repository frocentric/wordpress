<?php
/**
 * Templating functionality for Tribe Events Calendar
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Community__Templates' ) ) {

	/**
	 * Handle views and template files.
	 */
	class Tribe__Events__Community__Templates {

		public function __construct() {
			add_filter( 'tribe_events_template_paths', [ $this, 'add_community_template_paths' ] );
			add_filter( 'tribe_support_registered_template_systems', [ $this, 'add_template_updates_check' ] );
		}

		/**
		 * Filter template paths to add the community plugin to the queue
		 *
		 * @param array $paths
		 * @return array $paths
		 * @author Peter Chester
		 * @since 3.1
		 */
		public function add_community_template_paths( $paths ) {
			$paths['community'] = tribe( 'community.main' )->pluginPath;
			return $paths;
		}

		/**
		 * Register Community Events with the template updates checker.
		 *
		 * @param array $plugins
		 *
		 * @return array
		 */
		public function add_template_updates_check( $plugins ) {
			// ET+ views can be in one of a range of different subdirectories (eddtickets, shopptickets
			// etc) so we will tell the template checker to simply look in views/tribe-events and work
			// things out from there
			$plugins[ __( 'Community Events', 'tribe-events-community' ) ] = [
				Tribe__Events__Community__Main::VERSION,
				tribe( 'community.main' )->pluginPath . 'src/views/community',
				trailingslashit( get_stylesheet_directory() ) . 'tribe-events/community',
			];

			return $plugins;
		}

		/********** Singleton **********/

		/**
		 * @var Tribe__Events__Community__Templates $instance
		 */
		protected static $instance;

		/**
		 * Static Singleton Factory Method
		 *
		 * @return Tribe__Events__Community__Templates
		 */
		public static function instance() {
			return tribe( 'community.templates' );
		}


		/**
		 * Hook into 'tribe_community_events_title' to avoid PHP 7.2 deprecated notices with `create_function`
		 *
		 * @since 4.5.10
		 *
		 * @return mixed|void
		 */
		public function tribe_community_events_title() {
			/**
			 * Replace the CE submit event page title.
			 *
			 * @since 4.5.10
			 *
			 * @return string
			 */
			$title = __( 'Submit an Event', 'tribe-events-community' );
			$title = apply_filters( 'tribe_events_community_submit_event_page_title', $title );
			$title = apply_filters_deprecated(
				'tribe_ce_submit_event_page_title',
				[ $title ],
				'4.6.3',
				'tribe_events_community_submit_event_page_title',
				'The filter "tribe_ce_submit_event_page_title" has been renamed to "tribe_events_community_submit_event_page_title" to match plugin namespacing.'
			);

			return $title;
		}

	}
}
