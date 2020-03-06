<?php

//add_action( 'init', 'ninja_forms_conditionals_add_view_sub_header_filter' );
function ninja_forms_conditionals_add_view_sub_header_filter(){
	add_filter( 'ninja_forms_view_subs_table_header', 'ninja_forms_conditionals_view_sub_header_filter', 10, 2 );
}

function ninja_forms_conditionals_view_sub_header_filter( $field_results, $form_id ){
	return $field_results;
}