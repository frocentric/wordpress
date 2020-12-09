<?php

/**
 * Allow including admin templates
 *
 * @since 4.5
 */
class Tribe__Events__Pro__Editor__Template__Admin extends Tribe__Template {
	/**
	 * Building of the Class template configuration
	 *
	 * @since 4.5
	 */
	public function __construct() {
		$this->set_template_origin( Tribe__Events__Pro__Main::instance() );

		$this->set_template_folder( 'src/admin-views' );

		// Configures this templating class extract variables
		$this->set_template_context_extract( true );
	}
}
