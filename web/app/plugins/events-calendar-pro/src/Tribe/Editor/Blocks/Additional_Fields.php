<?php

/**
 * Class Tribe__Events__Pro__Editor__Blocks__Additional_Fields
 */
class Tribe__Events__Pro__Editor__Blocks__Additional_Fields {
	/**
	 * Register all the additionals fields as they might have
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function register() {
		$blocks = tribe( 'events-pro.editor.fields' )->get_block_names();
		foreach ( $blocks as $block ) {
			$field = new Tribe__Events__Pro__Editor__Blocks__Additional_Field( $block );
			$field->register();
		}
	}
}
