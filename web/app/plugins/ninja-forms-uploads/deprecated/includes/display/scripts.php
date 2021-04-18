<?php

add_action( 'init', 'ninja_forms_register_uploads_display_js_css' );
function ninja_forms_register_uploads_display_js_css(){
	add_action( 'ninja_forms_display_js', 'ninja_forms_upload_display_js', 10, 2 );
}

function ninja_forms_upload_display_js( $form_id ){
	if( !is_admin() ){
		$fields = ninja_forms_get_fields_by_form_id( $form_id );
		$output = false;
		$multi = false;
		foreach( $fields as $field ){
			if( $field['type'] == '_upload' ){
				if( !$output ){
					$output = true;
				}			
				if( !$multi && isset($field['data']['upload_multi']) && $field['data']['upload_multi'] == 1 ){
					$multi = true;
				}
			}
		}

		if( $output ){
			if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
				$suffix = '';
				$src = 'dev';
			} else {
				$suffix = '.min';
				$src = 'min';
			}

			wp_enqueue_script( 'ninja-forms-uploads-display',
				NINJA_FORMS_UPLOADS_URL .'/js/' . $src . '/ninja-forms-uploads-display' .$suffix .'.js',
				array( 'jquery', 'ninja-forms-display' ) );
			if( $multi ){
				wp_enqueue_script( 'jquery-multi-file',
					NINJA_FORMS_UPLOADS_URL .'/js/min/jquery.MultiFile.pack.js',
					array( 'jquery' ) );
				wp_localize_script( 'ninja-forms-uploads-display', 'ninja_forms_uploads_settings', array( 'delete' => __( 'Really delete this item?', 'ninja-forms-uploads' ) ) );
			}
		}
	}
}