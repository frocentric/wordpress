<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_External_Action
 */
class NF_FU_External_Action extends NF_Abstracts_Action {

	/**
	 * @var string
	 */
	protected $_name = 'file-upload-external';

	/**
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * @var string
	 */
	protected $_timing = 'normal';

	/**
	 * @var int
	 */
	protected $_priority = '9';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'External File Upload', 'ninja-forms-uploads' );

		$this->build_settings();
	}

	/**
	 * Load settings for the action
	 */
	protected function build_settings() {
		$settings = array();
		$external = NF_File_Uploads()->externals;
		$services = $external->get_services();
		foreach ( $services as $service ) {
			if ( ! ( $instance = $external->get( $service, $services ) ) ) {
				continue;
			}

			if ( $instance->is_compatible() && $instance->is_connected() ) {

				$settings[ 'field_list_' . $service ] = array(
					'name'        => 'field_list_' . $service,
					'type'        => 'field-list',
					'label'       => $external->get( $service )->name,
					'width'       => 'full',
					'group'       => 'primary',
					'field_types' => array( NF_FU_File_Uploads::TYPE ),
					'settings'    => array(
						array(
							'name'  => 'toggle',
							'type'  => 'toggle',
							'label' => __( 'Field', 'ninja-forms-uploads' ),
							'width' => 'full',
						),
					),
				);
			}
		}

		$settings['background_upload'] = array(
			'name'  => 'background_upload',
			'type'  => 'toggle',
			'group' => 'advanced',
			'label' => esc_html__( 'Background Upload', 'ninja-forms-uploads' ),
			'width' => 'full',
		);

		$this->_settings = array_merge( $this->_settings, $settings );
	}

	/**
	 * Process the upload to the service for those files selected in the action
	 *
	 * @param array $action_settings
	 * @param int   $form_id
	 * @param array $data
	 *
	 * @return array
	 */
	public function process( $action_settings, $form_id, $data ) {
		$upload_timestamp = time();

		$services = NF_File_Uploads()->externals->get_services();

		foreach ( $data['fields'] as $key => $field ) {
			if ( NF_FU_File_Uploads::TYPE !== $field['type'] ) {
				continue;
			}

			if ( ! isset( $field['files'] ) ||  empty( $field['files'] )) {
				continue;
			}

			$remove_from_server = isset( $field['save_to_server'] ) && "1" != $field['save_to_server'];
			$background_upload  = isset( $action_settings['background_upload'] ) ? (bool) $action_settings['background_upload'] : false;

			foreach ( $services as $service ) {
				$field_key = 'field_list_' . $service . '-' . $field['key'];

				if ( ! isset( $action_settings[ $field_key ] ) || 1 != $action_settings[ $field_key ] ) {
					continue;
				}

				foreach ( $field['files'] as $files_key => $file ) {
					if ( ! ( $instance = NF_File_Uploads()->externals->get( $service, $services ) ) ) {
						continue;
					}

					if ( ! $instance->is_connected() ) {
						continue;
					}

					$file['data'] = $instance->process_upload( $file['data'], $remove_from_server, $upload_timestamp, $field, $form_id, $background_upload );

					$field['files'][ $files_key ] = $file;
				}

				$data['fields'][ $key ] = $field;

				do_action( 'ninja_forms_uploads_external_action_post_process', $field, $service, $form_id );
			}
		}

		return $data;
	}
}