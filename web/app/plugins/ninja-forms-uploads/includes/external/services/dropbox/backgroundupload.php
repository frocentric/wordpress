<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Services_Dropbox_Backgroundupload
 */
class NF_FU_External_Services_Dropbox_Backgroundupload extends NF_FU_External_Abstracts_Backgroundupload {

	/**
	 * @var int
	 */
	protected $max_chunk_size = 5242880;

	/**
	 * @param NF_FU_External_Services_Dropbox_Service $service
	 * @param array                                   $data
	 *
	 * @return array|bool
	 */
	public function chunked_upload_file( $service, $data ) {
		$file = fopen( $this->upload_file, 'r' );

		if ( isset( $data['file_pointer'] ) ) {
			fseek( $file, $data['file_pointer'] );
		}

		while ( ! feof( $file ) ) {
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				// Batch limits reached.
				$data['file_pointer'] = ftell( $file );

				fclose( $file );

				return $data;
			}

			$chunk = fread( $file, $this->max_chunk_size );

			if ( ! isset( $data['session_cursor'] ) ) {
				$data['session_cursor'] = $service->get_client()->upload_session_start( $chunk );
			} else {
				$data['session_cursor'] = $service->get_client()->upload_session_append( $data['session_cursor'], $chunk );
			}

			gc_collect_cycles();
		}

		fclose( $file );

		$path = $this->external_path . $this->external_filename;
		$path = '/' . ltrim( $path, '/' );
		$response = $service->get_client()->upload_session_finish( $data['session_cursor'], $path, 'overwrite' );
		if ( empty( $response ) || isset( $response->error_summary ) ) {
			$error = isset( $response->error_summary ) ? $response->error_summary : 'unknown';

			return $this->task_error( $data['upload_id'], sprintf( 'Ninja Forms File Upload: %s Upload. File failed to upload (%s) %s', $this->service, $error, $this->upload_file ) );
		}

		unset( $data['file_pointer'] );
		unset( $data['session_cursor'] );

		return $this->complete_upload( $data );
	}
}