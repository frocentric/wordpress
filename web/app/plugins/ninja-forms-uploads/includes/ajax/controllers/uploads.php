<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_AJAX_Controllers_Uploads extends NF_Abstracts_Controller {

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * @var int
	 */
	protected $field_id;

	/**
	 * @var \NF_Database_Models_Field
	 */
	protected $field;

	/**
	 * @var array
	 */
	protected $content_range;

	/**
	 * Initialize
	 */
	public function init() {
		add_action( 'wp_ajax_nf_fu_upload', array( $this, 'handle_upload' ) );
		add_action( 'wp_ajax_nopriv_nf_fu_upload', array( $this, 'handle_upload' ) );
		add_action( 'wp_ajax_nf_fu_get_new_nonce', array( $this, 'get_new_nonce' ) );
		add_action( 'wp_ajax_nopriv_nf_fu_get_new_nonce', array( $this, 'get_new_nonce' ) );
		add_action( 'nf_fu_delete_temporary_file', array( $this, 'delete_temporary_file' ), 10, 1 );
	}

	/**
	 * @param string $key
	 *
	 * @return bool|int
	 */
	protected function get_posted_id( $key = 'field' ) {
		$id = filter_input( INPUT_POST, $key . '_id' );

		if ( empty( $id ) ) {
			return false;
		}

		/* Render Instance Fix */
		if ( strpos( $id, '_' ) ) {
			list( $id ) = explode( '_', $id );
		}

		if ( ! filter_var( $id, FILTER_VALIDATE_INT ) ) {
			return false;
		}

		return $id;
	}

	/**
	 * @return bool|int
	 */
	protected function get_form_id() {
		if ( $this->form_id ) {
			return $this->form_id;
		}

		$form_id = $this->get_posted_id( 'form' );

		if ( ! $form_id ) {
			return false;
		}

		$this->form_id = $form_id;

		return $form_id;
	}

	/**
	 * @return bool|int
	 */
	protected function get_field_id() {
		if ( $this->field_id ) {
			return $this->field_id;
		}

		$field_id = $this->get_posted_id( 'field' );

		if ( ! $field_id ) {
			return false;
		}

		$this->field_id = $field_id;

		return $field_id;
	}

	/**
	 * @return NF_Database_Models_Field|object
	 */
	protected function get_field() {
		if ( $this->field ) {
			return $this->field;
		}

		$field = Ninja_Forms()->form( $this->get_form_id() )->field( $this->get_field_id() )->get();

		$this->field = $field;

		return $field;
	}

	/**
	 * Process the upload of files
	 */
	public function handle_upload() {
		$field_id = $this->get_field_id();

		if ( ! $field_id ) {
			$this->_errors[] = __( 'No field ID supplied', 'ninja-forms-uploads' );
			$this->_respond();
		}

		$result = check_ajax_referer( 'nf-file-upload-' . $field_id, 'nonce', false );
		if ( false === $result ) {
			$this->_errors[] = __( 'Nonce error', 'ninja-forms-uploads' );
			$this->_respond();
		}

		$form_id = $this->get_form_id();

		if ( ! $form_id ) {
			$this->_errors[] = __( 'No form ID supplied', 'ninja-forms-uploads' );
			$this->_respond();
		}

		$field_instance_id = filter_input( INPUT_POST, 'field_id' );

		$files_key = 'files-' . $field_instance_id;
		if ( ! isset( $_FILES[ $files_key ] ) ) {
			$this->_errors[] = $this->code_to_message( '' );
			$this->_respond();
		}

		$this->_data['files'] = $this->_prepare( $_FILES[ $files_key ] );

		$this->_process();
		$this->_respond();
	}

	protected function get_filename_from_chunk() {
		$content_disposition_header = $this->get_server_var('HTTP_CONTENT_DISPOSITION');
		if ( empty( $content_disposition_header ) ) {
			return false;
		}
		$file_name = rawurldecode( preg_replace( '/(^[^"]+")|("$)/', '', $content_disposition_header ) );
		$file_name = rtrim( $file_name, '\\' );

		return $file_name;
	}

	protected function get_file_size_from_chunk() {
		$content_range = $this->get_content_range();
		if ( empty( $content_range ) || ! isset( $content_range[3] ) ) {
			return false;
		}

		return $content_range[3];
	}

	protected function get_content_range() {
		if ( $this->content_range ) {
			return $this->content_range;
		}

		// Content-Range: bytes 0-524287/2000000
		$content_range_header = $this->get_server_var( 'HTTP_CONTENT_RANGE' );
		if ( empty( $content_range_header ) ) {
			return array();
		}
		$this->content_range = preg_split( '/[^0-9]+/', $content_range_header );;

		return $this->content_range;
	}

	/**
	 * AJAX Handler for generating a new nonce and sending back to the form.
	 */
	public function get_new_nonce() {
		$field_id = $this->get_field_id();

		if ( ! $field_id ) {
			wp_send_json_error();
		}

		$nonce_data = NF_File_Uploads()->createFieldNonce( $field_id );

		wp_send_json_success( $nonce_data );
	}

	/**
	 * Delete temp file
	 *
	 * @param $file_path
	 */
	public function delete_temporary_file( $file_path ) {
		if ( file_exists( $file_path ) ) {
			@unlink( $file_path );
		}
	}

	/**
	 * Prepare the array of files to turn the array into a more useful structure
	 *
	 * @param array $files
	 *
	 * @return array
	 */
	protected function _prepare( $files ) {
		$clean_files = array();
		if ( ! is_array( $files['name'] ) ) {
			return array( $files );
		}

		$file_keys   = array_keys( $files );
		$file_count  = count( $files['name'] );

		for ( $i = 0; $i < $file_count; $i++ ) {
			foreach ( $file_keys as $key ) {
				$clean_files[ $i ][ $key ] = $files[ $key ][ $i ];
			}
		}

		return $clean_files;
	}

	protected function get_server_var( $id ) {
		return @$_SERVER[ $id ];
	}

	/**
	 * Process each file
	 *
	 * Temporarily store the uploaded files until the form is submitted
	 */
	protected function _process() {
		foreach ( $this->_data['files'] as $key => $file ) {

			$file_name = $this->get_filename_from_chunk();
			if ( $file_name ) {
				$file['name'] = $file_name;
			}

			$file_size = $this->get_file_size_from_chunk();
			if ( $file_size ) {
				$file['size'] = $file_size;
			}

			if ( false === $this->_validate( $file ) ) {
				unset( $this->_data['files'][ $key ] );
				@unlink( $file['tmp_name'] );
				continue;
			}

			$file_key     = strtolower( str_replace( array( ' ', '.' ), '_', $file['name'] ) );
			$new_tmp_name = filter_input( INPUT_POST, $file_key );
			if ( empty( $new_tmp_name ) ) {
				$new_tmp_name = $this->get_temp_filename( $file['name'] );
			}

			$new_tmp_file_path = NF_File_Uploads()->controllers->uploads->get_path( $new_tmp_name, true );

			$append_file = $this->get_content_range() && is_file( $new_tmp_file_path ) && $file['size'] > NF_FU_Helper::get_file_size( $new_tmp_file_path );

			if ( $append_file ) {
				$result = file_put_contents( $new_tmp_file_path, fopen( $file['tmp_name'], 'r' ), FILE_APPEND );
			} else {
				$result = move_uploaded_file( $file['tmp_name'], $new_tmp_file_path );
			}

			if ( false === $result ) {
				unset( $this->_data['files'][ $key ] );
				$this->_errors[] = __( 'Unable to move uploaded temp file', 'ninja-forms-uploads' );

				continue;
			}

			// Schedule a clean up of the file if the form doesn't get submitted
			wp_schedule_single_event( apply_filters( 'ninja_forms_uploads_temp_file_delete_time', time() + HOUR_IN_SECONDS ), 'nf_fu_delete_temporary_file', array( $new_tmp_file_path ) );

			$this->_data['files'][ $key ]['tmp_name'] = $new_tmp_name;
			$this->_data['files'][ $key ]['new_tmp_key'] = $file_key;
		}
	}

	/**
	 * Check for max_filesize in the field settings
	 *
	 * @param int $size
	 *
	 * @return bool
	 */
	protected function validate_max_file_size( $size, $total_size ) {
		$max_file_size_mb = $this->get_field()->get_setting( 'max_file_size' );

		if ( ! $max_file_size_mb ) {
			// Use the global setting
			$max_file_size_mb = NF_File_Uploads()->controllers->settings->get_max_file_size_mb();
		}

		if ( empty( $max_file_size_mb ) ) {
			// No maximum
			return true;
		}

		$max_file_size = NF_File_Uploads()->controllers->settings->file_size_bytes_from_mb( $max_file_size_mb );
		if ( $size > $max_file_size || $total_size > $max_file_size) {
			$this->_errors[] = sprintf( __( 'File exceeds maximum file size. File must be under: %sMB.', 'ninja-forms-uploads' ), $max_file_size_mb );

			return false;
		}

		return true;
	}

	/**
	 * Check for min_file_size in the field settings
	 *
	 * @param int $size
	 *
	 * @return bool
	 */
	protected function validate_min_file_size( $size ) {
		$min_file_size_mb = $this->get_field()->get_setting( 'min_file_size', 0 );
		$min_file_size_mb = empty( $min_file_size_mb ) ? 0 : $min_file_size_mb;
		$min_file_size    = NF_File_Uploads()->controllers->settings->file_size_bytes_from_mb( $min_file_size_mb );
		if ( $min_file_size > 0 && $size < $min_file_size ) {
			$this->_errors[] = sprintf( __( 'File size under minimum size. File must be %sMB or greater.', 'ninja-forms-uploads' ), $min_file_size_mb );

			return false;
		}

		return true;
	}

	/**
	 * Check the file type is allowed by WordPress
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	protected function validate_file_type( $file ) {
		if ( ! self::is_allowed_type( $file ) ) {
			$this->_errors[] = __( 'File extension not allowed', 'ninja-forms-uploads' );

			return false;
		}

		return true;
	}

	/**
	 * Check for blacklisted file types
	 *
	 * @param string $extension
	 *
	 * @return bool
	 */
	protected function validate_extension_blacklist( $extension ) {
		if ( self::blacklisted( self::get_extension_blacklist(), $extension ) ) {
			$this->_errors[] = sprintf( __( 'File extension of %s not allowed', 'ninja-forms-uploads' ), $extension );

			return false;
		}

		return true;
	}

	protected function validate_extension_whitelist( $extension ) {
		$upload_types = $this->get_field()->get_setting( 'upload_types' );
		if ( empty( $upload_types ) ) {
			// We aren't restricting file types, bail
			return true;
		}

		$types = str_replace( '.', '', strtolower( $upload_types ) );
		$types = array_map( 'trim', explode( ',', $types ) );

		if ( in_array( 'jpg', $types ) && ! in_array( 'jpeg', $types ) ) {
			$types[] = 'jpeg';
		}

		// Check file extension against whitelist of file extensions
		if ( is_array( $types ) && false === $this->whitelisted( $types, $extension ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check the chunk size is allowed.
	 *
	 * @param int $content_length
	 *
	 * @return bool
	 */
	protected function validate_chunk_size( $content_length ) {
		$post_max_size  = NF_FU_Helper::max_upload_bytes_int();
		if ( $post_max_size && ( $content_length > $post_max_size ) ) {
			$this->_errors[] = $this->code_to_message( 1 );

			return false;
		}

		return true;
	}

	/**
	 * Validate the file for upload
	 *
	 * @param array $file
	 *
	 * @return bool
	 */
	protected function _validate( $file ) {
		// Check for upload errors
		if ( $file['error'] && UPLOAD_ERR_OK !== $file['error'] ) {
			$this->_errors[] = $this->code_to_message( $file['error'] );

			return false;
		}

		$content_length = NF_FU_Helper::fix_integer_overflow( (int) $this->get_server_var( 'CONTENT_LENGTH' ) );

		if ( ! $this->validate_chunk_size( $content_length ) ) {
			return false;
		}

		$file_size     = $content_length;
		$uploaded_file = $file['tmp_name'];
		if ( $uploaded_file && is_uploaded_file( $uploaded_file ) ) {
			$file_size = NF_FU_Helper::get_file_size( $uploaded_file );
		}

		if ( ! $this->validate_max_file_size( $file_size, $file['size'] ) ) {
			return false;
		}

		if ( ! $this->validate_min_file_size( $file_size ) ) {
			return false;
		}

		$filename = sanitize_file_name( $file['name'] );

		if ( ! $this->validate_file_type( $filename ) ) {
			return false;
		}

		$extension = pathinfo( $filename, PATHINFO_EXTENSION );

		if ( ! $this->validate_extension_blacklist( $extension ) ) {
			return false;
		}

		if ( ! $this->validate_extension_whitelist( $extension ) ) {
			return false;
		}

		return true;
	}

	public static function get_extension_blacklist() {
		return apply_filters( 'ninja_forms_uploads_extension_blacklist', NF_File_Uploads()->config( 'extension-blacklist' ) );
	}

	/**
	 * Check a file extension against a disallowed list of types
	 *
	 * @param array     $types
	 * @param string    $file_type
	 *
	 * @return bool
	 */
	public static function blacklisted( $types, $file_type) {
		// Check for blacklisted file types
		foreach ( $types as $extension ) {
			if ( strtolower( ltrim( $extension, '.' ) ) === strtolower( $file_type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check the file type is allowed to be uploaded by WordPress
	 *
	 * @param string $filename
	 *
	 * @return bool
	 */
	public static function is_allowed_type( $filename ) {
		$mime_types_whitelist       = apply_filters( 'ninja_forms_upload_mime_types_whitelist', get_allowed_mime_types() );
		$check_mime_types_whitelist = apply_filters( 'ninja_forms_upload_check_mime_types_whitelist', true );

		$file_info = wp_check_filetype( $filename, $mime_types_whitelist );
		if ( $check_mime_types_whitelist && empty( $file_info['ext'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check a file extension against an allowed list of types
	 *
	 * @param array     $types
	 * @param string    $file_type
	 *
	 * @return bool
	 */
	protected function whitelisted( $types, $file_type) {
		// Check for whitelisted file types
		foreach ( $types as $extension ) {
			if ( strtolower( $extension ) === strtolower( $file_type ) ) {
				return true;
			}
		}

		$this->_errors[] = sprintf( __( 'File extension of %s not allowed', 'ninja-forms-uploads' ), $file_type );

		return false;
	}

	/**
	 * Generate temporary filename
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	protected function get_temp_filename( $filename ) {
		$temp_filename  = 'nftmp-';
		$temp_filename  .= NF_FU_Helper::random_string( 5 ) . '-';
		$extension      = pathinfo( $filename, PATHINFO_EXTENSION );
		$clean_filename = rtrim( $filename, '.' . $extension );
		$clean_filename = strtolower( $clean_filename );
		$clean_filename = sanitize_file_name( $clean_filename );
		$clean_filename = preg_replace( '/[^a-zA-Z0-9]/', '', $clean_filename );
		$temp_filename  .= $clean_filename . '.' . $extension;

		return $temp_filename;
	}

	/**
	 * Convert $_FILES Error Code to Message
	 *
	 * http://php.net/manual/en/features.file-upload.errors.php
	 *
	 * @param $code
	 *
	 * @return string
	 */
	private function code_to_message( $code ) {
		switch ( $code ) {
			case UPLOAD_ERR_INI_SIZE:
				$message = __( "The uploaded file exceeds the upload_max_filesize directive in php.ini", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$message = __( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_PARTIAL:
				$message = __( "The uploaded file was only partially uploaded", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_NO_FILE:
				$message = __(  "No file was uploaded", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = __( "Missing a temporary folder", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = __( "Failed to write file to disk", 'ninja-forms-uploads' );
				break;
			case UPLOAD_ERR_EXTENSION:
				$message = __( "File upload stopped by extension", 'ninja-forms-uploads' );
				break;

			default:
				$message = __( "Unknown upload error", 'ninja-forms-uploads' );
				break;
		}

		return $message;
	}
}
