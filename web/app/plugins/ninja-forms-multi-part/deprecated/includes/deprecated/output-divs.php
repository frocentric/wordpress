<?php

function ninja_forms_open_mp_div( $field_id, $data ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$page = $ninja_forms_loading->get_field_setting( $field_id, 'page' );
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$page = $ninja_forms_processing->get_field_setting( $field_id, 'page' );
	}

	$pages = $form_data['mp_pages'];

	$js_transition = 1;

	if( is_object( $ninja_forms_processing ) ){
		$current_page = absint( $ninja_forms_processing->get_extra_value( '_current_page' ) );
	}else{
		$current_page = 1;
	}

	if ( $current_page < 1 ) {
		$current_page = 1;
	}

	if ( is_object ( $ninja_forms_processing ) ) {
		$ninja_forms_processing->update_extra_value( '_current_page', $current_page );
	}

	if( isset( $form_data['multi_part'] ) ){
		$multi_part = $form_data['multi_part'];
	}else{
		$multi_part = 0;
	}

	if( $multi_part == 1 ){
		if ( $js_transition == 1 ) {
			foreach( $pages as $page => $vars ) {
				// Check to see if this field is the first field on a page.
				if ( $field_id == $vars['first_field'] ) {
					$divider_id = $vars['id'];
					if( $page == $current_page ){
						$style = '';
					}else{
						$style = 'display:none;';
					}

					if( $page == $current_page ){
						$class = 'ninja-forms-form-'.$form_id.'-mp-page-list-active';
					}else{
						$class = 'ninja-forms-form-'.$form_id.'-mp-page-list-inactive';
					}

					do_action( 'ninja_forms_display_before_mp_page', $form_id, $page );
					?>
					<div id="ninja_forms_form_<?php echo $form_id;?>_mp_page_<?php echo $page;?>" class="ninja-forms-form-<?php echo $form_id;?>-mp-page ninja-forms-mp-page" style="<?php echo $style;?>" rel="<?php echo $page;?>">
						<?php
					do_action( 'ninja_forms_display_mp_page_before_fields', $form_id, $page );

				}
			}
		}
	}
}

add_action( 'ninja_forms_display_before_field', 'ninja_forms_open_mp_div', 10, 2 );

function ninja_forms_close_mp_div( $field_id, $data ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$page = $ninja_forms_loading->get_field_setting( $field_id, 'page' );
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$page = $ninja_forms_processing->get_field_setting( $field_id, 'page' );
	}

	$pages = $form_data['mp_pages'];

	$js_transition = 1;

	if( is_object( $ninja_forms_processing ) ){
		$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );
	}else{
		$current_page = 1;
	}

	if( isset( $form_data['multi_part'] ) ){
		$multi_part = $form_data['multi_part'];
	}else{
		$multi_part = 0;
	}

	if( $multi_part == 1){
		if ( $js_transition == 1 ) {
			foreach( $pages as $page => $vars ) {
				if ( $field_id == $vars['last_field'] ) {
					do_action( 'ninja_forms_display_mp_page_after_fields', $form_id, $page );
					?>
					</div>
					<?php
					do_action( 'ninja_forms_display_after_mp_page', $form_id, $page );
				}
			}
		}
	}
}

add_action( 'ninja_forms_display_after_field', 'ninja_forms_close_mp_div', 10, 2 );