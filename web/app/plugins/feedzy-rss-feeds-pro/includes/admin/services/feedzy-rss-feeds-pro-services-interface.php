<?php
interface Feedzy_Rss_Feeds_Pro_Services_Interface {

	/**
	 * Init the API
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function init();

	/**
	 * Set the API options.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @param   string $key The key to set.
	 * @param   string $value The value.
	 */
	public function set_api_option( $key, $value );

	/**
	 * Get the API option by key.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @param   string $key The key to get.
	 * @return mixed
	 */
	public function get_api_option( $key );

	/**
	 * Invoke API.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @param   array  $settings Service settings.
	 * @param   string $text Text to spin.
	 * @param   string $type The type of text that is being spun e.g. 'title', 'content'.
	 * @param   array  $additional Additional parameters.
	 * @return bool|mixed
	 */
	public function call_api( $settings, $text, $type, $additional );

	/**
	 * Retrieve API errors.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @return mixed
	 */
	public function get_api_errors();

	/**
	 * Check API status.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @param   array $args Options array to pass to API.
	 */
	public function check_api( &$args, $settings );

	/**
	 * Returns the service name.
	 *
	 * @access  public
	 */
	public function get_service_slug();

	/**
	 * Returns the proper service name.
	 *
	 * @access  public
	 */
	public function get_service_name_proper();

}
