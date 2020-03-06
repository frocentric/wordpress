<?php

add_action( 'init', 'ninja_forms_style_list_metaboxes' );
function ninja_forms_style_list_metaboxes(){
	add_action( 'ninja_forms_style_field_metaboxes', 'ninja_forms_style_modify_list_metaboxes' );
	//if( is_admin() ){
		ninja_forms_style_add_list_metaboxes();
	//}
}

function ninja_forms_style_modify_list_metaboxes( $field_id ){

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	if( isset( $field_data['list_type'] ) ){
		$list_type = $field_data['list_type'];
	}else{
		$list_type = '';
	}
	if( $field_type == '_list' AND $list_type != 'dropdown' ){
		ninja_forms_style_add_list_metaboxes();
	}
}

function ninja_forms_style_add_list_metaboxes(){
	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'list_item_row_field',
		'field_type' => '_list',
		'title' => __( 'List Item Row', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap ul li',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'list_item_row_field', $args );		

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'list_item_label_field',
		'field_type' => '_list',
		'title' => __( 'List Item Label', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap ul li label',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'list_item_label_field', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'list_item_element_field',
		'field_type' => '_list',
		'title' => __( 'List Item Element', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap ul li input',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'list_item_element_field', $args );
}