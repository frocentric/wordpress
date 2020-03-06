<?php
add_action( 'admin_enqueue_scripts', 'ninja_forms_conditionals_admin_js', 10, 2 );
function ninja_forms_conditionals_admin_js( $page ){
	global $ninja_forms_fields;

	if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'ninja-forms' && isset ( $_REQUEST['tab'] ) && $_REQUEST['tab'] != '' ){

		$form_id = isset ( $_REQUEST['form_id'] ) ? $_REQUEST['form_id'] : '';

		if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
			$suffix = '';
			$src = 'dev';
		} else {
			$suffix = '.min';
			$src = 'min';
		}

		wp_enqueue_script( 'nf-cl-admin',
			NINJA_FORMS_CON_URL .'/js/' . $src . '/ninja-forms-conditionals-admin' . $suffix . '.js?nf_ver=' . NINJA_FORMS_CON_VERSION,
			array( 'jquery', 'ninja-forms-admin', 'backbone', 'underscore' ) );

		if ( empty ( $form_id ) )
			return false;

		$fields = Ninja_Forms()->form( $form_id )->fields;

		/**
		 * We need to localize our script so that we have the appropriate JSON values to work with our backbone/underscore templates.
		 * First, we'll get a list of conditionals currently on this object.
		 * We need to check and see if we are on a notification page or editing a form.
		 */
		$conditions_json = array();
		if ( isset ( $_REQUEST['notification-action'] ) && 'edit' == $_REQUEST['notification-action'] ) {
			$n_id = isset ( $_REQUEST['id'] ) ? $_REQUEST['id'] : '';
			if ( ! empty ( $n_id ) ) {
				$conditionals = nf_cl_get_conditions( $n_id );
				foreach ( $conditionals as $cond_id ) {
					$action = nf_get_object_meta_value( $cond_id, 'action' );
					$criteria = nf_cl_get_criteria( $cond_id );
					$criteria_json = array();
					foreach ( $criteria as $cr_id ) {
						$selected_param = nf_get_object_meta_value( $cr_id, 'param' );
						$compare = nf_get_object_meta_value( $cr_id, 'compare' );
						$value = nf_get_object_meta_value( $cr_id, 'value' );
						$criteria_json[] = array( 'id' => $cr_id, 'param' => $selected_param, 'compare' => $compare, 'value' => $value );
					}
					$connector = nf_get_object_meta_value( $cond_id, 'connector' );
					$conditions_json[ $cond_id ] = array( 'id' => $cond_id, 'action' => $action, 'connector' => $connector, 'criteria' => $criteria_json );
				}
			}
		}

		/**
		 * Now we get a list of all of our fields and their conditional values.
		 * $cl_fields will hold our fields and their labels.
		 * $field_conditions will hold our field type conditional settings.
		 */
		$cl_fields = array();
		$field_conditions = array();
		foreach ( $fields as $field ) {
			$field_type = $field['type'];
			$field_id = $field['id'];
			if ( isset ( $ninja_forms_fields[ $field_type ]['process_field'] ) && $ninja_forms_fields[ $field_type ]['process_field'] ) {
				$label = nf_get_field_admin_label( $field_id );
				$con_value = isset ( $ninja_forms_fields[ $field_type ]['conditional']['value'] ) ? $ninja_forms_fields[ $field_type ]['conditional']['value'] : array( 'type' => 'text' );
				$compare = array( 
					'==' 			=> __( 'Equal To', 'ninja-forms-conditionals' ),
					'!=' 			=> __( 'Not Equal To', 'ninja-forms-conditionals' ),
					'<' 			=> __( 'Less Than', 'ninja-forms-conditionals' ),
					'>'				=> __( 'Greater Than', 'ninja-forms-conditionals' ),
					'contains'		=> __( 'Contains', 'ninja-forms-conditionals' ),
					'notcontains'	=> __( 'Does Not Contain', 'ninja-forms-conditionals' ),
					'on'			=> __( 'On', 'ninja-forms-conditionals' ),
					'before'		=> __( 'Before', 'ninja-forms-conditionals' ),
					'after'			=> __( 'After', 'ninja-forms-conditionals' ),
				);
				$type = $con_value['type'];
				if ( 'list' == $type ) {
					if ( isset ( $field['data']['list']['options'] ) && is_array ( $field['data']['list']['options'] ) ) {
						$list_options = array();
						foreach ( $field['data']['list']['options'] as $opt ) {
							$opt_label = $opt['label'];
							$opt_value = $opt['value'];
							if ( ! isset ( $field['data']['list_show_value'] ) || $field['data']['list_show_value'] != 1 ) {
								$opt_value = $opt['label'];
							}
							$list_options[] = array( 'value' => $opt_value, 'label' => $opt_label );
						}
						$con_value = array( 'type' => 'select', 'options' => $list_options );
					}

					unset( $compare['contains'] );
					unset( $compare['notcontains'] );
					unset( $compare['on'] );
					unset( $compare['before'] );
					unset( $compare['after'] );

				} else if ( '_checkbox' == $field_type ) {
					$options[] = array( 'value' => 'checked', 'label' => __( 'Checked', 'ninja-forms' ) );
					$options[] = array( 'value' => 'unchecked', 'label' => __( 'Unchecked', 'ninja-forms' ) );
					$con_value = array( 'type' => 'select', 'options' => $options );

					unset( $compare['<'] );
					unset( $compare['>'] );
					unset( $compare['contains'] );
					unset( $compare['notcontains'] );
					unset( $compare['on'] );
					unset( $compare['before'] );
					unset( $compare['after'] );
				} else if ( '_text' == $field_type ) {
					if ( isset ( $field['data']['datepicker'] ) && $field['data']['datepicker'] == 1 ) {
						$field_type = 'date';
						unset( $compare['=='] );
						unset( $compare['!='] );
						unset( $compare['<'] );
						unset( $compare['>'] );
						unset( $compare['contains'] );
						unset( $compare['notcontains'] );
					} else {
						unset( $compare['on'] );
						unset( $compare['before'] );
						unset( $compare['after'] );
					}
				}
				$compare = apply_filters( 'nf_cl_compare_array', $compare, $field_id );
				$cl_fields[] = array( 'id' => $field_id, 'label' => $label . ' ID - ' . $field_id, 'conditions' => $con_value, 'compare' => $compare, 'type' => $field_type );
			}
		}

		$cl_fields = apply_filters( 'nf_cl_criteria_fields', $cl_fields );

		usort( $cl_fields, 'nf_cl_sort_by_label' );
		
		$triggers = array();

		if ( isset ( Ninja_Forms()->cl_triggers ) ) {
			foreach ( Ninja_Forms()->cl_triggers as $slug => $trigger ) {
				$triggers[] = array(
					'id' 			=> $slug,
					'label' 		=> $trigger->label,
					'type'			=> $trigger->type,
					'compare'		=> $trigger->comparison_operators,
					'conditions'	=> $trigger->conditions,
				);
			}			
		}

		$cr_param_groups = apply_filters( 'nf_cl_criteria_param_groups', array(
			__( 'Triggers', 'ninja-forms-conditionals' ) 	=> $triggers,
			__( 'Fields', 'ninja-forms-conditionals' ) 		=> $cl_fields,
		) );

		wp_localize_script( 'nf-cl-admin', 'nf_cl', array( 'cr_param_groups' => $cr_param_groups, 'conditions' => $conditions_json ) );
	}
}

add_action( 'admin_enqueue_scripts', 'ninja_forms_conditionals_admin_css' );
function ninja_forms_conditionals_admin_css(){
	if( isset( $_REQUEST['page'] ) AND $_REQUEST['page'] == 'ninja-forms' ){
		wp_enqueue_style('ninja-forms-conditionals-admin', NINJA_FORMS_CON_URL .'/css/ninja-forms-conditionals-admin.css?nf_ver=' . NINJA_FORMS_CON_VERSION, array( 'ninja-forms-admin' ) );
	}
}