<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Abstracts_Service
 */
abstract class NF_FU_External_Abstracts_Service {

	/**
	 * @var array
	 */
	private static $instances = array();

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var array
	 */
	public $settings;

	/**
	 * @var string
	 */
	public $slug;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	protected $library_alias;

	/**
	 * @var string
	 */
	protected $library_file;

	/**
	 * @var string Path to file being uploaded
	 */
	protected $upload_file;

	/**
	 * @var string Path prefix to file on service
	 */
	protected $external_path;

	/**
	 * @var string Filename of file on service
	 */
	protected $external_filename;

	/**
	 * @var array Ninja Form settings
	 */
	protected $nf_settings;

	/**
	 * @var int Maximum file size in bytes to send to service in a single request
	 */
	protected $max_single_upload_file_size;

	/**
	 * @var NF_FU_External_Abstracts_Backgroundupload
	 */
	protected $upload_process;
	
	/**
	 * Main Plugin Instance
	 *
	 * Insures that only one instance of a service class exists in memory at any one
	 * time.
	 *
	 * @return NF_FU_External_Abstracts_Service
	 */
	final public static function instance() {
		$called_class = get_called_class();

		if ( ! isset( self::$instances[ $called_class ] ) ) {
			self::$instances[ $called_class ] = new $called_class();

			$parts = explode( '_', str_replace( '_Service', '', $called_class ) );
			$slug  = strtolower( array_pop( $parts ) );

			$reflector = new ReflectionClass( $called_class );

			self::$instances[ $called_class ]->file          = $reflector->getFileName();
			self::$instances[ $called_class ]->slug          = $slug;
			
			self::$instances[ $called_class ]->init();
		}

		return self::$instances[ $called_class ];
	}

	/**
	 * Get instance internally
	 *
	 * @return mixed
	 */
	protected static function get_instance() {
		$called_class = get_called_class();

		return self::$instances[ $called_class ];
	}

	/**
	 * Do things on initialization
	 */
	protected function init() {
		$class         = get_called_class();
		$process_class = substr( $class, 0, -8 ) . '_Backgroundupload';

		if ( class_exists( $process_class ) ) {
			$this->upload_process = new $process_class( $this->slug );
		}
	}

	/**
	 * @param string $upload_file
	 */
	public function set_upload_file( $upload_file ) {
		$this->upload_file = $upload_file;
	}

	/**
	 * @param string $external_path
	 */
	public function set_external_path( $external_path ) {
		$this->external_path = $external_path;
	}

	/**
	 * @param string $external_filename
	 */
	public function set_external_filename( $external_filename ) {
		$this->external_filename = $external_filename;
	}

	/**
	 * Get settings
	 *
	 * @return array
	 */
	public function get_settings() {
		$config = dirname( self::get_instance()->file ) . '/config.php';
		if ( file_exists( dirname( self::get_instance()->file ) . '/config.php' ) ) {
			return include $config;
		}

		return array();
	}

	/**
	 * Load the service specific settings
	 *
	 * @return array
	 */
	public function load_settings() {
		if ( is_null( $this->settings ) ) {

			$plugin_settings = NF_File_Uploads()->controllers->settings->get_settings();
			$settings        = $this->get_settings();

			$this->settings = array();
			foreach ( $settings as $key => $setting ) {
				$default = isset( $setting['default'] ) ? $setting['default'] : '';

				if ( NF_File_Uploads()->controllers->settings->is_defined( $key ) ) {
					$constant_name = NF_File_Uploads()->controllers->settings->get_defined_name( $key );
					$value         = constant( $constant_name );
				} else {
					$value = isset( $plugin_settings[ $key ] ) ? $plugin_settings[ $key ] : $default;
				}

				$this->settings[ $key ] = $value;
			}

		}

		return $this->settings;
	}

	/**
	 * Get service name
	 *
	 * @return string
	 */
	public function get_name() {
		$name = self::get_instance()->slug;
		if ( ! is_null( self::get_instance()->name ) ) {
			$name = self::get_instance()->name;
		}

		return $name;
	}

	/**
	 * Get the external filename for a file
	 *
	 * @param null $timestamp
	 *
	 * @return string
	 */
	protected function get_filename_external( $timestamp = null ) {
		$original_filename = NF_FU_Helper::remove_directory_from_file( $this->upload_file );
		if ( empty( $timestamp ) ) {
			$timestamp = time();
		}
		$filename = $timestamp . '-' . $original_filename;

		return apply_filters( 'ninja_forms_uploads_' . self::get_instance()->slug . '_filename', $filename, $original_filename );
	}

	/**
	 * Get path on service
	 *
	 * @return string
	 */
	protected abstract function get_path_setting();

	/**
	 * Get the external file path
	 *
	 * @param string $custom_path
	 *
	 * @return string
	 */
	protected function get_external_path( $custom_path ) {
		$path = $this->settings[ $this->get_path_setting() ];

		$path = untrailingslashit( $path ) . '/' . untrailingslashit( $custom_path );

		return apply_filters( 'ninja_forms_uploads_' . $this->slug . '_path', $this->prepare_path( $path ) );
	}

	/**
	 * Prepare path
	 *
	 * @param string $path
	 * @param string $suffix
	 *
	 * @return string
	 */
	protected function prepare_path( $path, $suffix = '/' ) {
		$path = ltrim( $path, '/' );
		$path = rtrim( $path, '/' );

		if ( ! empty( $path ) ) {
			$path .= $suffix;
		}

		return $path;
	}

	/**
	 * Upload the attachment to the service
	 *
	 * @param array    $data
	 * @param bool     $remove_from_server
	 * @param null     $upload_timestamp
	 * @param array    $field
	 * @param null|int $form_id
	 * @param bool     $background_upload
	 *
	 * @return array
	 */
	public function process_upload( $data, $remove_from_server = false, $upload_timestamp = null, $field = array(), $form_id = null, $background_upload = false ) {
		$this->load_settings();

		$this->upload_file       = $data['file_path'];
		$custom_path             = isset( $data['custom_path'] ) ? $data['custom_path'] : '';
		$this->external_path     = $this->get_external_path( $custom_path );
		$this->external_filename = $this->get_filename_external( $upload_timestamp );

		if ( $this->should_background_upload( $background_upload, $data['file_path'], $field, $form_id  ) ) {
			$chunked = $this->is_file_larger_than_max_chunk( $data['file_path'] );

			$this->init_background_upload( $data, $chunked );

			if ( $remove_from_server ) {
				$data['defer_remove_from_server'] = $remove_from_server;
			}

			$data['external_locations'][ $this->slug ] = 0;

			NF_File_Uploads()->model->update( $data['upload_id'], $data );

			return $data;
		}

		$data = $this->upload_file( $data );

		if ( false === $data ) {
			return $data;
		}

		$data['upload_location']   = $this->slug;
		$data['external_path']     = $this->external_path;
		$data['external_filename'] = $this->external_filename;

		$data['external_locations'][ $this->slug ] = 1;

		// Update uploads table
		NF_File_Uploads()->model->update( $data['upload_id'], $data );

		return $data;
	}

	/**
	 * Is the service compatible with the site?
	 *
	 * @return bool
	 */
	public function is_compatible() {
		$missing_requirements = $this->get_missing_requirements();

		return empty( $missing_requirements );
	}

	/**
	 * Get the missing service requirements
	 *
	 * @return array|bool
	 */
	public function get_missing_requirements() {
		$missing_requirements = array();

		return $missing_requirements;
	}

	/**
	 * Get notices for the service.
	 *
	 * @return array|bool
	 */
	public function get_notices() {
		return array();
	}

	/**
	 * Check if the file should be uploaded in a single process or multipart upload to the service.
	 *
	 * @param bool       $should_bg_upload
	 * @param string $file
	 * @param array  $field
	 * @param int    $form_id
	 *
	 * @return bool
	 */
	protected function should_background_upload( $should_bg_upload, $file, $field, $form_id ) {
		$pre = apply_filters( 'ninja_forms_uploads_should_background_upload', $should_bg_upload, $file, $this->slug, $field, $form_id );

		if ( false !== $pre ) {
			return $pre;
		}

		return $this->is_file_larger_than_max_chunk( $file );
	}

	/**
	 * Is the file larger than allowed in a single upload by the service.
	 *
	 * @param string $file_path
	 *
	 * @return bool
	 */
	protected function is_file_larger_than_max_chunk( $file_path ) {
		$file_size = NF_FU_Helper::get_file_size( $file_path );

		if ( $file_size < apply_filters( 'ninja_forms_uploads_max_chunk_size_' . $this->slug, $this->max_single_upload_file_size ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Prepare the file upload data to be passed to the background process.
	 *
	 * @return array
	 */
	protected function prepare_data_for_background_upload() {
		return array(
			'upload_file'       => $this->upload_file,
			'external_path'     => $this->external_path,
			'external_filename' => $this->external_filename,
		);
	}

	/**
	 * Pass the file data to a background process to upload to the service.
	 *
	 * @param array $data
	 * @param bool  $chunked Should we upload in chunks
	 */
	protected function init_background_upload( $data, $chunked = true ) {
		$item = $this->prepare_data_for_background_upload();

		$data['chunked'] = $chunked;
		if ( ! $chunked ) {
			$data['external_path']     = $this->external_path;
			$data['external_filename'] = $this->external_filename;
		}

		$item['data'] = $data;

		$this->upload_process->push_to_queue( $item )->save()->data( array() )->dispatch();
	}

	/**
	 * Upload the file to the service
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	public abstract function upload_file( $data );

	/**
	 * Get the service URL to the file
	 *
	 * @param string $filename
	 * @param string $path
	 * @param array  $data
	 *
	 * @return string
	 */
	public abstract function get_url( $filename, $path = '', $data = array() );

	/**
	 * @param null|array $settings
	 *
	 * @return bool
	 */
	public abstract function is_connected( $settings = null );

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * class via the `new` operator from outside of this class.
	 */
	protected function __construct() {
	}

	/**
	 * As this class is a singleton it should not be clone-able
	 */
	protected function __clone() {
	}

	/**
	 * As this class is a singleton it should not be able to be unserialized
	 */
	public function __wakeup() {
	}
}