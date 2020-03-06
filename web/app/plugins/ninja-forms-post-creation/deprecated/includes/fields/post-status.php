<?php

function ninja_forms_register_field_post_status(){
	$args = array(
		'name' => __( 'Status', 'ninja-forms-pc' ),
		'display_function' => 'ninja_forms_field_post_status_display',		
		'group' => 'create_post',	
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'post_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'list',
			),
		),
		'limit' => 1,
		//'save_sub' => false,
		'pre_process' => 'ninja_forms_field_post_status_pre_process',
	);

	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field('_post_status', $args);
	}
}

add_action( 'init', 'ninja_forms_register_field_post_status' );

function ninja_forms_field_post_status_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;
	$ninja_forms_processing->update_form_setting( 'post_status', $user_value );
}

function ninja_forms_field_post_status_display( $field_id, $data ){
	global $post, $ninja_forms_processing;
	if( is_object( $post ) ){
		$selected_status = $post->post_status;
	}
	?>
	<select name="ninja_forms_field_<?php echo $field_id;?>"  rel="<?php echo $field_id;?>" >
		<option value="draft" <?php selected( $selected_status, 'draft' );?>><?php __( 'Draft', </option>
		<option value="pending" <?php selected( $selected_status, 'pending' );?>>Pending</option>
		<option value="publish" <?php selected( $selected_status, 'publish' );?>>Published</option>
	</select>
	<?php
}