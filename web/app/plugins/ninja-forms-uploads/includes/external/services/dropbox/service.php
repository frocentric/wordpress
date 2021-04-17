<?php

use NF_FU_VENDOR\Polevaultweb\WPDropboxAPI\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Services_Dropbox_Service
 */
class NF_FU_External_Services_Dropbox_Service extends NF_FU_External_Abstracts_Oauthservice {

	public $name = 'Dropbox';

	/**
	 * @var int Maximum file size in bytes to send to service in a single request
	 */
	protected $max_single_upload_file_size = 157286400;

	protected $client;
	protected $account_info_cache;

	protected $consumer_key = 'g80jscev5iosghi';

	/**
	 * Wrapper to get access token.
	 * If an OAuth2 token exists use it.
	 * If an OAuth1 token/secret exists then use them to convert to OAuth2 token.
	 *
	 * @return bool|string
	 */
	public function get_access_token() {
		$oauth2_token = parent::get_access_token();
		if ( $oauth2_token ) {
			return $oauth2_token;
		}

		$old_access_token = NF_File_Uploads()->controllers->settings->get_setting( 'dropbox_access_token' );
		if ( empty( $old_access_token ) ) {
			return false;
		}

		$old_access_token_secret = NF_File_Uploads()->controllers->settings->get_setting( 'dropbox_access_token_secret' );

		$token = $this->get_upgraded_token( $old_access_token, $old_access_token_secret );
		if ( false === $token ) {
			return $token;
		}

		NF_File_Uploads()->externals->wpoauth()->token_manager->set_access_token( $this->slug(), $token );

		NF_File_Uploads()->controllers->settings->remove_setting( 'dropbox_access_token' );
		NF_File_Uploads()->controllers->settings->remove_setting( 'dropbox_access_token_secret' );
		NF_File_Uploads()->controllers->settings->remove_setting( 'dropbox_request_token' );
		NF_File_Uploads()->controllers->settings->remove_setting( 'dropbox_request_token_secret' );
		NF_File_Uploads()->controllers->settings->remove_setting( 'dropbox_oauth_state' );
		NF_File_Uploads()->controllers->settings->update_settings();

		return $token;
	}

	/**
	 * Get the new access token from the old token and secret from the oauth proxy service.
	 *
	 * @param string $old_access_token
	 * @param string $old_access_token_secret
	 *
	 * @return string|bool
	 */
	protected function get_upgraded_token( $old_access_token, $old_access_token_secret ) {
		$params = array(
			'client_id'    => $this->consumer_key,
			'oauth_token'  => $old_access_token,
			'oauth_secret' => $old_access_token_secret,
		);

		$endpoint = NF_File_Uploads()->externals->wpoauth()->get_oauth_proxy_url();
		$url = $endpoint . '/dropbox_token_upgrade?' . http_build_query( $params, '', '&' );

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			return false; // Bail early
		}

		$body = wp_remote_retrieve_body( $request );

		$data = json_decode( $body, true );
		if ( ! $data || empty( $data['token'] ) ) {
			return false;
		}

		return $data['token'];
	}


	/**
	 * Get Dropbox API client instance
	 *
	 * @return API
	 */
	public function get_client() {
		if ( is_null( $this->client ) ) {
			$token = $this->get_access_token();

			$this->client = new API( $token );
		}

		return $this->client;
	}

	/**
	 * Has the account authorised our API app?
	 *
	 * @return bool
	 */
	public function is_authorized() {
		return ( bool ) $this->get_account_info();
	}

	/**
	 * Check the Dropbox account details for the connected user.
	 *
	 * @return bool
	 */
	public function get_account_info() {
		if ( ! isset( $this->account_info_cache ) ) {
			$response = $this->get_client()->get_account_info();
			if ( isset( $response->error_summary ) && false !== strpos( $response->error_summary, 'invalid_access_token' ) ) {
				$this->disconnect();

				return false;
			}

			if ( $response ) {
				$this->account_info_cache = $response;

				return $response;
			}
		}

		return $this->account_info_cache;
	}

	protected function get_disconnection_description() {
		$logout_link = sprintf( '<a href="%s">%s</a>', 'https://www.dropbox.com/logout', __( 'log out', 'ninja-forms-uploads' ) );
		$logout_text = sprintf( __( 'To connect with a different account, first %s of Dropbox', 'ninja-forms-uploads' ), $logout_link );
		echo '<p class="description">' . $logout_text . '</p>';
	}

	/**
	 * Get path on Dropbox to upload to
	 *
	 * @return string
	 */
	protected function get_path_setting() {
		return 'dropbox_file_path';
	}
	
	/**
	 * Upload the file to Dropbox
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	public function upload_file( $data ) {
		if ( $this->external_filename === '' ) {
			$this->external_filename = $this->remove_secret( $this->upload_file );
		}

		$retry_count = apply_filters( 'ninja_forms_upload_dropbox_retry_count', 3 );
		$i           = 0;
		while ( $i++ < $retry_count ) {
			$result = $this->get_client()->put_file( $this->upload_file, $this->external_filename, $this->external_path );
			if ( $result ) {
				return $data;
			}
		}

		return false;
	}

	/**
	 * Get the Dropbox URL for the file
	 *
	 * @param string $filename
	 * @param string $path
	 * @param array  $data
	 *
	 * @return string
	 */
	public function get_url( $filename, $path = '', $data = array() ) {
		$response = $this->get_client()->get_url( $path . $filename );
		if ( $response && isset( $response->link ) ) {
			return $response->link;
		}

		return admin_url();
	}

	protected function remove_secret( $file, $basename = true ) {
		if ( preg_match( '/-nf-secret$/', $file ) ) {
			$file = substr( $file, 0, strrpos( $file, '.' ) );
		}

		if ( $basename ) {
			return basename( $file );
		}

		return $file;
	}
}