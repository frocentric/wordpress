<?php
/**
 * @for     Single organizer Template
 * This file contains hooks and functions required to set up the single organizer view.
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Pro__Templates__Single_Organizer' ) ) {
	class Tribe__Events__Pro__Templates__Single_Organizer extends Tribe__Events__Pro__Template_Factory {


		protected $comments_off = true;


		/**
		 * Set up hooks for this template
		 *
		 * @return void
		 **/
		protected function hooks() {

			parent::hooks();

			add_filter( 'tribe_get_template_part_templates', array( $this, 'remove_list_navigation' ), 10, 3 );

			// Print JSON-LD markup on the `wp_head`
			add_action( 'wp_head', array( Tribe__Events__JSON_LD__Organizer::instance(), 'markup' ) );
		}

		/**
		 * Do any setup for upcoming events.
		 *
		 * @deprecated 4.3
		 *
		 * @return void
		 **/
		public function setup_upcoming_events() {
			// This method has been unhooked and it's very unlikely third party code will call it
			// directly, but to be safe it has been deprecated rather than removed outright
			_deprecated_function( __METHOD__, '4.3' );
		}

		/**
		 * Remove navigation from the list view included.
		 *
		 * @param array  $templates The templates to include.
		 * @param string $slug      The slug referencing the template.
		 * @param string $name      The name of the specific template.
		 *
		 * @return array The new array of templates to include.
		 */
		public function remove_list_navigation( $templates, $slug, $name ) {
			if ( $slug == 'list/nav' ) {
				$templates = array();
			}

			return $templates;
		}

	}
}
