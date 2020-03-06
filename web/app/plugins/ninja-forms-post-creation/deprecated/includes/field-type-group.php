<?php
add_action( 'init', 'ninja_forms_register_post_field_type_group', 8 );
function ninja_forms_register_post_field_type_group(){
	$args = array(
		'name' => __( 'Post Creation', 'ninja-forms-pc' ),
	);
	if( function_exists( 'ninja_forms_register_field_type_group' ) ){
		ninja_forms_register_field_type_group( 'create_post', $args );
	}
}