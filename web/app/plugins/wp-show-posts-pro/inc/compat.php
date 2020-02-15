<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wpsp_before_wrapper', 'wpsp_pro_wrapper_id_start' );
function wpsp_pro_wrapper_id_start() {
	if ( WPSP_VERSION < 1.0 ) {
		global $wpsp_id;
		echo '<div id="wpsp-' . $wpsp_id . '" style="margin:0;">';
	}
}

add_action( 'wpsp_after_wrapper','wpsp_pro_wrapper_id_end' );
function wpsp_pro_wrapper_id_end() {
	if ( WPSP_VERSION < 1.0 ) {
		echo '</div>';
	}
}