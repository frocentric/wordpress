<?php
/**
 * Registers and Enqueues the assets
 *
 * @since  4.4.30
 */
class Tribe__Events__Pro__Assets {
	/**
	 * Caches the result of the `should_enqueue_frontend` check.
	 *
	 * @since 5.0.0
	 *
	 * @var bool
	 */
	protected $should_enqueue_frontend;

	/**
	 * Registers and Enqueues the assets
	 *
	 * @since  4.4.30
	 *
	 * @return void
	 */
	public function register() {
		$pro = Tribe__Events__Pro__Main::instance();

		// Vendor
		tribe_assets(
			$pro,
			[
				[ 'tribe-events-pro-imagesloaded', 'vendor/imagesloaded/imagesloaded.pkgd.js', [ 'tribe-events-pro' ] ],
				[ 'tribe-events-pro-isotope', 'vendor/isotope/isotope.pkgd.js', [ 'tribe-events-pro-imagesloaded' ] ],
				[ 'tribe-events-pro-slimscroll', 'vendor/nanoscroller/jquery.nanoscroller.js', [ 'tribe-events-pro', 'jquery-ui-draggable' ] ],
			],
			null,
			[
				'in_footer' => false,
			]
		);

		// Vendor: Admin
		tribe_assets(
			$pro,
			[
				[ 'tribe-events-pro-handlebars', 'vendor/handlebars/handlebars.min.js' ],
				[ 'tribe-events-pro-moment', 'vendor/momentjs/moment.min.js' ],
			],
			'admin_enqueue_scripts',
			[
				'conditionals' => [ Tribe__Main::instance(), 'should_load_common_admin_css' ],
			]
		);

		$api_url = 'https://maps.google.com/maps/api/js';

		/**
		 * Allows users to use a diferent Google Maps JS URL
		 *
		 * @deprecated  4.4.33
		 *
		 * @param string $url
		 */
		$google_maps_js_url = apply_filters( 'tribe_events_pro_google_maps_api', $api_url );

		tribe_asset(
			$pro,
			'tribe-pro',
			'pro.js',
			[],
			null,
			[
				'priority' => 5,
			]
		);

		tribe_asset(
			$pro,
			'tribe_events-premium-admin',
			'events-admin.css',
			[],
			'admin_enqueue_scripts',
			[
				'priority' => 10,
			]
		);

		tribe_asset(
			$pro,
			'tribe-events-pro',
			'tribe-events-pro.js',
			[ 'jquery', 'tribe-events-calendar-script' ],
			'wp_enqueue_scripts',
			[
				'conditionals' => [ $this, 'should_enqueue_frontend' ],
				'in_footer'    => false,
				'localize'     => [
					'name' => 'TribeEventsPro',
					'data' => [ $this, 'get_data_tribe_events_pro' ],
				],
			]
		);

		tribe_asset(
			$pro,
			'tribe-events-pro-photo',
			'tribe-events-photo-view.js',
			[ 'tribe-events-pro-isotope' ],
			null,
			[
				'localize' => [
					'name' => 'TribePhoto',
					'data' => [
						'ajaxurl'     => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
						'tribe_paged' => tribe_get_request_var( 'tribe_paged', 0 ),
					],
				],
			]
		);

		tribe_asset(
			$pro,
			'tribe-events-pro-week',
			'tribe-events-week.js',
			array( 'tribe-events-pro-slimscroll' ),
			null,
			array(
				'localize' => array(
					'name' => 'TribeWeek',
					'data' => array(
						'ajaxurl'   => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
						'post_type' => Tribe__Events__Main::POSTTYPE,
					),
				),
			)
		);

		$pro_ajax_maps_deps = array( 'jquery-placeholder' );

		if ( ! tribe_is_using_basic_gmaps_api() ) {
			// This dependency is only available when a custom gMaps API Key is being used.
			$pro_ajax_maps_deps[] = 'tribe-events-google-maps';
		}

		tribe_asset(
			$pro,
			'tribe-events-pro-geoloc',
			'tribe-events-ajax-maps.js',
			$pro_ajax_maps_deps,
			null,
			array(
				'localize' => array(
					'name' => 'GeoLoc',
					'data' => array( $this, 'get_data_tribe_geoloc' ),
				),
			)
		);

		tribe_assets(
			$pro,
			array(
				array( 'tribe_events-premium-admin-style', 'events-admin.css', array() ),
				array( 'tribe_events-premium-admin', 'events-admin.js', array( 'jquery-ui-datepicker', 'wp-util', 'tribe-timepicker' ) ),
			),
			array( 'tribe_venues_enqueue', 'tribe_events_enqueue' )
		);

		tribe_assets(
			$pro,
			array(
				array( 'tribe-events-calendar-full-pro-mobile-style', 'tribe-events-pro-full-mobile.css', array( 'tribe-events-calendar-pro-style' ) ),
				array( 'tribe-events-calendar-pro-mobile-style', 'tribe-events-pro-theme-mobile.css', array( 'tribe-events-calendar-pro-style' ) ),
			),
			'wp_enqueue_scripts',
			array(
				'media'        => 'only screen and (max-width: ' . tribe_get_mobile_breakpoint() . 'px)',
				'groups'       => array( 'events-pro-styles' ),
				'conditionals' => array(
					'operator' => 'AND',
					array( $this, 'is_mobile_breakpoint' ),
					array( $this, 'should_enqueue_frontend' ),
				),
			)
		);

		tribe_asset(
			$pro,
			'tribe-events-full-pro-calendar-style',
			'tribe-events-pro-full.css',
			array(),
			'wp_enqueue_scripts',
			array(
				'priority'     => 5,
				'conditionals' => array(
					'operator' => 'AND',
					array( $this, 'is_style_option_tribe' ),
					array( $this, 'should_enqueue_frontend' ),
				),
			)
		);

		tribe_asset(
			$pro,
			'tribe-events-calendar-pro-style',
			$this->get_style_file(),
			array(),
			'wp_enqueue_scripts',
			array(
				'groups'       => array( 'events-pro-styles' ),
				'conditionals' => array(
					array( $this, 'should_enqueue_frontend' ),
				),
			)
		);

		tribe_asset(
			$pro,
			'tribe-events-calendar-pro-override-style',
			Tribe__Events__Templates::locate_stylesheet( 'tribe-events/pro/tribe-events-pro.css' ),
			array(),
			'wp_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'should_enqueue_frontend' ),
				'groups'       => array( 'events-pro-styles' ),
			)
		);


		tribe_asset(
			$pro,
			'tribe-mini-calendar',
			'widget-calendar.js',
			array( 'jquery' ),
			null,
			array(
				'localize'     => array(
					'name' => 'TribeMiniCalendar',
					'data' => array( 'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) ),
				),
			)
		);

		tribe_asset(
			$pro,
			'tribe-this-week',
			'widget-this-week.js',
			array( 'jquery' ),
			null,
			array(
				'localize'     => array(
					'name' => 'tribe_this_week',
					'data' => array( 'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) ),
				),
			)
		);

		tribe_asset(
			$pro,
			'tribe-events-countdown-widget',
			'widget-countdown.js',
			array( 'jquery' ),
			null,
			array()
		);

		tribe_asset(
			$pro,
			'widget-calendar-pro-style',
			$this->get_widget_style_file(),
			array(),
			null,
			array()
		);

		tribe_asset(
			$pro,
			Tribe__Events__Main::POSTTYPE . '-widget-calendar-pro-override-style',
			Tribe__Events__Templates::locate_stylesheet( 'tribe-events/pro/widget-calendar.css' ),
			array(),
			null,
			array()
		);
	}

	/**
	 * Checks if we have a mobile Breakpoint
	 *
	 * @since  4.4.30
	 *
	 * @return bool
	 */
	public function is_mobile_breakpoint() {
		$mobile_break = tribe_get_mobile_breakpoint();
		return $mobile_break > 0;
	}

	/**
	 * Checks if we are using Tribe setting for Style
	 *
	 * @since  4.4.30
	 *
	 * @return bool
	 */
	public function is_style_option_tribe() {
		$style_option = tribe_get_option( 'stylesheetOption', 'tribe' );
		return 'tribe' === $style_option;
	}

	/**
	 * Due to how we define which style we use based on an Option on the Administration
	 * we need to determine this file.
	 *
	 * @since  4.4.30
	 *
	 * @return string
	 */
	public function get_style_file() {
		$name = tribe_get_option( 'stylesheetOption', 'tribe' );

		$stylesheets = array(
			'tribe'    => 'tribe-events-pro-theme.css',
			'full'     => 'tribe-events-pro-full.css',
			'skeleton' => 'tribe-events-pro-skeleton.css',
		) ;

		// By default we go with `tribe`
		$file = $stylesheets['tribe'];

		// if we have one we use it
		if ( isset( $stylesheets[ $name ] ) ) {
			$file = $stylesheets[ $name ];
		}

		/**
		 * Allows filtering of the Stylesheet file for Events Calendar Pro
		 *
		 * @deprecated  4.4.30
		 *
		 * @param string $file Which file we are loading
		 * @param string $name Option from the DB of style we are using
		 */
		return apply_filters( 'tribe_events_pro_stylesheet_url', $file, $name );
	}


	/**
	 * Due to how we define which style we use based on an Option on the Administration
	 * we need to determine this file.
	 *
	 * @since  4.4.33
	 *
	 * @return string
	 */
	public function get_widget_style_file() {
		$name = tribe_get_option( 'stylesheetOption', 'tribe' );

		$stylesheets = array(
			'tribe'    => 'widget-theme.css',
			'full'     => 'widget-full.css',
			'skeleton' => 'widget-skeleton.css',
		) ;

		// By default we go with `tribe`
		$file = $stylesheets['tribe'];

		// if we have one we use it
		if ( isset( $stylesheets[ $name ] ) ) {
			$file = $stylesheets[ $name ];
		}

		/**
		 * Allows filtering of the Stylesheet file for Events Calendar Pro Widgets
		 *
		 * @deprecated  4.4.33
		 *
		 * @param string $file Which file we are loading
		 * @param string $name Option from the DB of style we are using
		 */
		return apply_filters( 'tribe_events_pro_widget_calendar_stylesheet_url', $file, $name );
	}

	/**
	 * When to enqueue the Pro Styles on the front-end
	 *
	 * @since  4.4.30
	 * @since 5.0.0 Cache the check value.
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend() {
		if ( null !== $this->should_enqueue_frontend ) {
			return $this->should_enqueue_frontend;
		}

		global $post;

		$should_enqueue = (
			tribe_is_event_query()
			|| ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'tribe_events' ) )
		);

		$this->should_enqueue_frontend = $should_enqueue;

		return $should_enqueue;
	}

	/**
	 * Gets the localize data for Main Events Calendar Pro
	 *
	 * @since  4.4.30
	 *
	 * @return array
	 */
	public function get_data_tribe_events_pro() {
		$data = array(
			'geocenter'           => Tribe__Events__Pro__Geo_Loc::instance()->estimate_center_point(),
			'map_tooltip_event'   => esc_html( sprintf( _x( '%s: ', 'Event title map marker prefix', 'tribe-events-calendar-pro' ), tribe_get_event_label_singular() ) ),
			'map_tooltip_address' => esc_html__( 'Address: ', 'tribe-events-calendar-pro' ),
		);

		/**
		 * Filters the Main Events Calendar Pro script localization
		 *
		 * @since 4.4.30
		 *
		 * @param array  $data        JS variable
		 * @param string $object_name The localization object var name.
		 * @param string $script      Which script this localizes
		 */
		$data = apply_filters( 'tribe_events_pro_localize_script', $data, 'TribeEventsPro', 'tribe-events-pro' );

		return $data;
	}

	/**
	 * Gets the localize data for Geoloc on Events Calendar Pro
	 *
	 * @since  4.4.30
	 *
	 * @return array
	 */
	public function get_data_tribe_geoloc() {

		$data = array(
			'ajaxurl'  => admin_url( 'admin-ajax.php', admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ) ),
			'nonce'    => wp_create_nonce( 'tribe_geosearch' ),
			'map_view' => 'map' === tribe( 'tec.main' )->displaying,
			'pin_url'  => Tribe__Customizer::instance()->get_option( array( 'global_elements', 'map_pin' ), false ),
		);

		/**
		 * Filters the Events Calendar Pro Maps script localization
		 *
		 * @since  4.4.30  Removed the Third param
		 *
		 * @param  array   $data    JS variable
		 * @param  string  $script  Which script this localizes
		 */
		$data = apply_filters( 'tribe_events_pro_geoloc_localize_script', $data, 'tribe-events-pro-geoloc' );

		return $data;

	}

}
