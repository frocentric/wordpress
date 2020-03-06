<?php

add_action( 'init', 'ninja_forms_style_default_metaboxes' );
function ninja_forms_style_default_metaboxes(){
	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'wrap',
		'title' => __( 'Wrap Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap',
		'css_exclude' => array( 'float', 'padding', 'margin' ),
	);
	ninja_forms_register_style_metabox( 'wrap', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'label',
		'title' => __( 'Label Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_label',
		'css_exclude' => array( 'float', 'padding', 'margin' ),
	);
	ninja_forms_register_style_metabox( 'label', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'field',
		'title' => __( 'Element Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]',
		'css_exclude' => array( 'float', 'padding', 'margin' ),
	);
	ninja_forms_register_style_metabox( 'field', $args );

}

add_action( 'init', 'ninja_forms_style_submit_metaboxes' );
function ninja_forms_style_submit_metaboxes(){
	add_action( 'ninja_forms_style_field_metaboxes', 'ninja_forms_style_modify_submit_metaboxes', 10, 1 );
}

function ninja_forms_style_submit_front_end( $field_id ){
	ninja_forms_style_modify_submit_metaboxes( $field_id );
}

add_action( 'ninja_forms_display_after_field', 'ninja_forms_style_submit_front_end' );


function ninja_forms_style_modify_submit_metaboxes( $field_id ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	if( $field_type == '_submit' ){
		ninja_forms_style_add_submit_metaboxes();
	}
}

function ninja_forms_style_add_submit_metaboxes(){

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'submit_element_hover',
		'field_type' => '_submit',
		'title' => __( 'Element Hover', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap .ninja-forms-field:hover',
	);

	ninja_forms_register_style_metabox( 'submit_element_hover', $args );
}