<?php

use NF_FU_VENDOR\Aws\Credentials\Credentials;
use NF_FU_VENDOR\Aws\S3\S3Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_S3_Service
 */
class NF_FU_External_Services_S3_Service extends NF_FU_External_Abstracts_Service {

	public $name = 'Amazon S3';

	/**
	 * @var int Maximum file size in bytes to send to service in a single request
	 */
	protected $max_single_upload_file_size = 5242880;

	protected static $clients = array();

	// ACL flags
	const ACL_PRIVATE = 'private';
	const ACL_PUBLIC_READ = 'public-read';
	const ACL_PUBLIC_READ_WRITE = 'public-read-write';
	const ACL_AUTHENTICATED_READ = 'authenticated-read';

	const DEFAULT_REGION = 'us-east-1';
	const AWS_SIGNATURE = 'v4';
	const S3_API_VERSION = '2006-03-01';

	/**
	 * Wrapper for getting the S3 client
	 *
	 * @param      $amazon_s3_access_key
	 * @param      $amazon_s3_secret_key
	 * @param null $region
	 *
	 * @return S3Client
	 */
	protected function get_s3client( $amazon_s3_access_key, $amazon_s3_secret_key, $region = 'us-west-2' ) {
		$credentials = new Credentials($amazon_s3_access_key , $amazon_s3_secret_key);

		$args = array(
			'version'         => self::S3_API_VERSION,
			'credentials'     => $credentials,
			'exception_class' => NF_FU_External_Loader::NF_FU_VENDOR_NS_PREFIX . '\Aws\S3\Exception\S3Exception',
		);

		if ( $region ) {
			$args['region']    = $this->translate_region( $region );
			$args['signature'] = self::AWS_SIGNATURE;
		}

		return new S3Client( $args );
	}

	/**
	 * Translate older bucket locations to newer S3 region names
	 * http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region
	 *
	 * @param $region
	 *
	 * @return string
	 */
	protected function translate_region( $region ) {
		if ( ! is_string( $region ) ) {
			// Don't translate any region errors
			return $region;
		}

		$region = strtolower( $region );

		switch ( $region ) {
			case 'eu':
				$region = 'eu-west-1';
				break;
		}

		return $region;
	}

	/**
	 * Is the service connected?
	 *
	 * @param null|array $settings
	 *
	 * @return bool
	 */
	public function is_connected( $settings = null ) {
		if ( is_null( $settings ) ) {
			$settings = $this->load_settings();
		}

		foreach ( $settings as $key => $value ) {
			if ( 'amazon_s3_file_path' === $key ) {
				continue;
			}

			if ( ! is_array( $value ) && '' === trim( $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Load S3 settings and ensure we have the region for the bucket
	 *
	 * @return array
	 */
	public function load_settings() {
		$settings = parent::load_settings();

		if ( ! $this->is_connected( $settings ) ) {
			return $settings;
		}

		$bucket = $settings['amazon_s3_bucket_name'];

		$data = NF_File_Uploads()->controllers->settings->get_settings();

		if ( ( ! isset( $data['amazon_s3_bucket_region'][ $bucket ] ) || empty( $data['amazon_s3_bucket_region'][ $bucket ] ) ) ) {
			// Retrieve the bucket region if we don't have it
			// Or the bucket has changed since we last retrieved it
			$s3     = $this->get_s3client( $settings['amazon_s3_access_key'], $settings['amazon_s3_secret_key'], apply_filters( 'ninja_forms_uploads_default_region', 'us-west-2' ) );
			$region = $this->get_bucket_region( $s3, $bucket );
			if ( false === $region ) {
				return $this->settings;
			}

			$this->settings['amazon_s3_bucket_region'][ $bucket ] = $region;

			$data['amazon_s3_bucket_region'] = $this->settings['amazon_s3_bucket_region'];
			update_option( 'ninja_forms_settings', $data );
		}

		return $this->settings;
	}

	/**
	 * Get the S3 client
	 *
	 * @param string $region
	 *
	 * @return S3Client
	 */
	public function get_client( $region = '' ) {
		if ( '' === $region || 'US' === $region) {
			$region = 'us-east-1';
		}

		if ( ! isset( self::$clients[ $region ] ) ) {

			$this->load_settings();

			$s3 = $this->get_s3client( $this->settings['amazon_s3_access_key'], $this->settings['amazon_s3_secret_key'], $region );

			self::$clients[ $region ] = $s3;
		}

		return self::$clients[ $region ];
	}

	/**
	 * Get a region of a bucket.
	 *
	 * @param S3Client $s3
	 * @param string $bucket
	 *
	 * @return bool|string
	 */
	protected function get_bucket_region( $s3, $bucket ) {
		try {
			$region = $s3->getBucketLocation( array( 'Bucket' => $bucket ) );
		} catch ( Exception $e ) {
			error_log( sprintf( __( 'There was an error attempting to get the region of the bucket %s: %s', 'ninja-forms-uploads' ), $bucket, $e->getMessage() ) );


			return false;
		}

		return $this->translate_region( $region['LocationConstraint'] );
	}

	/**
	 * Get region of configured bucket
	 *
	 *
	 * @return string
	 */
	public function get_region() {
		$bucket = $this->settings['amazon_s3_bucket_name'];
		$data   = NF_File_Uploads()->controllers->settings->get_settings();
		$region = isset( $data['amazon_s3_bucket_region'][ $bucket ] ) ? $data['amazon_s3_bucket_region'][ $bucket ] : '';

		return $region;
	}

	/**
	 * Get path on S3 to upload to
	 *
	 * @return string
	 */
	protected function get_path_setting() {
		return 'amazon_s3_file_path';
	}

	/**
	 * Upload the file to S3
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	public function upload_file( $data ) {
		$bucket = $this->settings['amazon_s3_bucket_name'];
		$region = $this->get_region();

		$result = $this->upload_file_to_s3( $bucket, $region, $this->upload_file, $this->external_path . $this->external_filename );

		if ( false === $result ) {
			return false;
		}

		$data['bucket'] = $bucket;
		$data['region'] = $region;

		return $data;
	}

	/**
	 * Wrapper for uploading to S3
	 *
	 * @param $bucket
	 * @param $region
	 * @param $file
	 * @param $key
	 *
	 * @return bool
	 */
	protected function upload_file_to_s3( $bucket, $region, $file, $key ) {
		$s3 = $this->get_client( $region );

		$args = array(
			'Bucket'     => $bucket,
			'Key'        => $key,
			'SourceFile' => $file,
			'ACL'        => apply_filters( 'ninja_forms_uploads_s3_acl', self::ACL_PRIVATE ),
		);

		$args = apply_filters( 'ninja_forms_uploads_s3_args', $args );

		try {
			$s3->putObject( $args );
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );

			return false;
		}

		return true;
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
	public function get_url( $filename, $path = '', $data = array() ) {
		$bucket = ( isset( $data['bucket'] ) ) ? $data['bucket'] : $this->settings['amazon_s3_bucket_name'];
		$region = ( isset( $data['region'] ) ) ? $data['region'] : $this->get_region();

		$s3 = $this->get_client( $region );

		return $this->get_s3_url( $s3, $bucket, $path . $filename, 3600 );
	}

	/**
	 * Wrapper for getting S3 URL
	 *
	 * @param S3Client $s3
	 * @param string   $bucket
	 * @param string   $key
	 *
	 * @return string
	 */
	protected function get_s3_url( $s3, $bucket, $key ) {
		$expires = apply_filters( 'ninja_forms_uploads_s3_expires', 3600 );

		$expires = time() + $expires;

		$commandArgs = array( 'Bucket' => $bucket, 'Key' => $key );

		$command = $s3->getCommand( 'GetObject', $commandArgs );

		return (string) $s3->createPresignedRequest( $command, $expires )->getUri();
	}

	/**
	 * @return array
	 */
	protected function prepare_data_for_background_upload() {
		$item = parent::prepare_data_for_background_upload();

		$item['bucket'] = $this->settings['amazon_s3_bucket_name'];

		return $item;
	}
}
