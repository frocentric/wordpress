<?php
/*
 *
 * Function used to copy a page from the Field Settings tab. It is called via ajax.
 *
 * @since 1.0.3
 * @returns void
 */

function ninja_forms_mp_copy_page(){
	$form_id = $_REQUEST['form_id'];
	$fields = $_REQUEST['field_data'];
	$new_ids = array();

	$order = 999;
	foreach( $fields[0] as $f => $data ){
		$data = serialize( $data );
		$args = array( 'type' => '_page_divider', 'data' => $data, 'order' => $order, 'fav_id' => 0, 'def_id' => 0 );
		$new_divider = ninja_forms_insert_field( $form_id, $args );
		$new_html = ninja_forms_return_echo( 'ninja_forms_edit_field', $new_divider );
		$new_ids[] = $new_divider;
	}

	unset( $fields[0] );

	$new_fields = array();

	foreach( $fields as $field ){
		foreach( $field as $f => $data ){
			$field_id = str_replace( 'ninja_forms_field_', '', $f );
			$field_row = ninja_forms_get_field_by_id( $field_id );
			$field_type = $field_row['type'];
			if( isset( $field_row['fav_id'] ) ){
				$fav_id = $field_row['fav_id'];
			}else{
				$fav_id = 0;
			}
			if( isset( $field_row['def_id'] ) ){
				$def_id = $field_row['def_id'];
			}else{
				$def_id = 0;
			}
			$data = serialize( $data );
			$args = array( 'type' => $field_type, 'data' => $data, 'order' => $order, 'fav_id' => $fav_id, 'def_id' => $def_id );
			$new_id = ninja_forms_insert_field( $form_id, $args );
			$new_fields[ $field_id ] = $new_id;
		}
	}
	
	do_action( 'nf_mp_copy_page', $new_fields );	

	foreach( $new_fields as $field_id ){
		$new_html .= ninja_forms_return_echo( 'ninja_forms_edit_field', $field_id );
	}

	header("Content-type: application/json");
	$array = array( 'new_html' => $new_html, 'new_ids' => $new_ids );
	echo json_encode( $array );
	die();
}


add_action( 'wp_ajax_ninja_forms_mp_copy_page', 'ninja_forms_mp_copy_page' );

function ninja_forms_mp_get_pages( $form_id = '' ){
	global $ninja_forms_loading, $ninja_forms_processing;
	
	$field_results = ninja_forms_get_fields_by_form_id( $form_id );

	$pages = array();
	$x = 0;
	$y = 0;
	$last_field = '';
	foreach( $field_results as $field ){

		if( $field['type'] == '_page_divider' ){
			$x++;
			$y = 0;
			$pages[$x]['id'] = $field['id'];
			if ( isset ( $field['data']['page_name'] ) ) {
				$page_name = $field['data']['page_name'];
			} else {
				$page_name = '';
			}
			$pages[$x]['page_title'] = $page_name;

		} else {
			if ( $y == 0 ) {
				if ( ! empty( $field['type'] ) ) {
					$pages[$x]['first_field'] = $field['id'];
					$y++;
				}
			}
		}

		if ( ! empty( $field['type'] ) ) {
			$pages[$x]['fields'][] = $field['id'];
		}

		if ( isset ( $ninja_forms_loading ) ) {
			$ninja_forms_loading->update_field_setting( $field['id'], 'page', $x );
		} else {
			$ninja_forms_processing->update_field_setting( $field['id'], 'page', $x );
		}
	}

	foreach ( $pages as $num => $vars ) {
		$last_field = end( $vars['fields'] );
		$pages[$num]['last_field'] = $last_field;
	}

	return $pages;
}

/*
 *
 * Function used to delete a page from the Field Settings tab. It is called via ajax.
 *
 * @since 1.0
 * @returns void
 */

function ninja_forms_mp_delete_page(){
	global $wpdb, $ninja_forms_fields;
	$fields = $_REQUEST['fields'];

	if( is_array( $fields ) AND !empty( $fields ) ){
		foreach( $fields as $field ){
			$field_id = str_replace( 'ninja_forms_field_', '', $field );
			ninja_forms_delete_field( $field_id );
		}
	}
	die();
}

add_action('wp_ajax_ninja_forms_mp_delete_page', 'ninja_forms_mp_delete_page');



function ninja_forms_mp_get_divider_by_page( $form_id, $current_page ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	}

	$divider_id = $pages[$current_page]['id'];

	return $divider_id;
}

function ninja_forms_mp_get_page_by_divider( $form_id, $field_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
	} else {
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
	}

	$x = 1;
	foreach ( $pages as $num => $vars ) {
		if ( $vars['id'] == $field_id ) {
			$page_num = $x;
			break;
		}
		$x++;
	}

	return $page_num;
}

function ninja_forms_mp_get_page_by_field_id( $field_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$page = $ninja_forms_loading->get_field_setting( $field_id, 'page' );
	} else {
		$page = $ninja_forms_processing->get_field_setting( $field_id, 'page' );
	}

	return $page;
}

/*
 *
 * Function that loops through our pages and adds an array with the pages information to our loading/processing classes.
 *
 * @since 1.2.6
 * @return void
 */

function ninja_forms_mp_set_page_array( $form_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
	}

	$pages = ninja_forms_mp_get_pages( $form_id );

	if ( isset ( $ninja_forms_loading ) ) {
		$ninja_forms_loading->update_form_setting( 'mp_pages', $pages );
	} else {
		$ninja_forms_processing->update_form_setting( 'mp_pages', $pages );
	}
}

add_action( 'ninja_forms_display_init', 'ninja_forms_mp_set_page_array' );
add_action( 'ninja_forms_before_pre_process', 'ninja_forms_mp_set_page_array' );
add_action( 'ninja_forms_edit_sub_pre_process', 'ninja_forms_mp_set_page_array', 3 );

function ninja_forms_mp_new_form_add_page( $form_id, $data ){
  if( $data['multi_part'] == 1 ){
    $args = array(
      'type' => '_page_divider',
      'order' => -1,
    );
    ninja_forms_insert_field( $form_id, $args );
  }
}

add_action( 'ninja_forms_save_new_form_settings', 'ninja_forms_mp_new_form_add_page', 10, 2 );
add_action( 'ninja_forms_save_form_settings', 'ninja_forms_mp_new_form_add_page', 10, 2 );