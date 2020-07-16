<?php

class Tribe__Events__Community__Captcha__Recaptcha
	extends Tribe__Events__Community__Captcha__Abstract_Captcha {

	const RECAPTCHA_API_SERVER = 'http://www.google.com/recaptcha/api';
	const RECAPTCHA_API_SECURE_SERVER = 'https://www.google.com/recaptcha/api';
	const RECAPTCHA_VERIFY_SERVER = 'www.google.com';

	/**
	 * The user must provide values for captcha keys in order to display reCAPTCHA on the front end.
	 * An empty value in either field will prevent the reCAPTCHA from rendering.
	 * If both fields are complete, the reCAPTCHA will automatically appear.
	 */
	protected function get_settings_fields() {
		return [
			'recaptcha-heading'   => [
				'type'  => 'heading',
				'label' => __( 'reCAPTCHA API Keys', 'tribe-events-community' ),
			],
			'recaptcha-info'      => [
				'type' => 'html',
				'html' => '<p>' . __( 'Provide reCAPTCHA API keys for both fields to enable reCAPTCHA on your Community Events form.', 'tribe-events-community' ) . '<br><br><em>' . __( 'Note: reCAPTCHA only appears for users who are not logged in.', 'tribe-events-community' ) . '</em></p>',
			],
			'recaptchaPublicKey'  => [
				'type'            => 'text',
				'label'           => __( 'Site Key', 'tribe-events-community' ),
				'tooltip'         => sprintf( __( 'Get your Site Key at %s', 'tribe-events-community' ), '<a href="' . esc_url( $this->registration_url() ) . '" target="_blank">' . esc_url( $this->registration_url() ) . '</a>' ),
				'default'         => '',
				'validation_type' => 'html',
				'can_be_empty'    => true,
				'parent_option'   => Tribe__Events__Community__Main::OPTIONNAME,
				'size'            => 'large',
			],
			'recaptchaPrivateKey' => [
				'type'            => 'text',
				'label'           => __( 'Secret Key', 'tribe-events-community' ),
				'tooltip'         => sprintf( __( 'Get your Secret Key at %s', 'tribe-events-community' ), '<a href="' . esc_url( $this->registration_url() ) . '" target="_blank">' . esc_url( $this->registration_url() ) . '</a>' ),
				'default'         => '',
				'validation_type' => 'html',
				'can_be_empty'    => true,
				'parent_option'   => Tribe__Events__Community__Main::OPTIONNAME,
				'size'            => 'large',
			],
		];
	}

	/**
	 * Add recaptcha settings to the front-end JS
	 *
	 * @return void
	 */
	public function enqueue_scripts_and_styles() {
		if ( ! $this->settings_valid() ) {
			return;
		}
		$locale = substr( get_locale(), 0, 2 );
		$recaptcha_options = [
			'theme' => 'white',
			'lang' => $locale,
		];
		$recaptcha_options = apply_filters( 'tribe_community_events_recaptcha_widget_options', $recaptcha_options );
		wp_localize_script( Tribe__Events__Main::POSTTYPE . '-community', 'RecaptchaOptions', $recaptcha_options );
	}

	/**
	 * @return string The captcha form
	 */
	protected function get_captcha_form() {
		if ( ! $this->settings_valid() ) {
			return '';
		}

		$public_key = $this->public_key();
		$captcha = $this->get_html( $public_key, null, is_ssl() );
		ob_start();
		tribe_get_template_part( 'community/modules/captcha', null, [ 'captcha' => $captcha ] );
		$form = ob_get_clean();
		return $form;
	}

	protected function get_fieldname_whitelist() {
		return $this->get_recaptcha_field_names();
	}

	protected function get_required_fields() {
		if ( $this->showing_captcha() ) {
			return $this->get_recaptcha_field_names();
		} else {
			return [];
		}
	}

	/**
	 * Send the captcha through the recaptcha library for validation
	 *
	 * @param array $submission
	 *
	 * @return bool
	 */
	public function validate_captcha( $submission ) {
		if ( empty( $submission['recaptcha_challenge_field'] ) ||  empty( $submission['recaptcha_response_field'] ) ) {
			return false;
		}
		$private_key = $this->private_key();
		$response = $this->check_answer(
			$private_key,
			$_SERVER[ 'REMOTE_ADDR' ],
			$submission[ 'recaptcha_challenge_field' ],
			$submission[ 'recaptcha_response_field' ]
		);
		return $response->is_valid;
	}

	protected function get_recaptcha_field_names() {
		return [
			'recaptcha_challenge_field',
			'recaptcha_response_field',
		];
	}

	/**
	 * @return string The URL where users can register for recaptcha and retrieve public and private keys
	 */
	protected function registration_url() {
		/**
		 * The php library we're using to interact with captcha does
		 * have a library for doing this, but it doesn't seem to work as intended.
		 * It purports to be able to pass params to reCAPTCHA to prefill the "domains"
		 * field, but it doesn't seem to work.  So, let's just use this instead.
		 */
		return apply_filters( 'tribe_community_events_recaptcha_registration_url', 'https://www.google.com/recaptcha/admin#createsite' );
	}

	protected function settings_valid() {
		$public = $this->public_key();
		$private = $this->private_key();
		if ( empty( $public ) || empty( $private ) ) {
			return false;
		}
		return true;
	}

	protected function public_key() {
		return tribe( 'community.main' )->getOption( 'recaptchaPublicKey', '' );
	}

	protected function private_key() {
		return tribe( 'community.main' )->getOption( 'recaptchaPrivateKey', '' );
	}

	/**
	 * gets a URL where the user can sign up for reCAPTCHA. If your application
	 * has a configuration page where you enter a key, you should provide a link
	 * using this function.
	 * @param string $domain The domain where the page is hosted
	 * @param string $appname The name of your application
	 */
	public function get_signup_url( $domain = null, $appname = null ) {
		return 'https://www.google.com/recaptcha/admin/create?' .  $this->qsencode( [ 'domains' => $domain, 'app' => $appname ] );
	}

	/**
	 * Gets the challenge HTML (javascript and non-javascript version).
	 * This is called from the browser, and the resulting reCAPTCHA HTML widget
	 * is embedded within the HTML form it was called from.
	 * @param string $pubkey A public key for reCAPTCHA
	 * @param string $error The error given by reCAPTCHA (optional, default is null)
	 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)
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
		return '<script src="' . esc_url( $server . '/challenge?k=' . $pubkey . $errorpart ) . '"></script>

			<noscript>
			<iframe src="' . esc_url( $server . '/noscript?k=' . $pubkey . $errorpart ) . '" height="300" width="500" frameborder="0"></iframe><br/>
			<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
			<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
			</noscript>';
	}

	/**
	 * Calls an HTTP POST function to verify if the user's guess was correct
	 * @param string $privkey
	 * @param string $remoteip
	 * @param string $challenge
	 * @param string $response
	 * @param array $extra_params an array of extra variables to post to the server
	 * @return Tribe__Events__Community__Captcha__Response
	 */
	public function check_answer( $privkey, $remoteip, $challenge, $response, $extra_params = [] ) {
		if ( null == $privkey  || '' == $privkey ) {
			if ( current_user_can( 'manage_options' ) ) {
				return "<div class='error'>To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a></div>";
			} else {
				return;
			}
		}

		if ( null == $remoteip || '' == $remoteip ) {
			if ( current_user_can( 'manage_options' ) ) {
				return '<div class="error">For security reasons, you must pass the remote ip to reCAPTCHA</div>';
			} else {
				return;
			}
		}

		//discard spam submissions
		if ( null == $challenge || 0 == strlen( $challenge ) || null == $response || 0 == strlen( $response ) ) {
			$recaptcha_response = new Tribe__Events__Community__Captcha__Response;
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = 'incorrect-captcha-sol';
			return $recaptcha_response;
		}

		$response = $this->http_post(
			self::RECAPTCHA_VERIFY_SERVER,
			'/recaptcha/api/verify',
			[
				'privatekey' => $privkey,
				'remoteip' => $remoteip,
				'challenge' => $challenge,
				'response' => $response,
			] + $extra_params
		);

		$recaptcha_response = new Tribe__Events__Community__Captcha__Response;

		if ( is_wp_error( $response ) ) {
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = _x( 'reCAPTCHA failed unexpectedly', 'failed recaptcha response', 'tribe-events-community' );
			return $recaptcha_response;
		}

		$answers = explode( "\n", $response[1] );

		if ( 'true' == trim( $answers[0] ) ) {
			$recaptcha_response->is_valid = true;
		} else {
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = $answers[1];
		}

		return $recaptcha_response;
	}

	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	private function qsencode( $data ) {
		$req = '';
		foreach ( $data as $key => $value ) {
			$req .= $key . '=' . urlencode( stripslashes( $value ) ) . '&';
		}

		// Cut the last '&'
		$req = substr( $req, 0, strlen( $req ) - 1 );
		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 *
	 * @param string $host
	 * @param string $path
	 * @param array $body
	 *
	 * @return array|WP_Error
	 */
	protected function http_post( $host, $path, $body ) {
		// Build the url to which we'll send our request.
		$url = esc_url( $host.$path );

		// Encode the body as a url query string.
		$req = $this->qsencode( $body );

		// Get the content length.
		$length = strlen( $req );

		// Build the request headers.
		$headers = [
			'Content-Type'   => 'application/x-www-form-urlencoded',
			'Content-Length' => $length,
			'User-Agent'     => 'reCAPTCHA/PHP',
		];

		// Build an args array for wp_remote_post.
		$args = [
			'method'      => 'POST',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'body'        => $body,
			'cookies'     => [],
		];

		// Send the request to reCaptcha.
		$response_array = wp_remote_post(
			$url,
			$args
		);

		if ( is_wp_error( $response_array ) ) {
			return $response_array;
		}

		// The response headers will be in the form of an iterable object; let's convert to an
		// actual array to avoid warnings when we later implode them into a string
		$response_head_array = [];

		foreach ( $response_array[ 'headers' ] as $key => $value ) {
			$response_head_array[ $key ] = $value;
		}

		// The response header as a string.
		$response_head_string = implode( "\r\n\r\n", $response_head_array );

		// The response body from reCaptcha.
		$response_body_string = $response_array[ 'body' ];

		// The response array formatted as expected elsewhere in this library.
		$response = [
			$response_head_string,
			$response_body_string,
		];

		return $response;
	}

	private function aes_pad( $val ) {
		$block_size = 16;
		$numpad = $block_size - ( strlen( $val ) % $block_size );
		return str_pad( $val, strlen( $val ) + $numpad, chr( $numpad ) );
	}
}
