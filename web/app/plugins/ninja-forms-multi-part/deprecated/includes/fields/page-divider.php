<?php
add_action( 'init', 'ninja_forms_register_field_page_divider', 9 );

function ninja_forms_register_field_page_divider( $form_id = '' ){
	global $ninja_forms_processing;

	$args = array(
		'name' => __( 'Part Settings', 'ninja-forms-mp' ),
		'sidebar' => '',
		'display_function' => '',
		'save_function' => '',
		'group' => '',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_desc' => false,
		'edit_conditional' => true,
		'process_field' => false,
		'li_class' => 'not-sortable',
		'conditional' => array(
			'action' => array(
				'show' => array(
					'name' => __( 'Show This', 'ninja-forms-mp' ),
					'js_function' => 'ninja_forms_show_mp_page',
					'output' => 'show',
				),				
				'hide' => array(
					'name' => __( 'Hide This', 'ninja-forms-mp' ),
					'js_function' => 'ninja_forms_hide_mp_page',
					'output' => 'hide',
				),			
			),
		),
		'show_remove' => false,
		'show_fav' => false,
		'show_field_id' => false,
	);

	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field( '_page_divider', $args );
	}
}

function ninja_forms_field_page_divider_display( $field_id, $data ) {
	global $ninja_forms_loading, $ninja_forms_processing;
	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
	}

	if ( isset( $data['page_name'] ) ) {
		// If we have a 'page_name' set, remove it and set the label instead.
		$data['label'] = $data['page_name'];
		unset( $data['page_name'] );
		// Update our field.
		$data = serialize( $data );
		$args = array(
			'update_array' => array(
				'data' => $data,
			),
			'where' => array(
				'id' => $field_id,
			),
		);

		ninja_forms_update_field( $args );
	}

	$label = isset ( $data['label'] ) ? $data['label'] : '';
	if( isset( $form_data['mp_display_titles'] ) AND $form_data['mp_display_titles'] == 1 ){
		?>
		<h4><?php echo $label;?></h4>
		<?php
	}
}

/**
 * Add our "Duplicate Page" button to the admin editor
 *
 * @since 1.3
 * @return void
 */
function nf_mp_output_copy_page_link( $field_id ) {
	$field = ninja_forms_get_field_by_id( $field_id );
	if ( '_page_divider' == $field['type'] ) {
		?>
		<a href="#" class="mp-copy-page button-secondary" data-field="<?php echo $field_id; ?>"><?php _e( 'Duplicate Part', 'ninja-forms-mp' ); ?></a>
		<?php		
	}
}

add_action( 'ninja_forms_edit_field_before_registered', 'nf_mp_output_copy_page_link', 9 );

/**
 * Add an edit function to convert older versions of MP settings.
 *
 * @since 1.3
 * @return void
 */
function nf_mp_page_update_title( $field_id ) {
	$field = ninja_forms_get_field_by_id( $field_id );
	$data = $field['data'];
	if ( isset( $data['page_name'] ) ) {
		// If we have a 'page_name' set, remove it and set the label instead.
		$data['label'] = $data['page_name'];
		unset( $data['page_name'] );
		// Update our field.
		$data = serialize( $data );
		$args = array(
			'update_array' => array(
				'data' => $data,
			),
			'where' => array(
				'id' => $field_id,
			),
		);

		ninja_forms_update_field( $args );
	}
}

add_action( 'ninja_forms_edit_field_li', 'nf_mp_page_update_title', 5 );

/**
 * Check our option to see if we've updated all of our form settings.
 * If we haven't, then update the form currently being viewed.
 * 
 * @since 1.3.4
 * @return void
 */
function nf_mp_page_update_title_on_form_display( $form_id ) {
	// Bail if we are in the admin
	if ( is_admin() )
		return false;

	$fields = Ninja_Forms()->form( $form_id )->fields;

	foreach ( $fields as $field_id => $field ) {
		if ( ! empty ( $field['data'] ) && ! empty ( $field['data']['page_name'] ) ) {
			$data = $field['data'];
			Ninja_Forms()->form( $form_id )->fields[ $field_id ]['data']['label'] = $data['page_name'];
			
			// If we have a 'page_name' set, remove it and set the label instead.
			$data['label'] = $data['page_name'];
			unset( $data['page_name'] );
			// Update our field.
			$data = serialize( $data );
			$args = array(
				'update_array' => array(
					'data' => $data,
				),
				'where' => array(
					'id' => $field_id,
				),
			);

			ninja_forms_update_field( $args );
		}
	}
}

add_action( 'nf_before_display_loading', 'nf_mp_page_update_title_on_form_display' );