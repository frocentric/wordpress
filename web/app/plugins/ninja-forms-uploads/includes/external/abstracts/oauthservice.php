<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Abstracts_Service
 */
abstract class NF_FU_External_Abstracts_Oauthservice extends NF_FU_External_Abstracts_Service {

	protected $consumer_key;

	protected $tokens = array();

	protected $transient_expires = 18000; // 5 hours

	/**
	 * Get provider slug
	 *
	 * @return string
	 */
	protected function slug() {
		if ( isset( $this->provider_slug ) ) {
			return $this->provider_slug;
		}

		return $this->slug;
	}

	protected function get_transient_key() {
		return 'nf_fu_' . $this->slug() . '_authorised';
	}

	/**
	 * Get the plugins page URL to return to for oAuth.
	 *
	 * @return string
	 */
	protected function get_callback_url() {
		return NF_File_Uploads()->page->get_url( 'external', array(), false );
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	protected function get_authorize_url( $args = array() ) {
		delete_transient( $this->get_transient_key() );

		return NF_File_Uploads()->externals->wpoauth()->get_authorize_url( $this->slug(), $this->consumer_key, $this->get_callback_url(), $args );
	}

	/**
	 * @return string
	 */
	protected function get_disconnect_url() {
		return NF_File_Uploads()->externals->wpoauth()->get_disconnect_url( $this->slug(), $this->get_callback_url() );
	}

	protected function disconnect() {
		NF_File_Uploads()->externals->wpoauth()->disconnect( $this->slug() );
	}

	/**
	 * Get the connect/disconnect URL in the settings template.
	 */
	public function connect_url() {
		if ( $this->is_connected() ) {
			$url = $this->get_disconnect_url();
			?>
			<a id="<?php echo $this->slug(); ?>-disconnect" href="<?php echo $url; ?>" class="button-secondary"><?php _e( 'Disconnect', 'ninja-forms-uploads' ); ?></a>
			<?php $this->get_disconnection_description(); ?>
		<?php } else {
			$url = $this->get_authorize_url();
			?>
			<a id="<?php echo $this->slug(); ?>-connect" href="<?php echo $url; ?>" class="button-primary"><?php _e( 'Connect', 'ninja-forms-uploads' ); ?></a>
			<?php
		}
	}

	protected function get_disconnection_description() {}

	/**
	 * Is the service connected?
	 *
	 * @param null|array $settings
	 *
	 * @return bool
	 */
	public function is_connected( $settings = null ) {
		if ( ! $this->has_access_token() ) {
			return false;
		}

		if ( ! $this->transient_expires || false === ( $authorised = get_transient( $this->get_transient_key() ) ) ) {
			$authorised = $this->is_authorized();

			if ( $this->transient_expires ) {
				set_transient( $this->get_transient_key(), $authorised, $this->transient_expires );
			}
		}

		return $authorised;
	}

	/**
	 * @return bool
	 */
	protected abstract function is_authorized();

	/**
	 * @return bool
	 */
	public function has_access_token() {
		return ( bool ) $this->get_access_token();
	}

	/**
	 * Ger the access token
	 *
	 * @return bool
	 */
	public function get_access_token() {
		if ( isset( $this->tokens[ $this->slug() ] ) ) {
			return $this->tokens[ $this->slug() ];
		}

		$oauth2_token = NF_File_Uploads()->externals->wpoauth()->token_manager->get_access_token( $this->slug() );
		if ( $oauth2_token ) {
			$this->tokens[ $this->slug() ] = $oauth2_token;

			return $oauth2_token;
		}

		return false;
	}

	/**
	 * Get a new access token.
	 *
	 * @return string|bool
	 */
	public function refresh_access_token() {
		return NF_File_Uploads()->externals->wpoauth()->refresh_access_token( $this->consumer_key, $this->slug() );
	}
}