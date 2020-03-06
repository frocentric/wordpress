<?php

add_action( 'init', 'ninja_forms_conditionals_register_import_filter' );
function ninja_forms_conditionals_register_import_filter(){
	add_action( 'ninja_forms_after_import_form', 'ninja_forms_conditionals_after_import_form' );
}

function ninja_forms_conditionals_after_import_form( $form ){
	global $wpdb;

	if( is_array( $form['field'] ) AND !empty( $form['field'] ) ){
		$field_rows = ninja_forms_get_fields_by_form_id( $form['id'] );
		if( is_array( $field_rows ) AND !empty( $field_rows ) ){
			for ($y=0; $y < count( $field_rows ); $y++) {
				if( isset( $field_rows[$y]['data']['conditional'] ) AND is_array( $field_rows[$y]['data']['conditional'] ) ){
					for ($i=0; $i < count( $field_rows[$y]['data']['conditional'] ); $i++) { 
						if( isset( $field_rows[$y]['data']['conditional'][$i]['cr'] ) AND is_array( $field_rows[$y]['data']['conditional'][$i]['cr'] ) ){
							for ($n=0; $n < count( $field_rows[$y]['data']['conditional'][$i]['cr'] ); $n++) { 
								foreach( $form['field'] as $inserted_field ){
									if( $inserted_field['old_id'] == $field_rows[$y]['data']['conditional'][$i]['cr'][$n]['field'] ){
										$field_rows[$y]['data']['conditional'][$i]['cr'][$n]['field'] = $inserted_field['id'];
									}
								}
							}
						}
					}
				}
				$field_rows[$y]['data'] = serialize( $field_rows[$y]['data'] );
				$args = array(
					'update_array' => array(
						'data' => $field_rows[$y]['data'],
						),
					'where' => array(
						'id' => $field_rows[$y]['id'],
						),
				);
				ninja_forms_update_field($args);
			}
		}
	}
}