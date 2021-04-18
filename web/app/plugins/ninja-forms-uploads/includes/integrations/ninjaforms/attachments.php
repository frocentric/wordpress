<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_Attachments {

	/**
	 * NF_FU_Integrations_NinjaForms_Attachments constructor.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_action_email_settings', array( $this, 'email_settings' ) );
		add_filter( 'ninja_forms_action_email_attachments', array( $this, 'attach_files' ), 10, 3 );
	}

	/**
	 * Allow uploads to be attached to email actions
	 *
	 * @param array $settings
	 *
	 * @return array $settings
	 */
	public function email_settings( $settings ) {
		$form_id = filter_input( INPUT_GET, 'form_id', FILTER_VALIDATE_INT );

		if ( empty ( $form_id ) ) {
			return $settings;
		}

		if ( ! $this->form_has_file_uploads( $form_id ) ) {
			return $settings;
		}

		$settings['field_list_fu_email_attachments'] = array(
			'name'        => 'field_list_fu_email_attachments',
			'type'        => 'field-list',
			'label'       => __( 'Attach File Uploads', 'ninja-forms-uploads' ),
			'width'       => 'full',
			'group'       => 'advanced',
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

		return $settings;
	}

	/**
	 * Has the form got file upload fields
	 *
	 * @param $form_id
	 *
	 * @return bool
	 */
	protected function form_has_file_uploads( $form_id ) {
		foreach ( Ninja_Forms()->form( $form_id )->get_fields() as $field_id => $field ) {
			if ( ! is_object( $field ) || ! method_exists( $field, 'get_settings' ) ) {
				continue;
			}

			$get_settings = $field->get_settings();
			if ( NF_FU_File_Uploads::TYPE == $get_settings['type'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Attach file uploads to the email
	 *
	 * @param array $attachments
	 * @param array $data
	 * @param array $settings
	 *
	 * @return array
	 */
	public function attach_files( $attachments, $data, $settings ) {
		foreach ( $settings as $key => $value ) {
			if ( false === strpos( $key, 'field_list_fu_email_attachments-' ) || 1 != $value ) {
				continue;
			}

			if ( ! isset( $data['fields'] ) ) {
				continue;
			}

			$field_key = str_replace( 'field_list_fu_email_attachments-', '', $key );
			foreach ( $data['fields'] as $field ) {
				if ( $field_key != $field['key'] ) {
					continue;
				}

				foreach ( $field['files'] as $file ) {
					$attachments[] = $file['data']['file_path'];
				}
			}
		}

		return $attachments;
	}
}