<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_Zapier_Zapier {

	public function __construct() {
		add_filter( 'nf_zapier_fu_field_value', array( $this, 'external_field_value' ), 10, 2 );
	}

	/**
	 * Ensure the Zapier addon uses external URLs where necessary.
	 *
	 * @param array $value
	 * @param array $field
	 *
	 * @return array
	 */
	public function external_field_value( $value, $field ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		foreach ( $value as $upload_id => $url ) {
			$data = $this->get_field_data_from_upload_id( $upload_id, $field['files'] );
			if ( ! $data ) {
				continue;
			}

			$value[ $upload_id ] = NF_File_Uploads()->controllers->uploads->get_file_url( $url, $data );
		}

		return $value;
	}

	protected function get_field_data_from_upload_id( $upload_id, $files ) {
		foreach ( $files as $file ) {
			if ( ! isset( $file['data'] ) ) {
				continue;
			}

			if ( $file['data']['upload_id'] == $upload_id ) {
				return $file['data'];
			}
		}

		return false;
	}
}