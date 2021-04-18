<?php
/**
 * Update the "attach file to this email" settings on any "Admin" email notifications we may have.
 *
 * @since 1.3.8
 * @return void
*/
function nf_fu_upgrade_settings() {
	// Check to see if we've already done this.
	$updated = get_option( 'nf_convert_upload_settings_complete', false );

    if( ! defined( 'NINJA_FORMS_VERSION' ) ) return;

	if ( $updated || version_compare( NINJA_FORMS_VERSION, '2.8', '<' ) )
		return;

	$notifications = nf_get_all_notifications();
	// Make sure that there are some notifications.
	if ( ! is_array ( $notifications ) )
		return;

	// Loop through our notifications and see if any of them were "admin emails"
	foreach ( $notifications as $n ) {
		if ( Ninja_Forms()->notification( $n['id'] )->get_setting( 'admin_email' ) ) {
			// Grab our form id so that we can loop over our fields.
			$form_id = Ninja_Forms()->notification( $n['id'] )->form_id;
			// Loop over our form fields. If we find an upload field, see if the option is checked.
			foreach ( Ninja_Forms()->form( $form_id )->fields as $field_id => $field ) {
				if ( '_upload' == $field['type'] && isset ( $field['data']['email_attachment'] ) && $field['data']['email_attachment'] == 1 ) {
					Ninja_Forms()->notification( $n['id'] )->update_setting( 'file_upload_' . $field_id, 1 );
				}
			}
		}
	}
	update_option( 'nf_convert_upload_settings_complete', true );
}

add_action( 'admin_init', 'nf_fu_upgrade_settings' );
