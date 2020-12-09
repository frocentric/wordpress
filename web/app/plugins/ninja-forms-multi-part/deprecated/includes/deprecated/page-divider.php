<?php
add_action( 'init', 'ninja_forms_register_field_page_divider', 9 );

function ninja_forms_register_field_page_divider( $form_id = '' ){
	global $ninja_forms_processing;

	$args = array(
		'name' => __( 'Page Divider', 'ninja-forms-mp' ),
		'sidebar' => '',
		'edit_function' => 'ninja_forms_field_page_divider_edit',
		'display_function' => '',
		'save_function' => '',
		'group' => '',
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_desc' => false,
		'edit_conditional' => true,
		'process_field' => false,
		'use_li' => false,
		'conditional' => array(
			'action' => array(
				'show' => array(
					'name' => __( 'Show This', 'ninja-forms-mp' ),
					'js_function' => 'ninja_forms_show_mp_page',
					'output' => 'show',
				),				
				'hide' => array(
					'name' => __( 'Hide This', 'ninja-forms-mp' ),
					'js_function' => 'ninja_forms_hide_mp_page',
					'output' => 'hide',
				),			
			),
		),
	);
	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field( '_page_divider', $args );
	}
}

function ninja_forms_field_page_divider_edit( $field_id, $data ){
	if( isset( $data['page_name'] ) ){
		$page_name = $data['page_name'];
	}else{
		$page_name = '';
	}
	$type_name = __( 'Multi-Part Page', 'ninja-forms-mp' );
	?>
	<li id="ninja_forms_field_<?php echo $field_id;?>" class="not-sortable page-divider menu-item-settings">
		<a href="#" id="" name="" class="button-secondary ninja-forms-mp-copy-page"><?php _e( 'Duplicate Page', 'ninja-forms-mp' );?></a>
		<div id="ninja_forms_field_<?php echo $field_id;?>" class="">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle" >
					<span class="item-title ninja-forms-field-title" id="ninja_forms_field_<?php echo $field_id;?>_title"><?php _e( 'Page Settings', 'ninja-forms-mp' );?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo $type_name;?></span>
						<a class="item-edit" id="ninja_forms_field_<?php echo $field_id;?>_toggle" title="<?php _e( 'Edit Menu Item', 'ninja-forms-mp' ); ?>" href="#"><?php _e( 'Edit Menu Item' , 'ninja-forms-mp' ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings type-class inside" id="ninja_forms_field_<?php echo $field_id;?>_inside" style="display:none;">
				<?php _e( 'Page Title', 'ninja-forms-mp' ); ?>: <input type="text" id="ninja_forms_field_<?php echo $field_id;?>_page_name" name="ninja_forms_field_<?php echo $field_id;?>[page_name]" value="<?php echo $page_name;?>" class="mp-page-name"> 
	
				<?php
				do_action( 'ninja_forms_edit_field_after_registered', $field_id, $data );
				?>
			</div>
		</div>
	</li>
	<?php
}

function ninja_forms_field_page_divider_display( $field_id, $data ){
	global $ninja_forms_loading, $ninja_forms_processing;
	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
	}

	if( isset( $data['page_name'] ) ){
		$page_name = $data['page_name'];
	}else{
		$page_name = '';
	}
	if( isset( $form_data['mp_display_titles'] ) AND $form_data['mp_display_titles'] == 1 ){
		?>
		<h4><?php echo $page_name;?></h4>
		<?php
	}
}