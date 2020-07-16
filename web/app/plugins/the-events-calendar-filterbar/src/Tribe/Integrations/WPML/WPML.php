<?php
/**
 * Class Tribe__Events__Filterbar__Integrations__WPML__WPML
 *
 * Handles anything relating to Events Filterbar and WPML integration
 *
 * @since 4.5.8
 */
class Tribe__Events__Filterbar__Integrations__WPML__WPML {

	/**
	 * Hooks any required filters and action
	 *
	 * @since  4.5.8
	 *
	 * @return void
	 */
	public function hook() {

		// Add WPML integration for the additional fields functionality
		add_filter( 'tribe_events_filter_additional_fields_query', array( __CLASS__, 'wpml_filter_additional_fields_query' ) );

	}

	/**
	 * Add WPML integration for the additional fields functionality
	 * from Events PRO
	 *
	 * @param string $query
	 *
	 * @since  4.5.8
	 * @return string
	 */
	public static function wpml_filter_additional_fields_query( $query ) {

		global $wpdb;

		// If PRO is not active we cannot support additional fields
		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
			return;
		}

		$language = apply_filters( 'wpml_current_language', false );
		$join     = "INNER JOIN {$wpdb->prefix}icl_translations ON element_id = $wpdb->posts.ID AND element_type = CONCAT( 'post_', $wpdb->posts.post_type )";
		$where    = $wpdb->prefix . "icl_translations.language_code = '" . $language . "'";
		$query    = str_replace( 'WHERE', $join . ' WHERE ', $query ) . ' AND ' . $where;

		return $query;
	}

}
