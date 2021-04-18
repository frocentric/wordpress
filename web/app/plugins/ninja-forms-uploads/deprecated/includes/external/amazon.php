<?php
require_once( NINJA_FORMS_UPLOADS_DIR . '/includes/lib/s3/s3.php' );

/**
 * Class External_Amazon
 */
class External_Amazon extends NF_Upload_External {

	private $title = 'Amazon S3';

	private $slug = 'amazon';

	private $settings;

	private $connected_settings = false;

	private $file_path = false;

	function __construct() {
		$this->set_settings();
		parent::__construct( $this->title, $this->slug, $this->settings );
	}

	private function set_settings() {
		$this->settings = array(
			array(
				'name'  => 'amazon_s3_access_key',
				'type'  => 'text',
				'label' => __( 'Access Key', 'ninja-forms-uploads' ),
				'desc'  => '',
			),
			array(
				'name'  => 'amazon_s3_secret_key',
				'type'  => 'text',
				'label' => __( 'Secret Key', 'ninja-forms-uploads' ),
				'desc'  => '',
			),
			array(
				'name'  => 'amazon_s3_bucket_name',
				'type'  => 'text',
				'label' => __( 'Bucket Name', 'ninja-forms-uploads' ),
				'desc'  => '',
			),
			array(
				'name'          => 'amazon_s3_file_path',
				'type'          => 'text',
				'label'         => __( 'File Path', 'ninja-forms-uploads' ),
				'desc'          => __( 'The default file path in the bucket where the file will be uploaded to', 'ninja-forms-uploads' ),
				'default_value' => 'ninja-forms/'
			),
		);
	}

	public function is_connected() {
		$data = get_option( 'ninja_forms_settings' );
		if ( ( isset( $data['amazon_s3_access_key'] ) && $data['amazon_s3_access_key'] != '' ) &&
		     ( isset( $data['amazon_s3_secret_key'] ) && $data['amazon_s3_secret_key'] != '' ) &&
		     ( isset( $data['amazon_s3_bucket_name'] ) && $data['amazon_s3_bucket_name'] != '' ) &&
		     ( isset( $data['amazon_s3_file_path'] ) && $data['amazon_s3_file_path'] != '' )
		) {
			return true;
		}

		return false;
	}

	private function load_settings() {
		if ( ! $this->connected_settings ) {
			$data                     = get_option( 'ninja_forms_settings' );
			$settings                 = array();
			$settings['access_key']   = $data['amazon_s3_access_key'];
			$settings['secret_key']   = $data['amazon_s3_secret_key'];
			$settings['bucket_name']  = $data['amazon_s3_bucket_name'];

			$bucket = $settings['bucket_name'];

			if ( ( ! isset( $data['amazon_s3_bucket_region'][ $bucket ] ) || empty( $data['amazon_s3_bucket_region'][ $bucket ] ) ) && isset( $settings['bucket_name'] ) ) {
				// Retrieve the bucket region if we don't have it
				// Or the bucket has changed since we last retrieved it
				$s3     = new S3( $settings['access_key'], $settings['secret_key'] );
				$region = $s3->getBucketLocation( $settings['bucket_name'] );

				$data['amazon_s3_bucket_region'] = array( $settings['bucket_name'] => $region );
				update_option( 'ninja_forms_settings', $data );
			} else {
				$region = $data['amazon_s3_bucket_region'][ $bucket ];
			}

			$settings['bucket_region'] = $region;
			$settings['file_path']     = $data['amazon_s3_file_path'];

			$this->connected_settings = $settings;
		}
	}

	private function prepare( $path = false, $region = null ) {
		$this->load_settings();
		if ( ! $path ) {
			$path = apply_filters( 'ninja_forms_uploads_' . $this->slug . '_path', $this->connected_settings['file_path'] );
		} else if ( $path == '' ) {
			$path = $this->connected_settings['file_path'];
		}
		$this->file_path = $this->sanitize_path( $path );

		$s3 = new S3( $this->connected_settings['access_key'], $this->connected_settings['secret_key'] );

		if ( is_null( $region ) ) {
			$region = $this->connected_settings['bucket_region'];
		}

		if ( '' !== $region && 'US' !== $region ) {
			// Use the correct API endpoint for non US standard bucket regions
			$s3->setEndpoint( 's3-' . $this->connected_settings['bucket_region'] . '.amazonaws.com' );
		}

		return $s3;
	}

	public function upload_file( $file, $path = false ) {
		$s3       = $this->prepare( $path );
		$filename = $this->get_filename_external( $file );
		$s3->putObjectFile( $file, $this->connected_settings['bucket_name'], $this->file_path . $filename, S3::ACL_PUBLIC_READ );

		return array( 'path' => $this->file_path, 'filename' => $filename );
	}

	/**
	 * Get the Amazon S3 URL using bucket and region for the file, falling
	 * back to the settings bucket and region
	 *
	 * @param string $filename
	 * @param string $path
	 * @param array  $data
	 *
	 * @return string
	 */
	public function file_url( $filename, $path = '', $data = array() ) {
		$bucket = ( isset( $data['bucket'] ) ) ? $data['bucket'] : $this->connected_settings['bucket_name'];
		$region = ( isset( $data['region'] ) ) ? $data['region'] : $this->connected_settings['bucket_region'];

		$s3 = $this->prepare( $path, $region );

		return $s3->getAuthenticatedURL( $bucket, $this->file_path . $filename, 3600 );
	}

	/**
	 * Save the bucket and region to the file data
	 * in case it is changed in settings.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function enrich_file_data( $data ) {
		$data['bucket'] = $this->connected_settings['bucket_name'];
		$data['region'] = $this->connected_settings['bucket_region'];

		return $data;
	}
}
