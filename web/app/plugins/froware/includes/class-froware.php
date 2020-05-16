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
		$this->loader->add_action( 'save_post', $plugin_admin, 'set_canonical_url', 10, 3 );
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

		// Filters.
		$this->loader->add_filter( 'generate_typography_default_fonts', $plugin_public, 'add_generatepress_fonts' );
		$this->loader->add_filter( 'nav_menu_css_class', $plugin_public, 'special_nav_class', 10, 3 );
		$this->loader->add_filter( 'wpsp_defaults', $plugin_public, 'wpsp_defaults' );
		$this->loader->add_filter( 'wpematico_item_parsers', $plugin_public, 'wpematico_item_parsers_callback', 10, 4 );
		$this->loader->add_filter( 'the_excerpt_rss', $plugin_public, 'filter_content_feed', 999, 1 );
		$this->loader->add_filter( 'the_content_feed', $plugin_public, 'filter_content_feed', 999, 1 );

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
			function( $atts = array(), $content = '' ) {
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
