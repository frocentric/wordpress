<?php
/**
 * Function used to create a new part on the Field Settings tab. It is called via ajax.
 *
 * @since 1.3
 * @returns void
 */

function nf_mp_new_page() {
	$form_id = $_REQUEST['form_id'];

	$data = serialize( array() );
	$args = array( 'type' => '_page_divider', 'data' => $data, 'order' => 999, 'fav_id' => 0, 'def_id' => 0 );

	$new_id = ninja_forms_insert_field( $form_id, $args );

	do_action( 'nf_mp_after_new_page', $new_id );	

	// Save our current order
	$order = json_decode( strip_tags( stripslashes( $_REQUEST['order'] ) ), true );

	if ( is_array ( $order ) ) {
		$order_array = array();
		$x = 0;
		foreach ( $order as $id ) {
			$id = str_replace( 'ninja_forms_field_', '', $id );
			$args = array(
				'update_array' => array(
					'order' => $x,
				),
				'where' => array(
					'id' => $id,
				),
			);
			ninja_forms_update_field( $args );
			$x++;
		}
	}

	// Update our form object since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();

	$pages = nf_mp_get_pages( $form_id );
	$current_page = nf_mp_get_page_count( $form_id );

	$fields = isset ( $pages[ $current_page ]['fields'] ) ? $pages[ $current_page ]['fields'] : array();
	$page_title = isset ( $pages[ $current_page ]['page_title'] ) ? $pages[ $current_page ]['page_title'] : '';
	$new_part = array( 'id' => $new_id, 'fields' => $fields, 'num' => $current_page, 'page_title' => $page_title );

	$new_nav = ninja_forms_return_echo( 'nf_mp_admin_page_nav', $form_id, $current_page );
	$new_slide = ninja_forms_return_echo( 'nf_mp_edit_field_output_all_uls', $form_id );

	header("Content-type: application/json");
	$array = array( 'new_nav' => $new_nav, 'new_slide' => $new_slide, 'new_part' => $new_part );
	echo json_encode( $array );

	die();
}

add_action( 'wp_ajax_nf_mp_new_page', 'nf_mp_new_page' );

/**
 *
 * Function used to delete a page from the Field Settings tab. It is called via ajax.
 *
 * @since 1.3
 * @returns void
 */

function nf_mp_delete_page(){
	$fields = $_REQUEST['fields'];
	$form_id = $_REQUEST['form_id'];
	$move_to_page = $_REQUEST['move_to_page'];

	if( is_array( $fields ) AND !empty( $fields ) ){
		foreach( $fields as $field ){
			$field_id = str_replace( 'ninja_forms_field_', '', $field );
			ninja_forms_delete_field( $field_id );
		}
	}

	// Update our form object since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();

	$page_count = nf_mp_get_page_count( $form_id );

	if ( $page_count == 1 ) {
		nf_mp_delete_dividers( $form_id );
	}

	do_action( 'nf_mp_after_delete_page' );	

	// Save our current order
	$order = json_decode( strip_tags( stripslashes( $_REQUEST['order'] ) ), true );

	if ( is_array ( $order ) ) {
		$order_array = array();
		$x = 0;
		foreach ( $order as $id ) {
			$id = str_replace( 'ninja_forms_field_', '', $id );
			$args = array(
				'update_array' => array(
					'order' => $x,
				),
				'where' => array(
					'id' => $id,
				),
			);
			ninja_forms_update_field( $args );
			$x++;
		}
	}

	// Update our form object since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();
	
	$new_nav = ninja_forms_return_echo( 'nf_mp_admin_page_nav', $form_id, $move_to_page );
	$new_slide = ninja_forms_return_echo( 'nf_mp_edit_field_output_all_uls', $form_id );

	header("Content-type: application/json");
	$array = array( 'new_nav' => $new_nav, 'new_slide' => $new_slide );
	echo json_encode( $array );

	die();
}

add_action( 'wp_ajax_nf_mp_delete_page', 'nf_mp_delete_page' );

/**
 *
 * Function used to copy a page from the Field Settings tab. It is called via ajax.
 *
 * @since 1.3
 * @returns void
 */

function nf_mp_copy_page(){
	// Setup our initial variables.
	$form_id = $_REQUEST['form_id'];
	$field_ids = $_REQUEST['field_ids'];
	$field_data = json_decode( stripslashes( $_REQUEST['field_data'] ), true );
	
	$new_ids = array();	
	$order = 999;
	$fields = array();
	$new_fields = array();

	// Loop through our received field data and re-key it so that it is easy to check/merge later.
	$tmp_array = array();
	foreach ( $field_data as $data ) {
		$field_id = $data['id'];
		unset( $data['id'] );
		$tmp_array[ $field_id ] = $data;
	}

	$field_data = apply_filters( 'nf_mp_before_copy_page', $tmp_array );
	
	// Loop through our received field ids. We're going to merge any receieved data for those fields with the data saved in the database.
	foreach ( $field_ids as $field_id ) {
		if ( ! isset ( Ninja_Forms()->form( $form_id )->fields[ $field_id ] ) )
			continue;

		// Grab our saved field data.
		$field = Ninja_Forms()->form( $form_id )->fields[ $field_id ];

		// // Use the wp_parse_args() function to merge our passed data with our saved data.
		if ( is_array( $field_data[ $field_id ] ) ) {
			$data = wp_parse_args( $field_data[ $field_id ], $field['data'] );
		} else {
			$data = $field['data'];
		}

		$new_type = $field['type'];

		$fav_id = isset ( $field['fav_id'] ) ? $field['fav_id'] : 0;
		$def_id = isset ( $field['def_id'] ) ? $field['def_id'] : 0;

		$data = serialize( $data );

		$args = array( 'type' => $new_type, 'data' => $data, 'order' => $order, 'fav_id' => $fav_id, 'def_id' => $def_id );

		$new_id = ninja_forms_insert_field( $form_id, $args );

		if ( $new_type == '_page_divider' )
			$divider_id = $new_id;
	}

	do_action( 'nf_mp_after_copy_page', $new_fields );	

	// Update our form object since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();

	$pages = nf_mp_get_pages( $form_id );
	$current_page = nf_mp_get_page_count( $form_id );

	$fields = isset ( $pages[ $current_page ]['fields'] ) ? $pages[ $current_page ]['fields'] : array();
	$page_title = isset ( $pages[ $current_page ]['page_title'] ) ? $pages[ $current_page ]['page_title'] : '';
	$new_part = array( 'id' => $divider_id, 'fields' => $fields, 'num' => $current_page, 'page_title' => $page_title );

	$new_nav = ninja_forms_return_echo( 'nf_mp_admin_page_nav', $form_id, $current_page );
	$new_slide = ninja_forms_return_echo( 'nf_mp_edit_field_output_all_uls', $form_id );

	header("Content-type: application/json");
	$array = array( 'new_nav' => $new_nav, 'new_slide' => $new_slide, 'new_part' => $new_part );
	echo json_encode( $array );
	die();
}

add_action( 'wp_ajax_nf_mp_copy_page', 'nf_mp_copy_page' );

/**
 * Enable multi-part forms by adding a new page. 
 * This adds a divider with an order of 0 and another divider at the end of all the fields.
 *
 * @since 1.3
 * @return void
 */
function nf_mp_enable() {
	$form_id = $_REQUEST['form_id'];
	// Bail if we aren't in the admin
	if ( ! is_admin() )
		return false;

	check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );

	// Add a page to the beginning of the form.
	$args = array( 'type' => '_page_divider', 'order' => -1, 'fav_id' => 0, 'def_id' => 0 );
	$new_id = ninja_forms_insert_field( $form_id, $args );
	// Update our form object cache since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();
	$pages = nf_mp_get_pages( $form_id );
	$fields = isset ( $pages[1]['fields'] ) ? $pages[1]['fields'] : array();
	$new_parts[] = array( 'id' => $new_id, 'fields' => $fields, 'num' => 1 );

	// Add a page to the end of the form.
	$args = array( 'type' => '_page_divider', 'order' => 999, 'fav_id' => 0, 'def_id' => 0 );
	$new_id = ninja_forms_insert_field( $form_id, $args );
	// Update our form object cache since we added new fields.
	Ninja_Forms()->form( $form_id )->update_fields();
	$pages = nf_mp_get_pages( $form_id );
	$fields = isset ( $pages[2]['fields'] ) ? $pages[2]['fields'] : array();
	$new_parts[] = array( 'id' => $new_id, 'fields' => $fields, 'num' => 2 );

	$new_nav = ninja_forms_return_echo('nf_mp_admin_page_nav', $form_id, 1 );
	$new_slide = ninja_forms_return_echo('nf_mp_edit_field_output_all_uls', $form_id );
	header("Content-type: application/json");
	$array = array ( 'new_nav' => $new_nav, 'new_slide' => $new_slide, 'new_parts' => $new_parts );
	echo json_encode($array);

	die();
}

add_action( 'wp_ajax_nf_mp_enable', 'nf_mp_enable' );
