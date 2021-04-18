<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_PdfSubmissions_PdfSubmissions {

	public function __construct() {
		add_filter( 'ninja_forms_uploads_mergetag_value_field', array( $this, 'format_field_for_mergetag' ) );
		add_filter( 'ninja_forms_submission_pdf_name', array( $this, 'update_all_mergetags' ), 10, 2 );
	}

	/**
	 * Ensure File Upload mergetags are used outside of the submission process
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public function format_field_for_mergetag( $field ) {
		if ( isset( $field['files'] ) ) {
			return $field;
		}

		$uploads = NF_File_Uploads()->controllers->uploads->get_field_uploads_by_submission_id( $field['id'], $_GET['sub_id'] );

		$files = array();
		foreach ( $uploads as $upload ) {
			$files[]['data']['upload_id'] = $upload['id'];
		}

		if ( empty( $files ) ) {
			return $field;
		}

		$field['files'] = $files;

		return $field;
	}

	/**
	 * Update all File Upload mergetag variants when exporting a PDF
	 *
	 * @param string $filename
	 * @param int $submission_id
	 *
	 * @return string
	 */
	public function update_all_mergetags( $filename, $submission_id ) {
		if ( ! isset( $_GET['sub_id'] ) ) {
			// We are in the submission process
			return $filename;
		}

		$submission = Ninja_Forms()->form()->get_sub( $submission_id );
		$form_id    = $submission->get_form_id();

		$form_fields = Ninja_Forms()->form( $form_id )->get_fields();
		foreach ( $form_fields as $field ) {
			if ( NF_FU_File_Uploads::TYPE !== $field->get_setting( 'type' ) ) {
				continue;
			}

			$field = array( 'id' => $field->get_id() );

			$field = $this->format_field_for_mergetag( $field );

			$field = NF_File_Uploads()->mergetags->normalize_field( $field, $form_id );

			// Update Mergetags
			NF_File_Uploads()->mergetags->update_mergetags( $field, NF_FU_Integrations_NinjaForms_MergeTags::get_default_tags() );
		}

		return $filename;
	}
}