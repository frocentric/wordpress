<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_Submissions {

	/**
	 * NF_FU_Integrations_NinjaForms constructor.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_custom_columns', array( $this, 'submission_table_row_value' ), 10, 2 );
		// Export value
		add_filter( 'ninja_forms_subs_export_field_value_' . NF_FU_File_Uploads::TYPE, array( $this, 'submission_export_value' ) );
		// TODO Edit sub value
		// TODO Front end editor edit sub
		// TODO PDF?
	}

	/**
	 * Display the upload file URL as an HTML link for each uploaded file to the submission.
	 * Submission table row td.
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return string
	 */
	public function submission_table_row_value( $value, $field ) {
		if ( NF_FU_File_Uploads::TYPE !== $field->get_setting( 'type' ) ) {
			return $value;
		}

		return $this->get_field_value_string( $value, true );
	}

	/**
	 * Format the export CSV value to a comma separated list of file URLs.
	 *
	 * @param mixed $field_value
	 *
	 * @return string
	 */
	public function submission_export_value( $field_value ) {
		return $this->get_field_value_string( $field_value );
	}

	/**
	 * Helper to get string of submitted file URLs.
	 *
	 * @param mixed $value
	 * @param bool  $link Wrap URL in HTML anchor.
	 *
	 * @return array|string
	 */
	protected function get_field_value_string( $value, $link = false ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$value = NF_File_Uploads()->normalize_submission_value( $value );

		$new_value = array();
		foreach ( $value as $upload_id => $file_url ) {
			$upload = NF_File_Uploads()->controllers->uploads->get( $upload_id );

			if ( false === $upload ) {
				continue;
			}

			if ( ! NF_File_Uploads()->controllers->uploads->file_exists( $upload->data ) ) {
				$new_value[] = __( 'File removed', 'ninja-forms-uploads' );
				continue;
			}

			$file_url = NF_File_Uploads()->controllers->uploads->get_file_url( $file_url, $upload->data );
			if ( $link ) {
				$new_value[] = sprintf( '<a href="%s" target="_blank">%s</a>', $file_url, $upload->user_file_name );
				continue;
			}

			$new_value[] = $file_url;
		}

		$glue = $link ? '<br>' : ',';

		return implode( $glue, $new_value );
	}
}