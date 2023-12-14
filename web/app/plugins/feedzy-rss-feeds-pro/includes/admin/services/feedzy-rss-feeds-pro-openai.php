<?php
/**
 * The OpenAI service functionality. The extended methods for PRO.
 *
 * @link       http://themeisle.com
 * @since      3.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

/**
 * Class Feedzy_Rss_Feeds_Pro_openai
 */
class Feedzy_Rss_Feeds_Pro_Openai implements Feedzy_Rss_Feeds_Pro_Services_Interface {

	/**
	 * The API options.
	 *
	 * @since   1.3.1
	 * @access  private
	 * @var     array $options The API options.
	 */
	private $options = array();

	/**
	 * The API errors.
	 *
	 * @since   1.3.1
	 * @access  private
	 * @var     array $errors The API errors.
	 */
	private $errors = array();

	/**
	 * Init the API.
	 *
	 * @since   1.3.1
	 * @access  public
	 * @param   string $api The API key.
	 * @param   string $model The API model.
	 */
	public function init( $api = '', $model = '' ) {
		$this->set_api_option( 'api', $api );
		$this->set_api_option( 'model', $model );
	}

	/**
	 * Set an option key and value.
	 *
	 * @since   1.3.1
	 * @access  public
	 * @param   string $key The option key.
	 * @param   string $value The option value.
	 */
	public function set_api_option( $key, $value ) {
		$this->options[ $key ] = $value;
	}

	/**
	 * Get an option by key.
	 *
	 * @since   1.3.1
	 * @access  public
	 * @param   string $key The option key.
	 * @return bool|mixed
	 */
	public function get_api_option( $key ) {
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}
		return false;
	}

	/**
	 * Verify API status.
	 *
	 * @since   1.3.1
	 * @access  public
	 */
	public function check_api( &$post_data, $settings ) {
		if ( empty( $post_data['openai_api_key'] ) || empty( $post_data['openai_api_model'] ) ) {
			return;
		}
		$this->init( $post_data['openai_api_key'], $post_data['openai_api_model'] );

		$credential = $this->api_cred_encrypt( $this->options['api'], $this->options['model'] );

		$response = wp_remote_post(
			FEEDZY_PRO_OPENAI_API,
			array(
				'method'      => 'POST',
				'timeout'     => 120,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array(
					'cred'          => $credential,
					'content'       => 'Say this is a test!',
					'license'       => $this->license_key(),
					'force_rewrite' => true,
					'site_url'      => get_site_url(),
				),
				'cookies'     => array(),
			)
		);
		// phpcs:ignore warning
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'openai: check_api response %s', print_r( $response, true ) ), 'debug', __FILE__, __LINE__ );

		if ( is_wp_error( $response ) ) {
			$error_message               = $response->get_error_message();
			$post_data['openai_message'] = $error_message;
			// phpcs:ignore warning
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'openai: check_api error %s', print_r( $error_message, true ) ), 'error', __FILE__, __LINE__ );
		} else {
			$decode_response = json_decode( $response['body'], true );
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$post_data['openai_last_check'] = date( 'd/m/Y H:i:s' );
			$post_data['openai_licence']    = 'no';
			$post_data['openai_message']    = '';
			if ( isset( $decode_response['rewrite_content'] ) ) {
				$post_data['openai_licence'] = 'yes';
			} else {
					// phpcs:ignore warning
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'openai: check_api error %s', print_r( $decode_response['error'], true ) ), 'error', __FILE__, __LINE__ );
				$post_data['openai_message'] = $decode_response['error'];
			}
		}
	}

	/**
	 * Call API.
	 *
	 * @since   1.3.1
	 * @access  public
	 * @param   array  $settings Service settings.
	 * @param   string $text Text to spin.
	 * @param   string $type The type of text that is being spun e.g. 'title', 'content'.
	 * @param   array  $additional Additional parameters.
	 * @return bool|mixed
	 */
	public function call_api( $settings, $text, $type, $additional = array() ) {
		if ( ! (
				isset( $settings['openai_api_key'] ) && ! empty( $settings['openai_api_key'] )
				&& isset( $settings['openai_api_model'] ) && ! empty( $settings['openai_api_model'] )
				&& ! empty( $text )
				&& 'yes' === $settings['openai_licence']
			)
		) {
			// phpcs:ignore warning
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'NOT calling openai for %s with settings = %s, additional = %s', $text, print_r( $settings, true ), print_r( $additional, true ) ), 'debug', __FILE__, __LINE__ );
			return $text;
		}

		$this->init( $settings['openai_api_key'], $settings['openai_api_model'] );
		$credential = $this->api_cred_encrypt( $settings['openai_api_key'], $settings['openai_api_model'] );
		if ( 'summarize' === $type ) {
			$text = apply_filters( 'feedzy_invoke_content_summarize_service', $text, array( 'cred' => $credential ) );
		} else {
			$text = apply_filters( 'feedzy_invoke_content_openai_services', $text, array( 'cred' => $credential ) );
		}
		return $text;
	}

	/**
	 * Return erros.
	 *
	 * @since   1.3.1
	 * @access  public
	 * @return array|bool
	 */
	public function get_api_errors() {
		if ( count( $this->errors ) > 0 ) {
			return $this->errors;
		}
		return false;
	}

	/**
	 * Returns the service name.
	 *
	 * @access  public
	 */
	public function get_service_slug() {
		return 'openai';
	}

	/**
	 * Returns the proper service name.
	 *
	 * @access  public
	 */
	public function get_service_name_proper() {
		return 'OpenAI';
	}

	/**
	 * Create API encrypt token.
	 *
	 * @param string $api API key.
	 * @param string $model API model.
	 * @return string
	 */
	private function api_cred_encrypt( $api = '', $model = '' ) {
		$encryption_key = md5( sprintf( '%s-%s', $this->license_key(), date_i18n( 'd-m-Y' ) ) );
		$iv             = substr( md5( get_site_url() ), 0, 16 );
		$method         = 'AES-256-CBC';
		$token          = sprintf( '%s||%s', $api, $model );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$credential = base64_encode( openssl_encrypt( $token, $method, $encryption_key, 0, $iv ) );
		return $credential;
	}

	/**
	 * License key.
	 */
	private function license_key() {
		// if license does not exist, use the site url
		// this should obviously never happen unless on dev instances.
		$license      = sprintf( 'n/a - %s', get_site_url() );
		$license_data = apply_filters( 'product_feedzy_license_key', '' );
		if ( ! empty( $license_data ) ) {
			$license = $license_data;
		}
		return $license;
	}
}
