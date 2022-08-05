<?php
/**
 * The SpinnerChief service functionality. The extended methods for PRO.
 *
 * @link       http://themeisle.com
 * @since      3.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

/**
 * Class Feedzy_Rss_Feeds_Pro_Spinnerchief
 */
class Feedzy_Rss_Feeds_Pro_Spinnerchief implements Feedzy_Rss_Feeds_Pro_Services_Interface {

	const API_URL = 'http://api.spinnerchief.com:443/apikey=#key#&username=#username#&password=#password#';

	/**
	 * The API URL
	 *
	 * @var string The URL with keys replaced from const API_URL.
	 */
	private $url = '';

	/**
	 * The languages supported by the API and their mapping with the languages in WordPress.
	 *
	 * @since   ?
	 * @access  private
	 * @var     array $languages The languages supported by the API and their mapping with the languages in WordPress.
	 */
	private static $languages = array(
		'ar'  => 'Arabic',
		'bel' => 'Belarusian',
		'bg'  => 'Bulgarian',
		'hr'  => 'Croatian',
		'da'  => 'Danish',
		'nl'  => 'Dutch',
		'en'  => 'English',
		'tl'  => 'Filipino',
		'fi'  => 'Finnish',
		'fr'  => 'French',
		'de'  => 'German',
		'el'  => 'Greek',
		'he'  => 'Hebrew',
		'id'  => 'Indonesian',
		'it'  => 'Italian',
		'lt'  => 'Lithuanian',
		'nb'  => 'Norwegian',
		'nn'  => 'Norwegian',
		'pl'  => 'Polish',
		'pt'  => 'Portuguese',
		'ro'  => 'Romanian',
		'sk'  => 'Slovak',
		'sl'  => 'Slovenian',
		'es'  => 'Spanish',
		'sv'  => 'Swedish',
		'tr'  => 'Turkish',
		'vi'  => 'Vietnamese',
	);

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
	 * @param   string $key The API key.
	 */
	public function init( $key = '', $username = '', $password = '' ) {
		$this->set_api_option( 'key', $key );
		$this->set_api_option( 'username', $username );
		$this->set_api_option( 'password', $password );

		$this->url = str_replace( array( '#key#', '#username#', '#password#' ), array( $this->get_api_option( 'key' ), $this->get_api_option( 'username' ), $this->get_api_option( 'password' ) ), self::API_URL );
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
		if ( ! (
				isset( $post_data['spinnerchief_password'] ) && ! empty( $post_data['spinnerchief_password'] )
				&& isset( $post_data['spinnerchief_username'] ) && ! empty( $post_data['spinnerchief_username'] )
				&& isset( $post_data['spinnerchief_key'] ) && ! empty( $post_data['spinnerchief_key'] )
			)
		) {
			// phpcs:ignore warning
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: NOT calling API with settings = ', print_r( $settings, true ) ), 'debug', __FILE__, __LINE__ );
			return;
		}

		$this->init( $post_data['spinnerchief_key'], $post_data['spinnerchief_username'], $post_data['spinnerchief_password'] );

		$url = $this->url . '&querytimes=2';

		$response = wp_remote_post( $url );

		// phpcs:ignore warning
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: calling %s and getting response %s', $url, print_r( $response, true ) ), 'debug', __FILE__, __LINE__ );

		if ( is_wp_error( $response ) ) {
			$error_message                     = 'Something went wrong: ' . $response->get_error_message();
			$post_data['spinnerchief_message'] = $error_message;
			// phpcs:ignore warning
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: check_api error %s', print_r( $error_message, true ) ), 'error', __FILE__, __LINE__ );
		} else {
			// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$post_data['spinnerchief_last_check'] = date( 'd/m/Y H:i:s' );
			$post_data['spinnerchief_licence']    = 'no';
			$post_data['spinnerchief_message']    = '';

			// phpcs:ignore warning
			$body = base64_decode( $response['body'] );
			if ( strpos( $body, 'error=' ) !== false ) {
				$error_message                     = str_replace( 'error=', '', $body );
				$post_data['spinnerchief_message'] = $error_message;
				// phpcs:ignore warning
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: check_api error %s', print_r( $error_message, true ) ), 'error', __FILE__, __LINE__ );
			} elseif ( is_numeric( $body ) ) {
				$post_data['spinnerchief_licence'] = 'yes';
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
				isset( $settings['spinnerchief_password'] ) && ! empty( $settings['spinnerchief_password'] )
				&& isset( $settings['spinnerchief_username'] ) && ! empty( $settings['spinnerchief_username'] )
				&& isset( $settings['spinnerchief_key'] ) && ! empty( $settings['spinnerchief_key'] )
				&& ! empty( $text )
				&& 'yes' === $settings['spinnerchief_licence']
			)
		) {
			// phpcs:ignore warning
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: NOT calling API for %s with settings = %s, additional = %s', $text, print_r( $settings, true ), print_r( $additional, true ) ), 'debug', __FILE__, __LINE__ );
			return null;
		}

		$this->init( $settings['spinnerchief_key'], $settings['spinnerchief_username'], $settings['spinnerchief_password'] );

		$additional = array_filter( $additional );

		if ( isset( $additional['lang'] ) ) {
			$languages = apply_filters( 'feedzy_spinnerchief_languages', self::$languages );
			// use only the first part of the language.
			$array = explode( '_', $additional['lang'] );
			$lang  = reset( $array );
			if ( array_key_exists( $lang, $languages ) ) {
				$additional['thesaurus'] = $languages[ $lang ];
			} else {
				// phpcs:ignore warning
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: Language %s (%s) does not seem to be supported from the list: %s. Ignorning.', $lang, $additional['lang'], print_r( $languages, true ) ), 'warn', __FILE__, __LINE__ );
			}
		}

		$url  = $this->url;
		$url_query_string = array();
		$parse_url = wp_parse_url( $url, PHP_URL_PATH );
		wp_parse_str( $parse_url, $url_query_string );

		$args = apply_filters( 'feedzy_spinnerchief_args', array_merge( $additional, array( 'spintype' => 1 ) ) );
		$args = array_merge( $url_query_string, $args );
		$url  = str_replace( $parse_url, '', $url ) . urldecode( http_build_query( $args ) );
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: calling %s for %s', $url, $text ), 'info', __FILE__, __LINE__ );

		$response = wp_remote_post(
			$url,
			apply_filters(
				'feedzy_service_api_params',
				array(
					// phpcs:ignore warning
					'body' => base64_encode( $text ),
				),
				'spinnerchief'
			)
		);

		// unset custom params
		unset( $additional['thesaurus'] );

		// phpcs:ignore warning
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: calling %s for %s and getting response %s', $url, $text, print_r( $response, true ) ), 'debug', __FILE__, __LINE__ );

		$body          = null;
		$error_message = null;

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
		} else {
			// phpcs:ignore warning
			$body = base64_decode( $response['body'] );
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: no error and body of response: raw = %s, base64 decoded = %s', $response['body'], $body ), 'debug', __FILE__, __LINE__ );

			if ( strpos( $body, 'error=' ) !== false ) {
				$error_message = str_replace( 'error=', '', $body );
			}
		}

		if ( ! is_null( $error_message ) ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, 'spinnerchief: error = ' . $error_message, 'error', __FILE__, __LINE__ );
			$this->errors[] = array(
				'type'    => 'ERROR',
				'message' => 'Something went wrong: ' . $error_message,
			);
		} else {
			if ( 'title' !== $type ) {
				$body = nl2br( $body );
			}
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'spinnerchief: %s spun to %s', $text, $body ), 'info', __FILE__, __LINE__ );
			return $body;
		}
		return null;
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
		return 'spinnerchief';
	}

	/**
	 * Returns the proper service name.
	 *
	 * @access  public
	 */
	public function get_service_name_proper() {
		return 'SpinnerChief';
	}

}
