<?php


class Tribe__Events__Pro__Integrations__WPML__API__Translations {

	/**
	 * @var array
	 */
	protected $master_series_ids_and_start_dates_cache = array();

	/**
	 * @var array
	 */
	protected $master_parent_event_ids_cache = array();

	/**
	 * @var array
	 */
	protected $src_language_cache = array();

	/**
	 * Returns a post parent post language code from the globals or from the database.
	 *
	 * @param int $parent_post_id
	 *
	 * @return bool|string The language code string (e.g. `en`) or `false` on failure.
	 */
	public function get_parent_language_code( $parent_post_id ) {
		if ( $this->is_created_from_post_edit_screen() ) {
			$language_code = $this->get_language_code_from_globals();

			return $language_code;
		} else {
			$language_code = $this->get_post_language_code_from_db( $parent_post_id );

			return $language_code;
		}
	}

	/**
	 * Whether the current post is being created from a post edit screen or not.
	 *
	 * @return bool
	 */
	private function is_created_from_post_edit_screen() {
		return ! empty( $_POST[ Tribe__Events__Pro__Integrations__WPML__WPML::$post_language_post_global_key ] );
	}

	/**
	 * Returns the post WPML language code reading it from the globals.
	 *
	 * @return string
	 */
	private function get_language_code_from_globals() {
		return $_POST[ Tribe__Events__Pro__Integrations__WPML__WPML::$post_language_post_global_key ];
	}

	/**
	 * Returns a post language code reading it from WPML tables.
	 *
	 * @param int $post_id
	 *
	 * @return bool|string Either the language code string (e.g. `en`) or `false` on failure.
	 */
	private function get_post_language_code_from_db( $post_id ) {
		$language_information = wpml_get_language_information( null, $post_id );
		if ( empty( $language_information ) || empty( $language_information['language_code'] ) ) {
			return false;
		}

		return $language_information['language_code'];
	}

	/**
	 * Returns the `trid` of a recurring event master series recurring event instance.
	 *
	 * @param int $event_id
	 * @param int $parent_event_id
	 *
	 * @return bool|int Either the master series recurring event instance trid (an int) or `false` on failure.
	 */
	public function get_master_series_instance_trid( $event_id, $parent_event_id ) {
		$master_parent_event_id = $this->get_master_parent_event_id( $parent_event_id );

		if ( empty( $master_parent_event_id ) ) {
			return false;
		}

		$this_event_start_date = get_post_meta( $event_id, '_EventStartDate', true );

		if ( empty( $this_event_start_date ) ) {
			return false;
		}

		$ids_and_start_dates = $this->get_master_series_ids_and_start_dates( $master_parent_event_id );

		return isset( $ids_and_start_dates[ $this_event_start_date ] ) ? $ids_and_start_dates[ $this_event_start_date ]->trid : false;
	}

	/**
	 * @return bool
	 */
	private function get_master_parent_event_id( $parent_event_id ) {
		if ( empty( $this->master_parent_event_ids_cache[ $parent_event_id ] ) ) {
			$this->master_parent_event_ids_cache[ $parent_event_id ] = isset( $_POST['icl_translation_of'] ) ? $_POST['icl_translation_of'] : $this->get_master_parent_event_id_from_db( $parent_event_id );
		}

		return ! empty( $this->master_parent_event_ids_cache[ $parent_event_id ] ) ? $this->master_parent_event_ids_cache[ $parent_event_id ] : false;
	}

	/**
	 * @param int $master_parent_event_id
	 */
	private function get_master_series_ids_and_start_dates( $master_parent_event_id ) {
		if ( empty( $this->master_series_ids_and_start_dates_cache[ $master_parent_event_id ] ) ) {
			$master_series_recurrence_dates = implode( "','", tribe_get_recurrence_start_dates( $master_parent_event_id ) );
			/** @var \wpdb $wpdb */
			global $wpdb;
			$wpml_translations_table = $wpdb->prefix . 'icl_translations';
			$post_type               = Tribe__Events__Main::POSTTYPE;
			$results                 = $wpdb->get_results( "SELECT p.ID AS 'event_id', pm.meta_value AS 'start_date', wpml.trid as 'trid'
					FROM {$wpdb->posts} p
					LEFT JOIN {$wpdb->postmeta} pm 
					ON p.ID = pm.post_id 
					LEFT JOIN {$wpml_translations_table} wpml 
					ON wpml.element_id = p.ID
					WHERE pm.meta_key = '_EventStartDate' 
					AND pm.meta_value IN ('{$master_series_recurrence_dates}')
					AND wpml.element_type = 'post_{$post_type}'
					AND wpml.element_id IN (SELECT ID FROM {$wpdb->posts} WHERE ID = {$master_parent_event_id} OR post_parent = {$master_parent_event_id}) 
					AND wpml.trid IS NOT NULL
					AND p.post_type = '{$post_type}'" );

			$this->master_series_ids_and_start_dates_cache[ $master_parent_event_id ] = ! empty( $results ) ? array_combine( wp_list_pluck( $results, 'start_date' ),
				$results ) : array();
		}

		return $this->master_series_ids_and_start_dates_cache[ $master_parent_event_id ];
	}

	/**
	 * @param $parent_event_id
	 *
	 * @return bool|null|string
	 */
	private function get_master_parent_event_id_from_db( $parent_event_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$table                  = $wpdb->prefix . 'icl_translations';
		$post_type              = Tribe__Events__Main::POSTTYPE;
		$master_parent_event_id = $wpdb->get_var( "SELECT element_id FROM {$table}
			WHERE trid = (
				SELECT trid FROM {$table} 
				WHERE element_id = {$parent_event_id}
				AND element_type= 'post_{$post_type}' ) 
			AND source_language_code IS NULL" );

		return ! empty( $master_parent_event_id ) ? $master_parent_event_id : false;
	}

	/**
	 * Inserts an event translation in the WPML tables.
	 *
	 * @param int    $event_id              The event post ID.
	 * @param string $language_code         The WPML language code to insert, e.g. 'ja'.
	 * @param  int   $trid                  A translation group identifier.
	 *                                      On a website with 4 languages 4 different posts will share the same `trid` value.
	 * @param bool   $overwrite_if_existing Whether the translation line should owerwrite an existing one or not.
	 *                                      By default the translation entry will not be overwritten.
	 *
	 * @return array
	 */
	public function insert_event_translation_for_language_code( $event_id, $language_code, $trid, $overwrite_if_existing = false ) {
		$src_language_code = $this->get_src_language_code_for_post( $event_id );
		$element_type      = 'post_' . Tribe__Events__Main::POSTTYPE;

		$insertion_result = wpml_add_translatable_content( $element_type, $event_id, $language_code, $trid );

		$wpml_did_insert_line_during_bootstrap = $insertion_result === WPML_API_CONTENT_EXISTS && $language_code !== $src_language_code;
		if ( $wpml_did_insert_line_during_bootstrap && $overwrite_if_existing ) {
			global $sitepress;
			if ( ! empty( $sitepress ) ) {
				/** @var Sitepress $sitepress */
				$insertion_result = $sitepress->set_element_language_details( $event_id, $element_type, $trid, $language_code, $src_language_code, false );
			}
		}

		$result = array( $language_code => $insertion_result );

		return $result;
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed|string
	 */
	private function get_src_language_code_for_post( $post_id ) {
		$post = get_post( $post_id );

		if ( empty( $post ) ) {
			return wpml_get_default_language();
		}

		$parent_id = ! empty( $post->post_parent ) ? $post->post_parent : $post->ID;

		if ( ! isset( $this->src_language_cache[ $parent_id ] ) ) {
			/** @var \wpdb $wpdb */
			global $wpdb;
			$this->src_language_cache[ $parent_id ] = $wpdb->get_var( "SELECT language_code
				FROM {$wpdb->prefix}icl_translations 
				WHERE element_type = 'post_{$post->post_type}'
				AND element_id = {$parent_id}
				AND source_language_code IS NULL" );
		}

		return $this->src_language_cache[ $parent_id ];
	}
}
