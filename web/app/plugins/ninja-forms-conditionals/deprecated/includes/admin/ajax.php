<?php

add_action('wp_ajax_ninja_forms_add_conditional', 'ninja_forms_add_conditional');
function ninja_forms_add_conditional(){
	global $wpdb, $ninja_forms_fields;

	$field_id = $_REQUEST['field_id'];
	$x = $_REQUEST['x'];
	ninja_forms_field_conditional_output($field_id, $x);
	die();
}

add_action('wp_ajax_ninja_forms_add_cr', 'ninja_forms_add_cr');
function ninja_forms_add_cr(){
	global $wpdb, $ninja_forms_fields;

	$field_id = $_REQUEST['field_id'];
	$x = $_REQUEST['x'];
	$y = $_REQUEST['y'];
	$new_html = ninja_forms_return_echo('ninja_forms_field_conditional_cr_output', $field_id, $x, $y);
	$new_html = utf8_encode( $new_html );
	header("Content-type: application/json");
	$array = array ('new_html' => $new_html, 'field_id' => $field_id, 'x' => $x, 'y' => $y);
	echo json_encode($array);
	die();
}

add_action('wp_ajax_ninja_forms_change_action', 'ninja_forms_change_action');
function ninja_forms_change_action(){
	global $wpdb, $ninja_forms_fields;

	$form_id = $_REQUEST['form_id'];
	$action_slug = $_REQUEST['action_slug'];
	$field_id = $_REQUEST['field_id'];
	$x = $_REQUEST['x'];
	$field_data = $_REQUEST['field_data'];

	$field_data = $field_data['ninja_forms_field_'.$field_id];

	$field_row = ninja_forms_get_field_by_id($field_id);
	$type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$type];
	if( isset( $reg_field['conditional']['action'][$action_slug] ) ){
		$conditional = $reg_field['conditional']['action'][$action_slug];
	}else if( $action_slug == 'change_value'){
		$conditional = array( 'output' => 'text' );
	}else{
		$conditional = '';
	}

	$conditional = apply_filters( 'nf_change_conditional_action_output', $conditional, $field_id, $action_slug );

	header("Content-type: application/json");

	if( isset( $conditional['output'] ) ){
		$new_type = $conditional['output'];
	}else{
		$new_type = '';
	}

	$new_html = ninja_forms_return_echo( 'ninja_forms_field_conditional_action_output', $field_id, $x, $conditional, '', $field_data );
	$new_html = utf8_encode( $new_html );
	$array = array('new_html' => $new_html, 'new_type' => $new_type );
	echo json_encode($array);

	die();

}

add_action('wp_ajax_ninja_forms_change_cr_field', 'ninja_forms_change_cr_field');
function ninja_forms_change_cr_field(){
	global $wpdb, $ninja_forms_fields;

	$field_id = $_REQUEST['field_id'];
	$field_value = $_REQUEST['field_value'];
	$x = $_REQUEST['x'];
	$y = $_REQUEST['y'];

	$field_row = ninja_forms_get_field_by_id($field_value);
	$type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$type];
	$conditional = $reg_field['conditional'];
	$cr = array( 'field' => $field_value );
	header("Content-type: application/json");

	$new_html = '';

	if(isset($conditional['value']) AND is_array($conditional['value'])){
		$new_html = ninja_forms_return_echo('ninja_forms_field_conditional_cr_value_output', $field_id, $x, $y, $conditional, $cr );
		$new_html = utf8_encode( $new_html );
		$array = array('new_html' => $new_html, 'new_type' => $conditional['value']['type'] );
		echo json_encode($array);
	}
	die();
}

function nf_cl_add_criteria() {
	// Bail if we aren't in the admin.
	if ( ! is_admin() )
		return false;

	// Bail if our nonce isn't correct.
	check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );

	echo "HELLO WORLD";
	die();
}

add_action('wp_ajax_nf_cl_add_criteria', 'nf_cl_add_criteria');