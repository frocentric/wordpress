<?php

add_action( 'init', 'ninja_forms_mp_register_post_process' );
function ninja_forms_mp_register_post_process(){
	add_action( 'ninja_forms_post_process', 'ninja_forms_mp_post_process', 1001 );
}

function ninja_forms_mp_post_process(){
	global $ninja_forms_processing;
	if( $ninja_forms_processing->get_form_setting( 'multi_part' ) ){
		$ninja_forms_processing->update_extra_value( '_current_page', 1 );
		$ninja_forms_processing->update_form_setting( 'sub_id', '' );
	}
}