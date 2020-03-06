<?php
add_action( 'init', 'ninja_forms_register_pc_update_meta_values' );
function ninja_forms_register_pc_update_meta_values(){
	add_action( 'ninja_forms_create_post', 'ninja_forms_pc_update_meta_values' );
}

function ninja_forms_pc_update_meta_values( $post_id ){
	global $ninja_forms_processing;

	$all_fields = $ninja_forms_processing->get_all_fields();

	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		foreach( $all_fields as $field_id => $value ){
			$field_row = ninja_forms_get_field_by_id( $field_id );
			$field_data = $field_row['data'];
			if( isset( $field_data['post_meta_value'] ) AND $field_data['post_meta_value'] != '' ){
				$meta_key = $field_data['post_meta_value'];
				$value = apply_filters( 'ninja_forms_add_post_meta_value', $value, $meta_key, $field_id );
				update_post_meta( $post_id, $field_data['post_meta_value'], $value );
			}
		}
	}
}