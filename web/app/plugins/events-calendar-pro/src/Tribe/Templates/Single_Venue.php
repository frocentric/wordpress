<?php
/**
 * @for     Single Venue Template
 * This file contains hooks and functions required to set up the single venue view.
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Pro__Templates__Single_Venue' ) ) {
	class Tribe__Events__Pro__Templates__Single_Venue extends Tribe__Events__Pro__Template_Factory {

		protected $asset_packages = array();

		protected $body_class = 'tribe-events-venue';

		protected $comments_off = true;

		/**
		 * Set up hooks for this template
		 *
		 * @return void
		 **/
		public function hooks() {
			parent::hooks();

			add_action( 'tribe_events_single_venue_before_upcoming_events', array( $this, 'setup_upcoming_events' ) );

			add_filter( 'tribe_get_template_part_path_list/nav.php', array( $this, 'filter_list_nav' ) );

			// Print JSON-LD markup on the `wp_head`
			add_action( 'wp_head', array( Tribe__Events__JSON_LD__Venue::instance(), 'markup' ) );
		}

		/**
		 * Setup meta display in this template
		 *
		 * @return void
		 **/
		public function setup_meta() {

			parent::setup_meta();

			// setup the template for the meta group
			tribe_set_the_meta_template( 'tribe_event_venue', array(
				'before'       => '',
				'after'        => '',
				'label_before' => '',
				'label_after'  => '',
				'meta_before'  => '<address class="venue-address">',
				'meta_after'   => '</address>',
			), 'meta_group' );
			// setup the template for the meta items
			tribe_set_the_meta_template( array(
				'tribe_event_venue_address',
				'tribe_event_venue_phone',
				'tribe_event_venue_website',
			), array(
				'before'       => '',
				'after'        => '',
				'label_before' => '',
				'label_after'  => '',
				'meta_before'  => '<span class="%s">',
				'meta_after'   => '</span>',
			) );

			// turn off the venue name in the group
			tribe_set_the_meta_visibility( 'tribe_event_venue_name', false );

			// remove the title for the group & meta items
			tribe_set_meta_label( 'tribe_event_venue', '', 'meta_group' );
			tribe_set_meta_label( array(
				'tribe_event_venue_address' => '',
				'tribe_event_venue_phone'   => '',
				'tribe_event_venue_website' => '',
			) );

			// set meta item priorities
			tribe_set_meta_priority( array(
				'tribe_event_venue_address' => 10,
				'tribe_event_venue_phone'   => 20,
				'tribe_event_venue_website' => 30,
			) );

			add_filter( 'tribe_event_meta_venue_address_gmap', '__return_false' );

			// disable venue info from showing on list module (since it's duplicate of this view)
			tribe_set_the_meta_visibility( 'tribe_list_venue_name_address', false );
		}

		/**
		 * Do any setup for upcoming events
		 *
		 * @return void
		 **/
		public function setup_upcoming_events() {
			// include the list view class for upcoming events
			tribe_initialize_view( 'list' );
		}

		/**
		 * Filters the nav template to use when displaying the single venue view.
		 *
		 * @param string $file
		 *
		 * @return string $template
		 */
		public function filter_list_nav( $file ) {
			$file = Tribe__Events__Templates::getTemplateHierarchy( 'pro/list/venue-nav' );

			return $file;
		}
	}
}
