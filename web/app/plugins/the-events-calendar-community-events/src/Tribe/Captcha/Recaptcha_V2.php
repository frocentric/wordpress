<?php


class Tribe__Events__Community__Captcha__Recaptcha_V2 extends Tribe__Events__Community__Captcha__Recaptcha {

	/**
	 * @var Tribe__Languages__Map_Interface
	 */
	protected $languages_map;

	/**
	 * Tribe__Events__Community__Captcha__Recaptcha_V2 constructor.
	 *
	 * @param Tribe__Languages__Map_Interface|null $languages_map
	 */
	public function __construct( Tribe__Languages__Map_Interface $languages_map = null ) {
		$this->languages_map = $languages_map ? $languages_map : new Tribe__Languages__Recaptcha_Map();
	}

	/**
	 * Gets the challenge HTML (javascript and non-javascript version).
	 * This is called from the browser, and the resulting reCAPTCHA HTML widget
	 * is embedded within the HTML form it was called from.
	 *
	 * @param string  $pubkey  A public key for reCAPTCHA
	 * @param string  $error   The error given by reCAPTCHA (optional, default is null)
	 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
	 *
	 * @return string - The HTML to be embedded in the user's form.
	 */
	public function get_html( $pubkey, $error = null, $use_ssl = false ) {
		if ( $pubkey == null || $pubkey == '' ) {
			if ( current_user_can( 'manage_options' ) ) {
				return "<div class='error'>To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a></div>";
			} else {
				return;
			}
		}

		if ( $use_ssl ) {
			$server = self::RECAPTCHA_API_SECURE_SERVER;
		} else {
			$server = self::RECAPTCHA_API_SERVER;
		}

		$errorpart = '';
		if ( $error ) {
			$errorpart = '&amp;error=' . $error;
		}

		$language_code = $this->languages_map->convert_language_code( get_locale() );
		$language      = ! empty( $language_code ) ? '?hl=' . $language_code : '';

		return '<script src="https://www.google.com/recaptcha/api.js' . $language . '" async defer></script>'
		       . '<div class="g-recaptcha" data-sitekey="' . $pubkey . '"></div>';
	}

	/**
	 * Send the captcha through the recaptcha library for validation
	 *
	 * @param array $submission
	 *
	 * @return bool
	 */
	public function validate_captcha( $submission ) {
		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			return false;
		}

		$user_response = $_POST['g-recaptcha-response'];
		$private_key   = $this->private_key();
		$user_ip       = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : false;

		$response = $this->check_answer( $private_key, $user_ip, null, $user_response );

		return $response->is_valid;
	}


	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct.
	 *
	 * @param string $privkey
	 * @param string $remoteip
	 * @param string $unused       Unused in reCAPTCHA v2
	 * @param string $response
	 * @param array  $extra_params an array of extra variables to post to the server
	 *
	 * @return Tribe__Events__Community__Captcha__Response
	 */
	public function check_answer( $privkey, $remoteip, $unused, $response, $extra_params = [] ) {
		if ( null == $privkey || '' == $privkey ) {
			if ( current_user_can( 'manage_options' ) ) {
				return "<div class='error'>To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a></div>";
			} else {
				return;
			}
		}

		//discard spam submissions
		if ( null == $response || 0 == strlen( $response ) ) {
			$recaptcha_response           = new Tribe__Events__Community__Captcha__Response;
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error    = 'incorrect-captcha-sol';

			return $recaptcha_response;
		}

		$request_args = [
			'secret'   => $privkey,
			'response' => $response,
		];

		// optional in recaptcha v2
		if ( ! empty( $remoteip ) ) {
			$request_args['remoteip'] = $remoteip;
		}

		$response = $this->http_post( 'https://www.google.com', '/recaptcha/api/siteverify', $request_args + $extra_params );

		$recaptcha_response = new Tribe__Events__Community__Captcha__Response;

		if ( is_wp_error( $response ) ) {
			$recaptcha_response->is_valid = false;
			$error = _x( 'reCAPTCHA failed unexpectedly', 'failed recaptcha response', 'tribe-events-community' );
			$recaptcha_response->error = $error;

			return $recaptcha_response;
		}

		$answers = json_decode( $response[1] );

		if ( ! empty( $answers->success ) && true === $answers->success ) {
			$recaptcha_response->is_valid = true;
		} else {
			$recaptcha_response->is_valid = false;
			$error_codes = ! empty( $answers->{'error-codes'} ) ?
				$answers->{'error-codes'} :
				[ _x( 'reCAPTCHA verification failed', 'failed recaptcha response validation', 'tribe-events-community' ) ];
			$recaptcha_response->error = reset( $error_codes );
		}

		return $recaptcha_response;
	}

	protected function get_recaptcha_field_names() {
		return [ 'g-recaptcha-response' ];
	}
}
