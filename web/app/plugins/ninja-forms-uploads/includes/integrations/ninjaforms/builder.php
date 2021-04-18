<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_Builder {

	/**
	 * NF_FU_Integrations_NinjaForms_Builder constructor.
	 */
	public function __construct() {
		add_action( 'nf_admin_enqueue_scripts', array( $this, 'add_template' ) );
	}

	/**
	 * Load the field template and styles for the Builder to render.
	 */
	public function add_template() {
		NF_File_Uploads()->template( 'fields-file_upload.html' );

		$ver = NF_File_Uploads()->plugin_version;
		$url = plugin_dir_url( NF_File_Uploads()->plugin_file_path );
		wp_enqueue_style( 'nf-fu-jquery-fileupload', $url . 'assets/css/file-upload.css', array(), $ver );
	}


}