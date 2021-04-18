<?php

/**
 * This function will be ran during the pre processing (pre_process) of a form.
 * The goals here are to:
 *		Move the file to a temporary location.
 *		Save the temporary location along with the file name
 *
 * @param int $field_id - ID number of the field that is currently being displayed.
 * @param array/string $user_value - the value of the field within the user-submitted form.
 */

function ninja_forms_field_upload_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;

	$plugin_settings = get_option('ninja_forms_settings');
	$field_data = $ninja_forms_processing->get_field_settings( $field_id );
	$field_data = $field_data['data'];

	if(isset($field_data['upload_types'])){
		$upload_types = $field_data['upload_types'];
	}else{
		$upload_types = '';
	}

	if(isset($field_data['upload_rename'])){
		$upload_rename = $field_data['upload_rename'];
	}else{
		$upload_rename = '';
	}

	if(isset($field_data['email_attachment'])){
		$email_attachment = $field_data['email_attachment'];
	}else{
		$email_attachment = '';
	}

	if(isset($plugin_settings['base_upload_dir'])){
		$base_upload_dir = $plugin_settings['base_upload_dir'];
	}else{
		$base_upload_dir = '';
	}

	if(isset($plugin_settings['custom_upload_dir'])){
		$custom_upload_dir = $plugin_settings['custom_upload_dir'];
	}else{
		$custom_upload_dir = '';
	}

	if(isset($plugin_settings['max_file_size'])){
		$max_file_size = $plugin_settings['max_file_size'];
	}else{
		$max_file_size = 2;
	}

	if( isset( $_FILES['ninja_forms_field_'.$field_id] ) ){
		$files = array();
		$fdata = $_FILES['ninja_forms_field_'.$field_id];

		if( is_array( $fdata['name'] ) ){
			foreach( $fdata['name'] as $key => $val ){
				if( $key == 'new' ){
					for ($x=0; $x < count( $fdata['name']['new'] ); $x++) {
						if( $fdata['error']['new'][$x] != 4 ){
							$files[$x] = array(
								'name'    => $fdata['name']['new'][$x],
							    'type'  => $fdata['type']['new'][$x],
							    'tmp_name'=> $fdata['tmp_name']['new'][$x],
							    'error' => $fdata['error']['new'][$x],
							    'size'  => $fdata['size']['new'][$x],
							);
						}
					}
				}else{
			    	$files[$key]=array(
					    'name'    => $fdata['name'][$key],
					    'type'  => $fdata['type'][$key],
					    'tmp_name'=> $fdata['tmp_name'][$key],
					    'error' => $fdata['error'][$key],
					    'size'  => $fdata['size'][$key],
					    'key' => $key
				    );
				}
		    }
		    $multi = true;
		}else{
			$files[0] = $fdata;
			$multi = false;
		}

		// Remove the initial value for our field in the $ninja_forms_processing global
		$ninja_forms_processing->remove_field_value( $field_id );

		// Loop through our submitted files array and remove any that are "empty" but have _upload_ fields submitted.
		$tmp_array = array();

		if( isset( $_POST['_upload_'.$field_id] ) AND is_array( $_POST['_upload_'.$field_id] ) ){
			foreach( $_POST['_upload_'.$field_id] as $key => $val ){
				if( ( isset( $files[$key] ) AND $files[$key]['name'] == '' ) OR !isset( $files[$key] ) ){
					$tmp_array[$key] = $_POST['_upload_'.$field_id][$key];
					unset( $files[$key] );
				}
			}
		}

		$ninja_forms_processing->update_field_value( $field_id, $tmp_array );

		$file_error = false;
		foreach( $files as $key => $f ){
			if( isset( $f['error'] ) AND !empty($f['error'] ) ){
				if( $f['error'] == 1 or $f['error'] == 2 ){
					$ninja_forms_processing->add_error('upload_'.$field_id, __('File exceeds maximum file size. File must be under: '.$max_file_size.'mb.', 'ninja-forms-uploads'), $field_id);
				}
				$file_error = true;
			}
		}

		if( !$file_error ){
			foreach( $files as $key => $file ){
				ninja_forms_field_upload_move_uploads( $field_id, $file, $multi );
			}
		}
		//print_r($ninja_forms_processing->get_field_value($field_id));
	}
	do_action( 'ninja_forms_upload_pre_process', $field_id );
}

function ninja_forms_field_upload_update_file_list( $field_id ){
	global $ninja_forms_processing;

	$uploads = $ninja_forms_processing->get_extra_value( 'uploads' );
	if( !is_array( $uploads ) ){
		$uploads = array();
	}

	array_push( $uploads, $field_id );

	$ninja_forms_processing->update_extra_value( 'uploads', $uploads );
}

add_action( 'ninja_forms_upload_pre_process', 'ninja_forms_field_upload_update_file_list' );

function ninja_forms_field_upload_move_uploads($field_id, $file_data, $multi = false){
	global $ninja_forms_processing;

	if( $ninja_forms_processing->get_form_setting( 'create_post' ) ){
		$create_post = $ninja_forms_processing->get_form_setting( 'create_post' );
	}else{
		$create_post = 0;
	}

	$plugin_settings = get_option('ninja_forms_settings');
	$field_data = $ninja_forms_processing->get_field_settings( $field_id );
	$field_data = $field_data['data'];

	if(isset($field_data['upload_types'])){
		$upload_types = $field_data['upload_types'];
	}else{
		$upload_types = '';
	}

	if(isset($field_data['upload_rename'])){
		$upload_rename = $field_data['upload_rename'];
	}else{
		$upload_rename = '';
	}

	if(isset($field_data['email_attachment'])){
		$email_attachment = $field_data['email_attachment'];
	}else{
		$email_attachment = '';
	}

	if(isset($plugin_settings['base_upload_dir'])){
		$base_upload_dir = $plugin_settings['base_upload_dir'];
	}

	if(isset($plugin_settings['base_upload_url'])){
		$base_upload_url = $plugin_settings['base_upload_url'];
	}else{
		$base_upload_url = '';
	}

	if(isset($plugin_settings['custom_upload_dir'])){
		$custom_upload_dir = $plugin_settings['custom_upload_dir'];
	}else{
		$custom_upload_dir = '';
	}

	$random_string = ninja_forms_random_string(5);

	if ( ! is_dir( $base_upload_dir ) )
		mkdir( $base_upload_dir );

	if ( ! is_dir( $base_upload_dir . '/tmp/' ) )
		mkdir( $base_upload_dir . '/tmp/' );

	$tmp_upload_file = $base_upload_dir.'/tmp/'.$random_string.'/';
	if(is_dir($tmp_upload_file)){
		rmdir($tmp_upload_file);
	}

	mkdir($tmp_upload_file);

	$tmp_upload_file .= 'ninja_forms_field_'.$field_id;

	$file_name = '';

	move_uploaded_file($file_data['tmp_name'], $tmp_upload_file);
	$user_file_name = $file_data['name'];

	$orig_user_file_name = $user_file_name;

	if($multi){
		$update_array = $ninja_forms_processing->get_field_value($field_id);
		if( !is_array( $update_array ) OR empty( $update_array ) ){
			$update_array = array();
		}
	}else{
		$update_array = array();
	}

	$user_file_array = array();
	if($user_file_name != ''){

		//Trim whitespace and replace special characters from our file name.
		$user_file_name = stripslashes(trim($user_file_name));
		$user_file_name = strtolower($user_file_name);
		$user_file_name = preg_replace("/[\/\&%#\$]/", "", $user_file_name);
		$user_file_name = preg_replace("/[\"\']/", "", $user_file_name);
		$user_file_name = preg_replace("/\s+/", "", $user_file_name);

		$user_file_array = explode(".", $user_file_name);
		$ext = array_pop($user_file_array);
		if(isset($upload_types) AND !empty($upload_types)){
			if( strpos( $upload_types, $ext ) === false || strpos( $user_file_name, '.php' ) ){
				$ninja_forms_processing->add_error('upload_'.$field_id, apply_filters( 'nf_uploads_unallowed_file_type', __( 'File type is not allowed: ', 'ninja-forms-uploads' ) ) . $user_file_name, $field_id);
			}
		}

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
			$display_name = '';
			$first_name = '';
			$last_name = '';
			$user_id = '';
		}

		//If we have a file naming convention, use it to change our file name.
		if(!empty($upload_rename)){
			if(is_array($user_file_array) AND !empty($user_file_array)){
				$user_file_name = implode($user_file_array);

			}

			$user_file_name = stripslashes( trim( $user_file_name ) );

			$user_file_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $user_file_name);

			$file_name = str_replace("%filename%", $user_file_name, $upload_rename);
			$file_name = str_replace("%formtitle%", $form_title, $file_name);
			$file_name = str_replace("%date%", date('Y-m-d'), $file_name);
			$file_name = str_replace("%month%", date('m'), $file_name);
			$file_name = str_replace("%day%", date('d'), $file_name);
			$file_name = str_replace("%year%", date('Y'), $file_name);
			$file_name = str_replace("%username%", $user_name, $file_name);
			$file_name = str_replace("%userid%", $user_id, $file_name);
			$file_name = str_replace("%displayname%", $display_name, $file_name);
			$file_name = str_replace("%firstname%", $first_name, $file_name);
			$file_name = str_replace("%lastname%", $last_name, $file_name);
			$file_name = str_replace("%random%", ninja_forms_random_string(5), $file_name );

			// Loop through our fields and see if we have any renaming fields.
			$fields = $ninja_forms_processing->get_all_fields();
			foreach ( $fields as $field_id => $user_value ) {
				if ( is_array( $user_value ) ) {
					$user_value = implode( ',', $user_value );
				}
				$user_value = strtolower( wp_kses_post( $user_value ) );
				$file_name = str_replace( "%field_" . $field_id . "%", $user_value, $file_name );
			}


			$file_name .= '.'.$ext;
			
		}else{
			$user_file_name = stripslashes( trim( $user_file_name ) );
			$user_file_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $user_file_name);
			$file_name = $user_file_name;
		}

		$file_name = apply_filters( 'nf_fu_filename' , $file_name );

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
		}

		$upload_path = $base_upload_dir.$custom_upload_dir;
		$custom_upload_url = str_replace( "\\", "/", $custom_upload_dir );

		$file_url = $base_upload_url.$custom_upload_url;
		$file_url = apply_filters( 'ninja_forms_uploads_url', $file_url, $field_id );
		$file_url = trailingslashit( $file_url );

		$upload_path = apply_filters( 'ninja_forms_uploads_dir', $upload_path, $field_id );
		$upload_path = trailingslashit( $upload_path );

		$file_dir = $upload_path.'/'.$file_name;

		// Check to see if the file already exists. If it does, add numbers to the end until we find one that doesn't.
		$file_name_array = ninja_forms_check_file_exists( $upload_path, $file_name );
		$file_name = $file_name_array['file_name'];
		$base_name = $file_name_array['base_name'];

		if ( ninja_forms_check_file_name_in_update_array ( $file_name ) ) {
			$x = 1;

			while ( ninja_forms_check_file_name_in_update_array ( $file_name ) ) {
				if( $x < 9 ){
					$num = "00".$x;
				}else if( $x > 9 AND $x < 99 ){
					$num = "0".$x;
				}else{
					$num = $x;
				}
				$name = $base_name.'-'.$num;
				if( $ext != '' ){
					$tmp_name = $name.'.'.$ext;
				}else{
					$tmp_name = $name;
				}

				$file_name_array = ninja_forms_check_file_exists( $upload_path, $tmp_name, $base_name, $x );
				$file_name = $file_name_array['file_name'];

				$x++;
			}
		}
	}

	$file_url .= $file_name;

	$tmp_array = array(
		'user_file_name' => $orig_user_file_name,
		'file_name' => $file_name,
		'file_path' => $tmp_upload_file,
		'file_url' => $file_url,
		'complete' => 0,
	);

	//$tmp_array['user_file_name'] = $orig_user_file_name;
	//$tmp_array['file_name'] = $file_name;
	//$tmp_array['file_path'] = $tmp_upload_file;
	//$tmp_array['file_url'] = $file_url;
	//$tmp_array['complete'] = 0;

	//array_push( $update_array, $tmp_array );

	if( isset( $file_data['key'] ) ){
		$file_key = $file_data['key'];
	}else{
		$file_key = ninja_forms_field_uploads_create_key( $update_array );
	}

	$update_array[$file_key] = $tmp_array;

	$update_array = apply_filters( 'ninja_forms_upload_pre_process_array', $update_array );

	$ninja_forms_processing->update_field_value( $field_id, $update_array );
}

function ninja_forms_field_uploads_create_key( $update_array ){
	$new_key = ninja_forms_random_string(5);
	if( array_key_exists( $new_key, $update_array ) ){
		$new_key = ninja_forms_random_string(5);
	}

	return $new_key;
}

function ninja_forms_check_file_exists( $upload_path, $file_name, $base_name = '', $x = 1 ){
	$file_dir = $upload_path.'/'.$file_name;
	$tmp_name = $file_name;
	if( strpos( $tmp_name, '.' ) !== false ){
		$tmp_name = explode( '.', $tmp_name );
		if ( $base_name == '' ) {
			$base_name = array_shift( $tmp_name );
		}
		$ext = array_pop( $tmp_name );
	}else{
		$base_name = $tmp_name;
		$ext = '';
	}

	if ( file_exists ( $file_dir ) ) {
		while( file_exists( $file_dir ) ){
			if( $x < 9 ){
				$num = "00".$x;
			}else if( $x > 9 AND $x < 99 ){
				$num = "0".$x;
			}else{
				$num = $x;
			}
			$name = $base_name.'-'.$num;
			if( $ext != '' ){
				$tmp_name = $name.'.'.$ext;
			}else{
				$tmp_name = $name;
			}

			$file_dir = $upload_path.'/'.$tmp_name;
			$x++;
		}
		$file_name = $tmp_name;
	}

	return array( 'file_name' => $file_name, 'base_name' => $base_name );
}

function ninja_forms_check_file_name_in_update_array( $file_name ){
	global $ninja_forms_processing;

	foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
		if ( $ninja_forms_processing->get_field_setting( $field_id, 'type' ) == '_upload' ) {
			$files_array = $ninja_forms_processing->get_field_value( $field_id );
			if ( is_array ( $files_array ) && ! empty ( $files_array ) ) {
				foreach ( $files_array as $key => $file ) {
					if ( isset ( $file['file_name'] ) && $file['file_name'] == $file_name ) {
						return true;
					}
				}
			}
		}
	}
	return false;
}
