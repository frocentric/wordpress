<?php
/*
Plugin Name: S3-Compatible Configuration
Plugin URI: https://ingenyus.com
Description: Allows configuration of S3-compatible endpoints for S3 Uploads plugin.
Version: 1.0
Author: Gary McPherson
Author URI: https://ingenyus.com
License: GPLv2 or later
*/

// Modify S3 client parameters.
add_filter( 's3_uploads_s3_client_params', function ( $params ) {
	$params['endpoint'] = S3_UPLOADS_ENDPOINT;
	$params['use_path_style_endpoint'] = true;
	$params['debug'] = S3_UPLOADS_DEBUG; // Set to true if uploads are failing.
	return $params;
} );

// Modify S3 bucket URL.
add_filter( 's3_uploads_bucket_url', function ( $url ) {
    if( defined('S3_UPLOADS_ENDPOINT') ) {

		$bucket = strtok( S3_UPLOADS_BUCKET, '/' );
        $path   = substr( S3_UPLOADS_BUCKET, strlen( $bucket ) );
        $endpoint = substr( S3_UPLOADS_ENDPOINT, strpos( S3_UPLOADS_ENDPOINT, '://' ) + 3);
        
        $url = 'https://' . $bucket . '.' . $endpoint . $path;
    }

	return $url;
} );