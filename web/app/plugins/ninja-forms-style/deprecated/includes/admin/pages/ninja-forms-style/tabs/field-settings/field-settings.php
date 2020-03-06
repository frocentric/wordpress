<?php

add_action( 'init', 'ninja_forms_register_tab_style_field_settings' );
function ninja_forms_register_tab_style_field_settings(){
	$args = array(
		'name' => __( 'Default Field Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_field_settings',
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'field_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_field_settings_metaboxes' );

function ninja_forms_register_style_field_settings_metaboxes(){
	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'field_settings',
		'slug' => 'wrap',
		'title' => __( 'Wrap Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'field_settings',
		'css_selector' => 'div.field-wrap',
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'field_settings',
		'slug' => 'label',
		'title' => __( 'Label Styles', 'ninja-forms-style'),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'field_settings',
		'css_selector' => 'div.field-wrap label',
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'field_settings',
		'slug' => 'field',
		'title' => __( 'Element Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'field_settings',
		'css_selector' => '.ninja-forms-field',
		'css_exclude' => array( 'width', 'height', 'etc' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

}

function ninja_forms_save_style_field_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['advanced'] = $data['advanced'];

	$plugin_settings['style']['field_settings']['wrap'] = $data['wrap'];
	$plugin_settings['style']['field_settings']['label'] = $data['label'];
	$plugin_settings['style']['field_settings']['field'] = $data['field'];
	
	update_option( 'ninja_forms_settings', $plugin_settings);
}