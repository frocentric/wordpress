<?php
_deprecated_file( __FILE__, '4.4.30', 'Deprecated class in favor of using `tribe_asset` registration' );

class Tribe__Events__Pro__Asset__Ajax_Photoview extends Tribe__Events__Asset__Abstract_Asset {

	public function handle() {
		$tribe_paged = ( ! empty( $_REQUEST['tribe_paged'] ) ) ? $_REQUEST['tribe_paged'] : 0;
		$ajax_data   = array(
			'ajaxurl'     => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
			'tribe_paged' => $tribe_paged,
		);

		$version = apply_filters( 'tribe_events_pro_js_version', Tribe__Events__Pro__Main::VERSION );

		$imagesloaded_path  = Tribe__Events__Template_Factory::getMinFile( $this->vendor_url . 'imagesloaded/imagesloaded.pkgd.js', true );
		wp_enqueue_script( 'tribe-events-pro-imagesloaded', $imagesloaded_path, array( 'tribe-events-pro' ), $version, true );

		$isotope_path       = Tribe__Events__Template_Factory::getMinFile( $this->vendor_url . 'isotope/isotope.pkgd.js', true );
		wp_enqueue_script( 'tribe-events-pro-isotope', $isotope_path, array( 'tribe-events-pro-imagesloaded' ), $version, true );

		$photoview_path     = Tribe__Events__Template_Factory::getMinFile( tribe_events_pro_resource_url( 'tribe-events-photo-view.js' ), true );
		wp_enqueue_script( 'tribe-events-pro-photo', $photoview_path, array( 'tribe-events-pro-isotope' ), $version, true );


		wp_localize_script( 'tribe-events-pro-photo', 'TribePhoto', $ajax_data );
	}
}
