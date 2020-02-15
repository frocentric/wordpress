<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_pro_sanitize_intval' ) ) {
	/**
	 * Sanitize our value so it has to be a positive integer
	 * @since 0.1
	 */
	function wpsp_pro_sanitize_intval( $input ) {
		if ( '' == $input ) {
			return $input;
		}

		return intval( $input );
	}
}

if ( ! function_exists( 'wpsp_pro_sanitize_absint' ) ) {
	/**
	 * Sanitize our value so it can be a negative or positive integer
	 * @since 0.1
	 */
	function wpsp_pro_sanitize_absint( $input ) {
		if ( '' == $input ) {
			return $input;
		}

		return absint( $input );
	}
}