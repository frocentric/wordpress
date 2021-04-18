<?php

use NF_FU_VENDOR\Google_Client;
use NF_FU_VENDOR\Google_Service_Drive;
use NF_FU_VENDOR\Google_Service_Drive_DriveFile;
use NF_FU_VENDOR\Google_Service_Oauth2;
use NF_FU_VENDOR\Google_Service_Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Googledrive_Service
 */
class NF_FU_External_Services_Googledrive_Service extends NF_FU_External_Abstracts_Oauthservice {

	public $name = 'Google Drive';
	public $provider_slug = 'google';
	protected $consumer_key = '842241093457-5bmvtib0dolifml4oivjklo62qeh5ap2.apps.googleusercontent.com';

	protected $client;
	protected $drive;

	protected $access_token;

	/**
	 * @var int Maximum file size in bytes to send to service in a single request
	 */
	protected $max_single_upload_file_size = 5242880;

	/**
	 * Cache account check for a minute, so we don't hit the API lots in one request.
	 *
	 * @var bool
	 */
	protected $transient_expires = 60;

	/**
	 * Has the account authorised our API app?
	 *
	 * @return bool
	 */
	public function is_authorized() {
		return ( bool ) $this->get_account_info();
	}

	protected function retrieve_account_info( $access_token ) {
		$service = new Google_Service_Oauth2( $this->get_client( $access_token ) );
		try {
			$response = $service->tokeninfo( array( 'access_token' => $access_token ) );

			return $response;
		} catch ( Google_Service_Exception $e ) {
			$error = json_decode( $e->getMessage() );
			if ( isset( $error->error_description ) && 'Invalid Value' === $error->error_description ) {
				if ( ! $this->refresh_access_token() ) {
					return new \WP_Error( 'google_drive_disconnected' );
				}
			}

			return false;
		}
	}

	/**
	 * Check if an access token has expired before using it. Refresh if it has.
	 *
	 * @return bool|string
	 */
	public function get_access_token() {
		if ( $this->access_token ) {
			return $this->access_token;
		}

		$token = NF_File_Uploads()->externals->wpoauth()->token_manager->get_access_token( $this->slug(), '' );
		if ( ! $token ) {
			return false;
		}

		if ( isset( $token['expires'] ) && ( time() - 20 ) < $token['expires'] ) {
			$this->access_token = $token['token'];

			return $this->access_token;
		}

		$this->access_token = $this->refresh_access_token();

		return $this->access_token;
	}

	public function get_account_info() {
		$access_token = $this->get_access_token();

		if ( ! $access_token ) {
			return false;
		}

		if ( ! isset( $this->account_info_cache ) ) {
			$response = $this->retrieve_account_info( $access_token );
			if ( is_wp_error( $response ) ) {
				$this->disconnect();

				return false;
			}

			if ( false === $response ) {
				return false;
			}

			if ( $response ) {
				$this->account_info_cache = $response;

				return $response;
			}
		}

		return $this->account_info_cache;
	}

	/**
	 * Get the URL to authorise the app
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_authorize_url( $args = array() ) {
		$args = array(
			'scope'  => array( 'https://www.googleapis.com/auth/drive' ),
			'prompt' => 'consent',
		);

		return parent::get_authorize_url( $args );
	}

	/**
	 * Get Google Drive API client instance
	 *
	 * @return Google_Client
	 */
	public function get_client() {
		if ( is_null( $this->client ) ) {
			$token = $this->get_access_token();

			$client = new Google_Client();
			$client->setAccessToken( $token );
			$this->client = $client;
		}

		return $this->client;
	}

	/**
	 * @param null|Google_Client $client
	 *
	 * @return Google_Service_Drive
	 */
	public function drive( $client = null ) {
		if ( is_null( $this->drive ) ) {
			if ( is_null( $client ) ) {
				$client = $this->get_client();
			}
			$this->drive = new Google_Service_Drive( $client );
		}

		return $this->drive;
	}

	/**
	 * Get path on service
	 *
	 * @return string
	 */
	protected function get_path_setting() {
		return 'google_drive_file_path';
	}

	/**
	 * Create Google Drive file object.
	 *
	 * @param string $filename
	 * @param string $path
	 *
	 * @return Google_Service_Drive_DriveFile
	 */
	public function create_drive_file( $filename, $path ) {
		$args = array(
			'name' => $filename,
		);

		if ( ! empty ( $path ) && '/' !== $path ) {
			// Check folder exists or create it
			$folder_id = $this->create_or_get_directory( $path );

			$args['parents'] = array( $folder_id );
		}

		return new Google_Service_Drive_DriveFile( $args );
	}

	/**
	 * Upload the file to the service
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	public function upload_file( $data ) {
		$fileMetadata = $this->create_drive_file( $this->external_filename, $this->external_path  );

		$content = file_get_contents( $this->upload_file );

		$file = $this->drive()->files->create( $fileMetadata, array(
			'data'       => $content,
			'uploadType' => 'multipart',
			'fields'     => 'id',
		) );

		$data['file_id'] = $file->id;

		return $data;
	}

	protected function get_all_directories() {
		$all_directories = array();
		$token = '';
		do {
			$args = array(
				'fields'                    => 'nextPageToken, files(id, name, parents)',
				'q'                         => 'mimeType = \'application/vnd.google-apps.folder\' AND trashed != true',
				'pageSize'                  => 100,
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
			);

			if ( $token ) {
				$args['pageToken'] = $token;
			}

			$directories     = $this->drive()->files->listFiles( $args );
			$all_directories = array_merge( $all_directories, $directories->getFiles() );
			$token           = $directories->getNextPageToken();

		} while ( ! is_null( $token ) );

		$directories = array();
		foreach ( $all_directories as $file ) {
			$id = $file->getID();

			$directories[ $id ] = array(
				'id'      => $id,
				'name'    => $file->getName(),
				'parents' => $file->getParents(),
			);
		}

		return $directories;
	}

	protected function get_directory_id( $path ) {
		$directories = array_filter( explode('/', $path ) );
		$directory_ids = array_fill_keys($directories, null);

		$current_directories = $this->get_all_directories();

		$parents = array();
		foreach ( $directories as $key => $directory ) {
			$parent = isset( $parents[ $key ] ) ? $parents[ $key ] : null;

			$found_directory = $this->find_directory( $directory, $parent, $current_directories );
			if ( ! $found_directory ) {
				continue;
			}

			$directory_ids[ $directory ] = $found_directory;

			$next_key = $key + 1;
			if ( isset( $directories[ $next_key ] ) ) {
				$parents[ $key + 1 ] = $found_directory;
			}
		}

		return $directory_ids;
	}

	protected function find_directory( $name, $parent = null, $directories ) {
		foreach( $directories as $id => $directory ) {
			if ( strtolower( $name ) !== strtolower( $directory['name'] ) ) {
				continue;
			}

			if ( ! $parent && ( is_null( $directory['parents'] ) || ! isset( $directories[ $directory['parents'][0] ] ) ) ) {
				return $id;
			}

			if ( $parent && in_array( $parent, $directory['parents'] ) ) {
				return $id;
			}
		}

		return false;
	}

	public function create_or_get_directory( $path ) {
		$directory_ids = $this->get_directory_id( $path );

		$parent_id    = null;
		foreach ( $directory_ids as $name => $file_id ) {
			if ( is_null( $file_id ) ) {
				// Create Dir
				$args = array(
					'mimeType' => 'application/vnd.google-apps.folder',
					'name'     => $name,
				);

				if ( $parent_id ) {
					$args['parents'] = array( $parent_id );
				}
				$dir     = new Google_Service_Drive_DriveFile( $args );
				$file    = $this->drive()->files->create( $dir );
				$file_id = $file->id;


				$directory_ids[ $name ] = $file_id;
			}

			$parent_id = $file_id;
		}

		return $parent_id;
	}

	/**
	 * Get the service URL to the file
	 *
	 * @param string $filename
	 * @param string $path
	 * @param array  $data
	 *
	 * @return string
	 */
	public function get_url( $filename, $path = '', $data = array() ) {
		$response = $this->drive()->files->get( $data['file_id'], array( 'fields' => 'id, webContentLink' ) );

		if ( $response && isset( $response->webContentLink ) ) {
			return str_replace( array( '/uc?', '&export=download' ), array( '/open?', '' ), $response->webContentLink );
		}

		return admin_url();
	}

	/**
	 * @param bool   $should_bg_upload
	 * @param string $file
	 * @param array  $field
	 * @param int    $form_id
	 *
	 * @return bool
	 */
	protected function should_background_upload( $should_bg_upload, $file, $field, $form_id ) {
		// Check if we are renaming
		$renaming = ! empty( $field['upload_rename'] ) || NF_File_Uploads()->controllers->settings->custom_upload_dir();
		if ( $renaming ) {
			$should_bg_upload = true;
		}

		return parent::should_background_upload( $should_bg_upload, $file, $field, $form_id );
	}
}