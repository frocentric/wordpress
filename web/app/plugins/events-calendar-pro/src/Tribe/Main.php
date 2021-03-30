<?php

if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
	class Tribe__Events__Pro__Main {

		private static $instance;

		public $pluginDir;
		public $pluginPath;
		public $pluginUrl;
		public $pluginSlug;

		/**
		 * Used when forming recurring events /all/ view permalinks.
		 *
		 * @since 4.4.14
		 *
		 * @var string
		 */
		public $all_slug = 'all';

		public $weekSlug = 'week';
		public $photoSlug = 'photo';

		public $singular_event_label;
		public $plural_event_label;

		/** @var Tribe__Events__Pro__Recurrence__Permalinks */
		public $permalink_editor = null;

		/**
		 * @var Tribe__Events__Pro__Single_Event_Meta
		 */
		public $single_event_meta;

		/** @var Tribe__Events__Pro__Recurrence__Single_Event_Overrides */
		public $single_event_overrides;

		/** @var Tribe__Events__Pro__Admin__Custom_Meta_Tools */
		public $custom_meta_tools;

		/** @var Tribe__Events__Pro__Recurrence__Queue_Processor */
		public $queue_processor;

		/**
		 * @var Tribe__Events__Pro__Recurrence__Queue_Realtime
		 */
		public $queue_realtime;

		/**
		 * @var Tribe__Events__Pro__Recurrence__Aggregator
		 */
		public $aggregator;

		/**
		 * @var Tribe__Events__Pro__Embedded_Maps
		 */
		public $embedded_maps;

		/**
		 * @var Tribe__Events__Pro__Shortcodes__Register
		 */
		public $shortcodes;

		/**
		 * Where in the themes we will look for templates
		 *
		 * @since 4.5
		 *
		 * @var string
		 */
		public $template_namespace = 'events-pro';

		const VERSION = '5.4.0.2';

		/**
		 * The Events Calendar Required Version
		 * Use Tribe__Events__Pro__Plugin_Register instead
		 *
		 * @deprecated 4.6
		 *
		 */
		const REQUIRED_TEC_VERSION = '5.3.1';

		private function __construct() {
			$this->pluginDir = trailingslashit( basename( EVENTS_CALENDAR_PRO_DIR ) );
			$this->pluginPath = trailingslashit( EVENTS_CALENDAR_PRO_DIR );
			$this->pluginUrl = plugins_url( $this->pluginDir, EVENTS_CALENDAR_PRO_DIR );
			$this->pluginSlug = 'events-calendar-pro';

			require_once( $this->pluginPath . 'src/functions/template-tags/general.php' );
			require_once( $this->pluginPath . 'src/functions/template-tags/map.php' );
			require_once( $this->pluginPath . 'src/functions/template-tags/week.php' );
			require_once( $this->pluginPath . 'src/functions/template-tags/venue.php' );
			require_once( $this->pluginPath . 'src/functions/template-tags/widgets.php' );
			require_once( $this->pluginPath . 'src/functions/template-tags/ical.php' );

			// Load Deprecated Template Tags
			if ( ! defined( 'TRIBE_DISABLE_DEPRECATED_TAGS' ) ) {
				require_once $this->pluginPath . 'src/functions/template-tags/deprecated.php';
			}

			add_action( 'admin_init', array( $this, 'run_updates' ), 10, 0 );

			// Tribe common resources
			add_action( 'tribe_helper_activation_complete', array( $this, 'helpersLoaded' ) );

			add_action( 'init', array( $this, 'init' ), 10 );
			add_action( 'tribe_load_text_domains', [ $this, 'loadTextDomain' ] );
			add_action( 'admin_print_styles', array( $this, 'admin_enqueue_styles' ) );
			add_action( 'tribe_events_enqueue', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'tribe_venues_enqueue', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_pro_scripts' ), 8 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			add_action( 'tribe_settings_do_tabs', array( $this, 'add_settings_tabs' ) );
			add_filter( 'tribe_settings_tab_fields', array( $this, 'filter_settings_tab_fields' ), 10, 2 );

			add_filter( 'body_class', array( $this, 'body_class' ) );
			add_filter( 'tribe_events_current_view_template', array( $this, 'select_page_template' ) );
			add_filter( 'tribe_events_current_template_class', array( $this, 'get_current_template_class' ) );
			add_filter( 'tribe_events_template_paths', array( $this, 'template_paths' ) );
			add_filter( 'tribe_events_template_class_path', array( $this, 'template_class_path' ) );

			add_filter( 'tribe_help_tab_getting_started_text', array( $this, 'add_help_tab_getting_started_text' ) );
			add_filter( 'tribe_help_tab_introtext', array( $this, 'add_help_tab_intro_text' ) );
			add_filter( 'tribe_help_tab_forumtext', array( $this, 'add_help_tab_forumtext' ) );
			add_filter( 'tribe_support_registered_template_systems', array( $this, 'register_template_updates' ) );

			add_action( 'widgets_init', array( $this, 'pro_widgets_init' ), 100 );
			add_action( 'wp_loaded', array( $this, 'allow_cpt_search' ) );
			add_action( 'plugin_row_meta', array( $this, 'addMetaLinks' ), 10, 2 );
			add_filter( 'tribe_get_events_title', array( $this, 'reset_page_title' ), 10, 2 );
			add_filter( 'tribe_events_title_tag', array( $this, 'maybeAddEventTitle' ), 10, 3 );

			add_filter( 'tribe_help_tab_forums_url', array( $this, 'helpTabForumsLink' ) );
			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'addLinksToPluginActions' ) );

			add_filter( 'tribe_events_before_html', array( $this, 'events_before_html' ), 10 );

			// add custom fields to "the_meta" on single event template
			add_filter( 'tribe_events_single_event_the_meta_addon', array( $this, 'single_event_the_meta_addon' ), 10, 2 );
			add_filter( 'tribe_events_single_event_meta_group_template_keys', array( $this, 'single_event_meta_group_template_keys' ), 10 );
			add_filter( 'tribe_events_single_event_meta_template_keys', array( $this, 'single_event_meta_template_keys' ), 10 );
			add_filter( 'tribe_event_meta_venue_name', array( 'Tribe__Events__Pro__Single_Event_Meta', 'venue_name' ), 10, 2 );
			add_filter( 'tribe_event_meta_organizer_name', array( 'Tribe__Events__Pro__Single_Event_Meta', 'organizer_name' ), 10, 2 );
			add_filter( 'tribe_events_single_event_the_meta_group_venue', array( $this, 'single_event_the_meta_group_venue' ), 10, 2 );

			$this->enable_recurring_info_tooltip();
			add_action( 'tribe_events_before_the_grid', array( $this, 'disable_recurring_info_tooltip' ), 10, 0 );
			add_action( 'tribe_events_after_the_grid', array( $this, 'enable_recurring_info_tooltip' ), 10, 0 );
			add_action( 'tribe_events_single_event_after_the_meta', array( $this, 'register_related_events_view' ) );

			// see function tribe_convert_units( $value, $unit_from, $unit_to )
			add_filter( 'tribe_convert_kms_to_miles_ratio', array( $this, 'kms_to_miles_ratio' ) );
			add_filter( 'tribe_convert_miles_to_kms_ratio', array( $this, 'miles_to_kms_ratio' ) );

			/* Setup Tribe Events Bar */
			add_filter( 'tribe-events-bar-views', array( $this, 'setup_weekview_in_bar' ), 10, 1 );
			add_filter( 'tribe-events-bar-views', array( $this, 'setup_photoview_in_bar' ), 30, 1 );
			add_filter( 'tribe_events_ugly_link', array( $this, 'ugly_link' ), 10, 3 );
			add_filter( 'tribe_events_get_link', array( $this, 'get_link' ), 10, 4 );
			add_filter( 'tribe_get_listview_link', array( $this, 'get_all_link' ) );
			add_filter( 'tribe_get_listview_dir_link', array( $this, 'get_all_dir_link' ) );
			add_filter( 'tribe_bar_datepicker_caption', array( $this, 'setup_datepicker_label' ), 10, 1 );
			add_action( 'tribe_events_after_the_title', array( $this, 'add_recurring_occurrence_setting_to_list' ) );
			add_action( 'tribe_events_list_before_the_content', array( 'Tribe__Events__Pro__Templates__Mods__List_View', 'print_all_events_link' ) );

			add_filter( 'tribe_is_ajax_view_request', array( $this, 'is_pro_ajax_view_request' ), 10, 2 );

			add_filter( 'wp', array( $this, 'detect_recurrence_redirect' ) );
			add_filter( 'template_redirect', array( $this, 'filter_canonical_link_on_recurring_events' ), 10, 1 );

			$this->permalink_editor = apply_filters( 'tribe_events_permalink_editor', new Tribe__Events__Pro__Recurrence__Permalinks() );
			add_filter( 'post_type_link', array( $this->permalink_editor, 'filter_recurring_event_permalinks' ), 10, 4 );
			add_filter( 'get_sample_permalink', array( $this->permalink_editor, 'filter_sample_permalink' ), 10, 2 );

			add_filter( 'tribe_events_register_venue_type_args', array( $this, 'addSupportsThumbnail' ), 10, 1 );
			add_filter( 'tribe_events_register_organizer_type_args', array( $this, 'addSupportsThumbnail' ), 10, 1 );
			add_action( 'post_updated_messages', array( $this, 'updatePostMessages' ), 20 );

			add_filter( 'tribe_events_default_value_strategy', array( $this, 'set_default_value_strategy' ) );
			add_action( 'plugins_loaded', array( $this, 'init_apm_filters' ) );

			// Fire up the Customizer Sections
			add_filter( 'tribe_customizer_section_args', array( $this, 'filter_month_week_customizer_label' ), 10, 3 );

			// override list view ajax get_event args if viewing all instances of a recurring post
			add_filter( 'tribe_events_listview_ajax_get_event_args', array( $this, 'override_listview_get_event_args' ), 10, 2 );
			add_filter( 'tribe_events_listview_ajax_event_display', array( $this, 'override_listview_display_setting' ), 10, 2 );

			// Event CSV import additions
			add_filter( 'tribe_events_importer_venue_column_names', array( Tribe__Events__Pro__CSV_Importer__Fields::instance(), 'filter_venue_column_names' ), 10, 1 );
			add_filter( 'tribe_events_importer_venue_array', array( Tribe__Events__Pro__CSV_Importer__Fields::instance(), 'filter_venue_array' ), 10, 4 );

			add_filter( 'oembed_discovery_links', array( $this, 'oembed_discovery_links_for_recurring_events' ) );
			add_filter( 'oembed_request_post_id', array( $this, 'oembed_request_post_id_for_recurring_events' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'load_widget_assets' ) );
			add_action( 'wp_ajax_tribe_widget_dropdown_terms', array( $this, 'ajax_widget_get_terms' ) );

			// Start the integrations manager
			Tribe__Events__Pro__Integrations__Manager::instance()->load_integrations();

			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );

			add_action( 'tribe_events_before_event_template_data_date_display', array( $this, 'disable_recurring_info_tooltip' ) );
			add_action( 'tribe_events_after_event_template_data_date_display', array( $this, 'enable_recurring_info_tooltip' ) );
			add_filter( 'tribe_customizer_inline_stylesheets', [ $this, 'customizer_inline_stylesheets' ], 10, 2 );
		}

		public function filter_month_week_customizer_label( $args, $section_id, $customizer ) {
			if ( 'month_week_view' !== $section_id ) {
				return $args;
			}

			$args['title'] = esc_html__( 'Month/Week View', 'events-calendar-pro' );
			return $args;
		}

		/**
		 * AJAX handler for the Widget Term Select2.
		 *
		 * @todo   We need to move this to use Tribe__Ajax__Dropdown class
		 *
		 * @return void
		 */
		public function ajax_widget_get_terms() {
			$disabled = (array) tribe_get_request_var( 'disabled', [] );
			$search   = tribe_get_request_var( [ 'search', 'term' ], false );

			$taxonomies = get_object_taxonomies( Tribe__Events__Main::POSTTYPE, 'objects' );
			$taxonomies = array_reverse( $taxonomies );

			$results = [];
			foreach ( $taxonomies as $tax ) {
				$group = [
					'text' => esc_attr( $tax->labels->name ),
					'children' => [],
					'tax' => $tax,
				];

				// echo sprintf( "<optgroup id='%s' label='%s'>", esc_attr( $tax->name ), esc_attr( $tax->labels->name ) );
				$terms = get_terms( $tax->name, [ 'hide_empty' => false ] );
				if ( empty( $terms ) ) {
					continue;
				}

				foreach ( $terms as $term ) {
					// This is a workaround to make #93598 work
					if ( $search && false === strpos( $term->name, $search ) ) {
						continue;
					}

					$group['children'][] = [
						'id' => esc_attr( $term->term_id ),
						'text' => esc_html( $term->name ),
						'taxonomy' => $tax,
						'disabled' => in_array( $term->term_id, $disabled ),
					];
				}

				$results[] = $group;
			}

			wp_send_json_success( [ 'results' => $results ] );
		}

		/**
		 * Make necessary database updates on admin_init
		 *
		 * @return void
		 */
		public function run_updates() {
			if ( ! class_exists( 'Tribe__Events__Updater' ) ) {
				return; // core needs to be updated for compatibility
			}
			$updater = new Tribe__Events__Pro__Updater( self::VERSION );
			if ( $updater->update_required() ) {
				$updater->do_updates();
			}
		}

		/**
		 * @return bool Whether related events should be shown in the single view
		 */
		public function show_related_events() {
			if ( tribe_get_option( 'hideRelatedEvents', false ) == true ) {
				return false;
			}

			return true;
		}

		/**
		 * add related events to single event view
		 *
		 * @return void
		 */
		public function register_related_events_view() {
			if ( $this->show_related_events() ) {
				tribe_single_related_events();
			}
		}

		/**
		 * Append the recurring info tooltip after an event schedule
		 *
		 * @param string $schedule_details
		 * @param int $event_id
		 *
		 * @return string
		 */
		public function append_recurring_info_tooltip( $schedule_details, $event_id = 0 ) {
			$tooltip = tribe_events_recurrence_tooltip( $event_id );

			return $schedule_details . $tooltip;
		}

		public function enable_recurring_info_tooltip() {
			add_filter( 'tribe_events_event_schedule_details', array( $this, 'append_recurring_info_tooltip' ), 9, 2 );
		}

		public function disable_recurring_info_tooltip() {
			remove_filter(
				'tribe_events_event_schedule_details', array(
				$this,
				'append_recurring_info_tooltip',
			), 9, 2
			);
		}

		public function recurring_info_tooltip_status() {
			if ( has_filter(
				'tribe_events_event_schedule_details', array(
					$this,
					'append_recurring_info_tooltip',
				)
			)
			) {
				return true;
			}

			return false;
		}

		/**
		 * Filters in a meta walker group for new items regarding the PRO addon.
		 *
		 * @param string $html The current HTML for the event meta..
		 * @param int $event_id The post_id of the current event.
		 *
		 * @return string The modified HTML for the event meta.
		 */
		public function single_event_the_meta_addon( $html, $event_id ) {

			// add custom meta if it's available
			$html .= tribe_get_meta_group( 'tribe_event_group_custom_meta' );

			return $html;
		}

		/**
		 * Adds for the meta walker a key for custom meta to do with PRO addon.
		 *
		 * @param array $keys The current array of meta keys.
		 *
		 * @return array The modified array.
		 */
		public function single_event_meta_template_keys( $keys ) {
			$keys[] = 'tribe_event_custom_meta';

			return $keys;
		}

		/**
		 * Adds for the meta walker a key for custom meta groups to do with PRO addon.
		 *
		 * @param array $keys The current array of meta keys.
		 *
		 * @return array The modified array.
		 */
		public function single_event_meta_group_template_keys( $keys ) {
			$keys[] = 'tribe_event_group_custom_meta';

			return $keys;
		}

		/**
		 * Adds (currently nothing) to the venue section of the meta walker for single events.
		 *
		 * @param bool $status Whether currently it is filtered to display venue information in a group or not.
		 * @param int $event_id The post_id of the current event.
		 *
		 * @return bool The modified boolean.
		 */
		public function single_event_the_meta_group_venue( $status, $event_id ) {

			return $status;
		}

		/**
		 * Modifies the page title for pro views.
		 *
		 * @param string $new_title The currently filtered title.
		 * @param string $title The oldest default title.
		 * @param string $sep The separator for title elements.
		 *
		 * @return string The modified title.
		 * @todo remove in 3.10
		 * @deprecated
		 */
		public function maybeAddEventTitle( $new_title, $title, $sep = null ) {
			if ( has_filter( 'tribe_events_pro_add_title' ) ) {
				_deprecated_function( "The 'tribe_events_pro_add_title' filter", '3.8', " the 'tribe_events_add_title' filter" );

				return apply_filters( 'tribe_events_pro_add_title', $new_title, $title, $sep );
			}

			return $new_title;
		}

		/**
		 * Gets the events_before_html content.
		 *
		 * @param string $html The events_before_html currently.
		 *
		 * @return string The modified html.
		 */
		public function events_before_html( $html ) {
			$wp_query = tribe_get_global_query_object();

			if ( is_null( $wp_query ) ) {
				return $html;
			}

			if ( $wp_query->tribe_is_event_venue || $wp_query->tribe_is_event_organizer ) {
				add_filter( 'tribe-events-bar-should-show', '__return_false' );
			}

			return $html;
		}

		/**
		 * Sets the page title for the various PRO views.
		 *
		 * @param string $title The current title.
		 *
		 * @return string The modified title.
		 */
		public function reset_page_title( $title, $depth = true ) {
			$wp_query = tribe_get_global_query_object();

			if ( is_null( $wp_query ) ) {
				return $title;
			}

			$tec = Tribe__Events__Main::instance();
			$date_format = apply_filters( 'tribe_events_pro_page_title_date_format', tribe_get_date_format( true ) );

			if ( tribe_is_showing_all() ) {
				$reset_title = sprintf( __( 'All %1$s for %2$s', 'tribe-events-calendar-pro' ), $this->plural_event_label_lowercase, get_the_title() );
			}

			// week view title
			if ( tribe_is_week() ) {
				$reset_title = sprintf(
					__( '%1$s for week of %2$s', 'tribe-events-calendar-pro' ),
					$this->plural_event_label,
					date_i18n( $date_format, strtotime( tribe_get_first_week_day( $wp_query->get( 'start_date' ) ) ) )
				);
			}

			if ( ! empty( $reset_title ) && is_tax( $tec->get_event_taxonomy() ) && $depth ) {
				$cat = get_queried_object();
				$reset_title = '<a href="' . tribe_get_events_link() . '">' . $reset_title . '</a>';
				$reset_title .= ' &#8250; ' . $cat->name;
			}

			return isset( $reset_title ) ? $reset_title : $title;
		}

		/**
		 * The class init function.
		 *
		 * @return void
		 */
		public function init() {
			Tribe__Events__Pro__Mini_Calendar::instance();
			Tribe__Events__Pro__This_Week::instance();
			Tribe__Events__Pro__Custom_Meta::init();
			Tribe__Events__Pro__Geo_Loc::instance();
			Tribe__Events__Pro__Community_Modifications::init();
			$this->custom_meta_tools = new Tribe__Events__Pro__Admin__Custom_Meta_Tools;
			$this->single_event_meta = new Tribe__Events__Pro__Single_Event_Meta;
			$this->single_event_overrides = new Tribe__Events__Pro__Recurrence__Single_Event_Overrides;
			$this->embedded_maps = new Tribe__Events__Pro__Embedded_Maps;
			$this->shortcodes = new Tribe__Events__Pro__Shortcodes__Register;
			$this->singular_event_label = tribe_get_event_label_singular();
			$this->plural_event_label = tribe_get_event_label_plural();
			$this->singular_event_label_lowercase = tribe_get_event_label_singular_lowercase();
			$this->plural_event_label_lowercase = tribe_get_event_label_plural_lowercase();

			// if enabled views have never been set then set those to all PRO views
			if ( false === tribe_get_option( 'tribeEnableViews', false ) ) {
				tribe_update_option( 'tribeEnableViews', array( 'list', 'month', 'day', 'photo', 'map', 'week' ) );
				// After setting the enabled view we Flush the rewrite rules
				flush_rewrite_rules();
			}
		}

		/**
		 * At the pre_get_post hook detect if we should redirect to a particular instance
		 * for an invalid 404 recurrence entries.
		 *
		 * @return mixed
		 */
		public function detect_recurrence_redirect() {
			global $wp;

			$wp_query = tribe_get_global_query_object();

			if ( is_null( $wp_query ) || ! isset( $wp_query->query_vars['eventDisplay'] ) ) {
				return false;
			}

			$current_url = null;
			$problem = _x( 'Unknown', 'debug recurrence', 'tribe-events-calendar-pro' );

			switch ( $wp_query->query_vars['eventDisplay'] ) {
				case 'single-event':
					// a recurrence event with a bad date will throw 404 because of WP_Query limiting by date range
					if ( is_404() || empty( $wp_query->query['eventDate'] ) ) {
						$recurrence_check = array_merge( array( 'posts_per_page' => 1 ), $wp_query->query );
						unset( $recurrence_check['eventDate'] );
						unset( $recurrence_check['tribe_events'] );

						// retrieve event object
						$get_recurrence_event = new WP_Query( $recurrence_check );
						// If a recurrence event actually exists then proceed with redirection.
						if (
							! empty( $get_recurrence_event->posts )
							&& tribe_is_recurring_event( $get_recurrence_event->posts[0]->ID )
							&& 'publish' === get_post_status( $get_recurrence_event->posts[0] )
						) {
							$problem = _x( 'invalid date', 'debug recurrence', 'tribe-events-calendar-pro' )
									 . empty( $wp_query->query['eventDate'] ) ? '' : ': ' . $wp_query->query['eventDate'];

							$current_url = Tribe__Events__Main::instance()->getLink( 'all', $get_recurrence_event->posts[0]->ID );
						}
						break;
					}

					// We are receiving the event date
					if ( ! empty( $wp_query->query['eventDate'] ) ) {
						$event_id = get_the_id();
						// if is a recurring event
						if ( tribe_is_recurring_event( $event_id ) ) {

							$event = get_post( $event_id );
							// if no post parent (ether the post parent or inexistent)
							if ( ! $event->post_parent ) {
								// get all the recursive event dates
								$dates = tribe_get_recurrence_start_dates( $event_id );

								$exist = false;
								foreach ( $dates as $date ) {
									// check if the date exists in any of the recurring event set
									if ( 0 === strpos( $date, $wp_query->query['eventDate'] ) ) {
										$exist = true;
										break;
									}
								}

								// if the event date coming on the URL doesn't exist, display the /all/ page
								if ( ! $exist ) {
									$problem = _x( 'incorrect slug', 'debug recurrence', 'tribe-events-calendar-pro' );
									$current_url = Tribe__Events__Main::instance()->getLink( 'all', $event_id );
									break;
								}
							}
						}
					}

					// A child event should be using its parent's slug. If it's using its own, redirect.
					if ( tribe_is_recurring_event( get_the_ID() ) && '' !== get_option( 'permalink_structure' ) ) {
						$event = get_post( get_the_ID() );
						if ( ! empty( $event->post_parent ) ) {
							if ( isset( $wp_query->query['name'] ) && $wp_query->query['name'] == $event->post_name ) {
								$problem = _x( 'incorrect slug', 'debug recurrence', 'tribe-events-calendar-pro' );
								$current_url = get_permalink( $event->ID );
							}
						}
					}
					break;

			}

			/**
			 * Provides an opportunity to modify the redirection URL prior to the actual redirection.
			 *
			 * @param string $current_url
			 */
			$current_url = apply_filters( 'tribe_events_pro_recurrence_redirect_url', $current_url );

			if ( ! empty( $current_url ) ) {
				// redirect user with 301
				$confirm_redirect = apply_filters( 'tribe_events_pro_detect_recurrence_redirect', true, $wp_query->query_vars['eventDisplay'] );
				do_action( 'tribe_events_pro_detect_recurrence_redirect', $wp_query->query_vars['eventDisplay'] );
				if ( $confirm_redirect ) {
					tribe( 'logger' )->log_warning(
						sprintf(
							/* Translators: 1: Error message, 2: URL */
							_x( 'Invalid instance of a recurring event was requested (%1$s) redirecting to %2$s', 'debug recurrence', 'tribe-events-calendar-pro' ),
							$problem,
							esc_url( $current_url )
						),
						__METHOD__
					);

					wp_safe_redirect( $current_url, 301 );
					exit;
				}
			}
		}

		public function filter_canonical_link_on_recurring_events() {
			if ( is_feed() ) {
				return;
			}

			if ( is_singular( Tribe__Events__Main::POSTTYPE ) && get_query_var( 'eventDate' ) && has_action( 'wp_head', 'rel_canonical' ) ) {
				remove_action( 'wp_head', 'rel_canonical' );
				add_action( 'wp_head', array( $this, 'output_recurring_event_canonical_link' ) );
			}
		}

		public function output_recurring_event_canonical_link() {
			// set the EventStartDate so Tribe__Events__Main can filter the permalink appropriately
			$post = get_post( get_queried_object_id() );
			$post->EventStartDate = get_query_var( 'eventDate' );

			// use get_post_permalink instead of get_permalink so that the post isn't converted
			// back to an ID, then to a post again (without the EventStartDate)
			$link = get_post_permalink( $post );

			echo "<link rel='canonical' href='" . esc_url( $link ) . "' />\n";
		}

		/**
		 * Loop through recurrence posts array and find out the next recurring instance from right now
		 *
		 * @param WP_Post[] $event_list
		 *
		 * @return int
		 */
		public function get_last_recurrence_id( $event_list ) {

			$wp_query = tribe_get_global_query_object();

			if ( ! is_null( $wp_query ) && empty( $event_list ) ) {
				$event_list = $wp_query->posts;
			}

			$right_now = current_time( 'timestamp' );
			$next_recurrence = 0;

			// find next recurrence date by loop
			foreach ( $event_list as $key => $event ) {
				if ( $right_now < strtotime( $event->EventStartDate ) ) {
					$next_recurrence = $event;
				}
			}
			if ( empty( $next_recurrence ) && ! empty( $event_list ) ) {
				$next_recurrence = reset( $event_list );
			}

			return apply_filters( 'tribe_events_pro_get_last_recurrence_id', $next_recurrence->ID, $event_list, $right_now );
		}

		/**
		 * Common library plugins have been activated. Functions that need to be applied afterwards can be added here.
		 *
		 * @return void
		 */
		public function helpersLoaded() {
			remove_action( 'widgets_init', 'tribe_related_posts_register_widget' );
			if ( class_exists( 'TribeRelatedPosts' ) ) {
				TribeRelatedPosts::instance();
				require_once( $this->pluginPath . 'vendor/tribe-related-posts/template-tags.php' );
			}
		}

		/**
		 * Add the default settings tab
		 *
		 * @return void
		 */
		public function add_settings_tabs() {
			require_once( $this->pluginPath . 'src/admin-views/tribe-options-defaults.php' );
			new Tribe__Settings_Tab( 'defaults', __( 'Default Content', 'tribe-events-calendar-pro' ), $defaultsTab );
			// The single-entry array at the end allows for the save settings button to be displayed.
			new Tribe__Settings_Tab( 'additional-fields', __( 'Additional Fields', 'tribe-events-calendar-pro' ), array(
				'priority' => 35,
				'fields'   => array( null ),
			) );
		}

		public function filter_settings_tab_fields( $fields, $tab ) {
			$this->singular_event_label = tribe_get_event_label_singular();
			$this->plural_event_label = tribe_get_event_label_plural();
			switch ( $tab ) {
				case 'display':
					$fields = Tribe__Main::array_insert_after_key(
						'tribeDisableTribeBar',
						$fields,
						array(
							'hideRelatedEvents' => array(
								'type'            => 'checkbox_bool',
								'label'           => __( 'Hide related events', 'tribe-events-calendar-pro' ),
								'tooltip'         => __( 'Remove related events from the single event view (with classic editor)', 'tribe-events-calendar-pro' ),
								'default'         => false,
								'validation_type' => 'boolean',
							),
						)
					);
					$fields = Tribe__Main::array_insert_after_key(
						'monthAndYearFormat',
						$fields,
						array(
							'weekDayFormat' => array(
								'type'            => 'text',
								'label'           => __( 'Week Day Format', 'tribe-events-calendar-pro' ),
								'tooltip'         => __( 'Enter the format to use for week days. Used when showing days of the week in Week view.', 'tribe-events-calendar-pro' ),
								'default'         => 'D jS',
								'size'            => 'medium',
								'validation_type' => 'not_empty',
							),
						)
					);
					$fields = Tribe__Main::array_insert_after_key(
						'hideRelatedEvents',
						$fields,
						array(
							'week_view_hide_weekends' => array(
								'type'            => 'checkbox_bool',
								'label'           => __( 'Hide weekends on Week View', 'tribe-events-calendar-pro' ),
								'tooltip'         => __( 'Check this to only show weekdays on Week View', 'tribe-events-calendar-pro' ),
								'default'         => false,
								'validation_type' => 'boolean',
							),
						)
					);
					$fields = Tribe__Main::array_insert_before_key(
						'tribeEventsBeforeHTML',
						$fields,
						array(
							'tribeEventsShortcodeBeforeHTML' => array(
								'type'            => 'checkbox_bool',
								'label'           => __( 'Enable the Before HTML (below) on shortcodes.', 'tribe-events-calendar-pro' ),
								'tooltip'         => __( 'Check this to show the Before HTML from the text area below on events displayed via shortcode.', 'tribe-events-calendar-pro' ),
								'default'         => false,
								'validation_type' => 'boolean',
							),
						)
					);
					$fields = Tribe__Main::array_insert_before_key(
						'tribeEventsAfterHTML',
						$fields,
						array(
							'tribeEventsShortcodeAfterHTML' => array(
								'type'            => 'checkbox_bool',
								'label'           => __( 'Enable the After HTML (below) on shortcodes.', 'tribe-events-calendar-pro' ),
								'tooltip'         => __( 'Check this to show the After HTML from the text area below on events displayed via shortcode.', 'tribe-events-calendar-pro' ),
								'default'         => false,
								'validation_type' => 'boolean',
							),
						)
					);
					break;
			}

			return $fields;
		}

		/**
		 * Add the "Getting Started" text to the help tab for PRO addon.
		 *
		 * @return string The modified content.
		 */
		public function add_help_tab_getting_started_text() {
			$getting_started_text[] = sprintf( __( "Thanks for buying Events Calendar PRO! From all of us at Modern Tribe, we sincerely appreciate it. If you're looking for help with Events Calendar PRO, you've come to the right place. We are committed to helping make your calendar be spectacular... and hope the resources provided below will help get you there.", 'tribe-events-calendar-pro' ) );
			$content = implode( $getting_started_text );

			return $content;
		}

		/**
		 * Add the intro text that concerns PRO to the help tab.
		 *
		 * @return string The modified content.
		 */
		public function add_help_tab_intro_text() {
			$intro_text[] = '<p>' . __( "If this is your first time using The Events Calendar Pro, you're in for a treat and are already well on your way to creating a first event. Here are some basics we've found helpful for users jumping into it for the first time:", 'tribe-events-calendar-pro' ) . '</p>';
			$intro_text[] = '<ul>';
			$intro_text[] = '<li>';
			$intro_text[] = sprintf( __( '%sOur New User Primer%s was designed for folks in your exact position. Featuring both step-by-step videos and written walkthroughs that feature accompanying screenshots, the primer aims to take you from zero to hero in no time.', 'tribe-events-calendar-pro' ), '<a href="https://evnt.is/4t" target="blank">', '</a>' );
			$intro_text[] = '</li><li>';
			$intro_text[] = sprintf( __( '%sInstallation/Setup FAQs%s from our support page can help give an overview of what the plugin can and cannot do. This section of the FAQs may be helpful as it aims to address any basic install questions not addressed by the new user primer.', 'tribe-events-calendar-pro' ), '<a href="https://evnt.is/4u" target="blank">', '</a>' );
			$intro_text[] = '</li><li>';
			$intro_text[] = sprintf( __( "Take care of your license key. Though not required to create your first event, you'll want to get it in place as soon as possible to guarantee your access to support and upgrades. %sHere's how to find your license key%s, if you don't have it handy.", 'tribe-events-calendar-pro' ), '<a href="https://evnt.is/4v" target="blank">', '</a>' );
			$intro_text[] = '</li></ul><p>';
			$intro_text[] = __( "Otherwise, if you're feeling adventurous, you can get started by heading to the Events menu and adding your first event.", 'tribe-events-calendar-pro' );
			$intro_text[] = '</p>';
			$intro_text = implode( $intro_text );

			return $intro_text;
		}

		/**
		 * Add help text regarding the Tribe forums to the help tab.
		 *
		 * @return string The content.
		 */
		public function add_help_tab_forumtext() {
			$forum_text[] = '<p>' . sprintf( __( 'Written documentation can only take things so far...sometimes, you need help from a real person. This is where our %ssupport forums%s come into play.', 'tribe-events-calendar-pro' ), '<a href="https://evnt.is/4w/" target="blank">', '</a>' ) . '</p>';
			$forum_text[] = '<p>' . sprintf( __( "Users who have purchased an Events Calendar PRO license are granted total access to our %spremium support forums%s. Unlike at the %sWordPress.org support forum%s, where our involvement is limited to identifying and patching bugs, we have a dedicated support team for PRO users. We're on the PRO forums daily throughout the business week, and no thread should go more than 24-hours without a response.", 'tribe-events-calendar-pro' ), '<a href="https://evnt.is/4w/" target="blank">', '</a>', '<a href="http://wordpress.org/support/plugin/the-events-calendar" target="blank">', '</a>' ) . '</p>';
			$forum_text[] = '<p>' . __( "Our number one goal is helping you succeed, and to whatever extent possible, we'll help troubleshoot and guide your customizations or tweaks. While we won't build your site for you, and we can't guarantee we'll be able to get you 100% integrated with every theme or plugin out there, we'll do all we can to point you in the right direction and to make you -- and your client, as is often more importantly the case -- satisfied.", 'tribe-events-calendar-pro' ) . '</p>';
			$forum_text[] = '<p>' . __( "Before posting a new thread, please do a search to make sure your issue hasn't already been addressed. When posting please make sure to provide as much detail about the problem as you can (with screenshots or screencasts if feasible), and make sure that you've identified whether a plugin / theme conflict could be at play in your initial message.", 'tribe-events-calendar-pro' ) . '</p>';
			$forum_text = implode( $forum_text );

			return $forum_text;
		}

		/**
		 * If the user has chosen to replace default values, set up
		 * the Pro class to read those defaults from options
		 *
		 * @param Tribe__Events__Default_Values $strategy
		 * @return Tribe__Events__Default_Values
		 */
		public function set_default_value_strategy( $strategy ) {
			return new Tribe__Events__Pro__Default_Values();
		}

		/**
		 * Adds the proper css class(es) to the body tag.
		 *
		 * @param array $classes The current array of body classes.
		 *
		 * @return array The modified array of body classes.
		 * @TODO move this to template class
		 */
		public function body_class( $classes ) {
			$wp_query = tribe_get_global_query_object();

			if ( is_null( $wp_query ) ) {
				return $classes;
			}

			// @TODO do we really need all these array_diff()s?

			if ( $wp_query->tribe_is_event_query ) {
				if ( $wp_query->tribe_is_week ) {
					$classes[] = 'tribe-events-week';
					// remove the default gridview class from core
					$classes = array_diff( $classes, array( 'events-gridview' ) );
				}
				if ( $wp_query->tribe_is_photo ) {
					$classes[] = 'tribe-events-photo';
					// remove the default gridview class from core
					$classes = array_diff( $classes, array( 'events-gridview' ) );
				}
				if ( $wp_query->tribe_is_map ) {
					$classes[] = 'tribe-events-map';
					// remove the default gridview class from core
					$classes = array_diff( $classes, array( 'events-gridview' ) );
				}

				if (
					! tribe_is_using_basic_gmaps_api()
					&& ( tribe_is_map() || ! tribe_get_option( 'hideLocationSearch', false ) )
				) {
					$classes[] = 'tribe-events-uses-geolocation';
				}

				if (
					! empty( $wp_query->query['tribe_events'] )
					&& 'custom-recurrence' === $wp_query->query['tribe_events']
					&& ! empty( $wp_query->query['eventDisplay'] )
					&& 'all' === $wp_query->query['eventDisplay']
				) {
					$classes[] = 'tribe-events-recurrence-archive';
				}
			}

			return $classes;
		}

		/**
		 * Set PRO query flags
		 *
		 * @param WP_Query $query The current query object.
		 *
		 * @return WP_Query The modified query object.
		 **/
		public function parse_query( $query ) {
			$query->tribe_is_week = false;
			$query->tribe_is_photo = false;
			$query->tribe_is_map = false;
			$query->tribe_is_event_pro_query = false;
			if ( ! empty( $query->query_vars['eventDisplay'] ) ) {
				$query->tribe_is_event_pro_query = true;
				switch ( $query->query_vars['eventDisplay'] ) {
					case 'week':
						$query->tribe_is_week = true;
						break;
					case 'photo':
						$query->tribe_is_photo = true;
						break;
					case 'map':
						/*
						* Query setup for the map view is located in
						* Tribe__Events__Pro__Geo_Loc->setup_geoloc_in_query()
						*/
						$query->tribe_is_map = true;
						break;
				}
			}
		}

		/**
		 * Add custom query modification to the pre_get_posts hook as necessary for PRO.
		 *
		 * @param WP_Query $query The current query object.
		 *
		 * @return WP_Query The modified query object.
		 */
		public function pre_get_posts( $query ) {
			if ( $query->is_single() && $query->get( 'eventDate' ) ) {
				$this->set_post_id_for_recurring_event_query( $query );
			}

			$recurrence_query = null;

			if ( ! empty( $query->tribe_is_event_pro_query ) ) {
				switch ( $query->query_vars['eventDisplay'] ) {
					case 'week':

						$start_date = tribe_get_first_week_day( $query->get( 'eventDate' ) );
						$end_date   = tribe_get_last_week_day( $start_date );

						// if the setting to hide weekends is true
						if ( tribe_get_option( 'week_view_hide_weekends', false ) == true ) {
							$start_of_week = get_option( 'start_of_week' );
							// check if the week is set to start on a weekend day
							// If so, start on the next weekday.
							// 0 = Sunday, 6 = Saturday
							if ( $start_of_week == 0 || $start_of_week == 6 ) {
								$start_date = date( Tribe__Date_Utils::DBDATEFORMAT, strtotime( $start_date . ' +1 Weekday' ) );
							}
							// If the week starts on saturday or friday
							// sunday and/or saturday would be on the other end, so we need to end the previous weekday
							// 5 = Friday, 6 = Saturday
							if ( $start_of_week == 5 || $start_of_week == 6 ) {
								$end_date = date( Tribe__Date_Utils::DBDATEFORMAT, strtotime( $end_date . ' -1 Weekday' ) );
							}
						}

						// if the setting to hide weekends is on
						// need to filter the query
						// need to only show 5 days on the week view

						// if we're using an non-default hour range on week view
						if ( has_filter( 'tribe_events_week_get_hours' ) ) {
							$start_date .= ' ' . tribe_events_week_get_hours( 'first-hour' );
							$end_date .= ' ' . tribe_events_week_get_hours( 'last-hour' );
						}

						$query->set( 'eventDate', $start_date  );
						$query->set( 'start_date', $start_date );
						$query->set( 'end_date', $end_date );
						$query->set( 'posts_per_page', -1 ); // show ALL week posts
						$query->set( 'hide_upcoming', false );
						break;
					case 'photo':
						$query->set( 'hide_upcoming', false );
						break;
					case 'all':
						$recurrence_query = new Tribe__Events__Pro__Recurrence__Event_Query( $query );
						$recurrence_query->hook();
						break;
				}

				/**
				 * Hooks into our query and recurrence query objects after we have done the setup.
				 *
				 * @param WP_Query                                         $query            Query object.
				 * @param null|Tribe__Events__Pro__Recurrence__Event_Query $recurrence_query Recurrence event query object (for `all` view).
				 *
				 * @since 4.7
				 */
				do_action( 'tribe_events_pro_pre_get_posts', $query, $recurrence_query );
			}
		}

		/**
		 * A recurring event will have the base post's slug in the
		 * 'name' query var. We need to remove that and replace it
		 * with the correct post's ID
		 *
		 * @param WP_Query $query
		 *
		 * @return void
		 */
		private function set_post_id_for_recurring_event_query( $query ) {
			$date = $query->get( 'eventDate' );
			$slug = isset( $query->query['name'] ) ? $query->query['name'] : '';

			if ( empty( $date ) || empty( $slug ) ) {
				return; // we shouldn't be here
			}

			/**
			 * Filters the recurring event parent post ID.
			 *
			 * @param bool|int $parent_id The parent event post ID. Defaults to `false`.
			 *                            If anything but `false` is returned from this filter
			 *                            that value will be used as the recurring event parent
			 *                            post ID.
			 * @param WP_Query $query     The current query.
			 */
			$parent_id = apply_filters( 'tribe_events_pro_recurring_event_parent_id', false, $query );

			$cache = new Tribe__Cache();
			if ( false === $parent_id ) {
				$post_id = $cache->get( 'single_event_' . $slug . '_' . $date, 'save_post' );
			} else {
				$post_id = $cache->get( 'single_event_' . $slug . '_' . $date . '_' . $parent_id, 'save_post' );
			}

			if ( ! empty( $post_id ) ) {
				unset( $query->query_vars['name'] );
				unset( $query->query_vars[ Tribe__Events__Main::POSTTYPE ] );
				$query->set( 'p', $post_id );

				return;
			}

			/** @var \wpdb $wpdb */
			global $wpdb;

			if ( false === $parent_id ) {
				$parent_sql = "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s AND post_type=%s";
				$parent_sql = $wpdb->prepare( $parent_sql, $slug, Tribe__Events__Main::POSTTYPE );
				$parent_id  = $wpdb->get_var( $parent_sql );
			}

			$parent_start = get_post_meta( $parent_id, '_EventStartDate', true );

			if ( empty( $parent_start ) ) {
				return; // how does this series not have a start date?
			} else {
				$parent_start_date = date( 'Y-m-d', strtotime( $parent_start ) );
			}

			$sequence_number = $query->get( 'eventSequence' );
			if ( $parent_start_date === $date && empty( $sequence_number )  ) {
				$post_id = $parent_id;
			} else {
				/* Look for child posts taking place on the requested date (but not
				 * necessarily at the same time as the parent event); take sequence into
				 * account to distinguish between recurring event instances happening on the same
				 * day.
				 */
				$sequence_number     = $query->get( 'eventSequence' );
				$should_use_sequence = ! empty( $sequence_number ) && is_numeric( $sequence_number ) && intval( $sequence_number ) > 1;
				$sequence_number     = intval( $sequence_number );
				if ( ! $should_use_sequence ) {
				$child_sql = "
					SELECT     ID
					FROM       {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} m ON m.post_id=p.ID AND m.meta_key='_EventStartDate'
					WHERE      p.post_parent=%d
					  AND      p.post_type=%s
					  AND      LEFT( m.meta_value, 10 ) = %s
				";
				$child_sql = $wpdb->prepare( $child_sql, $parent_id, Tribe__Events__Main::POSTTYPE, $date );
				} else {
					$child_sql = "
					SELECT     ID
					FROM       {$wpdb->posts} p
					INNER JOIN {$wpdb->postmeta} m ON m.post_id=p.ID AND m.meta_key='_EventStartDate'
					INNER JOIN {$wpdb->postmeta} seqm ON seqm.post_id=p.ID AND seqm.meta_key='_EventSequence'
					WHERE      p.post_parent=%d
					  AND      p.post_type=%s
					  AND      LEFT( m.meta_value, 10 ) = %s
					  AND      LEFT( seqm.meta_value, 10 ) = %s
				";
					$child_sql = $wpdb->prepare( $child_sql, $parent_id, Tribe__Events__Main::POSTTYPE, $date, $sequence_number );
				}
				$post_id = $wpdb->get_var( $child_sql );
			}

			if ( $post_id ) {
				unset( $query->query_vars['name'] );
				unset( $query->query_vars['tribe_events'] );
				$query->set( 'p', $post_id );
				$cache->set( 'single_event_' . $slug . '_' . $date, $post_id, Tribe__Cache::NO_EXPIRATION, 'save_post' );
			}
		}

		/**
		 * Get the path to the current events template.
		 *
		 * @param string $template The current template path.
		 *
		 * @return string The modified template path.
		 */
		public function select_page_template( $template ) {
			// venue view
			if ( is_singular( Tribe__Events__Main::VENUE_POST_TYPE ) ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/single-venue' );
			}
			// organizer view
			if ( is_singular( Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/single-organizer' );
			}
			// week view
			if ( tribe_is_week() ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/week' );
			}

			// photo view
			if ( tribe_is_photo() ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/photo' );
			}

			// map view
			if ( tribe_is_map() ) {
				if ( tribe_is_using_basic_gmaps_api() ) {
					$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/map-basic' );
				} else {
					$template = Tribe__Events__Templates::getTemplateHierarchy( 'pro/map' );
				}
			}

			// recurring "all" view
			if ( tribe_is_showing_all() ) {
				$template = Tribe__Events__Templates::getTemplateHierarchy( 'list' );
			}

			return $template;
		}

		/**
		 * Check the ajax request action looking for pro views
		 *
		 * @param $is_ajax_view_request bool
		 */
		public function is_pro_ajax_view_request( $is_ajax_view_request, $view ) {

			// if a particular view wasn't requested, or this isn't an ajax request, or there was no action param in the request, don't continue
			if ( $view == false || ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || empty( $_REQUEST['action'] ) ) {
				return $is_ajax_view_request;
			}

			switch ( $view ) {
				case 'map' :
					$is_ajax_view_request = ( $_REQUEST['action'] == Tribe__Events__Pro__Templates__Map::AJAX_HOOK );
					break;

				case 'photo' :
					$is_ajax_view_request = ( $_REQUEST['action'] == Tribe__Events__Pro__Templates__Photo::AJAX_HOOK );
					break;

				case 'week' :
					$is_ajax_view_request = ( $_REQUEST['action'] == Tribe__Events__Pro__Templates__Week::AJAX_HOOK );
					break;
			}

			return $is_ajax_view_request;

		}

		/**
		 * Specify the PHP class for the current page template
		 *
		 * @param string $class The current class we are filtering.
		 *
		 * @return string The class.
		 */
		public function get_current_template_class( $class ) {

			// venue view
			if ( is_singular( Tribe__Events__Main::VENUE_POST_TYPE ) ) {
				$class = 'Tribe__Events__Pro__Templates__Single_Venue';
			} // organizer view
			elseif ( is_singular( Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) {
				$class = 'Tribe__Events__Pro__Templates__Single_Organizer';
			} // week view
			elseif ( tribe_is_week() || tribe_is_ajax_view_request( 'week' ) ) {
				$class = 'Tribe__Events__Pro__Templates__Week';
			} // photo view
			elseif ( tribe_is_photo() || tribe_is_ajax_view_request( 'photo' ) ) {
				$class = 'Tribe__Events__Pro__Templates__Photo';
			} // map view
			elseif ( tribe_is_map() || tribe_is_ajax_view_request( 'map' ) ) {
				$class = 'Tribe__Events__Pro__Templates__Map';
			}

			return $class;

		}

		/**
		 * Add premium plugin paths for each file in the templates array
		 *
		 * @param $template_paths array
		 *
		 * @return array
		 */
		public function template_paths( $template_paths = array() ) {

			$template_paths['pro'] = $this->pluginPath;

			return $template_paths;

		}

		/**
		 * Add premium plugin paths for each file in the templates array
		 *
		 * @param $template_class_path string
		 *
		 * @return array
		 **/
		public function template_class_path( $template_class_paths = array() ) {

			$template_class_paths[] = $this->pluginPath.'/lib/template-classes/';

			return $template_class_paths;

		}

		/**
		 * Enqueues the necessary JS for the admin side of things.
		 *
		 * @return void
		 */
		public function admin_enqueue_scripts() {

			wp_enqueue_script(
				Tribe__Events__Main::POSTTYPE . '-premium-admin',
				tribe_events_pro_resource_url( 'events-admin.js' ),
				[ 'jquery-ui-datepicker' ],
				apply_filters( 'tribe_events_pro_js_version', self::VERSION ),
				true
			);

			wp_enqueue_script(
				Tribe__Events__Main::POSTTYPE . '-premium-recurrence',
				tribe_events_pro_resource_url( 'events-recurrence.js' ),
				[ Tribe__Events__Main::POSTTYPE . '-premium-admin', 'tribe-events-pro-handlebars', 'tribe-moment', 'tribe-dropdowns', 'jquery-ui-dialog', 'tribe-buttonset' ],
				apply_filters( 'tribe_events_pro_js_version', self::VERSION ),
				true
			);

			$data = apply_filters( 'tribe_events_pro_localize_script', [], 'TribeEventsProAdmin', Tribe__Events__Main::POSTTYPE.'-premium-admin' );

			wp_localize_script( Tribe__Events__Main::POSTTYPE . '-premium-admin', 'TribeEventsProAdmin', $data );
			wp_localize_script( Tribe__Events__Main::POSTTYPE . '-premium-admin', 'tribe_events_pro_recurrence_strings', [
				'date'       => Tribe__Events__Pro__Recurrence__Meta::date_strings(),
				'recurrence' => Tribe__Events__Pro__Recurrence__Strings::recurrence_strings(),
				'exclusion'  => [],
			] );
		}

		public function load_widget_assets( $hook = null ) {

			if (
				'widgets.php' !== $hook
				&& 'customize.php' !== $hook

				/**
				 * Filter the screen widgets assets will load
				 *
				 * @since 4.4.28
				 *
				 * @param boolean false by default assets will not load
				 * @param string $hook a string of current page php file such as post.php
				 */
				&& ! apply_filters( 'tribe_allow_widget_on_post_page_edit_screen', false, $hook )
			) {
					return;
			}

			wp_enqueue_script( 'tribe-admin-widget', tribe_events_pro_resource_url( 'admin-widget.js' ), array( 'jquery', 'underscore', 'tribe-select2' ), apply_filters( 'tribe_events_pro_js_version', self::VERSION ) );

		}

		public function admin_enqueue_styles() {
			tribe_asset_enqueue( 'tribe-select2-css' );
		}

		/**
		 * Enqueue the proper styles depending on what is required by a given page load.
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			global $post;

			if ( tribe_is_event_query()
				|| ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'tribe_events' ) )
			) {
				tribe_asset_enqueue_group( 'events-pro-styles' );
			}
		}

		/**
		 * Enqueue the proper PRO scripts as necessary.
		 *
		 * @param bool $force
		 * @param bool $footer
		 *
		 * @return void
		 */
		public function enqueue_pro_scripts( $force = false, $footer = false ) {
			global $post;

			if (
				$force
				|| tribe_is_event_query()
				|| ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'tribe_events' ) )
			) {

				// Be sure we enqueue TEC scripts
				tribe_asset_enqueue( 'tribe-events-calendar-script' );
				$data_tec = tribe( 'tec.assets' )->get_js_calendar_script_data();
				wp_localize_script( 'tribe-events-calendar-script', 'tribe_js_config', $data_tec );

				// Be sure we enqueue PRO when needed with the proper localization
				tribe_asset_enqueue( 'tribe-events-pro' );
				$data_pro = tribe( 'events-pro.assets' )->get_data_tribe_events_pro();
				wp_localize_script( 'tribe-events-pro', 'TribeEventsPro', $data_pro );

				if ( ! tribe_is_using_basic_gmaps_api() ) {
					// Be sure we enqueue PRO geoloc when needed with the proper localization
					tribe_asset_enqueue( 'tribe-events-pro-geoloc' );
					$data_geo = tribe( 'events-pro.assets' )->get_data_tribe_geoloc();
					wp_localize_script( 'tribe-events-pro-geoloc', 'GeoLoc', $data_geo );
				}
			}
		}

		/**
		 * Sets up to add the query variable for hiding subsequent recurrences of recurring events on the frontend.
		 *
		 * @param WP_Query $query The current query object.
		 *
		 * @return WP_Query The modified query object.
		 */
		public function setup_hide_recurrence_in_query( $query ) {

			if ( ! isset( $query->query_vars['is_tribe_widget'] ) || ! $query->query_vars['is_tribe_widget'] ){
				// don't hide any recurrences on the all recurrences view
				if ( tribe_is_showing_all() || tribe_is_week() || tribe_is_month() || tribe_is_day() ) {
					return $query;
				}
			}

			// don't hide any recurrences in the admin
			if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				return $query;
			}

			// don't override an explicitly passed value
			if ( isset( $query->query_vars['tribeHideRecurrence'] ) ) {
				return $query;
			}

			// if the admin option is set to hide recurrences, or the user option is set
			if ( $this->should_hide_recurrence( $query ) ) {
				$query->query_vars['tribeHideRecurrence'] = 1;
			}

			return $query;
		}

		/**
		 * Returns whether or not we show only the first instance of each recurring event in listview
		 *
		 * @param WP_Query $query The current query object.
		 *
		 * @return boolean
		 */
		public function should_hide_recurrence( $query = null ) {
			$hide = false;

			if ( tribe_is_showing_all() ) {
				// let's not hide recurrence if we are showing all recurrence events
				$hide = false;
			} elseif ( defined( 'REST_REQUEST' ) && true === REST_REQUEST ) {
				// let's not hide recurrence if we are processing a REST request
				$hide = false;
			} elseif ( ! empty( $_GET['tribe_post_parent'] ) ) {
				// let's not hide recurrence if we are showing all recurrence events via AJAX
				$hide = false;
			} elseif ( ! empty( $_POST['tribe_post_parent'] ) ) {
				// let's not hide recurrence if we are showing all recurrence events via AJAX
				$hide = false;
			} elseif (
				is_object( $query )
				&& ! empty( $query->query['eventDisplay'] )
				&& in_array( $query->query['eventDisplay'], array( 'month', 'week' ) )
			) {
				// let's not hide recurrence if we are on month or week view
				$hide = false;
			} elseif ( tribe_get_option( 'hideSubsequentRecurrencesDefault', false ) ) {
				// let's HIDE recurrence events if we've set the option
				$hide = true;
			} elseif ( isset( $_GET['tribeHideRecurrence'] ) && 1 == $_GET['tribeHideRecurrence'] ) {
				// let's HIDE recurrence events if tribeHideRecurrence via GET
				$hide = true;
			} elseif ( isset( $_POST['tribeHideRecurrence'] ) && 1 == $_POST['tribeHideRecurrence'] ) {
				// let's HIDE recurrence events if tribeHideRecurrence via POST
				$hide = true;
			}

			/**
			 * Filters whether recurring event instances should be hidden or not.
			 *
			 * @since 4.4.29
			 *
			 * @param bool $hide
			 * @param WP_Query|null $query
			 */
			$hide = apply_filters( 'tribe_events_pro_should_hide_recurrence', $hide, $query );

			return (bool) $hide;
		}

		/**
		 * Return the forums link as it should appear in the help tab.
		 *
		 * @return string
		 */
		public function helpTabForumsLink( $content ) {
			if ( get_option( 'pue_install_key_events_calendar_pro ' ) ) {
				return 'https://evnt.is/4x';
			} else {
				return 'https://evnt.is/4w';
			}
		}

		/**
		 * Return additional action for the plugin on the plugins page.
		 *
		 * @return array
		 */
		public function addLinksToPluginActions( $actions ) {
			if ( class_exists( 'Tribe__Events__Main' ) ) {
				$actions['settings'] = '<a href="' . Tribe__Settings::instance()->get_url() . '">' . esc_html__( 'Settings', 'tribe-events-calendar-pro' ) . '</a>';
			}

			return $actions;
		}

		/**
		 * Adds thumbnail/featured image support to Organizers and Venues when PRO is activated.
		 *
		 * @param array $post_type_args The current register_post_type args.
		 *
		 * @return array The new register_post_type args.
		 */
		public function addSupportsThumbnail( $post_type_args ) {
			$post_type_args['supports'][] = 'thumbnail';

			return $post_type_args;
		}

		/**
		 * Enable "view post" links on metaposts.
		 *
		 * @param $messages array
		 * @return array
		 */
		public function updatePostMessages( $messages ) {
			global $post, $post_ID;

			$messages[ Tribe__Events__Main::VENUE_POST_TYPE ][1] = sprintf( __( 'Venue updated. <a href="%s">View venue</a>', 'tribe-events-calendar-pro' ), esc_url( get_permalink( $post_ID ) ) );
			/* translators: %s: date and time of the revision */
			$messages[ Tribe__Events__Main::VENUE_POST_TYPE ][6] = sprintf( __( 'Venue published. <a href="%s">View venue</a>', 'tribe-events-calendar-pro' ), esc_url( get_permalink( $post_ID ) ) );
			$messages[ Tribe__Events__Main::VENUE_POST_TYPE ][8] = sprintf( __( 'Venue submitted. <a target="_blank" href="%s">Preview venue</a>', 'tribe-events-calendar-pro' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) );
			$messages[ Tribe__Events__Main::VENUE_POST_TYPE ][9]  = sprintf(
				__( 'Venue scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview venue</a>', 'tribe-events-calendar-pro' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'tribe-events-calendar-pro' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) )
			);
			$messages[ Tribe__Events__Main::VENUE_POST_TYPE ][10] = sprintf( __( 'Venue draft updated. <a target="_blank" href="%s">Preview venue</a>', 'tribe-events-calendar-pro' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) );

			$messages[ Tribe__Events__Main::ORGANIZER_POST_TYPE ][1] = sprintf( __( 'Organizer updated. <a href="%s">View organizer</a>', 'tribe-events-calendar-pro' ), esc_url( get_permalink( $post_ID ) ) );
			$messages[ Tribe__Events__Main::ORGANIZER_POST_TYPE ][6] = sprintf( __( 'Organizer published. <a href="%s">View organizer</a>', 'tribe-events-calendar-pro' ), esc_url( get_permalink( $post_ID ) ) );
			$messages[ Tribe__Events__Main::ORGANIZER_POST_TYPE ][8] = sprintf( __( 'Organizer submitted. <a target="_blank" href="%s">Preview organizer</a>', 'tribe-events-calendar-pro' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) );
			$messages[ Tribe__Events__Main::ORGANIZER_POST_TYPE ][9]  = sprintf(
				__( 'Organizer scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview organizer</a>', 'tribe-events-calendar-pro' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'tribe-events-calendar-pro' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) )
			);
			$messages[ Tribe__Events__Main::ORGANIZER_POST_TYPE ][10] = sprintf( __( 'Organizer draft updated. <a target="_blank" href="%s">Preview organizer</a>', 'tribe-events-calendar-pro' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) );

			return $messages;

		}

		/**
		 * Includes and handles registration/de-registration of the advanced list widget. Idea from John Gadbois.
		 *
		 * @return void
		 */
		public function pro_widgets_init() {
			// Widgets to register.
			$registered_widget_classes = [
				'Tribe__Events__Pro__Advanced_List_Widget',
				'Tribe__Events__Pro__Countdown_Widget',
				'Tribe__Events__Pro__Mini_Calendar_Widget',
				'Tribe__Events__Pro__Venue_Widget',
				'Tribe__Events__Pro__This_Week_Widget',
			];

			/**
			 * Allows plugins and future updates to dis/enable widgets via filer.
			 *
			 * @since 5.2.0
			 *
			 * @param array<string> $registered_widget_classes An array of widget class names to register.
			 */
			$registered_widget_classes = apply_filters(
				'tribe_events_pro_v1_registered_widget_classes',
				$registered_widget_classes
			);

			foreach ( $registered_widget_classes as $widget ) {
				register_widget( $widget );
			}

			// Widgets to unregister.
			$unregistered_widget_classes = [
				'Tribe__Events__List_Widget',
			];

			/**
			 * Allows plugins and future updates to dis/enable widgets via filer.
			 *
			 * @since 5.2.0
			 *
			 * @param array<string> $unregistered_widget_classes An array of widget class names to unregister.
			 */
			$unregistered_widget_classes = apply_filters(
				'tribe_events_pro_v1_unregistered_widget_classes',
				$unregistered_widget_classes
			);

			foreach ( $unregistered_widget_classes as $widget ) {
				unregister_widget( $widget );
			}
		}

		/**
		 * Load textdomain for localization
		 *
		 * @return void
		 */
		public function loadTextDomain() {
			$mopath = $this->pluginDir . 'lang/';
			$domain = 'tribe-events-calendar-pro';

			// If we don't have Common classes load the old fashioned way
			if ( ! class_exists( 'Tribe__Main' ) ) {
				load_plugin_textdomain( $domain, false, $mopath );
			} else {
				// This will load `wp-content/languages/plugins` files first
				Tribe__Main::instance()->load_text_domain( $domain, $mopath );
			}
		}

		/**
		 * Re-registers the custom post types for venues so they allow search from the frontend.
		 *
		 * @return void
		 */
		public function allow_cpt_search() {
			$tec = Tribe__Events__Main::instance();
			$venue_args = $tec->getVenuePostTypeArgs();
			$venue_args['exclude_from_search'] = false;
			register_post_type( Tribe__Events__Main::VENUE_POST_TYPE, apply_filters( 'tribe_events_register_venue_type_args', $venue_args ) );
		}

		/**
		 * Add meta links on the plugins page.
		 *
		 * @param array $links The current array of links to display.
		 * @param string $file The plugin to add meta links to.
		 *
		 * @return array The modified array of links to display.
		 */
		public function addMetaLinks( $links, $file ) {
			if ( $file == $this->pluginDir . 'events-calendar-pro.php' ) {
				$anchor = __( 'Support', 'tribe-events-calendar-pro' );
				$links[] = '<a href="https://evnt.is/4z">' . $anchor . '</a>';
				$anchor = __( 'View All Add-Ons', 'tribe-events-calendar-pro' );
				$links[] = '<a href="https://evnt.is/50">' . $anchor . '</a>';
			}

			return $links;
		}

		/**
		 * Add support for ugly links for ugly links with PRO views.
		 *
		 * @param string $eventUrl The current URL.
		 * @param string $type The type of endpoint/view whose link was requested.
		 * @param string $secondary More data that is necessary for generating the link.
		 *
		 * @return string The ugly-linked URL.
		 */
		public function ugly_link( $eventUrl, $type, $secondary ) {
			switch ( $type ) {
				case 'week':
					if ( ! apply_filters( 'tribe_events_force_ugly_link', false ) && empty( $_POST['baseurl'] ) ) {
						$eventUrl = add_query_arg( 'post_type', Tribe__Events__Main::POSTTYPE, $eventUrl );
					}

					$eventUrl = add_query_arg( array( 'tribe_event_display' => $type ), $eventUrl );
					if ( $secondary ) {
						$eventUrl = add_query_arg( array( 'date' => $secondary ), $eventUrl );
					}
					break;
				case 'photo':
				case 'map':
					$eventUrl = add_query_arg( array( 'tribe_event_display' => $type ), $eventUrl );
					break;
				case 'all':
					remove_filter(
						'post_type_link',
						array( $this->permalink_editor, 'filter_recurring_event_permalinks' ),
						10, 4
					);
					$post_id = $secondary ? $secondary : get_the_ID();
					$parent_id = wp_get_post_parent_id( $post_id );
					if ( ! empty( $parent_id ) ) {
						$post_id = $parent_id;
					}

					/**
					 * Filters the "all" part of the all recurrences link for a recurring event.
					 *
					 * @param string $all_frag Defaults to the localized versions of the "all" word.
					 * @param int $post_id The event post object ID.
					 * @param int $parent_id The event post object parent ID; this value will be the same as
					 *                             `$post_id` if the event has no parent.
					 */
					$all_frag = apply_filters(
						'tribe_events_pro_all_link_frag',
						$this->all_slug,
						$post_id,
						$parent_id
					);

					$eventUrl = add_query_arg( 'eventDisplay', $all_frag, get_permalink( $post_id ) );
					add_filter(
						'post_type_link',
						array( $this->permalink_editor, 'filter_recurring_event_permalinks' ),
						10, 4
					);
					break;
				default:
					break;
			}

			return apply_filters( 'tribe_events_pro_ugly_link', $eventUrl, $type, $secondary );
		}

		/**
		 * filter Tribe__Events__Main::getLink for pro views
		 *
		 * @param  string $event_url
		 * @param  string $type
		 * @param  string $secondary
		 * @param  string $term
		 *
		 * @return string
		 */
		public function get_link( $event_url, $type, $secondary, $term ) {
			switch ( $type ) {
				case 'week':
					$event_url = trailingslashit( esc_url_raw( $event_url . $this->weekSlug ) );
					if ( ! empty( $secondary ) ) {
						$event_url = esc_url_raw( trailingslashit( $event_url ) . $secondary );
					}
					break;
				case 'photo':
					$event_url = trailingslashit( esc_url_raw( $event_url . $this->photoSlug ) );
					if ( ! empty( $secondary ) ) {
						$event_url = esc_url_raw( trailingslashit( $event_url ) . $secondary );
					}
					break;
				case 'map':
					$event_url = trailingslashit( esc_url_raw( $event_url . Tribe__Events__Pro__Geo_Loc::instance()->rewrite_slug ) );
					if ( ! empty( $secondary ) ) {
						$event_url = esc_url_raw( trailingslashit( $event_url ) . $secondary );
					}
					break;
				case 'all':
					// Temporarily disable the post_type_link filter for recurring events
					$link_filter = array( $this->permalink_editor, 'filter_recurring_event_permalinks' );
					remove_filter( 'post_type_link', $link_filter, 10, 4 );

					// Obtain the ID of the parent event
					$post_id   = $secondary ? $secondary : get_the_ID();
					$parent_id = wp_get_post_parent_id( $post_id );
					$event_id  = ( 0 === $parent_id ) ? $post_id : $parent_id;

					/**
					 * Filters the "all" part of the all recurrences link for a recurring event.
					 *
					 * @param string $all_frag  Defaults to the localized versions of the "all" word.
					 * @param int    $post_id   The event post object ID.
					 * @param int    $parent_id The event post object parent ID; this value will be the same as
					 *                          `$post_id` if the event has no parent.
					 */
					$all_frag = apply_filters(
						'tribe_events_pro_all_link_frag',
						$this->all_slug,
						$event_id,
						$parent_id
					);

					$permalink = get_permalink( $event_id );

					$event_url = tribe_append_path( $permalink, $all_frag );

					// Restore the temporarily disabled permalink filter
					add_filter( 'post_type_link', $link_filter, 10, 4 );

					/**
					 * Filters the link to the "all" recurrences view for a recurring event.
					 *
					 * @param string $event_url The link to the "all" recurrences view for the event
					 * @param int $event_id The recurring event post ID
					 */
					$event_url = apply_filters( 'tribe_events_pro_get_all_link', $event_url, $event_id );
					break;
				default:
					break;
			}

			return apply_filters( 'tribe_events_pro_get_link', $event_url, $type, $secondary, $term );
		}

		/**
		 * When showing All events for a recurring event, override the default link
		 *
		 * @param string $link Current page link
		 *
		 * @return string Recurrence compatible current page link
		 */
		public function get_all_link( $link ) {
			if ( ! tribe_is_showing_all() && ! isset( $_POST['tribe_post_parent'] ) ) {
				return $link;
			}

			return $this->get_link( null, 'all', null, null );
		}

		/**
		 * When showing All events for a recurring event, override the default directional link to
		 * view "all" rather than "list"
		 *
		 * @param string $link Current page link
		 *
		 * @return string Recurrence compatible current page link
		 */
		public function get_all_dir_link( $link ) {
			if ( ! tribe_is_showing_all() && ! isset( $_POST['tribe_post_parent'] ) ) {
				return $link;
			}

			$link = preg_replace( '#tribe_event_display=list#', 'tribe_event_display=all', $link );

			return $link;
		}

		/**
		 * If an ajax request has come in with tribe_post_parent, make sure we limit results
		 * to by post_parent
		 *
		 * @param array $args Arguments for fetching events on the listview template
		 * @param array $posted_data POST data from listview ajax request
		 *
		 * @return array
		 */
		public function override_listview_get_event_args( $args, $posted_data ) {
			if ( empty( $posted_data['tribe_post_parent'] ) ) {
				return $args;
			}

			$args['post_parent'] = absint( $posted_data['tribe_post_parent'] );

			return $args;
		}//end override_listview_get_event_args

		/**
		 * overrides the "displaying" setting of the Tribe__Events__Main instance if we are displaying
		 * "all" recurring events"
		 *
		 * @param string $displaying The current eventDisplay value
		 * @param array $args get_event args used to fetch events that are visible in the ajax rendered listview
		 *
		 * @return string
		 */
		public function override_listview_display_setting( $displaying, $args ) {
			if ( empty( $args['post_parent'] ) ) {
				return $displaying;
			}

			return 'all';
		}//end override_listview_display_setting

		/**
		 * Add week view to the views selector in the tribe events bar.
		 *
		 * @param array $views The current array of views registered to the tribe bar.
		 *
		 * @return array The views registered with week view added.
		 */
		public function setup_weekview_in_bar( $views ) {
			$views[] = array(
				'displaying'     => 'week',
				'anchor'         => __( 'Week', 'tribe-events-calendar-pro' ),
				'event_bar_hook' => 'tribe_events_week_before_template',
				'url'            => tribe_get_week_permalink(),
			);

			return $views;
		}

		/**
		 * Add photo view to the views selector in the tribe events bar.
		 *
		 * @param array $views The current array of views registered to the tribe bar.
		 *
		 * @return array The views registered with photo view added.
		 */
		public function setup_photoview_in_bar( $views ) {
			$views[] = array(
				'displaying'     => 'photo',
				'anchor'     => __( 'Photo', 'tribe-events-calendar-pro' ),
				'event_bar_hook'       => 'tribe_events_before_template',
				'url'            => tribe_get_photo_permalink(),
			);

			return $views;
		}

		/**
		 * Change the datepicker label, depending on what view the user is on.
		 *
		 * @param string $caption The current caption for the datepicker.
		 *
		 * @return string The new caption.
		 */
		public function setup_datepicker_label ( $caption ) {
			if ( tribe_is_week() ) {
				$caption = __( 'Week Of', 'tribe-events-calendar-pro' );
			}

			return $caption;
		}

		/**
		 * Echo the setting for hiding subsequent occurrences of recurring events to frontend.
		 * Old function name contained a typo ("occurance") - this fixes it
		 * without breaking anything where users may be calling the old function.
		 *
		 * @TODO: perhaps deprecate the old one at some point?
		 *
		 * @return void
		 */
		public function add_recurring_occurance_setting_to_list () {
			return $this->add_recurring_occurrence_setting_to_list();
		}

		/**
		 * Echo the setting for hiding subsequent occurrences of recurring events to frontend.
		 *
		 * @return void
		 */
		public function add_recurring_occurrence_setting_to_list() {
			if ( tribe_get_option( 'userToggleSubsequentRecurrences', false ) && ! tribe_is_showing_all() && ( tribe_is_upcoming() || tribe_is_past() || tribe_is_map() || tribe_is_photo() ) || apply_filters( 'tribe_events_display_user_toggle_subsequent_recurrences', false ) ) {
				echo tribe_recurring_instances_toggle();
			}
		}

		/**
		 * Returns he ratio of kilometers to miles.
		 *
		 * @return float The ratio.
		 */
		public function kms_to_miles_ratio() {
			return 0.621371;
		}

		/**
		 * Returns he ratio of miles to kilometers.
		 *
		 * @return float The ratio.
		 */
		public function miles_to_kms_ratio() {
			return 1.60934;
		}

		/**
		 * Instances the filters.
		 */
		public function init_apm_filters() {
			new Tribe__Events__Pro__APM_Filters__APM_Filters( );
			new Tribe__Events__Pro__APM_Filters__Date_Filter( );
			new Tribe__Events__Pro__APM_Filters__Recur_Filter( );
			new Tribe__Events__Pro__APM_Filters__Content_Filter( );
			new Tribe__Events__Pro__APM_Filters__Title_Filter( );
			new Tribe__Events__Pro__APM_Filters__Venue_Filter( );
			new Tribe__Events__Pro__APM_Filters__Organizer_Filter( );

			/**
			 * Fires after APM filters have been instantiated.
			 *
			 * This is the action additional filters defining should hook to instantiate those filters.
			 *
			 * @since 4.1
			 */
			do_action( 'tribe_events_pro_init_apm_filters' );
		}

		/**
		 * Registers The Events Calendar with the views/overrides update checker.
		 *
		 * @param array $plugins
		 *
		 * @return array
		 */
		public function register_template_updates( $plugins ) {
			$plugins[ __( 'Events Calendar PRO', 'tribe-events-calendar-pro' ) ] = array(
				self::VERSION,
				$this->pluginPath . 'src/views/pro',
				trailingslashit( get_stylesheet_directory() ) . 'tribe-events/pro',
			);

			return $plugins;
		}

		/**
		 * plugin deactivation callback
		 * @see register_deactivation_hook()
		 *
		 * @param bool $network_deactivating
		 */
		public static function deactivate( $network_deactivating ) {
			if ( ! class_exists( 'Tribe__Events__Main' ) ) {
				return; // can't do anything since core isn't around
			}
			$deactivation = new Tribe__Events__Pro__Deactivation( $network_deactivating );
			add_action( 'shutdown', array( $deactivation, 'deactivate' ) );
		}

		/**
		 * The singleton function.
		 *
		 * @return Tribe__Events__Pro__Main The instance.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				$className = __CLASS__;
				self::$instance = new $className;
			}

			return self::$instance;
		}

		/**
		 * Outputs oembed resource links on the /all/ pages for recurring events
		 *
		 * @since 4.2
		 *
		 * @param string $output Resource links to output
		 *
		 * @return string
		 */
		public function oembed_discovery_links_for_recurring_events( $output ) {
			$wp_query = tribe_get_global_query_object();

			if ( $output ) {
				return $output;
			}

			if ( ! tribe_is_showing_all() ) {
				return $output;
			}

			if ( is_null( $wp_query ) || empty( $wp_query->posts[0] ) ) {
				return $output;
			}

			$post = $wp_query->posts[0];
			$post_id = $post->ID;

			$output = '<link rel="alternate" type="application/json+oembed" href="' . esc_url( get_oembed_endpoint_url( add_query_arg( 'post_id', $post_id, get_permalink( $post_id ) ) ) ) . '" />' . "\n";

			if ( class_exists( 'SimpleXMLElement' ) ) {
				$output .= '<link rel="alternate" type="text/xml+oembed" href="' . esc_url( get_oembed_endpoint_url( add_query_arg( 'post_id', $post_id, get_permalink( $post_id ) ), 'xml' ) ) . '" />' . "\n";
			}

			return $output;
		}

		/**
		 * Convert a /all/ URL to an upcoming post id for oembeds
		 *
		 * @since 4.2
		 *
		 * @param int $post_id Post ID of the event
		 * @param string $url URL of the oembed resource
		 *
		 * @return int
		 */
		public function oembed_request_post_id_for_recurring_events( $post_id, $url ) {
			if ( $post_id ) {
				return $post_id;
			}

			$recurring_event_id = tribe_get_upcoming_recurring_event_id_from_url( $url );
			if ( $recurring_event_id ) {
				return $recurring_event_id;
			}

			// we weren't able to find something better, so return the original value
			return $post_id;
		}

		/**
		 * Instances all classes that should be built at `plugins_loaded` time.
		 *
		 * Classes are bound using the `tribe_singleton` function before and then
		 * built calling the `tribe` function.
		 */
		public function on_plugins_loaded() {
			$this->all_slug = sanitize_title( __( 'all', 'tribe-events-calendar-pro' ) );
			$this->weekSlug = sanitize_title( __( 'week', 'tribe-events-calendar-pro' ) );
			$this->photoSlug = sanitize_title( __( 'photo', 'tribe-events-calendar-pro' ) );

			tribe_singleton( 'events-pro.main', $this );

			// Assets loader
			tribe_singleton( 'events-pro.assets', 'Tribe__Events__Pro__Assets', array( 'register' ) );

			tribe_singleton( 'events-pro.admin.settings', 'Tribe__Events__Pro__Admin__Settings', array( 'hook' ) );

			if ( ! tribe_events_views_v2_is_enabled() ) {
				tribe_singleton( 'events-pro.customizer.photo-view', 'Tribe__Events__Pro__Customizer__Photo_View' );
				tribe( 'events-pro.customizer.photo-view' );
			}
			tribe_singleton( 'events-pro.recurrence.nav', 'Tribe__Events__Pro__Recurrence__Navigation', array( 'hook' ) );
			tribe_singleton( 'events-pro.ical', 'Tribe__Events__Pro__iCal', [ 'hook' ] );

			tribe_register_provider( 'Tribe__Events__Pro__Editor__Provider' );

			tribe( 'events-pro.admin.settings' );
			tribe( 'events-pro.assets' );
			tribe( 'events-pro.recurrence.nav' );
			tribe( 'events-pro.ical' );

			tribe_register_provider( 'Tribe__Events__Pro__Service_Providers__ORM' );
			tribe_register_provider( 'Tribe__Events__Pro__Service_Providers__RBE' );
			tribe_register_provider( Tribe\Events\Pro\Views\V2\Service_Provider::class );
			tribe_register_provider( Tribe\Events\Pro\Views\V2\Widgets\Service_Provider::class );
			tribe_register_provider( Tribe\Events\Pro\Models\Service_Provider::class );
			tribe_register_provider( Tribe__Events__Pro__Service_Providers__Templates::class );

			// Rewrite support.
			tribe_register_provider( Tribe\Events\Pro\Rewrite\Provider::class );

			// Context support.
			tribe_register_provider( Tribe\Events\Pro\Service_Providers\Context::class );

			// Customizer support.
			tribe_register_provider( Tribe\Events\Pro\Service_Providers\Customizer::class );
		}

		/**
		 * Registers this plugin as being active for other tribe plugins and extensions
		 *
		 * @deprecated 4.6
		 *
		 * @return bool Indicates if Tribe Common wants the plugin to run
		 */
		public function register_active_plugin() {
			_deprecated_function( __METHOD__, '4.6', '' );

			if ( ! function_exists( 'tribe_register_plugin' ) ) {
				return true;
			}
			return tribe_register_plugin( EVENTS_CALENDAR_PRO_FILE, __CLASS__, self::VERSION );
		}

		/**
		 * Determines whether or not to show the custom fields metabox for events.
		 *
		 * @deprecated
		 *
		 * @return bool Whether to show or not.
		 */
		public function displayMetaboxCustomFields() {
			_deprecated_function( __METHOD__, '4.4.32', 'Use tribe( "tec.admin.event-meta-box" )->display_wp_custom_fields_metabox() instead' );

			$show_box = tribe_get_option( 'disable_metabox_custom_fields' );
			if ( $show_box == 'show' ) {
				return true;
			}
			if ( $show_box == 'hide' ) {
				remove_post_type_support( Tribe__Events__Main::POSTTYPE, 'custom-fields' );
				return false;
			}
			if ( empty( $show_box ) ) {
				global $wpdb;
				$meta_keys = $wpdb->get_results(
					"SELECT DISTINCT pm.meta_key FROM $wpdb->postmeta pm
									LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
									WHERE p.post_type = '" . Tribe__Events__Main::POSTTYPE . "'
									AND pm.meta_key NOT LIKE '_wp_%'
									AND pm.meta_key NOT IN (
										'_edit_last',
										'_edit_lock',
										'_thumbnail_id',
										'_EventConference',
										'_EventAllDay',
										'_EventHideFromUpcoming',
										'_EventOrigin',
										'_EventShowMap',
										'_EventVenueID',
										'_EventShowMapLink',
										'_EventCost',
										'_EventOrganizerID',
										'_EventRecurrence',
										'_EventStartDate',
										'_EventEndDate',
										'_EventDuration',
										'_FacebookID')"
				);
				if ( empty( $meta_keys ) ) {
					remove_post_type_support( Tribe__Events__Main::POSTTYPE, 'custom-fields' );
					$show_box = 'hide';
					$r = false;
				} else {
					$show_box = 'show';
					$r = true;
				}

				tribe_update_option( 'disable_metabox_custom_fields', $show_box );

				return $r;
			}

		}

		/**
		 * Add legacy stylesheets to customizer styles array to check.
		 *
		 * @param array<string> $sheets Array of sheets to search for.
		 * @param string        $css_template String containing the inline css to add.
		 *
		 * @return array Modified array of sheets to search for.
		 */
		public function customizer_inline_stylesheets( $sheets, $css_template ) {
			$pro_sheets = [
				'tribe-events-calendar-pro-style',
				'widget-calendar-pro-style',
			];

			return array_merge( $sheets, $pro_sheets );
		}
	} // end Class
}
