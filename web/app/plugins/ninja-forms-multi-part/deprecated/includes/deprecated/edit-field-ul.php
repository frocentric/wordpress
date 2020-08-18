<?php
add_action( 'admin_init', 'ninja_forms_mp_register_edit_field_ul' );
function ninja_forms_mp_register_edit_field_ul(){
	if( isset( $_REQUEST['form_id'] ) ){
		$form_id = $_REQUEST['form_id'];
		$form_row = ninja_forms_get_form_by_id( $form_id );
		$form_data = $form_row['data'];
		if( isset( $form_data['multi_part'] ) ){
				$multi_part = $form_data['multi_part'];
		}else{
			$multi_part = 0;
		}
		
		if( $multi_part == 1 ){
			remove_action( 'ninja_forms_edit_field_ul', 'ninja_forms_edit_field_output_ul' );
			add_action( 'ninja_forms_edit_field_ul', 'ninja_forms_edit_field_output_mp_ul' );
			add_action( 'ninja_forms_edit_field_before_ul', 'ninja_forms_edit_field_open_div' );
		}
	}
}

function ninja_forms_edit_field_output_mp_ul( $form_id ){
	
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
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
		
		if( isset( $_REQUEST['current_page'] ) ){
			$current_page = $_REQUEST['current_page'];
		}else{
			$current_page = 1;
		}

		if( is_array( $pages ) AND !empty( $pages ) ){
			foreach( $pages as $page => $fields ){
				?>
				<ul class="menu ninja-forms-field-list" id="ninja_forms_field_list_<?php echo $page;?>" data-order="<?php echo $page;?>">
			  		<?php
						if( is_array( $fields ) AND !empty( $fields ) ){
							foreach( $fields as $field_id ){
								ninja_forms_edit_field( $field_id );
							}
						}
					?>
				</ul>
				<?php
			}

		}
	}

}