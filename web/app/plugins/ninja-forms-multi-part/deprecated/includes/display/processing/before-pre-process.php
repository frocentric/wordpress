<?php

function ninja_forms_mp_check_page_conditional_before_pre_process(){
	global $ninja_forms_processing;
	$form_id = $ninja_forms_processing->get_form_ID();
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	if ( nf_mp_get_page_count( $form_id ) > 1 ) {
		//if( isset( $form_row['data']['ajax'] ) AND $form_row['data']['ajax'] == 1 ){
			if( function_exists( 'ninja_forms_conditionals_field_filter')  ){
				
				
				if( is_array( $pages ) AND !empty( $pages ) ){
					foreach( $pages as $page => $vars ){
						$show = ninja_forms_mp_check_page_conditional( $form_id, $page );
						if( !$show ){
							ninja_forms_mp_conditional_remove_page( $form_id, $page );
						}
					}
				}
			}		
		//}		
	}
}

add_action( 'ninja_forms_pre_process', 'ninja_forms_mp_check_page_conditional_before_pre_process', 11 );


function ninja_forms_mp_remove_save(){
	global $ninja_forms_processing, $ninja_forms_fields;

	if( is_object( $ninja_forms_processing ) ) {
		$form_id = $ninja_forms_processing->get_form_ID();
		if ( nf_mp_get_page_count( $form_id ) > 1 ) {
			$nav = ninja_forms_mp_breadcrumb_update_current_page();

			if( $ninja_forms_processing->get_extra_value( '_prev' ) ){
				$nav = 'prev';
			}

			if( $ninja_forms_processing->get_extra_value( '_next' ) ){
				$nav = 'next';
			}

			if( $nav != '' ){
				remove_action( 'ninja_forms_pre_process', 'ninja_forms_req_fields_process', 13 );
				$ninja_forms_processing->set_action( 'mp_save' );
				remove_action( 'ninja_forms_post_process', 'ninja_forms_save_sub' );
			}			
		}
	}
}

add_action( 'ninja_forms_pre_process', 'ninja_forms_mp_error_change_page', 20 );


function ninja_forms_mp_error_change_page(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();
	if( $ninja_forms_processing->get_action() == 'submit' && nf_mp_get_page_count( $form_id ) > 1 ) {
		
		if( $ninja_forms_processing->get_all_errors() ){
			$all_fields = $ninja_forms_processing->get_all_fields();
			$error_page = '';
			foreach( $all_fields as $field_id => $user_value ){
				if( $ninja_forms_processing->get_errors_by_location( $field_id ) ){
					$error_page = $ninja_forms_processing->get_field_setting( $field_id, 'page' );
					break;
				}
			}

		$ninja_forms_processing->update_extra_value( '_current_page', $error_page );

		}
	}
}

/*
 * Function used to update the _current_page extra value. It looks at whether or not the prev or next buttons have been pressed.
 * If they have, it updates the current page based upon which was pressed. 
 *
 * @since 1.0.3
 * @returns void
 */

function ninja_forms_mp_nav_update_current_page(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();

	if( $ninja_forms_processing->get_extra_value( '_current_page' ) ){
		$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );
	}else{
		$current_page = 1;
	}	
	if( $ninja_forms_processing->get_extra_value( '_page_count' ) ){
		$page_count = $ninja_forms_processing->get_extra_value( '_page_count' );
	}else{
		$page_count = 1;
	}

	$nav = '';
	if( $ninja_forms_processing->get_extra_value( '_prev' ) ){
		$nav = 'prev';
		if( $current_page != 1 ){
			$current_page--;
		}
	}

	if( $ninja_forms_processing->get_extra_value( '_next' ) ){
		$nav = 'next';
		if( $current_page != $page_count ){
			$current_page++;
		}
	}

	if( $nav != '' ){
		if ( function_exists( 'ninja_forms_conditionals_field_filter' ) ) {
			ninja_forms_conditionals_field_filter( $form_id );
		}
		$show = ninja_forms_mp_check_page_conditional( $form_id, $current_page );

		$ninja_forms_processing->update_extra_value( '_current_page', $current_page );

		if( ( $current_page <= $page_count ) AND !$show ){
			ninja_forms_mp_nav_update_current_page();
		}
	}		
}

/*
 *
 * Function that updates the _current_page extra value if a breadcrumb has been clicked.
 *
 * @since 1.0.3
 * @returns void
 */

function ninja_forms_mp_breadcrumb_update_current_page(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();
	$nav = '';
	$all_extras = $ninja_forms_processing->get_all_extras();
	if( is_array( $all_extras ) AND !empty( $all_extras ) ){
		foreach( $all_extras as $key => $val ){
			if( strpos( $key, '_mp_page_' ) !== false ){
				$nav = str_replace( '_mp_page_', '', $key );
				$ninja_forms_processing->update_extra_value( '_current_page', $nav );
				$show = ninja_forms_mp_check_page_conditional( $form_id, $nav );
				if( !$show ){
					$ninja_forms_processing->update_extra_value( '_prev', 'Previous' );
					ninja_forms_mp_nav_update_current_page();
				}

			}
		}
	}
	return $nav;
}

/*
 *
 * Function that checks whether or not this page should be shown based upon form conditionals.
 * 
 * @since 1.0.3
 * @returns bool true/false
 */

function ninja_forms_mp_check_page_conditional( $form_id = '', $current_page = '' ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if( $current_page == '' ){
		$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );		
	}

	// Check to see if the next page has been rendered "hidden" by conditional logic.
	$page_divider_id = ninja_forms_mp_get_divider_by_page( $form_id, $current_page );

	if ( isset ( $ninja_forms_loading ) ) {
		$field = $ninja_forms_loading->get_field_settings( $page_divider_id );
	} else {
		$field = $ninja_forms_processing->get_field_settings( $page_divider_id );		
	}

	$data = $field['data'];
	$show = true;
	if( function_exists( 'ninja_forms_conditionals_field_filter' ) ){
		if( isset( $data['conditional_action'] ) ){
			switch( $data['conditional_action'] ){
				case 'ninja_forms_show_mp_page':
					if( !isset( $data['conditional_pass'] ) OR !$data['conditional_pass'] ){
						$show = false;
					}
					break;
				case 'ninja_forms_hide_mp_page':
					if( isset( $data['conditional_pass'] ) AND $data['conditional_pass'] ){
						$show = false;
					}
					break;
			}
		}
	}
	return $show;
}

/*
 *
 * Function to remove the page and its fields from the $ninja_forms_processing global
 *
 * @since 1.0.3
 * @returns void
 */

function ninja_forms_mp_conditional_remove_page( $form_id, $page ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
		$page_divider_id = $pages[$page]['id'];
		if ( is_array( $pages[$page] ) ){
			foreach ( $pages[$page]['fields'] as $field_id ) {
				$ninja_forms_loading->update_field_value( $field_id, false );
			}
		}
		$ninja_forms_loading->update_field_value( $page_divider_id, false );
	} else {
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
		$page_divider_id = $pages[$page]['id'];
		if ( is_array( $pages[$page] ) ){
			foreach ( $pages[$page]['fields'] as $field_id ) {
				$ninja_forms_processing->update_field_value( $field_id, false );
			}
		}
		$ninja_forms_processing->update_field_value( $page_divider_id, false );
	}
}