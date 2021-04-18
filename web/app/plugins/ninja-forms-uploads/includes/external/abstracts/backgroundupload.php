<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Abstracts_Backgroundupload
 */
abstract class NF_FU_External_Abstracts_Backgroundupload extends \NF_FU_VENDOR\WP_Background_Process {

	/**
	 * @var string
	 */
	protected $service;

	/**
	 * @var
	 */
	protected $upload_file;

	/**
	 * @var
	 */
	protected $external_path;

	/**
	 * @var
	 */
	protected $external_filename;

	/**
	 * @var
	 */
	protected $max_chunk_size;

	/**
	 * NF_FU_External_Abstracts_Backgroundupload constructor.
	 *
	 * @param string $service
	 */
	public function __construct( $service ) {
		$this->service = $service;
		$this->action = 'nf_' . $this->service . '_upload';
		parent::__construct();
	}

	/**
	 * @return bool|NF_FU_External_Abstracts_Service
	 */
	protected function get_service() {
		return NF_File_Uploads()->externals->get( $this->service );
	}

	/**
	 * @param array $item
	 */
	protected function set_props( $item ) {
		foreach ( $item as $key => $value ) {
			if ( $key === 'data' ) {
				continue;
			}

			$this->{$key} = $value;
		}
	}

	/**
	 * @param array $item
	 *
	 * @return bool|array
	 */
	protected function task( $item ) {
		$this->set_props( $item );

		if ( ! file_exists( $this->upload_file ) ) {
			$upload_id = isset( $item['data']['upload_id'] ) ? $item['data']['upload_id'] : null;

			return $this->task_error( $upload_id, sprintf( __( 'Ninja Forms File Upload: %s Upload. File does not exist %s', 'ninja-forms-uploads' ), $this->service, $this->upload_file ) );
		}

		$data = $this->upload_file( $item['data'] );

		if ( is_array( $data ) ) {
			$item['data'] = $data;

			return $item;
		}

		// Upload has been completed
		return false;
	}

	/**
	 * @param int|null $upload_id
	 * @param string   $message
	 *
	 * @return bool
	 */
	protected function task_error( $upload_id = null, $message = '' ) {
		error_log( $message );

		// This file cannot be uploaded to the service
		if ( $upload_id ) {
			$upload_data = $this->get_upload_data( $upload_id );
			unset( $upload_data['external_locations'][ $this->service ] );
			if ( empty( $upload_data['external_locations'] ) ) {
				unset( $upload_data['external_locations'] );
			}
			NF_File_Uploads()->model->update( $upload_id, $upload_data );
		}

		// Remove the job from the queue
		return false;
	}

	/**
	 * @param $upload_id
	 *
	 * @return mixed
	 */
	protected function get_upload_data( $upload_id ) {
		$upload = NF_File_Uploads()->model->get( $upload_id );

		return unserialize( $upload->data );
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function complete_upload( $data ) {
		$old_data = $this->get_upload_data( $data['upload_id'] );

		$data = array_merge( $data, $old_data );

		$data['external_path']     = $this->external_path;
		$data['external_filename'] = $this->external_filename;
		$data['upload_location']   = $this->service;
		$data['external_locations'][ $this->service ] = 1;

		if ( $this->maybe_delete_server_file( $data ) ) {
			unset( $data['defer_remove_from_server'] );
			$data['removed_from_server'] = true;
		}

		unset( $data['chunked'] );

		NF_File_Uploads()->model->update( $data['upload_id'], $data );
		
		return true;
	}

	/**
	 * Remove the local file from the server after all background uploads have taken place.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function maybe_delete_server_file( $data ) {
		if ( ! isset( $data['defer_remove_from_server'] ) || ! $data['defer_remove_from_server'] ) {
			return false;
		}

		foreach ( $data['external_locations'] as $location => $uploaded ) {
			if ( 0 == $uploaded ) {
				return false;
			}
		}

		if ( ! file_exists( $this->upload_file ) ) {
			return false;
		}

		$result = unlink( $this->upload_file );

		return $result;
	}

	abstract protected function chunked_upload_file( $service, $data );

	/**
	 * Upload the file either in a single request or multipart chunked upload.
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	protected function upload_file( $data ) {
		$service = $this->get_service();

		if ( isset( $data['chunked'] ) && $data['chunked'] ) {
			return $this->chunked_upload_file( $service, $data );
		}

		$service->set_upload_file( $data['file_path'] );
		$service->set_external_path( $data['external_path'] );
		$service->set_external_filename( $data['external_filename'] );

		$data = $service->upload_file( $data );

		if ( false === $data ) {
			return $data;
		}

		return $this->complete_upload( $data );
	}
}