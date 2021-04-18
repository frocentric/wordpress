<?php
/*
 *
 * Function that filters the confirmation page in multi-part forms so that the filename is displayed properly.
 *
 * @since 1.0.4
 * @returns $user_value
 */

function ninja_forms_uploads_filter_mp_confirm_value( $user_value, $field_id ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	if ( $field_type == '_upload' ) {
		if ( is_array( $user_value ) ) {
			foreach( $user_value as $key => $file ){
				$user_value = $file['user_file_name'];
			}
		}
	}
	return $user_value;
}

add_filter( 'ninja_forms_mp_confirm_user_value', 'ninja_forms_uploads_filter_mp_confirm_value', 10, 2 );