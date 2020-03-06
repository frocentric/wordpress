<?php

function ninja_forms_style_output_layout_ul( $form_id, $cols, $fields = '', $page = '' ){
	global $ninja_forms_fields;

	if( $fields == '' ){
		$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	}else{
		foreach( $fields as $field_id ){
			$all_fields[] = ninja_forms_get_field_by_id( $field_id );
		}
	}

	if( $page != '' ){
		$ul_id = 'ninja_forms_style_list_'.$page;
	}else{
		$ul_id = '';
	}

	$all_fields = apply_filters( 'ninja_forms_style_all_fields', $all_fields, $form_id );

	?>
	<ul id="<?php echo $ul_id;?>" class="sortable ninja-forms-style-sortable cols-<?php echo $cols;?>" rel="<?php echo $cols;?>">
		<?php
		if( !empty( $all_fields ) ){
			foreach( $all_fields as $field ){
				$type = $field['type'];
				if( isset( $ninja_forms_fields[$type]['display_function'] ) ){
					$display_function = $ninja_forms_fields[$type]['display_function'];
				}else{
					$display_function = '';
				}
				if( isset( $field['data']['label'] ) ){
					$label = $field['data']['label'];
				}else{
					$label = '';
				}
				$label = strip_tags( $label );
				if( $label == '' AND isset( $ninja_forms_fields[$type]['name'] ) ){
					$label = $ninja_forms_fields[$type]['name'];
				}
				if( strlen( $label ) > 13 ){
					$label = substr( $label, 0, 10 )."...";
				}

				if( isset( $field['data']['style']['colspan'] ) ){
					$colspan = $field['data']['style']['colspan'];
				}else{
					$colspan = 1;
				}
				if( $display_function != '' ){
					?>
					<li class="ui-state-default span-<?php echo $colspan;?>" rel="<?php echo $colspan;?>" id="ninja_forms_field_<?php echo $field['id'];?>_li"><span class="style-handle"><?php echo $label;?><br /><?php _e( 'Field ID', 'ninja-forms-style' ); ?>: <?php echo $field['id'];?></span>
						<a href="#" class="ninja-forms-style-expand ninja-forms-style-button"><?php _e( 'resize', 'ninja-forms-style' ); ?></a><br /><a href="#TB_inline?height=750&width=600&height=600&inlineId=ninja_forms_field_style_div&modal=true" class="thickbox field-styling ninja-forms-style-button" id="styling_<?php echo $field['id'];?>"><?php _e( 'styling', 'ninja-forms-style' ); ?></a>
					</li>
					<input type="hidden" name="colspan[<?php echo $field['id'];?>]" id="ninja_forms_field_<?php echo $field['id'];?>_colspan" value="<?php echo $colspan;?>">
					<?php
				}else{
					?>
					<li class="ui-disabled" style="display:none;" id="ninja_forms_field_<?php echo $field['id'];?>"></li>
					<?php
				}
			}
		}
			?>
	</ul>
	<?php
}