<?php

function ninja_forms_register_mp_settings_metabox(){

	$effects = array(
		array( 'name' => 'Blind', 'value' => 'blind' ),
		array( 'name' => 'Fade', 'value' => 'fade' ),
		array( 'name' => 'Fold', 'value' => 'fold' ),
		array( 'name' => 'Slide', 'value' => 'slide' ),
	);

	$effects = apply_filters( 'ninja_forms_mp_ajax_effects_array', $effects );

	$direction = array(
		array( 'name' => 'Left to Right', 'value' => 'ltr' ),
		array( 'name' => 'Right to Left', 'value' => 'rtl' ),
		array( 'name' => 'Top to Bottom', 'value' => 'ttb' ),
		array( 'name' => 'Bottom to Top', 'value' => 'btt' ),
	);

	$direction = apply_filters( 'ninja_forms_mp_ajax_directions_array', $direction );

	if ( isset ( $_REQUEST['form_id'] ) ) {
		$form = ninja_forms_get_form_by_id( $_REQUEST['form_id'] );
		if ( isset ( $form['ajax'] ) ) {
			$ajax = $form['ajax'];
		} else {
			$ajax = 0;
		}
	} else {
		$ajax = 0;
	}

	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'slug' => 'multi_part',
		'title' => __( 'Multi-Part settings', 'ninja-forms-mp' ),
		'display_function' => '',
		'state' => 'closed',
		'settings' => array(
			array(
				'name' => 'multi_part',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __( 'Enable Multi-Part Form?', 'ninja-forms-mp' ),
				'display_function' => '',
				'help' => __( 'If this box is checked Ninja Forms will allow multi-part form pages to be created.', 'ninja-forms-mp' ),
				'default' => 0,
			),
			array(
				'name' => 'mp_progress_bar',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __( 'Display Progress Bar?', 'ninja-forms-mp' ),
				'display_function' => '',
				'help' => '',
				'default' => 0,
			),
			array(
				'name' => 'mp_breadcrumb',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __( 'Display Breadcrumb Navigation?', 'ninja-forms-mp' ),
				'display_function' => '',
				'help' => '',
				'default' => 0,
			),
			array(
				'name' => 'mp_display_titles',
				'type' => 'checkbox',
				'desc' => '',
				'label' => __( 'Display Page Titles?', 'ninja-forms-mp' ),
				'display_function' => '',
				'help' => '',
				'default' => 0,
			),
			array(
				'name' => 'mp_ajax_effect',
				'type' => 'select',
				'options' => $effects,
				'desc' => '',
				'label' => __( 'Page Transition Effect', 'ninja-forms-mp' ),
				'display_function' => '',
				'help' => '',
				'default_value' => 'slide',
			),
			array(
				'name' => 'mp_ajax_direction',
				'type' => 'select',
				'options' => $direction,
				'label' => __( 'Page Transition Direction', 'ninja-forms-mp' ),
			),
			array(
				'name' => 'mp_confirm',
				'type' => 'checkbox',
				'label' => __( 'Show Review Page', 'ninja-forms-mp' ),
				'desc' => __( 'This page will allow your users to review and modify their fields on one page before they submit the form.', 'ninja-forms-mp' ),
			),
			array(
				'name' => 'mp_confirm_msg',
				'type' => 'rte',
				'label' => __( 'Review Page Message', 'ninja-forms-mp' ),
				'desc' => __( 'This message will be shown to users at the top of the review page.', 'ninja-forms-mp' ),
			),
		),
	);
	
	if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
		ninja_forms_register_tab_metabox($args);
	}
}

add_action( 'admin_init', 'ninja_forms_register_mp_settings_metabox', 11 );