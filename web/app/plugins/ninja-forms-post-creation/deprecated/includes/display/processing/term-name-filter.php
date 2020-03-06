<?php
/*
 *
 * Function to filter the term IDS and return the term names.
 *
 * @since 1.0.6
 * @returns void
 */

function ninja_forms_filter_term_ids_for_name( $val, $field_id ){

	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( $field_row['type'] == '_list' ) {
		if ( isset( $field_row['data']['populate_term'] ) and !empty ( $field_row['data']['populate_term'] ) ) {
			$tax = $field_row['data']['populate_term'];
			if ( !is_array( $val ) ) {
				if ( strpos( $val, "," ) !== false ) {
					$val = explode( ",", $val );
				}				
			}

			if ( is_array( $val ) ) {
				$tmp = '';
				$x = 0;
				foreach ( $val as $v ) {
					$term_obj = get_term( $v, $tax );
					if ( $term_obj AND !is_wp_error( $term_obj ) ) {
						if ( $x == 0 ) {
							$tmp .= $term_obj->name;
						} else {
							$tmp .= ', '.$term_obj->name;
						}
						$x++;			
					}
				}
				$val = $tmp;
			} else {
				$term_obj = get_term( $val, $tax );
				if ( $term_obj AND !is_wp_error( $term_obj ) ) {
					$val = $term_obj->name;					
				}
			}
		}	
	}

	return $val;
}

add_filter( 'ninja_forms_email_user_value', 'ninja_forms_filter_term_ids_for_name', 10, 2 );
add_filter( 'ninja_forms_export_sub_value', 'ninja_forms_filter_term_ids_for_name', 10, 2 );

/*
 *
 * Function to filter the term IDS and return the term names for the backend submission editor.
 *
 * @since 1.0.6
 * @returns void
 */

function ninja_forms_filter_term_ids_for_name_sub_td( $val, $field_id, $sub_id ){
	return ninja_forms_filter_term_ids_for_name( $val, $field_id );
}

add_filter( 'ninja_forms_view_sub_td', 'ninja_forms_filter_term_ids_for_name_sub_td', 10, 3 );