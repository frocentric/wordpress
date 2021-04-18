<?php

/*
 *
 * Function that filters the email value so that only the file name is sent in the email list.
 *
 * @since 1.0.11
 * @return $user_value string
 */

function ninja_forms_upload_email_value_filter( $user_value, $field_id ) {
	global $ninja_forms_processing;

	$field = $ninja_forms_processing->get_field_settings( $field_id );
	if ( $field['type'] == '_upload' ) {
		if ( is_array ( $user_value ) ) {
			$tmp_array = array();
			foreach ( $user_value as $key => $data ) {
				if ( isset ( $data['user_file_name'] ) ) {
					$tmp_array[] = $data['user_file_name'];
				}
			}
			if ( empty( $tmp_array ) ) {
				$tmp_array = '';
			}
			$user_value = $tmp_array;
		}
	}

	return $user_value;
}

add_filter( 'ninja_forms_email_user_value', 'ninja_forms_upload_email_value_filter', 10, 2 );