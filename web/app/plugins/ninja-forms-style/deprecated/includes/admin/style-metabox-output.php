<?php

function ninja_forms_style_metabox_output( $metabox ){
	global $ninja_forms_css_options;

	$tr_class = 'advanced';

	$plugin_settings = get_option( 'ninja_forms_settings' );
	if( isset( $plugin_settings['style']['advanced'] ) AND $plugin_settings['style']['advanced'] == 1 ){
		$tr_class .= '';	
	}else{
		$tr_class .= ' hidden';
	}

	$css_options = $ninja_forms_css_options;

	$basic_array = array();
	$adv_array = array();
	foreach( $css_options as $key => $option ){
		if( $option['group'] == 'basic' ){
			$basic_array[$key] = $option;
		}else if( $option['group'] == 'advanced' ){
			$adv_array[$key] = $option;
		}
	}

	$metabox['tr_class'] = '';
	$metabox['options'] = $basic_array;

	ninja_forms_style_metabox_options_output( $metabox );

	$metabox['tr_class'] = $tr_class;
	$metabox['options'] = $adv_array;
	
	ninja_forms_style_metabox_options_output( $metabox );
}

function ninja_forms_style_metabox_options_output( $metabox ){
	$options = $metabox['options'];

	if( is_array( $options ) AND !empty( $options ) ){

		$page = $metabox['save_page'];
		$group = $metabox['slug'];
		$tr_class = $metabox['tr_class'];

		if( isset( $metabox['field_id'] ) ){
			$field_id = $metabox['field_id'];
		}else{
			$field_id = '';
		}				

		if( isset( $metabox['form_id'] ) ){
			$form_id = $metabox['form_id'];
		}else{
			$form_id = '';
		}		

		if( isset( $metabox['field_type'] ) ){
			$field_type = $metabox['field_type'];
		}else{
			$field_type = '';
		}

		if( isset( $metabox['css_exclude'] ) AND is_array( $metabox['css_exclude'] ) ){
			foreach( $options as $key => $val ){
				if( in_array($key, $metabox['css_exclude'] ) ){
					unset( $options[$key] );
				}
			}
		}

		$plugin_settings = get_option( 'ninja_forms_settings' );
	
		switch( $page ){
			case 'form':
				$form_row = ninja_forms_get_form_by_id( $form_id );
				$form_data = $form_row['data'];
				if( isset( $form_data['style']['groups'] ) ){
					$default_style = $form_data['style']['groups'];
				}else{
					$default_style = '';
				}
				
				break;
			case 'field':
				$field_row = ninja_forms_get_field_by_id( $field_id );
				$field_data = $field_row['data'];
				if( isset( $field_data['style']['groups'] ) ){
					$default_style = $field_data['style']['groups'];
				}else{
					$default_style = '';
				}
				break;
			case 'field_type':
				if( isset( $plugin_settings['style'][$page][$field_type] ) ){
					$default_style = $plugin_settings['style'][$page][$field_type];
				}else{
					$default_style = '';
				}
				break;
			default:
				if( isset( $plugin_settings['style'][$page] ) ){
					$default_style = $plugin_settings['style'][$page];
				}else{
					$default_style = '';
				}
				break;
		}


		

		foreach( $options as $key => $option ){
			$name = $group.'['.$key.']';

			if( isset( $option['default_value'] ) ){
				$value = $option['default_value'];
			}else{
				$value = '';
			}

			if( isset( $default_style[$group][$key] ) ){
				$value = $default_style[$group][$key];
			}

			$type = $option['type'];
			$label = $option['label'];

			if( isset( $option['help'] ) ){
				$help_text = $option['help'];
			}else{
				$help_text = '';
			}

			if( isset( $option['class'] ) ){
				$class = $option['class'];
			}else{
				$class = '';
			}

			if( isset( $option['desc'] ) ){
				$desc = $option['desc'];
			}else{
				$desc = '';
			}

			if($type == 'text'){ 
				$value = ninja_forms_esc_html_deep( $value );
				?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<?php echo $label; ?>
					</th>
					<td>
						<input type="text" class="code widefat <?php echo $class;?>" name="<?php echo $name;?>" id="" value="<?php echo $value;?>" />
						<?php if($help_text != ''){ ?>
						<a href="#" class="tooltip">
						    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
						    <span>
						        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
						        <?php echo $help_text;?>
						    </span>
						</a>
						<?php } ?>
					</td>
				<?php
			}elseif($type == 'select'){ ?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<?php echo $label; ?>
					</th>
					<td>
						<select name="<?php echo $name;?>" class="<?php echo $class;?>">
							<?php
							if(is_array($option['options']) AND !empty($option['options'])){
								foreach($option['options'] as $o){
									?>
									<option value="<?php echo $o['value'];?>" <?php selected($value, $o['value']); ?>><?php echo $o['name'];?></option>
									<?php
								}
							} ?>
						</select>
						<?php if($help_text != ''){ ?>
							<a href="#" class="tooltip">
							    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
							    <span>
							        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
							        <?php echo $help_text;?>
							    </span>
							</a>
						<?php } ?>
					</td>
				</tr>
				<?php
			}elseif($type == 'checkbox'){ ?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<label for="<?php echo $name;?>"><?php echo $label;?></label>
					</th>
					<td>
						<input type="hidden" name="<?php echo $name;?>" value="0">
						<input type="checkbox" name="<?php echo $name;?>" value="1" <?php checked($value, 1);?> id="<?php echo $name;?>" class="<?php echo $class;?>">
						<?php if($help_text != ''){ ?>
							<a href="#" class="tooltip">
							    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
							    <span>
							        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
							        <?php echo $help_text;?>
							    </span>
							</a>
						<?php } ?>
					</td>
				</tr>
				<?php
			}elseif( $type == 'checkbox_list' ){
				?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<label for="<?php echo $name;?>_select_all">- <?php _e( 'Select All', 'ninja-forms-style' );?></label>
					</th>
					<td>
						<input type="checkbox" name="" value="" id="<?php echo $name;?>_select_all" class="ninja-forms-select-all" title="ninja-forms-<?php echo $name;?>">
					</td>
				</tr>
				<?php
				if(is_array($s['options']) AND !empty($s['options'])){
					foreach( $s['options'] as $option ){
						$option_name = $option['name'];
						$option_value = $option['value'];
						?>
						<tr class="<?php echo $tr_class;?>">
							<th>
								<label for="<?php echo $option_name;?>"><?php echo $option_name;?></label>
							</th>
							<td>
								<input type="checkbox" class="ninja-forms-<?php echo $name;?> <?php echo $class;?>" name="<?php echo $name;?>[]" value="<?php echo $option_value;?> " <?php checked($value, $option_value);?> id="<?php echo $option_name;?>">
							</td>
						</tr>
						<?php
					}
				}
			}elseif($type == 'radio'){
				if(is_array($s['options']) AND !empty($s['options'])){
					$x = 0; ?>
					<tr class="<?php echo $tr_class;?>">
						<th>
							<?php echo $desc;?>
						</th>
							<?php foreach($s['options'] as $option){ ?>
								<input type="radio" name="<?php echo $name;?>" value="<?php echo $option['value'];?>" id="<?php echo $name."_".$x;?>" <?php checked($value, $option['value']);?> class="<?php echo $class;?>"> <label for="<?php echo $name."_".$x;?>"><?php echo $option['name'];?></label>
									<?php if($help_text != ''){ ?>
										<a href="#" class="tooltip">
										    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
										    <span>
										        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
										        <?php echo $help_text;?>
										    </span>
										</a>
									<?php } ?>
								<br />
							<?php
								$x++;
							} ?>
						</th>
					</tr>
				<?php }
			}elseif($type == 'textarea'){ 
				$value = ninja_forms_esc_html_deep( $value );
				?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<?php echo $label; ?>
					</th>
					<td>
						<textarea name="<?php echo $name;?>" class="<?php echo $class;?>"><?php echo $value;?></textarea>
					</td>
				</tr>
				<?php
			}elseif($type == 'rte'){ ?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<?php echo $label; ?>
					</th>
					<td>
						<?php wp_editor($value, $name); ?>
					</td>
				</tr>
				<?php
			}else if($type == ''){
				$display_function = $s['display_function'];
				$arguments['form_id'] = $form_id;
				$arguments['data'] = $current_settings;
				call_user_func_array($display_function, $arguments);
			}else if($type == 'submit'){
				?>
				<tr class="<?php echo $tr_class;?>">
					<td colspan="2">
					<input type="submit" name="<?php echo $name;?>" id="" class="<?php echo $class;?>" value="<?php echo $label;?>">
					</td>
				</tr>
				<?php
			}else if($type == 'file'){
				?>
				<tr class="<?php echo $tr_class;?>">
					<td colspan="2">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>" />
						<input type="file" name="<?php echo $name;?>" id="<?php echo $id;?>" class="<?php echo $class;?>">
					</td>
				</tr>
				<?php
			}else if( $type == 'desc' ){
				?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						<?php echo $label; ?>
					</th>
					<td>
						<?php echo $desc;?>
					</td>
				</tr>
				<?php
			}else if( $type == 'hidden' ){
				?>
				<input type="hidden" name="<?php echo $name;?>" value="<?php echo $value;?>">
				<?php
			}
			if( $desc != '' AND $type != 'desc' ){
				?>
				<tr class="<?php echo $tr_class;?>">
					<th>
						
					</th>
					<td class="howto">
						<?php echo $desc;?>
					</td>
				</tr>
				<?php
			}
		}
	}
}

function ninja_forms_style_advanced_checkbox_display(){
	$plugin_settings = get_option( 'ninja_forms_settings' );
	if( isset( $plugin_settings['style']['advanced'] ) ){
		$default_advanced = $plugin_settings['style']['advanced'];
	}else{
		$default_advanced = 0;
	}
	?>
	<input type="hidden" name="advanced">
	<label><input type="checkbox" name="advanced" id="advanced_css" value="1" <?php checked( $default_advanced, 1 );?>> <?php _e( 'Show Advanced CSS Properties', 'ninja-forms-style' );?></label>
	<br />
	<br />
	<?php
}