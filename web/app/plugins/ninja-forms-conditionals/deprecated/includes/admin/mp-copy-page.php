<?php
/**
 * Hook into our copy page multi-part form action.
 * Change any old ids to their new ones.
 *
 */

function nf_cl_mp_copy_page( $new_fields ) {

	foreach ( $new_fields as $old_field => $new_field ) {
		$field = ninja_forms_get_field_by_id( $new_field );
		if ( isset ( $field['data']['conditional'] ) ) {

			for ($x=0; $x < count( $field['data']['conditional'] ); $x++) { 
				if ( isset ( $field['data']['conditional'][ $x ]['cr'] ) ) {

					for ($y=0; $y < count( $field['data']['conditional'][ $x ]['cr'] ); $y++) { 
						if ( isset ( $field['data']['conditional'][ $x ]['cr'][ $y ]['field'] ) ) {
							$current_id = $field['data']['conditional'][ $x ]['cr'][ $y ]['field'];

							if ( isset ( $new_fields[ $current_id ] ) ) {
								$field['data']['conditional'][ $x ]['cr'][ $y ]['field'] = $new_fields[ $current_id ];
							}
						}
					}
				}
			}
		}
		$field['data'] = serialize( $field['data'] );
		$args = array(
			'update_array' => array(
				'data' => $field['data'],
				),
			'where' => array(
				'id' => $new_field,
				),
		);
		ninja_forms_update_field( $args );
	}
}

add_action( 'nf_mp_copy_page', 'nf_cl_mp_copy_page' );