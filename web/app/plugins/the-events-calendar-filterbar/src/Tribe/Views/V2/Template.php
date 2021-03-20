<?php
/**
 * The base template all Views templates will use to locate, manage, and render their HTML code.
 *
 * @package Tribe\Events\Filterbar\Views\V2
 * @since   4.9.0
 */

namespace Tribe\Events\Filterbar\Views\V2;

use Tribe__Template as Base_Template;
use Tribe__Events__Filterbar__View as Plugin;

/**
 * Class Events Filterbar Views V2 Templates loader
 *
 * @package Tribe\Events\Filterbar\Views\V2
 * @since   4.9.0
 */
class Template extends Base_Template {
	use With_Shortcode_Support;

	/**
	 * Template constructor.
	 *
	 * @since  4.9.0
	 *
	 * @param  string $slug The slug the template should use to build its path.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->set_template_origin( Plugin::instance() )
		     ->set_template_folder( 'src/views/v2' )
		     ->set_template_folder_lookup( true )
		     ->set_template_context_extract( true );
	}
}
