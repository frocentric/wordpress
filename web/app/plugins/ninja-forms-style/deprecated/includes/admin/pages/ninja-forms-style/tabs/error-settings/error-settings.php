<?php

add_action( 'init', 'ninja_forms_register_tab_style_error_settings' );
function ninja_forms_register_tab_style_error_settings(){
	$args = array(
		'name' => __( 'Error Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_error_settings',
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'error_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_error_settings_metaboxes' );
function ninja_forms_register_style_error_settings_metaboxes(){
	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'error_settings',
		'slug' => 'error_msg',
		'title' => __( 'Error Message Main Wrap Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-error-msg',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'error_settings',
		'slug' => 'field_error_wrap',
		'title' => __( 'Error Field Wrap Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-error',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'error_settings',
		'slug' => 'field_error_label',
		'title' => __( 'Error Label Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-error label',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'error_settings',
		'slug' => 'field_error_element',
		'title' => __( 'Error Element Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-error .ninja-forms-field',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'error_settings',
		'slug' => 'field_error_msg',
		'title' => __( 'Error Message Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-error div.ninja-forms-field-error',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

}

function ninja_forms_save_style_error_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['form_settings']['error_msg'] = $data['error_msg'];
	$plugin_settings['style']['form_settings']['field_error_wrap'] = $data['field_error_wrap'];
	$plugin_settings['style']['form_settings']['field_error_label'] = $data['field_error_label'];
	$plugin_settings['style']['form_settings']['field_error_element'] = $data['field_error_element'];
	$plugin_settings['style']['form_settings']['field_error_element'] = $data['field_error_element'];
	$plugin_settings['style']['form_settings']['field_error_msg'] = $data['field_error_msg'];

	update_option( 'ninja_forms_settings', $plugin_settings);
}