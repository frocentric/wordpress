<?php

add_action('wp_ajax_ninja_forms_style_field_styling', 'ninja_forms_style_field_styling');
function ninja_forms_style_field_styling(){
	global $ninja_forms_style_metaboxes;
	$field_id = $_REQUEST['field_id'];
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	?>
	<input type="hidden" name="field_id" value="<?php echo $field_id;?>">
	<?php
	ninja_forms_style_advanced_checkbox_display();

	do_action( 'ninja_forms_style_field_metaboxes', $field_id );

	if( is_array( $ninja_forms_style_metaboxes['page']['field'] ) ){
		foreach( $ninja_forms_style_metaboxes['page']['field'] as $key=>$args ){
			ninja_forms_output_tab_metabox('', $args['slug'], $args);
		}
	}

	if( isset( $ninja_forms_style_metaboxes['field'][$field_type] ) AND is_array( $ninja_forms_style_metaboxes['field'][$field_type] ) ){
		foreach( $ninja_forms_style_metaboxes['field'][$field_type] as $key=>$args ){
			ninja_forms_output_tab_metabox('', $args['slug'], $args);
		}
	}

	die();
}

function ninja_forms_style_field_metabox_output( $metabox ){
	$field_id = $_REQUEST['field_id'];
	$metabox['field_id'] = $field_id;
	ninja_forms_style_metabox_output( $metabox );
}

add_action('wp_ajax_ninja_forms_style_field_styling_save', 'ninja_forms_style_field_styling_save');
function ninja_forms_style_field_styling_save(){
	$data = $_REQUEST['data'];

	$advanced = $data['advanced'];
	unset( $data['advanced'] );
	$field_id = $data['field_id'];
	unset( $data['field_id'] );

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_data = $field_row['data'];

	$tmp_array = array();
	foreach( $data as $group => $d ){
		$field_data['style']['groups'][$group] = $d;
	}

	$field_data = serialize( $field_data );

	$args = array(
		'update_array' => array(
			'data' => $field_data,
			),
		'where' => array(
			'id' => $field_id,
			),
	);
	ninja_forms_update_field( $args );

	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['advanced'] = $advanced;

	update_option( 'ninja_forms_settings', $plugin_settings);

	die();
}

add_action('wp_ajax_ninja_forms_style_form_styling_save', 'ninja_forms_style_form_styling_save');
function ninja_forms_style_form_styling_save(){
	$data = $_REQUEST['data'];

	$advanced = $data['advanced'];
	unset( $data['advanced'] );
	$form_id = $data['form_id'];
	unset( $data['form_id'] );

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

	if ( is_array( $form_data ) ) {
		foreach ( $form_data as $key => $val ) {
			Ninja_Forms()->form( $form_id )->update_setting( $key, $val );
		}
	}

	Ninja_Forms()->form( $form_id )->dump_cache();

	$plugin_settings = get_option( 'ninja_forms_settings' );

	$plugin_settings['style']['advanced'] = $advanced;

	update_option( 'ninja_forms_settings', $plugin_settings);

	die();
}
