<?php
// Adds an attachment type to the email notification dropdown
function nf_fu_add_attachment_type( $types ) {
	// Bail if we don't have a form id set.
	if ( ! isset ( $_REQUEST['form_id'] ) )
		return $types;

	foreach( Ninja_Forms()->form( $_REQUEST['form_id'] )->fields as $field_id => $field ) {
		if ( '_upload' == $field['type'] ) {
			$label = nf_get_field_admin_label( $field_id );
			$types[ 'file_upload_' . $field_id ] = $label . ' - ID: ' . $field_id;
		}
	}
	
	return $types;
}

add_filter( 'nf_email_notification_attachment_types', 'nf_fu_add_attachment_type' );

// Add our attachment to the email notification if the box was checked.
function nf_fu_attach_files( $files, $id ) {
	global $ninja_forms_processing;

	foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
		$type = $ninja_forms_processing->get_field_setting( $field_id, 'type' );
		if ( '_upload' == $type && 1 == Ninja_Forms()->notification( $id )->get_setting( 'file_upload_' . $field_id ) ) {
			if ( is_array( $user_value ) ) {
				$file_urls = array();
				foreach ( $user_value as $key => $file ) {
					if ( ! isset ( $file['file_path'] ) )
						continue;
					
					$files[] = $file['file_path'] . $file['file_name'];
				}
			}
		}
	}

	return $files;
}

add_filter( 'nf_email_notification_attachments', 'nf_fu_attach_files', 10, 2 );