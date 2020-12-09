<?php

/*
 *
 * Function that adds a filter to hide the form if the confirm-submit error has been set.
 *
 * @since 1.0.3
 * @return void
 */

function ninja_forms_mp_confirm_page_check_visibility( $form_id ){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing ) ){
		// Hide the form if the confirm-submit error is set.
		if( $ninja_forms_processing->get_error( 'confirm-submit' ) ){ // Check for the confirm error
			// Add a filter to the form display that will hide the form.
			add_filter( 'ninja_forms_display_fields_wrap_visibility', 'ninja_forms_mp_hide_form', 11, 2 );
			// Add a filter to the breadcrumb display that will hide them.
			add_filter( 'ninja_forms_mp_display_breadcrumbs_visbility', 'ninja_forms_mp_hide_form', 11, 2 );
			// Add a filter to the page title display that will hide them.
			add_filter( 'ninja_forms_display_mp_page_title', 'ninja_forms_mp_hide_page_titles', 10, 3 );
			// Display the confirmation page.

			$html = ninja_forms_return_echo( 'ninja_forms_mp_output_confirm_page', $form_id );
			echo $html;

			// Remove our original form fields from the page
			remove_all_actions( 'ninja_forms_display_before_fields' );
			remove_all_actions( 'ninja_forms_display_fields' );
			remove_all_actions( 'ninja_forms_display_after_fields' );

			//$ninja_forms_processing->remove_error( 'confirm-submit' );
		}
	}
}

if ( !is_admin() ) {
	add_action( 'ninja_forms_display_after_open_form_tag', 'ninja_forms_mp_confirm_page_check_visibility', 7 );	
}

/*
 *
 * Function that outputs a hidden field to mark that our confirm page has been seen and agreed to. This function will only output if the form is set to submit via ajax.
 *
 * @since 1.0.3 
 * @returns void
 */

function ninja_forms_mp_output_hidden_confirm( $form_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_data = $ninja_forms_loading->get_all_form_settings();
	} else {
		$form_data = $ninja_forms_processing->get_all_form_settings();
	}

	if ( isset( $form_data['ajax'] ) AND $form_data['ajax'] == 1 ) {
		echo '<input type="hidden" id="ninja_forms_form_'.$form_id.'_mp_confirm" name="_mp_confirm" value="0">';
	}
}

if ( !is_admin() ) {
	add_action( 'ninja_forms_display_close_form_tag', 'ninja_forms_mp_output_hidden_confirm', 5 );	
}

/*
 *
 * Function that filters the form visibility and sets it to 0 so that the form will be hidden.
 *
 * @since 1.0.3
 * @return $display
 */

function ninja_forms_mp_hide_form( $display, $form_id ){
	return 0;
}

 /**
  * Function that filters the page title visibility by returning an empty string.
  * 
  * @since 1.0.3
  * @return $title
  */

function ninja_forms_mp_hide_page_titles( $title, $form_id, $current_page ){
 	return '';
}

 /**
  * Function that outputs the HTML of the confirm page.
  *
  * @since 1.0.3
  * @return $output
  */

function ninja_forms_mp_output_confirm_page( $form_id ){
 	global $nf_mp_confirm_title, $ninja_forms_processing, $ninja_forms_fields;
 	// Get the pages for the current form.
 	$pages = $ninja_forms_processing->get_form_setting( 'mp_pages' );

	if( is_array( $pages ) ){
		if( is_object( $ninja_forms_processing ) ){
			?>
			<div id="mp_confirm_msg" class="ninja-forms-mp-confirm-msg"><?php echo $ninja_forms_processing->get_form_setting( 'mp_confirm_msg' );?></div>
			<?php
		}else{
			$html = '';	
		}
		$current_page = $ninja_forms_processing->get_extra_value( '_current_page' );

		// Run our two loading action hooks.
		do_action( 'ninja_forms_display_pre_init', $form_id );
		do_action( 'ninja_forms_display_init', $form_id );

		if( is_object( $ninja_forms_processing)){
			$sub_id = $ninja_forms_processing->get_form_setting('sub_id');
		}else if(isset($_REQUEST['sub_id'])){
			$sub_id = $_REQUEST['sub_id'];
		}else{
			$sub_id = '';
		}

		// Output our sub ID
		if( ! empty( $sub_id ) ){
			?>
			<input type="hidden" name="_sub_id" value="<?php echo $sub_id;?>">
			<?php			
		}

		foreach ( $pages as $num => $vars ) {
			if( function_exists( 'ninja_forms_conditionals_field_filter')  ){
				$show = ninja_forms_mp_check_page_conditional( $form_id, $num );
			} else {
				$show = true;
			}

			//if ( $show ) {
				$show_title = true;
				if ( isset ( $vars['page_title'] ) AND $vars['page_title'] != '' ) {
					$first_field = $vars['first_field'];
					$nf_mp_confirm_title[$first_field] = $vars['page_title'];
					add_action( 'ninja_forms_display_before_field', 'ninja_forms_mp_output_confirm_page_titles', 10.5, 2 );
				}
				foreach ( $vars['fields'] as $field_id ) {
					$field = $ninja_forms_processing->get_field_settings( $field_id );
					if ( $show ) {
						$ninja_forms_processing->update_extra_value( '_current_page', $num );
					} else {
						$ninja_forms_processing->update_extra_value( '_current_page', 0 );
					}
					
					if( isset( $ninja_forms_fields[$field['type']] ) ){
						$field_type = $field['type'];
						$type = $ninja_forms_fields[$field['type']];

						if(isset($field['data']['req'])){
							$req = $field['data']['req'];
						}else{
							$req = '';
						}

						$default_label_pos = $type['default_label_pos'];
						$display_wrap = $type['display_wrap'];
						$display_label = $type['display_label'];
						$sub_edit_function = $type['sub_edit_function'];
						$display_function = $type['display_function'];

						$process_field = $type['process_field'];
						$data = $field['data'];


						//These filters can be used to temporarily modify the settings of a field, i.e. default_value.
						$data = apply_filters( 'ninja_forms_field', $data, $field_id );
						//Check the show_field value of our $data array. If it is set to false, don't output the field.
						if(isset($data['show_field'])){
							$show_field = $data['show_field'];
						}else{
							$show_field = true;
						}
						
						if( isset( $data['display_style'] ) ){
							$display_style = $data['display_style'];
						}else{
							$display_style = '';
						}

						if ( $display_function != '' AND $show_field ) {
							if ( isset( $data['label_pos'] ) ) {
									$label_pos = $data['label_pos'];
							}else{
									$label_pos = '';
							}
							if( $label_pos == '' ) {
								$label_pos = $default_label_pos;
							}

							if( isset( $data['visible'] ) ){
								$visible = $data['visible'];
							}else{
								$visible = true;
							}


							do_action( 'ninja_forms_display_before_field', $field_id, $data );
							
							//Check to see if display_wrap has been disabled. If it hasn't, show the wrapping DIV.
							if($display_wrap){
								$field_wrap_class = ninja_forms_get_field_wrap_class($field_id);
								$field_wrap_class = apply_filters( 'ninja_forms_field_wrap_class', $field_wrap_class, $field_id );
								do_action( 'ninja_forms_display_before_opening_field_wrap', $field_id, $data );
								?>
								<div class="<?php echo $field_wrap_class;?>" style="<?php echo $display_style;?>" id="ninja_forms_field_<?php echo $field_id;?>_div_wrap" data-visible="<?php echo $visible;?>">
								<?php
								do_action( 'ninja_forms_display_after_opening_field_wrap', $field_id, $data );
							}

							//Check to see if display_label has been disabled. If it hasn't, show the label.
							if( $display_label ){
								if( $label_pos == 'left' OR $label_pos == 'above' ){ // Check the label position variable. If it is left or above, show the label.
									do_action( 'ninja_forms_display_before_field_label', $field_id, $data );
									do_action( 'ninja_forms_display_field_label', $field_id, $data );
									do_action( 'ninja_forms_display_after_field_label', $field_id, $data );
								}
							}

							//Check to see if there is a registered display function. If so, call it.
							if($display_function != ''){

								do_action( 'ninja_forms_display_before_field_function', $field_id, $data );
								$arguments['field_id'] = $field_id;
								$arguments['data'] = $data;
								call_user_func_array($display_function, $arguments);
								do_action( 'ninja_forms_display_after_field_function', $field_id, $data );
								if( $label_pos == 'left' OR $label_pos == 'inside'){
									do_action( 'ninja_forms_display_field_help', $field_id, $data );
								}
							}

							//Check to see if display_label has been disabled. If it hasn't, show the label.
							if($display_label){
								if($label_pos == 'right' OR $label_pos == 'below'){ // Check the label position variable. If it is right or below, show the label.
									do_action( 'ninja_forms_display_before_field_label', $field_id, $data );
									do_action( 'ninja_forms_display_field_label', $field_id, $data );
									do_action( 'ninja_forms_display_after_field_label', $field_id, $data );
								}
							}

							//Check to see if display_wrap has been disabled. If it hasn't close the wrapping DIV
							if($display_wrap){
								do_action( 'ninja_forms_display_before_closing_field_wrap', $field_id, $data );
								?>
								</div>
								<?php
								do_action( 'ninja_forms_display_after_closing_field_wrap', $field_id, $data );
							}
							do_action( 'ninja_forms_display_after_field', $field_id, $data );
						}
					}
				}
				$ninja_forms_processing->update_extra_value( '_current_page', $current_page );
		}
			?>
			<input type="hidden" id="mp_confirm_page" name="_mp_confirm" value="1">
			<?php
	}
}

function ninja_forms_mp_output_confirm_page_titles( $field_id, $data ){
	global $nf_mp_confirm_title, $ninja_forms_processing;

	if ( isset ( $nf_mp_confirm_title[$field_id] ) ) {
		echo apply_filters( 'ninja_forms_mp_confirm_page_title', '<h4>'.$nf_mp_confirm_title[$field_id].'</h4>', $field_id );
	}
}