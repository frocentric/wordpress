<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Admin_Controllers_Uploads {

	/**
	 * @var string
	 */
	protected static $base_dir;

	/**
	 * @var string
	 */
	protected static $base_url;

	/**
	 * @var string
	 */
	protected static $tmp_dir;

	/**
	 * NF_FU_Admin_Controllers_Uploads constructor.
	 */
	public function __construct() {
		add_action( 'ninja_forms_after_submission', array( $this, 'remove_files_from_server') );
		add_action( 'ninja_forms_after_submission', array( $this, 'record_submission_id'), 11 );
	}

	/**
	 * Remove the file from the server if the field is not to be saved to the server.
	 * This happens on the latest hook possible after actions have been run.
	 *
	 * @param array $data
	 */
	public function remove_files_from_server( $data ) {
		if ( ! isset( $data['fields'] ) || ! is_array( $data['fields'] ) ) {
			return;
		}

		foreach( $data['fields'] as $field ) {
			if ( NF_FU_File_Uploads::TYPE !== $field['type'] ) {
				continue;
			}

			if ( ! isset( $field['save_to_server'] ) ) {
				continue;
			}

			if ( "1" == $field['save_to_server'] ) {
				continue;
			}

			if ( empty( $field['value'] ) ) {
				continue;
			}

			foreach ( $field['value'] as $upload_id => $url ) {
				$upload = $this->get( $upload_id );

				$file_path = $upload->file_path;

				if ( $this->is_uploading_to_external( $upload ) ) {
					// File being uploaded to external services in the background
					// Do not delete, the background jobs will take care of deleting the local file

					continue;
				}

				if ( ! file_exists( $file_path ) ) {
					continue;
				}

				// Delete local file
				$result = unlink( $file_path );

				if ( $result ) {
					$upload_data = $upload->data;

					$upload_data['removed_from_server'] = true;

					NF_File_Uploads()->model->update( $upload_id, $upload_data );
				}
			}
		}
	}

	/**
	 * Check the file is being uploaded to an external service
	 *
	 * @param $upload
	 *
	 * @return bool
	 */
	protected function is_uploading_to_external( $upload ) {
		if ( ! isset( $upload->external_locations ) || ! is_array( $upload->external_locations ) ) {
			return false;
		}

		foreach ( $upload->external_locations as $location => $uploaded ) {
			if ( 0 == $uploaded ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the base upload directory
	 *
	 * @return string
	 */
	public function get_base_dir() {
		if ( is_null( self::$base_dir ) ) {
			$base_upload_dir = wp_upload_dir();
			$base_upload_dir = $base_upload_dir['basedir'] . '/ninja-forms';

			wp_mkdir_p( $base_upload_dir );

			self::$base_dir = $base_upload_dir;
		}

		return self::$base_dir;
	}

	/**
	 * Get the URL base upload directory
	 *
	 * @return string
	 */
	public function get_base_url() {
		if ( is_null( self::$base_url ) ) {
			$base_upload_url = wp_upload_dir();
			$base_upload_url = $base_upload_url['baseurl'] . '/ninja-forms';

			self::$base_url = $base_upload_url;
		}

		return self::$base_url;

	}

	/**
	 * Get the temp upload directory
	 *
	 * @return string
	 */
	public function get_temp_dir() {
		if ( is_null( self::$tmp_dir ) ) {
			$base_upload_dir = $this->get_base_dir();
			$tmp_upload_dir  = $base_upload_dir . '/tmp';
			$tmp_upload_dir  = apply_filters( 'ninja_forms_uploads_tmp_dir', $tmp_upload_dir );

			wp_mkdir_p( $tmp_upload_dir );
			$this->maybe_create_tmp_htaccess( $tmp_upload_dir );

			self::$tmp_dir = $tmp_upload_dir;
		}

		return self::$tmp_dir;
	}

	/**
	 * Copy .htaccess file to tmp directory for security
	 * https://github.com/blueimp/jQuery-File-Upload/wiki/Security#php
	 *
	 * @param string $tmp_upload_dir
	 */
	protected function maybe_create_tmp_htaccess( $tmp_upload_dir ) {
		$dest = $tmp_upload_dir . '/.htaccess';
		if ( file_exists( $dest ) ) {
			return;
		}

		$source = dirname( NF_File_Uploads()->plugin_file_path ) . '/includes/.htaccess.txt';

		@copy( $source, $dest );
	}

	/**
	 * Get the file path for the temp file
	 *
	 * @param string $filename
	 * @param bool   $temp Use temp path
	 *
	 * @return string
	 */
	public function get_path( $filename = '', $temp = false ) {
		$file_path = $temp ? $this->get_temp_dir() : $this->get_base_dir();

		$field_id  = isset( $this->field_id ) ? $this->field_id : null;
		$file_path = apply_filters( 'ninja_forms_uploads_dir', $file_path, $field_id );

		return trailingslashit( $file_path ) . $filename;
	}

	/**
	 * Get the URL of a file
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function get_url( $filename ) {
		$field_id = isset( $this->field_id ) ? $this->field_id : null;
		$file_url = apply_filters( 'ninja_forms_uploads_url', $this->get_base_url(), $field_id );

		return trailingslashit( $file_url ) . $filename;
	}

	/**
	 * Get a file upload from the table
	 *
	 * @param int $id
	 *
	 * @return object|false
	 */
	public function get( $id ) {
		$upload = NF_File_Uploads()->model->get( $id );

		if ( is_null( $upload ) ) {
			return false;
		}

		$data = unserialize( $upload->data );

		foreach ( $data as $key => $value ) {
			$upload->$key = $value;
		}

		$upload->data = $data;

		return $upload;
	}

	/**
	 * Get the file URL for an upload
	 * 
	 * @param string $url
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_file_url( $url, $data ) {
		return apply_filters( 'ninja_forms_uploads_file_url', $url, $data );
	}

	/**
	 * Create attachment in media library from the file
	 *
	 * @param string      $file
	 * @param null|string $file_name
	 *
	 * @return array
	 */
	public function create_attachment( $file, $file_name = null ) {
		if ( is_null( $file_name ) ) {
			$file_name = basename( $file );
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$wp_filetype = wp_check_filetype( $file_name );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		$attach_data['ninja_forms_upload_field'] = true;

		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	/**
	 * Check if the uploaded file exists.
	 *
	 * @param $upload_data
	 *
	 * @return bool
	 */
	public function file_exists( $upload_data ) {
		if ( 'server' !== $upload_data['upload_location'] ) {
			// For now assume external files still exist
			return true;
		}

		if ( isset( $upload_data['removed_from_server'] ) && $upload_data['removed_from_server'] ) {
			return false;
		}

		return file_exists( $upload_data['file_path']);
	}

	/**
	 * Save the submission ID for each upload object.
	 *
	 * @param array $data
	 */
	public function record_submission_id( $data ) {
		if ( ! isset( $data['actions']['save']['sub_id'] ) ) {
			return;
		}

		if ( ! isset( $data['fields'] ) || ! is_array( $data['fields'] ) ) {
			return;
		}

		$sub_id = $data['actions']['save']['sub_id'];

		foreach ( $data['fields'] as $field ) {
			if ( NF_FU_File_Uploads::TYPE !== $field['type'] ) {
				continue;
			}

			if ( empty( $field['value'] ) ) {
				continue;
			}

			foreach ( $field['value'] as $upload_id => $url ) {
				$upload                = $this->get( $upload_id );
				$upload_data           = $upload->data;
				$upload_data['sub_id'] = $sub_id;

				NF_File_Uploads()->model->update( $upload_id, $upload_data );
			}
		}
	}

	/**
	 * Get all uploads for a specific field and submission
	 *
	 * @param int $field_id
	 * @param int $submission_id
	 *
	 * @return array
	 */
	public function get_field_uploads_by_submission_id( $field_id, $submission_id ) {
		global $wpdb;

		$where = $wpdb->prepare( 'WHERE field_id = %1$d AND data like \'%%%"sub_id";i:%2$d%%%\'', $field_id, $submission_id );

		return NF_File_Uploads()->model->fetch( $where );
	}
}