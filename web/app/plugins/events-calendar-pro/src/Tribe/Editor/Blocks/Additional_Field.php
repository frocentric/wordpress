<?php

class Tribe__Events__Pro__Editor__Blocks__Additional_Field extends Tribe__Editor__Blocks__Abstract {

	private $slug = '';

	/**
	 * Tribe__Events__Pro__Editor__Blocks__Additional_Field constructor.
	 *
	 * @since 4.5
	 *
	 * @param $slug
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Which is the name/slug of this block
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function slug() {
		return $this->slug;
	}

	/**
	 * Does the registration for PHP rendering for the Block, important due to been
	 * an dynamic Block
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function register() {
		$block_args = array(
			'render_callback' => array( $this, 'render' ),
		);

		register_block_type( $this->name(), $block_args );

		add_action( 'wp_ajax_' . $this->get_ajax_action(), array( $this, 'ajax' ) );

		$this->assets();
		$this->hook();
	}

	/**
	 * Set the default attributes of this block
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function default_attributes() {
		return array(
			'type'  => 'text',
			'label' => '',
			'value' => '',
		);
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it
	 *
	 * @since 4.5
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = array() ) {
		$attributes = $this->get_meta_data( $attributes );

		// Return early if no meta key is found.
		if ( empty( $attributes['meta_key'] ) ) {
			return;
		}

		$attributes['value'] = get_post_meta( get_the_ID(), $attributes['meta_key'], true );
		$attributes          = $this->set_checkbox_attributes( $attributes );
		$args['attributes']  = $this->attributes( $attributes );

		// Add the rendering attributes into global context
		tribe( 'events-pro.editor.frontend.template' )->add_template_globals( $args );

		$type     = isset( $attributes['type'] ) ? $attributes['type'] : 'text';
		$location = array( 'blocks', 'additional-fields', $type );

		return tribe( 'events-pro.editor.frontend.template' )->template( $location, $args, false );
	}

	/**
	 * Get meta data of the custom field.
	 *
	 * @since 5.1.2
	 *
	 * @param  array $attributes The block attributes.
	 *
	 * @return array The attributes with meta data of the custom field.
	 */
	protected function get_meta_data( $attributes ) {
		$custom_fields = (array) tribe_get_option( 'custom-fields' );

		foreach ( $custom_fields as $custom_field ) {
			if ( empty( $custom_field['name'] ) ) {
				continue;
			}

			$block_name = $this->get_block_name_from_meta_key( $custom_field['name'] );

			if ( str_replace( 'tribe/field-', '', $this->name() ) !== $block_name ) {
				continue;
			}

			$attributes['meta_key'] = $custom_field['name'];
			$attributes['type']     = $custom_field['type'];

			if ( isset( $custom_field['label'] ) ) {
				$attributes['label'] = $custom_field['label'];
			}

			break;
		}

		return $attributes;
	}

	/**
	 * Get the block name from meta key provided.
	 * Removes any non-numeric, a-z, A-Z, and dash characters.
	 *
	 * @since 5.1.2
	 *
	 * @param  string $meta_key Meta key to convert to block name.
	 *
	 * @return string Meta key converted to block name.
	 */
	protected function get_block_name_from_meta_key( $meta_key ) {
		return preg_replace( '/[^a-zA-Z0-9-]/', '', $meta_key );
	}

	/**
	 * Add attributes if block type is checkbox.
	 *
	 * @since 5.1.2
	 *
	 * @param  array $attributes The block attributes.
	 *
	 * @return array The block attributes, with checkbox attributes if block type is checkbox.
	 */
	protected function set_checkbox_attributes( $attributes ) {
		if ( 'checkbox' !== $attributes['type'] ) {
			return $attributes;
		}

		$attributes['dividerList'] = isset( $attributes['dividerList'] )
			? $attributes['dividerList']
			: ', ';
		$attributes['dividerEnd']  = isset( $attributes['dividerEnd'] )
			? $attributes['dividerEnd']
			: __( ' and ', 'tribe-events-calendar-pro' );
		$attributes['output']      = $this->get_checkbox_output( $attributes );

		return $attributes;
	}

	/**
	 * Get the checkbox output from attributes.
	 *
	 * @since 5.1.2
	 *
	 * @param  array $attributes The block attributes.
	 *
	 * @return string The checkbox text output.
	 */
	protected function get_checkbox_output( $attributes ) {
		$items = explode( '|', $attributes['value'] );

		if ( 1 >= count( $items ) ) {
			return implode( '', $items );
		}

		$start = implode( $attributes['dividerList'], array_slice( $items, 0, -1 ) );
		$end   = $items[ count( $items ) - 1 ];

		return "{$start}{$attributes['dividerEnd']}{$end}";
	}

	/**
	 * Register the Assets for when this block is active
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function assets() {
		tribe_asset(
			Tribe__Events__Pro__Main::instance(),
			'tribe-events-pro-additional-fields-fe',
			'app/additional-fields/frontend.css',
			array(),
			'wp_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'has_block' ),
			)
		);
	}
}
