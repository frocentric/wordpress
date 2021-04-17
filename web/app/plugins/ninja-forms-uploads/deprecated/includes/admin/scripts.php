<?php
add_action( 'admin_init', 'ninja_forms_uploads_admin_js' );

function ninja_forms_uploads_admin_js(){
	wp_enqueue_script( 'ninja-forms-uploads-admin',
		NINJA_FORMS_UPLOADS_URL .'/js/min/ninja-forms-uploads-admin.min.js',
		array( 'jquery', 'ninja-forms-admin' ) );
}