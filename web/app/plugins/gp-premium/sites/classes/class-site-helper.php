<?php
defined( 'WPINC' ) or die;

class GeneratePress_Sites_Helper {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.6
	 */
	private static $instance;

	/**
	 * Background processing.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.6
	 */
	public static $background_process;

	/**
	 * Initiator.
	 *
	 * @since 1.6
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'upload_mimes', array( $this, 'mime_types' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'check_real_mime_type' ), 10, 4 );
		add_filter( 'wie_widget_settings_array', array( $this, 'fix_custom_menu_widget_ids' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'add_svg_image_support' ), 10, 3 );

		// Batch Processing.
		require_once GP_LIBRARY_DIRECTORY . 'batch-processing/wp-async-request.php';
		require_once GP_LIBRARY_DIRECTORY . 'batch-processing/wp-background-process.php';
		require_once GENERATE_SITES_PATH . 'classes/class-sites-background-process.php';
		require_once GENERATE_SITES_PATH . 'classes/class-beaver-builder-batch-processing.php';
		require_once GENERATE_SITES_PATH . 'classes/class-site-import-image.php';

		self::$background_process = new GeneratePress_Site_Background_Process();

		if ( is_admin() ) {
			self::includes();
		}
	}

	/**
	 * Include necessary files.
	 *
	 * @since 1.6
	 */
	public static function includes() {

		// Content Importer
		if ( ! class_exists( 'WP_Importer' ) ) {
			require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
		}

		require_once GENERATE_SITES_PATH . 'libs/wxr-importer/WXRImporter.php';
		require_once GENERATE_SITES_PATH . 'libs/wxr-importer/WPImporterLogger.php';
		require_once GENERATE_SITES_PATH . 'libs/wxr-importer/WXRImportInfo.php';
		require_once GENERATE_SITES_PATH . 'classes/class-content-importer.php';

	}

	public static function get_mapped_term_ids() {
		return ( array ) get_transient( 'generatepress_sites_mapped_term_ids' );
	}

	public static function get_mapped_post_ids( $slug ) {
		return ( array ) get_option( "generatepress_sites_mapped_ids_{$slug}", array() );
	}

	/**
	 * Moves the existing widgets into the inactive area before new widgets are added.
	 * This prevents sites looking messed up due to existing widgets.
	 *
	 * @since 1.6
	 */
	public static function clear_widgets() {
		$data = get_option( 'sidebars_widgets' );

		$backup_data = get_option( '_generatepress_site_library_backup', array() );
		$backup_data['widgets'] = $data;

		// Set our backed up options.
		update_option( '_generatepress_site_library_backup', $backup_data );

		$all_widgets = array();

		foreach ( $data as $sidebar_id => $widgets ) {
			// Skip inactive widgets (should not be in export file).
			if ( 'wp_inactive_widgets' === $sidebar_id ) {
				continue;
			}

			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget_instance_id => $widget ) {
					$all_widgets[] = $widget;
				}
			}
		}

		$results['wp_inactive_widgets'] = $all_widgets;

		update_option( 'sidebars_widgets', $results );

	}

	/**
	 * Checks to see whether options exist or not.
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public static function do_options_exist() {
		$theme_mods = self::get_theme_mods();
		$settings = self::get_theme_settings();

		$has_data = array(
			'mods' => array(),
			'options' => array()
		);

		foreach ( $theme_mods as $theme_mod ) {
			if ( get_theme_mod( $theme_mod ) ) {
				$has_data['mods'][$theme_mod] = get_theme_mod( $theme_mod );
			}
		}

		foreach ( $settings as $setting ) {
			if ( get_option( $setting ) ) {

				// The blog module runs a migration script on activation for now. This checks if those migrated values have been changed.
				if ( 'generate_blog_settings' === $setting && function_exists( 'generate_blog_get_defaults' ) ) {
					$defaults = generate_blog_get_defaults();
					$options = get_option( $setting );
					$diff = array();

					foreach ( $options as $option => $value ) {
						if ( isset( $defaults[ $option ] ) && $value !== $defaults[ $option ] ) {
							$diff[ $option ] = $value;
						}
					}

					if ( empty( $diff ) ) {
						continue;
					}
				}

				$has_data['options'][$setting] = get_option( $setting );
			}
		}

		if ( ! array_filter( $has_data ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Imports our content and custom CSS.
	 *
	 * @since 1.6
	 *
	 * @param string $path
	 */
	public static function import_xml( $path, $slug ) {
		$options  = array(
			'fetch_attachments' => true,
			'default_author'    => 0,
		);

		$current_css = wp_get_custom_css_post();

		$logger   = new GeneratePress\WPContentImporter2\WPImporterLogger();
		$importer = new GeneratePress_Sites_Content_Importer( $options );
		$importer->set_logger( $logger );
		$result = $importer->import( $path );

		// Get all mapped post and term data.
		$existing_data = self::get_mapped_post_ids( $slug );
		$mapped_data = $importer->get_importer_data();
		$mapped_posts = $mapped_data['mapping']['post'];

		// Merge exiting mapped posts with any new ones. Existing posts don't get mapped, so we need to preserve them.
		$all_data = $mapped_posts + $existing_data;

		// Set our site specific mapped posts with all of our data.
		update_option( 'generatepress_sites_mapped_ids_' . $slug, $all_data, false );

		// Set mapped term IDs.
		// These are always the same, even if the site has been imported before. No fancy stuff needed.
		$term_mapping = $mapped_data['mapping']['term_id'];
		set_transient( 'generatepress_sites_mapped_term_ids', $term_mapping, 0.1 * HOUR_IN_SECONDS );

		wp_update_custom_css_post( $current_css->post_content );

		// Page builders need so much extra work.
		self::update_page_builder_content();

		// Log our content
		//self::log( $logger );
	}

	/**
	 * List plugins that have a pro version.
	 *
	 * We want to check to see if these exist before installing or activating
	 * the free versions.
	 *
	 * @since 1.6
	 *
	 * @return array
	 */
	public static function check_for_pro_plugins() {
		return apply_filters( 'generate_sites_pro_plugins', array(
			'beaver-builder-lite-version/fl-builder.php' => 'bb-plugin/fl-builder.php',
			'ultimate-addons-for-beaver-builder-lite/bb-ultimate-addon.php' => 'bb-ultimate-addon/bb-ultimate-addon.php',
			'powerpack-addon-for-beaver-builder/bb-powerpack-lite.php' => 'bbpowerpack/bb-powerpack.php',
		) );
	}

	/**
	 * Check to see if required plugins are active.
	 *
	 * @since 1.6
	 *
	 * @param string $plugin
	 */
	public static function is_plugin_installed( $plugin ) {
		$pro_plugins = self::check_for_pro_plugins();

		// Check to see if this plugin has a pro version.
		if ( array_key_exists( $plugin, $pro_plugins ) ) {
			if ( file_exists( WP_PLUGIN_DIR . '/' . $pro_plugins[$plugin] ) ) {
				return true;
			}
		}

		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Allow SVG images.
	 *
	 * @since 1.6
	 *
	 * @param array $response  Attachment response.
	 * @param object $attachment Attachment object.
	 * @param array $meta Attachment meta data.
	 */
	public static function add_svg_image_support( $response, $attachment, $meta ) {
		if ( ! function_exists( 'simplexml_load_file' ) ) {
			return $response;
		}

		if ( ! empty( $response['sizes'] ) ) {
			return $response;
		}

		if ( 'image/svg+xml' !== $response['mime'] ) {
			return $response;
		}

		$svg_path = get_attached_file( $attachment->ID );

		$dimensions = self::get_svg_dimensions( $svg_path );

		$response['sizes'] = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => $dimensions->width,
				'height'      => $dimensions->height,
				'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait',
			),
		);

		return $response;
	}

	/**
	 * Get the dimensions of the uploaded SVG.
	 *
	 * @since 1.6
	 *
	 * @param string $svg SVG file path.
	 * @return array Return SVG file height & width for valid SVG file.
	 */
	public static function get_svg_dimensions( $svg ) {
		$svg = simplexml_load_file( $svg );

		if ( false === $svg ) {
			$width  = '0';
			$height = '0';
		} else {
			$attributes = $svg->attributes();
			$width      = (string) $attributes->width;
			$height     = (string) $attributes->height;
		}

		return (object) array(
			'width'  => $width,
			'height' => $height,
		);
	}

	/**
	 * Taken from the core media_sideload_image function and
	 * modified to return an array of data instead of html.
	 *
	 * @since 1.6
	 *
	 * @param string $file The image file path.
	 * @return array An array of image data.
	 */
	public static function sideload_image( $file ) {

		$data = new stdClass();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}

		if ( ! empty( $file ) ) {

			// Set variables for storage, fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpe?g|jpe|svg|gif|png)\b/i', $file, $matches );
			$file_array = array();
			$file_array['name'] = basename( $matches[0] );

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $file );

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array['tmp_name'];
			}

			// Do the validation and storage stuff.
			$id = media_handle_sideload( $file_array, 0 );

			// If error storing permanently, unlink.
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] );
				return $id;
			}

			// Build the object to return.
			$meta					= wp_get_attachment_metadata( $id );
			$data->attachment_id	= $id;
			$data->url				= wp_get_attachment_url( $id );
			$data->thumbnail_url	= wp_get_attachment_thumb_url( $id );

			if ( isset( $meta['height'] ) ) {
				$data->height			= $meta['height'];
			}

			if ( isset( $meta['width'] ) ) {
				$data->width			= $meta['width'];
			}
		}

		return $data;

	}

	/**
	 * Re-maps menu locations.
	 *
	 * @since 1.6
	 *
	 * @param array Incoming locations.
	 */
	public static function set_nav_menu_locations( $locations = array() ) {
		$menu_locations = array();

		$term_ids = self::get_mapped_term_ids();

		if ( isset( $locations ) ) {
			self::log( '== Start mapping menu locations ==' );

			foreach ( $locations as $menu => $value ) {
				if ( empty( $value ) ) {
					continue;
				}

				$menu_locations[$menu] = $term_ids[$value];
				self::log( $value . ' -> ' . $term_ids[$value] );
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}
	}

	/**
	 * Re-maps the global Page Header locations.
	 *
	 * @since 1.6
	 *
	 * @param array Incoming locations.
	 */
	public static function set_global_page_header_locations( $locations = array(), $slug ) {
		$new_locations = array();

		$post_ids = self::get_mapped_post_ids( $slug );

		if ( isset( $locations ) && ! empty( $locations ) ) {
			self::log( '== Start mapping global page headers ==' );

			foreach( $locations as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}

				$new_locations[$key] = $post_ids[$value];
				self::log( $value . ' -> ' . $post_ids[$value] );
			}

			update_option( 'generate_page_header_global_locations', $new_locations );
		}
	}

	/**
	 * Re-maps the per-page Page Headers.
	 *
	 * @since 1.6
	 *
	 * @param array Incoming locations.
	 */
	public static function set_page_headers( $headers = array(), $slug ) {
		$new_headers = array();

		$post_ids = self::get_mapped_post_ids( $slug );

		if ( isset( $headers ) && ! empty( $headers ) ) {
			self::log( '== Start mapping individual page headers ==' );

			foreach ( $headers as $post_id => $header_id ) {
				update_post_meta( $post_ids[$post_id], '_generate-select-page-header', $post_ids[$header_id] );
				self::log( $header_id . ' -> ' . $post_ids[$header_id] );
			}
		}
	}

	/**
	 * Re-maps the front page and posts page.
	 *
	 * @since 1.6
	 *
	 * @param string Name of the option to update.
	 * @param string Title of the page.
	 */
	public static function set_reading_pages( $name, $value, $slug ) {
		if ( empty( $value ) ) {
			return;
		}

		self::log( '== Start mapping front and blog pages ==' );

		// Get import data, with new menu IDs.
		$post_ids = self::get_mapped_post_ids( $slug );

		update_option( $name, $post_ids[$value] );
		self::log( $value . ' -> ' . $post_ids[$value] );
	}

	/**
	 * Re-maps WooCommerce pages.
	 *
	 * @since 1.6
	 *
	 * @param string Name of the option to update.
	 * @param string Title of the page.
	 */
	public static function set_woocommerce_pages( $name, $value, $slug ) {
		if ( empty( $value ) ) {
			return;
		}

		self::log( '== Start mapping WooCommerce pages ==' );

		$post_ids = self::get_mapped_post_ids( $slug );

		update_option( $name, $post_ids[$value] );
		self::log( $value . ' -> ' . $post_ids[$value] );
	}

	/**
	 * Change the menu IDs in the custom menu widgets in the widget import data.
	 * This solves the issue with custom menu widgets not having the correct (new) menu ID, because they
	 * have the old menu ID from the export site.
	 *
	 * @param array $widget The widget settings array.
	 */
	public static function fix_custom_menu_widget_ids( $widget ) {
		// Skip (no changes needed), if this is not a custom menu widget.
		if ( ! array_key_exists( 'nav_menu', $widget ) || empty( $widget['nav_menu'] ) || ! is_int( $widget['nav_menu'] ) ) {
			return $widget;
		}

		// Get import data, with new menu IDs.
		$term_ids = self::get_mapped_term_ids();

		if ( ! isset( $term_ids[ $widget['nav_menu'] ] ) ) {
			return $widget;
		}

		self::log( '== Start mapping navigation widgets ==' );
		self::log( $widget['nav_menu'] . ' -> ' . $term_ids[ $widget['nav_menu'] ] );

		// Set the new menu ID for the widget.
		$widget['nav_menu'] = $term_ids[ $widget['nav_menu'] ];

		return $widget;
	}

	/**
	 * Re-maps the element locations.
	 *
	 * @since 1.7
	 *
	 * @param array Incoming locations.
	 */
	public static function set_element_locations( $locations = array(), $slug ) {
		$post_ids = self::get_mapped_post_ids( $slug );

		if ( isset( $locations ) && ! empty( $locations ) ) {
			self::log( '== Start mapping element locations ==' );

			foreach( $locations as $key => $value ) {
				$new_locations = array();
				if ( empty( $value ) ) {
					continue;
				}

				foreach ( ( array ) $value as $data ) {
					if ( $data['object'] ) {
						self::log( $data['object'] . ' -> ' . $post_ids[$data['object']] );
						$data['object'] = $post_ids[$data['object']];
					}

					$new_locations[] = $data;
				}

				update_post_meta( $post_ids[$key], '_generate_element_display_conditions', $new_locations );
			}
		}
	}

	/**
	 * Re-maps the element exclusions.
	 *
	 * @since 1.7
	 *
	 * @param array Incoming locations.
	 */
	public static function set_element_exclusions( $locations = array(), $slug ) {
		$post_ids = self::get_mapped_post_ids( $slug );

		if ( isset( $locations ) && ! empty( $locations ) ) {
			self::log( '== Start mapping element exclusions ==' );

			foreach( $locations as $key => $value ) {
				$new_locations = array();
				if ( empty( $value ) ) {
					continue;
				}

				foreach ( ( array ) $value as $data ) {
					if ( $data['object'] ) {
						self::log( $data['object'] . ' -> ' . $post_ids[$data['object']] );
						$data['object'] = $post_ids[$data['object']];
					}

					$new_locations[] = $data;
				}

				update_post_meta( $post_ids[$key], '_generate_element_exclude_conditions', $new_locations );
			}
		}
	}

	/**
	 * Update menu URLs.
	 *
	 * @since 1.7.3
	 *
	 * @param string Preview URL
	 */
	public static function update_menu_urls( $url ) {
		$args = array (
			'post_type' 	=> 'nav_menu_item',
			'fields'		=> 'ids',
			'no_found_rows' => true,
			'post_status'	=> 'any',
			'numberposts'	=> 50,
		);

		$items = get_posts( $args );

		foreach ( $items as $item_id ) {
			$item_type = get_post_meta( $item_id, '_menu_item_type', true );

			if ( 'custom' === $item_type ) {
				$item_url = get_post_meta( $item_id, '_menu_item_url', true );

				if ( $item_url && '#' !== $item_url ) {
					$item_url = str_replace( $url, site_url(), $item_url );

					update_post_meta( $item_id, '_menu_item_url', $item_url );
				}
			}
		}
	}

	/**
	 * Allow other files types to be uploaded.
	 *
	 * @since 1.6
	 *
	 * @param array Existing types.
	 * @return array Merged types.
	 */
	public static function mime_types( $mimes ) {
		$mimes = array_merge(
			$mimes, array(
				'xml' => 'text/xml',
				'wie' => 'text/plain',
				'svg' => 'image/svg+xml',
				'svgz' => 'image/svg+xml'
			)
		);

		return $mimes;
	}

	/**
	 * Different MIME type of different PHP version
	 *
	 * Filters the "real" file type of the given file.
	 *
	 * @since 1.8
	 *
	 * @param array  $wp_check_filetype_and_ext File data array containing 'ext', 'type', and
	 *                                          'proper_filename' keys.
	 * @param string $file                      Full path to the file.
	 * @param string $filename                  The name of the file (may differ from $file due to
	 *                                          $file being in a tmp directory).
	 * @param array  $mimes                     Key is the file extension with value as the mime type.
	 */
	public static function check_real_mime_type( $defaults, $file, $filename, $mimes ) {
		if ( 'content.xml' === $filename ) {
			$defaults['ext']  = 'xml';
			$defaults['type'] = 'text/xml';
		}

		if ( 'widgets.wie' === $filename ) {
			$defaults['ext']  = 'wie';
			$defaults['type'] = 'text/plain';
		}

		return $defaults;
	}

	/**
	 * Download a file to WordPress from a URL.
	 *
	 * @since 1.6
	 *
	 * @param string URL of the file.
	 * @return array
	 */
	public static function download_file( $file ) {
		// Gives us access to the download_url() and wp_handle_sideload() functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		// URL to the WordPress logo
		$url = $file;
		$timeout_seconds = 10;

		// Download file to temp dir
		$temp_file = download_url( $url, $timeout_seconds );

		if ( is_wp_error( $temp_file ) ) {

			return array(
				'success' => false,
				'data' => $temp_file->get_error_message()
			);

		}

		// Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => basename( $url ),
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array(
			'test_form' => false,
			'test_size' => true,
		);

		// Move the temporary file into the uploads directory
		$results = wp_handle_sideload( $file, $overrides );

		if ( empty( $results['error'] ) ) {

			return array(
				'success' => true,
				'data' => $results,
			);

		} else {

			return array(
				'success' => false,
				'error' => $results['error'],
			);

		}

	}

	/**
	 * Get data from the options.json file.
	 *
	 * @since 1.6
	 *
	 * @param string URL of the file.
	 * @return array
	 */
	public static function get_options( $url ) {
		$url = wp_safe_remote_get( esc_url( $url ) );

		if ( is_wp_error( $url ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $url ), true );
	}

	/**
	 * Check to see if a remote file exists.
	 *
	 * @since 1.6
	 *
	 * @param string URL of the file.
	 * @return bool
	 */
	public static function file_exists( $url ) {
		$response = wp_safe_remote_get( esc_url( $url ) );

		if ( is_wp_error( $response ) ) {
			self::log( $response->get_error_message() );
			return false;
		}

		return strlen( $response['body'] ) > 100 && ( '200' == $response['response']['code'] || '301' == $response['response']['code'] ) ? true : false;
	}

	/**
	 * Log events to the debug.log file.
	 *
	 * @since 1.6
	 * @param  mixed $log Log data.
	 * @return void
	 */
	public static function log( $log ) {
		if ( ! WP_DEBUG_LOG ) {
			return;
		}

		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}

	/**
	 * Get all posts to run through batch processing.
	 *
	 * @since 1.6
	 *
	 * @return object All posts.
	 */
	public static function get_all_posts() {
		$args = array(
			'post_type'     => 'any',
			'fields'        => 'ids',
			'no_found_rows' => true,
			'post_status'   => 'publish',
			'numberposts'	=> -1,
			'meta_query'    => array(
				'relation' => 'OR',
		        array(
		            'key'       => '_fl_builder_data',
					'compare'	=> 'EXISTS',
		        ),
				array(
					'key'		=> '_elementor_data',
					'compare'	=> 'EXISTS',
				)
			)
		);

		$posts = get_posts( $args );

		if ( $posts ) {
			return $posts;
		}

		return false;
	}

	/**
	 * Searches Elementor and Beaver Builder content for images to download.
	 *
	 * @since 1.6
	 */
	public static function update_page_builder_content() {

		// Add "bb-plugin" in import [queue].
		// Add "beaver-builder-lite-version" in import [queue].
		if ( is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) || is_plugin_active( 'bb-plugin/fl-builder.php' ) ) {
			if ( class_exists( 'GeneratePress_Sites_Process_Beaver_Builder' ) ) {
				$import = new GeneratePress_Sites_Process_Beaver_Builder();
				self::$background_process->push_to_queue( $import );
			}
		}

		// Dispatch Queue.
		self::$background_process->save()->dispatch();
	}

	/**
	 * Clear Elementor & Beaver Builder caches when needed.
	 *
	 * @since 1.6
	 */
	public static function clear_page_builder_cache() {

		if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'delete_asset_cache_for_all_posts' ) ) {
			// Clear all cache.
			FLBuilderModel::delete_asset_cache_for_all_posts();
			self::log( 'Cleared Beaver Builder cache.' );
		}

		if ( class_exists( 'Elementor\Plugin' ) && method_exists( 'Elementor\Posts_CSS_Manager', 'clear_cache' ) ) {
			// !important, Clear the cache after images import.
			Elementor\Plugin::instance()->posts_css_manager->clear_cache();
			self::log( 'Cleared Elementor cache.' );
		}

	}

	/**
	 * List out GP option names.
	 *
	 * @since 1.6
	 *
	 * @return array
	 */
	public static function get_theme_settings() {
		return array(
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
	}

	/**
	 * List out GP theme mods.
	 *
	 * @since 1.6
	 *
	 * @return array
	 */
	public static function get_theme_mods() {
		return array(
			'font_body_variants',
			'font_body_category',
			'font_site_title_variants',
			'font_site_title_category',
			'font_site_tagline_variants',
			'font_site_tagline_category',
			'font_navigation_variants',
			'font_navigation_category',
			'font_secondary_navigation_variants',
			'font_secondary_navigation_category',
			'font_buttons_variants',
			'font_buttons_category',
			'font_heading_1_variants',
			'font_heading_1_category',
			'font_heading_2_variants',
			'font_heading_2_category',
			'font_heading_3_variants',
			'font_heading_3_category',
			'font_heading_4_variants',
			'font_heading_4_category',
			'font_heading_5_variants',
			'font_heading_5_category',
			'font_heading_6_variants',
			'font_heading_6_category',
			'font_widget_title_variants',
			'font_widget_title_category',
			'font_footer_variants',
			'font_footer_category',
			'generate_copyright',
		);
	}

	/**
	 * Build the loading icon.
	 *
	 * @since 1.6
	 */
	public static function loading_icon() {
		?>
		<svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="#000">
		    <g fill="none" fill-rule="evenodd" stroke-width="2">
		        <circle cx="22" cy="22" r="1">
		            <animate attributeName="r"
		                begin="0s" dur="1.8s"
		                values="1; 20"
		                calcMode="spline"
		                keyTimes="0; 1"
		                keySplines="0.165, 0.84, 0.44, 1"
		                repeatCount="indefinite" />
		            <animate attributeName="stroke-opacity"
		                begin="0s" dur="1.8s"
		                values="1; 0"
		                calcMode="spline"
		                keyTimes="0; 1"
		                keySplines="0.3, 0.61, 0.355, 1"
		                repeatCount="indefinite" />
		        </circle>
		        <circle cx="22" cy="22" r="1">
		            <animate attributeName="r"
		                begin="-0.9s" dur="1.8s"
		                values="1; 20"
		                calcMode="spline"
		                keyTimes="0; 1"
		                keySplines="0.165, 0.84, 0.44, 1"
		                repeatCount="indefinite" />
		            <animate attributeName="stroke-opacity"
		                begin="-0.9s" dur="1.8s"
		                values="1; 0"
		                calcMode="spline"
		                keyTimes="0; 1"
		                keySplines="0.3, 0.61, 0.355, 1"
		                repeatCount="indefinite" />
		        </circle>
		    </g>
		</svg>
		<?php
	}
}

GeneratePress_Sites_Helper::get_instance();
