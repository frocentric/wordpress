<?php

function nf_add_cl_edit_field_section( $sections ) {
	$sections['cl'] = __( 'Conditional Logic Settings', 'ninja-forms-conditionals' );
	return $sections;
}

add_filter( 'nf_edit_field_settings_sections', 'nf_add_cl_edit_field_section' );


add_action('init', 'ninja_forms_register_edit_field_conditional', 999);
function ninja_forms_register_edit_field_conditional(){
	add_action('nf_edit_field_cl', 'ninja_forms_edit_field_conditional', 11);
}

function ninja_forms_edit_field_conditional($field_id){
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_conditional = $reg_field['edit_conditional'];
	if($edit_conditional){
		?>

		<div id="ninja-forms-conditionals">
		<span class="label">
			<a href="#" id="ninja_forms_field_<?php echo $field_id;?>_add_conditional" class="ninja-forms-field-add-conditional button-secondary"><?php _e( 'Add Conditional Statement', 'ninja-forms-conditionals' ); ?></a>
		</span>
		<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[conditional]" value="">
		<div id="ninja_forms_field_<?php echo $field_id;?>_conditionals" class="" name="">
			
		<?php
			if(isset($field_data['conditional']) AND is_array($field_data['conditional'])){
				$x = 0;
				foreach($field_data['conditional'] as $condition){
					ninja_forms_field_conditional_output($field_id, $x, $condition);
					$x++;
				}
			}
		?>
		</div>
		</div>
		<?php
	}
}


function ninja_forms_field_conditional_output($field_id, $x, $condition = '', $ajax = false){
	global $wpdb, $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id($field_id);

	$field_type = $field_row['type'];
	$field_data = $field_row['data'];

	$reg_field = $ninja_forms_fields[$field_type];
	$conditional = $reg_field['conditional'];
	if( isset( $condition['action'] ) ){
		$selected_action = $condition['action'];
	}else{
		$selected_action = '';
	}

	?>
		<div id="ninja_forms_field_<?php echo $field_id;?>_conditional_<?php echo $x;?>" class="ninja-forms-field-<?php echo $field_id;?>-conditional ninja-forms-condition" name="<?php echo $field_id;?>">
		<div class="ninja-forms-condition-title">
		<a href="#" id="ninja_forms_field_<?php echo $field_id;?>_remove_conditional" name="<?php echo $x;?>" class="ninja-forms-field-remove-conditional" title="<?php echo _e( 'Remove condition', 'ninja-forms-conditionals' ); ?>">X</a>
			<select id="ninja_forms_field_<?php echo $field_id;?>_conditional_<?php echo $x;?>_action" name="ninja_forms_field_<?php echo $field_id;?>[conditional][<?php echo $x;?>][action]" class="ninja-forms-field-conditional-action">
				<option value=""><?php _e( '-- Action', 'ninja-forms-conditionals' ); ?></option>
				<?php
				if(isset($conditional['action']) AND is_array($conditional['action'])){
					foreach($conditional['action'] as $slug => $action){
				?>
					<option value="<?php echo $action['js_function'];?>" <?php selected( $action['js_function'], $selected_action ); ?>><?php echo $action['name'];?></option>
				<?php
					}
				}else{
				?>
					<option value="show" <?php selected( $selected_action, 'show' );?>><?php _e( 'Show This', 'ninja-forms-conditionals' ); ?></option>
					<option value="hide" <?php selected( $selected_action, 'hide' );?>><?php _e( 'Hide This', 'ninja-forms-conditionals' ); ?></option>
					<option value="change_value" <?php selected( $selected_action, 'change_value' );?>><?php _e( 'Change Value', 'ninja-forms-conditionals' ); ?></option>
				<?php
				}
				?>
			</select>
			<span id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>_value_span">
				<?php
				if( isset( $conditional['action'][$selected_action] ) ){
					$conditional = $conditional['action'][$selected_action];
				}else if( $selected_action == 'change_value' ){
					$conditional = array( 'output' => 'text' );
				}else{
					$conditional = '';
				}
				ninja_forms_field_conditional_action_output( $field_id, $x, $conditional, $condition, $field_data );
				?>
			</span>
			<?php _e( 'If', 'ninja-forms-conditionals' ); ?>
			<select name="ninja_forms_field_<?php echo $field_id;?>[conditional][<?php echo $x;?>][connector]">
				<option value="and" <?php if(isset($condition['connector']) AND $condition['connector'] == 'and'){ echo 'selected';}?> ><?php _e( 'All', 'ninja-forms-conditionals' ); ?></option>
				<option value="or" <?php if(isset($condition['connector']) AND $condition['connector'] == 'or'){ echo 'selected';}?> ><?php _e( 'Any', 'ninja-forms-conditionals' ); ?></option>
			</select>
			<?php _e( 'of the following critera are met', 'ninja-forms-conditionals' ); ?>: <a href="#" id="ninja_forms_field_<?php echo $field_id;?>_add_cr" name="<?php echo $x;?>" class="ninja-forms-field-add-cr"><?php _e( 'Add Criteria', 'ninja-forms-conditionals' ); ?></a>
		</div>
		<div id="ninja_forms_field_<?php echo $field_id;?>_conditional_<?php echo $x;?>_cr" class="ninja-forms-criteria">
		<?php
			if(isset($condition['cr']) AND is_array($condition['cr'])){
				$y = 0;
				foreach($condition['cr'] as $cr){
					ninja_forms_field_conditional_cr_output($field_id, $x, $y, $cr);
					$y++;
				}
			}
		?>
		</div>
	</div>
	<?php
}

function ninja_forms_field_conditional_cr_output($field_id, $x, $y, $cr = '', $ajax = false){
	global $wpdb, $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$form_id = $field_row['form_id'];
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];

	if( isset( $cr['field'] ) AND $cr['field'] != '' ){
		$selected_row = ninja_forms_get_field_by_id( $cr['field'] );
		$selected_type = $selected_row['type'];
		if(isset($ninja_forms_fields[$selected_type]['conditional'])){
			$conditional = $ninja_forms_fields[$selected_type]['conditional'];
		}
	}
	$field_results = ninja_forms_get_fields_by_form_id( $form_id );

	?>
	<div class="description-wide single-criteria ninja-forms-field-<?php echo $field_id;?>-conditional-<?php echo $x;?>-cr" id="ninja_forms_field_<?php echo $field_id;?>_conditional_<?php echo $x;?>_cr_<?php echo $y;?>">
		&nbsp;&nbsp; <a href="#" id="ninja_forms_field_<?php echo $field_id;?>_remove_cr" class="ninja-forms-field-remove-cr" name="<?php echo $x;?>" rel="<?php echo $y;?>" title="<?php echo esc_html_e( 'Remove criteria', 'ninja-forms-conditionals' ); ?>">X</a> &rarr;
		<select name="ninja_forms_field_<?php echo $field_id;?>[conditional][<?php echo $x;?>][cr][<?php echo $y;?>][field]" class="ninja-forms-field-conditional-cr-field" id="ninja_forms_field_<?php echo $field_id;?>_cr_field" title="<?php echo $x;?>_<?php echo $y;?>">
			<option value=""><?php _e( '-- Field', 'ninja-forms-conditionals' ); ?></option>
				<?php
					if(is_array($field_results)){
						foreach($field_results as $field){
							$this_id = $field['id'];
							$field_data = $field['data'];
							if ( isset ( $field_data['label'] ) ) {
								$label = $field_data['label'];
							} else {
								$label = '';
							}
							$field_type = $field['type'];

							$label = htmlentities( $label );

							if( strlen( $label ) > 30 ){
								$label = substr( $label, 0, 30 ).'...';
							}

							if($ninja_forms_fields[$field_type]['process_field'] AND $this_id != $field_id){
				?>
							<option value="<?php echo $this_id;?>" <?php if(isset($cr['field']) AND $cr['field'] == $this_id){ echo 'selected';} ?>><?php _e( 'ID', 'ninja-forms-conditionals' ); ?>: <?php echo $this_id;?> - <?php echo $label;?></option>
				<?php
							}
						}
					}
				?>
		</select>
		<select name="ninja_forms_field_<?php echo $field_id;?>[conditional][<?php echo $x;?>][cr][<?php echo $y;?>][operator]">
			<option value="==" <?php if(isset($cr['field']) AND $cr['operator'] == '=='){ echo 'selected';} ?>><?php _e( 'Equal To', 'ninja-forms-conditionals' ); ?></option>
			<option value="!=" <?php if(isset($cr['field']) AND $cr['operator'] == '!='){ echo 'selected';} ?>><?php _e( 'Not Equal To', 'ninja-forms-conditionals' ); ?></option>
			<option value="<" <?php if(isset($cr['field']) AND $cr['operator'] == '<'){ echo 'selected';} ?>><?php _e( 'Less Than', 'ninja-forms-conditionals' ); ?></option>
			<option value=">" <?php if(isset($cr['field']) AND $cr['operator'] == '>'){ echo 'selected';} ?>><?php _e( 'Greater Than', 'ninja-forms-conditionals' ); ?></option>
		</select>
		<br /><span id="ninja_forms_field_<?php echo $field_id;?>_conditional_<?php echo $x;?>_cr_<?php echo $y;?>_value" class="">
		<?php
			if(isset($conditional['value']) AND is_array($conditional['value'])){
				ninja_forms_field_conditional_cr_value_output($field_id, $x, $y, $conditional, $cr, $field_data);
			}
		?>
		</span>
	</div>
	<?php
}

function ninja_forms_field_conditional_cr_value_output( $field_id, $x, $y="", $conditional, $cr = '', $field_data = '' ){
	global $wpdb, $ninja_forms_fields;

	if(isset($cr['field'])){
		$cr_field = $cr['field'];
		$cr_row = ninja_forms_get_field_by_id($cr_field);
		$field_data = $cr_row['data'];
	}
	if($y !== ''){
		$name = "ninja_forms_field_".$field_id."[conditional][".$x."][cr][".$y."][value]";
		$id = "ninja_forms_field_".$field_id."_conditional_".$x."_cr_".$y."_value";
		$class = 'ninja-forms-field-conditional-cr-value-list';
	}else{
		$name = "ninja_forms_field_".$field_id."[conditional][".$x."][value]";
		$id = "ninja_forms_field_".$field_id."_conditional_".$x."_value";
		$class = 'ninja-forms-field-'.$field_id.'-conditional-value';
	}

	$conditional = apply_filters( 'nf_change_conditional_cr_field', $conditional, $cr_field );

	if( isset( $conditional['value']['type'] ) ){
		$value_type = $conditional['value']['type'];
	}else{
		$value_type = '';
	}

	switch ( $value_type ) {
		case '':

			break;
		case 'text':
			?>
			<input type="text" name="<?php echo $name;?>" id="<?php echo $id;?>" class="" value="<?php if(isset($cr['value'])){ echo $cr['value']; }?>">
			<?php
			break;
		case 'textarea':
			?>
			<textarea name="<?php echo $name;?>" id="<?php echo $id;?>" class=""><?php if(isset($cr['value'])){ echo $cr['value']; }?></textarea>
			<?php
			break;
		case 'select':
			?>
			<select name="<?php echo $name;?>" id="<?php echo $id;?>">
			<?php
				foreach($conditional['value']['options'] as $name => $value){
			?>
				<option value="<?php echo $value;?>" <?php if(isset($cr['value']) AND $cr['value'] == $value){ echo 'selected';} ?>><?php echo $name;?></option>
			<?php
				}
			?>
			</select>
			<?php
			break;
		case 'list':
			?>
			<select name="<?php echo $name;?>" id="<?php echo $id;?>" class="<?php echo $class;?>">
			<?php
			if( isset( $field_data['list']['options'] ) AND is_array( $field_data['list']['options'] ) ){
				$i = 0;
				if ( ! isset ( $_REQUEST['output_options'] ) || $_REQUEST['output_options'] == 1 ) {
					foreach( $field_data['list']['options'] as $option ){
						if( !isset( $field_data['list_show_value'] ) OR $field_data['list_show_value'] == 0 ){
							$value = $option['label'];
						}else{
							$value = $option['value'];
						}
						?>
						<option value="<?php echo $value;?>" title="<?php echo $i;?>" <?php if(isset($cr['value']) AND $value == $cr['value']){ echo 'selected';}?>><?php echo $option['label'];?></option>
						<?php
						$i++;
					}					
				}
			}
			?>
			</select>
			<?php
			break;
		default:

			break;
	}
}

function ninja_forms_field_conditional_action_output( $field_id, $x, $conditional, $current = '', $field_data = '' ){
	global $wpdb, $ninja_forms_fields;

	$name = "ninja_forms_field_".$field_id."[conditional][".$x."][value]";
	$id = "ninja_forms_field_".$field_id."_conditional_".$x."_value";
	$class = 'ninja-forms-field-'.$field_id.'-conditional-value';

	if( isset( $current['value'] ) ){
		$current_value = $current['value'];
	}else{
		$current_value = '';
	}

	if( isset( $conditional['output'] ) ){
		$action_output = $conditional['output'];
	}else{
		$action_output = '';
	}

	switch ( $action_output ) {
		case '':
			?>
			<input type="hidden" name="<?php echo $name;?>" value="">
			<?php
			break;
		case 'show':
			?>
			<input type="hidden" name="<?php echo $name;?>" value="">
			<?php
			break;
		case 'hide':
			?>
			<input type="hidden" name="<?php echo $name;?>" value="">
			<?php
			break;
		case 'text':
			?>
			<input type="text" name="<?php echo $name;?>" id="<?php echo $id;?>" class="" value="<?php echo $current_value; ?>">
			<?php
			break;
		case 'textarea':
			?>
			<textarea name="<?php echo $name;?>" id="<?php echo $id;?>" class=""><?php echo $current_value; ?></textarea>
			<?php
			break;
		case 'select':
			?>
			<select name="<?php echo $name;?>" id="<?php echo $id;?>">
			<?php
				foreach($conditional['options'] as $name => $value){
			?>
				<option value="<?php echo $value;?>" <?php selected( $current_value, $value ); ?>><?php echo $name;?></option>
			<?php
				}
			?>
			</select>
			<?php
			break;
		case 'list':
			?>
			<select name="<?php echo $name;?>" id="<?php echo $id;?>" class="<?php echo $class;?>">
			<?php
			if(isset($field_data['list']['options']) AND is_array($field_data['list']['options'])){
				$i = 0;
				foreach($field_data['list']['options'] as $option){
					if(!isset($field_data['list_show_value']) OR $field_data['list_show_value'] == 0){
						$value = $option['label'];
					}else{
						$value = $option['value'];
					}
					?>
					<option value="<?php echo $value;?>" title="<?php echo $i;?>" <?php selected( $current_value, $value ); ?>><?php echo $option['label'];?></option>
					<?php
					$i++;
				}
			}
			?>
			</select>
			<?php
			break;
		default:

			//$arguments = func_get_args();
			//array_shift($arguments); // We need to remove the first arg ($function_name)
			$arguments['field_id'] = $field_id;
			$arguments['x'] = $x;
			$arguments['conditional'] = $conditional;
			$arguments['name'] = $name;
			$arguments['id'] = $id;
			$arguments['current'] = $current;
			$arguments['field_data'] = $field_data;

			call_user_func_array($action_output, $arguments);
			break;
	}
}