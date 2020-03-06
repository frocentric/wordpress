<?php

add_action( 'init', 'ninja_forms_register_tab_style_datepicker_settings' );
function ninja_forms_register_tab_style_datepicker_settings(){
	$args = array(
		'name' => __( 'DatePicker Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_datepicker_settings',
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'datepicker_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_datepicker_settings_metaboxes' );
function ninja_forms_register_style_datepicker_settings_metaboxes(){
	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_container',
		'title' => __( 'DatePicker Container', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div',
		'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_header',
		'title' => __( 'DatePicker Header', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div .ui-datepicker-header',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_week',
		'title' => __( 'DatePicker Week Days', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div .ui-datepicker-calendar th',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_days',
		'title' => __( 'DatePicker Days', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div .ui-datepicker-calendar td',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_prev',
		'title' => __( 'DatePicker Prev Link', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div .ui-datepicker-prev',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'datepicker_settings',
		'slug' => 'datepicker_next',
		'title' => __( 'DatePicker Next Link', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div#ui-datepicker-div .ui-datepicker-next',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

}

function ninja_forms_save_style_datepicker_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['form_settings']['datepicker_container'] = $data['datepicker_container'];
	$plugin_settings['style']['form_settings']['datepicker_header'] = $data['datepicker_header'];
	$plugin_settings['style']['form_settings']['datepicker_week'] = $data['datepicker_week'];
	$plugin_settings['style']['form_settings']['datepicker_days'] = $data['datepicker_days'];
	$plugin_settings['style']['form_settings']['datepicker_prev'] = $data['datepicker_prev'];
	$plugin_settings['style']['form_settings']['datepicker_next'] = $data['datepicker_next'];

	update_option( 'ninja_forms_settings', $plugin_settings);
}