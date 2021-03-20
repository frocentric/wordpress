<?php
/**
 * Widget Admin Templates
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

/**
 * Class Admin_Template
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Admin_Template extends \Tribe__Template {
	/**
	 * Template constructor.
	 *
	 * @since 5.2.0
	 */
	public function __construct() {
		$this->set_template_origin( tribe( 'events-pro.main' ) );
		$this->set_template_folder( 'src/admin-views' );

		// We specifically don't want to look up template files here.
		$this->set_template_folder_lookup( false );

		// Configures this templating class extract variables.
		$this->set_template_context_extract( true );
	}
}
