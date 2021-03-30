<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Community__Main' ) ) {
	/**
	 * Tribe Community Events main class
	 *
	 * @package Tribe__Events__Community__Main
	 * @author Modern Tribe Inc.
	 * @since  1.0
	 */
	class Tribe__Events__Community__Main {

		/**
		 * The current version of Community Events
		 */
		const VERSION = '4.8.5';

		/**
		 * Singleton instance variable
		 * @var object
		 */
		private static $instance;

		/**
		 * Whether before and after event HTML should be printed on the page.
		 * @var bool
		 */
		protected $should_print_before_after_html = true;

		/**
		 * Loadscripts or not
		 * @var bool
		 */
		private $loadScripts = false;

		/**
		 * plugin options
		 * @var array
		 */
		protected static $options;

		/**
		 * this plugin's directory
		 * @var string
		 */
		public $pluginDir;

		/**
		 * this plugin's path
		 * @var string
		 */
		public $pluginPath;

		/**
		 * this plugin's url
		 * @var string
		 */
		public $pluginUrl;

		/**
		 * this plugin's slug
		 * @var string
		 */
		public $pluginSlug;

		/**
		 * tribe url (used for calling the mothership)
		 * @var string
		 */
		public static $tribeUrl = 'http://tri.be/';

		/**
		 * default event status
		 * @var string
		 */
		public $defaultStatus;

		/**
		 * setting to allow anonymous submissions
		 * @var bool
		 */
		public $allowAnonymousSubmissions;

		/**
		 * Setting to allow editing submissions.
		 *
		 * @var bool
		 */
		public $allowUsersToEditSubmissions;

		/**
		 * Setting to allow deletion of submissions.
		 *
		 * @var bool
		 */
		public $allowUsersToDeleteSubmissions;

		/**
		 * setting to trash items instead of permanent delete
		 * @var bool
		 */
		public $trashItemsVsDelete;

		/**
		 * setting to use visual editor
		 * @var bool
		 */
		public $useVisualEditor;

		/**
		 * setting to control # of events per page
		 * @var int
		 */
		public $eventsPerPage;

		/**
		 * setting to control format for dates
		 * @var string
		 *
		 * @deprecated 4.5.10 Use tribe_get_datetime_format() instead.
		 */
		public $eventListDateFormat;

		/**
		 * setting for pagination range
		 * @var string
		 */
		public $paginationRange;

		/**
		 * setting for default organizer (requires ECP)
		 * @var int
		 */
		public $defaultCommunityOrganizerID;

		/**
		 * setting for default venue (requires ECP)
		 * @var int
		 */
		public $defaultCommunityVenueID;

		/**
		 * message to be displayed to the user
		 * @var array
		 */
		public $messages;

		/**
		 * the type of the message (error, notice, etc.)
		 * @var string
		 */
		public $messageType;

		/**
		 * the rewrite slug to use
		 * @var string
		 */
		public $communityRewriteSlug;

		/**
		 * Array of rewrite slugs for different components
		 * @var array
		 */
		public $rewriteSlugs;

		/**
		 * Attributes of current location.
		 * @var array
		 */
		public $context;

		/**
		 * is the current page the my events list?
		 * @var bool
		 */
		public $isMyEvents = false;

		/**
		 * is the current page the event edit page?
		 * @var bool
		 */
		public $isEditPage = false;

		/**
		 * should the permalinks be flushed upon plugin load?
		 * @var bool
		 */
		public $maybeFlushRewrite;

		/**
		 * @var Tribe__Events__Community__Anonymous_Users
		 */
		public $anonymous_users;

		/**
		 * The login form ID.
		 *
		 * Used for WP login form ID, hidden login submission field name, and query parameter.
		 *
		 * @since 4.6.3
		 *
		 * @var string
		 */
		private $login_form_id = 'tribe_events_community_login';

		/**
		 * @var int The ID of a page with the community shortcode on it
		 */
		private $tcePageId = null;

		/** @var Tribe__Events__Community__Captcha__Abstract_Captcha */
		private $captcha = null;

		/** @var Tribe__Events__Community__Event_Form */
		public $form;

		/**
		 * The default slugs to use for rewrites.
		 *
		 * @since 4.6.3
		 *
		 * @var array
		 */
		public $default_rewrite_slugs = [
			'add'       => 'add',
			'list'      => 'list',
			'edit'      => 'edit',
			'delete'    => 'delete',
			'venue'     => 'venue',
			'organizer' => 'organizer',
			'event'     => 'event',
		];

		/**
		 * URL to redirect instead of allowing access to admin.
		 *
		 * @deprecated 4.6.3 Use $this->get_block_roles_redirect_url() instead.
		 *
		 * @var string
		 */
		public $blockRolesRedirect  = '';

		/**
		 * A meta field to help us track if an event's "Submitted" email alert has already been sent.
		 *
		 * @since 4.5.11
		 *
		 * @var string
		 */
		private static $submission_email_sent_meta_key = '_tribe_community_submitted_email_sent';

		/**
		 * Holds the multisite default options values for CE.
		 * @var array
		 */
		public static $tribeCommunityEventsMuDefaults;

		/**
		 * option name to save all plugin options under
		 * as a serialized array
		 */
		const OPTIONNAME = 'tribe_community_events_options';

		/**
		 * Class constructor
		 * Sets all the class vars up and such
		 *
		 * @since 1.0
		 */
		public function __construct() {
			// Load multisite defaults
			if ( is_multisite() ) {
				$tribe_community_events_mu_defaults = [];

				if ( file_exists( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' ) ) {
					include_once( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' );
				}

				self::$tribeCommunityEventsMuDefaults = apply_filters( 'tribe_community_events_mu_defaults', $tribe_community_events_mu_defaults );
			}

			// get options
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );
			$this->allowAnonymousSubmissions     = $this->getOption( 'allowAnonymousSubmissions' );
			$this->allowUsersToEditSubmissions   = $this->getOption( 'allowUsersToEditSubmissions' );
			$this->allowUsersToDeleteSubmissions = $this->getOption( 'allowUsersToDeleteSubmissions' );
			$this->trashItemsVsDelete            = $this->getOption( 'trashItemsVsDelete' );
			$this->useVisualEditor               = $this->getOption( 'useVisualEditor' );
			$this->eventsPerPage                 = $this->getOption( 'eventsPerPage', 10 );
			$this->eventListDateFormat           = $this->getOption( 'eventListDateFormat' );
			$this->paginationRange               = 3;
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );

			/**
			 * By default both PostTypes are true
			 */

			$this->users_can_create[ Tribe__Events__Venue::POSTTYPE ]     = ! (bool) $this->getOption( 'prevent_new_venues', false );
			$this->users_can_create[ Tribe__Events__Organizer::POSTTYPE ] = ! (bool) $this->getOption( 'prevent_new_organizers', false );

			$this->emailAlertsEnabled            = $this->getOption( 'emailAlertsEnabled' );
			$emailAlertsList                     = $this->getOption( 'emailAlertsList' );

			$this->emailAlertsList = explode( "\n", $emailAlertsList );

			$this->blockRolesFromAdmin = $this->getOption( 'blockRolesFromAdmin' );
			$this->blockRolesList      = $this->getOption( 'blockRolesList' );

			$this->maybeFlushRewrite   = $this->getOption( 'maybeFlushRewrite' );

			if ( $this->blockRolesFromAdmin ) {
				add_action( 'init', [ $this, 'blockRolesFromAdmin' ] );
			}

			$this->pluginPath = trailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) );
			$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
			$this->pluginUrl = plugins_url() . '/' . $this->pluginDir;
			$this->pluginSlug = 'events-community';

			$this->register_active_plugin();

			$this->isMyEvents = false;
			$this->isEditPage = false;

			add_shortcode( 'tribe_community_events_title', [ $this, 'doShortCodeTitle' ] );

			//allow shortcodes for dynamic titles
			add_filter( 'the_title', 'do_shortcode' );
			add_filter( 'wp_title', 'do_shortcode' );

			if ( '' == get_option( 'permalink_structure' ) ) {
				add_action( 'template_redirect', [ $this, 'maybeRedirectMyEvents' ] );
			} else {
				add_action( 'template_redirect', [ $this, 'redirectUglyUrls' ] );
			}

			/**
			 * In 3.5 this is causing an error moved self::maybeLoadAssets(); into function init()...
			 * Also is important to remember that using methods with Params we need to make sure the Hook doesn't pass any params.
			 * In the case of `wp` it passes an instance of the class WP which was breaking how maybeLoadAssets works.
			 *
			 * @central #71943
			 */
			add_action( 'wp', [ $this, 'maybeLoadAssets' ], 10, 0 );

			add_action( 'tribe_load_text_domains', [ $this, 'loadTextDomain' ], 1 );

			add_action( 'init', [ $this, 'init' ], 5 );

			add_action( 'init', [ $this, 'load_captcha_plugin' ], 11 );

			add_action( 'admin_init', [ $this, 'maybeFlushRewriteRules' ] );

			add_action( 'wp_before_admin_bar_render', [ $this, 'addCommunityToolbarItems' ], 20 );

			add_filter( 'tribe_tickets_user_can_manage_attendees', [ $this, 'user_can_manage_own_event_attendees' ], 10, 3 );

			// Tribe common resources
			include_once(  $this->pluginPath . 'vendor/jbrinley/wp-router/wp-router.php' );

			add_action( 'tribe_settings_save_field_allowAnonymousSubmissions', [ $this, 'flushRewriteOnAnonymous' ], 10, 2 );

			add_filter( 'query_vars', [ $this, 'communityEventQueryVars' ] );

			add_action( 'admin_head', [ $this, 'possibly_show_event_cost' ] );

			// Priority set to 11 so some core body_class items can be removed after added.
			add_filter( 'body_class', [ $this, 'setBodyClasses' ], 11 );

			// Hook into templates class and add theme body classes
			add_filter( 'body_class', [ 'Tribe__Events__Templates', 'theme_body_class' ] );

			// ensure that we don't include tabindexes in our form fields
			add_filter( 'tribe_events_tab_index', '__return_null' );

			// options page hook
			add_action( 'tribe_settings_do_tabs', [ $this, 'doSettings' ], 10, 1 );

			add_action( 'wp_router_generate_routes', [ $this, 'addRoutes' ] );

			add_action( 'plugin_action_links_' . trailingslashit( $this->pluginDir ) . 'Main.php', [ $this, 'addLinksToPluginActions' ] );

			add_filter( 'tribe-events-pro-support', [ $this, 'support_info' ] );

			add_action( 'tribe_community_before_event_page', [ $this, 'maybe_delete_featured_image' ], 10, 1 );
			add_filter( 'tribe_help_tab_forums_url', [ $this, 'helpTabForumsLink' ], 100 );

			add_action( 'save_post', [ $this, 'flushPageIdTransient' ], 10, 1 );

			add_filter( 'user_has_cap', [ $this, 'filter_user_caps' ], 10, 3 );

			if ( is_multisite() ) {
				add_action( 'tribe_settings_get_option_value_pre_display', [ $this, 'multisiteDefaultOverride' ], 10, 3 );
			}

			add_filter( 'tribe_events_multiple_organizer_template', [ $this, 'overwrite_multiple_organizers_template' ] );

			add_action( 'plugins_loaded', [ $this, 'register_resources' ] );

			add_action( 'admin_init', [ $this, 'run_updates' ], 10, 0 );

			add_action( 'wp_ajax_tribe_events_community_delete_post', [ $this, 'ajaxDoDelete' ] );

			// Login form.
			add_filter( 'login_form_bottom', [ $this, 'add_hidden_form_fields_to_login_form' ] );
			add_filter( 'authenticate', [ $this, 'login_form_authentication' ], 70, 3 );
			add_action( 'wp_login_failed', [ $this, 'redirect_failed_login_to_front_end' ] );
			add_action( 'tribe_community_before_login_form', [ $this, 'output_login_form_notices' ] );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_admin_assets' ), 20 );

			// Binding the Implementations needs to happen to plugins_loaded
			$this->bind_implementations();
		}

		/**
		 * Filter out Linked Post Types creation, to allow or prevent depending on the Admin Settings
		 *
		 * @since 4.4
		 * @deprecated 4.5.2
		 *
		 * @param  array  $args       Original set of arguments
		 * @param  string $post_type  Post Type slug
		 *
		 * @return array
		 */
		public function filter_linked_post_type_creation( $args, $post_type ) {
			_deprecated_function( __METHOD__, '4.5.2' );
			return $args;
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

			return tribe_register_plugin( EVENTS_COMMUNITY_FILE, __CLASS__, self::VERSION );
		}

		/**
		 * Method used to overwrite the admin template for multiple organizers
		 *
		 * @param  string $template The original template
		 * @return string
		 */
		public function overwrite_multiple_organizers_template( $template ) {
			if ( is_admin() ) {
				return $template;
			}

			$community_file = Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/organizer-multiple.php' );

			ob_start();
			include $community_file;
			$community_html = trim( ob_get_clean() );

			// Only use this URL if the template is not empty
			if ( empty( $community_html ) ) {
				return $template;
			}

			return $community_file;
		}

		/**
		 * Object accessor method for the Event_Form object
		 *
		 * @return Tribe__Events__Community__Event_Form
		 */
		public function event_form() {
			if ( ! $this->form ) {
				$event = null;

				if ( ! empty( $_GET['event_id'] ) ) {
					$event = get_post( absint( $_GET['event_id'] ) );
				}

				$this->form = new Tribe__Events__Community__Event_Form( $event );
			}

			return $this->form;
		}//end event_form

		/**
		 * Determines what assets to load.
		 *
		 * @param bool $force
		 */
		public function maybeLoadAssets( $force = false ) {
			$force = tribe_is_truthy( $force );

			// We are not forcing if it's not a boolean
			if ( ! is_bool( $force ) ) {
				$force = false;
			}

			// If we are forcing it we just bail
			if ( ! $force && ! tribe_is_community_my_events_page() && ! tribe_is_community_edit_event_page() ) {
				return;
			}

			// Disable comments on this page.
			add_filter( 'comments_template', [ $this, 'disable_comments_on_page' ] );

			// Load EC resources.
			if ( did_action( 'wp_enqueue_scripts' ) ) {
				$this->enqueue_assets();
			} else {
				add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );
			}
		}

		/**
		 * Registers scripts and styles.
		 */
		public function register_resources() {
			tribe_asset( $this, 'tribe-events-community-select2', 'tribe-events-community-select2.css' );
			tribe_asset( $this, 'tribe-events-community-list', 'tribe-events-community-list.css' );
			tribe_asset( $this, 'tribe-events-community-shortcodes', 'tribe-events-community-shortcodes.css' );

			// Our stylesheet
			tribe_asset(
				$this,
				Tribe__Events__Main::POSTTYPE . '-community-styles',
				'tribe-events-community.css',
				[
					'tribe-datepicker',
					'tribe-select2-css',
					'tribe-common-admin',
					'tribe-dependency-style',
					'tribe-events-community-list',
				]
			);

			// Admin stylesheet
			tribe_asset(
				$this,
				Tribe__Events__Main::POSTTYPE . '-community-admin-styles',
				'tribe-events-community-admin.css',
				[]
			);

			// Custom stylesheet
			tribe_asset(
				$this,
				'tribe-events-community-override-styles',
				Tribe__Events__Templates::locate_stylesheet( 'tribe-events/community/tribe-events-community.css' ),
				[],
				'wp_enqueue_scripts',
				[
					'groups' => [ 'events-styles' ],
				]
			);

			// Our javascript
			tribe_asset(
				$this,
				Tribe__Events__Main::POSTTYPE . '-community',
				'tribe-events-community.js',
				[
					'jquery',
					'tribe-dependency',
				]
			);
		}

		/**
		 * Enqueue scripts & styles.
		 *
		 * @since 1.0
		 * @deprecated 4.4
		 *
		 * @return void
		 */
		public function addScriptsAndStyles() {
			_deprecated_function( __METHOD__, '4.4', 'Tribe__Events__Community__Main::enqueue_assets' );

			$this->enqueue_assets();
		}

		/**
		 * Enqueue on Community Events Pages
		 *
		 * @since  4.4
		 *
		 * @return void
		 */
		public function enqueue_assets() {
			/** @var Tribe__Assets $assets */
			$assets = tribe( 'assets' );

			// Remove front-end scripts in case they're enqueued.
			$assets->remove( 'tribe-events-pro' );
			$assets->remove( 'tribe-events-pro-geoloc' );

			tribe_asset_enqueue_group( 'events-admin' );

			tribe_asset_enqueue( 'tribe-events-dynamic' );
			tribe_asset_enqueue( 'tribe-jquery-timepicker-css' );

			tribe_asset_enqueue( Tribe__Events__Main::POSTTYPE . '-community-styles' );
			tribe_asset_enqueue( Tribe__Events__Main::POSTTYPE . '-community' );

			$required_fields = $this->required_fields_for_submission();
			$error_messages  = [];
			$handler         = new Tribe__Events__Community__Submission_Handler( [], null );

			foreach ( $required_fields as $field => $key ) {
				$label = $handler->get_field_label( $key );

				// Workaround for `post_content` alias.
				$key = 'post_content' === $key ? 'tcepostcontent' : $key;

				/* Translators : %s the form field label for required fields. */
				$message              = __( '%s is required', 'tribe-events-community' );
				$error_messages[ $key ] = sprintf( $message, $label );
			}

			wp_localize_script(
				Tribe__Events__Main::POSTTYPE . '-community',
				'tribe_submit_form_i18n',
				[
					'errors' => $error_messages,
				]
			);

			/**
			 * Fires on Community Events Pages, allowing third-parties to enqueue scripts.
			 */
			do_action( 'tribe_community_events_enqueue_resources' );

			// Hook for other plugins.
			do_action( 'tribe_events_enqueue' );
		}

		/**
		 * Enqueue the admin resources where needed.
		 *
		 * @since 4.6.3
		 *
		 * @param string $screen the current admin screen.
		 */
		public function maybe_enqueue_admin_assets( $screen ) {
			if (
				'tribe_events_page_tribe-common' === $screen
				&& isset( $_GET['tab'] )
				&& 'community' === $_GET['tab']
			) {
				wp_enqueue_style( Tribe__Events__Main::POSTTYPE . '-community-admin-styles' );
			}
		}

		/**
		 * Disable comments on community pages.
		 *
		 * @return null
		 * @author imaginesimplicity
		 * @since 1.0.3
		 */
		public function disable_comments_on_page() {
			return Tribe__Events__Templates::getTemplateHierarchy( 'community/blank-comments-template' );
		}

		/**
		 * Add wprouter and callbacks.
		 *
		 * @param object $router The router object.
		 * @return void
		 *
		 * @since 1.0
		 */
		public function addRoutes( $router ) {

			$tec_template = tribe_get_option( 'tribeEventsTemplate' );

			switch ( $tec_template ) {
				case '' :
					$template_name = Tribe__Events__Templates::getTemplateHierarchy( 'default-template' );
					break;
				case 'default' :
					$template_name = 'page.php';
					break;
				default :
					$template_name = $tec_template;
			}

			$template_name = apply_filters( 'tribe_events_community_template', $template_name );

			// edit venue
			$router->add_route(
				'ce-edit-venue-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['venue'] . '/(\d+)$',
					'query_vars'      => [ 'tribe_event_id' => 1 ],
					'page_callback'   => [
						get_class(),
						'editCallback',
					],
					'page_arguments'  => [ 'tribe_event_id' ],
					'access_callback' => true,
					'title'           => __( 'Edit a Venue', 'tribe-events-community' ),
					'template'        => $template_name,
				]
			);


			// edit organizer
			$router->add_route(
				'ce-edit-organizer-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['organizer'] . '/(\d+)$',
					'query_vars'      => [ 'tribe_event_id' => 1 ],
					'page_callback'   => [
						get_class(),
						'editCallback',
					],
					'page_arguments'  => [ 'tribe_event_id' ],
					'access_callback' => true,
					'title'           => __( 'Edit an Organizer', 'tribe-events-community' ),
					'template'        => $template_name,
				]
			);

			$edit_title = __( 'Edit an Event', 'tribe-events-community' );
			$edit_title = apply_filters( 'tribe_events_community_edit_event_page_title', $edit_title );
			$edit_title = apply_filters_deprecated(
				'tribe_ce_edit_event_page_title',
				[ $edit_title ],
				'4.6.3',
				'tribe_events_community_edit_event_page_title',
				'The filter "tribe_ce_edit_event_page_title" has been renamed to "tribe_events_community_edit_event_page_title" to match plugin namespacing.'
			);

			// edit event
			$router->add_route(
				'ce-edit-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['event'] . '/(\d+/?)$',
					'query_vars'      => [
						'tribe_community_event_id' => 1,
					],
					'page_callback'   => [
						get_class(),
						'editCallback',
					],
					'page_arguments'  => [
						'tribe_community_event_id',
					],
					'access_callback' => true,
					'title'           => $edit_title,
					'template'        => $template_name,
				]
			);

			// edit redirect
			$router->add_route(
				'ce-edit-redirect-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/(\d+)$',
					'query_vars'      => [ 'tribe_id' => 1 ],
					'page_callback'   => [
						get_class(),
						'redirectCallback',
					],
					'page_arguments'  => [ 'tribe_id' ],
					'access_callback' => true,
					'title'           => __( 'Redirect', 'tribe-events-community' ),
					'template'        => $template_name,
				]
			);

			$add_title = __( 'Submit an Event', 'tribe-events-community' );
			$add_title = apply_filters( 'tribe_events_community_submit_event_page_title', $add_title );
			$add_title = apply_filters_deprecated(
				'tribe_ce_submit_event_page_title',
				[ $add_title ],
				'4.6.3',
				'tribe_events_community_submit_event_page_title',
				'The filter "tribe_ce_submit_event_page_title" has been renamed to "tribe_events_community_submit_event_page_title" to match plugin namespacing.'
			);

			// add event
			$router->add_route(
				'ce-add-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['add'] . '$',
					'query_vars'      => [],
					'page_callback'   => [
						get_class(),
						'addCallback',
					],
					'page_arguments'  => [],
					'access_callback' => true,
					'title'           => $add_title,
					'template'        => $template_name,
				]
			);

			$router->add_route(
				'ce-redirect-to-add-route',
				[
					'path'            => $this->getCommunityRewriteSlug() . '/?$',
					'page_callback'   => 'wp_redirect',
					'page_arguments'  => [
						home_url( $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['add'] ),
						301,
					],
					'template'        => false,
					'access_callback' => true,
				]
			);

			$remove_title = __( 'Remove an Event', 'tribe-events-community' );
			$remove_title = apply_filters( 'tribe_events_community_remove_event_page_title', $remove_title );
			$remove_title = apply_filters_deprecated(
				'tribe_ce_remove_event_page_title',
				[ $remove_title ],
				'4.6.3',
				'tribe_events_community_remove_event_page_title',
				'The filter "tribe_ce_remove_event_page_title" has been renamed to "tribe_events_community_remove_event_page_title" to match plugin namespacing.'
			);

			// delete event
			$router->add_route(
				'ce-delete-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['list'] . '/(\d+)$',
					'query_vars'      => [
						'tribe_event_id' => 1,
					],
					'page_callback'   => [
						get_class(),
						'deleteCallback',
					],
					'page_arguments'  => [
						'tribe_event_id',
					],
					'access_callback' => true,
					'title'           => $remove_title,
					'template'        => $template_name,
				]
			);

			$list_title = __( 'My Events', 'tribe-events-community' );
			$list_title = apply_filters( 'tribe_events_community_event_list_page_title', $list_title );
			$list_title = apply_filters_deprecated(
				'tribe_ce_event_list_page_title',
				[ $list_title ],
				'4.6.3',
				'tribe_events_community_event_list_page_title',
				'The filter "tribe_ce_event_list_page_title" has been renamed to "tribe_events_community_event_list_page_title" to match plugin namespacing.'
			);

			// list events
			$router->add_route(
				'ce-list-route',
				[
					'path'            => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['list'] . '(/page/(\d+))?/?$',
					'query_vars'      => [
						'page' => 2,
					],
					'page_callback'   => [
						get_class(),
						'listCallback',
					],
					'page_arguments'  => [
						'page',
					],
					'access_callback' => true,
					'title'           => $list_title,
					'template'        => $template_name,
				]
			);

		}

		/**
		 * Used to ensure that CE views function when the Default Events Template is in use.
		 *
		 * We could consider using a template class at some future point, right now this provides
		 * a light functional means of letting users choose the Default Events Template for CE
		 * views.
		 *
		 * @param bool $print_before_after_override Whether before and after HTML should be printed
		 *                                          on the page in any case (`true`) or that should be
		 *                                          instead a consequence of the context.
		 */
		public function default_template_compatibility( $print_before_after_override = false ) {
			add_filter( 'tribe_events_current_view_template', [ $this, 'default_template_placeholder' ] );
			tribe_asset_enqueue_group( 'events-styles' );

			if ( false === $print_before_after_override && '' === tribe_get_option( 'tribeEventsTemplate', '' ) ) {
				$this->should_print_before_after_html = false;
			} else {
				$this->should_print_before_after_html = true;
			}
		}

		/**
		 * We need to provide an "inner" template if community views are being displayed using the
		 * default template.
		 *
		 * @param $unused_template
		 * @return string
		 */
		public function default_template_placeholder( $unused_template ) {
			return Tribe__Events__Templates::getTemplateHierarchy( 'community/default-placeholder.php' );
		}

		/**
		 * Redirect user to the right place.
		 *
		 * @param string $tribe_id The page being viewed.
		 * @return void
		 *
		 * @since 1.0
		 */
		public static function redirectCallback( $tribe_id ) {

			$tce = self::instance();

			if ( $tribe_id != $tce->rewriteSlugs['event'] && $tribe_id != $tce->rewriteSlugs['venue'] && $tribe_id != $tce->rewriteSlugs['organizer'] ) {
				// valid route
				$context = $tce->getContext( 'edit', $tribe_id );
				$url = $tce->getUrl( 'edit', $tribe_id, null, $context['post_type'] );
				wp_safe_redirect( esc_url_raw( $url ) ); exit;
			} else {
				// invalid route, redirect to My Events
				wp_safe_redirect( esc_url_raw( $tce->getUrl( 'list' ) ) ); exit;
			}

		}

		/**
		 * Display event editing.
		 *
		 * @param $tribe_id The event being viewed.
		 * @return string The form to display.
		 * @since 1.0

		 */
		public static function editCallback( $tribe_id ) {

			$tce = self::instance();

			$tce->isEditPage = true;
			add_filter( 'edit_post_link', [ $tce, 'removeEditPostLink' ] );

			$tce->removeFilters();

			$context = $tce->getContext( 'edit', $tribe_id );
			$tce->default_template_compatibility();

			if ( ! isset( $context['post_type'] ) ) {
				return __( 'Not found.', 'tribe-events-community' );
			}

			if ( $context['post_type'] == Tribe__Events__Main::VENUE_POST_TYPE ) {
				return $tce->doVenueForm( $tribe_id );
			}

			if ( $context['post_type'] == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
				return $tce->doOrganizerForm( $tribe_id );
			}

			if ( $context['post_type'] == Tribe__Events__Main::POSTTYPE ) {
				return $tce->doEventForm( $tribe_id );
			}
		}

		/**
		 * Display event deletion.
		 *
		 * @param int $tribe_event_id The event id.
		 * @return void
		 *
		 * @since 1.0
		 */
		public static function deleteCallback( $tribe_event_id ) {

			$tce = self::instance();
			$tce->removeFilters();
			echo $tce->doDelete( $tribe_event_id );
		}

		/**
		 * Display event adding.
		 *
		 * @return void
		 *
		 * @since 1.0
		 */
		public static function addCallback() {

			$tce = self::instance();

			$tce->isEditPage = true;
			add_filter( 'edit_post_link', [ $tce, 'removeEditPostLink' ] );

			$tce->removeFilters();
			$tce->default_template_compatibility();
			echo $tce->doEventForm();
		}

		/**
		 * Display event listings.
		 *
		 * @param string $page
		 * @return void
		 *
		 * @since 1.0
		 */
		public static function listCallback( $page = 1 ) {

			$tce = self::instance();

			$tce->isMyEvents = true;
			add_filter( 'edit_post_link', [ $tce, 'removeEditPostLink' ] );
			$tce->removeFilters();
			echo $tce->doMyEvents( $page );
		}

		/**
		 * Determine whether to redirect a user back to his events.
		 *
		 * @return void
		 *
		 * @since 1.0
		 */
		public function maybeRedirectMyEvents() {

			if ( ! is_admin() ) {
				//redirect my events with no args to todays page
				global $paged;
				if ( empty( $paged ) && isset( $_GET['tribe_action'] ) && $_GET['tribe_action'] == 'list' ) {
					$paged = 1;
					wp_safe_redirect( esc_url_raw( $this->getUrl( 'list', null, $paged ) ) ); exit;
				}
			}
		}

		/**
		 * Check if we're on the page specified with [tribe_community_events].
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @return bool
		 *
		 * @since 1.0
		 */
		public function isTcePage() {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			if ( is_404() )
				return false;

			$page_id = $this->get_community_page_id();
			if ( empty ( $page_id ) )
				return false;

			return get_the_ID() == $page_id;
		}

		/**
		 * Take care of ugly URLs.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function redirectUglyUrls() {

			if ( ! is_admin() ) {
				// redirect ugly link URLs to pretty permalinks
				if ( isset( $_GET['tribe_action'] ) ) {
					if ( isset( $_GET['paged'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], null, $_GET['paged'] );
					} elseif ( isset( $_GET['tribe_id'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], $_GET['tribe_id'] );
					} else {
						$url = $this->getUrl( $_GET['tribe_action'] );
					}
				}

				if ( isset( $url ) ) {
					wp_safe_redirect( esc_url_raw( $url ) ); exit;
				}
			}

		}

		/**
		 * Returns a filterable page title for the "Submit" page.
		 *
		 * @since 4.5.11
		 *
		 * @return string
		 */
		public function ugly_urls_events_page_title() {
			/**
			 * Allows for filtering the "Submit" page's title.
			 *
			 * @since 4.5.11
			 *
			 * @param string $title
			 */
			return apply_filters( 'tribe_ce_submit_event_page_title', __( 'Submit an Event', 'tribe-events-community' ) );
		}

		/**
		 * Outputs the notice about pretty permalinks.
		 *
		 * @since 1.0.3
		 * @since 4.6.7 Added link to Permalinks settings page.
		 */
		public function notice_permalinks() {
			?>
			<div class="error"><p>
					<?php _ex(
						sprintf(
							'Community Events requires non-default (pretty) Permalinks to be enabled or the %1$s shortcode to exist on a post or page. Please <a href="%2$s">enable pretty Permalinks</a>.',
							'[tribe_community_events]',
							esc_url( trailingslashit( get_admin_url() ) . 'options-permalink.php' )
						),
						'Pretty permalinks admin notice',
						'tribe-events-community'
					); ?>
				</p></div>
			<?php
		}

		/**
		 * Get the URL for a specific action.
		 *
		 * @param string $action    The action being performed.
		 * @param int    $id        The id of whatever is being done, if applicable.
		 * @param string $page      The page being used.
		 * @param string $post_type The post type being used.
		 *
		 * @return string The url.
		 *
		 * @since 1.0
		 */
		public function getUrl( $action, $id = null, $page = null, $post_type = null ) {

			if ( ! empty( $id ) && $action == 'edit' && function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $id ) ) {

				if ( $parent = wp_get_post_parent_id( $id ) ) {
					$id = $parent;
				}
			}

			if ( '' == get_option( 'permalink_structure' ) ) {
				add_action( 'admin_notices', [ $this, 'notice_permalinks' ] );
			} else {
				if ( ! empty ( $id ) ) {
					if ( $post_type ) {
						if ( $post_type == Tribe__Events__Main::POSTTYPE ) {
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['event'] . '/' . $id . '/';
						}

						if ( $post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['organizer'] . '/' . $id . '/';
						}

						if ( $post_type == Tribe__Events__Main::VENUE_POST_TYPE ) {
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['venue'] . '/' . $id . '/';
						}
					} else {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $id . '/';
					}
				} else {
					if ( $page ) {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/page/' . $page . '/';
					} else {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ];
					}
				}
			}
		}

		/**
		 * Gets the rewrite slug for community pages
		 * @since 1.0.6
		 *
		 * @return string rewrite slug
		 */
		public function getCommunityRewriteSlug() {
			$tec = Tribe__Events__Main::instance();
			$events_slug = $tec->getRewriteSlug();
			return $events_slug . '/' . $this->communityRewriteSlug;
		}

		/**
		 * Get delete button for an event.
		 *
		 * @param object $event The event to get the button for.
		 * @return string The button's output.
		 *
		 * @since 1.0
		 */
		public function getDeleteButton( $event ) {

			if ( ! $this->allowUsersToDeleteSubmissions ) {
				$output = '';
				return $output;
			}
			$label = __( 'Delete', 'tribe-events-community' );
			if ( class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $event->ID ) ) {
				if ( empty( $event->post_parent ) ) {
					$label = __( 'Delete All', 'tribe-events-community' );
					$message = __( 'Are you sure you want to permanently delete all instances of this recurring event?', 'tribe-events-community' );
				} else {
					$message = __( 'Are you sure you want to permanently delete this instance of a recurring event?', 'tribe-events-community' );
				}
			}

			$output  = ' <span class="delete wp-admin events-cal">| <a rel="nofollow" class="submitdelete" href="';
			$output .= esc_url( wp_nonce_url( $this->getUrl( 'delete', $event->ID ), 'tribe_community_events_delete' ) );
			$output .= '" data-event_id="' . esc_attr( $event->ID ) . '" data-nonce="' . wp_create_nonce( 'tribe_community_events_delete' ) .'">' . $label . '</a></span>';
			return $output;
		}

		/**
		 * Get edit button for an event.
		 *
		 * @param object $event The event object.
		 * @param string $label The label for the button.
		 * @param string $before What comes before the button.
		 * @param string $after What comes after the button.
		 * @return string $output The button's output.
		 *
		 * @since 1.0
		 */
		public function getEditButton( $event, $label = 'Edit', $before = '', $after = '' ) {
			if ( ! isset( $event->EventStartDate ) ) {
				$event->EventStartDate = tribe_get_event_meta( $event->ID, '_EventStartDate', true );
			}

			$output  = $before . '<a rel="nofollow" href="';
			$output .= esc_url( $this->getUrl( 'edit', $event->ID, null, Tribe__Events__Main::POSTTYPE ) );
			$output .= '"> ' . $label . '</a>' . $after;
			return $output;

		}

		/**
		 * Get the featured image delete button.
		 *
		 * @param object $event The event id.
		 * @return string The button's output.
		 * @author Paul Hughes
		 * @since 1.0
		 */
		public function getDeleteFeaturedImageButton( $event = null ) {
			if ( ! isset( $event ) ) {
				$event = get_post();
			}

			if ( ! has_post_thumbnail( $event->ID ) ) {
				return '';
			}

			$url = add_query_arg( 'action', 'deleteFeaturedImage', wp_nonce_url( $this->getUrl( 'edit', $event->ID, null, Tribe__Events__Main::POSTTYPE ), 'tribe_community_events_featured_image_delete' ) );

			if ( class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $event->ID ) ) {
				$url = add_query_arg( 'eventDate', date( 'Y-m-d', strtotime( $event->EventStartDate ) ), $url );
			}

			$output = '<a rel="nofollow" class="submitdelete" href="' . esc_url( $url ) . '">' . esc_html__( 'Remove image', 'tribe-events-community' ) . '</a>';
			return $output;
		}

		/**
		 * Get title for a page.
		 *
		 * @param string $action The action being performed.
		 * @param string $post_type The post type being viewed.
		 * @return string The title.
		 * @since 1.0
		 */
		public function getTitle( $action, $post_type ) {
			$i18n['delete'] = [
				Tribe__Events__Main::POSTTYPE => __( 'Remove an Event', 'tribe-events-community' ),
				Tribe__Events__Main::VENUE_POST_TYPE => __( 'Remove a Venue', 'tribe-events-community' ),
				Tribe__Events__Main::ORGANIZER_POST_TYPE => __( 'Remove an Organizer', 'tribe-events-community' ),
				'unknown' => __( 'Unknown Post Type', 'tribe-events-community' ),
			];

			$i18n['default'] = [
				Tribe__Events__Main::POSTTYPE => __( 'Edit an Event', 'tribe-events-community' ),
				Tribe__Events__Main::VENUE_POST_TYPE => __( 'Edit a Venue', 'tribe-events-community' ),
				Tribe__Events__Main::ORGANIZER_POST_TYPE => __( 'Edit an Organizer', 'tribe-events-community' ),
				'unknown' => __( 'Unknown Post Type', 'tribe-events-community' ),
			];

			if ( empty( $action ) || 'delete' !== $action ) {
				$action = 'default';
			}

			/**
			 * Allow users to hook and change the Page Title for all the existing pages.
			 * Don't remove the 'unknown' key from the array
			 */
			$i18n = apply_filters( 'tribe_ce_i18n_page_titles', $i18n, $action, $post_type );

			if ( ! empty( $i18n[ $action ][ $post_type ] ) ){
				return $i18n[ $action ][ $post_type ];
			} else {
				return $i18n[ $action ]['unknown'];
			}
		}

		/**
		 * Set context for where we are.
		 *
		 * @param string $action The current action.
		 * @param string $post_type The current post type.
		 * @param int $id The current id.
		 * @return void
		 *
		 * @since 1.0
		 */
		private function setContext( $action, $post_type, $id ) {

			$this->context = [
				'title' => $this->getTitle( $action, $post_type ),
				'post_type' => $post_type,
				'action' => $action,
				'id' => $id,
			];

		}

		/**
		 * Get context for where we are.
		 *
		 * @since 1.0
		 *
		 * @param string $action   The current action.
		 * @param int    $tribe_id The current post id.
		 *
		 * @return array The current context.
		 */
		public function getContext( $action = null, $tribe_id = null ) {

			// get context from query string
			if ( isset( $_GET['tribe_action'] ) )
			 $action = $_GET['tribe_action'];

			if ( isset( $_GET['tribe_id'] ) )
			 $tribe_id = intval( $_GET['tribe_id'] );

			$tribe_id = intval( $tribe_id );

			if ( isset( $this->context ) )
				return $this->context;

			switch ( $action ) {
				case 'edit':
					$context = [
						'title' => 'Test',
						'action' => $action,
					];

					if ( $tribe_id ) {
						$post = get_post( $tribe_id );
						if ( is_object( $post ) ) {
							$context = [
								'title' => $this->getTitle( $action, $post->post_type ),
								'action' => $action,
								'post_type' => $post->post_type,
								'id' => $tribe_id,
							];
						}
					}

				break;

				case 'list':
					$context = [
						'title' => apply_filters( 'tribe_ce_event_list_page_title', __( 'My Events', 'tribe-events-community' ) ),
						'action' => $action,
						'id' => null,
					];
				break;

				case 'delete':

					if ( $tribe_id )
						$post = get_post( $tribe_id );

					$context = [
						'title' => $this->getTitle( $action, $post->post_type ),
						'post_type' => $post->post_type,
						'action' => $action,
						'id' => $tribe_id,
					];

				break;

				default:
					$title = __( 'Submit an Event', 'tribe-events-community' );
					$title = apply_filters( 'tribe_events_community_submit_event_page_title', $title );
					$title = apply_filters_deprecated(
						'tribe_ce_submit_event_page_title',
						[ $title ],
						'4.6.3',
						'tribe_events_community_submit_event_page_title',
						'The filter "tribe_ce_submit_event_page_title" has been renamed to "tribe_events_community_submit_event_page_title" to match plugin namespacing.'
					);
					$context = [
						'title'  => $title,
						'action' => 'add',
						'id'     => null,
					];
			}

			$this->context = $context;

			return $context;
		}

		/**
		 * Set the title for the shortcode.
		 *
		 * @since 1.0
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @return string The title
		 */
		public function doShortCodeTitle() {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );

			return $context['title'];
		}

		/**
		 * Output the shortcode's content based on the content.
		 *
		 * @since 1.0
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @return string The shortcode's content.
		 */
		public function doShortCode() {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			if ( ! is_page() || ! in_the_loop() || tribe_is_event() )
				return '<p>' . esc_html__( 'This shortcode can only be used in pages.', 'tribe-events-community' ) . '</p>';

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );
			$this->maybeLoadAssets( true );
			$this->default_template_compatibility();

			switch ( $context['action'] ) {

				case 'edit':

					if ( $context['post_type'] == Tribe__Events__Main::VENUE_POST_TYPE ) {
						return $this->doVenueForm( $context['id'] );
					}

					if ( $context['post_type'] == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
						return $this->doOrganizerForm( $context['id'] );
					}

					if ( $context['post_type'] == Tribe__Events__Main::POSTTYPE ) {
						return $this->doEventForm( $context['id'] );
					}

				break;

				case 'list':

					return $this->doMyEvents( null, true );

				break;

				case 'delete':

					return $this->doDelete( $context['id'] );

				break;

				case 'add':
				default:

					return $this->doEventForm();
			}
		}

		/**
		 * Unhook content filters from the content.
		 *
		 * @return void
		 *
		 * @since 1.0
		 */
		public function removeFilters() {
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );
		}

		/**
		 * Set the body classes.
		 *
		 * @param array $classes The current array of body classes.
		 * @return array The body classes to add.
		 * @since 1.0.1
		 * @author Paul Hughes
		 */
		public function setBodyClasses( $classes ) {
			$is_community_page = false;

			if ( tribe_is_community_my_events_page() ) {
				$classes[] = 'tribe_community_list';
				 $is_community_page = true;
			}

			if ( tribe_is_community_edit_event_page() ) {
				$classes[] = 'tribe_community_edit';
				 $is_community_page = true;
			}

			if ( $is_community_page ) {
				$classes = $this->theme_compatibility_body_class_changes( $classes );
			}

			return $classes;
		}

		/**
		 * Alters the body classes specifically for theme compatibility purposes.
		 *
		 * @param array $classes
		 *
		 * @return array
		 */
		protected function theme_compatibility_body_class_changes( $classes ) {
			$child_theme  = get_option( 'stylesheet' );
			$parent_theme = get_option( 'template' );

			if ( 'twentyseventeen' === $child_theme || 'twentyseventeen' === $parent_theme ) {
				$has_sidebar = array_search( 'has-sidebar', $classes );

				if ( $has_sidebar ) {
					unset( $classes[ $has_sidebar ] );
				}
			}

			return $classes;
		}

		/**
		 * Upon page save, flush the transient for the page-id.
		 *
		 * @param int $post_id The current post id.
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.5
		 */
		public function flushPageIdTransient( $post_id ) {
			if ( get_post_type( $post_id ) == 'page' ) {
				delete_transient( 'tribe-community-events-page-id' );
			}
		}

		/**
		 * Adds the event specific query vars to WordPress.
		 *
		 * @since 1.0
		 * @link  http://codex.wordpress.org/Custom_Queries#Permalinks_for_Custom_Archives
		 *
		 * @param array $qvars Array of query variables.
		 *
		 * @return array Filtered array of query variables.
		 *
		 */
		public function communityEventQueryVars( $qvars ) {
			$qvars[] = 'tribe_event_id';
			$qvars[] = 'tribe_venue_id';
			$qvars[] = 'tribe_organizer_id';
			return $qvars;
		}

		protected function create_event_object_from_submission( $submission ) {
			return (object) $submission;
		}

		/**
		 * Send email alert to email list when an event is submitted.
		 *
		 * @param int $tribe_event_id The event ID.
		 * @return boolean
		 *
		 * @since 1.0
		 */
		public function sendEmailAlerts( $tribe_event_id ) {
			$post         = get_post( intval( $tribe_event_id ) );
			$already_sent = get_post_meta( $tribe_event_id, self::$submission_email_sent_meta_key, true );

			if ( tribe_is_truthy( $already_sent ) ) {
				return false;
			}

			$subject = sprintf( '[%s] ' . __( 'Community Events Submission', 'tribe-events-community' ) . ':', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) . ' "' . $post->post_title . '"';

			// Get Message HTML from Email Template
			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/email-template' );

			$message = ob_get_clean();
			$headers  = [ 'Content-Type: text/html' ];
			$h        = implode( "\r\n", $headers ) . "\r\n";

			if ( ! is_array( $this->emailAlertsList ) ) {
				return false;
			}

			$sent_all = true;

			foreach ( $this->emailAlertsList as $email ) {
				$sent_one = wp_mail( trim( $email ), $subject, $message, $h );

				if ( ! $sent_one ) {
					$sent_all = false;
				}
			}

			update_post_meta( $tribe_event_id, self::$submission_email_sent_meta_key, 'yes' );

			return $sent_all;
		}

		/**
		 * Searches current user's events for the event closest to
		 * today but not in the past, and returns the 'page' that event is on.
		 *
		 * @return object The page object.
		 *
		 * @since 1.0
		 */
		public function findTodaysPage() {

			if ( WP_DEBUG ) delete_transient( 'tribe_community_events_today_page' );
			$todaysPage = get_transient( 'tribe_community_events_today_page' );

			$todaysPage = null;

			if ( ! $todaysPage ) {
				$current_user = wp_get_current_user();
				if ( is_object( $current_user ) && ! empty( $current_user->ID ) ) {
					$args = [
						'posts_per_page' => -1,
						'paged' => 0,
						'nopaging' => true,
						'author' => $current_user->ID,
						'post_type' => Tribe__Events__Main::POSTTYPE,
						'post_status' => 'any',
						'order' => 'ASC',
						'orderby' => 'meta_value',
						'meta_key' => '_EventStartDate',
						'meta_query' => [
							'key' => '_EventStartDate',
							'value' => date( 'Y-m-d 00:00:00' ),
							'compare' => '<=',
						],
					];

					$tp = new WP_Query( $args );

					$pc = $tp->post_count;

					unset( $tp );

					$todaysPage = floor( $pc / $this->eventsPerPage );

					//handle bounds
					if ( $todaysPage <= 0 )
						$todaysPage = 1;

					set_transient( 'tribe-community-events_today_page', $todaysPage, 60 * 60 * 1 ); //cache for an hour
				}
			}

			return $todaysPage;

		}

		/** */
		public function ajaxDoDelete() {
			$permission = check_ajax_referer( 'tribe_community_events_delete', 'nonce', false );
			if ( $permission == false ) {
				wp_send_json_error(  __( 'You do not have permission to delete this event.', 'tribe-events-community' ) );
				wp_die();
			}

			$event_id = absint( $_REQUEST[ 'id' ] );
			$event    = get_post( $event_id );

			$message = '';
			$error = false;

			if ( isset( $event->ID ) ) {
				if ( $this->trashItemsVsDelete ) {
					if ( wp_trash_post( $event_id ) ) {
						$message = __( 'Trashed Event: ', 'tribe-events-community' ) . $event->post_title;
					} else {
						$error = true;
						$message = __( 'There was an error trashing your event: ', 'tribe-events-community' ) . $event->post_title;
					}

				} else {
					if ( wp_delete_post( $event_id, true ) ) {
						$message = __( 'Deleted Event: ', 'tribe-events-community' ) . $event->post_title;
					} else {
						$error = true;
						$message = __( 'There was an error deleting your event: ', 'tribe-events-community' ) . $event->post_title;
					}
				}
			} else {
				$error = true;
				$message = __( 'This event does not appear to exist.', 'tribe-events-community' );
			}

			if ( $error ) {
				wp_send_json_error( $message );
			} else {
				wp_send_json_success( $message );
			}

			wp_die();
		}

		/**
		 * Delete view for an event.
		 *
		 * @param int $tribe_event_id The event's ID.
		 * @return string The deletion view.
		 *
		 * @since 1.0
		 */
		public function doDelete( $tribe_event_id ) {
			$this->default_template_compatibility();

			if ( wp_verify_nonce( $_GET['_wpnonce'], 'tribe_community_events_delete' ) && current_user_can( 'delete_post', $tribe_event_id ) ) {
				//does this event even exist?
				$event = get_post( $tribe_event_id );

				if ( isset( $event->ID ) ) {
					if ( $this->trashItemsVsDelete ) {
						wp_trash_post( $tribe_event_id );
						$this->enqueueOutputMessage( __( 'Trashed Event #', 'tribe-events-community' ) . $tribe_event_id );
					} else {
						wp_delete_post( $tribe_event_id, true );
						$this->enqueueOutputMessage( __( 'Deleted Event #', 'tribe-events-community' ) . $tribe_event_id );
					}
				} else {
					$this->enqueueOutputMessage( sprintf( __( 'This event (#%s) does not appear to exist.', 'tribe-events-community' ), $tribe_event_id ) );
				}
			} else {
				$this->enqueueOutputMessage( __( 'You do not have permission to delete this event.', 'tribe-events-community' ) );
			}

			$output = '<div id="tribe-community-events" class="delete">';

			ob_start();
			$this->enqueue_assets();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/delete' );
			$output .= ob_get_clean();

			/**
			 * Sets the URL normally used to take users back to the main Community Events list view.
			 *
			 * @param string $back_url
			 */
			$back_url = apply_filters( 'tribe_events_community_deleted_event_back_url', tribe( 'community.main' )->getUrl( 'list' ) );
			$output .= '<a href="' . esc_url( $back_url ) . '">&laquo; ' . _x( 'Back', 'As in "go back to previous page"', 'tribe-events-community' ) . '</a>';

			$output .= '</div>';

			return $output;

		}

		/**
		 * Event editing form.
		 *
		 * @since 1.0
		 *
		 * @param int $id the event's ID.
		 * @param boolean $shortcode.
		 *
		 * @return string The editing view markup.
		 *
		 */
		public function doEventForm( $id = null, $shortcode = false ) {

			/**
			 * Allow the user to add content or functions above the submission page.
			 *
			 * @since 1.0
			 *
			 * @param int $id The event's ID.
			 */
			do_action( 'tribe_community_before_event_page', $id );

			// Get various forms of the word "Event".
			$events_label_singular           = tribe_get_event_label_singular();
			$events_label_plural             = tribe_get_event_label_plural();
			$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

			$output    = '';
			$show_form = true;
			$event     = null;

			if ( $id ) {
				$edit           = true;
				$tribe_event_id = $id = intval( $id );
			} else {
				$edit           = false;
				$tribe_event_id = null;
			}

			if ( $tribe_event_id && class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $tribe_event_id ) ) {
				$this->enqueueOutputMessage( sprintf( __( '%sWarning:%s You are editing a recurring %s. All changes will be applied to the entire series.', 'tribe-events-community' ), '<b>', '</b>', $events_label_singular_lowercase ), 'error' );
			}

			if ( $edit && $tribe_event_id ) {
				$event = get_post( intval( $tribe_event_id ) );
			}

			// TODO: Not entirely sure this check is necessary. -- jbrinley
			if ( $edit && ( ! $tribe_event_id || ! isset( $event->ID ) ) ) {
				$this->enqueueOutputMessage( sprintf( __( '%s not found.', 'tribe-events-community' ), $events_label_singular ), 'error' );
				$output    = $this->outputMessage( null, false );
				$show_form = false;
			}

			// login check
			if ( ( ! $this->allowAnonymousSubmissions && ! is_user_logged_in() ) || ( $edit && $tribe_event_id && ! is_user_logged_in() ) ) {
				do_action( 'tribe_events_community_event_submission_login_form' );
				do_action_deprecated(
					'tribe_ce_event_submission_login_form',
					[],
					'4.6.3',
					'tribe_events_community_event_submission_login_form',
					'The action "tribe_ce_event_submission_login_form" has been renamed to "tribe_events_community_event_submission_login_form" to match plugin namespacing.'
				);

				$output .= $this->login_form( __( 'Please log in first.', 'tribe-events-community' ) );
				return $output;
			}

			// security check
			if ( $edit && $tribe_event_id && ! current_user_can( 'edit_post', $tribe_event_id ) ) {
				$output .= '<p>' . sprintf( __( 'You do not have permission to edit this %s.', 'tribe-events-community' ), $events_label_singular_lowercase ) . '</p>';
				return $output;
			}

			// file upload check
			if ( $this->max_file_size_exceeded() ) {
				$this->enqueueOutputMessage( sprintf( __( 'The file you attempted to upload exceeded the maximum file size of %1$s.', 'tribe-events-community' ), size_format( $this->max_file_size_allowed() ) ), 'error' );
			}

			$this->loadScripts = true;

			/**
			 * Allow the user to add content or functions above the submission form.
			 *
			 * @since 1.0
			 */
			do_action( 'tribe_events_community_before_event_submission_page' );
			do_action_deprecated(
				'tribe_ce_before_event_submission_page',
				[],
				'4.6.3',
				'tribe_events_community_before_event_submission_page',
				'The action "tribe_ce_before_event_submission_page" has been renamed to "tribe_events_community_before_event_submission_page" to match plugin namespacing.'
			);

			$output .= '<div id="tribe-community-events" class="tribe-community-events form">';

			if ( $this->allowAnonymousSubmissions || is_user_logged_in() ) {
				$errors     = [];
				$submission = $this->get_submitted_event();

				if ( ! empty( $submission ) ) {
					if ( isset( $submission['post_ID'] ) ) {
						$tribe_event_id = absint( $submission['post_ID'] );
					}//end if

					$handler = new Tribe__Events__Community__Submission_Handler( $submission, $tribe_event_id );

					if ( $handler->validate() ) {

						add_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

						// Modify submit url only on the shortcode submission view.
						if ( ! empty( $submission['community-shortcode-type'] ) && 'submission_form' === $submission['community-shortcode-type'] ) {

							 // Modify the default submission $link on Community Events form.
							add_filter( 'tribe_events_community_submission_url', [ tribe( 'community.shortcodes' ), 'custom_nav_link' ] );
						}

						$tribe_event_id = $handler->save();

						remove_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

						delete_transient( 'tribe_community_events_today_page' ); //clear cache

						if ( $tribe_event_id ) {
							// email alerts
							if ( $this->emailAlertsEnabled ) {
								$this->sendEmailAlerts( $tribe_event_id );
							}
						} else {
							// This is only to prevent bad images
							$event = $this->create_event_object_from_submission( $handler->get_submission() );
						}
					} else {
						$event  = $this->create_event_object_from_submission( $handler->get_submission() );
						$errors = $handler->get_invalid_fields();
					}

					$messages   = $handler->get_messages();
					$has_errors = in_array( 'error', wp_list_pluck( $messages, 'type' ) );

					foreach ( $messages as $m ) {
						if ( $has_errors && 'error' !== $m->type ) {
							continue;
						}
						$this->enqueueOutputMessage( $m->message, $m->type );
					}
				}

				if ( isset( $tribe_event_id ) && $edit ) {
					$event = get_post( intval( $tribe_event_id ) );
				} elseif ( empty( $event ) ) {
					$event = new stdClass();
				}

				$GLOBALS['post'] = $event;

				/**
				 * Allow the user to override the default "show form" logic.
				 *
				 * @since 1.0
				 *
				 * @param bool $show_form
				 */
				$show_form = apply_filters( 'tribe_community_events_show_form', $show_form );

				if ( $show_form ) {
					$tec_template = tribe_get_option( 'tribeEventsTemplate' );

					if ( ! empty( $tec_template ) ) {
						ob_start();
						tribe_events_before_html();
						$output .= ob_get_clean();
					}

					/**
					 * Allow the user to add content or functions right before the submission template is loaded.
					 *
					 * @since 1.0
					 */
					do_action( 'tribe_events_community_before_event_submission_page_template' );
					do_action_deprecated(
						'tribe_ce_before_event_submission_page_template',
						[],
						'4.6.3',
						'tribe_events_community_before_event_submission_page_template',
						'The action "tribe_ce_before_event_submission_page_template" has been renamed to "tribe_events_community_before_event_submission_page_template" to match plugin namespacing.'
					);

					if ( empty( $submission ) || $this->messageType == 'error' ) {
						$required = $this->required_fields_for_submission();
						$this->event_form()->set_event( $event );
						$this->event_form()->set_required_fields( $required );
						$this->event_form()->set_error_fields( $errors );
						$output .= $this->event_form()->render();
					} else {
						ob_start();
						include Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/header-links' );
						if ( $shortcode ) {
							$this->outputMessage();
						}
						$output .= ob_get_clean();
					}

					if ( ! empty( $tec_template ) ) {
						ob_start();
						tribe_events_after_html();
						$output .= ob_get_clean();
					}
				}
			}
			$output .= '</div>';

			wp_reset_query();

			return $output;
		}

		/**
		 * If a request comes in to delete a featured image,
		 * delete it and redirect back to the event page
		 *
		 * @see do_action('before_tribe_community_event_page')
		 * @see Tribe__Events__Community__Main::doEventForm()
		 * @param int $event_id
		 * @return void
		 */
		public function maybe_delete_featured_image( $event_id ) {
			// Delete the featured image, if there was a request to do so.
			if ( $event_id && isset( $_GET['action'] ) && $_GET['action'] == 'deleteFeaturedImage' && wp_verify_nonce( $_GET['_wpnonce'], 'tribe_community_events_featured_image_delete' ) && current_user_can( 'edit_post', $event_id ) ) {
				$featured_image_id = get_post_thumbnail_id( $event_id );
				if ( $featured_image_id ) {
					delete_post_meta( $event_id, '_thumbnail_id' );
					$image_parent = wp_get_post_parent_id( $featured_image_id );
					if ( $image_parent == $event_id ) {
						wp_delete_attachment( $featured_image_id, true );
					}
				}
				$redirect = $_SERVER['REQUEST_URI'];
				$redirect = remove_query_arg( '_wpnonce', $redirect );
				$redirect = remove_query_arg( 'action', $redirect );
				wp_safe_redirect( esc_url_raw( $redirect ), 302 );
				exit();
			}
		}

		/**
		 * Get the View/Edit link for the post
		 * @since 3.7
		 *
		 * @param int $event_id post ID of event
		 * @return string HTML link
		 */
		public function get_view_edit_links( $event_id ) {
			$edit_link = $view_link = '';

			if ( get_post_status( $event_id ) == 'publish' ) {
				$view_link = sprintf( '<a href="%s" class="view-event">%s</a>',
					esc_url( get_permalink( $event_id ) ),
					__( 'View', 'tribe-events-community' ) );
			}

			if ( current_user_can( 'edit_post', $event_id ) ) {
				$edit_link = sprintf( '<a href="%s" class="edit-event">%s</a>',
					esc_url( tribe_community_events_edit_event_link( $event_id ) ),
					__( 'Edit', 'tribe-events-community' )
				);
			}

			// If the user isn't allowed to edit and the post wasn't published, return an empty string
			if ( empty( $edit_link ) && empty( $view_link ) ) {
				return '';
			}

			$separator = '<span class="sep"> | </span>';
			return '(' . tribe_separated_field( $view_link, $separator, $edit_link ) . ')';
		}

		/**
		 * Check for and return submitted event
		 * @since 3.3
		 *
		 * @return array event array or empty array if not a CE submitted event
		 */
		private function get_submitted_event() {
			if ( empty( $_POST[ 'community-event' ] ) ) {
				return [];
			}

			if ( ! check_admin_referer( 'ecp_event_submission' ) ) {
				return [];
			}
			$submission = $_POST;

			return $submission;
		}

		/**
		 * Returns an array of fields required for submission.
		 *
		 * @since 3.3
		 *
		 * @return array required fields
		 */
		public function required_fields_for_submission() {
			$required_fields = [
				'post_content',
				'post_title',
			];

			$terms_enabled     = $this->getOption( 'termsEnabled' );
			$terms_description = $this->getOption( 'termsDescription' );

			if ( tribe_is_truthy( $terms_enabled ) && ! empty( $terms_description ) ) {
				$required_fields[] = 'terms';
			}

			/**
			 * Required Community Event Fields
			 *
			 * @param array $required_fields An array of required fields (case sensitive) from:
			 *        post_title, post_content, EventStartDate, EventStartTime, EventEndDate,
			 *        EventEndTime, EventCurrencySymbol, tax_input (for Event Categories), venue,
			 *        organizer, EventShowMapLink, EventURL, is_recurring,
			 *        event_image (for Event Featured Image)
			 */
			return apply_filters( 'tribe_events_community_required_fields', $required_fields );
		}

		/**
		 * Required Community Event field groups.
		 *
		 * Groups are related set of required fields, a group will be marked as "required"
		 * if even one of its fields is marked as required (logic OR).
		 * Groups are not used to validate the submission, like single fields are, but
		 * to mark a whole group as required in the display logic.
		 *
		 * @return array An array of groups required for the submission.
		 */
		public function required_field_groups_for_submission() {
			$groups = [
				'taxonomy'       => [ 'tax_input' ],
				'featured_image' => [ 'event_image' ],
				'date_time'      => [
					'EventStartDate',
					'EventStartTime',
					'EventEndDate',
					'EventEndTime',
				],
			];

			/**
			 * Filter the required groups.
			 *
			 * A group will be marked as "required" if at least one of its fields is required.
			 *
			 * @param array $groups   An associative array of field groups in the format:
			 *                        [ <group> => [ <field1>, <field2>, ... ]
			 */
			$groups = apply_filters( 'tribe_events_community_required_field_groups', $groups );

			$required_fields = $this->required_fields_for_submission();

			foreach ( $groups as $group => $group_required_fields ) {
				$check_required_fields = array_intersect( $group_required_fields, $required_fields );
				if ( empty( $check_required_fields ) ) {
					unset( $groups[ $group ] );
				}
			}

			return array_keys( $groups );
		}

		/**
		 * Outputs login form.
		 *
		 * @since 3.1
		 * @since 4.6.3 Wrapped in div.tribe-community-events
		 *
		 * @param string $caption
		 *
		 * @return string HTML login form
		 */
		public function login_form( $caption = '' ) {
			ob_start();

			echo '<div class="tribe-community-events">';

			/**
			 * Fires immediately before the login form is rendered (where Community
			 * Events requires that the user logs in).
			 */
			do_action( 'tribe_community_before_login_form' );

			echo '<p>' . esc_html( $caption ) . '</p>';

			wp_login_form( [ 'form_id' => $this->login_form_id ] );

			if ( get_option( 'users_can_register' ) ) {
				wp_register( '<div class="tribe-ce-register">', '</div>', true );
				echo ' | ';
			}

			$this->lostpassword_link();

			/**
			 * Fires immediately after the login form is rendered (where Community
			 * Events requires that the user logs in).
			 */
			do_action( 'tribe_community_after_login_form' );

			echo '</div>';

			return ob_get_clean();
		}

		/**
		 * A uniform way of generating a "Lost your password?" link on CE login forms.
		 *
		 * @since 4.5.14
		 */
		public function lostpassword_link() {
			echo sprintf(
				'<a class="tribe-ce-lostpassword" href="%1$s">%2$s</a>',
				wp_lostpassword_url(),
				esc_html__( 'Lost your password?', 'tribe-events-community' )
			);
		}

		/**
		 * Add hidden form fields to our rendering of the WordPress login form so we know when logging in is attempted
		 * within our context and so we can redirect upon successful login.
		 *
		 * @since 4.6.3
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public function add_hidden_form_fields_to_login_form( $content ) {
			if (
				$this->isEditPage
				|| $this->isMyEvents
			) {
				// Identify an attempt from our login form
				$content .= sprintf( '%1$s<input type="hidden" name="%2$s" value="1" />%1$s', PHP_EOL, $this->login_form_id );

				/**
				 * Where to redirect upon successful login from Community Events login form.
				 *
				 * Default is just the current URL without the failed query var (if exists).
				 *
				 * @since 4.6.3
				 *
				 * @param string $redirect_upon_success The URL to redirect to.
				 *
				 * @return string
				 */
				$redirect_upon_success = apply_filters( 'tribe_events_community_successful_login_redirect_to', remove_query_arg( $this->login_form_id ) );

				$content .= sprintf( '%1$s<input type="hidden" name="redirect_to" value="%2$s" />%1$s', PHP_EOL, esc_url( $redirect_upon_success ) );
			}

			return $content;
		}

		/**
		 * Filter the WordPress authentication upon login attempt to force the login error redirect to always fire.
		 *
		 * We look for WP_Error of the type that bypasses the redirect hook for certain types of failed logins.
		 * We prefix such error(s) so the redirect hook fires while not losing the error message(s). We do not use them,
		 * but another plugin may care.
		 *
		 * @since 4.6.3
		 *
		 * @see   \wp_authenticate() The array of $ignore_codes to account for.
		 *
		 * @param WP_Error|WP_User|null $user     WP_User if the user is authenticated. WP_Error or null otherwise.
		 * @param string                $username Submitted value for username.
		 * @param string                $password Submitted value for password.
		 *
		 * @return WP_Error|WP_User|null
		 */
		public function login_form_authentication( $user, $username, $password ) {
			if (
				! $user instanceof WP_Error
				|| ! tribe_is_truthy( tribe_get_request_var( $this->login_form_id ) )
			) {
				return $user;
			}

			$ignore_codes = [
				'empty_username',
				'empty_password',
			];

			if (
				empty( $user->get_error_code() )
				|| ! in_array( $user->get_error_code(), $ignore_codes )
			) {
				return $user;
			}

			foreach ( $ignore_codes as $code ) {
				$new_key = $this->login_form_id . '_' . $code;

				foreach ( $user->errors as $key => $error ) {
					if ( $code !== $key ) {
						continue;
					}

					$user->errors[ $new_key ] = $error;

					unset( $user->errors[ $key ] );
				}
			}

			return $user;
		}


		/**
		 * Keep our login form's failed attempts on the front end, adding a query parameter.
		 *
		 * @since 4.6.3
		 *
		 * @param string $username Submitted value for username.
		 */
		public function redirect_failed_login_to_front_end( $username ) {
			if (
				$this->isEditPage
				|| $this->isMyEvents
				|| tribe_is_truthy( tribe_get_request_var( $this->login_form_id ) )
			) {
				$referrer = wp_get_referer();

				if ( ! empty( $referrer ) ) {
					wp_safe_redirect( add_query_arg( $this->login_form_id, 'failed', $referrer ) );
					tribe_exit();
				}
			}
		}

		/**
		 * Add the login form notices, such as a failed login message.
		 *
		 * @since 4.6.3
		 */
		public function output_login_form_notices() {
			if ( 'failed' === tribe_get_request_var( $this->login_form_id ) ) {
				$output = '<div id="login_error" class="tribe-community-notice tribe-community-notice-error">';

				$output .= sprintf(
					_x( '%1$sERROR%2$s: Invalid username, email address, or incorrect password.', 'failed login message', 'tribe-events-community' ),
					'<strong>',
					'</strong>'
				);

				$output .= '</div>';

				echo $output;
			}
		}

		/**
		 * Main form for events.
		 *
		 * @since 1.0
		 *
		 * @param int  $tribe_venue_id The event's venue ID.
		 *
		 * @return string The form.
		 */
		public function doVenueForm( $tribe_venue_id ) {
			$tribe_venue_id = intval( $tribe_venue_id );

			$output = '';

			add_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

			if ( empty( $tribe_venue_id ) ) {
				$output .= '<p>' . __( 'Venue not found.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			if ( ! is_user_logged_in() ) {
				return $this->login_form( __( 'Please log in to edit this venue', 'tribe-events-community' ) );
			}

			if ( ! current_user_can( 'edit_post', $tribe_venue_id ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this venue.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form venue">';

			if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_venue_submission' ) ) {
				if ( isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] ) {
					$_POST['ID'] = $tribe_venue_id;
					$scrubber = new Tribe__Events__Community__Venue_Submission_Scrubber( $_POST );
					$_POST = $scrubber->scrub();

					remove_action(
						'save_post_' . Tribe__Events__Main::VENUE_POST_TYPE,
						[ Tribe__Events__Main::instance(), 'save_venue_data' ],
						16,
						2
					);

					wp_update_post( [
						'post_title' => $_POST[ 'post_title' ],
						'ID' => $tribe_venue_id,
						'post_content' => $_POST[ 'post_content' ],
					] );

					Tribe__Events__API::updateVenue( $tribe_venue_id, $_POST['venue'] );

					$this->enqueueOutputMessage( __( 'Venue updated.', 'tribe-events-community' ) );
						/*
						// how it should work, but updateVenue does not return a boolean
						if ( Tribe__Events__API::updateVenue($tribe_venue_id, $_POST) ) {
						$this->enqueueOutputMessage( __("Venue updated.",'tribe-events-community') );
						}else{
						$this->enqueueOutputMessage( __("There was a problem saving your venue, please try again.",'tribe-events-community'), 'error' );
						}
						*/
				} else {
					$this->enqueueOutputMessage( __( 'Venue name cannot be blank.', 'tribe-events-community' ), 'error' );
				}
			} else {
				if ( isset( $_POST[ 'community-event' ] ) ) {
					$this->enqueueOutputMessage( __( 'There was a problem updating your venue, please try again.', 'tribe-events-community' ), 'error' );
				}
			}

			global $post;
			$post = get_post( intval( $tribe_venue_id ) );

			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/edit-venue' );

			$output .= ob_get_clean();

			wp_reset_query();

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

			return $output;
		}

		/**
		 * The front-end "edit organizer" form.
		 *
		 * @since 1.0
		 *
		 * @param int $organizer_id The organizer's ID.
		 * @return string The form.
		 */
		public function doOrganizerForm( $organizer_id ) {
			$valid        = false;
			$organizer_id = intval( $organizer_id );

			add_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

			// Some preliminary checks to ensure editing the organizer is allowed.
			if ( ! $organizer_id ) {
				return '<p>' . esc_html__( 'Organizer not found.', 'tribe-events-community' ) . '</p>';
			}

			if ( Tribe__Events__Main::ORGANIZER_POST_TYPE !== get_post_type( $organizer_id ) ) {
				return '<p>' . esc_html__( 'Only an Organizer can be edited on this page.', 'tribe-events-community' ) . '</p>';
			}

			if ( ! is_user_logged_in() ) {
				return $this->login_form( __( 'Please log in to edit this organizer', 'tribe-events-community' ) );
			}

			if ( ! current_user_can( 'edit_post', $organizer_id ) ) {
				return '<p>' . esc_html__( 'You do not have permission to edit this organizer.', 'tribe-events-community' ) . '</p>';
			}

			/**
			 * Filter Community Events Required Organizer Fields
			 *
			 * @param array of fields to validate - Organizer, Phone, Website, Email
			 */
			$required_organizer_fields = apply_filters( 'tribe_events_community_required_organizer_fields', [] );

			// Begin the actual edit-organizer form now that user is confirmed to be allowed to be here.
			$this->loadScripts  = true;
			$output             = '<div id="tribe-community-events" class="form organizer">';

			if ( Tribe__Utils__Array::get( $_POST, 'community-event', false ) ) {

				if ( ! check_admin_referer( 'ecp_organizer_submission' ) ) {
					$this->enqueueOutputMessage( esc_html__( 'There was a problem updating this organizer, please try again.', 'tribe-events-community' ), 'error' );
				}

				if ( ! isset( $_POST[ 'post_title' ] ) ) {
					$this->enqueueOutputMessage( esc_html__( 'Organizer name cannot be blank.', 'tribe-events-community' ), 'error' );
				}

				$_POST['ID']             = $organizer_id;
				$scrubber                = new Tribe__Events__Community__Organizer_Submission_Scrubber( $_POST );
				$_POST                   = $scrubber->scrub();
				$invalid_fields          = [];
				$has_all_required_fields = true;

				foreach ( $required_organizer_fields as $field ) {

					// This array of required fields is shared with the submission form, on which the existence of an Organizer is not a given.
					// Here on the edit-organizer form, though, it *is* a given, so we can skip checking the parent 'Organizer' field to prevent unnecessary messages about it.
					if ( 'Organizer' === $field ) {
						continue;
					}

					$required_field = Tribe__Utils__Array::get( $_POST, [ 'organizer', $field ], '' );

					if ( empty( $required_field ) ) {
						$this->enqueueOutputMessage( sprintf( esc_html__( '%1$s required', '"{Field name} required" text for error message above edit-organizer form in Community Events.', 'tribe-events-community' ), $field ), 'error' );
						$has_all_required_fields = false;
					}
				}

				remove_action(
					'save_post_'.Tribe__Events__Main::ORGANIZER_POST_TYPE,
					[ Tribe__Events__Main::instance(), 'save_organizer_data' ],
					16,
					2
				);

				if ( $has_all_required_fields ) {

					wp_update_post( [
						'post_title'   => $_POST[ 'post_title' ],
						'ID'           => $organizer_id,
						'post_content' => $_POST[ 'post_content' ],
					] );

					Tribe__Events__API::updateOrganizer( $organizer_id, $_POST['organizer'] );
					$this->enqueueOutputMessage( esc_html__( 'Organizer updated.', 'tribe-events-community' ) );
				}
			}

			global $post;
			$post = get_post( $organizer_id );

			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/edit-organizer' );

			$output .= ob_get_clean();

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

			return $output;
		}

		/**
		 * Show the current user's events.
		 *
		 * @since  1.0
		 *
		 * @param int  $page Pagination.
		 * @param bool $print_before_after_override
		 * @param bool $shortcode
		 *
		 * @return string The page.
		 */
		public function doMyEvents( $page = null, $print_before_after_override = false, $shortcode = false ) {
			$output = '';
			$this->default_template_compatibility( $print_before_after_override );

			$this->loadScripts = true;
			do_action( 'tribe_events_community_before_event_list_page' );
			do_action_deprecated(
				'tribe_ce_before_event_list_page',
				[],
				'4.6.3',
				'tribe_events_community_before_event_list_page',
				'The action "tribe_ce_before_event_list_page" has been renamed to "tribe_events_community_before_event_list_page" to match plugin namespacing.'
			);

			$output .= '<div class="tribe-community-events-content">';
			ob_start();

			if ( $this->should_print_before_after_html ) {
				tribe_events_before_html();
			}

			$output .= ob_get_clean();

			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				global $paged;

				if ( empty( $paged ) && ! empty( $page ) ) {
					$paged = $page;
				}

				add_filter( 'tribe_query_can_inject_date_field', '__return_false' );

				/**
				 * Allow filtering the "my events" query 'orderby' param directly.
				 *
				 * @since 4.6.2
				 *
				 * @param string 'event_date'    defaults to event_date now for orderby
				 */
				$orderby = apply_filters( 'tribe_events_community_my_events_query_orderby', 'event_date' );
				$orderby = apply_filters_deprecated(
					'tribe_ce_my_events_query_orderby',
					[ $orderby ],
					'4.6.3',
					'tribe_events_community_my_events_query_orderby',
					'The filter "tribe_ce_my_events_query_orderby" has been renamed to "tribe_events_community_my_events_query_orderby" to match plugin namespacing.'
				);

				/**
				 * Allow filtering the "my events" query 'order' param directly.
				 *
				 * @since 4.6.2
				 *
				 * @param string 'ASC'    defaults to ASC now for order
				 */
				$order = apply_filters( 'tribe_events_community_my_events_query_order', 'ASC' );
				$order = apply_filters_deprecated(
					'tribe_ce_my_events_query_order',
					[ $order ],
					'4.6.3',
					'tribe_events_community_my_events_query_order',
					'The filter "tribe_ce_my_events_query_order" has been renamed to "tribe_events_community_my_events_query_order" to match plugin namespacing.'
				);

				$args = [
					'posts_per_page'      => $this->eventsPerPage,
					'paged'               => $paged,
					'author'              => $current_user->ID,
					'post_type'           => Tribe__Events__Main::POSTTYPE,
					'post_status'         => [ 'pending', 'draft', 'future', 'publish' ],
					'eventDisplay'        => empty( $_GET['eventDisplay'] ) ? 'list' : $_GET['eventDisplay'],
					'tribeHideRecurrence' => false,
					'orderby'             => sanitize_text_field( $orderby ),
					'order'               => sanitize_text_field( $order ),
					's'                   => isset( $_GET['event-search'] ) ? esc_html( $_GET['event-search'] ) : '',
				];

				/**
				 * Allow filtering the "my events" query args.
				 * Note that 'order' and 'orderby can be filtered directly -
				 *     removing the need to sift through the array to change them
				 *     via: `tribe_ce_my_events_query_order` and `tribe_ce_my_events_query_orderby`
				 *
				 * @since 4.6.1.2
				 *
				 * @param array $args    array of query args
				 */
				$args = apply_filters( 'tribe_events_community_my_events_query', $args );
				$args = apply_filters_deprecated(
					'tribe_ce_my_events_query',
					[ $args ],
					'4.6.3',
					'tribe_events_community_my_events_query',
					'The filter "tribe_ce_my_events_query" has been renamed to "tribe_events_community_my_events_query" to match plugin namespacing.'
				);
				$events = tribe_get_events( $args, true );

				remove_filter( 'tribe_query_can_inject_date_field', '__return_false' );

				/**
				 * Allow users to inject content into the end of the list
				 *
				 * @since 4.6.1.2
				 */
				do_action( 'tribe_events_community_before_event_list_page_template' );
				do_action_deprecated(
					'tribe_ce_before_event_list_page_template',
					[],
					'4.6.3',
					'tribe_events_community_before_event_list_page_template',
					'The action "tribe_ce_before_event_list_page_template" has been renamed to "tribe_events_community_before_event_list_page_template" to match plugin namespacing.'
				);
				ob_start();
				if ( $shortcode ) {
					$template = 'community/event-list-shortcode';
				} else {
					$template = 'community/event-list';
				}

				/**
				 * Allow filtering the template being included for the event list.
				 *
				 * @since 4.6.2
				 *
				 * @param string $template Template to include.
				 */
				$template = apply_filters( 'tribe_events_community_list_page_template_include', $template );

				include Tribe__Events__Templates::getTemplateHierarchy( $template );

				$output .= ob_get_clean();

				wp_reset_query();
			} else {
				do_action( 'tribe_tribe_events_community_event_list_login_form' );
				do_action_deprecated(
					'tribe_ce_event_list_login_form',
					[],
					'4.6.3',
					'tribe_tribe_events_community_event_list_login_form',
					'The action "tribe_ce_event_list_login_form" has been renamed to "tribe_tribe_events_community_event_list_login_form" to match plugin namespacing.'
				);
				$output .= $this->login_form( __( 'Please log in to view your events', 'tribe-events-community' ) );
			}

			ob_start();

			if ( $this->should_print_before_after_html ) {
				tribe_events_after_html();
			}

			$output .= ob_get_clean();
			$output .= '</div>';

			return $output;
		}

		/**
		 * Indicates whether or not the image size was exceeded
		 *
		 * @return boolean
		 */
		public function max_file_size_exceeded() {
			return (
				isset( $_SERVER['CONTENT_LENGTH'] )
				&& (int) $_SERVER['CONTENT_LENGTH'] > $this->max_file_size_allowed()
			);
		}

		/**
		 * Indicate the max upload size allowed
		 *
		 * @since 4.5.12
		 *
		 * @return int
		 */
		public function max_file_size_allowed() {
			/**
			 * Filter the the max upload size allowed.
			 *
			 * By default, it's using the `wp_max_upload_size()` value
			 *
			 * @since 4.5.12
			 *
			 * @param int `wp_max_upload_size()` The default WordPress max upload size.
			 */
			return apply_filters( 'tribe_community_events_max_file_size_allowed', wp_max_upload_size() );
		}

		/**
		 * Honeypot to prevent spam.
		 *
		 * @since       1.0
		 * @deprecated  4.5
		 *
		 * @return      void
		 */
		public function formSpamControl() {
			_deprecated_function( __METHOD__, '4.5', 'tribe_get_template_part( \'community/modules/spam-control\' )' );

			tribe_get_template_part( 'community/modules/spam-control' );
		}

		/**
		 * If we have a spam submission, just kick the user away
		 * @return void
		 */
		public function spam_check( $submission ) {
			$timestamp = empty( $submission['render_timestamp'] ) ? 0 : intval( $submission['render_timestamp'] );
			if ( ! empty( $submission['tribe-not-title'] ) || $timestamp == 0 || time() - $timestamp < 3 ) { // you can't possibly fill out this form in 3 seconds
				wp_safe_redirect( home_url(), 303 );
				exit();
			}
		}

		/**
		 * Display event details.
		 *
		 * @since 1.0
		 * @deprecated 4.5
		 * @uses Tribe__Events__Main::EventsChooserBox()
		 *
		 * @param object $event The event post
		 * @return void
		 */
		public function formEventDetails( $event = null ) {
			global $post;
			$tec = Tribe__Events__Main::instance();

			// TEC doesn't like an empty $post object
			if ( ! $event ) {
				// error with php 5.4
				if ( ! is_object( $post ) ) {
					$post = new stdClass;
				}

				if ( isset( $post->ID ) ) {
					$old_post_id = $post->ID;
				}
				$post->ID = 0;
				$post->post_type = Tribe__Events__Main::POSTTYPE;
			}

			if ( isset( $event->ID ) && $event->ID ) {
				$tec->EventsChooserBox( $event );
			} else {
				$tec->EventsChooserBox();
			}

			if ( ! $event && isset( $old_post_id ) ) {
				$post->ID = $old_post_id;
			}
		}

		/**
		 * Form event title.
		 *
		 * @since 1.0
		 *
		 * @param object $event The event to display the tile for.
		 * @return void
		 */
		public function formTitle( $event = null ) {
			$title = get_the_title( $event );
			if ( empty( $title ) && ! empty( $_POST['post_title'] ) ) {
				$title = stripslashes( $_POST['post_title'] );
			}
			?>
			<input
				id="post_title"
				type="text"
				name="post_title"
				value="<?php esc_attr_e( $title ); ?>"
				class="<?php tribe_community_events_field_classes( 'post_title', [] ); ?>"
			/>
			<?php
		}

		/**
		 * Form event content.
		 *
		 * @param object $event The event to display the tile for.
		 * @return void
		 *
		 * @since 1.0
		 */
		public function formContentEditor( $event = null ) {
			if ( $event == null ) {
				$event = get_post();
			}
			if ( $event ) {
				$post_content = $event->post_content;
			} elseif ( ! empty( $_POST['post_content'] ) ) {
				$post_content = stripslashes( $_POST['post_content'] );
			} else {
				$post_content = '';
			}

			$classes = tribe_community_events_field_classes( 'post_content', [ 'frontend' ], false );

			// if the admin wants the rich editor, and they are using WP 3.3, show the WYSIWYG, otherwise default to just a text box
			if ( $this->useVisualEditor && function_exists( 'wp_editor' ) ) {
				$settings = [
					'wpautop'       => true,
					'media_buttons' => false,
					'editor_class'  => $classes,
					'textarea_rows' => 5,
				];

				wp_editor( $post_content, 'tcepostcontent', $settings );
			} else {
				?><textarea
					id="post_content"
					name="tcepostcontent"
					class="<?php echo $classes; ?>"
				><?php
					echo esc_textarea( $post_content );
				?></textarea><?php
			}
		}

		/**
		 * Form category dropdown.
		 *
		 * @since 1.0
		 * @deprecated 4.5
		 *
		 * @param object $event The event to display the tile for.
		 * @param array $currently_selected DEPRECATED Category ids that should start selected (theoretically passed from the $_POST variable).
		 * @return void
		 */
		public function formCategoryDropdown( $event = null, $currently_selected = [] ) {
			_deprecated_function(
				__METHOD__,
				'4.2',
				'Tribe__Events__Community__Modules__Taxonomy_Block::the_category_checklist'
			);

			tribe_get_template_part(
				'community/modules/taxonomy',
				null,
				[ 'taxonomy' => Tribe__Events__Main::TAXONOMY ]
			);
		}

		/**
		 * Display status icon.
		 *
		 * @since 1.0
		 * @deprecated 4.5
		 *
		 * @param string $status The post status.
		 * @return string The status image element markup.
		 *
		 * @since 1.0
		 */
		public function getEventStatusIcon( $status ) {
			$icon = str_replace( ' ', '-', $status ) . '.png';

			if ( file_exists( get_stylesheet_directory() . 'events/community/' . $icon ) ) {
				return '<img src="' . get_stylesheet_directory_uri() . 'events/community' . esc_attr( $icon ) . '" alt="' . esc_attr( $status ) . ' icon" class="icon ' . esc_attr( $status ) . '">';
			} elseif ( file_exists( get_template_directory() . 'events/community/icons/' . esc_attr( $icon ) ) ) {
				return '<img src="' . get_template_directory_uri() . 'events/community' . esc_attr( $icon ) . '" alt="' . esc_attr( $status ) . ' icon" class="icon ' . esc_attr( $status ) . '">';
			} else {
				return '<img src="' . $this->pluginUrl . 'src/resources/images/' . esc_attr( $icon ) . '" alt="' . esc_attr( $status ) . ' icon" class="icon ' . esc_attr( $status ) . '">';
			}
		}

		/**
		 * Filter pagination
		 *
		 * @since 1.0
		 *
		 * @param object $query        The query to paginate
		 * @param int    $pages        The pages
		 * @param int    $range        The range
		 * @param bool   $shortcode
		 *
		 * @return string The pagination links
		 */
		public function pagination( $query, $pages = 0, $range = 3, $shortcode = false ) {
			$output    = '';

			// Cast as Int for PHP 8 compatibility.
			$range = (int) $range;
			$pages = (int) $pages;

			$showitems = ( $range * 2 ) + 1;

			global $paged;
			if ( empty( $paged ) ) {
				$paged = 1;
			}

			if ( $pages == 0 ) {
				//global $wp_query;
				$pages = ceil( $query->found_posts / $this->eventsPerPage );

				if ( ! $pages ) {
					$pages = 1;
				}
			}

			if ( $paged > $pages ) {
				$this->enqueueOutputMessage( __( 'The requested page number was not found.', 'tribe-events-community' ) );
			}
			if ( 1 != $pages ) {
				add_filter( 'get_pagenum_link', [ $this, 'fix_pagenum_link' ] );

				// If we are using the Community Events Shortcode, we should paginate the current post URL
				if ( $shortcode ) {
					// Ensure that the URLs will always end with slash.
					// This is necessary for the Events List to be paginated on posts or pages with ugly permalinks.
					$url = rtrim( get_permalink(), '/' ) . '/';

					$output .= "<div class='tribe-pagination'>";
					if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( $url . $paged ) . "'>&laquo;</a>";
					}
					if ( $paged > 1 && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( $url . ( $paged - 1 ) ) . "'>&lsaquo;</a>";
					}

					for ( $i = 1; $i <= $pages; $i ++ ) {
						if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
							$output .= ( $paged == $i ) ? '<span class="current">' . $i . '</span>' : '<a href="' . esc_url( $url . $i ) . '" class="inactive">' . $i . '</a>';
						}
					}

					if ( $paged < $pages && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( $url . ( $paged + 1 ) ) . "'>&rsaquo;</a>";
					}
					if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( $url . $paged ) . "'>&raquo;</a>";
					}
					$output .= "</div>\n";
				} else {
					$output .= "<div class='tribe-pagination'>";
					if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( get_pagenum_link( 1 ) ) . "'>&laquo;</a>";
					}
					if ( $paged > 1 && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( get_pagenum_link( $paged - 1 ) ) . "'>&lsaquo;</a>";
					}

					for ( $i = 1; $i <= $pages; $i ++ ) {
						if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
							$output .= ( $paged == $i ) ? '<span class="current">' . esc_html( $i ) . '</span>' : '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="inactive">' . esc_html( $i ) . '</a>';
						}
					}

					if ( $paged < $pages && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( get_pagenum_link( $paged + 1 ) ) . "'>&rsaquo;</a>";
					}
					if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
						$output .= "<a href='" . esc_url( get_pagenum_link( $pages ) ) . "'>&raquo;</a>";
					}
					$output .= "</div>\n";
				}
			}

			return $output;
		}

		/**
		 * Get the template file with an output buffer.
		 *
		 * @since 1.0
		 *
		 * @param string $template_path The path.
		 * @param string $template_file The file.
		 * @return string The file's output.
		 */
		public function get_template( $template_path, $template_file ) {
			ob_start();
			include $this->getTemplatePath( $template_path, $template_file );
			return ob_get_clean();
		}

		/**
		 * Get a file's path.
		 *
		 * @param string $path The path.
		 * @param string $file The file.
		 * @return string The file's path.
		 * @since 1.0
		 */
		public static function getTemplatePath( $path, $file ) {
			_deprecated_function( __FUNCTION__, '2.1', 'Tribe__Events__Community__Templates::getTemplateHierarchy()' );

			// protect duplicate call to views
			$template_path = $path == 'views' ? '' : $path;
			return Tribe__Events__Templates::getTemplateHierarchy(
				 $file,
				 [
					'subfolder' => $path,
					'namespace' => 'community',
					'plugin_path' => self::instance()->pluginPath,
				]
			);
		}

		/**
		 * Filter the limit query.
		 *
		 * @since 1.0
		 *
		 * @return string The modified query.
		 */
		public function limitQuery() {
			global $paged;
			if ( $paged - 1 <= 0 ) {
				$page = 0;
			} else {
				$page = $paged - 1;
			}

			$lq = 'LIMIT ' . ( ( $this->eventsPerPage * $page ) ) . ',' . $this->eventsPerPage;
			return $lq;
		}

		/**
		 * Add messages to the error/notice queue
		 *
		 * @since 3.1
		 *
		 * @param string $message
		 * @param null|string $type
		 */
		public function enqueueOutputMessage( $message, $type = null ) {
			$this->messages[] = $message;
			if ( $type ) {
				$this->messageType = $type;
			}
		}

		/**
		 * Output a message to the user.
		 *
		 * @since 1.0
		 *
		 * @param string $type The message type.
		 * @param bool $echo Whether to display or return the message.
		 * @return string The message.
		 */
		public function outputMessage( $type = null, $echo = true ) {

			if ( ! $type && ! $this->messageType ) {
				$type = 'updated';
			} elseif ( ! $type && $this->messageType ) {
				$type = $this->messageType;
			}

			$errors = [];

			if ( isset( $this->messages ) && ! empty( $this->messages ) ) {
				$errors = [
					[
						'type'    => $type,
						'message' => '<p>' . join( '</p><p>', $this->messages ) . '</p>',
					],
				];
			}

			$errors = apply_filters( 'tribe_community_events_form_errors', $errors );

			if ( ! is_array( $errors ) ) {
				return '';
			}

			// Prevent the undefined property notice $messages on Community Events shortcodes
			if ( empty( $this->messages ) ) {
				$this->messages = [];
			}

			ob_start();

			$existing_messages = isset( $this->messages ) ? $this->messages : [];

			/**
			 * Allows for adding content before the form's various messages.
			 *
			 * @since 4.5.15
			 *
			 * @param array $existing_messages The current array of messages to display on the form; empty array if none exist.
			 */
			do_action( 'tribe_community_events_before_form_messages', $existing_messages );

			foreach ( $errors as $error ) {
				printf(
					'<div class="tribe-community-notice tribe-community-notice-%1$s">%2$s</div>',
					esc_attr( $error[ 'type' ] ),
					wp_kses_post( $error[ 'message' ] )
				);
			}

			unset( $this->messages );

			if ( $echo ) {
				echo ob_get_clean();
			} else {
				return ob_get_clean();
			}
		}

		/**
		 * Filter pagination links.
		 *
		 * @since 1.0
		 *
		 * @param string $result The link.
		 * @return string The filtered link.
		 */
		public function fix_pagenum_link( $result ) {

			// pretty permalinks - fix page one to have args so we don't redirect to todays's page
			if ( '' != get_option( 'permalink_structure' ) && ! strpos( $result, '/page/' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			// ugly links - fix page one to have args so we don't redirect to todays's page
			if ( '' == get_option( 'permalink_structure' ) && ! strpos( $result, 'paged=' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			return $result;
		}

		/**
		 * @param array $user_caps The capabilities the user has
		 * @param array $requested_caps The capabilities the user needs
		 * @param array $args [0] = The specific cap requested, [1] = The user ID
		 * @return array mixed
		 */
		public function filter_user_caps( $user_caps, $requested_caps, $args ) {
			if ( ! empty( $args[1] ) ) {
				if ( $this->allowUsersToEditSubmissions ) {
					$user_caps['edit_tribe_events'] = true;
					$user_caps['edit_tribe_venues'] = true;
					$user_caps['edit_tribe_organizers'] = true;

					$user_caps['edit_published_tribe_events'] = true;
					$user_caps['edit_published_tribe_venues'] = true;
					$user_caps['edit_published_tribe_organizers'] = true;
				}

				if ( $this->allowUsersToDeleteSubmissions ) {
					$user_caps['delete_tribe_events'] = true;
					$user_caps['delete_tribe_venues'] = true;
					$user_caps['delete_tribe_organizers'] = true;

					$user_caps['delete_published_tribe_events'] = true;
					$user_caps['delete_published_tribe_venues'] = true;
					$user_caps['delete_published_tribe_organizers'] = true;
				}
			}
			return $user_caps;
		}

		/**
		 * Determine if the specified user can edit the specified post.
		 *
		 * @param int|null $id The current post ID.
		 * @param string $post_type The post type.
		 * @return bool Whether the use has the permissions to edit a given post.
		 *
		 * @since 1.0
		 * @deprecated since version 3.1
		 */
		public function userCanEdit( $id = null, $post_type = null ) {
			// if we're talking about a specific post, use standard WP permissions
			if ( $id ) {
				return current_user_can( 'edit_post', $id );
			}

			if ( empty( $post_type ) || ! is_user_logged_in() ) {
				return false;
			}

			// only supports Tribe Post Types
			if ( ! in_array( $post_type, Tribe__Events__Main::getPostTypes() ) ) {
				return false;
			}

			// admin override
			if ( is_super_admin() || current_user_can( 'manage_options' ) ) {
				return true;
			}

			return $this->allowUsersToEditSubmissions;
		}

		/**
		 * Add a settings tab.
		 *
		 * Additionally sets up a filter to append information to the existing events template setting tooltip.
		 *
		 * @return void
		 *
		 * @since 1.0
		 */
		public function doSettings() {
			require_once $this->pluginPath . 'src/admin-views/community-options-template.php';
			new Tribe__Settings_Tab( 'community', __( 'Community', 'tribe-events-community' ), $communityTab );
			add_filter( 'tribe_field_tooltip', [ $this, 'amend_template_tooltip' ], 10, 3 );
		}

		/**
		 * This method filters the tooltip for the tribeEventsTemplate setting to make it clear that it also
		 * impacts on Community Events output.
		 *
		 * @param $text
		 * @param $tooltip
		 * @param $field = null (this may not provided when tribe_field_tooltip callbacks take place)
		 * @return string
		 */
		public function amend_template_tooltip( $text, $tooltip, $field = null ) {
			if ( null === $field || 'tribeEventsTemplate' !== $field->id ) {
				return $text;
			}
			$description = __( 'This template is also used for Community Events.', 'tribe-events-community' );
			return str_replace( $tooltip, "$tooltip $description ", $text );
		}

		/**
		 * If the anonymous submit setting is changed, flush the rewrite rules.
		 *
		 * @param string $field The name of the field being saved.
		 * @param string $value The new value of the field.
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public function flushRewriteOnAnonymous( $field, $value ) {
			if ( $field == 'allowAnonymousSubmissions' && $value != $this->allowAnonymousSubmissions ) {
				Tribe__Events__Main::flushRewriteRules();
			}
		}

		/**
		 * Add a community events origin to the audit system.
		 *
		 * @return string The community events slug.
		 *
		 * @since 1.0
		 */
		public function filterPostOrigin() {
			return 'community-events';
		}

		/**
		 * Get all options for the plugin.
		 *
		 * @since 1.0
		 *
		 * @param bool $force
		 *
		 * @return array The current settings for the plugin.
		 */
		public static function getOptions( $force = false ) {
			if ( ! isset( self::$options ) || $force ) {
				$options       = get_option( self::OPTIONNAME, [] );
				self::$options = apply_filters( 'tribe_community_events_get_options', $options );
			}
			return self::$options;
		}

		/**
		 * Get value for a specific option.
		 *
		 * @param string $optionName Name of option.
		 * @param mixed $default Default value.
		 * @param bool $force
		 * @return mixed Results of option query.
		 *
		 * @since 1.0
		 */
		public function getOption( $optionName, $default = '', $force = false ) {
			if ( ! $optionName ) {
				return;
			}

			if ( ! isset( self::$options ) || $force ) {
				self::getOptions( $force );
			}

			$option = $default;
			if ( isset( self::$options[ $optionName ] ) ) {
				$option = self::$options[ $optionName ];
			} elseif ( is_multisite() && isset( self::$tribeCommunityEventsMuDefaults ) && is_array( self::$tribeCommunityEventsMuDefaults ) && in_array( $optionName, array_keys( self::$tribeCommunityEventsMuDefaults ) ) ) {
				$option = self::$tribeCommunityEventsMuDefaults[ $optionName ];
			}

			return apply_filters( 'tribe_get_single_option', $option, $default, $optionName );
		}

		/**
		 * Set value for a specific option.
		 *
		 * @param string $optionName Name of option.
		 * @param string $value  Value to set.
		 *
		 * @since 1.0
		 */
		public function setOption( $optionName, $value ) {
			if ( ! $optionName ) {
				return;
			}

			if ( ! isset( self::$options ) ) {
				self::getOptions();
			}
			self::$options[ $optionName ] = $value;
			update_option( self::OPTIONNAME, self::$options );
		}

		/**
		 * Get the plugin's path.
		 *
		 * @return string The path.
		 *
		 * @since 1.0
		 */
		public static function getPluginPath() {
			return self::instance()->pluginPath;
		}

		/**
		 * Get the current user's role.
		 *
		 * @return string The role.
		 *
		 * @since 1.0
		 */
		public function getCurrentUserRole() {
			$user_roles = $this->getUserRoles();
			if ( empty( $user_roles ) ) {
				return false;
			}
			return array_shift( $user_roles );
		}

		/**
		 * get roles for a specified user, or current user
		 *
		 * @since 3.1
		 *
		 * @param integer $user_id defaults to get_current_user_id()
		 * @return array user roles or an empty array if none found
		 */
		public function getUserRoles( $user_id = 0 ) {
			$user_id = $user_id ? $user_id : get_current_user_id();
			if ( empty( $user_id ) ) {
				return [];
			}

			$user = new WP_User( $user_id );
			if ( isset( $user->roles ) ) {
				return $user->roles;
			}
			return [];
		}

		/**
		 * Get the URL to redirect Block Roles from Admin.
		 *
		 * @since 4.6.3
		 *
		 * @see \Tribe__Events__Community__Main::user_can_access_admin() Check for this before redirecting to this URL.
		 *
		 * @return string
		 */
		private function get_block_roles_redirect_url() {
			$option = $this->getOption( 'blockRolesRedirect' );

			if ( empty( $option ) ) {
				$url = $this->getUrl( 'list' );
			} else {
				$url = $option;
			}

			return esc_url_raw( $url );
		}

		/**
		 * Facilitate blocking specific roles from the admin environment.
		 */
		public function blockRolesFromAdmin() {
			// Get Current User ID
			$user_id = get_current_user_id();

			// Let WordPress worry about admin access for unauthenticated users
			if ( ! is_user_logged_in() ) {
				return;
			}

			// If the user has access privileges then we don't need to interfere, else hide the WP Admin Bar
			if ( $this->user_can_access_admin( $user_id ) ) {
				return;
			} else {
				add_filter( 'show_admin_bar', '__return_false' );
			}

			// If it is not an admin request - or if it is an ajax request - then we don't need to interfere
			if (
				! is_admin()
				|| wp_doing_ajax()
			) {
				return;
			}

			// Make sure the action to send the email comes from the FE
			if (
				'email' === tribe_get_request_var( 'action' )
				&& 'tickets-attendees' === tribe_get_request_var( 'page' )
				&& tribe_get_request_var( 'event_id' )
			) {
				return;
			}

			wp_redirect( $this->get_block_roles_redirect_url() );
			tribe_exit();
		}

		/**
		 * Get determination if the user has a role that allows access to the admin
		 *
		 * @since 4.5.9
		 *
		 * @param int $user_id
		 *
		 * @return bool
		 */
		public function get_user_can_access_admin( $user_id = 0 ) {
			if ( $this->user_can_access_admin( $user_id ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Determine if the user has a role that allows him to access the admin
		 *
		 * @param int $user_id
		 * @return bool Whether the user is allowed to access the admin (by this plugin)
		 * @since 3.1
		 */
		protected function user_can_access_admin( $user_id = 0 ) {
			if ( ! is_array( $this->blockRolesList ) || empty( $this->blockRolesList ) ) {
				return true;
			}

			if ( is_super_admin( $user_id ) ) {
				return true;
			}
			$user_roles = $this->getUserRoles( $user_id );

			// if a user has multiple roles, still let him in if he has a non-blocked role
			$diff = array_diff( $user_roles, $this->blockRolesList );
			if ( empty( $diff ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Get the appropriate logout URL for the current user.
		 *
		 * @since 3.1
		 *
		 * @return string The logout URL.
		 */
		public function logout_url() {
			$can_access_admin = $this->user_can_access_admin();

			if ( $can_access_admin ) {
				$redirect_to = '';
			} else {
				$redirect_to = $this->get_block_roles_redirect_url();
			}

			/**
			 * The appropriate logout URL for the current user.
			 *
			 * @since 4.6.3
			 *
			 * @param string $redirect_to      The URL to redirect_to to.
			 * @param bool   $can_access_admin Whether or not the current user can access the WordPress admin area.
			 *
			 * @return string
			 */
			$redirect_to = apply_filters( 'tribe_events_community_logout_url_redirect_to', $redirect_to, $can_access_admin );

			return wp_logout_url( $redirect_to );
		}

		/**
		 * Add the community events toolbar items.
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public function addCommunityToolbarItems() {
			/** @var WP_Admin_Bar $wp_admin_bar */
			global $wp_admin_bar;

			$wp_admin_bar->add_group( [
				'id' => 'tribe-community-events-group',
				'parent' => 'tribe-events-add-ons-group',
			] );

			$wp_admin_bar->add_menu( [
				'id' => 'tribe-community-events-submit',
				'title' => sprintf( __( 'Community: Submit %s', 'tribe-events-community' ), tribe_get_event_label_singular() ),
				'href' => esc_url( $this->getUrl( 'add' ) ),
				'parent' => 'tribe-community-events-group',
			] );

			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu( [
					'id' => 'tribe-community-events-my-events',
					'title' => sprintf( __( 'Community: My %s', 'tribe-events-community' ), tribe_get_event_label_plural() ),
					'href' => esc_url( $this->getUrl( 'list' ) ),
					'parent' => 'tribe-community-events-group',
				] );
			}

			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( [
					'id' => 'tribe-community-events-settings-sub',
					'title' => __( 'Community Events', 'tribe-events-community' ),
					'href' => Tribe__Settings::instance()->get_url( [ 'tab' => 'community' ] ),
					'parent' => 'tribe-events-settings',
				] );
			}
		}

		/**
		 * Return additional action for the plugin on the plugins page.
		 *
		 * @param array $actions
		 * @return array
		 * @since 1.0.2
		 */
		public function addLinksToPluginActions( $actions ) {
			if ( class_exists( 'Tribe__Events__Main' ) ) {
				$actions['settings'] = '<a href="' . Tribe__Settings::instance()->get_url( [ 'tab' => 'community' ] ) . '">' . __( 'Settings', 'tribe-events-community' ) . '</a>';
			}
			return $actions;
		}

		/**
		 * Load the plugin's textdomain.
		 *
		 * @return void
		 * @since 1.0
		 */
		public function loadTextDomain() {
			$mopath = $this->pluginDir . 'lang/';
			$domain = 'tribe-events-community';

			// If we don't have Common classes load the old fashioned way
			if ( ! class_exists( 'Tribe__Main' ) ) {
				load_plugin_textdomain( $domain, false, $mopath );
			} else {
				// This will load `wp-content/languages/plugins` files first
				Tribe__Main::instance()->load_text_domain( $domain, $mopath );
			}
		}

		/**
		 * Init the plugin.
		 *
		 * @return void
		 * @since 1.0
		 */
		public function init() {

			// Setup Main Service Provider
			tribe_register_provider( 'Tribe__Events__Community__Service_Provider' );

			$this->anonymous_users = new Tribe__Events__Community__Anonymous_Users( $this );

			// Start the integrations manager
			tribe( 'community.integrations' )->load_integrations();

			$this->set_rewrite_slugs();
		}

		/**
		 * Sets up the rewrite slugs.
		 *
		 * Grabs the slugs from options, allows other plugins to filter them,
		 * then sets a value from $this->default_rewrite_slugs if they are blank.
		 *
		 * Note these slugs are NOT translated, as this can lead to 404s on multi-lingual sites.
		 *
		 * @since 4.6.3
		 */
		public function set_rewrite_slugs() {
			$this->communityRewriteSlug = sanitize_title( $this->getOption( 'communityRewriteSlug', 'community' ) );

			/**
			 * Allows for filtering the main community rewrite slug.
			 *
			 * @since 4.6.3
			 *
			 * @param string $rewrite_slug The slug value.
			 */
			$this->communityRewriteSlug = apply_filters( 'tribe_community_events_rewrite_slug', $this->communityRewriteSlug );

			// Set default if we end up with an empty string.
			if ( empty( $this->communityRewriteSlug ) ) {
				$this->communityRewriteSlug = 'community';
			}

			$this->rewriteSlugs['edit']      = sanitize_title( $this->getOption( 'community-edit-slug', $this->default_rewrite_slugs['edit'], true ) );
			$this->rewriteSlugs['add']       = sanitize_title( $this->getOption( 'community-add-slug', $this->default_rewrite_slugs['add'], true ) );
			$this->rewriteSlugs['delete']    = sanitize_title( $this->getOption( 'community-delete-slug', $this->default_rewrite_slugs['delete'], true ) );
			$this->rewriteSlugs['list']      = sanitize_title( $this->getOption( 'community-list-slug', $this->default_rewrite_slugs['list'], true ) );
			$this->rewriteSlugs['venue']     = sanitize_title( $this->getOption( 'community-venue-slug', $this->default_rewrite_slugs['venue'], true ) );
			$this->rewriteSlugs['organizer'] = sanitize_title( $this->getOption( 'community-organizer-slug', $this->default_rewrite_slugs['organizer'], true ) );
			$this->rewriteSlugs['event']     = sanitize_title( $this->getOption( 'community-event-slug', $this->default_rewrite_slugs['event'], true ) );

			/**
			 * Allows for filtering the community rewrite slugs.
			 *
			 * @since 4.6.3
			 *
			 * @param array The slug array.
			 */
			$this->rewriteSlugs = apply_filters( 'tribe_community_events_rewrite_slug', $this->rewriteSlugs );

			foreach ( $this->rewriteSlugs as $key => $value ) {
				/**
				 * Allows for filtering the rewrite slugs individually.
				 *
				 * @since 4.6.3
				 *
				 * @param string $value The slug value.
				 * @param string $key   The slug key.
				 */
				$this->rewriteSlugs[ $key ] = apply_filters( 'tribe_community_events_' . $key . '_rewrite_slug', $value, $key );
			}

			// Just in case, reset any slugs that were empty or just whitespace with the defaults.
			$this->rewriteSlugs = array_filter( array_map( 'trim', $this->rewriteSlugs ) );
			$this->rewriteSlugs = array_merge( $this->default_rewrite_slugs, $this->rewriteSlugs );
		}

		public function load_captcha_plugin() {
			$this->captcha = apply_filters( 'tribe_community_events_captcha_plugin', new Tribe__Events__Community__Captcha__Recaptcha_V2() );
			if ( empty( $this->captcha ) ) {
				$this->captcha = new Tribe__Events__Community__Captcha__Null_Captcha();
			}
			$this->captcha->init();
		}

		public function captcha() {
			return $this->captcha;
		}

		/**
		 * Singleton instance method.
		 *
		 * @return Tribe__Events__Community__Main The instance
		 *
		 * @since 1.0
		 */
		public static function instance() {
			return tribe( 'community.main' );
		}

		/**
		 * Sets the setting variable that says the rewrite rules should be flushed upon plugin load.
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public static function activateFlushRewrite() {
			$options = self::getOptions();
			$options['maybeFlushRewrite'] = true;
			update_option( self::OPTIONNAME, $options );
		}

		/**
		 * Checks if it should flush rewrite rules (after plugin is loaded).
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public function maybeFlushRewriteRules() {
		 	if ( $this->maybeFlushRewrite == true ) {
		 		Tribe__Events__Main::flushRewriteRules();
		 		$options = self::getOptions();
				$options['maybeFlushRewrite'] = false;
				update_option( self::OPTIONNAME, $options );
			}
		}

		/**
		 * Removes the Edit link from My Events and Edit Event community pages.
		 *
		 * @param string $content
		 * @return string An empty string.
		 * @author Paul Hughes
		 * @since 1.0.3
		 */
		public function removeEditPostLink( $content ) {
			$content = '';
			return $content;
		}

		/**
		 * Return the forums link as it should appear in the help tab.
		 *
		 * @param string $content
		 * @return string
		 * @author Paul Hughes
		 * @since 1.0.3
		 */
		public function helpTabForumsLink( $content ) {
			$promo_suffix = '?utm_source=helptab&utm_medium=plugin-community&utm_campaign=in-app';
			return ( isset( Tribe__Events__Main::$tecUrl ) ? Tribe__Events__Main::$tecUrl : Tribe__Events__Main::$tribeUrl ) . 'support/forums/' . $promo_suffix;
		}

		/**
		 * Allows multisite installs to override defaults for settings.
		 *
		 * @param mixed $value The current default.
		 * @param string $key The option key.
		 * @param array $field The field.
		 * @return mixed The MU default value of the option.
		 * @author Paul Hughes
		 * @since 1.0.6
		 */
		public function multisiteDefaultOverride( $value, $key, $field ) {
			if ( isset( $field['parent_option'] ) && $field['parent_option'] == self::OPTIONNAME ) {
				$current_options = $this->getOptions();
				if ( isset( $current_options[ $key ] ) ) {
					return $value;
				} elseif ( isset( self::$tribeCommunityEventsMuDefaults[ $key ] ) ) {
					$value = self::$tribeCommunityEventsMuDefaults[ $key ];
				}
			}
			return $value;
		}

		/**
		 * Find the ID of the page with the community shortcode on it
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @return int
		 */
		private function get_community_page_id() {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			if ( isset( $this->tcePageId ) ) {
				return $this->tcePageId;
			}
			$this->tcePageId = $this->findPageByShortcode( '[tribe_community_events]' );
			return $this->tcePageId;
		}

		/**
		 * Find the page id that has the specified shortcode in it.
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @param string $shortcode The shortcode to search for.
		 * @return int The page id.
		 *
		 * @since 1.0
		 */
		public function findPageByShortcode( $shortcode ) {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			global $wpdb;
			$id = get_transient( 'tribe-community-events-page-id' );

			if ( $id === false ) {
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT id from $wpdb->posts WHERE post_content LIKE '%%%s%%' AND post_type in ('page')", $shortcode ) );
				set_transient( 'tribe-community-events-page-id', $id, ( 60 * 60 * 24 * 10 ) );
			}
			return $id;
		}

		/**
		 * Add support for shortcodes in WP >= 4.4 wp_get_document_title calls
		 *
		 * @deprecated 4.6.2 Use tribe( 'community.shortcodes' )->tribe_community_shortcode() instead
		 *
		 * @param array $title Array of title parts
		 *
		 * @return array
		 */
		public function support_shortcodes_in_post_title( $parts ) {

			_deprecated_function( __METHOD__, '4.6.2', "tribe( 'community.shortcodes' )->tribe_community_shortcode()" );

			foreach ( $parts as &$part ) {
				$part = do_shortcode( $part );
			}

			return $parts;
		}

		/**
		 * Add in Community Event Slugs to the System Info after Settings
		 *
		 * @param $systeminfo
		 *
		 * @return mixed
		 */
		public function support_info( $systeminfo ) {

			if ( '' != get_option( 'permalink_structure' ) ) {
				$community_data = [
					'Community Add' => esc_url( $this->getUrl( 'add' ) ),
					'Community List' => esc_url( $this->getUrl( 'list' ) ),
					'Community Options' => get_option( 'tribe_community_events_options', [] ),
				];
				$systeminfo     = Tribe__Main::array_insert_after_key( 'Settings', $systeminfo, $community_data );
			}

			return $systeminfo;
		}

		/**
		 * Show event cost if created in Community Events and has cost
		 *
		 * @since 4.5.3
		 *
		 */
		public function possibly_show_event_cost() {
			global $post;

			if ( ! $post || ! Tribe__Admin__Helpers::instance()->is_screen() ) {
				return;
			}

			$origin = tribe_get_event_meta( $post->ID, '_EventOrigin', true );
			$cost   = tribe_get_event_meta( $post->ID, '_EventCost', true );
			if ( $this->filterPostOrigin() === $origin && $cost ) {
				add_filter( 'tribe_events_admin_show_cost_field', '__return_true' );
			}
		}

		/**
		 * Registers the implementations in the container.
		 *
		 * Classes that should be built at `plugins_loaded` time are also instantiated.
		 *
		 * @since 4.5.10
		 *
		 * @return void
		 */
		protected function bind_implementations() {

		}

		/**
		 * Make necessary database updates on admin_init
		 *
		 * @since 4.5.10
		 *
		 */
		public function run_updates() {
			if ( ! class_exists( 'Tribe__Events__Updater' ) ) {
				return; // core needs to be updated for compatibility
			}

			$updater = new Tribe__Events__Community__Updater( self::VERSION );
			if ( $updater->update_required() ) {
				$updater->do_updates();
			}
		}

		/**
		 * Hooked to tribe_tickets_user_can_manage_attendees
		 * Allows event creator to edit attendees if allowUsersToEditSubmissions is true
		 *
		 * @since 4.6.1
		 *
		 * @param boolean $user_can user can/can't edit
		 * @param int $user_id ID of user to check, uses current user if empty
		 * @param int $event_id Event ID.
		 *
		 * @return boolean
		 */
		public function user_can_manage_own_event_attendees( $user_can, $user_id, $event_id ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			// Cannot manage attendees without user.
			if ( ! $user_id ) {
				return false;
			}

			// Cannot manage attendees without event.
			if ( empty( $event_id ) ) {
				return false;
			}

			// Can manage attendees from admin area.
			if ( is_admin() ) {
				return true;
			}

			// Cannot determine management if origin is not current origin.
			if ( $this->filterPostOrigin() !== get_post_meta( $event_id, '_EventOrigin', true ) )  {
				return $user_can;
			}

			// Cannot manage attendees that they do not own.
			if ( (int) $user_id !== (int) get_post_field( 'post_author', $event_id ) ) {
				return false;
			}

			// Cannot manage attendees if they are not allowed to edit submissions.
			if ( ! tribe( 'community.main' )->getOption( 'allowUsersToEditSubmissions' ) ) {
				return false;
			}

			return true;
		}

		/*
		 * Add Community Events to the list of add-ons to check required version.
		 *
		 * @deprecated 4.6
		 *
		 * @param array $plugins
		 * @return array The existing plugins including CE.
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public static function init_addon( $plugins ) {
			_deprecated_function( __METHOD__, '4.6' );

			$plugins['TribeCE'] = [
				'plugin_name'      => 'The Events Calendar: Community Events',
				'required_version' => '4.9.3.2', // Do not worry about updating this.
				'current_version'  => self::VERSION,
				'plugin_dir_file'  => basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/tribe-community-events.php',
			];

			return $plugins;
		}
	}
}
