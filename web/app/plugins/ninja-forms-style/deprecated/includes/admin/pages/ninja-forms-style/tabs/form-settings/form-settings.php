<?php

add_action( 'init', 'ninja_forms_register_tab_style_form_settings' );
function ninja_forms_register_tab_style_form_settings(){
	$args = array(
		'name' => __( 'Form Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_form_settings',
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'form_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_form_settings_metaboxes' );
function ninja_forms_register_style_form_settings_metaboxes(){
	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'container',
		'title' => __( 'Container Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-form-wrap',
		'css_exclude' => '',
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'title',
		'title' => __( 'Title Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'h2.ninja-forms-form-title',
		'css_exclude' => '',
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'required-message',
		'title' => __( 'Required Message Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => '.ninja-forms-required-items',
		'css_exclude' => '',
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'row',
		'title' => __( 'Row Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-row',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'row-odd',
		'title' => __( 'Odd Row Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-row:nth-child(odd)',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'form_settings',
		'slug' => 'success-msg',
		'title' => __( 'Success Response Message Styles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-success-msg',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

function ninja_forms_save_style_form_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['advanced'] = $data['advanced'];
	$plugin_settings['style']['form_settings']['container'] = $data['container'];
	$plugin_settings['style']['form_settings']['title'] = $data['title'];
	$plugin_settings['style']['form_settings']['required-message'] = $data['required-message'];
	$plugin_settings['style']['form_settings']['row'] = $data['row'];
	$plugin_settings['style']['form_settings']['row-odd'] = $data['row-odd'];
	$plugin_settings['style']['form_settings']['success-msg'] = $data['success-msg'];

	update_option( 'ninja_forms_settings', $plugin_settings);
}