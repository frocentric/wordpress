<?php
/**
 * Creates an array out of the conditional settings of all the fields of a given form.
 * This is called by ninja_forms_display_form() in display-fields.php.
 * It is used mainly to output the conditional settings into JS format.
**/
/*
//add_action('init', 'ninja_forms_register_display_conditionals');
function ninja_forms_register_display_conditionals(){
	add_action('ninja_forms_display_conditionals', 'ninja_forms_display_conditionals');
}
*/

function ninja_forms_display_conditionals( $form_id ){
	global $ninja_forms_fields, $ninja_forms_loading, $ninja_forms_processing;

	if ( is_admin() )
		return false;

	$field_results = ninja_forms_get_fields_by_form_id($form_id);
	$field_results = apply_filters('ninja_forms_display_fields_array', $field_results, $form_id);

	if ( is_array ( $field_results ) AND !empty ( $field_results ) ) {
		/*
		$watch_fields = array();
		foreach($field_results as $field){
			$data = $field['data'];
			if(isset($data['conditional']) AND is_array($data['conditional'])){
				foreach($data['conditional'] as $conditional){
					if(isset($conditional['cr']) AND is_array($conditional['cr'])){
						foreach($conditional['cr'] as $cr){
							$watch_fields[$cr['field']] = 1;
						}
					}
				}
			}
		}
		*/
		//foreach( $field_results as $field_id => $user_value ) {
		foreach( $field_results as $field ) {
			
			// if ( isset ( $ninja_forms_loading ) ) {
			// 	$field = $ninja_forms_loading->get_field_settings( $field_id );
			// } else {
			// 	$field = $ninja_forms_processing->get_field_settings( $field_id );
			// }

			if( isset( $ninja_forms_fields[$field['type']] ) ){
				$type = $ninja_forms_fields[$field['type']];
				$field_id = $field['id'];
				$display_wrap = $type['display_wrap'];
				$display_label = $type['display_label'];
				$display_function = $type['display_function'];
				$data = $field['data'];

				$x = 0;
				if(isset($data['conditional']) AND is_array($data['conditional'])){
					if(!isset($local_vars)){
						$local_vars = array();
					}
					foreach($data['conditional'] as $conditional){
						$local_vars['field_'.$field_id]['conditional'][$x]['action'] = $conditional['action'];
						$local_vars['field_'.$field_id]['conditional'][$x]['connector'] = $conditional['connector'];
						$local_vars['field_'.$field_id]['conditional'][$x]['value'] = $conditional['value'];
						if(isset($conditional['cr']) AND is_array($conditional['cr'])){
							$y = 0;
							foreach($conditional['cr'] as $cr){
								if( isset( $cr['field'] ) ){
									$field = $cr['field'];
								}else{
									$field = '';
								}

								if( isset( $cr['operator'] ) ){
									$operator = $cr['operator'];
								}else{
									$operator = '';
								}

								if( isset( $cr['value'] ) ){
									$value = $cr['value'];
								}else{
									$value = '';
								}
								$local_vars['field_'.$field_id]['conditional'][$x]['cr'][$y]['field'] = $field;
								$local_vars['field_'.$field_id]['conditional'][$x]['cr'][$y]['operator'] = $operator;
								$local_vars['field_'.$field_id]['conditional'][$x]['cr'][$y]['value'] = $value;
								$y++;
							}
						}
						$x++;
					}
				}
			}
		}

		if(isset($local_vars)){
			return $local_vars;
		}else{
			return '';
		}
	}
}
