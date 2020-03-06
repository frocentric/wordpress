<?php
add_action( 'init', 'ninja_forms_register_conditional_display_js_css' );
function ninja_forms_register_conditional_display_js_css(){
	add_action( 'ninja_forms_display_js', 'ninja_forms_conditionals_display_js', 10, 2 );
	add_action( 'ninja_forms_display_css', 'ninja_forms_conditionals_display_css' );
}


function ninja_forms_conditionals_display_js( $form_id ){
	$conditionals = ninja_forms_display_conditionals( $form_id );
	if( !empty( $conditionals ) ){
		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}

		wp_enqueue_script( 'ninja-forms-conditionals-display',
			NINJA_FORMS_CON_URL . '/js/' . $src .'/ninja-forms-conditionals-display' . $suffix . '.js?nf_ver=' . NINJA_FORMS_CON_VERSION,
			array( 'jquery', 'ninja-forms-display' ) );

		wp_localize_script( 'ninja-forms-conditionals-display', 'ninja_forms_form_'.$form_id.'_conditionals_settings', array( 'conditionals' => $conditionals ) );
	}
}


function ninja_forms_conditionals_display_css( $form_id ){
	$conditionals = ninja_forms_display_conditionals( $form_id );
	if( !empty( $conditionals ) ){
		wp_enqueue_style('ninja-forms-conditionals-display', NINJA_FORMS_CON_URL .'/css/ninja-forms-conditionals-display.css?nf_ver=' . NINJA_FORMS_CON_VERSION );
	}
}