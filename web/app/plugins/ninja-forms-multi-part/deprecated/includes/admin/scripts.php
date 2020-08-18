<?php

function ninja_forms_mp_admin_js(){
	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
	}else{
		$form_id = '';
	}
	if( $form_id != '' AND $form_id != 'new' AND $form_id != 'all' ){
		if( isset( $_REQUEST['page'] ) AND $_REQUEST['page'] == 'ninja-forms' ){
			if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
				$suffix = '';
				$src = 'dev';
			} else {
				$suffix = '.min';
				$src = 'min';
			}
			wp_enqueue_script( 'nf-mp-admin',
				NINJA_FORMS_MP_URL .'/js/' . $src .'/ninja-forms-mp-admin' . $suffix . '.js?nf_ver=' . NINJA_FORMS_MP_VERSION,
				array( 'jquery', 'ninja-forms-admin', 'nf-builder', 'jquery-ui-droppable' ) );
			$pages = nf_mp_get_pages( $form_id );
			wp_localize_script( 'nf-mp-admin', 'nf_mp', array( 'pages' => $pages, 'remove_page_text' => __( 'Really remove this page? All fields will be deleted.' ) ) );
		}
	}
}
add_action( 'admin_init', 'ninja_forms_mp_admin_js' );

function ninja_forms_mp_admin_css(){
	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
	}else{
		$form_id = '';
	}
	if( $form_id != '' AND $form_id != 'new' AND $form_id != 'all' ){
		if( isset( $_REQUEST['page'] ) AND $_REQUEST['page'] == 'ninja-forms' ){
			wp_enqueue_style( 'ninja-forms-mp-admin', NINJA_FORMS_MP_URL .'/css/ninja-forms-mp-admin.css?nf_ver=' . NINJA_FORMS_MP_VERSION, array( 'ninja-forms-admin' ) );
		}
	}
}
add_action( 'admin_init', 'ninja_forms_mp_admin_css' );