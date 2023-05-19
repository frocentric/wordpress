<?php
/**
 * Rest API functions
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GeneratePress_Site_Library_Rest
 */
class GeneratePress_Site_Library_Rest extends WP_REST_Controller {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 */
	private static $instance;

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'generatepress-site-library/v';

	/**
	 * Version.
	 *
	 * @var string
	 */
	protected $version = '1';

	/**
	 * Initiator.
	 *
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * GenerateBlocks_Rest constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'init', array( 'GeneratePress_Site_Library_Helper', 'woocommerce_no_new_pages' ), 4 );
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$namespace = $this->namespace . $this->version;

		// Get Templates.
		register_rest_route(
			$namespace,
			'/get_sites/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'get_sites' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/get_site_data/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'get_site_data' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_theme_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/activate_plugins/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'activate_plugins' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_content/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_content' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_site_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_site_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_widgets/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_widgets' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/restore_theme_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'restore_theme_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/restore_content/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'restore_content' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);
	}

	/**
	 * Get edit options permissions.
	 *
	 * @return bool
	 */
	public function update_settings_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function get_sites( WP_REST_Request $request ) {
		$force_refresh = $request->get_param( 'forceRefresh' );
		$sites = get_option( 'generatepress_sites', array() );

		$time_now = strtotime( 'now' );
		$sites_expire = get_option( 'generatepress_sites_expiration', sanitize_text_field( $time_now ) );

		if ( $force_refresh || empty( $sites ) || $sites_expire < $time_now ) {
			$sites = array();

			$data = wp_safe_remote_get( 'https://gpsites.co/wp-json/wp/v2/sites?per_page=100' );

			if ( is_wp_error( $data ) ) {
				update_option( 'generatepress_sites', 'no results', false );
				update_option( 'generatepress_sites_expiration', strtotime( '+5 minutes' ), false );
				return $this->failed( 'no results' );
			}

			$data = json_decode( wp_remote_retrieve_body( $data ), true );

			if ( ! is_array( $data ) ) {
				update_option( 'generatepress_sites', 'no results', false );
				update_option( 'generatepress_sites_expiration', strtotime( '+5 minutes' ), false );
				return $this->failed( 'no results' );
			}

			foreach ( (array) $data as $site ) {
				$sites[ $site['name'] ] = array(
					'name'              => $site['name'],
					'directory'         => $site['directory'],
					'preview_url'       => $site['preview_url'],
					'author_name'       => $site['author_name'],
					'author_url'        => $site['author_url'],
					'description'       => $site['description'],
					'page_builder'      => $site['page_builder'],
					'category'          => $site['category'],
					'min_version'       => $site['min_version'],
					'min_theme_version' => $site['min_theme_version'],
					'min_generateblocks_version' => $site['min_generateblocks_version'],
					'uploads_url'       => $site['uploads_url'],
					'plugins'           => $site['plugins'],
					'documentation'     => $site['documentation'],
					'image_width'       => ! empty( $site['image_width'] ) ? $site['image_width'] : 600,
					'image_height'      => ! empty( $site['image_height'] ) ? $site['image_height'] : 600,
				);
			}

			update_option( 'generatepress_sites', $sites, false );
			update_option( 'generatepress_sites_expiration', strtotime( '+1 day' ), false );
		}

		$sites = apply_filters( 'generate_add_sites', $sites );

		return $this->success( $sites );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function get_site_data( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );

		if ( GeneratePress_Site_Library_Helper::file_exists( $site_data['directory'] . '/options.json' ) ) {
			$settings = GeneratePress_Site_Library_Helper::get_options( $site_data['directory'] . '/options.json' );

			$data['options'] = true;
			$data['modules'] = $settings['modules'];
			$data['plugins'] = $settings['plugins'];

			if ( is_array( $data['plugins'] ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				$plugin_data = array();

				foreach ( $data['plugins'] as $name => $slug ) {
					$basename = strtok( $slug, '/' );
					$plugin_data[ $name ] = array(
						'name' => $name,
						'slug' => $slug,
						'installed' => GeneratePress_Site_Library_Helper::is_plugin_installed( $slug ) ? true : false,
						'active' => is_plugin_active( $slug ) ? true : false,
						'repo' => GeneratePress_Site_Library_Helper::file_exists( 'https://api.wordpress.org/plugins/info/1.0/' . $basename ) ? true : false,
					);
				}

				$data['plugin_data'] = $plugin_data;
			}
		}

		if ( GeneratePress_Site_Library_Helper::file_exists( $site_data['directory'] . '/content.xml' ) ) {
			$data['content'] = true;
		} else {
			$data['content'] = false;
		}

		if ( GeneratePress_Site_Library_Helper::file_exists( $site_data['directory'] . '/widgets.wie' ) ) {
			$data['widgets'] = true;
		} else {
			$data['widgets'] = false;
		}

		return $this->success( $data );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function import_options( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );

		if ( ! GeneratePress_Site_Library_Helper::file_exists( $site_data['directory'] . '/options.json' ) ) {
			return $this->failed( 'No theme options exist.' );
		}

		// Delete existing backup.
		delete_option( '_generatepress_site_library_backup' );

		// Backup options.
		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		$theme_mods = GeneratePress_Site_Library_Helper::get_theme_mods();
		$settings = GeneratePress_Site_Library_Helper::get_theme_settings();

		$data = array(
			'mods' => array(),
			'options' => array(),
		);

		foreach ( $theme_mods as $theme_mod ) {
			$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
		}

		foreach ( $settings as $setting ) {
			$data['options'][ $setting ] = get_option( $setting );
		}

		$backup_data['theme_options'] = $data;

		$modules = GeneratePress_Site_Library_Helper::premium_modules();

		$active_modules = array();
		foreach ( $modules as $name => $key ) {
			if ( 'activated' === get_option( $key ) ) {
				$active_modules[ $name ] = $key;
			}
		}

		$backup_data['modules'] = $active_modules;

		$settings = GeneratePress_Site_Library_Helper::get_options( $site_data['directory'] . '/options.json' );

		// Remove all existing theme options.
		$option_keys = array(
			'generate_settings',
			'generate_background_settings',
			'generate_blog_settings',
			'generate_hooks',
			'generate_page_header_settings',
			'generate_secondary_nav_settings',
			'generate_spacing_settings',
			'generate_menu_plus_settings',
			'generate_woocommerce_settings',
		);

		foreach ( $option_keys as $key ) {
			delete_option( $key );
		}

		// Need to backup these items before we remove all theme mods.
		$backup_data['site_options']['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
		$backup_data['site_options']['custom_logo'] = get_theme_mod( 'custom_logo' );

		// Remove existing theme mods.
		remove_theme_mods();

		// Remove existing activated premium modules.
		$premium_modules = GeneratePress_Site_Library_Helper::premium_modules();

		foreach ( $premium_modules as $name => $key ) {
			delete_option( $key );
		}

		// Activate necessary modules.
		foreach ( $settings['modules'] as $name => $key ) {
			// Only allow valid premium modules.
			if ( ! in_array( $key, $premium_modules ) ) {
				GeneratePress_Site_Library_Helper::log( 'Bad premium module key: ' . $key );
				continue;
			}

			update_option( $key, 'activated' );
		}

		// Set theme mods.
		foreach ( $settings['mods'] as $key => $val ) {
			// Only allow valid theme mods.
			if ( ! in_array( $key, GeneratePress_Site_Library_Helper::get_theme_mods() ) ) {
				GeneratePress_Site_Library_Helper::log( 'Bad theme mod key: ' . $key );
				continue;
			}

			set_theme_mod( $key, $val );
		}

		// Set theme options.
		foreach ( $settings['options'] as $key => $val ) {
			// Only allow valid options.
			if ( ! in_array( $key, GeneratePress_Site_Library_Helper::get_theme_settings() ) ) {
				GeneratePress_Site_Library_Helper::log( 'Bad theme setting key: ' . $key );
				continue;
			}

			if ( is_array( $val ) || is_object( $val ) ) {
				foreach ( $val as $option_name => $option_value ) {
					// Import any images.
					if ( is_string( $option_value ) && preg_match( '/\.(jpg|jpeg|png|gif)/i', $option_value ) ) {
						$data = GeneratePress_Site_Library_Helper::sideload_image( $option_value );

						if ( ! is_wp_error( $data ) ) {
							$val[ $option_name ] = $data->url;
						}
					}

					// Set these options if we import content.
					unset( $val['hide_title'] );
					unset( $val['hide_tagline'] );
					unset( $val['logo_width'] );
				}
			}

			update_option( $key, $val );
		}

		// Re-add non-theme option related theme mods.
		set_theme_mod( 'nav_menu_locations', $backup_data['site_options']['nav_menu_locations'] );
		set_theme_mod( 'custom_logo', $backup_data['site_options']['custom_logo'] );

		$existing_settings = get_option( 'generate_settings', array() );

		if ( isset( $backup_data['theme_options']['options']['generate_settings']['hide_title'] ) ) {
			$existing_settings['hide_title'] = $backup_data['theme_options']['options']['generate_settings']['hide_title'];
		}

		if ( isset( $backup_data['theme_options']['options']['generate_settings']['hide_tagline'] ) ) {
			$existing_settings['hide_tagline'] = $backup_data['theme_options']['options']['generate_settings']['hide_tagline'];
		}

		if ( isset( $backup_data['theme_options']['options']['generate_settings']['logo_width'] ) ) {
			$existing_settings['logo_width'] = $backup_data['theme_options']['options']['generate_settings']['logo_width'];
		}

		update_option( 'generate_settings', $existing_settings );

		// Remove dynamic CSS cache.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		$dynamic_css_data = get_option( 'generatepress_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'generatepress_dynamic_css_data', $dynamic_css_data );

		// Custom CSS.
		$css = $settings['custom_css'];
		$css = '/* GeneratePress Site CSS */ ' . $css . ' /* End GeneratePress Site CSS */';

		$current_css = wp_get_custom_css_post();

		if ( isset( $current_css->post_content ) ) {
			$current_css->post_content = preg_replace( '#(/\\* GeneratePress Site CSS \\*/).*?(/\\* End GeneratePress Site CSS \\*/)#s', '', $current_css->post_content );
			$css = $current_css->post_content . $css;
		}

		wp_update_custom_css_post( $css );

		update_option( '_generatepress_site_library_backup', $backup_data );

		return $this->success( __( 'Options imported', 'gp-premium' ) );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function activate_plugins( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );
		$settings = GeneratePress_Site_Library_Helper::get_options( $site_data['directory'] . '/options.json' );
		$plugins = $settings['plugins'];

		// Backup plugins.
		$backup_data = get_option( '_generatepress_site_library_backup', array() );
		$backup_data['plugins'] = get_option( 'active_plugins', array() );
		update_option( '_generatepress_site_library_backup', $backup_data );

		if ( ! empty( $plugins ) ) {
			$pro_plugins = GeneratePress_Site_Library_Helper::check_for_pro_plugins();
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			foreach ( $plugins as $plugin ) {
				// If the plugin has a pro version and it exists, activate it instead.
				if ( array_key_exists( $plugin, $pro_plugins ) ) {
					if ( file_exists( WP_PLUGIN_DIR . '/' . $pro_plugins[ $plugin ] ) ) {
						$plugin = $pro_plugins[ $plugin ];
					}
				}

				// Install BB lite if pro doesn't exist.
				if ( 'bb-plugin/fl-builder.php' === $plugin && ! file_exists( WP_PLUGIN_DIR . '/bb-plugin/fl-builder.php' ) ) {
					$plugin = 'beaver-builder-lite-version/fl-builder.php';
				}

				if ( ! is_plugin_active( $plugin ) ) {
					activate_plugin( $plugin, '', false, true );

					if ( 'woocommerce/woocommerce.php' === $plugin ) {
						add_option( 'generate_woocommerce_no_create_pages', true );
					}
				}
			}

			return $this->success( __( 'Plugins activated', 'gp-premium' ) );
		}
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function import_content( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );
		$site_slug = $request->get_param( 'siteSlug' );
		$import_options = $request->get_param( 'importOptions' );
		$import_content = $request->get_param( 'importContent' );

		// Increase PHP max execution time.
		set_time_limit( apply_filters( 'generate_sites_content_import_time_limit', 300 ) );

		$xml_path = $site_data['directory'] . '/content.xml';
		$xml_file = GeneratePress_Site_Library_Helper::download_file( $xml_path );
		$xml_path = $xml_file['data']['file'];

		if ( ! $xml_path ) {
			return $this->failed( 'No content found.' );
		}

		// Increase PHP max execution time.
		set_time_limit( apply_filters( 'generate_sites_content_import_time_limit', 300 ) );

		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( 'GeneratePress_Site_Library_Helper', 'track_post' ) );
		add_action( 'wxr_importer.processed.term', array( 'GeneratePress_Site_Library_Helper', 'track_term' ) );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		if ( ! apply_filters( 'generate_sites_regen_thumbnails', true ) ) {
			add_filter( 'intermediate_image_sizes_advanced', '__return_null' );
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );
		$backup_data['content'] = true;
		update_option( '_generatepress_site_library_backup', $backup_data );

		GeneratePress_Site_Library_Helper::import_xml( $xml_path, $site_slug );

		return $this->success( 'Content imported' );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function import_site_options( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );
		$site_slug = $request->get_param( 'siteSlug' );
		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		$settings = GeneratePress_Site_Library_Helper::get_options( $site_data['directory'] . '/options.json' );

		foreach ( $settings['site_options'] as $key => $val ) {
			switch ( $key ) {
				case 'page_for_posts':
				case 'page_on_front':
					$backup_data['site_options'][ $key ] = get_option( $key );
					GeneratePress_Site_Library_Helper::set_reading_pages( $key, $val, $site_slug );
					break;

				case 'woocommerce_shop_page_id':
				case 'woocommerce_cart_page_id':
				case 'woocommerce_checkout_page_id':
				case 'woocommerce_myaccount_page_id':
					$backup_data['site_options'][ $key ] = get_option( $key );
					GeneratePress_Site_Library_Helper::set_woocommerce_pages( $key, $val, $site_slug );
					break;

				case 'nav_menu_locations':
					if ( ! isset( $backup_data['site_options']['nav_menu_location'] ) ) {
						$backup_data['site_options']['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
					}

					GeneratePress_Site_Library_Helper::set_nav_menu_locations( $val );
					break;

				case 'element_locations':
					GeneratePress_Site_Library_Helper::set_element_locations( $val, $site_slug );
					break;

				case 'element_exclusions':
					GeneratePress_Site_Library_Helper::set_element_exclusions( $val, $site_slug );
					break;

				case 'custom_logo':
					if ( ! isset( $backup_data['site_options']['custom_logo'] ) ) {
						$backup_data['site_options']['custom_logo'] = get_theme_mod( 'custom_logo' );
					}

					$data = GeneratePress_Site_Library_Helper::sideload_image( $val );

					if ( ! is_wp_error( $data ) && isset( $data->attachment_id ) ) {
						set_theme_mod( 'custom_logo', $data->attachment_id );
						update_post_meta( $data->attachment_id, '_wp_attachment_is_custom_header', get_option( 'stylesheet' ) );
					} else {
						remove_theme_mod( 'custom_logo' );
					}

					break;

				default:
					if ( in_array( $key, (array) GeneratePress_Site_Library_Helper::disallowed_options() ) ) {
						GeneratePress_Site_Library_Helper::log( 'Disallowed option: ' . $key );
					} else {
						$backup_data['site_options'][ $key ] = get_option( $key );
						delete_option( $key );
						update_option( $key, $val );
					}
					break;
			}
		}

		// Set theme options.
		$theme_settings = get_option( 'generate_settings', array() );
		$update_theme_settings = false;

		foreach ( $settings['options'] as $key => $val ) {
			if ( 'generate_settings' !== $key ) {
				continue;
			}

			if ( is_array( $val ) || is_object( $val ) ) {
				foreach ( $val as $option_name => $option_value ) {
					if ( 'hide_title' === $option_name ) {
						$theme_settings['hide_title'] = $option_value;
						$update_theme_settings = true;
					}

					if ( 'hide_tagline' === $option_name ) {
						$theme_settings['hide_tagline'] = $option_value;
						$update_theme_settings = true;
					}

					if ( 'logo_width' === $option_name ) {
						$theme_settings['logo_width'] = $option_value;
						$update_theme_settings = true;
					}
				}
			}
		}

		if ( $update_theme_settings ) {
			update_option( 'generate_settings', $theme_settings );

			// Remove dynamic CSS cache.
			delete_option( 'generate_dynamic_css_output' );
			delete_option( 'generate_dynamic_css_cached_version' );

			$dynamic_css_data = get_option( 'generatepress_dynamic_css_data', array() );

			if ( isset( $dynamic_css_data['updated_time'] ) ) {
				unset( $dynamic_css_data['updated_time'] );
			}

			update_option( 'generatepress_dynamic_css_data', $dynamic_css_data );
		}

		// Set our backed up options.
		update_option( '_generatepress_site_library_backup', $backup_data );

		// Update any custom menu link URLs.
		GeneratePress_Site_Library_Helper::update_menu_urls( $site_data['preview_url'] );

		// Clear page builder cache.
		GeneratePress_Site_Library_Helper::clear_page_builder_cache();

		return $this->success( 'Site options imported' );
	}

	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function import_widgets( WP_REST_Request $request ) {
		$site_data = $request->get_param( 'siteData' );

		require_once GP_PREMIUM_DIR_PATH . 'site-library/classes/class-site-widget-importer.php';

		$widgets_path = $site_data['directory'] . '/widgets.wie';

		$wie_file = GeneratePress_Site_Library_Helper::download_file( $widgets_path );
		$wie_path = $wie_file['data']['file'];

		$data = implode( '', file( $wie_path ) );
		$data = json_decode( $data );

		GeneratePress_Site_Library_Helper::clear_widgets();

		add_filter( 'wie_widget_settings_array', array( 'GeneratePress_Site_Library_Helper', 'fix_custom_menu_widget_ids' ) );
		$widgets_importer = GeneratePress_Sites_Widget_Importer::instance();
		$widgets_importer->wie_import_data( $data );
		remove_filter( 'wie_widget_settings_array', array( 'GeneratePress_Site_Library_Helper', 'fix_custom_menu_widget_ids' ) );

		return $this->success( 'Widgets imported' );
	}

	/**
	 * Restore our theme options.
	 */
	public function restore_theme_options() {
		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		if ( ! empty( $backup_data ) ) {
			if ( ! empty( $backup_data['theme_options']['mods'] ) ) {
				remove_theme_mods();
			}

			if ( ! empty( $backup_data['theme_options']['options'] ) ) {
				$option_keys = array(
					'generate_settings',
					'generate_background_settings',
					'generate_blog_settings',
					'generate_hooks',
					'generate_page_header_settings',
					'generate_secondary_nav_settings',
					'generate_spacing_settings',
					'generate_menu_plus_settings',
					'generate_woocommerce_settings',
				);

				foreach ( $option_keys as $key ) {
					delete_option( $key );
				}
			}

			if ( ! empty( $backup_data['modules'] ) ) {
				$modules = GeneratePress_Site_Library_Helper::premium_modules();

				foreach ( $modules as $name => $key ) {
					delete_option( $key );
				}

				foreach ( (array) $backup_data['modules'] as $name => $key ) {
					update_option( $key, 'activated' );
				}
			}

			if ( ! empty( $backup_data['theme_options']['mods'] ) ) {
				foreach ( $backup_data['theme_options']['mods'] as $key => $val ) {
					// Only allow valid theme mods.
					if ( ! in_array( $key, GeneratePress_Site_Library_Helper::get_theme_mods() ) ) {
						GeneratePress_Site_Library_Helper::log( 'Bad theme mod key: ' . $key );
						continue;
					}

					set_theme_mod( $key, $val );
				}
			}

			if ( ! empty( $backup_data['theme_options']['options'] ) ) {
				foreach ( $backup_data['theme_options']['options'] as $key => $val ) {
					// Only allow valid options.
					if ( ! in_array( $key, GeneratePress_Site_Library_Helper::get_theme_settings() ) ) {
						GeneratePress_Site_Library_Helper::log( 'Bad theme setting key: ' . $key );
						continue;
					}

					update_option( $key, $val );
				}
			}

			// Re-add non-theme option related theme mods.
			if ( isset( $backup_data['site_options']['nav_menu_locations'] ) ) {
				set_theme_mod( 'nav_menu_locations', $backup_data['site_options']['nav_menu_locations'] );
			}

			if ( isset( $backup_data['site_options']['custom_logo'] ) ) {
				set_theme_mod( 'custom_logo', $backup_data['site_options']['custom_logo'] );
			}
		}

		return $this->success( __( 'Theme options restored.', 'gp-premium' ) );
	}

	/**
	 * Restore content.
	 */
	public function restore_content() {
		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		// Plugins.
		if ( ! empty( $backup_data['plugins'] ) && ! empty( $backup_data['site_options'] ) ) {
			update_option( 'active_plugins', $backup_data['plugins'] );
		}

		// Content.
		if ( ! empty( $backup_data ) ) {
			global $wpdb;
			$post_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_generatepress_sites_imported_post'" );
			$term_ids = $wpdb->get_col( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key='_generatepress_sites_imported_term'" );

			foreach ( $post_ids as $id ) {
				wp_delete_post( $id, true );
			}
		}

		// Site options.
		if ( ! empty( $backup_data['site_options'] ) ) {
			foreach ( $backup_data['site_options'] as $key => $val ) {
				if ( in_array( $key, (array) GeneratePress_Site_Library_Helper::disallowed_options() ) ) {
					GeneratePress_Site_Library_Helper::log( 'Disallowed option: ' . $key );
					continue;
				}

				if ( 'nav_menu_locations' === $key || 'custom_logo' === $key ) {
					set_theme_mod( $key, $val );
				} else {
					if ( ! $val && ! is_numeric( $val ) ) {
						delete_option( $key );
					} else {
						update_option( $key, $val );
					}
				}
			}
		}

		// Widgets.
		if ( ! empty( $backup_data['widgets'] ) ) {
			update_option( 'sidebars_widgets', $backup_data['widgets'] );
		}

		// CSS.
		$current_css = wp_get_custom_css_post();

		if ( isset( $current_css->post_content ) ) {
			// Remove existing library CSS.
			$current_css->post_content = preg_replace( '#(/\\* GeneratePress Site CSS \\*/).*?(/\\* End GeneratePress Site CSS \\*/)#s', '', $current_css->post_content );
		}

		wp_update_custom_css_post( $current_css->post_content );

		// Clean up.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );
		delete_option( '_generatepress_site_library_backup' );

		return $this->success( __( 'Content restored.', 'gp-premium' ) );
	}

	/**
	 * Success rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function success( $response ) {
		return new WP_REST_Response(
			array(
				'success'  => true,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Failed rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function failed( $response ) {
		return new WP_REST_Response(
			array(
				'success'  => false,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Error rest.
	 *
	 * @param mixed $code     error code.
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function error( $code, $response ) {
		return new WP_REST_Response(
			array(
				'error'      => true,
				'success'    => false,
				'error_code' => $code,
				'response'   => $response,
			),
			401
		);
	}
}

GeneratePress_Site_Library_Rest::get_instance();
