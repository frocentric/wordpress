<?php

use NF_FU_VENDOR\Google_Http_MediaFileUpload;
use NF_FU_VENDOR\Google_Service_Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Services_Googledrive_Backgroundupload
 */
class NF_FU_External_Services_Googledrive_Backgroundupload extends NF_FU_External_Abstracts_Backgroundupload {

	/**
	 * @var int
	 */
	protected $max_chunk_size = 5242880;

	/**
	 * @param NF_FU_External_Services_Googledrive_Service $service
	 * @param array                                       $data
	 *
	 * @return array|bool
	 */
	public function chunked_upload_file( $service, $data ) {
		$file = $service->create_drive_file( $this->external_filename, $this->external_path );

		$client = $service->get_client();
		$client->setDefer( true );

		$drive = $service->drive( $client );

		$request = $drive->files->create( $file );

		$mimeType = wp_check_filetype( $this->upload_file )['type'];

		$media = new Google_Http_MediaFileUpload( $client, $request, $mimeType, null, true, $this->max_chunk_size );

		if ( ! isset( $data['drive_file_size'] ) ) {
			$data['drive_file_size'] = NF_FU_Helper::get_file_size( $this->upload_file );
		}

		$media->setFileSize( $data['drive_file_size'] );

		if ( isset( $data['drive_media_resume_uri'] ) ) {
			try {
				$media->resume( $data['drive_media_resume_uri'] );
			} catch ( Google_Service_Exception $exception ) {
				unset( $data['drive_media_resume_uri']  );
				unset( $data['drive_file_pointer']  );

				return $data;
			}
		}

		$file = fopen( $this->upload_file, 'r' );

		if ( isset( $data['drive_file_pointer'] ) ) {
			fseek( $file, $data['drive_file_pointer'] );
		}

		$status = false;
		while ( ! $status && ! feof( $file ) ) {
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				// Batch limits reached.
				$data['drive_file_pointer']     = ftell( $file );
				$data['drive_media_resume_uri'] = $media->getResumeUri();

				fclose( $file );
				$client->setDefer( false );

				return $data;
			}

			$chunk  = fread( $file, $this->max_chunk_size );
			$status = $media->nextChunk( $chunk );

			gc_collect_cycles();
		}

		fclose( $file );
		$client->setDefer( false );

		$data['file_id'] = $status->id;

		unset( $data['drive_file_pointer'] );
		unset( $data['drive_media_resume_uri'] );
		unset( $data['drive_file_size'] );

		return $this->complete_upload( $data );
	}


}