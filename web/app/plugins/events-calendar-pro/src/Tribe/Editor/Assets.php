<?php
/**
 * Events Gutenberg Assets
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Assets {
	/**
	 * Registers and Enqueues the assets
	 *
	 * @since 4.5
	 *
	 * @param string $key Which key we are checking against
	 *
	 * @return boolean
	 */
	public function register() {
		$plugin = Tribe__Events__Pro__Main::instance();

		tribe_asset(
			$plugin,
			'tribe-pro-gutenberg-data',
			'app/data.js',
			/**
			 * @todo revise this dependencies
			 */
			array(
				'react',
				'react-dom',
				'wp-components',
				'wp-api',
				'wp-api-request',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
				'tribe-common-gutenberg-data',
				'tribe-common-gutenberg-utils',
				'tribe-common-gutenberg-store',
				'tribe-common-gutenberg-hoc',
			),
			'enqueue_block_editor_assets',
			array(
				'in_footer' => false,
				'localize'  => array(),
				'priority'  => 200,
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
			)
		);

		tribe_asset(
			$plugin,
			'tribe-pro-gutenberg-blocks',
			'app/blocks.js',
			/**
			 * @todo revise this dependencies
			 */
			array(
				'react',
				'react-dom',
				'wp-components',
				'wp-api',
				'wp-api-request',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
				'tribe-common-gutenberg-data',
				'tribe-common-gutenberg-utils',
				'tribe-common-gutenberg-store',
				'tribe-common-gutenberg-icons',
				'tribe-common-gutenberg-hoc',
				'tribe-common-gutenberg-elements',
				'tribe-common-gutenberg-components',
			),
			'enqueue_block_editor_assets',
			array(
				'in_footer' => false,
				'localize'  => array(),
				'priority'  => 201,
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
			)
		);

		tribe_asset(
			$plugin,
			'tribe-pro-gutenberg-blocks-styles',
			'app/blocks.css',
			array( 'tribe-common-gutenberg-elements-styles' ),
			'enqueue_block_editor_assets',
			array(
				'in_footer' => false,
				'localize'  => array(),
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
			)
		);

		tribe_asset(
			$plugin,
			'tribe-pro-gutenberg-elements',
			'app/elements.js',
			/**
			 * @todo revise this dependencies
			 */
			array(
				'react',
				'react-dom',
				'wp-components',
				'wp-api',
				'wp-api-request',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'tribe-common-gutenberg-data',
				'tribe-common-gutenberg-utils',
				'tribe-common-gutenberg-store',
				'tribe-common-gutenberg-icons',
				'tribe-common-gutenberg-hoc',
				'tribe-common-gutenberg-elements',
				'tribe-common-gutenberg-components'
			),
			'enqueue_block_editor_assets',
			array(
				'in_footer' => false,
				'localize'  => array(),
				'priority'  => 202,
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
			)
		);

		tribe_asset(
			$plugin,
			'tribe-pro-gutenberg-element',
			'app/elements.css',
			array( 'tribe-common-gutenberg-elements-styles' ),
			'enqueue_block_editor_assets',
			array(
				'in_footer' => false,
				'localize'  => array(),
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
			)
		);
	}
}
