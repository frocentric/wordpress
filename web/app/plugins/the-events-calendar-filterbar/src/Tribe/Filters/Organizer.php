<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Organizer
 */

use Tribe__Cache_Listener as Cache_Listener;

class Tribe__Events__Filterbar__Filters__Organizer extends Tribe__Events__Filterbar__Filter {
	public static $cache_key_organizer_ids = 'tribe_filterbar_organizer_ids';

	public $type = 'select';

	public function get_admin_form() {
		$title = $this->get_title_field();
		$type = $this->get_multichoice_type_field();
		return $title.$type;
	}

	protected function get_values() {
		/** @var wpdb $wpdb */
		global $wpdb;

		$organizer_ids = tribe( 'cache' )->get_transient( self::$cache_key_organizer_ids, Cache_Listener::TRIGGER_SAVE_POST );

		if ( empty( $organizer_ids ) ) {
			$sql = "
			SELECT
				m.meta_value
			FROM
				{$wpdb->postmeta} m
				INNER JOIN {$wpdb->posts} p
					ON p.ID=m.post_id
			WHERE p.post_type=%s
			AND p.post_status='publish'
			AND m.meta_key='_EventOrganizerID'
			AND m.meta_value > 0
		";

			// get organizer IDs associated with published posts
			$organizer_ids = $wpdb->get_col( $wpdb->prepare( $sql, Tribe__Events__Main::POSTTYPE ) );

			array_filter( $organizer_ids );

			if ( empty( $organizer_ids ) ) {
				return [];
			}

			$organizer_ids = array_unique( $organizer_ids );

			tribe( 'cache' )->set_transient( self::$cache_key_organizer_ids, $organizer_ids, DAY_IN_SECONDS, Cache_Listener::TRIGGER_SAVE_POST );
		}

		/**
		 * Filter Total Organizers in Filter Bar
		 * Use this with caution, this will load organizers on the front-end, may be slow
		 * The base limit is 200 for safety reasons
		 *
		 *
		 * @parm int  200 posts per page limit
		 * @parm array $organizer_ids   ids of organizers attached to events
		 */
		$limit = apply_filters( 'tribe_events_filter_bar_organizers_limit', 200, $organizer_ids );

		$organizers = get_posts( array(
			'post_type' => Tribe__Events__Main::ORGANIZER_POST_TYPE,
			'posts_per_page' => $limit,
			'suppress_filters' => false,
			'post__in' => $organizer_ids,
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
		) );

		$organizers_array = array();
		foreach ( $organizers as $organizer ) {
			$organizers_array[] = array(
				'name' => $organizer->post_title,
				'value' => $organizer->ID,
			);
		}
		return $organizers_array;
	}

	protected function setup_join_clause() {
		global $wpdb;
		$this->joinClause = "INNER JOIN {$wpdb->postmeta} AS organizer_filter ON ({$wpdb->posts}.ID = organizer_filter.post_id AND organizer_filter.meta_key = '_EventOrganizerID')";
	}

	protected function setup_where_clause() {
		if ( is_array( $this->currentValue ) ) {
			$organizer_ids = implode( ',', array_map( 'intval', $this->currentValue ) );
		} else {
			$organizer_ids = '"' . esc_sql( (string) $this->currentValue ) . '"';
		}

		$this->whereClause = " AND organizer_filter.meta_value IN ($organizer_ids) ";
	}
}
