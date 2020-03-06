<?php

add_action( 'admin_init', 'ninja_forms_post_creation_admin_js' );
function ninja_forms_post_creation_admin_js(){
	if( isset( $_REQUEST['page']) AND $_REQUEST['page'] == 'ninja-forms' ){
		wp_enqueue_script('ninja-forms-post-creation-admin',
			NINJA_FORMS_POST_URL .'/js/dev/ninja-forms-post-creation-admin.js',
			array( 'jquery', 'ninja-forms-admin' ) );
	}
}

add_action( 'admin_init', 'ninja_forms_post_creation_admin_css' );
function ninja_forms_post_creation_admin_css(){
	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
	}else{
		$form_id = '';
	}
	if( $form_id != '' AND $form_id != 'new' AND $form_id != 'all' ){
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];
		if( isset( $form_data['create_post'] ) ){
			$create_post = $form_data['create_post'];
		}else{
			$create_post = 0;
		}
		
		if( $create_post == 1 ){
			if( isset( $_REQUEST['page']) AND $_REQUEST['page'] == 'ninja-forms' ){
				wp_enqueue_style( 'ninja-forms-post-creation-admin-css', NINJA_FORMS_POST_URL .'/css/ninja-forms-post-creation-admin.css', array( 'ninja-forms-admin' ) );
			}
		}
	}
}