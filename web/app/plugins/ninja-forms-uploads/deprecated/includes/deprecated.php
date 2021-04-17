<?php
/*
 *
 * Function that sets admin email attachment for this file if it is enabled.
 *
 * @since 1.0.7
 * @returns void
 */

function ninja_forms_upload_email_attachment( $field_id ){
	global $ninja_forms_processing;

	$field = ninja_forms_get_field_by_id( $field_id );
	$field_data = $field['data'];
	if ( isset ( $field_data['email_attachment'] ) AND $field_data['email_attachment'] == 1 ){

		$files = $ninja_forms_processing->get_field_value( $field_id );

		if ( is_array ( $files ) ) {
			foreach ( $files as $key => $val ) {
				if ( isset ( $val['file_path'] ) ) {
					$upload_path = $val['file_path'];
					$file_name = $val['file_name'];
					$attach_files = $ninja_forms_processing->get_form_setting( 'admin_attachments' );
					array_push( $attach_files, $upload_path.'/'.$file_name );
					$ninja_forms_processing->update_form_setting( 'admin_attachments', $attach_files );					
				}
			}
		}
	}
}

add_action( 'ninja_forms_upload_process', 'ninja_forms_upload_email_attachment' );