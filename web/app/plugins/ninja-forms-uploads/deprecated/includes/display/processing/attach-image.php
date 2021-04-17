<?php

function ninja_forms_attach_files_to_post( $post_id ){
	global $ninja_forms_processing;

	if( $ninja_forms_processing->get_extra_value( 'uploads' ) ){
		foreach( $ninja_forms_processing->get_extra_value( 'uploads' ) as $field_id ){
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );
			$user_value = $ninja_forms_processing->get_field_value( $field_id );

			if( is_array( $user_value ) AND !empty( $user_value ) ){
				$tmp_array = array();

				$args = array(
		           'post_parent' => $post_id,
		           'post_status' => 'null',
		           'post_type'=> 'attachment',
		           'posts_per_page' => -1,
		        );

		        $attachments = get_posts( $args );

		        if( !empty( $attachments ) ){
		        	$x = 0;
		        	foreach( $attachments as $attachment ){

		        		$attach_field = get_post_meta( $attachment->ID, 'ninja_forms_field_id', true );
						$file_key = get_post_meta( $attachment->ID, 'ninja_forms_file_key', true );
						$upload_id = get_post_meta( $attachment->ID, 'ninja_forms_upload_id', true );
						if( $attach_field == $field_id ){
							if( !array_key_exists( $file_key, $user_value ) ){
								if( $upload_id != '' ){
									ninja_forms_delete_upload( $upload_id );
								}
								wp_delete_attachment( $attachment->ID );
							}else{
								$tmp_array[$x]['id'] = $attachment->ID;
								$tmp_array[$x]['field_id'] = $attach_field;
								$tmp_array[$x]['file_key'] = $file_key;
							}
						}else if( $attach_field == '' ){
							wp_update_post( array( 'ID' => $attachment->ID, 'post_parent' => 0 ) );
						}
						$x++;
		        	}
		        }

		        $attachments = $tmp_array;

				foreach( $user_value as $key => $file ){
					// Check to see if we are changing files that already exist.
					if( !isset( $file['changed'] ) OR $file['changed'] == 1 ){
						foreach( $attachments as $attachment ){
							if( $attachment['file_key'] == $key ){
								$upload_id = get_post_meta( $attachment['id'], 'ninja_forms_upload_id', true );
								if( $upload_id != '' ){
									ninja_forms_delete_upload( $upload_id );
								}
								wp_delete_attachment( $attachment['id'] );
							}
						}
					}

					if( isset( $file['complete'] ) AND $file['complete'] == 1 && ( ! isset ( $file['changed'] ) || $file['changed'] == 1 ) ){
						
						$filename = $file['file_path'].$file['file_name'];
						$attach_array = ninja_forms_generate_metadata( $post_id, $filename );
						
						$attach_id = $attach_array['attach_id'];
						$attach_data = $attach_array['attach_data'];
						if( !empty( $attach_array ) AND isset( $field_row['data']['featured_image'] ) AND $field_row['data']['featured_image'] == 1 ){
							ninja_forms_set_featured_image( $post_id, $attach_id );
						}
						update_post_meta( $attach_id, 'ninja_forms_field_id', $field_id );
						update_post_meta( $attach_id, 'ninja_forms_file_key', $key );
						update_post_meta( $attach_id, 'ninja_forms_upload_id', $file['upload_id'] );
						$file['attachment_id'] = $attach_id;
						$user_value[ $key ] = $file;
						
					}
				}
				$ninja_forms_processing->update_field_value( $field_id, $user_value );
			} else {

				$args = array(
		           'post_parent' => $post_id,
		           'post_status' => 'null',
		           'post_type'=> 'attachment',
		           'posts_per_page' => -1,
		        );

		        $attachments = get_posts( $args );

				// Loop through our attachments and make sure that we don't have any empty fields.
				foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
					if ( $ninja_forms_processing->get_field_setting( $field_id, 'type') == '_upload' ) {
						
				        if( !empty( $attachments ) ){
				        	foreach( $attachments as $attachment ){

				        		$attach_field = get_post_meta( $attachment->ID, 'ninja_forms_field_id', true );
								if( $attach_field == $field_id && empty ( $user_value ) ){
									wp_delete_attachment( $attachment->ID );
								}
				        	}
				        }
					}
				}
			}
		}
	}
}

add_action( 'ninja_forms_create_post', 'ninja_forms_attach_files_to_post' );
add_action( 'ninja_forms_update_post', 'ninja_forms_attach_files_to_post' );

/*
 *
 * Function to check whether or not a file should be added to the media library. If it is, call the attachment function.
 *
 * @since 1.0.3
 * @return void
 */

function ninja_forms_check_add_to_media_library( $form_id ){
	global $ninja_forms_processing;
	if ( $ninja_forms_processing->get_form_setting( 'create_post' ) != 1 ) {
		if( $ninja_forms_processing->get_extra_value( 'uploads' ) ){
			foreach( $ninja_forms_processing->get_extra_value( 'uploads' ) as $field_id ){

				$field_row = $ninja_forms_processing->get_field_settings( $field_id );
				$user_value = $ninja_forms_processing->get_field_value( $field_id );
				if( isset( $field_row['data']['media_library'] ) AND $field_row['data']['media_library'] == 1 ){
					
					if( is_array( $user_value ) ){
						foreach( $user_value as $key => $file ){
							$filename = $file['file_path'].$file['file_name'];
							$attach_array = ninja_forms_generate_metadata( '', $filename );
							$user_value[$key]['attachment_id'] = $attach_array['attach_id'];
						}
						// $ninja_forms_processing->update_field_value( $field_id, $user_value );
					}
				}
			}
		}		
	}
}

add_action( 'ninja_forms_post_process', 'ninja_forms_check_add_to_media_library' );


function ninja_forms_generate_metadata( $post_id, $filename ){

	$wp_filetype = wp_check_filetype( basename( $filename ), null );
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
		'post_content' => '',
		'post_status' => 'null'
	);
	$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
	// you must first include the image.php file
	// for the function wp_generate_attachment_metadata() to work
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	$attach_data['ninja_forms_upload_field'] = true;
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	return array( 'attach_id' => $attach_id, 'attach_data' => $attach_data );
}

function ninja_forms_set_featured_image( $post_id, $attach_id ) {
	// set as featured image
	return update_post_meta( $post_id, '_thumbnail_id', $attach_id );
}

function ninja_forms_post_edit_file_attachment_filter( $data, $field_id ){
	global $post;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	if( $field_type == '_upload' AND is_object( $post ) ){
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $post->ID,
		    'posts_per_page' => -1,
		); 
		$attachments = get_posts($args);
		if( $attachments ){
			foreach ($attachments as $attachment) {
				$attach_field = get_post_meta( $attachment->ID, 'ninja_forms_field_id', true );
				$file_key = get_post_meta( $attachment->ID, 'ninja_forms_file_key', true );
				$upload_id = get_post_meta( $attachment->ID, 'ninja_forms_upload_id', true );
				if( $attach_field == $field_id ){
					$filename = basename ( get_attached_file( $attachment->ID ) );
					$filepath = str_replace( $filename, '', get_attached_file( $attachment->ID ) );
					if ( ! is_array( $data['default_value'] ) ) {
						$data['default_value'] = array();
					}					
					$data['default_value'][$file_key] = array(
						'user_file_name' => $filename,
						'file_name' => $filename,
						'file_path' => $filepath,
						'file_url' => wp_get_attachment_url( $attachment->ID ),
						'complete' => 1,
						'upload_id' => $upload_id
					);

				}
			}
		}

		if( isset( $field_row['data']['featured_image'] ) AND $field_row['data']['featured_image'] == 1 ){
			$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
			if ( $post_thumbnail_id != '' ) {
				$attach_field = get_post_meta( $post_thumbnail_id, 'ninja_forms_field_id', true );
				if( $attach_field == '' ){
					$file_key = ninja_forms_field_uploads_create_key( array() );
					$upload_id = '';				
					$filename = basename ( get_attached_file( $post_thumbnail_id ) );
					$filepath = str_replace( $filename, '', get_attached_file( $post_thumbnail_id ) );
					$data['default_value'][$file_key] = array(
						'user_file_name' => $filename,
						'file_name' => $filename,
						'file_path' => $filepath,
						'file_url' => wp_get_attachment_url( $post_thumbnail_id ),
						'complete' => 1,
						'upload_id' => $upload_id
					);
				}				
			}

		}

		if( isset( $data['default_value'] ) AND is_array( $data['default_value'] ) ){
			uasort($data['default_value'], 'ninja_forms_compare_file_name');
		}
	}
	
	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_post_edit_file_attachment_filter', 25, 2 );

function ninja_forms_compare_file_name( $a, $b ){
	if ( !isset ( $a['file_name'] ) or !isset ( $b['file_name'] ) )
		return false;
    if( $a['file_name'] == $b['file_name'] ){
        return 0;
    }
    return ($a['file_name'] < $b['file_name']) ? -1 : 1;
}