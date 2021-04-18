<?php

abstract class NF_Upload_External {

	private $slug;

	private $title;

	private $settings;

	function __construct( $title, $slug, $settings ) {
		$this->title    = $title;
		$this->slug     = $slug;
		$this->settings = $settings;

		$this->init();
	}

	public function init( ){
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'ninja_forms_upload_locations', array( $this, 'register_location' ) );
		add_action( 'ninja_forms_post_process', array( $this, 'upload_to_external' ) );
		add_action( 'ninja_forms_post_process', array( $this, 'remove_server_upload' ), 1001 );
	}

	public static function instance( $external, $require = false ) {
		if ( $require ) {
			require_once( $external );
			$external = basename( $external, '.php' );
		}
		$external_class = 'External_' . ucfirst( $external );
		if ( class_exists( $external_class ) ) {
			return new $external_class();
		}

		return false;
	}

	public function register_location( $locations ) {
		if ( $this->is_connected() && ! $this->already_registered( $locations, $this->slug ) ) {
			$locations[] = array(
				'value' => $this->slug,
				'name'  => $this->title
			);
		}

		return $locations;
	}

	private function already_registered( $locations, $slug ) {
		foreach ( $locations as $location ) {
			if ( isset( $location[ 'value' ] ) && $location[ 'value' ] == $slug ) {
				return true;
			}
		}

		return false;
	}

	public function register_settings() {
		$args = array(
			'page'     => 'ninja-forms-uploads',
			'tab'      => 'external_settings',
			'slug'     => $this->slug . '_settings',
			'title'    => sprintf( __( '%s Settings', 'ninja-forms-uploads' ), $this->title ),
			'settings' => $this->settings
		);
		if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
			ninja_forms_register_tab_metabox( $args );
		}
	}

	public function is_connected() {
		return false;
	}

	private function post_process( $form_id ) {
		if ( ! $this->is_connected() ) {
			return false;
		}

		return ninja_forms_upload_get_uploaded_files( $this->slug );
	}

	public function upload_to_external( $form_id ) {
		$files = $this->post_process( $form_id );
		if ( ! is_array( $files ) || empty( $files ) ) {
			return;
		}

		global $ninja_forms_processing, $wpdb;

		foreach ( $files as $data ) {
			if ( ! $data['user_value'] ) {
				continue;
			}
			foreach ( $data['user_value'] as $key => $file ) {
				if ( ! isset( $file['file_path'] ) ) {
					continue;
				}
				$filename = $file['file_path'] . $file['file_name'];
				if ( file_exists( $filename ) ) {
					$args = $this->upload_file( $filename );
					if ( $args['path'] != '' ) {
						$path = trailingslashit( $args['path'] );
					}
					if ( isset( $data['field_row']['data']['upload_location'] ) ) {
						$data['user_value'][ $key ]['upload_location'] = $data['field_row']['data']['upload_location'];
					}
					$data['user_value'][ $key ]['external_path'] = $path;
					$data['user_value'][ $key ]['external_filename'] = $args['filename'];

					// Allow the external service classes to alter the file data
					$file_data = $this->enrich_file_data( $data['user_value'][ $key ] );

					$wpdb->update( NINJA_FORMS_UPLOADS_TABLE_NAME, array( 'data' => serialize( $file_data) ), array( 'id' => $data['user_value'][ $key ]['upload_id'] ) );
				}
			}

			$ninja_forms_processing->update_field_value( $data['field_id'], $data['user_value'] );
		}


	}

	public function remove_server_upload( $form_id ) {
		$files = $this->post_process( $form_id );
		if ( ! is_array( $files ) || empty( $files ) ) {
			return;
		}

		ninja_forms_upload_remove_uploaded_files( $files );
	}

	public function upload_file( $filename, $path = '' ) {
		return '';
	}

	public function file_url( $filename, $path = '', $data = array() ) {
		return '';
	}

	public function sanitize_path( $path, $suffix = '/' ) {
		$path = ltrim( $path, '/' );
		$path = rtrim( $path, '/' );

		return $path . $suffix;
	}

	public function get_filename_external( $file ) {
		$filename = basename( $file );
		$filename = time() . '-'. $filename;

		return apply_filters( 'ninja_forms_uploads_' . $this->slug . '_filename', $filename );
	}

	public function enrich_file_data( $data ){
		return $data;
	}

} 