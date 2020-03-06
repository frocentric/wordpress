<?php

add_action( 'admin_init', 'ninja_forms_style_admin_js' );
function ninja_forms_style_admin_js(){

	if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
		$suffix = '';
		$src = 'dev';
	} else {
		$suffix = '.min';
		$src = 'min';
	}

	if( isset( $_REQUEST['page'] ) AND ( $_REQUEST['page'] == 'ninja-forms-style' OR ( $_REQUEST['page'] == 'ninja-forms' AND ( isset( $_REQUEST['tab'] ) AND $_REQUEST['tab'] == 'form_layout' ) ) ) ){
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'ninja-forms-style-admin',
			NINJA_FORMS_STYLE_URL .'/js/' . $src .'/ninja-forms-style-admin' . $suffix . '.js?nf_ver=' . NINJA_FORMS_STYLE_VERSION,
			array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-sortable' ) );
		wp_localize_script( 'ninja-forms-style-admin', 'nf_style', array( 'layout_error' => __( 'There is an error with your layout. Please ensure that all columns are fully spanned. See the error(s) below.', 'ninja-forms-style' ) ) );
	}
}

add_action( 'admin_init', 'ninja_forms_style_admin_css' );
function ninja_forms_style_admin_css(){

	if( isset( $_REQUEST['page'] ) AND ( $_REQUEST['page'] == 'ninja-forms' OR $_REQUEST['page'] == 'ninja-forms-style' ) ){
		wp_enqueue_style( 'wp-color-picker' );		
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'ninja-forms-style-admin',
			NINJA_FORMS_STYLE_URL.'/css/ninja-forms-style-admin.css?nf_ver=' . NINJA_FORMS_STYLE_VERSION );
	}

}
