<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_Templates {

	/**
	 * NF_FU_Integrations_NinjaForms_Templates constructor.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_new_form_templates', array( $this, 'register_templates' ) );
	}

	/**
	 * Register Templates
	 *
	 * Registers our custom form templates.
	 *
	 * @param $templates
	 *
	 * @return mixed
	 */
	public function register_templates( $templates ) {
		$templates['file-upload'] = array(
			'id'            => 'file-upload',
			'title'         => __( 'File Upload', 'ninja-forms-uploads' ),
			'template-desc' => __( 'Allow users to upload files using a form. You can add and remove fields as needed.', 'ninja-forms-uploads' ),
			'form'          => $this->form_template(),
		);

		return $templates;
	}

	/**
	 * Form Template
	 *
	 * This method is used to load the form templates
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public static function form_template( $data = array() ) {
		$path = dirname( NF_File_Uploads()->plugin_file_path ) . '/includes/templates/file-upload.nff';

		if ( ! file_exists( $path ) ) {
			return '';
		}

		extract( $data );

		ob_start();

		include $path;

		return ob_get_clean();
	}
}