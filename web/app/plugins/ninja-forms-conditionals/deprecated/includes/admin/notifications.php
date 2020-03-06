<?php
function nf_cl_notification_settings( $id ) {
	$conditions = nf_cl_get_conditions( $id );
	if ( empty ( $conditions ) ) {
		$add_button_style = '';
	} else {
		$add_button_style = 'display:none;';
	}
	?>
	<tbody id="nf-conditions">
		<tr>
			<th scope="row"><?php _e( 'Conditional Processing', 'ninja-forms-conditionals' ); ?></label></th>
			<td>
				<div id="nf_cl_conditions">
					<a href="#" class="nf-cl-add button-secondary add-condition" style="<?php echo $add_button_style; ?>"><div class="dashicons dashicons-plus-alt"></div> <?php _e( 'Add', 'ninja-forms-conditionals' ); ?></a>
				</div>
			</td>
		</tr>
	</tbody>

	<script type="text/html" id="tmpl-nf-cl-condition">
		<div id="nf_cl_condition_<#= cond_id #>" class="nf-cl-condition" name=""><!-- Opening Conditional Logic Div -->
			<div class="nf-cl-condition-title">
				<select id="" name="conditions[<#= cond_id #>][action]" class="nf-cl-conditional-action">
					<option value="process" <# if ( 'process' == action ) { #> selected="selected" <# } #>><?php _e( 'Process This', 'ninja-forms-conditionals' ); ?></option>
					<option value="noprocess" <# if ( 'noprocess' == action ) { #> selected="selected" <# } #>><?php _e( 'Do Not Process This', 'ninja-forms-conditionals' ); ?></option>
				</select>
				<?php _e( 'If', 'ninja-forms-conditionals' ); ?>
				<select name="conditions[<#= cond_id #>][connector]">
					<option value="and" <# if ( 'and' == connector ) { #> selected="selected" <# } #>><?php _e( 'All', 'ninja-forms-conditionals' ); ?></option>
					<option value="or" <# if ( 'or' == connector ) { #> selected="selected" <# } #>><?php _e( 'Any', 'ninja-forms-conditionals' ); ?></option>
				</select>
				<?php _e( 'of the following criteria are met', 'ninja-forms-conditionals' ); ?>: <a href="#" id="" name="" class="button-secondary nf-cl-add add-cr" data-cond-id="<#= cond_id #>"><div class="dashicons dashicons-plus-alt" data-cond-id="<#= cond_id #>"></div><span class="spinner" style="float:left;"></span> <?php _e( 'Add Criteria', 'ninja-forms-conditionals' ); ?></a>
				<a href="#" class="nf-cl-delete delete-condition" style=""><div class="dashicons dashicons-dismiss"></div></a>
			</div>
			<div id="" class="nf-cl-criteria"></div>
		</div> <!-- Close Conditional Logic Div -->
	</script>

	<script type="text/html" id="tmpl-nf-cl-criteria">
		<div class="single-criteria" id="<#= div_id #>">
			<a href="#" class="nf-cl-delete delete-cr" style=""><div class="dashicons dashicons-dismiss"></div></a>
			<select name="<#= cr_name #>[param]" class="cr-param" id="" title="" data-cr-id="<#= cr_id #>" data-num="<#= num #>" data-cond-id="<#= cond_id #>">
				<option value="">-- <?php _e( 'Select One', 'ninja-forms-conditionals' ); ?></option>
				<#
				_.each( param_groups, function( params, group_label ) {
					#>
					<optgroup label="<#= group_label #>">
						<#
						_.each( params, function( param ) {
							if ( selected_param == param.id ) {
								var selected = 'selected="selected"';
							} else {
								var selected = '';
							}
							#>
							<option value="<#= param.id #>" <#= selected #>><#= param.label #></option>
							<#
						})
						#>
					</optgroup>
					<#
				});
				#>
			</select>
			<span class="cr-compare"></span>
			<span class="cr-value"></span>
		</div>
	</script>

	<script type="text/html" id="tmpl-nf-cl-criteria-compare">
		<select name="<#= cr_name #>[compare]">
			<#
			var param = nf_cl.getParam( selected_param );
			if ( param !== null ) {
				_.each( param.compare, function( value, key ) {
					#>
					<option value="<#= key #>" <# if ( compare == key ) { #>selected="selected"<# }  #>><#= value #></option>
					<#
				} );
			}
			#>
		</select>
	</script>	

	<script type="text/html" id="tmpl-nf-cl-criteria-value">
		<#
		var param = nf_cl.getParam( selected_param );
		if ( param !== null ) {
			var type = param.conditions.type;
			if ( type == 'text' ) {
				#>
				<input type="text" name="<#= cr_name #>[value]" value="<#= value #>">
				<#
			} else if ( type == 'select' ) {
				#>
				<select name="<#= cr_name #>[value]">
					<#
					_.each( param.conditions.options, function( opt, key ) {
						// If we don't have an opt.value, then use the opt var.
						if ( 'undefined' == typeof opt.value ) {
							var v = opt;
							var l = key;
							opt = {};
							opt.label = l;
							opt.value = v;
						}

						if ( value == opt.value ) {
							var selected = 'selected="selected"';
						} else {
							var selected = '';
						}
						#>
						<option value="<#= opt.value #>" <#= selected #>><#= opt.label #></option>
						<#
					});
					#>
				</select>
				<#
			} else if ( type == 'textarea' ) {
				#>
				<textarea name="<#= cr_name #>[value]" style="vertical-align:top"><#= value #></textarea>
				<#
			}
		}
		#>
	</script>

	<?php
}

add_action( 'nf_edit_notification_settings', 'nf_cl_notification_settings' );

/**
 * Hook into our notification save action
 *
 * @since 1.2.8
 * @return void
 */
function nf_cl_save_notification( $n_id, $data, $new ) {
	// Bail if we don't have any conditional data.
	if ( ! isset ( $data['conditions'] ) || ! is_array ( $data['conditions'] ) ) {
		// Loop through our current conditions and remove any that weren't sent.
		$conditions = nf_cl_get_conditions( $n_id );

		foreach ( $conditions as $cond_id ) {
			nf_cl_delete_condition( $cond_id );
		}
		return false;	
	}

	// Loop through our current conditions and remove any that weren't sent.
	$conditions = nf_cl_get_conditions( $n_id );

	foreach ( $conditions as $cond_id ) {
		if ( ! isset ( $data['conditions'][ $cond_id ] ) ) {
			nf_cl_delete_condition( $cond_id );
		}
	}
	
	// $data['conditions'] will store all the information about our conditions.
	foreach ( $data['conditions'] as $cond_id => $d ) { // Loop through our conditions and save the data.
		if ( 'new' == $cond_id ) {
			// If we are creating a new condition, insert it and grab the id.
			$cond_id = nf_cl_insert_condition( $n_id );
		}

		// Delete criteria that has been removed.
		$criteria = nf_cl_get_criteria( $cond_id );
		foreach ( $criteria as $cr_id ) {
			if ( ! isset ( $d['criteria'][ $cr_id ] ) ) {
				nf_delete_object( $cr_id );
			}
		}

		// Loop through any new criteria.
		if ( isset ( $d['criteria']['new'] ) ) {
			foreach ( $d['criteria']['new'] as $cr ) {
				$cr_id = nf_cl_insert_criteria( $cond_id );
				foreach ( $cr as $key => $value ) {
					// Insert our meta values
					nf_update_object_meta( $cr_id, $key, $value );
				}
			}
			unset( $d['criteria']['new'] );
		}

		if ( isset ( $d['criteria'] ) ) {
			foreach ( $d['criteria'] as $cr_id => $cr ) {
				foreach ( $cr as $key => $value ) {
					nf_update_object_meta( $cr_id, $key, $value );
				}					
			}
			unset ( $d['criteria'] );
		}

		// Save our other condition values.
		foreach ( $d as $key => $value ) {
			nf_update_object_meta( $cond_id, $key, $value );
		}
	}	

}

add_action( 'nf_save_notification', 'nf_cl_save_notification', 10, 3 );

/**
 * Hook into processing and modify our notifications
 *
 * @since 1.2.8
 * @return void
 */
function nf_cl_notification_process( $id ) {
	global $ninja_forms_processing;

	// Check to see if this notification is active. If it isn't, we don't want to check anything else.
	if ( ! Ninja_Forms()->notification( $id )->active )
		return false;

	// Check to see if we have any conditions on this notification
	$conditions = nf_cl_get_conditions( $id );

	if ( empty ( $conditions ) || ! is_array ( $conditions ) )
		return false;

	foreach ( $conditions as $cond_id ) {
		// Grab our action
		$action = nf_get_object_meta_value( $cond_id, 'action' );
		// Grab our connector
		$connector = nf_get_object_meta_value( $cond_id, 'connector' );
		// Grab our criteria
		$criteria = nf_cl_get_criteria( $cond_id );
		$pass_array = array();
		foreach ( $criteria as $cr_id ) {
			$param = nf_get_object_meta_value( $cr_id, 'param' );
			$compare = nf_get_object_meta_value( $cr_id, 'compare' );
			$value = nf_get_object_meta_value( $cr_id, 'value' );

			if ( isset ( Ninja_Forms()->cl_triggers[ $param ] ) ) {
				$pass_array[] = Ninja_Forms()->cl_triggers[ $param ]->compare( $value, $compare );
			} else {
				$user_value = $ninja_forms_processing->get_field_value( $param );
				$pass_array[] = ninja_forms_conditional_compare( $user_value, $value, $compare );
			}
		}

		// Check our connector. If it is set to "all", then all our criteria have to match.
		if ( 'and' == $connector ) {
			$pass = true;
			foreach ( $pass_array as $p ) {
				if ( ! $p ) {
					$pass = false;
					break;
				}
			}
		} else { // If our connector is set to "any", then only one criteria has to match.
			$pass = false;
			foreach ( $pass_array as $p ) {
				if ( $p ) {
					$pass = true;
					break;
				}
			}
		}

		if ( $pass ) {
			if ( 'process' == $action ) {
				Ninja_Forms()->notification( $id )->active = true;
			} else if ( 'noprocess' == $action ) {
				Ninja_Forms()->notification( $id )->active = false;
			}
		} else {
			if ( 'process' == $action ) {
				Ninja_Forms()->notification( $id )->active = false;
			} else if ( 'noprocess' == $action ) {
				Ninja_Forms()->notification( $id )->active = true;
			}
		}
	}
}

add_action( 'nf_notification_before_process', 'nf_cl_notification_process' );

/**
 * Filter our form export.
 */
function nf_cl_form_export( $form_row ) {
	// Make sure that this form has notifications on it.
	if ( isset ( $form_row['notifications'] ) ) {
		// Loop through our notifications and check conditions.
		foreach ( $form_row['notifications'] as $id => $notification ) {
			$conditions = nf_cl_get_conditions( $id );
			// Make sure that we actually notifications to connect.
			if ( empty ( $conditions ) )
				continue;

			$c_array = array(); // Stores all of our conditions.
			// Loop over each condition.
			foreach ( $conditions as $c_id ) {
				// Grab the criteria ids for this condition.
				$criteria = nf_cl_get_criteria( $c_id );
				$cr_array = array(); // Stores all of our criteria.
				// Loop through our criteria and populate our criteria array
				foreach ( $criteria as $cr_id ) {
					// Grab our three criteria settings.
					$cr_array[] = array(
						'param' 	=> nf_get_object_meta_value( $cr_id, 'param' ),
						'compare' 	=> nf_get_object_meta_value( $cr_id, 'compare' ),
						'value'		=> nf_get_object_meta_value( $cr_id, 'value' ),
					);
				}
				// Add the criteria to the condition array.
				$c_array[] = array( 'action' => nf_get_object_meta_value( $c_id, 'action' ), 'connector' => nf_get_object_meta_value( $c_id, 'connector' ), 'criteria' => $cr_array );
			}
			$form_row['notifications'][ $id ]['conditions'] = $c_array;
		}
	}

	return $form_row;
}

add_filter( 'nf_export_form_row', 'nf_cl_form_export' );

/**
 * Change the field IDs in our notification conditions after we import a form.
 */
function nf_cl_form_import( $n, $n_id, $form ) {
	
	// Bail if we don't have any fields set.
	if ( ! isset ( $form['field'] ) || empty ( $form['field'] ) )
		return $n;

	$fields = array();
	foreach ( $form['field'] as $field ) {
		$old_id = $field['old_id'];
		$fields[ $old_id ] = $field['id'];
	}

	// Make sure we have some conditions.
	if ( isset ( $n['conditions'] ) ) {
		// Loop through our conditions, change the field ids, and insert them into the database.
		foreach ( $n['conditions'] as $condition ) {
			// Make sure that we have criteria set.
			if ( ! isset ( $condition['criteria'] ) || empty( $condition['criteria'] ) )
				continue; // There isn't any criteria set. Skip to the next condition.
			
			// Insert our condition
			$c_id = nf_cl_insert_condition( $n_id );

			// Update our condition meta
			nf_update_object_meta( $c_id, 'action', $condition['action'] );
			nf_update_object_meta( $c_id, 'connector', $condition['connector'] );

			// Change our field ids.
			// Loop through our criteria and search for field ids

			foreach ( $condition['criteria'] as $cr ) {
				// First, we check the param to see if it is a field id.
				if ( isset ( $fields[ $cr['param'] ] ) )
					$cr['param'] = $fields[ $cr['param'] ];

				// Next, check to see if the value is a field id
				if ( isset ( $fields[ $cr['value'] ] ) )
					$cr['value'] = $fields[ $cr['param'] ];

				// Insert our criteria
				$cr_id = nf_cl_insert_criteria( $c_id );

				// Update our criteria object meta.
				nf_update_object_meta( $cr_id, 'param', $cr['param'] );
				nf_update_object_meta( $cr_id, 'compare', $cr['compare'] );
				nf_update_object_meta( $cr_id, 'value', $cr['value'] );

			}

		}

		unset( $n['conditions'] );
	}

	return $n;
}

add_filter( 'nf_import_notification_meta', 'nf_cl_form_import', 10, 3 );