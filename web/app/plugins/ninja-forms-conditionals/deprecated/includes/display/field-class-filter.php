<?php

/* 
 *
 * Function that loops through all of our fields and adds a listner class if this field is referenced in another conditional.
 *
 * @since 1.2.1
 * @return void
 */

function ninja_forms_conditionals_field_class_filter( $form_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	$field_results = ninja_forms_get_fields_by_form_id( $form_id );

	foreach( $field_results as $field ){

		if ( isset ( $field['data']['conditional'] ) ) {
			$conditional = $field['data']['conditional'];
		} else {
			$conditional = '';
		}

		if ( isset( $conditional ) AND is_array( $conditional ) ) {
			foreach ( $conditional as $conditional ) {
				if ( isset( $conditional['cr'] ) AND is_array( $conditional['cr'] ) ) {
					foreach ( $conditional['cr'] as $cr ) {
						if ( isset ( $ninja_forms_loading ) ) {
							$cr_field_class = $ninja_forms_loading->get_field_setting( $cr['field'], 'field_class' );
						} else {
							$cr_field_class = $ninja_forms_processing->get_field_setting( $cr['field'], 'field_class' );
						}

						if ( strpos ( $cr_field_class, 'ninja-forms-field-conditional-listen' ) === false ) {
							$cr_field_class .= ' ninja-forms-field-conditional-listen ';
						}

						if ( isset ( $ninja_forms_loading ) ) {
							$ninja_forms_loading->update_field_setting( $cr['field'], 'field_class', $cr_field_class );
						} else {
							$ninja_forms_processing->update_field_setting( $cr['field'], 'field_class', $cr_field_class );
						}

					}
				}
			}
		}
	}
}

add_action( 'ninja_forms_display_init', 'ninja_forms_conditionals_field_class_filter', 11, 2 );