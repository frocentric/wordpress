<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Admin_Upgrade {


	/**
	 * NF_FU_Admin_Upgrade constructor.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_upgrade_settings', array( $this, 'upgrade' ), 99 );
		add_filter( 'ninja_forms_after_upgrade_settings', array( $this, 'after_upgrade' ), 99 );
	}

	/**
	 * Get all the custom action defaults
	 *
	 * @return array
	 */
	protected function new_actions() {
		$actions = array(
			'file-upload-external'      => array(
				'type'   => 'file-upload-external',
				'label'  => 'External File Upload',
				'active' => '1',
			),
		);

		return $actions;
	}

	/**
	 * Upgrade the settings and field settings when NF core is upgraded to 3.0
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function upgrade( $form ) {
		$actions = $this->new_actions();

		foreach ( $form['actions'] as $action_key => $action ) {
			// Email attachments
			$action = $this->email_attachments( $action, $form );

			// Create Post
			$action = $this->create_post( $action, $form );

			$form['actions'][ $action_key ] = $action;
		}

		foreach ( $form['fields'] as $key => $field ) {

			if ( '_upload' !== $field['type'] ) {
				// Not an upload field
				continue;
			}

			// Convert field type
			$field['type'] = NF_FU_File_Uploads::TYPE;

			if ( 0 == $field['data']['upload_multi'] || empty( $field['data']['upload_multi_count'] ) ) {
				// Default for single upload
				$field['data']['upload_multi_count'] = 1;
			}
			unset( $field['data']['upload_multi'] );

			if ( isset( $field['data']['upload_location'] ) ) {
				$location = $field['data']['upload_location'];

				$field['data']['save_to_server'] = 1;
				if ( 'none' === $location ) {
					// No upload location is now an action
					$field['data']['save_to_server'] = false;
				} else if ( 'server' !== $location ) {
					// External location
					$service = 'amazon' === $location ? 's3' : 'dropbox';

					$actions[ 'file-upload-external' ][ $key ] = $service;
				}

				unset( $field['data']['upload_location'] );
			}

			$form['fields'][ $key ] = $field;
		}

		foreach ( $actions as $action_key => $action ) {
			if ( 3 === count( $action ) ) {
				// Remove unused actions
				unset( $actions[ $action_key ] );
			}
		}

		$form['actions'] = array_merge( $form['actions'], array_values( $actions ) );

		return $form;
	}

	/**
	 * Replace old keys with new Field keys
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function after_upgrade( $form ) {
		$actions = array_keys( $this->new_actions() );
		// Replace field IDs with keys in actions
		foreach ( $form['actions'] as $action_key => $action ) {
			if ( 'email' === $action['type'] ) {
				// Email attachments
				foreach ( $form['fields'] as $field_key => $field ) {
					if ( isset( $action[ 'field_list_fu_email_attachments-' . $field_key ] ) ) {
						$new_key = 'field_list_fu_email_attachments-' . $field['key'];

						$action[ $new_key ] = $action[ 'field_list_fu_email_attachments-' . $field_key ];
						unset( $action[ 'field_list_fu_email_attachments-' . $field_key ] );
					}
				}
			} else if ( 'create-post' === $action['type'] ) {
				// Create Post featured image
				foreach ( $form['fields'] as $field_key => $field ) {
					if ( isset( $action['featured_image'] ) && $field_key === $action['featured_image'] ) {
						$action['featured_image'] = $field['key'];
					}
				}
			} else if ( in_array( $action['type'], $actions ) ) {
				// FU actions
				foreach ( $form['fields'] as $field_key => $field ) {
					if ( isset( $action[ $field_key ] ) ) {
						$new_key = 'field_list_' . $action[ $field_key ] . '-' . $field['key'];

						$action[ $new_key ] = '1';
						unset( $action[ $field_key ] );
					}
				}
			}

			$form['actions'][ $action_key ] = $action;
		}


		return $form;
	}

	/**
	 * Add email attachments to the action
	 *
	 * @param array $action
	 * @param array $form
	 *
	 * @return array
	 */
	protected function email_attachments( $action, $form ) {
		if ( 'email' !== $action['type'] ) {
			return $action;
		}

		foreach ( $form['fields'] as $key => $field ) {
			if ( '_upload' !== $field['type'] ) {
				// Not an upload field
				continue;
			}

			if ( isset( $action[ 'file_upload_' . $field['id'] ] ) ) {
				$action[ 'field_list_fu_email_attachments-' . $key ] = $action[ 'file_upload_' . $field['id'] ];
				unset( $action[ 'file_upload_' . $field['id'] ] );
			}
		}

		return $action;
	}

	/**
	 * Add featured_image to Create Post action
	 *
	 * @param array $action
	 * @param array $form
	 *
	 * @return array
	 */
	protected function create_post( $action, $form ) {
		if ( 'create-post' !== $action['type'] ) {
			return $action;
		}

		foreach ( $form['fields'] as $key => $field ) {
			if ( '_upload' !== $field['type'] ) {
				// Not an upload field
				continue;
			}

			if ( isset( $field['data']['featured_image'] ) && 1 == $field['data']['featured_image'] ) {
				$action['featured_image'] = $key;
			}
		}

		return $action;
	}
}