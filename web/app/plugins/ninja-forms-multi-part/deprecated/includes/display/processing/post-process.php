<?php

add_action( 'init', 'ninja_forms_mp_register_post_process' );
function ninja_forms_mp_register_post_process(){
	add_action( 'ninja_forms_post_process', 'ninja_forms_mp_post_process', 1001 );
}

function ninja_forms_mp_post_process(){
	global $ninja_forms_processing;
	$form_id = $ninja_forms_processing->get_form_ID();
	if( nf_mp_get_page_count( $form_id ) > 1 ) {
		$ninja_forms_processing->update_extra_value( '_current_page', 1 );
		$ninja_forms_processing->update_form_setting( 'sub_id', '' );
	}
}