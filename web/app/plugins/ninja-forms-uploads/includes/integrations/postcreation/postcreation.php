<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_PostCreation_PostCreation {

	public function __construct() {
		add_filter( 'ninja_forms_create_post_settings', array( $this, 'create_post_settings' ) );
		add_action( 'ninja_forms_create_post', array( $this, 'maybe_add_featured_image' ), 10, 4);
	}

	public function create_post_settings( $action_settings ) {
		$action_settings['featured_image'] = array(
			'name'               => 'featured_image',
			'type'               => 'field-select',
			'field_value_format' => 'key',
			'label'              => __( 'Featured Image', 'ninja-forms-uploads' ),
			'width'              => 'full',
			'group'              => 'advanced',
			'field_types'        => array(
				'file_upload',
			),
			'field_filter'       => array(
				'upload_multi_count' => 1,
			),
			'help' => __( 'Single file upload fields only', 'ninja-forms-uploads' ),
		);

		return $action_settings;
	}

	public function maybe_add_featured_image( $post_id, $action_settings, $form_id, $form_data ) {
		if ( empty( $action_settings['featured_image' ] ) ) {
			return;
		}

		$file_key = $action_settings['featured_image' ];

		$field = false;
		foreach( $form_data['fields'] as $form_field ) {
			if ( $file_key === $form_field['key'] ) {
				$field = $form_field;

				break;
			}
		}

		if ( ! $field || empty( $field['files'] ) ) {
			return;
		}

		$file = $field['files'][0]['data'];

		if ( ! isset( $file['attachment_id'] ) ) {
			$attachment_id = NF_File_Uploads()->controllers->uploads->create_attachment( $file['file_path'], $file['file_name'] );
		} else {
			$attachment_id = $file['attachment_id'];
		}

		update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
	}
}