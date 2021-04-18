<?php
return array(
	'progress_bar_styles' => array(
		'name' => 'progress_bar_styles',
		'type' => 'fieldset',
		'label' => __( 'Progress Bar Styles', 'ninja-forms-uploads' ),
		'width' => 'full',
		'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element .nf-fu-progress .nf-fu-progress-bar',
	),

	'progress_bar_background_styles' => array(
		'name' => 'progress_bar_background_styles',
		'type' => 'fieldset',
		'label' => __( 'Progress Bar Background Styles', 'ninja-forms-uploads' ),
		'width' => 'full',
		'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element .nf-fu-progress',
	),

	'upload_files_list_styles' => array(
		'name' => 'upload_files_list_styles',
		'type' => 'fieldset',
		'label' => __( 'Uploaded Files List Styles', 'ninja-forms-uploads' ),
		'width' => 'full',
		'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element .files_uploaded',
	),
	'upload_file_delete_styles' => array(
		'name' => 'upload_file_delete_styles',
		'type' => 'fieldset',
		'label' => __( 'Uploaded File Delete Link Styles', 'ninja-forms-uploads' ),
		'width' => 'full',
		'selector' => '.nf-form-content .nf-field-container #nf-field-{ID}-wrap .nf-field-element .files_uploaded p a.delete',
	),

);