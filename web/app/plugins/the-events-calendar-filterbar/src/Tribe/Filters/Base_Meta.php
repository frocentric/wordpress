<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Country
 */

use Tribe__Cache_Listener as Cache_Listener;

class Tribe__Events__Filterbar__Filters__Base_Meta extends Tribe__Events__Filterbar__Filter {
	public static $cache_key_base_ids = 'tribe_filterbar_base_ids';

	public $type = 'select';

	public function get_admin_form() {
		$title = $this->get_title_field();
		$type = $this->get_multichoice_type_field();
		return $title . $type;
	}

	public $searched_meta = null;
	public $relation_meta = null;
	public $join_name = null;

	public function get_searched_post_type() {
		return '';
	}

	public function get_related_post_type() {
		return '';
	}

	public function filter_related_name( $data ) {
		return trim( $data->meta_value );
	}

	public function filter_related_value( $data ) {
		return $data->post_id;
	}

	public function is_valid_data( $name, $value ) {
		return true;
	}

	protected function get_values() {
		/** @var wpdb $wpdb */
		global $wpdb;

		$search_data = tribe( 'cache' )->get_transient( static::$cache_key_base_ids, Cache_Listener::TRIGGER_SAVE_POST );

		if ( empty( $search_data ) ) {
			$search_in = implode( "', '", array_map( 'esc_sql', (array) $this->searched_meta ) );

			if ( empty( $search_in ) ) {
				return [];
			}

			$search_sql =
				"SELECT m.post_id, m.meta_value
			FROM {$wpdb->postmeta} m
			INNER JOIN {$wpdb->posts} p ON p.ID = m.post_id
			WHERE
				p.post_type = %s AND
				p.post_status = 'publish' AND m.meta_key IN ( '{$search_in}' ) AND m.meta_value != ''";

			// Get The Searched Meta
			$search_data = $wpdb->get_results( $wpdb->prepare( $search_sql, $this->get_searched_post_type() ) );

			/** @var Tribe__Feature_Detection $feature_detection */
			$feature_detection = tribe( 'feature-detection' );
			/** @var Tribe__Cache $cache */
			$cache = tribe( 'cache' );
			if (
				wp_using_ext_object_cache()
				|| strlen( serialize( $search_data ) ) < ( $feature_detection->get_mysql_max_packet_size() * .4 )
			) {
				// Only cache if the database will allow it.
				$cache->set_transient( static::$cache_key_base_ids, $search_data, DAY_IN_SECONDS,
					Cache_Listener::TRIGGER_SAVE_POST );
			}
		}

		// Fetch the possible related ids
		$possible_ids = array_unique( wp_list_pluck( $search_data, 'post_id' ) );

		// Build a SQL structure for Integers for a IN compare
		$related_sql = implode( ', ', $possible_ids );

		if ( empty( $related_sql ) ) {
			return [];
		}

		$search_related_sql = "
			SELECT
				m.meta_value
			FROM
				{$wpdb->postmeta} m
				INNER JOIN {$wpdb->posts} p
					ON p.ID = m.post_id
			WHERE
				p.post_type = %s
				AND p.post_status = 'publish'
				AND m.meta_key = '{$this->relation_meta}'
				AND m.meta_value IN ( {$related_sql} )
		";

		// Get Related data from DB
		$related_ids_raw = $wpdb->get_col( $wpdb->prepare( $search_related_sql, $this->get_related_post_type() ) );
		$related_ids     = [];
		foreach ( $related_ids_raw as $id ) {
			if ( ! is_numeric( $id ) ) {
				continue;
			}

			$related_ids[ $id ] = $id;
		}

		if ( empty( $search_data ) ) {
			return [];
		}

		$related = [];

		foreach ( $search_data as $data ) {
			if ( ! isset( $related_ids[ $data->post_id ] ) ) {
				continue;
			}

			$name = $this->filter_related_name( $data );
			$value = $this->filter_related_value( $data );

			if ( ! $this->is_valid_data( $name, $value ) ) {
				continue;
			}

			//only add a value once
			if ( ! isset( $related[ $name ] ) || ! isset( $related[ $name ][ $value ] ) ) {
				$related[ $name ][ $value ] = $value;
			}
		}

		if ( empty( $related ) ) {
			return [];
		}

		// Order It alphabetically
		ksort( $related );

		foreach ( $related as $name => $related_ids ) {
			$return[] = array(
				'name' => $name,
				'value' => implode( '-', $related_ids ),
			);
		}

		return $return;
	}

	protected function setup_join_clause() {
		global $wpdb;
		$this->joinClause = "INNER JOIN {$wpdb->postmeta} AS {$this->join_name} ON ({$wpdb->posts}.ID = {$this->join_name}.post_id AND {$this->join_name}.meta_key = '{$this->relation_meta}')";
	}

	protected function setup_where_clause() {
		$related = (array) $this->currentValue;
		$related_array = array();
		foreach ( $related as $related_string ) {
			if ( empty( $related_string ) ) {
				continue;
			}
			// change dash to comma from multiselect values
			$related_string = str_replace( '-', ',', $related_string );
			$related_array  = array_merge( $related_array, explode( ',', $related_string ) );
		}

		$related_array = array_unique( array_map( 'absint', array_filter( $related_array ) ) );
		$related_in_sql = implode( ',', $related_array );
		$this->whereClause = " AND {$this->join_name}.meta_value IN ( {$related_in_sql} ) ";
	}
}
