<?php
/**
 * Rest API functions
 *
 * @package GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GenerateBlocks_Rest
 */
class GeneratePress_Pro_Rest extends WP_REST_Controller {
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
	protected $namespace = 'generatepress-pro/v';

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
	 * GeneratePress_Pro_Rest constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$namespace = $this->namespace . $this->version;

		register_rest_route(
			$namespace,
			'/modules/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_module' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/license/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_licensing' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/beta/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_beta_testing' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/export/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'export' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/import/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'import' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/reset/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'reset' ),
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
	 * Update modules.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function update_module( WP_REST_Request $request ) {
		$module_key = $request->get_param( 'key' );
		$action = $request->get_param( 'action' );
		$current_setting = get_option( $module_key, false );
		$modules = GeneratePress_Pro_Dashboard::get_modules();
		$safe_module_keys = array();

		foreach ( $modules as $key => $data ) {
			$safe_module_keys[] = $data['key'];
		}

		if ( ! in_array( $module_key, $safe_module_keys ) ) {
			return $this->failed( 'Bad module key.' );
		}

		$message = '';

		if ( 'activate' === $action ) {
			update_option( $module_key, 'activated' );
			$message = __( 'Module activated.', 'gp-premium' );
		}

		if ( 'deactivate' === $action ) {
			update_option( $module_key, 'deactivated' );
			$message = __( 'Module deactivated.', 'gp-premium' );
		}

		return $this->success( $message );
	}

	/**
	 * Update licensing.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function update_licensing( WP_REST_Request $request ) {
		$new_license_key = $request->get_param( 'key' );
		$old_license = get_option( 'gen_premium_license_key', '' );
		$old_status = get_option( 'gen_premium_license_key_status', 'deactivated' );
		$new_license = strpos( $new_license_key, '***' ) !== false
			? trim( $old_license )
			: trim( $new_license_key );

		if ( $new_license ) {
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => sanitize_key( $new_license ),
				'item_name'  => rawurlencode( 'GP Premium' ),
				'url'        => home_url(),
			);
		} elseif ( $old_license && 'valid' === $old_status ) {
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => sanitize_key( $old_license ),
				'item_name'  => rawurlencode( 'GP Premium' ),
				'url'        => home_url(),
			);
		}

		if ( isset( $api_params ) ) {
			$response = wp_remote_post(
				'https://generatepress.com',
				array(
					'timeout' => 30,
					'sslverify' => false,
					'body' => $api_params,
				)
			);

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				if ( is_object( $response ) ) {
					return $this->failed( $response->get_error_message() );
				} elseif ( is_array( $response ) && isset( $response['response']['message'] ) ) {
					if ( 'Forbidden' === $response['response']['message'] ) {
						$message = __( '403 Forbidden. Your server is not able to communicate with generatepress.com in order to activate your license key.', 'gp-premium' );
					} else {
						$message = $response['response']['message'];
					}
				}
			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {
					switch ( $license_data->error ) {
						case 'expired':
							$message = sprintf(
								/* translators: License key expiration date. */
								__( 'Your license key expired on %s.', 'gp-premium' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) // phpcs:ignore
							);
							break;

						case 'disabled':
						case 'revoked':
							$message = __( 'Your license key has been disabled.', 'gp-premium' );
							break;

						case 'missing':
							$message = __( 'Invalid license.', 'gp-premium' );
							break;

						case 'invalid':
						case 'site_inactive':
							$message = __( 'Your license is not active for this URL.', 'gp-premium' );
							break;

						case 'item_name_mismatch':
							/* translators: GP Premium */
							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'gp-premium' ), __( 'GP Premium', 'gp-premium' ) );
							break;

						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.', 'gp-premium' );
							break;

						default:
							$message = __( 'An error occurred, please try again.', 'gp-premium' );
							break;
					}
				}
			}

			update_option( 'gen_premium_license_key_status', esc_attr( $license_data->license ) );
		}

		update_option( 'gen_premium_license_key', sanitize_key( $new_license ) );

		if ( ! isset( $api_params ) ) {
			return $this->success( __( 'Settings saved.', 'gp-premium' ) );
		}

		if ( ! empty( $message ) ) {
			return $this->failed( $message );
		}

		return $this->success( $license_data );
	}

	/**
	 * Update licensing.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function update_beta_testing( WP_REST_Request $request ) {
		$new_beta_tester = $request->get_param( 'beta' );

		if ( ! empty( $new_beta_tester ) ) {
			update_option( 'gp_premium_beta_testing', true, false );
		} else {
			delete_option( 'gp_premium_beta_testing' );
		}

		if ( ! isset( $api_params ) ) {
			return $this->success( __( 'Settings saved.', 'gp-premium' ) );
		}

		if ( ! empty( $message ) ) {
			return $this->failed( $message );
		}

		return $this->success( $license_data );
	}

	/**
	 * Export settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function export( WP_REST_Request $request ) {
		$exportable_modules = $request->get_param( 'items' );

		if ( ! $exportable_modules ) {
			$exportable_modules = GeneratePress_Pro_Dashboard::get_exportable_modules();
		}

		$export_type = $request->get_param( 'type' );

		if ( 'all' === $export_type ) {
			$data = array(
				'modules' => array(),
				'mods' => array(),
				'options' => array(),
			);

			$module_settings = array();

			foreach ( $exportable_modules as $exported_module_key => $exported_module_data ) {
				if ( isset( $exported_module_data['settings'] ) ) {
					$module_settings[] = $exported_module_data['settings'];
				}
			}

			$modules = GeneratePress_Pro_Dashboard::get_modules();

			// Export module status of the exported options.
			foreach ( $modules as $module_key => $module_data ) {
				if ( isset( $module_data['settings'] ) && in_array( $module_data['settings'], $module_settings ) ) {
					$data['modules'][ $module_key ] = $module_data['key'];
				}
			}

			$theme_mods = GeneratePress_Pro_Dashboard::get_theme_mods();

			foreach ( $theme_mods as $theme_mod ) {
				if ( 'generate_copyright' === $theme_mod ) {
					if ( in_array( 'copyright', $module_settings ) ) {
						$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
					}
				} else {
					if ( in_array( 'generate_settings', $module_settings ) ) {
						$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
					}
				}
			}

			$settings = GeneratePress_Pro_Dashboard::get_setting_keys();

			foreach ( $settings as $setting ) {
				if ( in_array( $setting, $module_settings ) ) {
					$data['options'][ $setting ] = get_option( $setting );
				}
			}
		}

		if ( 'global-colors' === $export_type ) {
			$data['global-colors'] = generate_get_option( 'global_colors' );
		}

		if ( 'typography' === $export_type ) {
			$data['font-manager'] = generate_get_option( 'font_manager' );
			$data['typography'] = generate_get_option( 'typography' );
		}

		$data = apply_filters( 'generate_export_data', $data, $export_type );

		return $this->success( $data );
	}

	/**
	 * Import settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function import( WP_REST_Request $request ) {
		$settings = $request->get_param( 'import' );

		if ( empty( $settings ) ) {
			$this->failed( __( 'No settings to import.', 'gp-premium' ) );
		}

		if ( ! empty( $settings['typography'] ) ) {
			$existing_settings = get_option( 'generate_settings', array() );
			$existing_settings['typography'] = $settings['typography'];

			if ( ! empty( $settings['font-manager'] ) ) {
				$existing_settings['font_manager'] = $settings['font-manager'];
			}

			update_option( 'generate_settings', $existing_settings );
		} elseif ( ! empty( $settings['global-colors'] ) ) {
			$existing_settings = get_option( 'generate_settings', array() );
			$existing_settings['global_colors'] = $settings['global-colors'];

			update_option( 'generate_settings', $existing_settings );
		} else {
			$modules = GeneratePress_Pro_Dashboard::get_modules();

			foreach ( (array) $settings['modules'] as $key => $val ) {
				if ( isset( $modules[ $key ] ) && in_array( $val, $modules[ $key ] ) ) {
					update_option( $val, 'activated' );
				}
			}

			foreach ( (array) $settings['mods'] as $key => $val ) {
				if ( in_array( $key, GeneratePress_Pro_Dashboard::get_theme_mods() ) ) {
					set_theme_mod( $key, $val );
				}
			}

			foreach ( (array) $settings['options'] as $key => $val ) {
				if ( in_array( $key, GeneratePress_Pro_Dashboard::get_setting_keys() ) ) {
					update_option( $key, $val );
				}
			}
		}

		// Delete existing dynamic CSS cache.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		$dynamic_css_data = get_option( 'generatepress_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'generatepress_dynamic_css_data', $dynamic_css_data );

		return $this->success( __( 'Settings imported.', 'gp-premium' ) );
	}

	/**
	 * Reset settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function reset( WP_REST_Request $request ) {
		$reset_items = $request->get_param( 'items' );

		if ( ! $reset_items ) {
			$reset_items = GeneratePress_Pro_Dashboard::get_exportable_modules();
		}

		$module_settings = array();

		foreach ( $reset_items as $reset_module_key => $reset_module_data ) {
			if ( isset( $reset_module_data['settings'] ) ) {
				$module_settings[] = $reset_module_data['settings'];
			}
		}

		$theme_mods = GeneratePress_Pro_Dashboard::get_theme_mods();

		foreach ( $theme_mods as $theme_mod ) {
			if ( 'generate_copyright' === $theme_mod ) {
				if ( in_array( 'copyright', $module_settings ) ) {
					remove_theme_mod( $theme_mod );
				}
			} else {
				if ( in_array( 'generate_settings', $module_settings ) ) {
					remove_theme_mod( $theme_mod );
				}
			}
		}

		$settings = GeneratePress_Pro_Dashboard::get_setting_keys();

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, $module_settings ) ) {
				delete_option( $setting );
			}
		}

		// Delete our dynamic CSS option.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		// Reset our dynamic CSS file updated time so it regenerates.
		$dynamic_css_data = get_option( 'generatepress_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'generatepress_dynamic_css_data', $dynamic_css_data );

		// Delete any GeneratePress Site CSS in Additional CSS.
		$additional_css = wp_get_custom_css_post();

		if ( ! empty( $additional_css ) ) {
			$additional_css->post_content = preg_replace( '#(/\\* GeneratePress Site CSS \\*/).*?(/\\* End GeneratePress Site CSS \\*/)#s', '', $additional_css->post_content );
			wp_update_custom_css_post( $additional_css->post_content );
		}

		return $this->success( __( 'Settings reset.', 'gp-premium' ) );
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

GeneratePress_Pro_Rest::get_instance();
