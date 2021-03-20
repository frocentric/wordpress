<?php
/**
 * Registers the filters and functions needed to extend The Events Calendar ORM to support
 * PRO functionality.
 *
 * @since 5.0.0
 */

/**
 * Class Tribe__Events__Pro__Service_Providers__Templates
 *
 * @since 5.0.0
 */
class Tribe__Events__Pro__Service_Providers__Templates extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations and registers the required filters.
	 *
	 * @since 5.0.0
	 */
	public function register() {
		add_filter( 'tribe_template_origin_namespace_map', [ $this, 'filter_add_template_origin_namespace' ], 15, 3 );
		add_filter( 'tribe_template_path_list', [ $this, 'filter_template_path_list' ], 15, 2 );
	}

	/**
	 * Includes Pro into the path namespace mapping, allowing for a better namespacing when loading files.
	 *
	 * @since 5.0.0
	 *
	 * @param array            $namespace_map Indexed array containing the namespace as the key and path to `strpos`.
	 * @param string           $path          Path we will do the `strpos` to validate a given namespace.
	 * @param Tribe__Template  $template      Current instance of the template class.
	 *
	 * @return array  Namespace map after adding Pro to the list.
	 */
	public function filter_add_template_origin_namespace( $namespace_map, $path, Tribe__Template $template ) {
		$main = tribe( 'events-pro.main' );
		$namespace_map[ $main->template_namespace ] = $main->pluginPath;
		return $namespace_map;
	}

	/**
	 * Filters the list of folders TEC will look up to find templates to add the ones defined by PRO.
	 *
	 * @since 5.0.0
	 *
	 * @param array           $folders  The current list of folders that will be searched template files.
	 * @param Tribe__Template $template Which template instance we are dealing with.
	 *
	 * @return array The filtered list of folders that will be searched for the templates.
	 */
	public function filter_template_path_list( array $folders = [], Tribe__Template $template = null ) {
		$main = tribe( 'events-pro.main' );

		$path = (array) rtrim( $main->pluginPath, '/' );

		// Pick up if the folder needs to be added to the public template path.
		$folder = $template->get_template_folder();

		if ( ! empty( $folder ) ) {
			$path = array_merge( $path, $folder );
		}

		$folders['events-pro'] = [
			'id'        => 'events-pro',
			'namespace' => $main->template_namespace,
			'priority'  => 25,
			'path'      => implode( DIRECTORY_SEPARATOR, $path ),
		];

		return $folders;
	}
}
