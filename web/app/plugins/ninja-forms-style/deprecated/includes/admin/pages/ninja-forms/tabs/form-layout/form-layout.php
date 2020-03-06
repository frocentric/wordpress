<?php

function ninja_forms_register_tab_form_style(){
	$args = array(
		'name' => __( 'Layout & Styles', 'ninja-forms-style' ),
		'page' => 'ninja-forms',
		'display_function' => 'ninja_forms_form_style_tab',
		'save_function' => 'ninja_forms_save_form_style',
		'show_save' => false,
		'disable_no_form_id' => true,
	);
	if( function_exists( 'ninja_forms_register_tab' ) ){
		ninja_forms_register_tab( 'form_layout', $args );
	}
}

add_action( 'admin_init', 'ninja_forms_register_tab_form_style', 12 );

function ninja_forms_form_style_tab(){
	global $ninja_forms_fields;

	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
	}else{
		$form_id = '';
	}
	?>
	<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e( 'Save Layout', 'ninja-forms-style' ); ?>" />
	<a href="#TB_inline?height=750&width=600&height=600&inlineId=ninja_forms_form_style_div&modal=true" class="thickbox button-secondary"><?php _e( 'Modify Form Styles', 'ninja-forms-style' );?></a>
	<br />
	<br />
	<?php

	do_action( 'ninja_forms_style_layout_tab_div', $form_id );

	?>
		<div style="clear: both">
			<br />
			<br />

			<?php add_thickbox(); ?>
			<div id="ninja_forms_field_style_div" style="display:none;">
				<br />
				<input type="button" class="save-field-styling button-secondary" value="<?php _e( 'Save', 'ninja-forms-style' );?>">
				<input type="button" class="cancel-field-styling button-secondary" value="<?php _e( 'Cancel', 'ninja-forms-style' );?>">
				<span class="spinner" style="float:left;display:none;" id="loading_style"></span>
				<br />
				<br />

				<div id="ninja_forms_field_styling">
				</div>
			</div>

			<div id="ninja_forms_form_style_div" style="display:none;">
				<br />
				<input type="button" class="save-form-styling button-secondary" value="<?php _e( 'Save', 'ninja-forms-style' );?>">
				<input type="button" class="cancel-form-styling button-secondary" value="<?php _e( 'Cancel', 'ninja-forms-style' );?>">
				<!-- <span class="spinner" style="float:left;display:none;" id="loading_style"></span> -->
				<br />
				<br />
				<div id="ninja_forms_form_style_inputs">
					<input type="hidden" name="form_id" value="<?php echo $form_id;?>">
					<?php
						ninja_forms_style_advanced_checkbox_display();
						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'container',
							'title' => __( 'Container Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-forms-form-wrap',
							'css_exclude' => '',
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'container', $args);
						}

						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'title',
							'title' => __( 'Title Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-forms-form-wrap .ninja-forms-form-title',
							'css_exclude' => '',
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'container', $args);
						}

						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'row',
							'title' => __( 'Row Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-row',
							'css_exclude' => array( 'float' ),
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'row', $args);
						}

						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'row-odd',
							'title' => __( 'Odd Row Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-row:nth-child(odd)',
							'css_exclude' => array( 'float' ),
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'row-odd', $args);
						}

						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'success-msg',
							'title' => __( 'Success Response Message Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-forms-success-msg',
							'css_exclude' => array( 'float' ),
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'success-msg', $args);
						}
						$args = array(
							'page' => 'ninja-forms-style',
							'tab' => 'form_settings',
							'slug' => 'error_msg',
							'title' => __( 'Error Response Message Styles', 'ninja-forms-style' ),
							'state' => 'closed',
							'display_function' => 'ninja_forms_style_form_metabox_output',
							'save_page' => 'form',
							'css_selector' => 'div.ninja-forms-error-msg',
							'css_exclude' => array( 'float' ),
						);

						if( function_exists( 'ninja_forms_output_tab_metabox' ) ){
							ninja_forms_output_tab_metabox('', 'error_msg', $args);
						}

					?>
				</div>
			</div>
		</div>

		<style>
			#ninja_forms_admin_metaboxes {
				display: none;
			}
			.postbox h3, .metabox-holder h3 {
				font-size: 15px;
				font-weight: normal;
				padding: 7px 10px;
				margin: 0;
				line-height: 1;
			}
		</style>
	<?php

}

function ninja_forms_style_form_metabox_output( $metabox ){
	$form_id = $_REQUEST['form_id'];
	$metabox['form_id'] = $form_id;
	ninja_forms_style_metabox_output( $metabox );
}

function ninja_forms_save_form_style( $form_id, $data ){
	global $wpdb, $ninja_forms_admin_update_message;

	$order = '';
	$cols = array();
	if( isset( $data['order'] ) ){
		$order = $data['order'];
	}else{
		$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
		$pages = array();
		if( is_array( $all_fields ) AND !empty( $all_fields ) ){
			$pages = array();
			$this_page = array();
			$x = 0;
			foreach( $all_fields as $field ){
				if( $field['type'] == '_page_divider' ){
					$x++;
				}
				$pages[$x][] = $field['id'];
			}
		}
		$page_count = count($pages);

		for ($i=1; $i <= $page_count; $i++) {
			if( isset( $data['order_'.$i] ) ){
				if( $order == '' ){
					$order = $data['order_'.$i];
				}else{
					$order .= ','.$data['order_'.$i];
				}
			}
		}
	}

	if( $order != '' ){
		$order = str_replace( 'ninja_forms_field_', '', $order );
		$order = str_replace( '_li', '', $order );
		$order = explode( ',', $order );
		if( is_array( $order ) AND !empty( $order ) ){
			foreach( $order as $key => $val ){
				$wpdb->update( NINJA_FORMS_FIELDS_TABLE_NAME, array( 'order' => $key ), array( 'id' => $val ));
			}
		}
	}

	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];

	if( isset( $data['container'] ) ){
		$form_data['style']['groups']['container'] = $data['container'];
	}

	if( isset( $data['title'] ) ){
		$form_data['style']['groups']['title'] = $data['title'];
	}

	if( isset( $data['row'] ) ){
		$form_data['style']['groups']['row'] = $data['row'];
	}

	if( isset( $data['row-odd'] ) ){
		$form_data['style']['groups']['row-odd'] = $data['row-odd'];
	}

	if( isset( $data['success-msg'] ) ){
		$form_data['style']['groups']['success-msg'] = $data['success-msg'];
	}

	if( isset( $data['error_msg'] ) ){
		$form_data['style']['groups']['error_msg'] = $data['error_msg'];
	}

	$mp_enabled = false;
	if ( function_exists( 'nf_mp_get_page_count' ) ) {
		if ( nf_mp_get_page_count( $form_id ) > 1 ) {
			$mp_enabled = true;
		}
	} else {
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];
		if( isset( $form_data['multi_part'] ) AND $form_data['multi_part'] == 1 ){
			$mp_enabled = true;
		}
	}

	if( $mp_enabled ){
		$form_data['style']['cols'] = $cols;
		unset( $form_data['style']['mp'] );
		for ($i=1; $i <= $page_count; $i++) {
			$form_data['style']['mp'][$i]['cols'] = $data['cols_'.$i];
		}
	}else{
		$form_data['style']['cols'] = $data['cols'];
	}

	$args = array(
		'update_array' => array(
			'data' => serialize( $form_data ),
			),
		'where' => array(
			'id' => $form_id,
			),
	);

	ninja_forms_update_form( $args );

	if( !empty( $data['colspan'] ) ){
		foreach( $data['colspan'] as $field_id => $span ){
			$field_row = ninja_forms_get_field_by_id( $field_id );
			$field_data = $field_row['data'];
			$field_data['style']['colspan'] = $span;
			$field_data = array('data' => serialize($field_data));
			$wpdb->update( NINJA_FORMS_FIELDS_TABLE_NAME, $field_data, array( 'id' => $field_id ));
		}
	}

	$update_msg = __( 'Form Layout Saved', 'ninja-forms-style' );
	return $update_msg;
}
