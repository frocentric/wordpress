<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Events__Pro__Shortcodes__Tribe_Events__Map {
	protected $shortcode;
	protected $date = '';

	public function __construct( Tribe__Events__Pro__Shortcodes__Tribe_Events $shortcode ) {
		$this->shortcode = $shortcode;
		$this->setup();
		$this->hooks();
	}

	protected function hooks() {
		add_filter( 'tribe_events_pro_tribe_events_shortcode_before_render', array( $this, 'title_bar' ) );
		add_action( 'tribe_events_pro_tribe_events_shortcode_pre_render', array( $this, 'shortcode_pre_render' ) );
		add_action( 'tribe_events_pro_tribe_events_shortcode_post_render', array( $this, 'shortcode_post_render' ) );
		add_action( 'tribe_events_pro_tribe_events_shortcode_before_render', array( $this, 'load_map_container' ) );
	}

	protected function setup() {
		Tribe__Events__Main::instance()->displaying = 'map';
		$this->shortcode->set_current_page();
		$this->shortcode->prepare_default();

		Tribe__Events__Pro__Main::instance()->enqueue_pro_scripts();
		tribe_asset_enqueue_group( 'events-pro-styles' );
		tribe_asset_enqueue( 'tribe-events-pro-geoloc' );

		$this->shortcode->set_template_object( new Tribe__Events__Pro__Templates__Map( $this->shortcode->get_query_args() ) );
	}

	/**
	 * Filters the baseurl of ugly links
	 *
	 * @param string $url URL to filter
	 *
	 * @return string
	 */
	public function filter_baseurl( $url ) {
		return trailingslashit( get_home_url( null, $GLOBALS['wp']->request ) );
	}

	/**
	 * Add Title Bar to Map View Shortcode
	 *
	 * @since 4.4.31
	 *
	 */
	public function title_bar() {
		tribe_get_template_part( 'pro/map/title-bar' );
	}

	/**
	 * Load the map container before the HTML of the shortcode is rendered the function is called by the action:
	 *
	 * - tribe_events_pro_tribe_events_shortcode_before_render
	 *
	 * @see Tribe__Events__Pro__Shortcodes__Tribe_Events->render_view()
	 *
	 * @since 4.4.26
	 */
	public function load_map_container() {
		if ( tribe_is_using_basic_gmaps_api() ) {
			$embed_url = tribe_events_get_map_view_basic_embed_url();

			if ( $embed_url ) {
			    tribe_get_template_part( 'modules/map-basic', null, array(
			        'width'     => '100%',
			        'height'    => '440px',
			        'embed_url' => $embed_url,
			    ) );
			}
		} else {
			tribe_get_template_part( 'pro/map/gmap-container' );
		}
	}

	public function shortcode_pre_render() {
		add_filter( 'tribe_events_force_ugly_link', '__return_true' );
		add_filter( 'tribe_events_ugly_link_baseurl', array( $this, 'filter_baseurl' ) );
	}

	public function shortcode_post_render() {
		remove_filter( 'tribe_events_force_ugly_link', '__return_true' );
		remove_filter( 'tribe_events_ugly_link_baseurl', array( $this, 'filter_baseurl' ) );
	}
}
