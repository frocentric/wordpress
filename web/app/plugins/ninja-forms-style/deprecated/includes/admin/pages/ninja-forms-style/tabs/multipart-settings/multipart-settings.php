<?php

add_action( 'init', 'ninja_forms_register_tab_style_mp_settings' );
function ninja_forms_register_tab_style_mp_settings(){
	$args = array(
		'name' => __( 'Multi-Part Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms-style',
		'display_function' => 'ninja_forms_style_advanced_checkbox_display',
		'save_function' => 'ninja_forms_save_style_mp_settings',
	);
	if( function_exists( 'ninja_forms_register_tab' ) && function_exists( 'ninja_forms_mp_load_translations' ) ){
		ninja_forms_register_tab( 'mp_settings', $args );
	}
}

add_action( 'init', 'ninja_forms_register_style_mp_settings_metaboxes' );
function ninja_forms_register_style_mp_settings_metaboxes(){

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_page',
		'title' => __( 'Form Page', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div .ninja-forms-mp-page',
		//'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_breadcrumb_container',
		'title' => __( 'Breadcrumb Container', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => '.ninja-forms-form ul.ninja-forms-mp-breadcrumbs',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_breadcrumb_buttons',
		'title' => __( 'Breadcrumb Buttons', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => '.ninja-forms-form ul.ninja-forms-mp-breadcrumbs li input[type=submit]',
		//'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_breadcrumb_hover',
		'title' => __( 'Breadcrumb Button Hover', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => '.ninja-forms-form ul.ninja-forms-mp-breadcrumbs li input[type=submit]:hover',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_breadcrumb_active',
		'title' => __( 'Breadcrumb Active Button', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => '.ninja-forms-form ul.ninja-forms-mp-breadcrumbs li input[type=submit].ninja-forms-mp-breadcrumb-active',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_progress_container',
		'title' => __( 'Progress Bar Container', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.meter',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_progressbar_fill',
		'title' => __( 'Progress Bar Fill', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.meter > span',
		'css_exclude' => array( 'float', 'margin' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_page_title',
		'title' => __( 'Page Titles', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'h3.ninja-forms-mp-page-title',
		//'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_prevnext_container',
		'title' => __( 'Prev/Next Container', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'div.ninja-forms-mp-nav-wrap',
		'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_prev',
		'title' => __( 'Previous Button', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'form.ninja-forms-form div.ninja-forms-mp-nav-wrap .ninja-forms-mp-prev',
		//'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_next',
		'title' => __( 'Next Button', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'form.ninja-forms-form div.ninja-forms-mp-nav-wrap .ninja-forms-mp-next',
		//'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}

	$args = array(
		'page' => 'ninja-forms-style',
		'tab' => 'mp_settings',
		'slug' => 'mp_button_hover',
		'title' => __( 'Prev / Next Button Hover', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_metabox_output',
		'save_page' => 'form_settings',
		'css_selector' => 'form.ninja-forms-form div.ninja-forms-mp-nav-wrap .ninja-forms-mp-nav:hover',
		//'css_exclude' => array( 'float' ),
	);

	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

function ninja_forms_save_style_mp_settings( $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['form_settings']['mp_breadcrumb_container'] = $data['mp_breadcrumb_container'];
	$plugin_settings['style']['form_settings']['mp_breadcrumb_buttons'] = $data['mp_breadcrumb_buttons'];
	$plugin_settings['style']['form_settings']['mp_breadcrumb_active'] = $data['mp_breadcrumb_active'];
	$plugin_settings['style']['form_settings']['mp_breadcrumb_hover'] = $data['mp_breadcrumb_hover'];
	$plugin_settings['style']['form_settings']['mp_progress_container'] = $data['mp_progress_container'];
	$plugin_settings['style']['form_settings']['mp_progressbar_fill'] = $data['mp_progressbar_fill'];
	$plugin_settings['style']['form_settings']['mp_page_title'] = $data['mp_page_title'];
	$plugin_settings['style']['form_settings']['mp_prevnext_container'] = $data['mp_prevnext_container'];
	$plugin_settings['style']['form_settings']['mp_prev'] = $data['mp_prev'];
	$plugin_settings['style']['form_settings']['mp_next'] = $data['mp_next'];
	$plugin_settings['style']['form_settings']['mp_button_hover'] = $data['mp_button_hover'];
	$plugin_settings['style']['form_settings']['mp_page'] = $data['mp_page'];

	update_option( 'ninja_forms_settings', $plugin_settings);
}