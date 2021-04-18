<?php
return apply_filters( 'ninja_forms_uploads_settings_google_drive', array(
	'google_drive_connect'   => array(
		'id'               => 'google_drive_connect',
		'type'             => 'callback',
		'label'            => __( 'Connect to Google Drive', 'ninja-forms-uploads' ),
		'desc'             => '',
		'display_function' => array( $this, 'connect_url' ),
	),
	'google_drive_file_path' => array(
		'id'      => 'google_drive_file_path',
		'type'    => 'textbox',
		'label'   => __( 'File Path', 'ninja-forms-uploads' ),
		'desc'    => __( 'Custom directory for the files to be uploaded to in Google Drive', 'ninja-forms-uploads' ),
		'default' => '',
	),
) );