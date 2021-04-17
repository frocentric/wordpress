<?php

function ninja_forms_uploads_activation(){
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "CREATE TABLE IF NOT EXISTS ".NINJA_FORMS_UPLOADS_TABLE_NAME." (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) DEFAULT NULL,
	  `form_id` int(11) NOT NULL,
	  `field_id` int(11) NOT NULL,
	  `data` longtext CHARACTER SET utf8 NOT NULL,
	  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";
	
	dbDelta($sql);

	$opt = get_option( 'ninja_forms_settings' );

	if( isset( $opt['version'] ) ){
		$current_version = $opt['version'];
	}else{
		$current_version = '';
	}
	
	$base_upload_url = wp_upload_dir();
	$base_upload_url = $base_upload_url['baseurl'].'/ninja-forms';
	$opt['base_upload_url'] = $base_upload_url;
	
	$base_upload_dir = wp_upload_dir();
	$base_upload_dir = $base_upload_dir['basedir'].'/ninja-forms';
	$opt['base_upload_dir'] = $base_upload_dir;

	if( !is_dir( $base_upload_dir ) ){
		mkdir( $base_upload_dir );
	}

	if( !is_dir( $base_upload_dir."/tmp/" ) ){
		mkdir( $base_upload_dir."/tmp/" );
	}
	
	if( !isset( $opt['upload_error'] ) ){
		$opt['upload_error'] = __( 'There was an error uploading your file.', 'ninja-forms-uploads' );
	}

	if( !isset( $opt['max_filesize'] ) ){
		$opt['max_filesize'] = 2;
	}

	$opt['uploads_version'] = NINJA_FORMS_UPLOADS_VERSION;

	update_option( 'ninja_forms_settings', $opt );
}