<?php

add_action( 'init', 'ninja_forms_register_style_display_css' );
function ninja_forms_register_style_display_css(){
	add_action( 'ninja_forms_before_form_display', 'ninja_forms_style_display_css' );
}

function ninja_forms_style_display_css( $form_id ){
	global $ninja_forms_tabs_metaboxes, $ninja_forms_tabs, $ninja_forms_css_options, $ninja_forms_style_metaboxes;

	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );

	wp_enqueue_style( 'ninja-forms-style-display',
		NINJA_FORMS_STYLE_URL.'/css/ninja-forms-style-display.css' );

	$plugin_settings = get_option( 'ninja_forms_settings' );

	if( isset( $plugin_settings['style']['field_type'] ) ){
		$field_type_css = $plugin_settings['style']['field_type'];
	}else{
		$field_type_css = '';
	}

	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];


	?>
	<style type="text/css" media="screen">
	<?php

	//Output default settings CSS
	foreach( $ninja_forms_tabs['ninja-forms-style'] as $slug => $tab ){
		if( $slug != 'field_type_settings' ){
			if( isset( $ninja_forms_tabs_metaboxes['ninja-forms-style'][$slug] ) ){
				foreach( $ninja_forms_tabs_metaboxes['ninja-forms-style'][$slug] as $metabox ){

					$save_page = $metabox['save_page'];
					$group = $metabox['slug'];

					$output = false;
					if( isset( $plugin_settings['style'][$save_page][$group] ) ){
						foreach( $plugin_settings['style'][$save_page][$group] as $prop => $val ){
							if( $val != '' ){
								$output = true;
								break;
							}
						}
					}

					if( $output ){
						echo $metabox['css_selector'].'{';
							if( isset( $plugin_settings['style'][$save_page][$group] ) ){
								foreach( $plugin_settings['style'][$save_page][$group] as $group => $val ){
									if( $val != '' ){
										if( isset( $ninja_forms_css_options[$group]['css_property'] ) ){
											$css_property = $ninja_forms_css_options[$group]['css_property'];

											if( $css_property != '' ){
												echo $css_property.': '.$val.';';
											}else{
												echo $val;
											}
										}
									}
								}
							}
						echo '}';
					}

				}
			}
		}
	}

	//Output field-type CSS
	if( is_array( $field_type_css ) ){
		foreach( $field_type_css as $field_type => $groups ){
			$output = array();
			//Check to make sure that all of the css values aren't empty.
			foreach( $groups as $group => $props ){
				$output[$group] = false;
				if( is_array( $props ) AND !empty( $props ) ){
					foreach( $props as $k => $val ){
						if( $val != '' ){
							$output[$group] = true;
							break;
						}
					}
				}
			}

			if(strpos($field_type, "_") === 0){
				$type_slug = substr($field_type, 1);
			}

			foreach( $groups as $group => $props ){
				if( $output[$group] ){
					if( isset( $ninja_forms_tabs_metaboxes['ninja-forms-style']['field_type_settings'][$group] ) ){
						$css_selector = $ninja_forms_tabs_metaboxes['ninja-forms-style']['field_type_settings'][$group]['css_selector'];
						$css_selector = str_replace( '[type_slug]', $type_slug, $css_selector );
						$css_selector = apply_filters( 'ninja_forms_style_field_type_css_selector', $css_selector, $type_slug );
						if( is_array( $props ) AND !empty( $props ) ){
							echo $css_selector.'{';
							foreach( $props as $prop => $value ){
								if( $value != '' ){
									if( isset( $ninja_forms_css_options[$prop]['css_property'] ) ){
										$css_property = $ninja_forms_css_options[$prop]['css_property'];
										if( $css_property != '' ){
											echo $css_property.': '.$value.';';
										}else{
											echo $value;
										}
									}
								}
							}
							echo '}';
						}
					}
				}
			}
		}
	}

	//Output form-specific CSS
	if( isset( $form_data['style']['groups'] ) AND is_array( $form_data['style']['groups'] ) ){
		foreach( $form_data['style']['groups'] as $group => $props ){
			$output = false;
			foreach( $props as $prop => $value ){
				if( $value != '' ){
					$output = true;
					break;
				}
			}
			if( $output ){
				switch( $group ){
					case 'container':
						echo '#ninja_forms_form_'.$form_id.'_wrap {';
						break;
					case 'title':
						echo '#ninja_forms_form_'.$form_id.'_wrap h2.ninja-forms-form-title {';
						break;
					case 'row':
						echo '#ninja_forms_form_'.$form_id.'_wrap div.ninja-row {';
						break;
					case 'row-odd':
						echo '#ninja_forms_form_'.$form_id.'_wrap div.ninja-row:nth-child(odd) {';
						break;
					case 'success-msg':
						echo '#ninja_forms_form_'.$form_id.'_wrap div.ninja-forms-success-msg {';
						break;
					case 'error_msg':
						echo '#ninja_forms_form_'.$form_id.'_wrap div.ninja-forms-error-msg {';
						break;
				}

				foreach( $props as $prop => $value ){
					if( $value != '' ){
						if( isset( $ninja_forms_css_options[$prop]['css_property'] ) ){
							$css_property = $ninja_forms_css_options[$prop]['css_property'];
							if( $css_property != '' ){
								echo $css_property.': '.$value.';';
							}else{
								echo $value;
							}
						}
					}
				}
				echo '}';
			}
		}
	}

	//Output field-specific CSS
	if( !empty( $all_fields ) ){
		foreach( $all_fields as $field ){
			$field_id = $field['id'];
			do_action( 'ninja_forms_style_field_metaboxes', $field_id );

			$field_type = $field['type'];
			if( isset( $field['data']['style']['groups'] ) AND is_array( $field['data']['style']['groups'] ) ){
				foreach( $field['data']['style']['groups'] as $group => $props ){

					$output = false;
					foreach( $props as $prop => $value ){
						if( $value != '' ){
							$output = true;
							break;
						}
					}
					if( $output ){
						if( isset( $ninja_forms_style_metaboxes['field'][$field_type][$group] ) ){
							$css_selector = $ninja_forms_style_metaboxes['field'][$field_type][$group]['css_selector'];
							$css_selector = str_replace( '[field_id]', $field_id, $css_selector );
						}else if( isset( $ninja_forms_style_metaboxes['page']['field'][$group] ) ){
							$css_selector = $ninja_forms_style_metaboxes['page']['field'][$group]['css_selector'];
							$css_selector = str_replace( '[field_id]', $field_id, $css_selector );
						}

						echo $css_selector."{";
						foreach( $props as $prop => $value ){
							if( $value != '' ){
								if( isset( $ninja_forms_css_options[$prop]['css_property'] ) ){
									$css_property = $ninja_forms_css_options[$prop]['css_property'];
									if( $css_property != '' ){
										echo $css_property.': '.$value.';';
									}else{
										echo $value;
									}
								}
							}
						}
						echo '}';
					}
				}
			}
		}
	}


	?>
	</style>
	<?php

}
