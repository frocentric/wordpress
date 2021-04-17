<?php

/*
 * Function that checks to see if a mutlidimensional array is empty.
 *
 * @since 1.2
 * @return bool
 */
function ninja_forms_is_array_empty( $array ) {
    if ( is_array ( $array ) ) {
        foreach ( $array as $value ) {
            if ( !ninja_forms_is_array_empty ( $value ) ) {
                return false;
            }
        }
    } elseif ( !empty ( $array ) ) {
        return false;
    }
    return true;
}

/*
 * Normally, Ninja Forms checks for an empty input to determine whether or not a field has been left blank.
 * This function will be called to provide custom 'required field' validation.
 * 
 * If both the $_FILES[] and $_POST['_upload_ID_user_file_name'] are empty, then the upload field has not been submitted.
 *
 * @param int $field_id - ID number of the field that is currently being displayed.
 * @param array/string $user_value - the value of the field within the user-submitted form.
 */

function ninja_forms_field_upload_req_validation($field_id, $user_value){
	global $ninja_forms_processing;

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

		$file_error = false;
		if ( empty( $files ) ) {
			$file_error = true;
		}

		if(!isset($_POST['_upload_'.$field_id])){
			$name = false;
		}else{
			$name = true;
		}

		if($file_error AND !$name){
			return false;
		}else{
			return true;
		}

	}else{

		if( $ninja_forms_processing->get_field_value( $field_id ) ){
			$user_value = $ninja_forms_processing->get_field_value( $field_id );

			if ( ninja_forms_is_array_empty( $user_value ) ) {
				return false;
			} else {
				return true;
			}

		}else{
			return false;
		}
	}
}

/**
 * This function will filter the values output into the submissions table for uploads.
 * Instead of outputting what is actually in the submission database, which is an array of values (file_name, file_path, file_url),
 * this filter will output a link to where the file is stored.
 *
 * @param array/string $user_value - the value of the field within the user-submitted form.
 * @param int $field_id - ID number of the field that is currently being displayed.
 */

function ninja_forms_field_upload_sub_td($user_value, $field_id){
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	if($field_type == '_upload'){
		if ( is_array( $user_value ) ) {
			$user_value = NF_File_Uploads()->normalize_submission_value( $user_value );
			$new_value = '';
			$x = 0;
			foreach($user_value as $value){
				if($x > 0){
					$new_value .= ' , ';
				}				
				$new_value .= '<a href="'.$value['file_url'].'" target="_blank">'.$value['file_name'].'</a>';
				$x++;
			}
			$user_value = $new_value;
		}
	}

	return $user_value;
}

/**
 * This function will filter the values that are saved within the submission database.
 * It allows those editing the submission to replace files submitted by uploading new ones.
 *
 * @param array/string $user_value - the value of the field within the user-submitted form.
 * @param int $field_id - ID number of the field that is currently being displayed.
 */

function ninja_forms_field_upload_save_sub($user_value, $field_id){
	global $ninja_forms_processing;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$sub_id = $ninja_forms_processing->get_form_setting('sub_id');
	if($field_type == '_upload'){
		if($ninja_forms_processing->get_form_setting('doing_save')){
			ninja_forms_field_upload_pre_process($field_id, $user_value);
			$user_value = $ninja_forms_processing->get_field_value($field_id);
		}else{
			//Check to see if sub_id has been set. If it hasn't, then don't do any filtering.
			if($sub_id != ''){
				
				//Check to see if this is an upload field. If it is, we'll do some processing.
				//If not, we'll just return the $user_value that was passed.
				if(isset($_FILES['ninja_forms_field_'.$field_id]['error'][0]) AND $_FILES['ninja_forms_field_'.$field_id]['error'][0] != 4){
					ninja_forms_field_upload_pre_process($field_id, $user_value);
					ninja_forms_field_upload_process($field_id, $user_value);
					$user_value = $ninja_forms_processing->get_field_value($field_id);
				}else if(isset($_POST['_upload_'.$field_id])){
					$user_value = $_POST['_upload_'.$field_id];
				}
			}
		}
	}

	return $user_value;
}

function ninja_forms_field_upload_filter_data($data, $field_id){
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	if($field_type == '_upload'){
		$data['label'] = '';
	}
	return $data;
}

function ninja_forms_get_uploads($args = array()){
	global $wpdb;
	$plugin_settings = get_option( 'ninja_forms_settings' );
	if ( isset ( $plugin_settings['date_format'] ) ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'mm/dd/yyyy';
	}
	$where = '';
	$limit = '';
	$upload_id = '';

	if(!empty($args)){

		if(isset($args['form_id'])){
			$where .= 'WHERE `form_id` = '.$args['form_id'];
		}
		if( isset( $args['upload_user'] ) ){
			$user = $args['upload_user'];
			if( is_numeric( $user ) ){
				if($where == ''){
					$where .= "WHERE ";
				}else{
					$where .= " AND ";
				}
				$where .= "`user_id` = ".$user;
			}else{
				$user_data = get_user_by( 'email', $user );
				if( !$user_data ){
					$user_data = get_user_by( 'slug', $user );
				}
				if( !$user_data ){
					$user_data = get_user_by( 'login', $user );
				}

				if($where == ''){
					$where .= "WHERE ";
				}else{
					$where .= " AND ";
				}

				if( $user_data ){
					$user_id = $user_data->ID;
					$where .= "`user_id` = ".$user_id;
				}else{
					$where .="`user_id` = 0";
				}
			}
		}
		if(isset($args['id'])){
			if($where == ''){
				$where .= "WHERE ";
			}else{
				$where .= " AND ";
			}
			$where .= "`id` = ".$args['id'];
			$upload_id = $args['id'];
		}
		if(isset($args['begin_date'])){
			$begin_date = $args['begin_date'];
			if ( strtolower( substr( $date_format, 0, 1 ) ) == 'd' ) {
				$begin_date = str_replace( '/', '-', $begin_date );
			}
			$begin_date .= ' 23:59:59';
			$begin_date = strtotime($begin_date);
			$begin_date = date("Y-m-d g:i:s", $begin_date);

			if($where == ''){
				$where .= "WHERE ";
			}else{
				$where .= " AND ";
			}
			$where .= "DATE(date_updated) > '".$begin_date."'";
		}
		if(isset($args['end_date'])){
			$end_date = $args['end_date'];
			if ( strtolower( substr( $date_format, 0, 1 ) ) == 'd' ) {
				$end_date = str_replace( '/', '-', $end_date );
			}
			$end_date .= ' 23:59:59';
			$end_date = strtotime($end_date);
			$end_date = date("Y-m-d g:i:s", $end_date);
			if($where == ''){
				$where .= "WHERE ";
			}else{
				$where .= " AND ";
			}
			$where .= "DATE(date_updated) < '".$end_date."'";
		}
	}

	$results = $wpdb->get_results( "SELECT * FROM ".NINJA_FORMS_UPLOADS_TABLE_NAME." ".$where." ORDER BY `date_updated` DESC", ARRAY_A );
	

	if( isset( $args['upload_types'] ) OR isset( $args['upload_name'] ) ){
		if(is_array($results) AND !empty($results)){
			$tmp_results = array();

			for ($x = 0; $x < count($results); $x++){
				$results[$x]['data'] = unserialize($results[$x]['data']);
				$data = $results[$x]['data'];
				$form_id = $results[$x]['form_id'];
				$form_row = ninja_forms_get_form_by_id($form_id);
				$form_data = $form_row['data'];
				if ( isset ( $form_data['form_title'] ) ) {
					$form_title = $form_data['form_title'];
				} else {
					$form_title = '';
				}
				
				$results[$x]['data']['form_title'] = $form_title;
				
				$user_file_name = $data['user_file_name'];
				$user_file_array = explode(".", $user_file_name);
				$user_ext = array_pop($user_file_array);

				$file_name = $data['file_name'];
				$file_array = explode(".", $file_name);
				$ext = array_pop($file_array);

				if(isset($args['upload_name'])){
					if(stripos($file_name, $args['upload_name']) !== false OR stripos($user_file_name, $args['upload_name']) !== false){
						$file_name_found = true;
					}else{
						$file_name_found = false;
					}
				}
				if(isset($args['upload_types'])){
					if( stripos( $args['upload_types'], $user_ext ) !== false OR stripos( $args['upload_types'], $ext ) !== false ){
						$ext_found = true;
					}else{
						$ext_found = false;
					}
				}
				if(isset($args['upload_name']) AND isset($args['upload_types'])){
					if($file_name_found AND $ext_found){
						array_push($tmp_results, $results[$x]);
					}
				}else if(isset($args['upload_name']) AND !isset($args['upload_types'])){
					if($file_name_found){
						array_push($tmp_results, $results[$x]);
					}
				}else if(isset($args['upload_types']) AND !isset($args['upload_name'])){
					if($ext_found){
						array_push($tmp_results, $results[$x]);
					}
				}
			}
			$results = $tmp_results;
		}
	}else{
		if(is_array($results) AND !empty($results)){
			for ($x = 0; $x < count($results); $x++){
				$results[$x]['data'] = unserialize($results[$x]['data']);
			}
		}
	}
	
	ksort($results);
	array_values($results);

	if($upload_id != ''){
		$results = $results[0];
	}
	
	return $results;
}

// ninja_forms_delete_upload is located in includes/ajax.php.