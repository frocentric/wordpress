<?php

function ninja_forms_conditionals_field_filter( $form_id ){
	global $pagenow, $ninja_forms_loading, $ninja_forms_processing;

	if ( is_admin() && $pagenow != 'admin-ajax.php' )
		return false;

	if ( isset ( $ninja_forms_loading ) ) {
		$all_fields = $ninja_forms_loading->get_all_fields();
	} else {
		$all_fields = $ninja_forms_processing->get_all_fields();
	}

	if ( ! is_array( $all_fields ) )
		return false;

	foreach ( $all_fields as $field_id => $user_value ) {
		if ( isset ( $ninja_forms_loading ) ) {
			$field = $ninja_forms_loading->get_field_settings( $field_id );
		} else {
			$field = $ninja_forms_processing->get_field_settings( $field_id );
		}

		// Quick and dirty way of cleaning up the label for required elements with inside label positions
        //$field['data']['req_added'] = 1;

		$data = apply_filters( 'ninja_forms_field', $field['data'], $field_id );
		
		// We don't want to use the default value if we are on a calc field.
		if ( $field['type'] == '_calc' ) {
			$data['default_value'] = 0;
		}

		$x = 0;
		$display_style = '';

		if( isset ( $data['conditional'] ) AND is_array ( $data['conditional'] ) AND !empty ( $data['conditional'] ) ){
			$action_pass = array();

			foreach( $data['conditional'] as $conditional ){
				$action = $conditional['action'];
				$con_value = $conditional['value'];
				if ( is_array( $con_value ) ) {
					if ( isset ( $con_value['value'] ) and isset ( $con_value['label'] ) ) {
						if ( $con_value['value'] == '_ninja_forms_no_value' ) {
							$con_value = $con_value['label'];
						} else {
							$con_value = $con_value['value'];
						}
					}
				}
				if(isset( $conditional['cr']) AND is_array($conditional['cr']) AND !empty($conditional['cr'])){
					$pass_array = array();
					$x = 0;
					
					foreach($conditional['cr'] as $cr){
						
						$pass_array[$x] = false;
						if( isset ( $ninja_forms_loading ) ) {
							$user_value = $ninja_forms_loading->get_field_value( $cr['field'] );
						}else{
							$user_value = $ninja_forms_processing->get_field_value( $cr['field'] );
						}

						if( isset( $cr['value'] ) ){
							if( is_array( $user_value ) ){
								foreach( $user_value as $v ){
									if( !$pass_array[$x] ){
										$pass_array[$x] = ninja_forms_conditional_compare($v, $cr['value'], $cr['operator']);
									}else{
										break;
									}
								}
							}else{
								$pass_array[$x] = ninja_forms_conditional_compare($user_value, $cr['value'], $cr['operator']);
							}
						}else{
							$pass_array[$x] = true;
						}

						$x++;

					}
					
				}
				
				if( isset ( $pass_array ) and is_array( $pass_array ) ){
					if( $conditional['connector'] == 'and' ){
						$pass = true;
					}else if( $conditional['connector'] == 'or' ){
						$pass = false;
					}

					foreach( $pass_array as $p ){
						if( $conditional['connector'] == 'and' ){
							if( $pass ){
								$pass = $p;
							}else{
								break;
							}
						}else if( $conditional['connector'] == 'or' ){
							if( $pass ){
								break;
							}else{
								$pass = $p;
							}
						}
					}
				}

				if ( isset ( $pass ) and ( !isset ( $action_pass[$action][$con_value] ) OR !$action_pass[$action][$con_value] ) ) {
					$action_pass[$action][$con_value] = $pass;
				}
			}
		
			foreach( $data['conditional'] as $conditional ){
				$action = $conditional['action'];
				$con_value = $conditional['value'];
				if ( is_array( $con_value ) ) {
					if ( isset ( $con_value['value'] ) and isset ( $con_value['label'] ) ) {
						if ( $con_value['value'] == '_ninja_forms_no_value' ) {
							$con_value = $con_value['label'];
						} else {
							$con_value = $con_value['value'];
						}
					}
				}
				$pass = $action_pass[$action][$con_value];

				switch( $conditional['action'] ){
					case 'show':
						if( !$pass ){
							$data['display_style'] = 'display:none;';
							$data['visible'] = false;

                            if( isset( $data['class'] ) ) {
                                // Append to class list, with leading comma
                                $data['class'] .= ',ninja-forms-field-calc-no-new-op,ninja-forms-field-calc-no-old-op';
                            } else {
                                // Create class list, without leading comma
                                $data['class'] = 'ninja-forms-field-calc-no-new-op,ninja-forms-field-calc-no-old-op';
                            }

							// Set our $calc to 0 if we're dealing with a list field.
							if ( $field['type'] == '_list' ) {
								if ( isset ( $data['list']['options'] ) AND is_array ( $data['list']['options'] ) ) {
									for ($x=0; $x < count( $data['list']['options'] ) ; $x++) {
										//$data['list']['options'][$x]['calc'] = '';
									}
								}							
							}

							if ( isset ( $ninja_forms_loading ) ) {
								if( $field['type'] != '_spam' ){
									$ninja_forms_loading->update_field_value( $field_id, false );
								}
							} else if ( isset ( $ninja_forms_processing ) ) {
								if( $field['type'] != '_spam' ){
                                    $user_value = $ninja_forms_processing->get_field_value( $field_id );
									$ninja_forms_processing->update_field_value( $field_id, false );
                                    $ninja_forms_processing->update_extra_value( '_' . $field_id, $user_value);
								}
							}

						}else{
							$data['display_style'] = '';
							$data['visible'] = true;

                            if ( isset ( $ninja_forms_processing ) ) {
                                if( $field['type'] != '_spam' ){
                                    $current_value = $ninja_forms_processing->get_field_value( $field_id );
                                    $user_value = $ninja_forms_processing->get_extra_value( '_' . $field_id );
                                    if ( ! $current_value && $user_value  ) {
                                        $ninja_forms_processing->update_field_value($field_id, $user_value);
                                    }
                                }
                            }

						}
						break;
					case 'hide':
						if( $pass ){
							$data['display_style'] = 'display:none;';
							$data['visible'] = false;

                            if ( isset( $data['class'] ) ) {
                                // Append to class list, with leading comma
                                $data['class'] .= ',ninja-forms-field-calc-no-new-op,ninja-forms-field-calc-no-old-op';
                            } else {
                                // Create class list, without leading comma
                                $data['class'] = 'ninja-forms-field-calc-no-new-op,ninja-forms-field-calc-no-old-op';
                            }


							// Set our $calc to 0 if we're dealing with a list field.
							if ( $field['type'] == '_list' ) {
								if ( isset ( $data['list']['options'] ) AND is_array ( $data['list']['options'] ) ) {
									for ($x=0; $x < count( $data['list']['options'] ) ; $x++) {
										//$data['list']['options'][$x]['calc'] = '';
									}
								}
							}
							if ( isset ( $ninja_forms_processing ) ) {
								if( $field['type'] != '_spam' ){
									$ninja_forms_processing->update_field_value( $field_id, false );
								}
							}
						} else {
							$data['display_style'] = '';
							$data['visible']= true;
						}
						break;
					case 'change_value':
						if( $pass ){
							$data['default_value'] = $conditional['value'];

							if ( isset ( $ninja_forms_loading ) ) {
								$ninja_forms_loading->update_field_value( $field_id, $conditional['value'] );
							} else if ( isset ( $ninja_forms_processing ) ) {
								$ninja_forms_processing->update_field_value( $field_id, $conditional['value'] );
							}
						}
						break;
					case 'add_value':
						if( $pass ){
							if( !isset( $conditional['value']['value'] ) ){
								$value = $conditional['value']['label'];
							}else{
								$value = $conditional['value'];
							}
							if( !isset( $data['list']['options'] ) OR !is_array( $data['list']['options'] ) ){
								$data['list']['options'] = array();
							}
							$found = false;
							for ($x=0; $x < count( $data['list']['options'] ) ; $x++) { 
								if( isset( $data['list_show_value'] ) AND $data['list_show_value'] == 1 ){
									if( $data['list']['options'][$x]['value'] == $con_value ){
										$found = true;
									}
								}else{
									if( $data['list']['options'][$x]['label'] == $con_value ){
										$found = true;
									}
								}
							}
							if ( !$found ) {
								array_push( $data['list']['options'], $value );	
							}
						}
						break;
					case 'remove_value':
						if( $pass ){
							if( isset( $data['list']['options'] ) AND is_array( $data['list']['options'] ) ){
								for ($x=0; $x < count( $data['list']['options'] ) ; $x++) { 
									if( isset( $data['list_show_value'] ) AND $data['list_show_value'] == 1 ){
										if( $data['list']['options'][$x]['value'] == $conditional['value'] ){
											$data['list']['options'][$x]['display_style'] = 'display:none;';
											$data['list']['options'][$x]['disabled'] = true;
										}
									}else{
										if( $data['list']['options'][$x]['label'] == $conditional['value'] ){
											$data['list']['options'][$x]['display_style'] = 'display:none;';
											$data['list']['options'][$x]['disabled'] = true;
										}
									}
								}
								$data['list']['options'] = array_values( $data['list']['options'] );
							}
						}
						break;
					default:
						$data['conditional_action'] = $conditional['action'];
						$data['conditional_pass'] = $pass;
				}
			}

			$field['data'] = $data;
			if ( isset ( $ninja_forms_loading ) ) {
				$ninja_forms_loading->update_field_settings( $field_id, $field );
			} else {
                if( 1 == $ninja_forms_processing->get_form_setting( 'processing_complete' ) ) {
                    $field['data']['default_value'] = '';
                }
                $ninja_forms_processing->update_field_settings($field_id, $field);
			}
		}
	}
}

add_action( 'ninja_forms_display_pre_init', 'ninja_forms_conditionals_field_filter', 75 );
add_action( 'ninja_forms_display_init', 'ninja_forms_conditionals_field_filter', 12 );
add_action( 'ninja_forms_pre_process', 'ninja_forms_conditionals_field_filter', 1000 );