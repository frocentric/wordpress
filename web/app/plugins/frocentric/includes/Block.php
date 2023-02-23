<?php
/**
 * Handle Gutenberg blocks registration.
 *
 * @class       Block
 * @version     1.0.0
 * @package     Frocentric/Classes/
 */

namespace Frocentric;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block main class
 */
final class Block {

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
	}

	/**
	 * Register Gutenberg blocks
	 *
	 * @return void
	 */
	public static function register_blocks() {

		// A register block for each existing block.
		$blocks = apply_filters( 'frocentric_gutenberg_blocks', array() );

		foreach ( $blocks as $key => $block ) {

			$block_name = is_string( $block ) ? $block : $key;
			$args       = is_string( $block ) ? array() : $block;

			if ( is_admin() ) {
				self::register_admin( $block_name, $args );
			}

			self::register( $block_name, $args );
		}
	}

	/**
	 * Register a new block for admin
	 *
	 * @param string $block Block name.
	 * @param array  $args  Block arguments.
	 * @return void
	 */
	public static function register_admin( $block = '', $args = array() ) {

		$block_name = empty( $block ) ? PREFIX : $block;
		$block_path = $block . '/';

		if ( ! file_exists( Utils::plugin_path() . '/assets/build/' . $block_path . 'index.asset.php' ) ) {
			return;
		}

		$asset_block = include Utils::plugin_path() . '/assets/build/' . $block_path . 'index.asset.php';

		wp_register_script(
			'frocentric-' . $block_name . '-blocks',
			Utils::plugin_url() . '/assets/build/' . $block_path . 'index.js',
			$asset_block['dependencies'],
			$asset_block['version'],
			true
		);
	}

	/**
	 * Register a new block
	 *
	 * @param string $block Block name.
	 * @param array  $args  Block arguments.
	 * @return void
	 */
	public static function register( $block = '', $args = array() ) {

		$block_name = empty( $block ) ? PREFIX : $block;

		// Set frontend styles.
		$styles = array();
		if ( ! empty( $args['styles'] ) ) {
			array_push( $styles, $args['styles'] );
		}

		// Set frontend script.
		$script = '';
		if ( ! empty( $args['script'] ) ) {
			$script = $args['script'];
		}

		// Set editor styles.
		$editor_style = 'frocentric-' . $block_name . '-blocks';
		if ( ! empty( $args['editor_style'] ) ) {
			$editor_style = $args['editor_style'];
		}

		register_block_type(
			'frocentric/' . $block_name,
			array(
				'style'         => $styles,
				'script'        => $script,
				'editor_style'  => $editor_style,
				'editor_script' => 'frocentric-' . $block_name . '-blocks',
			)
		);
	}
}
