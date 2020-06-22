<?php
class Tribe__Events__Pro__Editor__Blocks__Related_Events
extends Tribe__Editor__Blocks__Abstract {

	/**
	 * Which is the name/slug of this block
	 *
	 * @since 4.6.2
	 *
	 * @return string
	 */
	public function slug() {
		return 'related-events';
	}

	/**
	 * Set the default attributes of this block
	 *
	 * @since 4.6.2
	 *
	 * @return string
	 */
	public function default_attributes() {

		$defaults = array(
			'title' => esc_html__( 'Related Events', 'tribe-events-calendar-pro' ),
		);

		return $defaults;
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since 4.6.2
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = array() ) {
		$args['attributes'] = $this->attributes( $attributes );
		$args['post_id'] = $post_id = tribe( 'events.editor.template' )->get( 'post_id', null, false );

		$args['events'] = tribe_get_related_posts();

		// Add the rendering attributes into global context
		tribe( 'events-pro.editor.frontend.template' )->add_template_globals( $args );

		return tribe( 'events-pro.editor.frontend.template' )->template( array( 'blocks', $this->slug() ), $args, false );
	}

	/**
	 * Register the Assets for when this block is active
	 *
	 * @since 4.6.2
	 *
	 * @return void
	 */
	public function assets() {
		tribe_asset(
			Tribe__Events__Pro__Main::instance(),
			'tribe-events-pro-' . $this->slug(),
			'app/' . $this->slug() . '/frontend.css',
			array(),
			'wp_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'has_block' ),
			)
		);
	}
}
