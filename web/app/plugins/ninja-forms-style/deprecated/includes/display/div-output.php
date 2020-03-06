<?php

function ninja_forms_style_ul_open( $field_id, $data ){
	global $ninja_forms_style_row_col, $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}

	if( isset( $form_data['ajax'] ) ){
		$ajax = $form_data['ajax'];
	}else{
		$ajax = 0;
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

	if ( $mp_enabled ) {

		$current_page = $field_row['data']['page'];

		if( isset( $form_data['style']['mp'][$current_page]['cols'] ) ){
			$cols = $form_data['style']['mp'][$current_page]['cols'];
		}else{
			$cols = 1;
		}
	}else{
		if( isset( $form_data['style']['cols'] ) ){
			$cols = $form_data['style']['cols'];
		}else{
			$cols = 1;
		}
	}

	$field_data = $field_row['data'];
	if( isset( $field_data['style']['colspan'] ) ){
		$colspan = $field_data['style']['colspan'];
	}else{
		$colspan = 1;
	}

	if( $cols > 1 ){
   		if( !isset( $ninja_forms_style_row_col ) ){
   			$ninja_forms_style_row_col = 0;
   		}
		if( $ninja_forms_style_row_col == 0 ){
			?>
			<div class="ninja-row">
			<?php
		}
		?>
				<div class="ninja-col-<?php echo $colspan;?>-<?php echo $cols;?>">

 		<?php
   	}
}

add_action( 'ninja_forms_display_before_field', 'ninja_forms_style_ul_open', 11, 2);

function ninja_forms_style_ul_close( $field_id, $data ){
	global $ninja_forms_style_row_col, $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
		$form_data = $ninja_forms_loading->get_all_form_settings();
		$pages = $ninja_forms_loading->get_form_setting( 'mp_pages' );
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
		$form_data = $ninja_forms_processing->get_all_form_settings();
		$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}

	if( isset( $form_data['ajax'] ) ){
		$ajax = $form_data['ajax'];
	}else{
		$ajax = 0;
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

		$current_page = $field_row['data']['page'];

		if( isset( $form_data['style']['mp'][$current_page]['cols'] ) ){
			$cols = $form_data['style']['mp'][$current_page]['cols'];
		}else{
			$cols = 1;
		}
	}else{
		if( isset( $form_data['style']['cols'] ) ){
			$cols = $form_data['style']['cols'];
		}else{
			$cols = 1;
		}
	}

	$field_data = $field_row['data'];
	if( isset( $field_data['style']['colspan'] ) ){
		$colspan = $field_data['style']['colspan'];
	}else{
		$colspan = 1;
	}

	if( $cols > 1 ){
		?>
		</div>
		<?php
		$ninja_forms_style_row_col = $ninja_forms_style_row_col + $colspan;
		if( ( $ninja_forms_style_row_col ) >= $cols ){
			?>
			</div>
			<?php
			$ninja_forms_style_row_col = 0;
		}
	}
}

add_action( 'ninja_forms_display_after_field', 'ninja_forms_style_ul_close', 9, 2 );
