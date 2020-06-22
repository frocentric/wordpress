<?php

abstract class Tribe__Events__Community__Captcha__Abstract_Captcha {

	public function __construct() {
	}

	/**
	 * Set up callbacks to hook into the event submission process
	 * @return void
	 */
	public function init() {
		add_filter( 'tribe_addons_tab_fields', [ $this, 'add_settings_fields' ], 10 );
		add_filter( 'tribe_get_template_part_content', [ $this, 'add_form_fields' ], 10, 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ], 15, 0 );
		add_filter( 'tribe_events_community_allowed_event_fields', [ $this, 'whitelist_form_fields' ], 10, 1 );
		add_filter( 'tribe_events_community_required_fields', [ $this, 'set_required_fields' ], 10, 1 );
		add_filter( 'tribe_community_events_validate_submission', [ $this, 'validate_submission' ], 10, 3 );
	}

	/**
	 * Add fields to the community settings tab
	 *
	 * @param array $fields
	 * @param string $tab_id
	 *
	 * @return array
	 */
	public function add_settings_fields( $fields ) {
		$captcha_fields = $this->get_settings_fields();

		if ( ! empty( $captcha_fields ) ) {
			$fields = array_merge( $fields, $captcha_fields );
//			$fields = Tribe__Main::array_insert_after_key( 'single_geography_mode', $fields, $captcha_fields );
		}

		return $fields;
	}

	/**
	 * Get the field definitions for any settings required by the captcha plugin
	 * @return array
	 */
	protected function get_settings_fields() {
		return [];
	}

	/**
	 * Add the captcha form to the anti-spam section of the submission form
	 *
	 * @since  4.5 Moved this into `tribe_get_template_part_content` and added all the other params
	 *
	 * @param  string $html     Template HTML
	 * @param  string $template Name of the template
	 * @param  string $file     Full File Path
	 * @param  string $slug     Name of the template without file ext
	 * @param  string $name     Template name (extra name)
	 *
	 * @return string
	 */
	public function add_form_fields( $html, $template, $file, $slug, $name ) {
		if ( 'community/modules/spam-control' !== $slug ) {
			return $html;
		}

		if ( ! $this->showing_captcha() ) {
			return $html;
		}
		$captcha_fields = $this->get_captcha_form();
		return $html . $captcha_fields;
	}

	/**
	 * @return bool Whether we're showing the captcha form for the current user
	 */
	protected function showing_captcha() {
		$show = true;
		if ( is_user_logged_in() || ! $this->settings_valid() ) {
			$show = false;
		}
		return apply_filters( 'tribe_community_events_show_captcha', $show );
	}

	protected function settings_valid() {
		return true;
	}

	/**
	 * @return string The front-end captcha form
	 */
	protected function get_captcha_form() {
		return '';
	}

	/**
	 * Enqueue any front-end scripts/styles required by the captcha plugin
	 *
	 * @return void
	 */
	public function enqueue_scripts_and_styles() {
		// nothing here
	}

	/**
	 * Whitelist the form fields added by the captcha plugin
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function whitelist_form_fields( $fields ) {
		return array_merge( $fields, $this->get_fieldname_whitelist() );
	}

	/**
	 * @return array The names of form fields added by the captcha plugin
	 */
	protected function get_fieldname_whitelist() {
		return [];
	}

	/**
	 * Set the names of the form fields required by the captcha plugin
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function set_required_fields( $fields ) {
		$captcha_fields = $this->get_required_fields();
		return array_merge( $fields, $captcha_fields );
	}

	/**
	 * @return array The names of form fields required by the captcha plugin
	 */
	protected function get_required_fields() {
		return [];
	}

	/**
	 * @param bool $valid
	 * @param array $submission
	 * @param Tribe__Events__Community__Submission_Handler $submission_handler
	 *
	 * @return mixed
	 */
	public function validate_submission( $valid, $submission, $submission_handler ) {
		if ( $this->showing_captcha() ) {
			$valid_captcha = $this->validate_captcha( $submission );
			if ( ! $valid_captcha ) {
				$submission_handler->add_message( __( 'Invalid value for the Anti-Spam Check', 'tribe-events-community' ), 'error' );
				$valid = false;
			}
		}
		return $valid;
	}

	/**
	 * Validate the captcha response
	 *
	 * @param array $submission
	 *
	 * @return bool
	 */
	protected function validate_captcha( $submission ) {
		return true;
	}
}
