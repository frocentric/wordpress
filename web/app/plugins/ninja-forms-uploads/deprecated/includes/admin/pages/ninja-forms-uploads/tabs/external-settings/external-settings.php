<?php
add_action('init', 'ninja_forms_external_settings', 1);
function ninja_forms_external_settings() {
	$load = false;
	if ( isset( $_GET['page'] ) &&  'ninja-forms-uploads' == $_GET['page'] && isset( $_GET['tab'] ) &&  'external_settings' == $_GET['tab'] ) {
		$load = true;
	}

	if ( isset( $_GET['page'] ) &&  'ninja-forms' == $_GET['page'] && isset( $_GET['tab'] ) &&  'field_settings' == $_GET['tab'] ) {
		$load = true;
	}

	if ( $load ) {
		nf_fu_load_externals();
	}
}

add_action( 'ninja_forms_pre_process', 'ninja_forms_pre_process_load_externals', 1 );
function ninja_forms_pre_process_load_externals() {
	global $ninja_forms_processing;
	if ( $ninja_forms_processing->get_form_setting( 'create_post' ) != 1 ) {
		if ( $ninja_forms_processing->get_extra_value( 'uploads' ) ) {
			foreach ( $ninja_forms_processing->get_extra_value( 'uploads' ) as $field_id ) {
				$field_row = $ninja_forms_processing->get_field_settings( $field_id );
				if ( isset( $field_row['data']['upload_location'] ) AND ninja_forms_upload_is_location_external( $field_row['data']['upload_location'] ) ) {
					nf_fu_load_externals();
				}
			}
		}
	}
}

add_action('admin_init', 'ninja_forms_register_tab_external_settings');
function ninja_forms_register_tab_external_settings(){
    $args = array(
        'name' => __( 'External Settings', 'ninja-forms-uploads' ),
        'page' => 'ninja-forms-uploads',
        'display_function' => '',
        'save_function' => 'ninja_forms_save_upload_settings',
        'tab_reload' => true,
    );
    if( function_exists( 'ninja_forms_register_tab' ) ){
        ninja_forms_register_tab('external_settings', $args);
    }
}

add_action( 'admin_init', 'ninja_forms_external_url' );
if ( defined( 'NINJA_FORMS_UPLOADS_USE_PUBLIC_URL') && NINJA_FORMS_UPLOADS_USE_PUBLIC_URL ) {
	add_action('template_redirect', 'ninja_forms_external_url');
}

function ninja_forms_external_url() {
	if ( isset( $_GET['nf-upload'] ) ) {
		$args = array(
			'id' => $_GET['nf-upload']
		);
		$upload = ninja_forms_get_uploads( $args );
		$external = NF_Upload_External::instance( $upload['data']['upload_location'] );
		if ( $external ) {
			$path     = ( isset( $upload['data']['external_path'] ) ) ? $upload['data']['external_path'] : '';
			$filename = ( isset( $upload['data']['external_filename'] ) ) ? $upload['data']['external_filename'] : $upload['data']['file_name'];
			$file_url = $external->file_url( $filename, $path, $upload['data'] );
		}
		wp_redirect( $file_url );
		die();
	}
}

function ninja_forms_upload_file_url( $data ) {
	nf_fu_load_externals();
	$file_url = isset ( $data['file_url'] ) ? $data['file_url'] : '';
	if ( isset( $data['upload_location'] ) && ( isset( $data['upload_id'] ) ) && ninja_forms_upload_is_location_external( $data['upload_location'] ) ) {
		$external = NF_Upload_External::instance( $data['upload_location'] );
		if ( $external && $external->is_connected() ) {
			$url_path = '?nf-upload='. $data['upload_id'];
			if ( defined( 'NINJA_FORMS_UPLOADS_USE_PUBLIC_URL') && NINJA_FORMS_UPLOADS_USE_PUBLIC_URL ) {
				$file_url = home_url( $url_path );
			} else {
				$file_url = admin_url( $url_path );
			}
		}
	}

	return $file_url;
}

function ninja_forms_upload_is_location_external( $location ) {
	return ! in_array( $location, array( NINJA_FORMS_UPLOADS_DEFAULT_LOCATION, 'none' ) );
}