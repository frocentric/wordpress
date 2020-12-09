<?php

add_action( 'init', 'ninja_forms_register_mp_display_js_css' );
function ninja_forms_register_mp_display_js_css(){
	add_action( 'ninja_forms_display_js', 'ninja_forms_mp_display_js', 10, 2 );
	add_action( 'ninja_forms_display_css', 'ninja_forms_mp_display_css', 10, 2 );
}

function ninja_forms_mp_display_js( $form_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );		
	}

	$page_count = count( $pages );

	$js_transition = 1;

	if( isset( $form_data['mp_ajax_effect'] ) ){
		$effect = $form_data['mp_ajax_effect'];
	}else{
		$effect = 'slide';
	}
	if( isset( $form_data['mp_ajax_direction'] ) ){
		$direction = $form_data['mp_ajax_direction'];
	}else{
		$direction = 'ltr';
	}

	if( $page_count > 1 ){

		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}

		wp_enqueue_script( 'ninja-forms-mp-display',
			NINJA_FORMS_MP_URL .'/js/' . $src . '/ninja-forms-mp-display' . $suffix . '.js?nf_ver=' . NINJA_FORMS_MP_VERSION,
			array( 'jquery', 'ninja-forms-display' ) );

		if( $js_transition == 1 ){
			wp_enqueue_script( 'jquery-effects-'.$effect );
		}
		
		wp_localize_script( 'ninja-forms-mp-display', 'ninja_forms_form_'.$form_id.'_mp_settings', array( 'page_count' => $page_count, 'js_transition' => $js_transition, 'effect' => $effect, 'direction' => $direction ) );
	}
}

function ninja_forms_mp_display_css( $form_id ){
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];
	if( nf_mp_get_page_count( $form_id ) > 1 ){
		wp_enqueue_style('ninja-forms-mp-display', NINJA_FORMS_MP_URL .'/css/ninja-forms-mp-display.css?nf_ver=' . NINJA_FORMS_MP_VERSION );
	}
}