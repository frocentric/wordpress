<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_SaveProgress_SaveProgress {

	public function __construct() {
		add_action( 'ninja-forms-save-progress-save-created', array( $this, 'maybe_persist_file_upload_files' ), 10, 3 );
		add_action( 'ninja-forms-save-progress-save-updated', array( $this, 'maybe_persist_file_upload_files' ), 10, 3 );
	}

	/**
	 * Remove the scheduled cron to delete the temporary file uploads on the Save Progress save hooks.
	 * 
	 * @param bool  $saved
	 * @param int   $form_id
	 * @param array $save_data
	 */
	public function maybe_persist_file_upload_files( $saved, $form_id, $save_data ) {
		foreach ( $save_data as $field ) {
			if ( ! isset ( $field['files'] ) || ! is_array( $field['files'] ) ) {
				continue;
			}

			foreach ( $field['files'] as $file ) {
				$new_tmp_file_path = NF_File_Uploads()->controllers->uploads->get_path( $file['tmp_name'], true );
				wp_clear_scheduled_hook( 'nf_fu_delete_temporary_file', array( $new_tmp_file_path ) );
			}
		}
	}
}