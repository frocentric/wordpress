<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters( 'ninja_forms_uploads_settings_external', array(
	'external_public_url' => array(
		'id'    => 'external_public_url',
		'type'  => 'checkbox',
		'label' => __( 'Use Public URL', 'ninja-forms-uploads' ),
		'desc'  => __( 'URLs to files uploaded to external services are by default only accessible to logged in users of the site. Enable this to make them publicly viewable.', 'ninja-forms-uploads' ),
	),
) );
