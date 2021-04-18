<?php

function nf_fu_export_filter( $user_value, $field_id ) {
	$field = ninja_forms_get_field_by_id( $field_id );

	if ( $field['type'] == '_upload' ) {
		if ( is_array( $user_value ) ) {
			$user_value = NF_File_Uploads()->normalize_submission_value( $user_value );
			$file_urls = array();
			foreach ( $user_value as $key => $file ) {
				if ( ! isset ( $file['file_url'] ) )
					continue;
				$file_url = ninja_forms_upload_file_url( $file );
				$file_urls[] = apply_filters( 'nf_fu_export_file_url', $file_url );
			}
			$user_value = apply_filters( 'nf_fu_export_files', $file_urls );
		}
	}

	return $user_value;
}

function nf_fu_add_export_filter() {
	if ( ! nf_fu_pre_27() ) {
		add_filter( 'nf_subs_export_pre_value', 'nf_fu_export_filter', 10, 2 );
	}
}

add_action( 'init', 'nf_fu_add_export_filter' );