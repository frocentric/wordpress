<?php

add_action( 'init', 'ninja_forms_register_post_creation_display_js_css' );
function ninja_forms_register_post_creation_display_js_css(){
	add_action( 'ninja_forms_display_js', 'ninja_forms_post_creation_display_js', 10, 2 );
	add_action( 'ninja_forms_display_css', 'ninja_forms_post_creation_display_css', 10, 2 );
}

function ninja_forms_post_creation_display_js( $form_id ){
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];
	if( isset( $form_data['create_post'] ) AND $form_data['create_post'] == 1 ){
		wp_enqueue_script( 'ninja-forms-pc-display',
			NINJA_FORMS_POST_URL .'/js/min/ninja-forms-post-creation-display.min.js',
			array( 'jquery', 'ninja-forms-display' ) );
	}
	
}

function ninja_forms_post_creation_display_css( $form_id ){
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];
	if( isset( $form_data['create_post'] ) AND $form_data['create_post'] == 1 ){
		wp_enqueue_style('ninja-forms-pc', NINJA_FORMS_POST_URL .'/css/ninja-forms-post-creation-display.css');
	}
}