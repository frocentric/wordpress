<?php
/**
 * Outputs the HTML for the Multi-Part Form Page Title.
 *
**/

function ninja_forms_mp_check_page_title( $form_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_data = $ninja_forms_loading->get_all_form_settings();
	} else {
		$form_data = $ninja_forms_processing->get_all_form_settings();
	}

	if( isset( $form_data['mp_display_titles'] ) AND $form_data['mp_display_titles'] == 1 ){

		$js_transition = 1;

		if( $js_transition == 1 ){
			add_action( 'ninja_forms_display_mp_page_before_fields', 'ninja_forms_mp_display_page_title', 10, 2 );
			remove_action( 'ninja_forms_display_before_fields', 'ninja_forms_display_req_items', 12 );
			add_action( 'ninja_forms_display_mp_page_before_fields', 'ninja_forms_mp_display_page_req_items', 10, 2 );
		}
	}
}

function ninja_forms_mp_display_page_title( $form_id, $page = '' ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if( ( isset( $ninja_forms_processing ) AND $ninja_forms_processing->get_form_setting( 'processing_complete' ) != 1 ) OR !isset ( $ninja_forms_processing ) ){
		if( $page != '' ){
			$current_page = $page;
		}else{
			if( is_object( $ninja_forms_processing ) ){
				$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );
			}else{
				$current_page = 1;
			}
		}

		if ( isset ( $ninja_forms_loading ) ) {
			$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
		} else {
			$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
		}

		if( isset( $pages[$current_page]['page_title'] ) ){
			$page_title = $pages[$current_page]['page_title'];
		}else{
			$page_title = '';
		}
		
		$title = '<h3 class="ninja-forms-mp-page-title">'.$page_title.'</h3>';
		$title = apply_filters( 'ninja_forms_display_mp_page_title', $title, $form_id, $current_page );
		echo $title;
	}
}

add_action( 'ninja_forms_display_before_fields', 'ninja_forms_mp_check_page_title', 9 );

function ninja_forms_mp_display_page_req_items( $form_id, $page ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$all_fields = $ninja_forms_loading->get_all_fields();
	} else {
		$all_fields = $ninja_forms_processing->get_all_fields();
	}

	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		$pages = array();
		$x = 0;
		foreach( $all_fields as $field_id => $user_value ){
			if ( isset ( $ninja_forms_loading ) ) {
				$field = $ninja_forms_loading->get_field_settings( $field_id );
			} else {
				$field = $ninja_forms_processing->get_field_settings( $field_id );
			}
			if( $field['type'] == '_page_divider' ){
				$x++;
			}else{
				$pages[$x][] = $field;
			}
		}
	}

	$found = false;
	foreach( $pages[$page] as $fields ){
		if( isset( $fields['data']['req'] ) AND $fields['data']['req'] == 1 ){
			$found = true;
			break;
		}
	}

	if( $found ){
		ninja_forms_display_req_items( $form_id );
	}
}