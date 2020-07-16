<?php
/**
 * Controls the filter views.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }

if ( ! class_exists( 'Tribe__Events__Filterbar__View' ) ) {
	class Tribe__Events__Filterbar__View {

		/**
		 * @static
		 * @var The instance of the class.
		 */
		protected static $instance;

		/**
		 * @var string The absolute path to the main plugin file
		 */
		protected static $plugin_file = '';

		/**
		 * @var The directory of the plugin.
		 */
		public $pluginDir;

		/**
		 * @var the plugin path.
		 */
		public $pluginPath;

		/**
		 * @var Whether filters sidebar is being displayed or not.
		 */
		protected $sidebarDisplayed;

		/**
		 * @var The default filters for a MU site.
		 */
		protected static $defaultMuFilters;

		const VERSION = '4.10.0';

		/**
		 * The Events Calendar Required Version
		 * Use Tribe__Events__Filterbar__Plugin_Register instead
		 *
		 * @deprecated 4.6
		 *
		 */
		const REQUIRED_TEC_VERSION = '5.0.1-dev';

		/**
		 * Where in the themes we will look for templates
		 *
		 * @since 4.9.0
		 *
		 * @var string
		 */
		public $template_namespace = 'events-filterbar';

		/**
		 * Create the plugin instance and include the other class.
		 *
		 * @param string $plugin_file_path Deprecated in 4.3, path set by TRIBE_EVENTS_FILTERBAR_FILE instead.
		 * @since 3.4
		 * @return void
		 */
		public static function init( $plugin_file_path = null ) {

			if ( null === $plugin_file_path ) {
				$plugin_file_path = TRIBE_EVENTS_FILTERBAR_FILE;
			}

			self::$plugin_file = $plugin_file_path;
			self::$instance = self::instance();

			tribe_register_provider( Tribe\Events\Filterbar\Service_Providers\Context::class );
			tribe_register_provider( Tribe\Events\Filterbar\Views\V2\Service_Provider::class );
		}

		/**
		 * The singleton function.
		 *
		 * @since 3.4
		 * @return Tribe__Events__Filterbar__View The instance.
		 */
		public static function instance() {
			if ( ! self::$instance instanceof self ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * The class constructor.
		 *
		 * @author PaulHughes01
		 * @since 3.4
		 * @return void
		 */
		public function __construct() {

			$this->pluginPath = trailingslashit( TRIBE_EVENTS_FILTERBAR_DIR );
			$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
			$this->pluginUrl = trailingslashit( plugins_url() . '/' . $this->pluginDir );
			$this->sidebarDisplayed = false;
			$this->register_active_plugin();

			add_action( 'wp', array( $this, 'setSidebarDisplayed' ) );
			add_action( 'tribe_events_ajax_accessibility_check', array( $this, 'display_dynamic_a11y_notice' ) );
			add_action( 'parse_query', array( $this, 'maybe_initialize_filters_for_query' ), 10, 1 );
			add_action( 'tribe_repository_events_query', array( $this, 'maybe_initialize_filters_for_query' ), 1, 1 );
			add_action( 'current_screen', array( $this, 'maybe_initialize_filters_for_screen' ), 10, 0 );
			add_filter( 'body_class', array( $this, 'addBodyClass' ) );

			add_filter( 'tribe_events_template_paths', array( $this, 'template_paths' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueStylesAndScripts' ), 11 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );

			$settings_page = new Tribe__Events__Filterbar__Settings();
			$settings_page->set_hooks();

			add_action( 'tribe_load_text_domains', [ $this, 'loadTextDomain' ] );

			// Load multisite defaults
			if ( is_multisite() ) {
				$tribe_events_filters_default_mu_filters = array();
				if ( file_exists( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' ) )
					include( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' );
				self::$defaultMuFilters = apply_filters( 'tribe_events_mu_filters_default_filters', $tribe_events_filters_default_mu_filters );
			}

			add_action( 'admin_init', array( $this, 'run_updates' ), 10, 0 );

			/** Load the main filter-view.css stylesheet */
			tribe_asset(
				$this,
				'tribe-filterbar-styles',
				'filter-view.css',
				array( 'tribe-select2-css', 'tribe-common-admin', 'dashicons' ),
				'wp_enqueue_scripts',
				array(
					'conditionals' => array( $this, 'should_enqueue_assets' ),
				)
			);

			/** Load the mobile filter-view stylesheet */
			tribe_asset(
				$this,
				'tribe-filterbar-mobile-styles',
				'filter-view-mobile.css',
				array( 'tribe-select2-css', 'tribe-common-admin', 'dashicons' ),
				'wp_enqueue_scripts',
				array(
					'media'        => 'only screen and (max-width: ' . tribe_get_mobile_breakpoint() . 'px)',
					'conditionals' => array( $this, 'should_enqueue_assets' ),
				)
			);

			/** Load JS */
			tribe_asset(
				$this,
				'tribe-filterbar-js',
				'filter-scripts.js',
				array( 'tribe-dropdowns', 'jquery-ui-slider' ),
				'wp_enqueue_scripts',
				array(
					'conditionals' => array( $this, 'should_enqueue_assets' ),
				)
			);
		}

		/**
		 * Registers this plugin as being active for other tribe plugins and extensions
		 *
		 * @return bool Indicates if Tribe Common wants the plugin to run
		 */
		public function register_active_plugin() {
			if ( ! function_exists( 'tribe_register_plugin' ) ) {
				return true;
			}

			return tribe_register_plugin( TRIBE_EVENTS_FILTERBAR_FILE, __CLASS__, self::VERSION );
		}

		/**
		 * Enqueue the plugin stylesheet(s).
		 *
		 * @author PaulHughes01
		 * @since 3.4
		 * @return void
		 */
		public function enqueueStylesAndScripts() {

			if ( ! $this->should_enqueue_assets() ) {
				return false;
			}

			$show_filter = apply_filters( 'tribe_events_filters_should_show', in_array( get_post_type(), array( Tribe__Events__Main::VENUE_POST_TYPE, Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) ? false : true );

			if ( $show_filter ) {
				// Only display filters before template if the layout is horizontal
				if ( tribe_get_option( 'events_filters_layout', 'vertical' ) == 'vertical' ) {
					add_action( 'tribe_events_bar_after_template', array( $this, 'displaySidebar' ), 25 );
				} else {
					if ( tribe_get_option( 'tribeDisableTribeBar', false ) == true ) {
						add_action( 'tribe_events_before_template', array( $this, 'displaySidebar' ), 25 );
					} else {
						add_action( 'tribe_events_bar_after_template', array( $this, 'displaySidebar' ), 25 );
					}
				}
			}

			tribe_asset_enqueue( 'tribe-events-calendar-script' );

			wp_enqueue_style( 'custom-jquery-styles' );

			// Check for override stylesheet.
			$user_stylesheet_url = Tribe__Events__Templates::locate_stylesheet( 'tribe-events/filterbar/filter-view.css' );
			$user_stylesheet_url = apply_filters( 'tribe_events_filterbar_stylesheet_url', $user_stylesheet_url );

			// If override stylesheet exists, then enqueue it.
			if ( $user_stylesheet_url ) {
				wp_enqueue_style( 'tribe-events-filterbar-override-style', $user_stylesheet_url );
			}

			wp_localize_script( 'tribe-filterbar-js', 'tribe_filter', array(
				'reverse_position'       => tribe_get_option( 'reverseCurrencyPosition', false ),
				'currency_symbol'        => tribe_get_option( 'defaultCurrencySymbol' ),
				'featured_active_filter' => _x( 'Active', 'Featured Events active filter display label', 'tribe-events-filter-view' ),
			) );
		}

		/**
		 * Enqueue the admin scripts.
		 *
		 * @author PaulHughes01
		 * @since 3.4
		 * @return void
		 */
		public function enqueueAdminScripts() {
			global $current_screen;
			if ( $current_screen->id == 'tribe_events_page_' . Tribe__Settings::$parent_slug && isset( $_GET['tab'] ) && $_GET['tab'] == 'filter-view' ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

		}

		public function setSidebarDisplayed( $unused_query ) {
			if ( tribe_is_event_query() && ( ! is_single() || tribe_is_showing_all() ) && ! is_admin() ) {
				$active_filters = $this->get_active_filters();
				if ( ! empty( $active_filters ) ) {
					$this->sidebarDisplayed = true;
				}
			}
		}

		/**
		 * A simple conditional to check if the current front-end page is one on which Filter Bar
		 * assets should be loaded.
		 *
		 * @since 4.5.3
		 *
		 * @return boolean
		 */
		public function should_enqueue_assets() {
			return tribe_is_event_query() ||  tribe_is_event_organizer() || tribe_is_event_venue();
		}

		/**
		 * Add the filters body class.
		 *
		 * @author PaulHughes01
		 * @since 3.4
		 * @return array The new set of body classes.
		 */
		public function addBodyClass( $classes ) {
			$classes[] = 'tribe-events-filter-view';
			$classes[] = 'tribe-filters-' . tribe_get_option( 'events_filters_default_state', 'closed' );
			$classes[] = 'tribe-filters-' . tribe_get_option( 'events_filters_layout', 'vertical' );

			return $classes;
		}

		/**
		 * Add premium plugin paths for each file in the templates array
		 *
		 * @param $template_paths array
		 * @return array
		 * @since 3.4
		 */
		public function template_paths( $template_paths = array() ) {
			// To prevent problems with Backwards compatibility
			$template_paths['filter-bar'] = $this->pluginPath;

			// New Path
			$template_paths['filterbar'] = $this->pluginPath;
			return $template_paths;
		}

		/**
		 * Display the filters sidebar.
		 *
		 * @author PaulHughes01
		 * @since 3.4
		 * @return void
		 */
		public function displaySidebar( $html ) {
			if ( $this->sidebarDisplayed ) {
				if ( ! is_single() || tribe_is_showing_all() ) {
					ob_start();
					tribe_get_template_part( 'filter-bar/filter-view-' . tribe_get_option( 'events_filters_layout', 'vertical' ) );
					$html = ob_get_clean() . $html;
				}
				echo $html;
			}
		}

		/**
		 * Returns whether or not there are tribe specific query vars in the given query object
		 *
		 * @param WP_Query $query Query object
		 *
		 * @return boolean
		 */
		public function is_tribe_query( $query ) {
			// if the post type is the event post type, we're in a tribe query
			if ( $query->get( 'post_type' ) === Tribe__Events__Main::POSTTYPE ) {
				return true;
			}

			foreach ( array_keys( $query->query ) as $key ) {
				if ( 0 === strpos( $key, 'tribe_' ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Create the default filters and execute the action for other filters to hook into.
		 * NOTE: The slug must be one word, no underscores or hyphens.
		 *
		 * @param WP_Query $query
		 *
		 * @return void
		 */
		public function maybe_initialize_filters_for_query( $query = null ) {
			/**
			 * Filters whether to initialize Filterbar filters for the query or not.
			 *
			 * @since 4.9.0
			 *
			 * @param bool          $initialize_filters Whether to initialize Filterbar filters for the query or not.
			 * @param WP_Query|null $query              The current query object, if any.
			 */
			$initialize_filters = apply_filters( 'tribe_events_filter_bar_initialize_filters', true, $query );

			if ( ! $initialize_filters ) {
				return;
			}

			if ( $this->is_tribe_query( $query ) ) {
				if (
					$query->is_main_query()
					|| ( defined( 'DOING_AJAX' ) && DOING_AJAX )
					|| (
						! empty( $query->query['tribe_render_context'] )
						&& 'default' === $query->query['tribe_render_context']
					)
				) {
					$this->initialize_filters();
				}
			}
		}

		public function maybe_initialize_filters_for_screen() {
			if ( $this->on_settings_page() ) {
				$this->initialize_filters();
			}
		}

		private function on_settings_page() {
			global $current_screen;
			if (
				   isset( $current_screen )
				&& $current_screen->id == 'tribe_events_page_tribe-common'
				&& isset( $_GET['tab'] )
				&& $_GET['tab'] == 'filter-view'
			) {
				return true;
			}
			return false;
		}

		public function initialize_filters() {
			static $initialized = false;
			if ( $initialized ) {
				return; // only run once
			}
			$initialized = true;

			tribe_singleton(
				'filterbar.filters.category',
				new Tribe__Events__Filterbar__Filters__Category( sprintf( esc_html__( '%s Category', 'tribe-events-filter-view' ), tribe_get_event_label_singular() ), 'eventcategory' )
			);


			tribe_singleton(
				'filterbar.filters.cost',
				new Tribe__Events__Filterbar__Filters__Cost(
					sprintf( __( 'Cost (%s)', 'tribe-events-filter-view' ), tribe_get_option( 'defaultCurrencySymbol', '$' ) ),
					'cost'
				)
			);
			tribe_singleton(
				'filterbar.filters.tag',
				new Tribe__Events__Filterbar__Filters__Tag( __( 'Tags', 'tribe-events-filter-view' ), 'tags' )
			);
			tribe_singleton(
				'filterbar.filters.venue',
				new Tribe__Events__Filterbar__Filters__Venue( tribe_get_venue_label_plural(), 'venues' )
			);
			tribe_singleton(
				'filterbar.filters.organizer',
				new Tribe__Events__Filterbar__Filters__Organizer( tribe_get_organizer_label_plural(), 'organizers' )
			);
			tribe_singleton(
				'filterbar.filters.day-of-week',
				new Tribe__Events__Filterbar__Filters__Day_Of_Week( __( 'Day', 'tribe-events-filter-view' ), 'dayofweek' )
			);
			tribe_singleton(
				'filterbar.filters.time-of-day',
				new Tribe__Events__Filterbar__Filters__Time_Of_Day( __( 'Time', 'tribe-events-filter-view' ), 'timeofday' )
			);
			tribe_singleton(
				'filterbar.filters.country',
				new Tribe__Events__Filterbar__Filters__Country( __( 'Country', 'tribe-events-filter-view' ), 'country' )
			);
			tribe_singleton(
				'filterbar.filters.city',
				new Tribe__Events__Filterbar__Filters__City( __( 'City', 'tribe-events-filter-view' ), 'city' )
			);
			tribe_singleton(
				'filterbar.filters.state',
				new Tribe__Events__Filterbar__Filters__State( __( 'State/Province', 'tribe-events-filter-view' ), 'state' )
			);
			tribe_singleton(
				'filterbar.filters.featured-events',
				new Tribe__Events__Filterbar__Filters__Featured_Events(
					sprintf( esc_html__( 'Featured %s', 'tribe-events-filter-view' ), tribe_get_event_label_plural() ),
					'featuredevent'
				)
			);

			Tribe__Events__Filterbar__Additional_Fields__Manager::init();

			do_action( 'tribe_events_filters_create_filters' );
		}

		/**
		 * @return array|bool
		 */
		public function get_filter_settings() {
			$settings = get_option( Tribe__Events__Filterbar__Settings::OPTION_ACTIVE_FILTERS, false );
			if ( false === $settings ) {
				$settings = $this->get_multisite_default_settings();
			}
			return $settings;
		}

		protected function get_multisite_default_settings() {
			if ( ! is_multisite() ) {
				return false;
			}

			if ( empty( self::$defaultMuFilters ) ) {
				return false;
			}
			return self::$defaultMuFilters;
		}

		public function get_active_filters() {
			$current_filters = $this->get_filter_settings();
			if ( ! is_array( $current_filters ) ) { // everything is active
				$current_filters = $this->get_registered_filters();
			}
			return apply_filters( 'tribe_events_active_filters', array_keys( $current_filters ) );
		}

		public function get_registered_filters() {
			$filters = apply_filters( 'tribe_events_all_filters_array', array() );
			return $filters;
		}

		/**
		 * Load the plugin's textdomain.
		 *
		 * @return void
		 * @since 3.4
		 */
		public function loadTextDomain() {
			$mopath = $this->pluginDir . 'lang/';
			$domain = 'tribe-events-filter-view';

			// If we don't have Common classes load the old fashioned way
			if ( ! class_exists( 'Tribe__Main' ) ) {
				load_plugin_textdomain( $domain, false, $mopath );
			} else {
				// This will load `wp-content/languages/plugins` files first
				Tribe__Main::instance()->load_text_domain( $domain, $mopath );
			}
		}


		/**
		 * Get the absolute system path to the plugin directory, or a file therein
		 * @static
		 * @param string $path
		 * @return string
		 */
		public static function plugin_path( $path ) {
			$base = dirname( self::$plugin_file );
			if ( $path ) {
				return trailingslashit( $base ) . $path;
			} else {
				return untrailingslashit( $base );
			}
		}

		/**
		 * Get the absolute URL to the plugin directory, or a file therein
		 * @static
		 * @param string $path
		 * @return string
		 */
		public static function plugin_url( $path ) {
			return plugins_url( $path, self::$plugin_file );
		}


		/**
		 * Make necessary database updates on admin_init
		 *
		 * @since 4.5
		 *
		 */
		public function run_updates() {
			if ( ! class_exists( 'Tribe__Events__Updater' ) ) {
				return; // core needs to be updated for compatibility
			}

			$updater = new Tribe__Events__Filterbar__Updater( self::VERSION );
			if ( $updater->update_required() ) {
				$updater->do_updates();
			}
		}

		public function display_dynamic_a11y_notice() {
			if ( 'automatic' === tribe_get_option( 'liveFiltersUpdate', 'automatic' ) ) {
				echo '<div class="a11y-hidden" aria-label="' . __( 'Accessibility Form Notice', 'tribe-events-filter-view' ) . '">';
				echo __( 'Notice: Utilizing the form controls will dynamically update the content', 'tribe-events-filter-view' );
				echo '</div>';
			}

			return false;
		}

		/**
		 * Initialize the addon to make sure the versions line up.
		 *
		 * @deprecated 4.6
		 *
		 * @author PaulHughes01
		 * @since 0.1
		 * @param array $plugins The array of registered plugins.
		 * @return array The array of registered plugins.
		 */
		public static function initAddon( $plugins ) {
			_deprecated_function( __METHOD__, '4.6', '' );

			$plugins['TribeFilterView'] = array(
				'plugin_name'      => 'The Events Calendar: Filter Bar',
				'required_version' => self::REQUIRED_TEC_VERSION,
				'current_version'  => self::VERSION,
				'plugin_dir_file'  => basename( dirname( dirname( __FILE__ ) ) ) . '/the-events-calendar-filter-view.php',
			);
			return $plugins;
		}
	}
}
