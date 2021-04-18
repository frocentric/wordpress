<?php

use Aws\S3\S3Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Services_S3_Backgroundupload
 */
class NF_FU_External_Services_S3_Backgroundupload extends NF_FU_External_Abstracts_Backgroundupload {

	/**
	 * @var int
	 */
	protected $max_chunk_size = 5242880;

	/**
	 * @var string
	 */
	protected $bucket;

	/**
	 * @param NF_FU_External_Services_S3_Service $service
	 * @param array                              $data
	 *
	 * @return array|bool
	 */
	public function chunked_upload_file( $service, $data ) {
		$service = $this->get_service();
		$region = $service->get_region();
		$bucket = $this->bucket;
		$s3 = $service->get_client( $region );

		if ( ! isset( $data['s3_upload_id'] ) ) {
			$data['s3_upload_id'] = $this->start_chunked_upload( $s3, $bucket );
		}

		$filename = $this->upload_file;
		$key = $this->external_path . $this->external_filename;

		// Upload the file in parts.
		try {
			$partNumber = isset( $data['s3_part_number'] ) ? $data['s3_part_number'] : 1;
			$parts      = isset( $data['s3_parts'] ) ? $data['s3_parts'] : array();

			$file = fopen( $filename, 'r' );

			if ( isset( $data['s3_file_pointer'] ) ) {
				fseek( $file, $data['s3_file_pointer'] );
			}

			while ( ! feof( $file ) ) {
				if ( $this->time_exceeded() || $this->memory_exceeded() ) {
					// Batch limits reached.
					$data['s3_file_pointer'] = ftell( $file );

					fclose( $file );

					return $data;
				}

				$result = $s3->uploadPart( [
					'Bucket'     => $bucket,
					'Key'        => $key,
					'UploadId'   => $data['s3_upload_id'],
					'PartNumber' => $partNumber,
					'Body'       => fread( $file, $this->max_chunk_size ),
				] );

				$parts['Parts'][ $partNumber ] = [
					'PartNumber' => $partNumber,
					'ETag'       => $result['ETag'],
				];

				$partNumber ++;

				$data['s3_part_number'] = $partNumber;
				$data['s3_parts'] = $parts;
				gc_collect_cycles();
			}
			fclose( $file );
		} catch ( S3Exception $e ) {
			$result = $s3->abortMultipartUpload( [
				'Bucket'   => $bucket,
				'Key'      => $key,
				'UploadId' => $data['s3_upload_id'],
			] );

			return $data;
		}

		// Complete the multipart upload.
		$result = $s3->completeMultipartUpload( [
			'Bucket'          => $bucket,
			'Key'             => $key,
			'UploadId'        => $data['s3_upload_id'],
			'MultipartUpload' => $data['s3_parts'],
		] );

		unset( $data['s3_upload_id'] );
		unset( $data['s3_file_pointer'] );
		unset( $data['s3_part_number'] );
		unset( $data['s3_parts'] );

		$data['bucket'] = $bucket;
		$data['region'] = $region;

		return $this->complete_upload( $data );
	}

	/**
	 * @param S3Client $s3
	 *
	 * @param  string  $bucket
	 *
	 * @return mixed
	 */
	protected function start_chunked_upload( $s3, $bucket ) {
		$mimeType = wp_check_filetype( $this->upload_file )['type'];

		$result = $s3->createMultipartUpload( [
			'Bucket'      => $bucket,
			'Key'         => $this->external_path . $this->external_filename,
			'ContentType' => $mimeType,
			'ACL'         => apply_filters( 'ninja_forms_uploads_s3_acl', NF_FU_External_Services_S3_Service::ACL_PRIVATE ),
		] );

		return $result['UploadId'];
	}


}