<?php
/**
 * The base template all Views templates will use to locate, manage, and render their HTML code.
 *
 * @since   5.0.0
 * @package Tribe\Events\Filterbar\Views\V2_1
 */

namespace Tribe\Events\Filterbar\Views\V2_1;

use Tribe__Events__Filterbar__View as Plugin;
use Tribe__Events__Main as TEC;
use Tribe__Template as Base_Template;

/**
 * Class Events Filterbar Views V2_1 Templates loader
 *
 * @since   5.0.0
 * @package Tribe\Events\Filterbar\Views\V2_1
 */
class Template extends Base_Template {
	use \Tribe\Events\Filterbar\Views\V2\With_Shortcode_Support;

	/**
	 * Template constructor.
	 *
	 * @since  5.0.0
	 *
	 * @param string $slug The slug the template should use to build its path.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->set_template_origin( Plugin::instance() )
		     ->set_template_folder( 'src/views/v2_1' )
		     ->set_template_folder_lookup( true )
		     ->set_template_context_extract( true )
		     ->set_aliases( [ 'v2_1' => 'v2' ] );
	}

	/**
	 * Overrides the base method to add The Events Calendar as a possible source of templates to look up.
	 *
	 * The Events Calendar will be searched for templates after Filter Bar (priority 20) and before Common
	 * (priority 100).
	 *
	 * @since 5.0.0
	 *
	 * @return array<string,array> A list of folders that should be searched for templates.
	 */
	protected function get_template_path_list() {
		$path_list = parent::get_template_path_list();
		$fbar_root = Plugin::instance()->pluginPath;
		$tec_root  = TEC::instance()->plugin_path;
		foreach (
			[
				'plugin'    => 'the-events-calendar',
				'plugin_v2' => 'the-events-calendar_v2'
			] as $plugin_slug => $tec_slug
		) {
			if ( ! isset( $path_list[ $tec_slug ] ) && isset( $path_list[ $plugin_slug ] ) ) {
				$path_list[ $tec_slug ]             = $path_list[ $plugin_slug ];
				$path_list[ $tec_slug ]['id']       = $tec_slug;
				$path_list[ $tec_slug ]['priority'] = 30;
				$fbar_path                          = $path_list[ $plugin_slug ]['path'];
				$path_list[ $tec_slug ]['path']     = str_replace( $fbar_root, $tec_root, $fbar_path );
			}
		}

		uasort( $path_list, 'tribe_sort_by_priority' );

		return $path_list;
	}
}
