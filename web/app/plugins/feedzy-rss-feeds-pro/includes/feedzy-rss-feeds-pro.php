<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
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
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */
class Feedzy_Rss_Feeds_Pro {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Feedzy_Rss_Feed_Pro_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * @since   1.0.0
	 * @access  public
	 */
	public function __construct() {
		$this->plugin_name = 'feedzy-rss-feeds-pro';
		$this->version     = '2.4.3';
		$this->loader      = new Feedzy_Rss_Feeds_Pro_Loader();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		$plugin_i18n = new Feedzy_Rss_Feeds_Pro_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the public functionality.
	 *
	 * @access   private
	 */
	private function define_public_hooks() {
		include_once FEEDZY_PRO_ABSPATH . '/includes/public/template-functions.php';
		include_once FEEDZY_PRO_ABSPATH . '/includes/public/amazon-api-functions.php';
		$plugin = new Feedzy_Rss_Feeds_Pro_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp', $plugin, 'wp' );
		$this->loader->add_filter( 'the_author', $plugin, 'the_author' );
		$this->loader->add_filter( 'author_link', $plugin, 'author_link', 10, 3 );
		$this->loader->add_filter( 'get_the_author_display_name', $plugin, 'the_author' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_ui = new Feedzy_Rss_Feeds_Pro_Ui( $this->get_plugin_name(), $this->get_version(), $this->loader );
		$this->loader->add_filter( 'feedzy_rss_feeds_ui_lang_filter', $plugin_ui, 'feedzy_add_tinymce_lang' );
		$this->loader->add_filter( 'feedzy_get_form_elements_filter', $plugin_ui, 'get_form_elements_pro' );

		$plugin_admin = new Feedzy_Rss_Feeds_Pro_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'feedzy_save_fields', $plugin_admin, 'save_feedzy_import_feed_meta', 2, 2 );
		$this->loader->add_action( 'wp_ajax_update_settings_page', $plugin_admin, 'update_settings_page' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'register_required_plugins' );
		$this->loader->add_action( 'feedzy_run_cron_extra', $plugin_admin, 'run_cron_extra', 10, 1 );
		$this->loader->add_action( 'feedzy_run_job_pre', $plugin_admin, 'run_job_pre', 10, 2 );
		$this->loader->add_action( 'feedzy_import_extra', $plugin_admin, 'import_extra', 10, 5 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init', 10, 1 );
		$this->loader->add_action( 'feedzy_metabox_show_rows', $plugin_admin, 'metabox_show_rows', 10, 3 );
		$this->loader->add_action( 'load-feedzy_page_feedzy-settings', $plugin_admin, 'view_feedzy_settings' );

		$this->loader->add_filter( 'feedzy_metabox_options', $plugin_admin, 'add_metabox_options', 10, 2 );
		$this->loader->add_filter( 'feedzy_render_view', $plugin_admin, 'render_view', 10, 2 );
		$this->loader->add_filter( 'feedzy_custom_field_template', $plugin_admin, 'custom_field_template', 10, 1 );
		$this->loader->add_filter( 'feedzy_import_feed_url', $plugin_admin, 'import_feed_url', 10, 3 );
		$this->loader->add_filter( 'feedzy_shortcode_options', $plugin_admin, 'run_cron_options', 10, 2 );
		$this->loader->add_filter( 'feedzy_run_status_errors', $plugin_admin, 'run_status_errors', 10, 2 );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2 );
		$this->loader->add_filter( 'feedzy_add_classes_item', $plugin_admin, 'add_grid_class', 10, 2 );
		$this->loader->add_filter( 'feedzy_item_keyword', $plugin_admin, 'item_additional_filter', 20, 4 );
		$this->loader->add_filter( 'feedzy_get_short_code_attributes_filter', $plugin_admin, 'feedzy_pro_get_short_code_attributes' );
		$this->loader->add_filter( 'feedzy_global_output', $plugin_admin, 'render_content', 10, 4 );
		$this->loader->add_filter( 'feedzy_item_url_filter', $plugin_admin, 'referral_url', 10, 2 );
		$this->loader->add_filter( 'feedzy_item_filter', $plugin_admin, 'add_data_to_item', 10, 5 );
		$this->loader->add_filter( 'feedzy_settings_tabs', $plugin_admin, 'settings_tabs', 11, 1 );
		$this->loader->add_filter( 'feedzy_magic_tags_title', $plugin_admin, 'magic_tags_title', 11 );
		$this->loader->add_filter( 'feedzy_magic_tags_date', $plugin_admin, 'magic_tags_date', 11 );
		$this->loader->add_filter( 'feedzy_magic_tags_content', $plugin_admin, 'magic_tags_content', 11 );
		$this->loader->add_filter( 'feedzy_magic_tags_image', $plugin_admin, 'magic_tags_image', 11 );
		$this->loader->add_filter( 'feedzy_agency_magic_tags_title', $plugin_admin, 'agency_magic_tags_title', 12 );
		$this->loader->add_filter( 'feedzy_agency_magic_tags_date', $plugin_admin, 'agency_magic_tags_date', 12 );
		$this->loader->add_filter( 'feedzy_agency_magic_tags_image', $plugin_admin, 'agency_magic_tags_image', 12 );
		$this->loader->add_filter( 'feedzy_invoke_services', $plugin_admin, 'invoke_services', 10, 4 );
		$this->loader->add_filter( 'feedzy_parse_custom_tags', $plugin_admin, 'parse_custom_tags', 10, 2 );
		$this->loader->add_filter( 'feedzy_get_service_magic_tags', $plugin_admin, 'get_service_magic_tags', 10, 2 );
		$this->loader->add_filter( 'feedzy_extract_from_custom_tag', $plugin_admin, 'extract_from_custom_tag', 10, 5 );
		$this->loader->add_filter( 'feedzy_invoke_content_rewrite_services', $plugin_admin, 'invoke_content_rewrite_services', 10, 4 );
		$this->loader->add_filter( 'feedzy_invoke_auto_translate_services', $plugin_admin, 'invoke_auto_translate_services', 10, 6 );
		$this->loader->add_filter( 'feedzy_invoke_content_openai_services', $plugin_admin, 'invoke_content_openai_services', 10, 2 );
		$this->loader->add_filter( 'feedzy_invoke_content_summarize_service', $plugin_admin, 'invoke_content_summarize_service', 10, 2 );
		$this->loader->add_filter( 'feedzy_invoke_image_generate_service', $plugin_admin, 'invoke_image_generate_service', 10, 2 );

		// Text spinner.
		$this->loader->add_filter( 'feedzy_parse_custom_tags', $plugin_admin, 'feedzy_text_spinner', 10, 1 );

		$plugin_widget = new Feedzy_Rss_Feeds_Pro_Widget( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'feedzy_widget_form_filter', $plugin_widget, 'feedzy_pro_form_widget', 11, 3 );
		$this->loader->add_filter( 'feedzy_widget_update_filter', $plugin_widget, 'feedzy_pro_widget_update', 11, 3 );
		$this->loader->add_filter( 'feedzy_widget_shortcode_attributes_filter', $plugin_widget, 'feedzy_pro_widget_shortcode_attributes', 11, 3 );

		// Load elementor action and filter.
		$plugin_elementor_widget = new Feedzy_Rss_Feeds_Pro_Elementor();
		$this->loader->add_action( 'elementor/dynamic_tags/register', $plugin_elementor_widget, 'feedzy_elementor_register_dynamic_tags' );
		$this->loader->add_action( 'elementor/documents/register', $plugin_elementor_widget, 'feedzy_elementor_register_document' );
		$this->loader->add_action( 'elementor/widgets/register', $plugin_elementor_widget, 'feedzy_elementor_widgets_registered' );
		$this->loader->add_filter( 'elementor/template-library/create_new_dialog_types', $plugin_elementor_widget, 'feedzy_elementor_dialog_types', 10, 2 );

		if ( defined( 'TI_CYPRESS_TESTING' ) ) {
			$this->load_cypress_hooks();
		}
	}

	/**
	 * Define the hooks that are needed for cypress.
	 *
	 * @since   ?
	 * @access  private
	 */
	private function load_cypress_hooks() {
		add_filter(
			'feedzy_items_limit',
			function( $limit ) {
				return range( 1, 10, 1 );
			},
			10,
			1
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return  string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return  Feedzy_Rss_Feeds_Pro_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return  string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Checks if the free version is older than a particular version.
	 *
	 * @since 3.3.0
	 */
	public static function is_free_older_than( $version ) {
		return version_compare( Feedzy_Rss_Feeds::instance()->get_version(), $version, '<' );
	}

}
