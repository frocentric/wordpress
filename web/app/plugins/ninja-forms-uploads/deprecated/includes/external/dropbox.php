<?php

require_once( NINJA_FORMS_UPLOADS_DIR . '/includes/lib/dropbox/dropbox.php' );

class External_Dropbox extends NF_Upload_External {

	private $title = 'Dropbox';

	private $slug = 'dropbox';

	private $path;

	private $settings;

	function __construct() {
		$this->set_settings();
		parent::__construct( $this->title, $this->slug, $this->settings );

		add_action( 'admin_init', array( $this, 'disconnect' ) );
		add_action( 'admin_notices', array( $this, 'connect_notice' ) );
		add_action( 'admin_notices', array( $this, 'disconnect_notice' ) );
	}

	private function set_settings() {
		$this->settings = array(
			array(
				'name'             => 'dropbox_connect',
				'type'             => '',
				'label'            => sprintf( __( 'Connect to %s', 'ninja-forms-uploads' ), $this->title ),
				'desc'             => '',
				'display_function' => array( $this, 'connect_url' )
			),
			array(
				'name'          => 'dropbox_file_path',
				'type'          => 'text',
				'label'         => __( 'File Path', 'ninja-forms-uploads' ),
				'desc'          => __( 'Custom directory for the files to be uploaded to in your Dropbox:<br> /Apps/Ninja Forms Uploads/'. $this->get_path() , 'ninja-forms-uploads' ),
				'default_value' => ''
			),
			array(
				'name' => 'dropbox_token',
				'type' => 'hidden'
			)
		);
	}

	private function get_path() {
		if ( is_null( $this->path ) ) {
			$plugin_settings = get_option( 'ninja_forms_settings' );
			$this->path = isset( $plugin_settings['dropbox_file_path'] ) ? $plugin_settings['dropbox_file_path'] : '';
			$this->path = trim( $this->path );
			$this->path = apply_filters( 'ninja_forms_uploads_' . $this->slug . '_path', $this->path );
			if ( '/' == $this->path ) {
				$this->path = '';
			}

			if ( '' != $this->path ) {
				$this->path = $this->sanitize_path( $this->path );
			}
		}

		return $this->path;
	}

	public function is_connected() {
		$data = get_option( 'ninja_forms_settings' );
		if ( ( isset( $data['dropbox_access_token'] ) && $data['dropbox_access_token'] != '' ) &&
		     ( isset( $data['dropbox_access_token_secret'] ) && $data['dropbox_access_token_secret'] != '' )
		) {
			if ( false === ( $authorised = get_transient( 'nf_fu_dropbox_authorised' ) ) ) {
				$dropbox = new nf_dropbox();
				$authorised = $dropbox->is_authorized();

				set_transient( 'nf_fu_dropbox_authorised', $authorised, 60 * 60 * 5 );
			}
			return $authorised;
		}

		return false;
	}

	public function upload_file( $file, $path = '' ) {
		$dropbox  = new nf_dropbox();
		$path     = $this->get_path();
		$filename = $this->get_filename_external( $file );
		$dropbox->upload_file( $file, $filename, $path );

		return array( 'path' => $path, 'filename' => $filename );
	}

	public function file_url( $filename, $path = '', $data = array() ) {
		$dropbox = new nf_dropbox();
		$url     = $dropbox->get_link( $path . $filename );
		if ( $url ) {
			return $url;
		}

		return admin_url();
	}

	public function connect_url( $form_id, $data ) {
		$dropbox        = new nf_dropbox();
		$callback_url   = admin_url( '/admin.php?page=ninja-forms-uploads&tab=external_settings' );
		$disconnect_url = admin_url( '/admin.php?page=ninja-forms-uploads&tab=external_settings&action=disconnect_' . $this->slug );
		if ( $dropbox->is_authorized() ) {
			?>
			<a id="dropbox-disconnect" href="<?php echo $disconnect_url; ?>" class="button-secondary"><?php _e( 'Disconnect', 'ninja-forms-uploads' ); ?></a>
		<?php } else { ?>
			<a id="dropbox-connect" href="<?php echo $dropbox->get_authorize_url( $callback_url ); ?>" class="button-secondary"><?php _e( 'Connect', 'ninja-forms-uploads' ); ?></a>
		<?php
		}
	}

	public function disconnect() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'ninja-forms-uploads' &&
		     isset( $_GET['tab'] ) && $_GET['tab'] == 'external_settings' &&
		     isset( $_GET['action'] ) && $_GET['action'] == 'disconnect_' . $this->slug
		) {

			$dropbox = new nf_dropbox();
			$dropbox->unlink_account();
		}
	}

	public function connect_notice() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'ninja-forms-uploads' &&
		     isset( $_GET['tab'] ) && $_GET['tab'] == 'external_settings' &&
		     isset( $_GET['oauth_token'] ) && isset( $_GET['uid'] )
		) {
			echo '<div class="updated"><p>' . sprintf( __( 'Connected to %s', 'ninja-forms-uploads' ), $this->title ) . '</p></div>';
		}
	}

	public function disconnect_notice() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'ninja-forms-uploads' &&
		     isset( $_GET['tab'] ) && $_GET['tab'] == 'external_settings' &&
		     isset( $_GET['action'] ) && $_GET['action'] == 'disconnect_' . $this->slug
		) {
			echo '<div class="updated"><p>' . sprintf( __( 'Disconnected from %s', 'ninja-forms-uploads' ), $this->title ) . '</p></div>';
		}
	}

}