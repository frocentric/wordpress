<?php

/**
 * This function will be ran during the processing (process) of a form.
 * The goals here are to:
 *		Move the temporary file to its permanent location.
 *
 * @param int $field_id - ID number of the field that is currently being displayed.
 * @param array/string $user_value - the value of the field within the user-submitted form.
 */

function ninja_forms_field_upload_process($field_id, $user_value){
	global $ninja_forms_processing;

	$field_data = $ninja_forms_processing->get_all_submitted_fields();

	$plugin_settings = get_option('ninja_forms_settings');
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_data = $field_row['data'];
	
	$base_upload_dir = $plugin_settings['base_upload_dir'];

	if(isset($plugin_settings['custom_upload_dir'])){
		$custom_upload_dir = $plugin_settings['custom_upload_dir'];
	}else{
		$custom_upload_dir = '';
	}

	$tmp_upload_file = $ninja_forms_processing->get_field_value( $field_id );

	if( is_array( $tmp_upload_file ) ){
		foreach( $tmp_upload_file as $key => $file ){
			if( ( isset( $file['complete'] ) AND $file['complete'] == 0 ) OR !isset( $file['complete'] ) ){
				if( isset( $file['file_path'] ) ){
					$file_path = $file['file_path'];
				}else{
					$file_path = '';
				}
				
				if($file_path != ''){
					$file_name = $file['file_name'];
					$user_file_name = $file['user_file_name'];

					$form_title = strtolower( stripslashes( trim( $ninja_forms_processing->get_form_setting('form_title') ) ) );
					$form_title = preg_replace("/[\/\&%#\$]/", "", $form_title);
					$form_title = preg_replace("/[\"\']/", "", $form_title);
					$form_title = preg_replace('/\s+/', '', $form_title);

					if(is_user_logged_in()){
						$current_user = wp_get_current_user();
						$user_name = $current_user->user_nicename;
						$user_id = $current_user->ID;
						$display_name = $current_user->display_name;
						$first_name = $current_user->user_firstname;
						$last_name = $current_user->user_lastname;
					}else{
						$user_name = '';
						$user_id = '';
						$display_name ='';
						$first_name = '';
						$last_name = '';
					}

					if($custom_upload_dir != ''){
						$custom_upload_dir = stripslashes(trim($custom_upload_dir));

						$custom_upload_dir = str_replace("%filename%", $user_file_name, $custom_upload_dir);
						$custom_upload_dir = str_replace("%formtitle%", $form_title, $custom_upload_dir);
						$custom_upload_dir = str_replace("%date%", date('Y-m-d'), $custom_upload_dir);
						$custom_upload_dir = str_replace("%month%", date('m'), $custom_upload_dir);
						$custom_upload_dir = str_replace("%day%", date('d'), $custom_upload_dir);
						$custom_upload_dir = str_replace("%year%", date('Y'), $custom_upload_dir);
						$custom_upload_dir = str_replace("%username%", $user_name, $custom_upload_dir);
						$custom_upload_dir = str_replace("%userid%", $user_id, $custom_upload_dir);
						$custom_upload_dir = str_replace("%displayname%", $display_name, $custom_upload_dir);
						$custom_upload_dir = str_replace("%firstname%", $first_name, $custom_upload_dir);
						$custom_upload_dir = str_replace("%lastname%", $last_name, $custom_upload_dir);

						if( strpos( $custom_upload_dir, '/' ) !== false ){
							$sep = '/';
						}else if( strpos( $custom_upload_dir, '\\' ) !== false ){
							$sep = '\\';
						} else {
							$sep = '/';
						}
						
						$custom_upload_dir = untrailingslashit( $custom_upload_dir );

						//Replacement for line 85->98 in ninja-forms-uploads\includes\display\processing\processing.php
						$tmp_upload_dir = explode( $sep, $custom_upload_dir );
						$tmp_dirs = array(); //We are going to store all dir levels in this array first
						$tmp_dir = $base_upload_dir; //easier to set here directly instead of in the foreach loop

						//Let’s store all dir levels
						foreach( $tmp_upload_dir as $dir ){
							$tmp_dir = $tmp_dir.$dir.$sep;
							//Prepend to array to get the deepest dir at the beginning
							array_unshift($tmp_dirs, $tmp_dir);
						}

						$to_create = array();
						//check which dirs to create
						foreach( $tmp_dirs as $dir ){
							if( is_dir($dir) ) {
								break;
							} else {
								array_unshift( $to_create, $dir ); //Prepend to array so the deepest dir will at the end.
							}
						}

						//create dirs
						foreach( $to_create as $dir ) {
							mkdir($dir);
						}

					}
					
					$upload_dir = $base_upload_dir.$custom_upload_dir;

					$upload_dir = apply_filters( 'ninja_forms_uploads_dir', $upload_dir, $field_id );

					$upload_dir = trailingslashit( $upload_dir );

					if( strpos( $upload_dir, '/' ) !== false ){
						$sep = '/';
					}else if( strpos( $upload_dir, '\\' ) !== false ){
						$sep = '\\';
					}
					
					//Replacement for line 113->124 ninja-forms-uploads\includes\display\processing\processing.php
					$tmp_upload_dir = explode( $sep, $upload_dir );
					$tmp_dirs = array(); //We are going to store all dir levels in this array first
					$tmp_dir = '';

					//Let’s store all dir levels
					foreach( $tmp_upload_dir as $dir ){
						$tmp_dir = $tmp_dir.$dir.$sep;
						//Prepend to array to get the deepest dir at the beginning
						array_unshift( $tmp_dirs, $tmp_dir );
					}

					$to_create = array();
					//check which dirs to create
					foreach( $tmp_dirs as $dir ){
						if( is_dir($dir) ) {
							break;
						} else {
							array_unshift( $to_create, $dir ); //Prepend to array so the deepest dir will at the end.
						}
					}

					//create dirs
					foreach( $to_create as $dir ) {
						mkdir($dir);
					}
					
					$file_dir = $upload_dir.$file_name;

					if(!$ninja_forms_processing->get_all_errors()){
						if( file_exists ( $file_path ) AND !is_dir( $file_path ) AND copy( $file_path, $file_dir ) ){

							$current_uploads = $ninja_forms_processing->get_field_value( $field_id );
							if( is_array( $current_uploads ) AND !empty( $current_uploads ) ){
								foreach( $current_uploads as $key => $file ){
									if( $file['file_path'] == $file_path ){
										$current_uploads[$key]['file_path'] = $upload_dir;
										//$current_uploads[$key]['file_name'] = $file_name;
										$current_uploads[$key]['complete'] = 1;
									}
								}
							}

							$ninja_forms_processing->update_field_value($field_id, $current_uploads);

							if(file_exists($file_path)){
								$dir = str_replace('ninja_forms_field_'.$field_id, '', $file_path);
								unlink($file_path);
								if(is_dir($dir)){
									rmdir($dir);
								}
							}
							
						}else{
							$ninja_forms_processing->add_error('upload_'.$field_id, __( 'File Upload Error', 'ninja-forms-uploads' ), $field_id);
						}
					}
				}
			}
		}
		if ( !$ninja_forms_processing->get_all_errors() ) {
			do_action('ninja_forms_upload_process', $field_id);
		}		
	}
}

/**
 * This section updates the upload database whenever a file is uploaded.
 *
 */

function ninja_forms_upload_db_update( $field_id ){
	global $wpdb, $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();
	$user_id = $ninja_forms_processing->get_user_ID();
	$files = $ninja_forms_processing->get_field_value( $field_id );
	$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	if( is_array( $files ) AND !empty( $files ) ){
		foreach( $files as $key => $f ){
			if( !isset( $f['upload_id'] ) OR $f['upload_id'] == '' ){
				if( isset( $field_row['data']['upload_location'] ) ) {
					$f['upload_location'] = $field_row['data']['upload_location'];
				}
				if ( isset ( $f['user_file_name'] ) ) {
					$data = serialize( $f );
					$wpdb->insert( NINJA_FORMS_UPLOADS_TABLE_NAME, array('user_id' => $user_id, 'form_id' => $form_id, 'field_id' => $field_id, 'data' => $data) );
					$files[$key]['upload_id'] = $wpdb->insert_id;						
				}
			}
		}
		$ninja_forms_processing->update_field_value( $field_id, $files );
	}
}

add_action('ninja_forms_upload_process', 'ninja_forms_upload_db_update');

/**
 * Get files uploaded to form for a specific location to save to
 *
 * @param $location
 *
 * @return array|bool
 */
function ninja_forms_upload_get_uploaded_files( $location ) {
	global $ninja_forms_processing;
	$files = array();
	if ( $ninja_forms_processing->get_extra_value( 'uploads' ) ) {
		foreach ( $ninja_forms_processing->get_extra_value( 'uploads' ) as $field_id ) {
			$field_row  = $ninja_forms_processing->get_field_settings( $field_id );
			$user_value = $ninja_forms_processing->get_field_value( $field_id );
			if ( isset( $field_row['data']['upload_location'] ) AND $field_row['data']['upload_location'] == $location ) {
				if ( is_array( $user_value ) ) {
					$files[] = array(
						'user_value' => $user_value,
						'field_row'  => $field_row,
						'field_id'   => $field_id
					);
				}
			}
		}
	}

	return $files;
}

/**
 * Remove uploaded files
 *
 * @param $files
 */
function ninja_forms_upload_remove_uploaded_files( $files ) {
	if ( ! $files ) {
		return;
	}

	foreach( $files as $data ) {
		if ( ! is_array( $data['user_value'] ) ) {
			return;
		}

		foreach ( $data['user_value'] as $key => $file ) {
			if ( ! isset( $file['file_path'] ) ) {
				continue;
			}
			$filename = $file['file_path'] . $file['file_name'];
			if ( file_exists( $filename ) ) {
				// Delete local file
				unlink( $filename );
			}
		}
	}
}

/**
 * Remove uploaded files for when a file upload field has no location set
 * Eg. when an admin only wants uploads to be sent on email attachments and not saved anywhere
 */
function ninja_forms_upload_remove_files_no_location() {
	$files_data = ninja_forms_upload_get_uploaded_files( 'none' );

	ninja_forms_upload_remove_uploaded_files( $files_data );
}
add_action( 'ninja_forms_post_process', 'ninja_forms_upload_remove_files_no_location', 1001 );