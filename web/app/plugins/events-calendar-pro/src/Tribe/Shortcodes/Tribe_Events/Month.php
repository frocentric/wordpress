<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Events__Pro__Shortcodes__Tribe_Events__Month {
	protected $shortcode;
	protected $date = '';

	public function __construct( Tribe__Events__Pro__Shortcodes__Tribe_Events $shortcode ) {
		$this->shortcode = $shortcode;
		$this->setup();
		$this->hooks();
	}

	protected function hooks() {
		add_filter( 'tribe_events_pro_tribe_events_shortcode_title_bar', array( $this, 'title_bar' ) );
		add_filter( 'tribe_get_next_month_link', array( $this, 'next_month_url' ) );
		add_filter( 'tribe_get_previous_month_link', array( $this, 'prev_month_url' ) );
	}

	protected function setup() {
		Tribe__Events__Main::instance()->displaying = 'month';
		$this->set_current_month();
		$this->shortcode->prepare_default();
		tribe_asset_enqueue( 'tribe-events-ajax-calendar' );
		$this->shortcode->set_template_object( new Tribe__Events__Template__Month( $this->shortcode->get_query_args() ) );
	}

	protected function set_current_month() {
		$default = date_i18n( 'Y-m-d' );
		$this->date = $this->shortcode->get_url_param( 'tribe-bar-date' );

		if ( empty( $this->date ) ) {
			$this->date = $this->shortcode->get_attribute( 'date', $default );
		}

		// Expand "yyyy-mm" dates to "yyyy-mm-dd" format
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}$/', $this->date ) ) {
			$this->date .= '-01';
		}
		// If we're not left with a "yyyy-mm-dd" date, override with the today's date
		elseif ( ! preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->date ) ) {
			$this->date = $default;
		}

		$this->shortcode->update_query( array(
			'eventDate' => $this->date,
		) );
	}

	/**
	 * Add Title Bar to Month View Shortcode
	 *
	 * @since 4.4.31
	 *
	 */
	public function title_bar() {
		tribe_get_template_part( 'month/title-bar' );
	}

	/**
	 * Returns the next month pagination URL for use in embedded month views.
	 *
	 * Can be overridden by adding a further filter on "tribe_get_next_month_link" with
	 * a priority greater than 10.
	 *
	 * @return string
	 */
	public function next_month_url() {
		$path = $_SERVER['REQUEST_URI'];
		$next_month = Tribe__Events__Main::instance()->nextMonth( $this->date );
		return add_query_arg( 'date', $next_month, $path );
	}

	/**
	 * Returns the previous month pagination URL for use in embedded month views.
	 *
	 * Can be overridden by adding a further filter on "tribe_get_previous_month_link" with
	 * a priority greater than 10.
	 *
	 * @return string
	 */
	public function prev_month_url() {
		$prev_month = Tribe__Events__Main::instance()->previousMonth( $this->date );
		return add_query_arg( 'date', $prev_month, get_home_url( null, $GLOBALS[ 'wp' ]->request ) );
	}
}
