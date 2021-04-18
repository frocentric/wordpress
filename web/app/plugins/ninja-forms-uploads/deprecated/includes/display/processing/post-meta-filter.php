<?php

/*
 *
 * Function that filters the post meta values if the field is an upload field.
 *
 * @since 1.0.11
 * @return $value
 */

function ninja_forms_upload_post_meta_filter( $user_value, $meta_key, $field_id ){
	global $ninja_forms_processing;

	$field = $ninja_forms_processing->get_field_settings( $field_id );
	if ( $field['type'] == '_upload' ) {
		if ( is_array ( $user_value ) ) {
			foreach ( $user_value as $key => $data ) {
				if ( isset ( $data['file_url'] ) ) {
					$user_value = $data['file_url'];					
				}
				break;
			}
		}
	}
	return $user_value;
}

add_filter( 'ninja_forms_add_post_meta_value', 'ninja_forms_upload_post_meta_filter', 10, 3 );