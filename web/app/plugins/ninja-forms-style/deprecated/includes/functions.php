<?php
if ( !function_exists( 'ninja_forms_field_type_dropdown' ) ) {
	function ninja_forms_field_type_dropdown( $args = '' ){
		global $ninja_forms_fields, $ninja_forms_field_type_groups;
		if( isset( $args['echo'] ) ){
			$echo = $args['echo'];
		}else{
			$echo = true;
		}

		if( isset( $args['selected'] ) ){
			$selected = $args['selected'];
		}else{
			$selected = '';
		}

		$groups = $ninja_forms_field_type_groups;
		ksort( $groups );

		$output = '<select name="field_type" id="field_type">
			<option value="">' . __( '- Field Type', 'ninja-forms-style' ) . '</option>';
			foreach( $groups as $gslug => $group ){
				$group_name = $group['name'];
				$output .= '<optgroup label="'.$group_name.'">';
					foreach( $ninja_forms_fields as $fslug => $field ){
						if( isset( $field['group'] ) AND $field['group'] == $gslug ){
							if( isset( $field['type_dropdown_function'] ) AND $field['type_dropdown_function'] != '' ){
								$dropdown_function = $field['type_dropdown_function'];
								$arguments['selected'] = $selected;
								$output .= call_user_func_array( $dropdown_function, $arguments );
							}else{
								if( $fslug == $selected ){
									$select = 'selected="selected"';
								}else{
									$select = '';
								}
								$output .= '<option value="'.$fslug.'" '.$select.'>'.$field['name'].'</option>';
							}
						}
					}
				$output .= '</optgroup>';
			}

		$output .= '</select>';

		if( $echo ){
			echo $output;
		}else{
			return $output;
		}
	}
}


function ninja_forms_style_filter_submit_css_selecter( $css_selector, $field_type ) {

	if ( $css_selector == 'submit_hover' ) {
		$css_selector = '.ninja-forms-form-wrap .ninja-forms-form input[type=submit]:hover';
	} elseif ( $field_type == 'submit' ) {
		$css_selector = '.ninja-forms-form-wrap .ninja-forms-form input[type=submit]';
	}

	return $css_selector;
}
add_filter( 'ninja_forms_style_field_type_css_selector', 'ninja_forms_style_filter_submit_css_selecter', 10, 2 );