<?php

/**
 * Class Tribe__Events__Community__Integrations__WP_Edit
 *
 * Handles the integration between The Events Calendar plugin and
 * the WP Edit plugin.
 */
class Tribe__Events__Community__Integrations__WP_Edit {

	/**
	 * @var Tribe__Events__Community__Integrations__WP_Edit
	 */
	protected static $instance;

	/**
	 * @return Tribe__Events__Community__Integrations__WP_Edit
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * If/when the WP Edit plugin is running, prevent its "disable wpautop" option from
	 * breaking the Community Events submission form.
	 *
	 * @since 4.5.10
	 */
	public function prevent_wpautop_conflict() {

		global $jwl_toggle_wpautop;

		if ( ! empty( $jwl_toggle_wpautop ) && $jwl_toggle_wpautop instanceof JWL_Toggle_wpautop ) {
			remove_action( 'the_post', [ $jwl_toggle_wpautop, 'the_post' ] );
			remove_action( 'loop_end', [ $jwl_toggle_wpautop, 'loop_end' ] );
		}
	}
}
