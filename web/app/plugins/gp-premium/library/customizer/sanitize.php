<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_premium_sanitize_empty_absint' ) ) {
	function generate_premium_sanitize_empty_absint( $input ) {
		if ( '' == $input ) {
			return '';
		}

		return absint( $input );
	}
}

if ( ! function_exists( 'generate_premium_sanitize_choices' ) ) {
	/**
	 * Sanitize choices
	 */
	function generate_premium_sanitize_choices( $input, $setting ) {

		// Ensure input is a slug
		$input = sanitize_key( $input );

		// Get list of choices from the control
		// associated with the setting
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it;
		// otherwise, return the default
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
}

if ( ! function_exists( 'generate_premium_sanitize_checkbox' ) ) {
	/**
	 * Sanitize checkbox
	 */
	function generate_premium_sanitize_checkbox( $checked ) {
		// Boolean check.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
}

if ( ! function_exists( 'generate_premium_sanitize_hex_color' ) ) {
	/**
	 * Sanitize hex colors
	 * We don't use the core function as we want to allow empty values
	 *
	 * @since 0.1
	 */
	function generate_premium_sanitize_hex_color( $color ) {
	    if ( '' === $color ) {
	        return '';
		}

	    // 3 or 6 hex digits, or the empty string.
	    if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
	        return $color;
		}

	    return '';
	}
}

if ( ! function_exists( 'generate_premium_sanitize_rgba' ) ) {
	/**
	 * Sanitize RGBA colors
	 *
	 * @since 1.3.42
	 */
	function generate_premium_sanitize_rgba( $color ) {
	    if ( '' === $color ) {
	        return '';
		}

		// If string does not start with 'rgba', then treat as hex
		// sanitize the hex color and finally convert hex to rgba
		if ( false === strpos( $color, 'rgba' ) ) {
			return generate_premium_sanitize_hex_color( $color );
		}

		// By now we know the string is formatted as an rgba color so we need to further sanitize it.
		$color = str_replace( ' ', '', $color );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba('.$red.','.$green.','.$blue.','.$alpha.')';

	    return '';
	}
}

if ( ! function_exists( 'generate_premium_sanitize_decimal_integer' ) ) {
	/**
	 * Sanitize integers that can use decimals
	 *
	 * @since 1.3.41
	 */
	function generate_premium_sanitize_decimal_integer( $input ) {
		return abs( floatval( $input ) );
	}
}

/**
 * Sanitize integers that can use decimals
 * @since 1.4
 */
function generate_premium_sanitize_decimal_integer_empty( $input ) {
	if ( '' == $input ) {
		return '';
	}

	return abs( floatval( $input ) );
}

if ( ! function_exists( 'generate_premium_sanitize_html' ) ) {
	/**
	 * Sanitize our fields that accept HTML
	 */
	function generate_premium_sanitize_html( $input ) {
		return wp_kses_post( $input );
	}
}

function generate_premium_sanitize_variants( $input ) {
	if ( is_array( $input ) ) {
		$input = implode( ',', $input );
	}

	return sanitize_text_field( $input );
}
