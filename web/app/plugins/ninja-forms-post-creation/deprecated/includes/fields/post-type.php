<?php

function ninja_forms_register_field_post_type(){
	$args = array(
		'name' => __( 'Type', 'ninja-forms-pc' ),
		'display_function' => 'ninja_forms_field_post_type_display',		
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
		'pre_process' => 'ninja_forms_field_post_type_pre_process',
	);

	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field('_post_type', $args);
	}
}

add_action('init', 'ninja_forms_register_field_post_type');

function ninja_forms_field_post_type_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;
	$ninja_forms_processing->update_form_setting( 'post_type', $user_value );
}

function ninja_forms_field_post_type_display( $field_id, $data ){
	global $post, $ninja_forms_processing, $wp_post_types;

	if( is_object( $post ) ){
		$selected_type = $post->post_type;
	}

	$post_types = get_post_types();
	if( is_array( $post_types ) AND !empty( $post_types ) ){
		?>
		<select name="ninja_forms_field_<?php echo $field_id;?>" rel="<?php echo $field_id;?>" >
			<?php
			foreach( $post_types as $type ){
				if( $type != 'revision' AND $type != 'nav_menu_item' ){
					$obj = $wp_post_types[$type];
					?>
					<option value="<?php echo $type;?>" <?php selected( $selected_type, $type );?>><?php echo $obj->labels->singular_name;?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
	}
	
}