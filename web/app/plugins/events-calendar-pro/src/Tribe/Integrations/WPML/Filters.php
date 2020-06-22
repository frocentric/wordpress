<?php


class Tribe__Events__Pro__Integrations__WPML__Filters {

	/**
	 * @var Tribe__Events_Pro__Integrations__WPML__Filters
	 */
	protected static $instance;

	/**
	 * @var int
	 */
	protected $recurring_event_parent_id;

	/**
	 * @return Tribe__Events_Pro__Integrations__WPML__Filters
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function filter_wpml_is_redirected_event( $redirect_target, $post_id, $query ) {
		if ( $redirect_target ) {
			if ( 'all' === $query->get( 'eventDisplay' ) || $query->get( 'eventDate' ) ) {
				$redirect_target = false;
			}
		}

		if ( $this->is_single_event_main_query( $query ) ) {
			$this->recurring_event_parent_id = $post_id;
			add_action( 'tribe_events_pro_recurring_event_parent_id', array( $this, 'filter_recurring_event_parent_id' ) );
		}

		return $redirect_target;
	}

	public function filter_wpml_ls_languages_event( $languages ) {
		if ( ! tribe_is_showing_all() ) {
			return $languages;
		}

		foreach ( $languages as $key => $language ) {
			$parts = explode( '/', untrailingslashit( $language['url'] ) );
			array_pop( $parts );
			$parts[]           = 'all';
			$language['url']   = trailingslashit( implode( '/', $parts ) );
			$languages[ $key ] = $language;
		}

		return $languages;
	}

	public function filter_wpml_get_ls_translations_event( $translations, $query ) {
		if ( $this->is_all_event_query( $query ) ) {
			$translations = apply_filters( 'wpml_content_translations', null, $query->get( 'post_parent' ), 'tribe_events' );
		}

		return $translations;
	}

	public function filter_tribe_events_pre_get_posts( $query ) {
		if ( $query->get( 'p' ) && $query->get( 'post_parent' ) ) {
			unset( $query->query_vars['p'] );
		}

		return $query;
	}

	public function filter_wpml_pre_parse_query_event( $query ) {
		if ( $this->is_all_event_query( $query ) ) {
			$query->set( 'tribe_name_before_wpml_parse_query', $query->get( 'name' ) );
		}

		return $query;
	}

	public function filter_wpml_post_parse_query_event( WP_Query $query ) {
		$name_before_wpml_parse_query = $query->get( 'tribe_name_before_wpml_parse_query', '' );
		if ( $this->is_all_event_query( $query ) && ! empty( $name_before_wpml_parse_query ) ) {
			$query->set( 'name', $query->get( 'tribe_name_before_wpml_parse_query' ) );
		}

		return $query;
	}

	private function is_all_event_query( WP_Query $query ) {
		return $query->get( 'post_type' ) == 'tribe_events' && 'all' === $query->get( 'eventDisplay' );
	}

	public function filter_recurring_event_parent_id() {
		return $this->recurring_event_parent_id;
	}

	protected function is_single_event_main_query( WP_Query $query ) {
		return $query->is_main_query() && $query->is_single() && $query->get( 'post_type' ) === Tribe__Events__Main::POSTTYPE;
	}

	/**
	 * Adds Pro domain to the list of domains to use to translate strings.
	 *
	 * @param array $domains
	 *
	 * @return array
	 */
	public function filter_tribe_events_rewrite_i18n_domains( array $domains ) {
		$domains['tribe-events-calendar-pro'] = Tribe__Events__Pro__Main::instance()->pluginDir . 'lang/';

		return $domains;
	}

	/**
	 * Filters the rewrite slugs to use for the map view taking WPML existence into account.
	 *
	 * @param array $rewrite_slugs
	 *
	 * @return array
	 */
	public function filter_tribe_events_pro_geocode_rewrite_slugs( array $rewrite_slugs ) {
		// use the non translated version, we'll translate it below
		$geoloc_rewrite_slug = Tribe__Settings_Manager::get_option( 'geoloc_rewrite_slug', 'map' );

		$translations = Tribe__Events__Integrations__WPML__Utils::get_wpml_i18n_strings( array( $geoloc_rewrite_slug ) );

		return $translations[0];
	}

	/**
	 * Filters the "all" fragment of the all recurrences link.
	 *
	 * @param string $all_frag
	 * @param int    $post_id
	 */
	public function filter_tribe_events_pro_all_link_frag( $all_frag, $post_id ) {
		$language_information = wpml_get_language_information( null, $post_id );

		if ( ! is_array( $language_information ) || empty( $language_information['locale'] ) ) {
			return $all_frag;
		}

		$locale = $language_information['locale'];

		$all_frags = Tribe__Events__Integrations__WPML__Utils::get_wpml_i18n_strings( array( 'all' ), $locale );

		if ( empty( $all_frags[0] ) || ! is_array( $all_frags[0] ) ) {
			return $all_frag;
		}

		// the last translation is the one in the language we seek
		return end( $all_frags[0] );
	}

	/**
	 * Filters the permalink generated for a recurring event "all" view to remove aberrations.
	 *
	 * @param string $event_url
	 * @param int    $event_id
	 *
	 * @return string
	 */
	public function filter_tribe_events_pro_get_all_link( $event_url, $event_id ) {
		$post = get_post( Tribe__Main::post_id_helper( $event_id ) );

		if ( ! tribe_is_event( $post ) || $post->post_parent != 0 ) {
			return $event_url;
		}

		$post_name = $post->post_name;

		// WPML might replace the post name with `<post_name>/<date>`; we undo that here.
		$event_url = preg_replace( '~' . preg_quote( $post_name ) . '\\/\\d{4}-\\d{2}-\\d{2}~', $post_name, $event_url );

		return $event_url;
	}

	/**
	 * We use the `post_type_link` filter as an action to move the `WPML_Slug_Translation::post_type_link_filter` method.
	 *
	 * The `WPML_Slug_Translation::post_type_link_filter` method is moved down the filter chain from 1 (WPML set priority)
	 * to 15 (after default).
	 * This change allows the smart WPML slug translation filter to work on the Eng version of the correct post permalink.
	 *
	 * @param string $post_type_link
	 *
	 * @return string The untouched post type link.
	 */
	public function move_wpml_slug_translation_filter( $post_type_link ) {
		if ( function_exists( 'wpml_st_load_slug_translation' ) && $slug_translation = wpml_st_load_slug_translation() ) {
			remove_filter( 'post_type_link', array( $slug_translation, 'post_type_link_filter' ), 1 );
			add_filter( 'post_type_link', array( $slug_translation, 'post_type_link_filter' ), 15, 4 );
		}

		return $post_type_link;
	}

	/**
	 * Attach language information to recurring event permalinks.
	 *
	 * @since 4.4.23
	 *
	 * @see https://wpml.org/wpml-hook/wpml_permalink/ Documentation of wpml_permalink
	 *
	 * @param string $post_link
	 * @param boolean $has_structure
	 * @return string
	 */
	public function filter_recurring_event_permalinks( $post_link, $has_structure ) {
		return apply_filters( 'wpml_permalink', $post_link );
	}
}
