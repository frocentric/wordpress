<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_File_Uploads
 */
final class NF_FU_File_Uploads {

	/**
	 * @var NF_FU_File_Uploads
	 */
	private static $instance;

	/**
	 * @var string
	 */
	public $plugin_file_path;

	/**
	 * @var string
	 */
	public $plugin_name;

	/**
	 * @var stdClass
	 */
	public $controllers;

	/**
	 * @var NF_FU_Integrations_NinjaForms_MergeTags
	 */
	public $mergetags;

	/**
	 * @var NF_FU_Database_Models_Upload
	 */
	public $model;

	/**
	 * @var NF_FU_External_Loader
	 */
	public $externals;

	/**
	 * @var NF_FU_Admin_Menus_Uploads
	 */
	public $page;

	/**
	 * @var string
	 */
	protected $plugin_option_prefix;

	/**
	 * @var string
	 */
	public $plugin_version;

	/**
	 * @var string
	 */
	protected $class_prefix;

	/**
	 * File Upload field type
	 */
	const TYPE = 'file_upload';

	/**
	 * Main Plugin Instance
	 *
	 * Insures that only one instance of a plugin class exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @param string $plugin_file_path
	 * @param string $plugin_version
	 *
	 * @return NF_FU_File_Uploads Instance
	 */
	public static function instance( $plugin_file_path, $plugin_version ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NF_FU_File_Uploads ) ) {
			self::$instance = new NF_FU_File_Uploads();

			spl_autoload_register( array( self::$instance, 'autoloader' ) );

			// Initialize the class
			self::$instance->init( $plugin_file_path, $plugin_version );
		}

		return self::$instance;
	}

	/**
	 * Initialize the class.
	 *
	 * @param string $plugin_file_path
	 * @param string $plugin_version
	 */
	protected function init( $plugin_file_path, $plugin_version ) {
		$this->plugin_file_path     = $plugin_file_path;
		$this->plugin_name          = 'File Uploads';
		$this->plugin_option_prefix = 'uploads';
		$this->plugin_version       = $plugin_version;
		$this->class_prefix         = 'NF_FU';

		add_action( 'admin_init', array( $this, 'setup_license' ) );

		// Import Form Upgrade Routine for 3.0
		new NF_FU_Admin_Upgrade();

		if ( ! self::$instance->is_ninja_forms_three() ) {
			self::$instance->load_deprecated();

			return;
		}

		// This is THREE!
		add_filter( 'ninja_forms_register_fields', array( $this, 'register_field' ) );
		add_filter( 'ninja_forms_field_template_file_paths', array( $this, 'register_template_path' ) );
		add_action( 'ninja_forms_loaded', array( $this, 'load_plugin' ) );
		add_action( 'init', array( $this, 'load_translations' ) );
		add_action( 'ninja_forms_rollback', array( $this, 'handle_rollback' ) );
		add_filter( 'ninja_forms_telemetry_should_send', '__return_true' );

		// External services
		self::$instance->externals = new NF_FU_External_Loader();

		// Integrations
		self::$instance->mergetags = new NF_FU_Integrations_NinjaForms_MergeTags();
		new NF_FU_Integrations_NinjaForms_Submissions();
		new NF_FU_Integrations_NinjaForms_Attachments();
		new NF_FU_Integrations_NinjaForms_Templates();
		new NF_FU_Integrations_NinjaForms_Builder();
		new NF_FU_Integrations_PostCreation_PostCreation();
		new NF_FU_Integrations_SaveProgress_SaveProgress();
		new NF_FU_Integrations_Zapier_Zapier();
		new NF_FU_Integrations_PdfSubmissions_PdfSubmissions();
		if ( class_exists( 'NF_Styles' ) ) {
			new NF_FU_Integrations_LayoutStyles_LayoutStyles();
		}

		self::$instance->controllers               = new stdClass();
		self::$instance->controllers->settings     = new NF_FU_Admin_Controllers_Settings();
		self::$instance->controllers->custom_paths = new NF_FU_Admin_Controllers_CustomPaths();
		self::$instance->controllers->uploads      = new NF_FU_Admin_Controllers_Uploads();
	}

	/**
	 * Load all the 3.0+ plugin code
	 */
	public function load_plugin() {
		$this->install();

		$ajax_upload = new NF_FU_AJAX_Controllers_Uploads();
		$ajax_upload->init();


		self::$instance->model = new NF_FU_Database_Models_Upload();

		self::$instance->page = new NF_FU_Admin_Menus_Uploads();
		new NF_FU_Display_Render();

		Ninja_Forms()->merge_tags[ 'file_uploads' ] = new NF_FU_Integrations_NinjaForms_FileUploadMergeTags();
	}

	/**
	 * Register field
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function register_field( $fields ) {
		$fields[ self::TYPE ] = new NF_FU_Fields_Upload();

		return $fields;
	}

	/**
	 * Register the template path for the plugin
	 *
	 * @param array $file_paths
	 *
	 * @return array
	 */
	public function register_template_path( $file_paths ) {
		$file_paths[] = dirname( $this->plugin_file_path ) . '/includes/templates/';

		return $file_paths;
	}

	/**
	 * Install plugin
	 */
	public function install() {
		$migrations = new NF_FU_Database_Migrations();
		$migrations->migrate();
	}

	/**
	 * Check the site is running Ninja Forms THREE
	 *
	 * @return bool
	 */
	protected function is_ninja_forms_three() {
		if ( get_option( 'ninja_forms_load_deprecated', false ) ) {
			return false;
		}

		return version_compare( get_option( 'ninja_forms_version', '0' ), '3', '>='  );
	}

	/**
	 * Load the plugin for deprecated Ninja Form installs
	 */
	protected function load_deprecated() {
		require_once dirname( $this->plugin_file_path ) . '/deprecated/deprecated-file-uploads.php';
	}

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * class via the `new` operator from outside of this class.
	 */
	protected function __construct() {
	}

	/**
	 * As this class is a singleton it should not be clone-able
	 */
	protected function __clone() {
	}

	/**
	 * As this class is a singleton it should not be able to be unserialized
	 */
	public function __wakeup() {
	}

	/**
	 * Autoload the classes
	 *
	 * @param string $class_name
	 */
	public function autoloader( $class_name ) {
		if ( class_exists( $class_name ) ) {
			return;
		}

		$classes_dir = realpath( plugin_dir_path( $this->plugin_file_path ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;

		$this->maybe_load_class( $class_name, $this->class_prefix, $classes_dir );
	}

	/**
	 * Load class file
	 *
	 * @param string $class
	 * @param string $prefix
	 * @param string $dir
	 * @param bool   $preserve_case
	 */
	public function maybe_load_class( $class, $prefix, $dir, $preserve_case = false ) {
		if ( false === strpos( $class, $prefix ) ) {
			return;
		}

		$class_name = str_replace( $prefix, '', $class );
		$class_name = $preserve_case ? $class_name : strtolower( $class_name );
		$class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';

		if ( file_exists( $dir . $class_file ) ) {
			require_once $dir . $class_file;
		}
	}

	/**
	 * Licensing for the addon
	 */
	public function setup_license() {
		if ( ! class_exists( 'NF_Extension_Updater' ) ) {
			return;
		}

		new NF_Extension_Updater( $this->plugin_name, $this->plugin_version, 'WP Ninjas', $this->plugin_file_path, $this->plugin_option_prefix );
	}

	/**
	 * Config
	 *
	 * @param string $file_name
	 * @param array  $data
	 *
	 * @return mixed
	 */
	public function config( $file_name, $data = array() ) {
		extract( $data );

		return include dirname( $this->plugin_file_path ) . '/includes/config/' . $file_name . '.php';
	}

	/**
	 * Template
	 *
	 * @param string $file_name
	 * @param array  $data
	 *
	 * @return mixed
	 */
	public function template( $file_name, array $data = array() ) {
		extract( $data );

		$ext       = pathinfo( $file_name, PATHINFO_EXTENSION );
		$file_name = empty( $ext ) ? $file_name . '.php' : $file_name;

		return include dirname( $this->plugin_file_path ) . '/includes/templates/' . $file_name;
	}

	/**
	 * Load translations for add-on.
	 * First, look in WP_LANG_DIR subfolder, then fallback to add-on plugin folder.
	 */
	public function load_translations() {
		$textdomain = 'ninja-forms-uploads';

		$locale  = apply_filters( 'plugin_locale', get_locale(), $textdomain );
		$mo_file = $textdomain . '-' . $locale . '.mo';

		$wp_lang_dir = trailingslashit( WP_LANG_DIR ) . 'ninja-forms-uploads/';

		load_textdomain( $textdomain, $wp_lang_dir . $mo_file );

		$plugin_dir = trailingslashit( basename( dirname( $this->plugin_file_path ) ) );
		$lang_dir   = apply_filters( 'ninja_forms_uploads_lang_dir', $plugin_dir . 'languages/' );
		load_plugin_textdomain( $textdomain, false, $lang_dir );
	}

	/**
	 * Normalize the submission value for a file upload so we don't need to convert data
	 * and the plugin can use both formats in a pre and post 3.0 world
	 *
	 * @param array $value
	 *
	 * @return array
	 */
	public function normalize_submission_value( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$three = $this->is_ninja_forms_three();

		$clean_value = array();

		$first = reset( $value );
		if ( is_array( $first ) && isset( $first['user_file_name'] ) ) {
			// Pre 3.0 submission format
			if ( $three ) {
				foreach ( $value as $item ) {
					$clean_value[ $item['upload_id'] ] = $item['file_url'];
				}
			} else {
				$clean_value = $value;
			}
		} else {
			// New 3.0 submission format
			if ( $three ) {
				$clean_value = $value;
			} else {
				global $wpdb;
				foreach ( $value as $item ) {
					$upload = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . NINJA_FORMS_UPLOADS_TABLE_NAME . ' WHERE id = %d', $item['upload_id'] ) );
					if ( ! empty( $upload ) ) {
						$clean_value[ $item['upload_id'] ] = unserialize( $upload->data );
					}
				}
			}
		}

		return $clean_value;
	}

	/**
	 * Ensure the deprecated activation code is run on Ninja Forms 2.9.x rollback
	 */
	public function handle_rollback() {
		global $wpdb;
		if ( ! defined( 'NINJA_FORMS_UPLOADS_TABLE_NAME' ) ) {
			define( 'NINJA_FORMS_UPLOADS_TABLE_NAME', $wpdb->prefix . "ninja_forms_uploads" );
		}
		if ( ! defined( 'NINJA_FORMS_UPLOADS_VERSION' ) ) {
			define( "NINJA_FORMS_UPLOADS_VERSION", $this->plugin_version );
		}

		require_once dirname( $this->plugin_file_path ) . '/deprecated/includes/activation.php';

		ninja_forms_uploads_activation();
	}

	/**
	 * Create a nonce for the field along with expiry timestamp.
	 *
	 * @param int $field_id
	 *
	 * @return array
	 */
	public function createFieldNonce( $field_id ) {
		$nonce = array(
			'nonce' => wp_create_nonce( 'nf-file-upload-' . $field_id ),
			'nonce_expiry' => time() + wp_nonce_tick(),
		);

		return $nonce;
	}
}
