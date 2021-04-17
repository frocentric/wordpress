<?php

return apply_filters( 'ninja_forms_uploads_settings_s3', array(
	'amazon_s3_access_key' => array(
		'id'      => 'amazon_s3_access_key',
		'type'    => 'textbox',
		'default' => '',
		'label'   => __( 'Access Key', 'ninja-forms-uploads' ),
	),
	'amazon_s3_secret_key' => array(
		'id'      => 'amazon_s3_secret_key',
		'type'    => 'textbox',
		'default' => '',
		'label'   => __( 'Secret Key', 'ninja-forms-uploads' ),
	),
	'amazon_s3_bucket_name' => array(
		'id'      => 'amazon_s3_bucket_name',
		'type'    => 'textbox',
		'default' => '',
		'label'   => __( 'Bucket Name', 'ninja-forms-uploads' ),
	),
	'amazon_s3_file_path' => array(
		'id'      => 'amazon_s3_file_path',
		'type'    => 'textbox',
		'default' => 'ninja-forms/',
		'label'   => __( 'File Path', 'ninja-forms-uploads' ),
		'desc'  => __( 'The default file path in the bucket where the file will be uploaded to.', 'ninja-forms-uploads' )
	),
) );