<?php
/**
 * GeneratePress Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants as Constants;
use Frocentric\Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeneratePress Class.
 */
class GeneratePress {

	/**
	 * Filter post date markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   array
	 * @since    1.0.0
	 */
	public static function generate_inside_post_meta_item_output( $output, $item ) {
		if ( 'author' === $item ) {
			$user_id = get_the_author_meta( 'ID' );
			$output  = sprintf( '<a href="%1$s" class="avatar-link">%2$s</a>', get_author_posts_url( $user_id ), get_avatar( $user_id, 32 ) );
		}

		return $output;
	}

	/**
	 * Filter post date markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   array
	 * @since    1.0.0
	 */
	public static function generate_post_date_output( $output, $time_string ) {
		return sprintf( // WPCS: XSS ok, sanitization ok.
			'<span class="posted-on">%1$s%2$s</span> ',
			apply_filters( 'generate_inside_post_meta_item_output', '', 'date' ),
			$time_string
		);
	}

	/**
	 * Filter SVG icon markup.
	 * Removes default icon markup.
	 *
	 * @param    string $output      Default output.
	 * @param    string $icon        The icon.
	 * @return   string
	 * @since    1.0.0
	 */
	public static function generate_svg_icon( $output, $icon ) {
		$output = str_replace(
			'<svg viewBox="0 0 512 512" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"><path d="M71.029 71.029c9.373-9.372 24.569-9.372 33.942 0L256 222.059l151.029-151.03c9.373-9.372 24.569-9.372 33.942 0 9.372 9.373 9.372 24.569 0 33.942L289.941 256l151.03 151.029c9.372 9.373 9.372 24.569 0 33.942-9.373 9.372-24.569 9.372-33.942 0L256 289.941l-151.029 151.03c-9.373 9.372-24.569 9.372-33.942 0-9.372-9.373-9.372-24.569 0-33.942L222.059 256 71.029 104.971c-9.372-9.373-9.372-24.569 0-33.942z" /></svg>',
			'',
			$output
		);

		return $output;
	}

	/**
	 * Generate SVG icon markup for navigation menu.
	 *
	 * @param    string $output      Default output.
	 * @param    string $time_string Post time string.
	 * @return   string
	 * @since    1.0.0
	 */
	public static function generate_svg_icon_element( $output, $icon ) {
		if ( 'menu-bars' === $icon ) {
			$output = '<img alt="Open menu" src="' . esc_url( get_stylesheet_directory_uri() ) . '/images/burger-menu-open.svg" class="open-menu" />';
			$output .= '<img alt="Close menu" src="' . esc_url( get_stylesheet_directory_uri() ) . '/images/burger-menu-close.svg" class="close-menu" />';
		}

		return $output;
	}

	/**
	 * Add custom fonts to GeneratePress font list.
	 *
	 * @param    string[] $fonts    Array of loaded fonts.
	 * @return   array
	 * @since    1.0.0
	 */
	public static function generate_typography_default_fonts( $fonts ) {
		$fonts[] = 'Helvetica Neue Condensed Bold';
		$fonts[] = 'Nunito Sans';

		sort( $fonts );

		return $fonts;
	}

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
			add_filter( 'generate_inside_post_meta_item_output', [ __CLASS__, 'generate_inside_post_meta_item_output' ], 20, 2 );
			add_filter( 'generate_post_date_output', [ __CLASS__, 'generate_post_date_output' ], 10, 2 );
			add_filter( 'generate_svg_icon', [ __CLASS__, 'generate_svg_icon' ], 10, 2 );
			add_filter( 'generate_svg_icon_element', [ __CLASS__, 'generate_svg_icon_element' ], 10, 2 );
			add_filter( 'generate_typography_default_fonts', [ __CLASS__, 'generate_typography_default_fonts' ] );
		}
	}
}
