<?php

//Add the ajax listener for deleting the upload
add_action('wp_ajax_ninja_forms_delete_upload', 'ninja_forms_delete_upload');
function ninja_forms_delete_upload($upload_id = ''){
	global $wpdb;
	if(isset($_REQUEST['upload_id']) AND $upload_id == ''){
		$upload_id = $_REQUEST['upload_id'];
	}

	$args = array('id' => $upload_id);
	$upload_row = ninja_forms_get_uploads($args);
	$upload_data = $upload_row['data'];
	if(is_array($upload_data) AND isset($upload_data['file_path'])){
		$file = $upload_data['file_path'].$upload_data['file_name'];
		if(file_exists($file)){
			unlink($file);
		}
	}

	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_UPLOADS_TABLE_NAME." WHERE id = %d", $upload_id));	
	if(isset($_REQUEST['upload_id'])){
		die();	
	}
}