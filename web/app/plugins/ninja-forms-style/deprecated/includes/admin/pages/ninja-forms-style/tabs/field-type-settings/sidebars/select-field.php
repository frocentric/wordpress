<?php

add_action( 'admin_init', 'ninja_forms_register_style_sidebar_select_field' );
function ninja_forms_register_style_sidebar_select_field(){
	$args = array(
		'name' => __( 'Select A Field Type', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'tab' => 'field_type_settings',
		'display_function' => 'ninja_forms_style_sidebar_select_field_display',
		'save_function' => '',
	);

	if( function_exists( 'ninja_forms_register_sidebar' ) ){
		ninja_forms_register_sidebar('select_subs', $args);
	}
	
}

function ninja_forms_style_sidebar_select_field_display(){
	global $ninja_forms_fields;
	$ninja_forms_fields['_list']['type_dropdown_function'] = 'ninja_forms_list_field_type_dropdown';
	$args = array();
	if( isset( $_REQUEST['field_type'] ) ){
		$args['selected'] = $_REQUEST['field_type'];
	}
	ninja_forms_field_type_dropdown( $args );
}

/*
 *
 * Function that outputs select options for our "list" field type, since it has sub-types.
 *
 * @since 1.0.3
 * @returns string $output
 */

function ninja_forms_list_field_type_dropdown( $selected ){
	$output = '<option value="" disabled>' . __( 'List', 'ninja-forms-style' ) . '</option>';

	if ( $selected == '_list-dropdown' ) {
		$select = 'selected="selected"';
	} else {
		$select = '';
	}
	$output .= '<option value="_list-dropdown" '.$select.'>&nbsp;&nbsp;&nbsp;&nbsp;' . __( 'Dropdown (Select)', 'ninja-forms-style' ) . '</option>';

	if ( $selected == '_list-radio' ) {
		$select = 'selected="selected"';
	} else {
		$select = '';
	}
	$output .= '<option value="_list-radio" '.$select.'>&nbsp;&nbsp;&nbsp;&nbsp;' . __( 'Radio', 'ninja-forms-style' ) . '</option>';

	if ( $selected == '_list-checkbox' ) {
		$select = 'selected="selected"';
	} else {
		$select = '';
	}
	$output .= '<option value="_list-checkbox" '.$select.'>&nbsp;&nbsp;&nbsp;&nbsp;' . __( 'Checkboxes', 'ninja-forms-style' ) . '</option>';

	if ( $selected == '_list-multi' ) {
		$select = 'selected="selected"';
	} else {
		$select = '';
	}
	$output .= '<option value="_list-multi" '.$select.'>&nbsp;&nbsp;&nbsp;&nbsp;' . __( 'Multi-Select', 'ninja-forms-style' ) . '</option>';

	return $output;
}