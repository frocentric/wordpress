<?php
namespace ElementorPro\License;

use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class API {

	const PRODUCT_NAME = 'Elementor Pro';

	const STORE_URL = 'https://my.elementor.com/api/v1/licenses/';
	const RENEW_URL = 'https://go.elementor.com/renew/';

	// License Statuses
	const STATUS_VALID = 'valid';
	const STATUS_INVALID = 'invalid';
	const STATUS_EXPIRED = 'expired';
	const STATUS_SITE_INACTIVE = 'site_inactive';
	const STATUS_DISABLED = 'disabled';

	// Features
	const FEATURE_PRO_TRIAL = 'pro_trial';

	// Requests lock config.
	const REQUEST_LOCK_TTL = MINUTE_IN_SECONDS;
	const REQUEST_LOCK_OPTION_NAME = '_elementor_pro_api_requests_lock';

	const TRANSIENT_KEY_PREFIX = 'elementor_pro_remote_info_api_data_';

	/**
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	private static function remote_post( $body_args = [] ) {
		$use_home_url = true;

		/**
		 * The license API uses `home_url()` function to retrieve the URL. This hook allows
		 * developers to use `get_site_url()` instead of `home_url()` to set the URL.
		 *
		 * When set to `true` (default) it uses `home_url()`.
		 * When set to `false` it uses `get_site_url()`.
		 *
		 * @param boolean $use_home_url Whether to use `home_url()` or `get_site_url()`.
		 */
		$use_home_url = apply_filters( 'elementor_pro/license/api/use_home_url', $use_home_url );

		$body_args = wp_parse_args(
			$body_args,
			[
				'api_version' => ELEMENTOR_PRO_VERSION,
				'item_name' => self::PRODUCT_NAME,
				'site_lang' => get_bloginfo( 'language' ),
				'url' => $use_home_url ? home_url() : get_site_url(),
			]
		);

		$response = wp_remote_post( self::STORE_URL, [
			'timeout' => 40,
			'body' => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, esc_html__( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', esc_html__( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data;
	}

	public static function activate_license( $license_key ) {
		$body_args = [
			'edd_action' => 'activate_license',
			'license' => $license_key,
		];

		$license_data = self::remote_post( $body_args );

		return $license_data;
	}

	public static function deactivate_license() {
		$body_args = [
			'edd_action' => 'deactivate_license',
			'license' => Admin::get_license_key(),
		];

		$license_data = self::remote_post( $body_args );

		return $license_data;
	}

	public static function set_transient( $cache_key, $value, $expiration = '+12 hours' ) {
		$data = [
			'timeout' => strtotime( $expiration, current_time( 'timestamp' ) ),
			'value' => json_encode( $value ),
		];

		update_option( $cache_key, $data, false );
	}

	private static function get_transient( $cache_key ) {
		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) ) {
			return false;
		}

		if ( current_time( 'timestamp' ) > $cache['timeout'] && is_user_logged_in() ) {
			return false;
		}

		return json_decode( $cache['value'], true );
	}

	public static function set_license_data( $license_data, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = '+12 hours';

			self::set_transient( Admin::LICENSE_DATA_FALLBACK_OPTION_NAME, $license_data, '+24 hours' );
		}

		self::set_transient( Admin::LICENSE_DATA_OPTION_NAME, $license_data, $expiration );
	}

	/**
	 * Check if another request is in progress.
	 *
	 * @param string $name Request name
	 *
	 * @return bool
	 */
	public static function is_request_running( $name ) {
		$requests_lock = get_option( self::REQUEST_LOCK_OPTION_NAME, [] );
		if ( isset( $requests_lock[ $name ] ) ) {
			if ( $requests_lock[ $name ] > time() - self::REQUEST_LOCK_TTL ) {
				return true;
			}
		}

		$requests_lock[ $name ] = time();
		update_option( self::REQUEST_LOCK_OPTION_NAME, $requests_lock );

		return false;
	}

	public static function get_license_data( $force_request = false ) {
		$license_data_error = [
			'license' => 'http_error',
			'payment_id' => '0',
			'license_limit' => '0',
			'site_count' => '0',
			'activations_left' => '0',
			'success' => false,
		];

		$license_key = Admin::get_license_key();

		if ( empty( $license_key ) ) {
			return $license_data_error;
		}

		$license_data = self::get_transient( Admin::LICENSE_DATA_OPTION_NAME );

		if ( false === $license_data || $force_request ) {
			$body_args = [
				'edd_action' => 'check_license',
				'license' => $license_key,
			];

			if ( self::is_request_running( 'get_license_data' ) ) {
				return $license_data_error;
			}

			$license_data = self::remote_post( $body_args );

			if ( is_wp_error( $license_data ) ) {
				$license_data = self::get_transient( Admin::LICENSE_DATA_FALLBACK_OPTION_NAME );
				if ( false === $license_data ) {
					$license_data = $license_data_error;
				}

				self::set_license_data( $license_data, '+30 minutes' );
			} else {
				self::set_license_data( $license_data );
			}
		}

		return $license_data;
	}

	public static function get_version( $force_update = true ) {
		$cache_key = self::TRANSIENT_KEY_PREFIX . ELEMENTOR_PRO_VERSION;

		$info_data = self::get_transient( $cache_key );

		if ( $force_update || false === $info_data ) {
			$updater = Admin::get_updater_instance();

			$translations = wp_get_installed_translations( 'plugins' );
			$plugin_translations = [];
			if ( isset( $translations[ $updater->plugin_slug ] ) ) {
				$plugin_translations = $translations[ $updater->plugin_slug ];
			}

			$locales = array_values( get_available_languages() );

			$body_args = [
				'edd_action' => 'get_version',
				'name' => $updater->plugin_name,
				'slug' => $updater->plugin_slug,
				'version' => $updater->plugin_version,
				'license' => Admin::get_license_key(),
				'translations' => wp_json_encode( $plugin_translations ),
				'locales' => wp_json_encode( $locales ),
				'beta' => 'yes' === get_option( 'elementor_beta', 'no' ),
			];

			if ( self::is_request_running( 'get_version' ) ) {
				return new \WP_Error( esc_html__( 'Another check is in progress.', 'elementor-pro' ) );
			}

			$info_data = self::remote_post( $body_args );

			self::set_transient( $cache_key, $info_data );
		}

		return $info_data;
	}

	public static function get_plugin_package_url( $version ) {
		$url = 'https://my.elementor.com/api/v1/pro-downloads/';

		$body_args = [
			'item_name' => self::PRODUCT_NAME,
			'version' => $version,
			'license' => Admin::get_license_key(),
			'url' => home_url(),
		];

		$response = wp_remote_post( $url, [
			'timeout' => 40,
			'body' => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 401 === $response_code ) {
			return new \WP_Error( $response_code, $data['message'] );
		}

		if ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, esc_html__( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', esc_html__( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data['package_url'];
	}

	public static function get_previous_versions() {
		$url = 'https://my.elementor.com/api/v1/pro-downloads/';

		$body_args = [
			'version' => ELEMENTOR_PRO_VERSION,
			'license' => Admin::get_license_key(),
			'url' => home_url(),
		];

		$response = wp_remote_get( $url, [
			'timeout' => 40,
			'body' => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 401 === $response_code ) {
			return new \WP_Error( $response_code, $data['message'] );
		}

		if ( 200 !== $response_code ) {
			return new \WP_Error( $response_code, esc_html__( 'HTTP Error', 'elementor-pro' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', esc_html__( 'An error occurred, please try again', 'elementor-pro' ) );
		}

		return $data['versions'];
	}

	public static function get_errors() {
		return [
			'no_activations_left' => sprintf(
				/* translators: 1: Bold text Open Tag, 2: Bold text closing tag, 3: Link open tag, 4: Link closing tag. */
				esc_html__( '%1$sYou have no more activations left.%2$s %3$sPlease upgrade to a more advanced license%4$s (you\'ll only need to cover the difference).', 'elementor-pro' ),
				'<strong>',
				'</strong>',
				'<a href="https://go.elementor.com/upgrade/" target="_blank">',
				'</a>'
			),
			'expired' => printf(
				/* translators: 1: Bold text Open Tag, 2: Bold text closing tag, 3: Link open tag, 4: Link closing tag. */
				esc_html__(
					'%1$sOh no! Your Elementor Pro license has expired.%2$s Want to keep creating secure and high-performing websites? Renew your subscription to regain access to all of the Elementor Pro widgets, templates, updates & more. %3$sRenew now%4$s',
					'elementor-pro'
				),
				'<strong>',
				'</strong>',
				'<a href="https://go.elementor.com/renew/" target="_blank">',
				'</a>'
			),
			'missing' => esc_html__( 'Your license is missing. Please check your key again.', 'elementor-pro' ),
			'revoked' => sprintf(
				/* translators: 1: Bold text Open Tag, 2: Bold text closing tag. */
				esc_html__( '%1$sYour license key has been cancelled%2$s (most likely due to a refund request). Please consider acquiring a new license.', 'elementor-pro' ),
				'<strong>',
				'</strong>'
			),
			'key_mismatch' => esc_html__( 'Your license is invalid for this domain. Please check your key again.', 'elementor-pro' ),
		];
	}

	public static function get_error_message( $error ) {
		$errors = self::get_errors();

		if ( isset( $errors[ $error ] ) ) {
			$error_msg = $errors[ $error ];
		} else {
			$error_msg = esc_html__( 'An error occurred. Please check your internet connection and try again. If the problem persists, contact our support.', 'elementor-pro' ) . ' (' . $error . ')';
		}

		return $error_msg;
	}

	public static function is_license_active() {
		$license_data = self::get_license_data();

		return self::STATUS_VALID === $license_data['license'];
	}

	public static function is_license_expired() {
		$license_data = self::get_license_data();

		return self::STATUS_EXPIRED === $license_data['license'];
	}

	public static function is_licence_pro_trial() {
		return self::is_licence_has_feature( self::FEATURE_PRO_TRIAL );
	}

	public static function is_licence_has_feature( $feature_name ) {
		$license_data = self::get_license_data();

		return ! empty( $license_data['features'] )
			&& in_array( $feature_name, $license_data['features'], true );
	}

	public static function is_license_about_to_expire() {
		$license_data = self::get_license_data();

		if ( ! empty( $license_data['subscriptions'] ) && 'enable' === $license_data['subscriptions'] ) {
			return false;
		}

		if ( 'lifetime' === $license_data['expires'] ) {
			return false;
		}

		return time() > strtotime( '-28 days', strtotime( $license_data['expires'] ) );
	}

	/**
	 * @param string $library_type
	 *
	 * @return int
	 */
	public static function get_library_access_level( $library_type = 'template' ) {
		$license_data = static::get_license_data();

		$access_level = ConnectModule::ACCESS_LEVEL_CORE;

		if ( static::is_license_active() ) {
			$access_level = ConnectModule::ACCESS_LEVEL_PRO;
		}

		// For BC: making sure that it returns the correct access_level even if "features" is not defined in the license data.
		if ( ! isset( $license_data['features'] ) || ! is_array( $license_data['features'] ) ) {
			return $access_level;
		}

		$library_access_level_prefix = "{$library_type}_access_level_";

		foreach ( $license_data['features'] as $feature ) {
			if ( strpos( $feature, $library_access_level_prefix ) !== 0 ) {
				continue;
			}

			$access_level = (int) str_replace( $library_access_level_prefix, '', $feature );
		}

		return $access_level;
	}
}
