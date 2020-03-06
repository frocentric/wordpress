<?php

add_action( 'init', 'ninja_forms_register_create_post_process' );
function ninja_forms_register_create_post_process(){
	add_action( 'ninja_forms_process', 'ninja_forms_create_post_process', 11 );
	add_action ('ninja_forms_pre_process', 'ninja_forms_create_post_pre_process' );
}

function ninja_forms_create_post_pre_process(){
	global $ninja_forms_processing;
	if( isset( $_POST['_post_id'] ) ){
		$ninja_forms_processing->update_form_setting( 'post_id', $_POST['_post_id'] );
	}
}

function ninja_forms_create_post_process(){
	global $ninja_forms_processing, $current_user;
	get_currentuserinfo();

	$create_post = $ninja_forms_processing->get_form_setting( 'create_post' );
	
	if($create_post == 1){
		//Get post information set by the admin
		$post_tax = $ninja_forms_processing->get_form_setting( 'post_tax' );
		$post_as = $ninja_forms_processing->get_form_setting( 'post_as' );
		$post_type = $ninja_forms_processing->get_form_setting( 'post_type' );
		$post_status = $ninja_forms_processing->get_form_setting( 'post_status' );
		$post_excerpt = $ninja_forms_processing->get_form_setting( 'post_excerpt' );

		//Get post information sent by the user.
		$post_title = $ninja_forms_processing->get_form_setting( 'post_title' );
		$post_content = $ninja_forms_processing->get_form_setting( 'post_content' );
		$post_tags = $ninja_forms_processing->get_form_setting( 'post_tags' );

		if( $ninja_forms_processing->get_form_setting( 'post_id' ) ){
			$post_id = $ninja_forms_processing->get_form_setting( 'post_id' );
		}else{
			$post_id = '';
		}
		
		$current_user_id = get_current_user_id();
		if( 0 == $post_as AND 0 != $current_user_id ){
			$post_as = $current_user_id;
		}


		$args = array(
			'ID' 			 => $post_id,
			'post_author'    => $post_as,
			'post_content'   => $post_content,
			'post_excerpt' 	 => $post_excerpt,
			'post_status'    => $post_status,
			'post_title'     => $post_title,
			'post_type'      => $post_type,
			'tags_input'     => $post_tags,
		);

		// Insert the post into the database
		$post_id = wp_insert_post( $args );
		$ninja_forms_processing->update_form_setting( 'post_id', $post_id );

		$all_taxonomies = get_taxonomies( '','names' );
		
		unset( $all_taxonomies['post_tag'] );
		unset( $all_taxonomies['nav_menu'] );
		unset( $all_taxonomies['link_category'] );
		unset( $all_taxonomies['post_format'] );

		//We have to set the post taxonomies after our post has been created/updated.
		foreach( $all_taxonomies as $tax ){
			if( $ninja_forms_processing->get_form_setting( $tax.'_terms' ) ){
				$tax_terms = $ninja_forms_processing->get_form_setting( $tax.'_terms' );
				$tax_terms = array_map('intval', $tax_terms);
				wp_set_object_terms( $post_id, $tax_terms, $tax );			
			}
		}

		do_action( 'ninja_forms_create_post', $post_id );
	}
}

/*
 *
 * Function used to store the attachment IDs being uploaded into a $_SESSION variable.
 *
 * This should affect files being uploaded by the media manager popup on the front-end.
 * Becuase the post hasn't technically been created yet, when you insert an image or media, it doesn't automatically attach it to the post id.
 *
 * @since 0.8
 * @returns $data
 */

function ninja_forms_set_attachment_to_change( $data, $attachment_id ){
	if ( !isset ( $data['ninja_forms_upload_field'] ) OR !$data['ninja_forms_upload_field'] ) {
		if ( !isset( $_SESSION['ninja_forms_change_attachment'] ) OR !is_array( $_SESSION['ninja_forms_change_attachment'] ) ) {
			$_SESSION['ninja_forms_change_attachment'] = array();
		}
		$_SESSION['ninja_forms_change_attachment'][] = $attachment_id;
	}
	
	return $data;
}

add_filter( 'wp_update_attachment_metadata', 'ninja_forms_set_attachment_to_change', 10, 2 );

/*
 *
 * Function used to attach media uploads to the newly created post when a post is updated or created.
 *
 * @since 0.8
 * @returns void
 */

function ninja_forms_attach_media_uploads( $post_id ){
	if ( isset( $_SESSION['ninja_forms_change_attachment'] ) AND is_array( $_SESSION['ninja_forms_change_attachment'] ) ) {
		foreach ( $_SESSION['ninja_forms_change_attachment'] as $attachment_id ) {
			$post = get_post( $attachment_id, ARRAY_A );
			if ( is_array( $post ) ) {
				wp_update_post( array( 'ID' => $attachment_id, 'post_type' => 'attachment', 'post_parent' => $post_id ) );	
			}
		} 
		$_SESSION['ninja_forms_change_attachment'] = '';
	}
}

add_action( 'ninja_forms_create_post', 'ninja_forms_attach_media_uploads' );
