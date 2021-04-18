<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_MergeTags {

	/**
	 * NF_FU_Integrations_NinjaForms_MergeTags constructor.
	 */
	public function __construct() {
		add_filter( 'ninja_forms_merge_tag_value_' . NF_FU_File_Uploads::TYPE, array( $this, 'merge_tag_value' ), 10, 2 );
		add_filter( 'ninja_forms_submission_actions', array( $this, 'update_all_mergetags' ), 10, 3 );
		add_action( 'ninja_forms_uploads_external_action_post_process', array( $this, 'update_mergetags_for_external' ), 10, 3 );

		add_filter( 'ninja_forms_merge_tags_other', array( $this, 'add_merge_tag_other' ) );
		add_filter( 'ninja_forms_get_html_safe_fields', array( $this, 'add_html_safe_fields' ) );
	}

	/**
	 * Update all mergetags with cleaned values.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function update_all_mergetags( $actions, $form_cache, $form_data ) {
		if ( ! isset( $form_data['fields'] ) ) {
			return $actions;
		}

		foreach ( $form_data['fields'] as $field ) {
			if ( ! isset( $field['files'] ) ) {
				continue;
			}

			if ( ! isset( $field['files'][0]['data'] ) || ! isset( $field['files'][0]['data']['upload_id'] ) ) {
				continue;
			}

			$field = $this->normalize_field( $field, $form_data['id'] );

			// Update Mergetags
			$this->update_mergetags( $field, self::get_default_tags() );
		}

		return $actions;
	}

	/**
	 * Format the file URLs to links using the filename as link text
	 *
	 * @param string $value
	 * @param array  $field
	 *
	 * @return string
	 */
	public function merge_tag_value( $value, $field ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		$field = apply_filters( 'ninja_forms_uploads_mergetag_value_field', $field );

		if ( ! isset( $field['files'] ) || empty( $field['files'] ) ) {
			return '';
		}

		if ( ! isset( $field['files'][0]['data'] ) || ! isset( $field['files'][0]['data']['upload_id'] ) ) {
			return '';
		}

		$values = $this->get_values( $field );

		return $values['html'];
	}

	/**
	 * @return array
	 */
	public static function get_default_tags() {
		return array(
			'default'          => 'html',
			'plain'            => 'plain',
			'embed'            => 'embed',
			'link'             => 'link',
			'url'              => 'url',
			'filename'         => 'filename',
			'pdf_embed'        => 'pdf_embed',
			'attachment_id'    => 'attachment_id',
			'attachment_url'   => 'attachment_url',
			'attachment_embed' => 'attachment_embed',
		);
	}

	/**
	 * Update mergetag(s) value
	 *
	 * @param array $field
	 * @param array $tags Array keyed on field suffix ('default' for normal field), and value as the type of value, eg.
	 *                    html or plain
	 */
	public function update_mergetags( $field, $tags = array() ) {
		$all_merge_tags = Ninja_Forms()->merge_tags;

		if ( ! isset( $all_merge_tags['fields'] ) ) {
			return;
		}

		$values = $this->get_values( $field );

		$field['value'] = $values['html'];
		$all_merge_tags['fields']->add_field( $field );

		foreach ( $tags as $type => $value_type ) {
			$tag    = '_' . $type;
			$suffix = ':' . $type;
			if ( 'default' === $type ) {
				$tag    = '';
				$suffix = '';
			}

			$value = isset( $values[ $value_type ] ) ? $values[ $value_type ] : $values['plain'];
			$all_merge_tags['fields']->add( 'field_' . $field['key'] . $tag, $field['key'], "{field:{$field['key']}{$suffix}}", $value, false, false );
		}

		// Save merge tags
		Ninja_Forms()->merge_tags = $all_merge_tags;
	}

	/**
	 * Get the formatted value sets for the mergetag value.
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function get_values( $field ) {
		$values = array();

		if ( empty( $field['files'] ) ) {
			$values['html'][]  = '';
			$values['link'][]  = '';
			$values['embed'][] = '';
			$values['url'][]   = '';
			$values['plain'][] = '';
			$values['pdf_embed'][] = '';
			$values['filename'][] = '';
		}

		foreach ( $field['files'] as $file ) {
			$upload = NF_File_Uploads()->controllers->uploads->get( $file['data']['upload_id'] );

			if ( false === $upload ) {
				continue;
			}

			$file_url = NF_File_Uploads()->controllers->uploads->get_file_url( $upload->file_url, $upload->data );

			$values['html'][]  = sprintf( '<a href="%s" target="_blank">%s</a>', $file_url, $upload->file_name );
			$values['link'][]  = sprintf( '<a href="%s" target="_blank">%s</a>', $file_url, $upload->file_name );
			$values['embed'][] = sprintf( '<img src="%s">', $file_url );
			$values['url'][]   = $file_url;
			$values['plain'][] = $file_url;

			$pdf_file_path = $this->get_file_for_pdf( $upload );

			if ( ! empty( $pdf_file_path ) ) {
				$pdf_file_path = sprintf( '<img src="%s">',$pdf_file_path );
			}

			$values['pdf_embed'][] = $pdf_file_path;

			$values['filename'][] = basename( $file_url );
			if ( isset( $upload->attachment_id ) ) {
				$attachment_url = wp_get_attachment_image_url( $upload->attachment_id, 'full' );
				$values['attachment_id'][]  = $upload->attachment_id;
				$values['attachment_url'][] = $attachment_url;
				$values['attachment_embed'][] = sprintf( '<img src="%s">', $attachment_url );
			}
		}

		if ( isset( $values['html'] ) ) {
			$values['html'] = implode( '<br>', $values['html'] );
		}
		if ( isset( $values['link'] ) ) {
			$values['link'] = implode( '<br>', $values['link'] );
		}
		if ( isset( $values['embed'] ) ) {
			$values['embed'] = implode( '<br>', $values['embed'] );
		}

		if ( isset( $values['plain'] ) ) {
			$values['plain'] = implode( ', ', $values['plain'] );
		}
		if ( isset( $values['url'] ) ) {
			$values['url'] = implode( ', ', $values['url'] );
		}
		if ( isset( $values['filename'] ) ) {
			$values['filename'] = implode( ',', $values['filename'] );
		}

		if ( isset( $values['pdf_embed'] ) ) {
			$values['pdf_embed'] = implode( ',', $values['pdf_embed'] );
		}

		if ( isset( $values['attachment_id'] ) ) {
			$values['attachment_id'] = implode( ',', $values['attachment_id'] );
		}

		if ( isset( $values['attachment_url'] ) ) {
			$values['attachment_url'] = implode( ', ', $values['attachment_url'] );
		}

		if ( isset( $values['attachment_embed'] ) ) {
			$values['attachment_embed'] = implode( '<br>', $values['attachment_embed'] );
		}

		return $values;
	}

	protected function get_file_for_pdf( $upload ) {
		if ( file_exists( $upload->file_path ) ) {
			return $upload->file_path;
		}

		if ( 'server' === $upload->upload_location ) {
			return '';
		}

		if ( ! ( $instance = NF_File_Uploads()->externals->get( $upload->upload_location ) ) ) {
			return '';
		}

		if ( ! $instance->is_connected() ) {
			return '';
		}

		$path     = ( isset( $upload->external_path ) ) ? $upload->external_path : '';
		$filename = ( isset( $upload->external_filename ) ) ? $upload->external_filename : $upload->file_name;
		$file_url = $instance->get_url( $filename, $path, $upload->data );

		$result = file_put_contents( $upload->file_path, file_get_contents( $file_url ) );
		if ( $result ) {
			wp_schedule_single_event( apply_filters( 'ninja_forms_uploads_temp_file_delete_time', time() + HOUR_IN_SECONDS ), 'nf_fu_delete_temporary_file', array( $upload->file_path ) );

			return $upload->file_path;
		}

		return '';
	}

	/**
	 * Update mergetags with external service URL values
	 *
	 * @param array  $field
	 * @param string $service
	 * @param int       $form_id
	 */
	public function update_mergetags_for_external( $field, $service, $form_id ) {
		$tags = array(
			$service            => 'html',
			$service . '_plain' => 'plain',
		);

		$tags = array_merge( $tags, self::get_default_tags() );

		$field = $this->normalize_field( $field, $form_id );

		$this->update_mergetags( $field, $tags );
	}

	/**
	 * Ensure the field array has the key
	 *
	 * @param $field
	 * @param $form_id
	 *
	 * @return mixed
	 */
	public function normalize_field( $field, $form_id ) {
		$fieldModel = Ninja_Forms()->form( $form_id )->get_field( $field['id'] );
		$settings   = $fieldModel->get_settings();
		unset( $settings['files'] );

		$field = array_merge( $field, $settings );

		return $field;
	}

	/**
	 * Add mergetags to the 'Other' section.
	 *
	 * @param array $mergetags
	 *
	 * @return array
	 */
	public function add_merge_tag_other( $mergetags ) {
		$mergetags['fu_date']   = array(
			'id'       => 'fu_date',
			'tag'      => '{other:formatted_date}',
			'label'    => __( 'Date in yyyy-mm-dd format', 'ninja-form-uploads' ),
			'callback' => array( $this, 'replace_merge_tag_date' ),
		);
		$mergetags['fu_year']   = array(
			'id'       => 'fu_year',
			'tag'      => '{other:year}',
			'label'    => __( 'Year in yyyy format', 'ninja-form-uploads' ),
			'callback' => array( $this, 'replace_merge_tag_year' ),
		);
		$mergetags['fu_month']  = array(
			'id'       => 'fu_month',
			'tag'      => '{other:month}',
			'label'    => __( 'Month in mm format', 'ninja-form-uploads' ),
			'callback' => array( $this, 'replace_merge_tag_month' ),
		);
		$mergetags['fu_day']    = array(
			'id'       => 'fu_day',
			'tag'      => '{other:day}',
			'label'    => __( 'Day in dd format', 'ninja-form-uploads' ),
			'callback' => array( $this, 'replace_merge_tag_day' ),
		);
		$mergetags['fu_random'] = array(
			'id'       => 'fu_random',
			'tag'      => '{other:random}',
			'label'    => __( 'Random 5 character string', 'ninja-form-uploads' ),
			'callback' => array( $this, 'replace_merge_tag_random' ),
		);

		return $mergetags;
	}

	public function replace_merge_tag_date() {
		return date( 'Y-m-d' );
	}

	public function replace_merge_tag_year() {
		return date( 'Y' );
	}

	public function replace_merge_tag_month() {
		return date( 'm' );
	}

	public function replace_merge_tag_day() {
		return date( 'd' );
	}

	public function replace_merge_tag_random() {
		return NF_FU_Helper::random_string( 5 );
	}

	/**
	 * Add the field type to the core whitelist so html isn't stripped from mergetags.
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function add_html_safe_fields( $fields ) {
		$fields[] = NF_FU_File_Uploads::TYPE;

		return $fields;
	}
}