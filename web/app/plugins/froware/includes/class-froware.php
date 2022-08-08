<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ingenyus.com
 * @since      1.0.0
 *
 * @package    Froware
 * @subpackage Froware/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Froware
 * @subpackage Froware/includes
 * @author     Gary McPherson <gary@ingenyus.com>
 */
class Froware {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Froware_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'froware';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_shortcodes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Froware_Loader. Orchestrates the hooks of the plugin.
	 * - Froware_i18n. Defines internationalization functionality.
	 * - Froware_Admin. Defines all hooks for the admin area.
	 * - Froware_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-froware-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-froware-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-froware-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-froware-public.php';

		$this->loader = new Froware_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Froware_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Froware_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Froware_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'override_elementor_enqueue_scripts_hook', 20 );
		$this->loader->add_filter( 'option_active_plugins', $plugin_admin, 'filter_active_plugins', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Froware_Public( $this->get_plugin_name(), $this->get_version() );

		// Actions.
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_login_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'after_setup_theme', $plugin_public, 'extend_theme_support', 100 );
		$this->loader->add_action( 'wp_ajax_nopriv_import_event', $plugin_public, 'import_event' );
		$this->loader->add_action( 'wp_ajax_import_event', $plugin_public, 'import_event' );
		$this->loader->add_action( 'wp_ajax_nopriv_validate_event_url', $plugin_public, 'validate_event_url' );
		$this->loader->add_action( 'wp_ajax_validate_event_url', $plugin_public, 'validate_event_url' );
		$this->loader->add_action( 'wpea_after_create_tec_eventbrite_event', $plugin_public, 'track_new_event', 10, 3 );
		$this->loader->add_action( 'tribe_events_community_form_before_template', $plugin_public, 'event_import_form' );
		$this->loader->add_action( 'init', $plugin_public, 'add_taxonomy_to_pages' );
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'override_community_events_parse_request_hook' );
		$this->loader->add_action( 'wp_insert_post', $plugin_public, 'wp_insert_post_callback', 10, 3 );
		$this->loader->add_action( 'set_object_terms', $plugin_public, 'discourse_update_post_meta', 10, 4 );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'elementor_pre_get_posts', 100 );
		$this->loader->add_action( 'wpdc_after_sso_client_user_update', $plugin_public, 'discourse_sso_update_user_meta', 10, 2 );

		// Filters.
		$this->loader->add_filter( 'generate_typography_default_fonts', $plugin_public, 'add_generatepress_fonts' );
		$this->loader->add_filter( 'generate_inside_post_meta_item_output', $plugin_public, 'generate_inside_post_meta_item_output', 20, 2 );
		$this->loader->add_filter( 'generate_post_date_output', $plugin_public, 'generate_post_date_output', 10, 2 );
		$this->loader->add_filter( 'generate_svg_icon_element', $plugin_public, 'generate_svg_icon_element', 10, 2 );
		$this->loader->add_filter( 'login_redirect', $plugin_public, 'discourse_login_redirect', 10, 3 );
		$this->loader->add_filter( 'logout_redirect', $plugin_public, 'logout_redirect', 10, 3 );
		$this->loader->add_filter( 'generate_svg_icon', $plugin_public, 'remove_svg_icon', 10, 2 );
		$this->loader->add_filter( 'nav_menu_css_class', $plugin_public, 'special_nav_class', 10, 3 );
		$this->loader->add_filter( 'wp_nav_menu_objects', $plugin_public, 'wp_nav_menu_objects_callback' );
		$this->loader->add_filter( 'wpsp_defaults', $plugin_public, 'wpsp_defaults' );
		$this->loader->add_filter( 'feedzy_content', $plugin_public, 'feedzy_content_callback', 5, 2 );
		$this->loader->add_filter( 'feedzy_insert_post_args', $plugin_public, 'feedzy_insert_post_args_callback', 10, 6 );
		$this->loader->add_filter( 'feedzy_item_filter', $plugin_public, 'feedzy_item_filter_callback', 10, 5 );
		$this->loader->add_filter( 'the_excerpt_rss', $plugin_public, 'filter_content_feed', 999, 1 );
		$this->loader->add_filter( 'the_content_feed', $plugin_public, 'filter_content_feed', 999, 1 );
		$this->loader->add_filter( 'the_content', $plugin_public, 'append_copyright_notice', 999, 1 );
		$this->loader->add_filter( 'twig_anything_request_args', $plugin_public, 'twig_anything_request_args', 10, 2 );
		$this->loader->add_filter( 'discourse_comment_html', $plugin_public, 'discourse_comment_html', 10, 1 );
		$this->loader->add_filter( 'discourse_replies_html', $plugin_public, 'discourse_replies_html', 10, 1 );
		$this->loader->add_filter( 'ninja_forms_post_run_action_type_redirect', $plugin_public, 'ninja_forms_post_run_action_type_redirect_callback', 10, 1 );
		$this->loader->add_filter( 'wp_get_nav_menu_items', $plugin_public, 'set_logout_menu_item_url', 10, 3 );
		$this->loader->add_filter( 'e_addons/dynamic', $plugin_public, 'parse_api_fields', 10, 3 );
		$this->loader->add_filter( 'get_avatar_url', $plugin_public, 'discourse_get_avatar_url', 10, 3 );
	}

	/**
	 * Register all of the shortcodes loaded by the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shortcodes() {

		// Shortcodes.
		add_shortcode(
			'wpse_comments_template',
			function( $atts = [], $content = '' ) {
				if ( is_singular() && post_type_supports( get_post_type(), 'comments' ) ) {
					ob_start();
					comments_template();
					add_filter( 'comments_open', 'wpse_comments_open' );
					add_filter( 'get_comments_number', 'wpse_comments_number' );
					return ob_get_clean();
				}
				return '';
			}
		);

		add_shortcode(
			'froware_import_event',
			function( $atts = [] ) {
				$plugin_public = new Froware_Public( $this->get_plugin_name(), $this->get_version() );

        // phpcs:ignore
				if ( ! empty( $_POST ) ) {
					esc_attr_e( 'Form submitted' );
				} else {
					$plugin_public->event_import_form();
				}
			}
		);

		add_shortcode(
			'frocentric_post_format',
			function( $atts = [] ) {
				if ( ! empty( get_post() ) ) {
					$format = get_post_format();

					if ( empty( $format ) ) {
						$format = 'standard';
					}

					$format_string = get_post_format_string( $format );

					if ( $format_string === 'Standard' ) {
						$format_string = 'Text';
					}

					$icon = $this->get_icon( $format );

					return '<a href="/type/post-format-' . $format . '" class="dashicons dashicons-' . $icon . '" title="' . esc_attr( 'View all ' . strtolower( $format_string ) . ' format posts' ) . '">' . $format_string . '</a>';
				} else {
					return '';
				}
			}
		);
	}

	// phpcs:ignore
	protected function get_icon( $format ) {
		switch ( $format ) {
			case 'aside':
				$icon = 'format-aside';
				break;
			case 'audio':
				$icon = 'microphone';
				break;
			case 'chat':
				$icon = 'format-chat';
				break;
			case 'gallery':
				$icon = 'format-gallery';
				break;
			case 'image':
				$icon = 'format-image';
				break;
			case 'link':
				$icon = 'admin-links';
				break;
			case 'quote':
				$icon = 'format-quote';
				break;
			case 'status':
				$icon = 'post-status';
				break;
			case 'video':
				$icon = 'video-alt3';
				break;
			default:
				$icon = 'text';
				break;
		}
	}

	/**
	 * Disables comments.
	 *
	 * @param    bool $open          Whether comments are open for the current post.
	 * @return   bool
	 */
	public function wpse_comments_open( $open ) {
		remove_filter( current_filter(), __FUNCTION__ );
		return false;
	}

	/**
	 * Sets comment count to 0.
	 *
	 * @param    bool $number          The number of comments on the current post.
	 * @return   int
	 */
	public function wpse_comments_number( $number ) {
		remove_filter( current_filter(), __FUNCTION__ );
		return 0;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Froware_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
