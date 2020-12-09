<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Events__Pro__Admin__Settings {

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return self
	 *
	 */
	public static function instance() {
		return tribe( 'events-pro.admin.settings' );
	}

	/**
	 * Hook the required Methods to the correct filters/actions
	 *
	 * @return void
	 */
	public function hook() {
		add_filter( 'tribe_settings_tab_fields', array( $this, 'inject_mobile_fields' ), 10, 2 );
		add_filter( 'tribe_events_header_attributes', array( $this, 'include_mobile_default_view' ) );
	}

	/**
	 * Filters the Settings Fields to add the mobile fields
	 *
	 * @param  array  $settings An Array for The Events Calendar fields
	 * @param  string $id       Which tab you are dealing field
	 *
	 * @return array
	 */
	public function inject_mobile_fields( $settings, $id ) {
		// We don't care about other tabs
		if ( 'display' !== $id ) {
			return $settings;
		}

		// Include the fields and replace with the return from the include
		$settings = include Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/tribe-options-mobile.php';

		return $settings;
	}

	/**
	 * Include the Headers to make Default Mobile view works
	 *
	 * @param  array  $attrs       The original Attributes
	 * @return array
	 */
	public function include_mobile_default_view( $attrs ) {
		$attrs['data-redirected-view'] = tribe_get_request_var( 'tribe_redirected' );
		$attrs['data-default-mobile-view'] = tribe_get_mobile_default_view();
		$attrs['data-default-view'] = Tribe__Events__Main::instance()->default_view();

		return $attrs;
	}

}
