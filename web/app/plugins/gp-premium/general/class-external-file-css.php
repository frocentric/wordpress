<?php
/**
 * This file builds an external CSS file for our options.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Build and enqueue a dynamic stylsheet if needed.
 */
class GeneratePress_External_CSS_File {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.11.0
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.11.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_css' ), 20 );
		add_action( 'wp', array( $this, 'init' ), 9 );
		add_action( 'customize_save_after', array( $this, 'delete_saved_time' ) );
		add_action( 'customize_register', array( $this, 'add_customizer_field' ) );
		add_filter( 'generate_option_defaults', array( $this, 'add_option_default' ) );
		add_filter( 'generatepress_dynamic_css_print_method', array( $this, 'set_print_method' ) );

		if ( ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Just checking, false positive.
			add_action( 'wp_ajax_generatepress_regenerate_css_file', array( $this, 'regenerate_css_file' ) );
		}
	}

	/**
	 * Set our CSS Print Method default.
	 *
	 * @param array $defaults Our existing defaults.
	 */
	public function add_option_default( $defaults ) {
		$defaults['css_print_method'] = 'inline';

		return $defaults;
	}

	/**
	 * Add our option to the Customizer.
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	public function add_customizer_field( $wp_customize ) {
		if ( ! function_exists( 'generate_get_defaults' ) ) {
			return;
		}

		$defaults = generate_get_defaults();

		require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'GeneratePress_Action_Button_Control' );
		}

		$wp_customize->add_setting(
			'generate_settings[css_print_method]',
			array(
				'default' => $defaults['css_print_method'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'generate_settings[css_print_method]',
			array(
				'type' => 'select',
				'label' => __( 'Dynamic CSS Print Method', 'gp-premium' ),
				'description' => __( 'Generating your dynamic CSS in an external file offers significant performance advantages.', 'gp-premium' ),
				'section' => 'generate_general_section',
				'choices' => array(
					'inline' => __( 'Inline Embedding', 'gp-premium' ),
					'file' => __( 'External File', 'gp-premium' ),
				),
				'settings' => 'generate_settings[css_print_method]',
			)
		);

		$wp_customize->add_control(
			new GeneratePress_Action_Button_Control(
				$wp_customize,
				'generate_regenerate_external_css_file',
				array(
					'section' => 'generate_general_section',
					'data_type' => 'regenerate_external_css',
					'nonce' => esc_html( wp_create_nonce( 'generatepress_regenerate_css_file' ) ),
					'label' => __( 'Regenerate CSS File', 'gp-premium' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => 'generate_is_using_external_css_file_callback',
				)
			)
		);
	}

	/**
	 * Set our CSS Print Method.
	 *
	 * @param string $method The existing method.
	 */
	public function set_print_method( $method ) {
		if ( ! function_exists( 'generate_get_option' ) ) {
			return $method;
		}

		return generate_get_option( 'css_print_method' );
	}

	/**
	 * Determine if we're using file mode or inline mode.
	 */
	public function mode() {
		$mode = generate_get_css_print_method();

		if ( 'file' === $mode && $this->needs_update() ) {
			$data = get_option( 'generatepress_dynamic_css_data', array() );

			if ( ! isset( $data['updated_time'] ) ) {
				// No time set, so set the current time minus 5 seconds so the file is still generated.
				$data['updated_time'] = time() - 5;
				update_option( 'generatepress_dynamic_css_data', $data );
			}

			// Only allow processing 1 file every 5 seconds.
			$current_time = (int) time();
			$last_time    = (int) $data['updated_time'];

			if ( 5 <= ( $current_time - $last_time ) ) {

				// Attempt to write to the file.
				$mode = ( $this->can_write() && $this->make_css() ) ? 'file' : 'inline';

				// Does again if the file exists.
				if ( 'file' === $mode ) {
					$mode = ( file_exists( $this->file( 'path' ) ) ) ? 'file' : 'inline';
				}
			}
		}

		return $mode;
	}

	/**
	 * Set things up.
	 */
	public function init() {
		if ( 'file' === $this->mode() ) {
			add_filter( 'generate_using_dynamic_css_external_file', '__return_true' );
			add_filter( 'generate_dynamic_css_skip_cache', '__return_true', 20 );

			// Remove inline CSS in GP < 3.0.0.
			if ( ! function_exists( 'generate_get_dynamic_css' ) && function_exists( 'generate_enqueue_dynamic_css' ) ) {
				remove_action( 'wp_enqueue_scripts', 'generate_enqueue_dynamic_css', 50 );
			}
		}
	}

	/**
	 * Enqueue the dynamic CSS.
	 */
	public function enqueue_dynamic_css() {
		if ( 'file' === $this->mode() ) {
			wp_enqueue_style( 'generatepress-dynamic', esc_url( $this->file( 'uri' ) ), array( 'generate-style' ), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

			// Move the child theme after our dynamic stylesheet.
			if ( is_child_theme() && wp_style_is( 'generate-child', 'enqueued' ) ) {
				wp_dequeue_style( 'generate-child' );
				wp_enqueue_style( 'generate-child' );
			}

			// Re-add no-cache CSS in GP < 3.0.0.
			if ( ! function_exists( 'generate_get_dynamic_css' ) && function_exists( 'generate_no_cache_dynamic_css' ) ) {
				$nocache_css = generate_no_cache_dynamic_css();

				if ( function_exists( 'generate_do_icon_css' ) ) {
					$nocache_css .= generate_do_icon_css();
				}

				wp_add_inline_style( 'generate-style', wp_strip_all_tags( $nocache_css ) );
			}
		}
	}

	/**
	 * Make our CSS.
	 */
	public function make_css() {
		$content = '';

		if ( function_exists( 'generate_get_dynamic_css' ) ) {
			$content = generate_get_dynamic_css();
		} elseif ( function_exists( 'generate_base_css' ) && function_exists( 'generate_font_css' ) && function_exists( 'generate_advanced_css' ) && function_exists( 'generate_spacing_css' ) ) {
			$content = generate_base_css() . generate_font_css() . generate_advanced_css() . generate_spacing_css();
		}

		$content = apply_filters( 'generate_external_dynamic_css_output', $content );

		if ( ! $content ) {
			return false;
		}

		$filesystem = generate_premium_get_wp_filesystem();

		if ( ! $filesystem ) {
			return false;
		}

		// Take care of domain mapping.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {
			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {
				$mapped_domain = domain_mapping_siteurl( false );
				$original_domain = get_original_url( 'siteurl' );

				$content = str_replace( $original_domain, $mapped_domain, $content );
			}
		}

		if ( is_writable( $this->file( 'path' ) ) || ( ! file_exists( $this->file( 'path' ) ) && is_writable( dirname( $this->file( 'path' ) ) ) ) ) {
			$chmod_file = 0644;

			if ( defined( 'FS_CHMOD_FILE' ) ) {
				$chmod_file = FS_CHMOD_FILE;
			}

			if ( ! $filesystem->put_contents( $this->file( 'path' ), wp_strip_all_tags( $content ), $chmod_file ) ) {

				// Fail!
				return false;

			} else {

				$this->update_saved_time();

				// Success!
				return true;

			}
		}
	}

	/**
	 * Determines if the CSS file is writable.
	 */
	public function can_write() {
		global $blog_id;

		// Get the upload directory for this site.
		$upload_dir = wp_get_upload_dir();

		// If this is a multisite installation, append the blogid to the filename.
		$css_blog_id = ( is_multisite() && $blog_id > 1 ) ? '_blog-' . $blog_id : null;

		$file_name   = '/style' . $css_blog_id . '.min.css';
		$folder_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'generatepress';

		// Does the folder exist?
		if ( file_exists( $folder_path ) ) {
			// Folder exists, but is the folder writable?
			if ( ! is_writable( $folder_path ) ) {
				// Folder is not writable.
				// Does the file exist?
				if ( ! file_exists( $folder_path . $file_name ) ) {
					// File does not exist, therefore it can't be created
					// since the parent folder is not writable.
					return false;
				} else {
					// File exists, but is it writable?
					if ( ! is_writable( $folder_path . $file_name ) ) {
						// Nope, it's not writable.
						return false;
					}
				}
			} else {
				// The folder is writable.
				// Does the file exist?
				if ( file_exists( $folder_path . $file_name ) ) {
					// File exists.
					// Is it writable?
					if ( ! is_writable( $folder_path . $file_name ) ) {
						// Nope, it's not writable.
						return false;
					}
				}
			}
		} else {
			// Can we create the folder?
			// returns true if yes and false if not.
			return wp_mkdir_p( $folder_path );
		}

		// all is well!
		return true;
	}

	/**
	 * Gets the css path or url to the stylesheet
	 *
	 * @param string $target path/url.
	 */
	public function file( $target = 'path' ) {
		global $blog_id;

		// Get the upload directory for this site.
		$upload_dir = wp_get_upload_dir();

		// If this is a multisite installation, append the blogid to the filename.
		$css_blog_id = ( is_multisite() && $blog_id > 1 ) ? '_blog-' . $blog_id : null;

		$file_name   = 'style' . $css_blog_id . '.min.css';
		$folder_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'generatepress';

		// The complete path to the file.
		$file_path = $folder_path . DIRECTORY_SEPARATOR . $file_name;

		// Get the URL directory of the stylesheet.
		$css_uri_folder = $upload_dir['baseurl'];

		$css_uri = trailingslashit( $css_uri_folder ) . 'generatepress/' . $file_name;

		// Take care of domain mapping.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {
			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {
				$mapped_domain   = domain_mapping_siteurl( false );
				$original_domain = get_original_url( 'siteurl' );
				$css_uri         = str_replace( $original_domain, $mapped_domain, $css_uri );
			}
		}

		$css_uri = set_url_scheme( $css_uri );

		if ( 'path' === $target ) {
			return $file_path;
		} elseif ( 'url' === $target || 'uri' === $target ) {
			$timestamp = ( file_exists( $file_path ) ) ? '?ver=' . filemtime( $file_path ) : '';
			return $css_uri . $timestamp;
		}
	}

	/**
	 * Update the our updated file time.
	 */
	public function update_saved_time() {
		$data = get_option( 'generatepress_dynamic_css_data', array() );
		$data['updated_time'] = time();

		update_option( 'generatepress_dynamic_css_data', $data );
	}

	/**
	 * Delete the saved time.
	 */
	public function delete_saved_time() {
		$data = get_option( 'generatepress_dynamic_css_data', array() );

		if ( isset( $data['updated_time'] ) ) {
			unset( $data['updated_time'] );
		}

		update_option( 'generatepress_dynamic_css_data', $data );
	}

	/**
	 * Update our plugin/theme versions.
	 */
	public function update_versions() {
		$data = get_option( 'generatepress_dynamic_css_data', array() );

		$data['theme_version'] = GENERATE_VERSION;
		$data['plugin_version'] = GP_PREMIUM_VERSION;

		update_option( 'generatepress_dynamic_css_data', $data );
	}

	/**
	 * Do we need to update the CSS file?
	 */
	public function needs_update() {
		$data = get_option( 'generatepress_dynamic_css_data', array() );
		$update = false;

		// If there's no updated time, needs update.
		// The time is set in mode().
		if ( ! isset( $data['updated_time'] ) ) {
			$update = true;
		}

		// If we haven't set our versions, do so now.
		if ( ! isset( $data['theme_version'] ) && ! isset( $data['plugin_version'] ) ) {
			$update = true;
			$this->update_versions();

			// Bail early so we don't check undefined versions below.
			return $update;
		}

		// Version numbers have changed, needs update.
		if ( (string) GENERATE_VERSION !== (string) $data['theme_version'] || (string) GP_PREMIUM_VERSION !== (string) $data['plugin_version'] ) {
			$update = true;
			$this->update_versions();
		}

		return $update;
	}

	/**
	 * Regenerate the CSS file.
	 */
	public function regenerate_css_file() {
		check_ajax_referer( 'generatepress_regenerate_css_file', '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'gp-premium' ) );
		}

		$this->delete_saved_time();

		wp_send_json_success();
	}
}

GeneratePress_External_CSS_File::get_instance();
