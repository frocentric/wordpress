<?php

/**
 * Allow including of Gutenberg Template
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Template__Frontend extends Tribe__Template {
	/**
	 * Building of the Class template configuration
	 *
	 * @since 4.5
	 */
	public function __construct() {
		$this->set_template_origin( Tribe__Events__Pro__Main::instance() );

		$this->set_template_folder( 'src/views' );

		// Configures this templating class extract variables
		$this->set_template_context_extract( true );

		// Uses the public folders
		$this->set_template_folder_lookup( true );

	}

	/**
	 * Return the attributes of the template
	 *
	 * @since 4.5
	 *
	 * @param array $default_attributes
	 * @return array
	 */
	public function attributes( $default_attributes = array() ) {
		return wp_parse_args(
			$this->get( 'attributes', array() ),
			$default_attributes
		);
	}

	/**
	 * Return a specific attribute
	 *
	 * @since 4.5
	 *
	 * @param  mixed $default
	 * @return mixed
	 */
	public function attr( $index, $default = null ) {
		return $this->get( array_merge( array( 'attributes' ), (array) $index ), array(), $default );
	}
}
